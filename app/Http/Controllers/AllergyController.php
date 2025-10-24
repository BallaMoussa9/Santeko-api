<?php

namespace App\Http\Controllers;

use App\Models\Allergies; // Utilisez le nom de votre modèle 'Allergies'
use App\Http\Requests\AllergyRequest;
use Illuminate\Http\JsonResponse;

class AllergyController extends Controller
{
    /**
     * Affiche la liste des allergies.
     */
    public function index(): JsonResponse
    {
        $allergies = Allergies::with(['patient', 'medicalRecord'])->get();
        return response()->json($allergies);
    }

    /**
     * Crée un nouvel enregistrement d'allergie.
     */
    public function store(AllergyRequest $request): JsonResponse
    {
        $allergy = Allergies::create($request->validated());
        return response()->json($allergy, 201);
    }

    /**
     * Affiche une allergie spécifique.
     */
    public function show(Allergies $allergy): JsonResponse
    {
        return response()->json($allergy->load(['patient', 'medicalRecord']));
    }

    /**
     * Met à jour une allergie existante.
     */
    public function update(AllergyRequest $request, Allergies $allergy): JsonResponse
    {
        $allergy->update($request->validated());
        return response()->json($allergy);
    }

    /**
     * Supprime une allergie.
     */
    public function destroy(Allergies $allergy): JsonResponse
    {
        $allergy->delete();
        return response()->json(null, 204);
    }
}
