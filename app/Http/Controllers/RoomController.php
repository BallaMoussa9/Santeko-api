<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class RoomController extends Controller
{
    /**
     * Afficher toutes les ressources (Chambres) ou filtrer.
     */
    public function index(Request $request)
    {
        // Exemple de filtrage/recherche
        $query = Room::with('department');

        if ($request->has('search')) {
            $searchTerm = $request->get('search');
            $query->where('room_number', 'like', "%{$searchTerm}%")
                  ->orWhere('floor', 'like', "%{$searchTerm}%");
        }

        if ($request->has('department_id')) {
             $query->where('department_id', $request->get('department_id'));
        }

        return response()->json($query->paginate(15));
    }

    /**
     * Créer une nouvelle ressource (Chambre).
     */
     /**
     * Créer une nouvelle ressource (Chambre).
     */
    public function store(Request $request)
    {
        // 🔑 LOG 1: Enregistre le corps brut de la requête AVANT la validation.
        Log::info('Tentative de création de chambre : Données brutes reçues.', [
            'request_data' => $request->all(),
            'ip' => $request->ip(),
            'user_id' => auth()->check() ? auth()->id() : 'Guest',
        ]);

        // Validation simple (minimum requis)
        $validated = $request->validate([
            'room_number' => 'required|string|max:10|unique:rooms,room_number',
            'department_id' => 'required|exists:departments,id',
            'capacity' => 'required|integer|min:1',
            'type' => 'nullable|in:simple,double,suite,isolation',
            'floor' => 'nullable|string|max:10',
            'notes' => 'nullable|string',
        ]);

        // 🔑 LOG 2: Enregistre les données APRÈS la validation (pour confirmer la propreté des données)
        Log::info('Chambre - Validation réussie.', [
            'validated_data' => $validated,
        ]);

        $room = Room::create($validated);

        // 🔑 LOG 3: Enregistre la création réussie.
        Log::notice('Nouvelle chambre créée avec succès.', [
            'room_id' => $room->id,
            'room_number' => $room->room_number,
        ]);

        return response()->json($room, 201); // 201 Created
    }

    /**
     * Afficher une ressource spécifique.
     */
    public function show(Room $room)
    {
        return response()->json($room->load('service'));
    }

    /**
     * Mettre à jour la ressource spécifique.
     */
    public function update(Request $request, Room $room)
    {
        // Validation avec exclusion de l'ID actuel pour l'unicité
        $validated = $request->validate([
            'room_number' => 'required|string|max:10|unique:rooms,room_number,' . $room->id,
            'department_id' => 'required|exists:departments,id',
            'capacity' => 'required|integer|min:1',
            'type' => 'nullable|in:simple,double,suite,isolation',
            'floor' => 'nullable|string|max:10',
            'notes' => 'nullable|string',
            'is_available' => 'sometimes|boolean', // Champ mis à jour souvent séparément
        ]);

        $room->update($validated);
        return response()->json($room->load('department'));
    }

    /**
     * Supprimer une ressource spécifique.
     */
    public function destroy(Room $room)
    {
        $room->delete();
        return response()->json(null, 204); // 204 No Content
    }
}
