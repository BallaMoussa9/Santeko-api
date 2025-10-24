<?php

namespace App\Http\Controllers;

use App\Models\Birth; // IMPORTER LE MODÈLE BIRTH
use App\Http\Requests\NaissanceRequest;
use Illuminate\Http\JsonResponse;

class NaissanceController extends Controller
{
    /**
     * Display a listing of the resource.
     * Récupère et affiche tous les enregistrements de naissances.
     */
    public function index(): JsonResponse
    {
        $naissances = Birth::all(); // UTILISER LE MODÈLE BIRTH
        return response()->json($naissances);
    }

    /**
     * Store a newly created resource in storage.
     * Enregistre une nouvelle entrée de naissance.
     */
    public function store(NaissanceRequest $request): JsonResponse
    {
        $naissance = Birth::create($request->validated()); // UTILISER LE MODÈLE BIRTH
        return response()->json($naissance, 201);
    }

    /**
     * Display the specified resource.
     * Affiche les détails d'une entrée de naissance spécifique.
     *
     * @param  \App\Models\Birth  $naissance Le modèle Birth injecté par la résolution implicite de modèle.
     */
    public function show(Birth $naissance): JsonResponse // UTILISER LE MODÈLE BIRTH DANS LE TYPE HINT
    {
        return response()->json($naissance);
    }

    /**
     * Update the specified resource in storage.
     * Met à jour une entrée de naissance spécifique.
     *
     * @param  \App\Http\Requests\NaissanceRequest  $request La requête validée.
     * @param  \App\Models\Birth  $naissance Le modèle Birth injecté.
     */
    public function update(NaissanceRequest $request, Birth $naissance): JsonResponse // UTILISER LE MODÈLE BIRTH DANS LE TYPE HINT
    {
        $naissance->update($request->validated());
        return response()->json($naissance);
    }

    /**
     * Remove the specified resource from storage.
     * Supprime une entrée de naissance spécifique.
     *
     * @param  \App\Models\Birth  $naissance Le modèle Birth injecté.
     */
    public function destroy(Birth $naissance): JsonResponse // UTILISER LE MODÈLE BIRTH DANS LE TYPE HINT
    {
        $naissance->delete();
        return response()->json(null, 204);
    }
}
