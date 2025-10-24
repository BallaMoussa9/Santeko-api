<?php

namespace App\Http\Controllers;

use App\Models\Death; // Importer le modèle Death pour la table 'deaths'
use App\Http\Requests\MortaliteRequest; // Importer la requête de formulaire pour la validation
use Illuminate\Http\Request; // Garder Request pour le type-hinting, bien que MortaliteRequest soit utilisé
use Illuminate\Http\JsonResponse; // Pour les retours JSON

class MortaliterController extends Controller
{
    /**
     * Display a listing of the resource.
     * Récupère et affiche toutes les entrées de décès.
     */
    public function index(): JsonResponse
    {
        $deaths = Death::all(); // Récupère tous les enregistrements de décès
        return response()->json($deaths); // Retourne les données en JSON
    }

    /**
     * Store a newly created resource in storage.
     * Enregistre une nouvelle entrée de décès dans la base de données.
     */
    public function store(MortaliteRequest $request): JsonResponse
    {
        // La validation est déjà effectuée par MortaliteRequest
        // Crée un nouvel enregistrement Death avec les données validées
        $death = Death::create($request->validated());

        // Retourne la nouvelle ressource avec un statut 201 Created
        return response()->json($death, 201);
    }

    /**
     * Display the specified resource.
     * Affiche les détails d'une entrée de décès spécifique.
     *
     * @param  \App\Models\Death  $mortalite Le modèle Death injecté par la résolution implicite de modèle.
     * Le nom de la variable ($mortalite) doit correspondre au paramètre de route {mortalite}.
     */
    public function show(Death $mortalite): JsonResponse
    {
        // Retourne le modèle Death trouvé en JSON
        return response()->json($mortalite);
    }

    /**
     * Update the specified resource in storage.
     * Met à jour une entrée de décès spécifique dans la base de données.
     *
     * @param  \App\Http\Requests\MortaliteRequest  $request La requête contenant les données de mise à jour validées.
     * @param  \App\Models\Death  $mortalite Le modèle Death injecté.
     */
    public function update(MortaliteRequest $request, Death $mortalite): JsonResponse
    {
        // Met à jour l'enregistrement Death avec les données validées
        $mortalite->update($request->validated());

        // Retourne la ressource mise à jour
        return response()->json($mortalite);
    }

    /**
     * Remove the specified resource from storage.
     * Supprime une entrée de décès spécifique de la base de données.
     *
     * @param  \App\Models\Death  $mortalite Le modèle Death injecté.
     */
    public function destroy(Death $mortalite): JsonResponse
    {
        // Supprime l'enregistrement Death
        $mortalite->delete();

        // Retourne une réponse vide avec un statut 204 No Content pour indiquer le succès de la suppression
        return response()->json(null, 204);
    }
}
