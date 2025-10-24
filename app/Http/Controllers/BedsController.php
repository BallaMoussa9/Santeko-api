<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BedsController extends Controller
{
    /**
     * Afficher toutes les ressources (Lits) ou filtrer.
     */
    public function index(Request $request)
    {
        $query = Bed::with(['room.department', 'patient.user']); // Charger les relations importantes

        // Exemple de filtrage par chambre ou statut
        if ($request->has('room_id')) {
            $query->where('room_id', $request->get('room_id'));
        }

        if ($request->has('status')) {
             $query->where('status', $request->get('status'));
        }

        return response()->json($query->paginate(20));
    }

    /**
     * Créer une nouvelle ressource (Lit).
     */
    public function store(Request $request)
    {
        // Validation simple
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'bed_number' => [
                'required',
                'string',
                'max:10',
                // S'assurer que l'unicité est seulement PAR chambre
                Rule::unique('beds')->where(fn ($query) => $query->where('room_id', $request->room_id)),
            ],
            'status' => 'nullable|in:available,occupied,cleaning,maintenance',
            'is_private' => 'sometimes|boolean',
            'equipment_notes' => 'nullable|string',
            'patient_id' => 'nullable|unique:beds,patient_id|exists:patients,id', // Unique dans la table beds
        ]);

        $bed = Bed::create($validated);
        return response()->json($bed->load(['room.department', 'patient.user']), 201);
    }

    /**
     * Afficher une ressource spécifique.
     */
    public function show(Bed $bed)
    {
        return response()->json($bed->load(['room.department', 'patient.user']));
    }

    /**
     * Mettre à jour la ressource spécifique.
     */
  public function update(Request $request, Bed $bed)
    {
        $validated = $request->validate([
            // Rendre room_id "sometimes" : il est validé uniquement si présent dans la requête
            'room_id' => 'sometimes|required|exists:rooms,id',

            // Rendre bed_number "sometimes"
            // La règle d'unicité doit aussi être conditionnelle pour ne pas échouer si bed_number n'est pas fourni.
            'bed_number' => [
                'sometimes', // Valider seulement si bed_number est présent
                'required', // Il doit être requis s'il est présent
                'string',
                'max:10',
                // Unicité par room_id, en ignorant le lit actuel
                // Cette règle sera appliquée uniquement si 'bed_number' est dans la requête
                Rule::unique('beds')->where(fn ($query) => $query->where('room_id', $request->room_id ?? $bed->room_id))->ignore($bed->id),
            ],

            // Le statut est facultatif pour une mise à jour partielle
            'status' => 'sometimes|nullable|in:available,occupied,cleaning,maintenance',

            'is_private' => 'sometimes|boolean',
            'equipment_notes' => 'sometimes|nullable|string', // equipment_notes peut être null et parfois présent

            'patient_id' => [
                'sometimes', // Valider seulement si patient_id est présent
                'nullable', // Il peut être null (pour libérer le lit d'un patient)
                'exists:patients,id',
                // Unicité du patient_id, en ignorant le lit actuel.
                // S'applique seulement si 'patient_id' est dans la requête et n'est pas null
                Rule::unique('beds')->ignore($bed->id, 'id')->whereNotNull('patient_id'),
            ],
        ]);

        // Mettez à jour uniquement les champs qui ont été validés et sont présents dans la requête
        $bed->update($validated);

        // Retourne le lit mis à jour avec les relations
        // N'oubliez pas d'inclure 'patient.user' si vous en avez besoin
        return response()->json($bed->load(['room.department', 'patient.user']));
    }

    /**
     * Supprimer une ressource spécifique.
     */
    public function destroy(Bed $bed)
    {
        $bed->delete();
        return response()->json(null, 204);
    }
}
