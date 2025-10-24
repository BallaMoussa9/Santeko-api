<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Validation\ValidationException;

use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Http\Resources\UserResource;
use App\Http\Requests\RegistereRequest;

use App\Models\Patient;
use App\Models\MedicalRecord;
//CreateNewUser $creator
class AuthRegisteredUserController extends Controller
{
      public function store(RegistereRequest $request)
{
    // 1. Validation et préparation des données pour l'utilisateur
    $data = $request->validated();

    if ($request->hasFile('profile_photo')) {
        // Enregistrement de la photo de profil
        $path = $request->file('profile_photo')->store('profiles', 'public');
        $data['profile_photo'] = $path;
    }

    // 2. Création de l'utilisateur
    $user = User::create($data);

    // 3. Attribuer le rôle 'patient'
    $user->assignRole('patient');

    // 4. Création de l'enregistrement Patient
    // On utilise l'ID de l'utilisateur ($user->id) pour initialiser la table 'patients'.
    $patient = Patient::create([
        'user_id' => $user->id,
        // Les autres champs sont nullables, donc non obligatoires ici.
        // Si vous avez d'autres valeurs par défaut (ex: 'status'), ajoutez-les.
    ]);

    // 5. Création de l'enregistrement MedicalRecord
    // On utilise l'ID du Patient ($patient->id) pour initialiser la table 'medical_records'.
    $medicalRecord = MedicalRecord::create([
        'patient_id' => $patient->id,
        // Les autres champs sont nullables, donc non obligatoires ici.
        // Assurez-vous que le champ 'status' de MedicalRecord a une valeur par défaut dans la migration
        // ou ajoutez-la ici si nécessaire (ex: 'status' => 'active').
    ]);

    // 6. Mise à jour de l'utilisateur et du patient avec les clés étrangères
    // Si l'utilisateur a une colonne `patient_id` et le patient a une colonne `medical_record_id`,
    // vous devez les mettre à jour (basé sur votre log, cela semble être le cas).

    // a) Mise à jour de l'utilisateur avec le patient_id
    $user->update(['patient_id' => $patient->id]);

    // b) Mise à jour du patient avec le medical_record_id
    $patient->update(['medical_record_id' => $medicalRecord->id]);

    // 7. Retour de la réponse
    return response()->json([
        'message' => 'User and associated records registered successfully',
        'user' => $user->load('patient'), // Charge le patient pour le retour
        'patient' => $patient,
        'medical_record' => $medicalRecord,
        'token' => $user->createToken('auth_token')->plainTextToken,
    ], 201);
}
public function login(Request $request)
    {
        // 1. Validation des données
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            // 'role' est le NOM du rôle que le front-end a sélectionné (ex: doctor)
            'role' => ['required', 'string'],
        ]);

        // 2. Tenter de trouver l'utilisateur par son email
        $user = User::where('email', $request->email)->first();

        // 3. Vérifier si l'utilisateur existe et si le mot de passe est correct
        if (! $user || ! Hash::check($request->password, $user->password)) {
            // Renvoie une erreur 401 ou une erreur de validation standard
            throw ValidationException::withMessages([
                'email' => ['Identifiants invalides.'],
            ]);
        }

        // 4. Charger les rôles de l'utilisateur
        $user->load('roles');

        // 5. Récupérer l'objet Role correspondant au nom demandé par le frontend
        $requestedRole = Role::where('name', $request->role)->first();

        // 6. Vérifier si le rôle demandé existe dans la base de données
        if (!$requestedRole) {
            throw ValidationException::withMessages([
                'role' => ['Le rôle sélectionné est invalide ou n\'existe pas.'],
            ]);
        }

        // 7. Vérifier si l'utilisateur authentifié possède le rôle demandé (via table pivot)
        // La méthode `contains` vérifie la présence du modèle Role dans la collection de rôles de l'utilisateur
        $userHasRequestedRole = $user->roles->contains('id', $requestedRole->id);

        if (!$userHasRequestedRole) {
            // Changement du message pour être plus précis
            throw ValidationException::withMessages([
                'role' => ['Rôle incorrect : vous n\'avez pas le rôle ' . $request->role . '.'],
            ]);
        }

        // 8. Supprimer anciens tokens et générer un nouveau
        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        // 9. Retourner la réponse : NOTEZ BIEN les clés 'role_name' et 'role_id'
        return response()->json([
            'message' => 'Connexion réussie',
            'user' => $user->toArray(),
            'role_name' => $requestedRole->name, // Le nom du rôle pour les getters Vue
            'role_id' => $requestedRole->id,     // L'ID du rôle pour la redirection Vue
            'token' => $token,
        ], 200);
    }
}
/**
 *
 *
 * $user = User::create($data->only([
        'first_name',
        'last_name',
        'birth_date',
        'phone',
        'counrty',
        'city',
        'profile_photo',
        'status',
        'address',
        'email',
        'password'
    ]));
 */
