<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use App\Http\Requests\DonorRequest;
use Illuminate\Http\JsonResponse;

class DonorController extends Controller
{
    /**
     * Affiche la liste des donneurs.
     */
    public function index(): JsonResponse
    {
        $donors = Donor::all();
        return response()->json($donors);
    }

    /**
     * Enregistre un nouveau donneur.
     */
    public function store(DonorRequest $request): JsonResponse
    {
        $donor = Donor::create($request->validated());
        return response()->json($donor, 201);
    }

    /**
     * Affiche les informations d'un donneur spécifique.
     */
    public function show(Donor $donor): JsonResponse
    {
        return response()->json($donor);
    }

    /**
     * Met à jour les informations d'un donneur.
     */
    public function update(DonorRequest $request, Donor $donor): JsonResponse
    {
        $donor->update($request->validated());
        return response()->json($donor);
    }

    /**
     * Supprime un donneur.
     */
    public function destroy(Donor $donor): JsonResponse
    {
        $donor->delete();
        return response()->json(null, 204);
    }
}
