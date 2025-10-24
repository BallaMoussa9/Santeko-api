<?php

namespace App\Http\Controllers;

use App\Models\StatistiqueRegionale;
use App\Http\Requests\StatistiqueRegionaleRequest;
use Illuminate\Http\JsonResponse;

class StatisticController extends Controller
{
    /**
     * Affiche une liste de ressources (statistiques régionales).
     */
    public function index(): JsonResponse
    {
        // Récupère toutes les statistiques régionales.
        // Vous pouvez ajouter une logique de filtre ou de pagination ici si nécessaire.
        $stats = StatistiqueRegionale::all();

        return response()->json($stats);
    }

    /**
     * Stocke une nouvelle ressource (statistique régionale) dans le stockage.
     */
    public function store(StatistiqueRegionaleRequest $request): JsonResponse
    {
        // Crée une nouvelle statistique régionale avec les données validées.
        $stat = StatistiqueRegionale::create($request->validated());

        return response()->json($stat, 201); // Code 201 pour "Créé"
    }

    /**
     * Affiche la ressource (statistique régionale) spécifiée.
     */
    public function show(StatistiqueRegionale $statistiqueRegionale): JsonResponse
    {
        // Laravel injecte automatiquement l'instance de StatistiqueRegionale.
        return response()->json($statistiqueRegionale);
    }

    /**
     * Met à jour la ressource (statistique régionale) spécifiée dans le stockage.
     */
    public function update(StatistiqueRegionaleRequest $request, StatistiqueRegionale $statistiqueRegionale): JsonResponse
    {
        // Met à jour la statistique avec les données validées.
        $statistiqueRegionale->update($request->validated());

        return response()->json($statistiqueRegionale);
    }

    /**
     * Supprime la ressource (statistique régionale) spécifiée du stockage.
     */
    public function destroy(StatistiqueRegionale $statistiqueRegionale): JsonResponse
    {
        // Supprime la statistique régionale.
        $statistiqueRegionale->delete();

        return response()->json(null, 204); // Code 204 pour "Pas de contenu" (succès de la suppression)
    }
}
