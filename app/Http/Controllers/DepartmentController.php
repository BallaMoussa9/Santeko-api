<?php

namespace App\Http\Controllers; // <-- MODIFICATION ICI : Ajout de 'Api'

use App\Http\Controllers\Controller;
use App\Models\Department; // Importez votre modèle Department
use Illuminate\Http\Request;
use App\Http\Requests\DepartmentRequest; // Assurez-vous que ce Request existe et est à jour
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth; // <-- AJOUT DE L'IMPORTATION POUR AUTH
use Illuminate\Validation\Rule;


class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     * Accessible à tous les utilisateurs.
     */
    public function index(): JsonResponse
    {
        $departments = Department::all();
        return response()->json([
            'message' => 'Liste des départements récupérée avec succès.',
            'data' => $departments // Encapsule les données dans une clé 'data'
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     * Accessible uniquement aux administrateurs.
     */
 public function store(DepartmentRequest $request): JsonResponse
{
    // Vérification du rôle de l'utilisateur
    // On suppose que Auth::user() renvoie un modèle User qui a la relation de rôle
    if (!Auth::check() || !Auth::user()->hasRole('admin')) {
        $userId = Auth::check() ? Auth::user()->id : 'non connecté';
        return response()->json([
            'message' => "Accès non autorisé : L'utilisateur avec l'ID {$userId} n'a pas le rôle d'administrateur."
        ], 403);
    }

    // Récupérer les données validées de la requête (qui incluent déjà doctor_id si fourni)
    $validatedData = $request->validated();

    // ✅ Ajouter l'ID de l'administrateur connecté au tableau des données validées
    // C'est l'ID de l'utilisateur dans la table 'users'
    $validatedData['admin_id'] = Auth::id();

    // ✅ Le 'doctor_id' est déjà dans $validatedData grâce à la DepartmentRequest.
    // Pas besoin de l'ajouter explicitement ici, sauf si vous voulez le modifier.
    // Par exemple, si vous vouliez forcer doctor_id à null s'il n'est pas admin, mais ce n'est pas le cas ici.

    $department = Department::create($validatedData);

    return response()->json([
        'message' => 'Département créé avec succès.',
        'data' => $department
    ], 201);
}

    /**
     * Display the specified resource.
     * Accessible à tous les utilisateurs.
     */
    public function show(Department $department): JsonResponse
    {
        return response()->json([
            'message' => 'Département récupéré avec succès.',
            'data' => $department
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     * Accessible uniquement aux administrateurs.
     */
     public function update(DepartmentRequest $request, Department $department): JsonResponse
    {
        // Vérification du rôle de l'utilisateur
        if (!Auth::check()) {
            return response()->json(['message' => 'Accès non autorisé : Veuillez vous connecter.'], 403);
        }

        if (!Auth::user()->hasRole('admin')) {
            $userId = Auth::user()->id;
            return response()->json(['message' => "Accès non autorisé : L'utilisateur avec l'ID {$userId} n'a pas le rôle d'administrateur."], 403);
        }

        $department->update($request->validated());
        return response()->json([
            'message' => 'Département mis à jour avec succès.',
            'data' => $department
        ]);
    }
    /**
     * Remove the specified resource from storage.
     * Accessible uniquement aux administrateurs.
     */
    public function destroy(Department $department): JsonResponse
    {
        // Vérification du rôle de l'utilisateur
        if (!Auth::check()) {
            return response()->json(['message' => 'Accès non autorisé : Veuillez vous connecter.'], 403);
        }

        if (!Auth::user()->hasRole('admin')) {
            $userId = Auth::user()->id;
            return response()->json(['message' => "Accès non autorisé : L'utilisateur avec l'ID {$userId} n'a pas le rôle d'administrateur."], 403);
        }

        $department->delete();
        return response()->json(['message' => 'Département supprimé avec succès.']);
    }
}
