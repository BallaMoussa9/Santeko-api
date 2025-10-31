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
    // 1. Vérification du rôle de l'utilisateur
    $user = Auth::user();
    if (!$user || !$user->hasRole('admin')) {
        $userId = $user ? $user->id : 'non connecté';
        return response()->json([
            'message' => "Accès non autorisé : L'utilisateur avec l'ID {$userId} n'a pas le rôle d'administrateur."
        ], 403);
    }

    // 2. Récupérer l'ID du profil Admin de l'utilisateur
    // Nous utilisons la relation 'admin()' définie dans votre modèle User
    $adminProfile = $user->admin()->first();

    if (!$adminProfile) {
        // Cette erreur ne devrait pas se produire si la synchronisation des rôles est correcte,
        // mais elle est cruciale en cas de désynchronisation.
        return response()->json([
            'message' => "Erreur de synchronisation : L'utilisateur est admin mais n'a pas d'entrée de profil dans la table 'admins'."
        ], 500);
    }

    // L'ID dont la table `departments` a besoin est l'ID de la table `admins` (la clé primaire de Admin)
    $admin_fk_id = $adminProfile->id;

    // 3. Récupérer les données validées
    $validatedData = $request->validated();

    // 4. INJECTION DE LA CLÉ ÉTRANGÈRE CORRECTE
    // On utilise l'ID du profil Admin, qui est la clé primaire de la table `admins`.
    $validatedData['admin_id'] = $admin_fk_id;

    // 5. Création du département
    try {
        $department = Department::create($validatedData);

        return response()->json([
            'message' => 'Département créé avec succès.',
            'data' => $department
        ], 201);
    } catch (\Exception $e) {
        // En cas d'autre erreur SQL (comme le doctor_id qui n'existe pas)
        return response()->json([
            'message' => 'Échec de la création du département. Détails : ' . $e->getMessage(),
            'error' => $e->getMessage()
        ], 500);
    }
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
