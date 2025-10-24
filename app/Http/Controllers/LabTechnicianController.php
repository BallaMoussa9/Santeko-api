<?php

namespace App\Http\Controllers;

use App\Models\LabTechnician;
use Illuminate\Http\Request;
use App\Http\Requests\LabTechnicianRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Validation\Rule;
use App\Http\Requests\LabTechnicianUpdateRequest;
use Illuminate\Support\Facades\Storage;




class LabTechnicianController extends Controller
{
    /**
     * Affiche la liste des techniciens de laboratoire.
     */
    public function index(): JsonResponse
    {
        // Une politique d'autorisation peut être utilisée pour contrôler l'accès
       // Gate::authorize('viewAny', LabTechnician::class);
        $labTechnicians = LabTechnician::with(['user', 'laboratory'])->get();
        return response()->json($labTechnicians);
    }

    /**
     * Crée un nouveau technicien.
     */
   public function store(LabTechnicianRequest $request): JsonResponse
{
    $data = $request->validated();

    // 1. Créer le nouvel utilisateur
    $user = User::create([
        'first_name'    => $data['first_name'],
        'last_name'     => $data['last_name'],
        'email'         => $data['email'],
        'password'      => Hash::make($data['password']),
        'birth_date'    => $data['birth_date'] ?? null,
        'phone'         => $data['phone'] ?? null,
        'country'       => $data['country'] ?? null,
        'city'          => $data['city'] ?? null,
        'address'       => $data['address'] ?? null,
        'department_id' => $data['department_id'] ?? null,
        'status'        => 'active', // statut par défaut
    ]);

    // 2. Gérer la photo de profil
    if ($request->hasFile('profile_photo') && $request->file('profile_photo')->isValid()) {
        $path = $request->file('profile_photo')->store('profiles', 'public');
        $user->profile_photo = $path;
        $user->save();
    }

    // 3. Assigner le rôle lab_technician via la méthode assignRole()
    $user->assignRole('lab_technician');

    // 4. Créer le technicien de laboratoire lié à l'utilisateur
    $labTechnician = LabTechnician::create([
        'user_id'       => $user->id,
        'laboratory_id' => $data['laboratory_id'],
        'speciality'    => $data['speciality'] ?? null,
        'qualification' => $data['qualification'] ?? null,
        'status'        => $data['status'] ?? 'active',
    ]);

    return response()->json($labTechnician->load(['user', 'laboratory']), 201);
}


    /**
     * Affiche un technicien spécifique.
     */
    public function show(int  $labTechnician): JsonResponse
    {
        $labTechnician = LabTechnician::with(['user', 'laboratory'])->findOrFail($labTechnician);
       // Gate::authorize('view', $labTechnician);
        return response()->json($labTechnician->load(['user', 'laboratory']));
    }

    /**
     * Met à jour un technicien existant.
     */
   public function update(LabTechnicianUpdateRequest $request, int  $labTechnician)
    {
        // $request->validated() contient toutes les données validées
        $data = $request->validated();

        // --- 1. Mettre à jour l'utilisateur associé ---
        // In your LabTechnicianController.php, in the update() method
        $labTechnician = LabTechnician::with('user')->findOrFail($labTechnician);
// ...
$user = $labTechnician->user;

if (!$user) {
    \Log::error("LabTechnician ID {$labTechnician->id} has no associated user.", ['labTechnician' => $labTechnician]);
    return response()->json([
        'message' => 'User associated with this lab technician not found.',
        'lab_technician_id' => $labTechnician->id, // Add this line
        'provided_user_id' => $labTechnician->user_id,
        'user_id' => $user, // And this line
    ], 404);
}
// ...

        // Filtrer les données spécifiques à l'utilisateur
        $userData = Arr::only($data, [
            'first_name', 'last_name', 'birth_date', 'phone', 'email', 'city', 'address', 'country', 'department_id', 'password', 'role_id'
        ]);

        // Gérer le mot de passe si un nouveau est fourni
        if (isset($userData['password'])) {
            $userData['password'] = Hash::make($userData['password']);
        }

        // Gérer le téléchargement de la photo de profil si un fichier est présent
        if ($request->hasFile('profile_photo') && $request->file('profile_photo')->isValid()) {
            // Supprimer l'ancienne photo si elle existe
            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                 Storage::disk('public')->delete($user->profile_photo);
            }
            $path = $request->file('profile_photo')->store('profiles', 'public');
            $userData['profile_photo'] = $path; // Enregistre le chemin de la nouvelle photo
        } elseif ($request->has('profile_photo') && is_null($request->input('profile_photo'))) {
            // Si le champ est envoyé explicitement à null, cela signifie supprimer la photo existante
            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $userData['profile_photo'] = null;
        }

        $user->update($userData); // Met à jour l'enregistrement User

        // --- 2. Mettre à jour le technicien de laboratoire ---
        // Filtrer les données spécifiques au technicien de laboratoire
        $labTechnicianData = Arr::only($data, [
            'laboratory_id', 'speciality', 'qualification', 'status',
        ]);
        $labTechnician->update($labTechnicianData); // Met à jour l'enregistrement LabTechnician

        // Charger les relations mises à jour et retourner la réponse
        return response()->json($labTechnician->load(['user', 'laboratory']));
    }

    /**
     * Supprime un technicien.
     */
    public function destroy(LabTechnician $labTechnician): JsonResponse
    {
       // Gate::authorize('delete', $labTechnician);
        $labTechnician->delete();
        return response()->json(['message' => 'Technicien de laboratoire supprimé avec succès.'], 204);
    }

}
