<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // Pas strictement nécessaire pour le "store" si pas de 'unique' avec 'ignore'

class NurseStoreRequest extends FormRequest
{
    /**
     * Déterminer si l'utilisateur est autorisé à faire cette requête.
     * C'est ici que vous définissez qui a le droit de créer un infirmier.
     */
    public function authorize(): bool
    {
        // 1. Vérifier si un utilisateur est authentifié
        if (!auth()->check()) {
            return false;
        }

        // 2. Récupérer l'utilisateur authentifié
        $user = auth()->user();

        // 3. Utiliser la méthode hasRole() de votre modèle User
        return $user->hasRole('admin');
    }

    /**
     * Obtenir les règles de validation qui s'appliquent à la requête.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // --- Informations de l'utilisateur (User Model) ---
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'birth_date' => ['required', 'date', 'before:today'],
            'phone' => ['required', 'string', 'max:15'], // Exemple: format '+33612345678' ou '0612345678'
            'country' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'], // Max 2MB
            'email' => ['required', 'email', 'max:255', 'unique:users,email'], // L'email doit être unique dans la table 'users'
            'password' => ['required', 'string', 'min:8', 'confirmed'], // 'confirmed' requiert un champ 'password_confirmation'

            // --- Informations spécifiques à l'infirmier (Nurse Model) ---
            // Le département est facultatif ici. S'il est fourni, il doit exister dans la table 'departments'.
            'department_id' => ['nullable', 'exists:departments,id'],
        ];
    }

    /**
     * Obtenir les messages d'erreur personnalisés pour les règles de validation.
     * Cela rend les messages d'erreur plus amicaux pour l'utilisateur.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // Messages pour les champs utilisateur
            'first_name.required' => 'Le prénom est obligatoire.',
            'last_name.required' => 'Le nom de famille est obligatoire.',
            'birth_date.required' => 'La date de naissance est obligatoire.',
            'birth_date.date' => 'La date de naissance doit être une date valide.',
            'birth_date.before' => 'La date de naissance doit être antérieure à aujourd\'hui.',
            'phone.required' => 'Le numéro de téléphone est obligatoire.',
            'phone.max' => 'Le numéro de téléphone ne doit pas dépasser 15 caractères.',
            'city.required' => 'La ville est obligatoire.',
            'address.required' => 'L\'adresse est obligatoire.',
            'profile_photo.image' => 'La photo de profil doit être une image.',
            'profile_photo.mimes' => 'La photo de profil doit être de type JPG, PNG, JPEG, GIF ou SVG.',
            'profile_photo.max' => 'La taille maximale de la photo de profil est de 2 Mo.',
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'L\'adresse email doit être une adresse valide.',
            'email.max' => 'L\'adresse email ne doit pas dépasser 255 caractères.',
            'email.unique' => 'Cette adresse email est déjà utilisée par un autre utilisateur.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.string' => 'Le mot de passe doit être une chaîne de caractères.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',

            // Messages pour les champs infirmier
            'department_id.exists' => 'Le département sélectionné n\'existe pas.',
        ];
    }
}
