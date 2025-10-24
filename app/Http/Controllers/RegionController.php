<?php

namespace App\Http\Controllers;

use App\Models\Region;
use App\Http\Requests\RegionRequest;
use Illuminate\Http\JsonResponse;

class RegionController extends Controller
{
    /**
     * Affiche la liste de toutes les régions.
     */
    public function index(): JsonResponse
    {
        $regions = Region::all();
        return response()->json([
            'message' => 'Liste des régions récupérée avec succès.',
            'data' => $regions,
        ]);
    }

    /**
     * Crée une nouvelle région.
     */
    public function store(RegionRequest $request): JsonResponse
    {
        $region = Region::create($request->validated());

        return response()->json([
            'message' => 'Région créée avec succès.',
            'data' => $region,
        ], 201);
    }

    /**
     * Affiche une région spécifique.
     */
    public function show(Region $region): JsonResponse
    {
        return response()->json([
            'message' => 'Détails de la région récupérés avec succès.',
            'data' => $region,
        ]);
    }

    /**
     * Met à jour une région existante.
     */
    public function update(RegionRequest $request, Region $region): JsonResponse
    {
        $region->update($request->validated());

        return response()->json([
            'message' => 'Région mise à jour avec succès.',
            'data' => $region,
        ]);
    }

    /**
     * Supprime une région.
     */
    public function destroy(Region $region): JsonResponse
    {
        $region->delete();

        return response()->json([
            'message' => 'Région supprimée avec succès.',
        ], 200);
    }
}
