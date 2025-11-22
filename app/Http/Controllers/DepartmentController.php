<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Department; 
use App\Models\User;
use App\Models\Role; // Nécessaire pour la méthode getResponsibleUsers
use Illuminate\Http\Request;
use App\Http\Requests\DepartmentRequest; 
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     * Accessible à tous les utilisateurs.
     */
    public function index(): JsonResponse
    {
        // CHARGER LA RELATION 'user' pour obtenir le responsable
        $departments = Department::with('user')->get();
        return response()->json([
            'message' => 'Liste des départements récupérée avec succès.',
            'data' => $departments
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     * Accessible uniquement aux administrateurs.
     */
    public function store(DepartmentRequest $request): JsonResponse
    {
        // 1. Vérification du rôle de l'utilisateur (ADMIN)
        $user = Auth::user();
        if (!$user || !$user->hasRole('admin')) {
            $userId = $user ? $user->id : 'non connecté';
            return response()->json([
                'message' => "Accès non autorisé. L'utilisateur avec l'ID {$userId} n'a pas le rôle d'administrateur."
            ], 403);
        }

        // 2. Récupérer l'ID du profil Admin pour la colonne 'admin_id'
        $adminProfile = $user->admin()->first();
        if (!$adminProfile) {
             return response()->json(['message' => "Erreur de synchronisation: Profil Admin non trouvé."], 500);
        }

        // 3. Récupérer les données validées
        $validatedData = $request->validated();

        // 4. INJECTION ET MAPPAGE DES CLÉS ÉTRANGÈRES
        $validatedData['admin_id'] = $adminProfile->id;
        
        // ✅ CORRECTION CRUCIALE : Mappage de 'responsible_user_id' vers 'user_id'
        // Ceci garantit que la valeur est prise du formulaire et enregistrée dans la colonne 'user_id'
        $validatedData['user_id'] = $validatedData['responsible_user_id'] ?? null;
        unset($validatedData['responsible_user_id']); // Supprimer la clé non-DB
        
        // 5. Création du département
        try {
            $department = Department::create($validatedData);

            // Charger l'utilisateur responsable pour la réponse immédiate
            $department->load('user'); 

            return response()->json([
                'message' => 'Département créé avec succès.',
                'data' => $department
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Échec de la création du département.',
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
        $department->load('user'); 
        
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
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            $userId = Auth::check() ? Auth::user()->id : 'non connecté';
            return response()->json(['message' => "Accès non autorisé : L'utilisateur avec l'ID {$userId} n'a pas le rôle d'administrateur."], 403);
        }

        $validatedData = $request->validated();
        
        // ✅ CORRECTION CRUCIALE : Mappage de 'responsible_user_id' vers 'user_id' pour la mise à jour
        $validatedData['user_id'] = $validatedData['responsible_user_id'] ?? null;
        unset($validatedData['responsible_user_id']); 

        $department->update($validatedData);
        
        // Charger l'utilisateur responsable pour la réponse immédiate
        $department->load('user');
        
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

    /**
     * Récupère la liste des utilisateurs potentiels pouvant être responsables.
     */
    public function getResponsibleUsers(): JsonResponse
    {
        $eligibleRoles = ['admin', 'doctor', 'lab_technician', 'nurse', 'first_responder']; 

        $roleIds = Role::whereIn('name', $eligibleRoles)->pluck('id');

        $responsibleUsers = User::whereHas('roles', function ($query) use ($roleIds) {
            $query->whereIn('role_id', $roleIds);
        })
        ->with('roles:id,name')
        ->get()
        ->map(function ($user) {
            return [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'role' => $user->roles->first() ? $user->roles->first()->name : 'N/A',
            ];
        });

        return response()->json([
            'message' => 'Liste des responsables potentiels récupérée avec succès.',
            'data' => $responsibleUsers
        ], 200);
    }
}