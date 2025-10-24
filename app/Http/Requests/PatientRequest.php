<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // Ajouté pour l'ENUM

class PatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Définition des valeurs ENUM autorisées pour le statut du patient
        $validStatuses = ['actif', 'en_traitement', 'stable', 'critique', 'sorti', 'archive'];

        return [
            // --- Infos patient ---
            'genre' => ['required', 'string', 'max:255'],
            'group_sanguine' => ['required', 'string', 'max:255'],
            'telephone_urgence' => ['nullable', 'string', 'max:255'],
            'maladies_chroniques' => ['nullable', 'string', 'max:255'],
            'assurance_maladie' => ['nullable', 'string', 'max:255'],
            'numero_urgence' => ['nullable', 'string', 'max:255'],
            'poids' => ['nullable', 'numeric', 'max:500'],
            'taille' => ['nullable', 'numeric', 'max:300'],

            // 🔑 NOUVEAUX ATTRIBUTS
            'status' => ['required', Rule::in($validStatuses)], // Validation stricte de l'ENUM
            'last_consultation_date' => ['nullable', 'date', 'after_or_equal:today'], // Date à partir d'aujourd'hui
            // Note: Le champ est nullable, mais s'il est envoyé, il doit suivre la règle de date.

            // --- Infos user ---
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'birth_date' => ['required', 'date', 'before:today'],
            'phone'      => ['required', 'string', 'max:15'],
            'country'    => ['required', 'string', 'max:255'],
            'city'       => ['required', 'string', 'max:255'],
            'address'    => ['required', 'string', 'max:255'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'email'      => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'   => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required_with:password', 'string', 'min:8'],

            // --- Champs spécifiques ---
            'hospital_id' => ['required', 'exists:hospitals,id'],
            'doctor_id' => ['nullable', 'exists:doctors,id'], // Corrigé pour pointer vers la table 'doctors'
        ];
    }

    public function messages(): array
    {
        return [
            // Patient
            'genre.required' => 'Le champ genre est obligatoire.',
            'group_sanguine.required' => 'Le champ groupe sanguin est obligatoire.',

            'poids.numeric'  => 'Le poids doit être un nombre.',
            'poids.max'      => 'Le poids ne doit pas dépasser 500 kg.',

            'taille.numeric'  => 'La taille doit être un nombre.',
            'taille.max'      => 'La taille ne doit pas dépasser 300 cm.',

            // 🔑 MESSAGES NOUVEAUX ATTRIBUTS
            'status.required' => 'Le statut du patient est obligatoire.',
            'status.in' => 'Le statut du patient sélectionné est invalide.',
            'last_consultation_date.date' => 'La date de dernière consultation doit être une date valide.',
            'last_consultation_date.after_or_equal' => 'La date de dernière consultation ne peut pas être antérieure à aujourd\'hui.',


            // User
            'first_name.required' => 'Le prénom est obligatoire.',
            'last_name.required' => 'Le nom est obligatoire.',
            'birth_date.required' => 'La date de naissance est obligatoire.',
            'birth_date.before'   => 'La date de naissance doit être antérieure à aujourd\'hui.',
            'phone.required' => 'Le numéro de téléphone est obligatoire.',
            'country.required' => 'Le pays est obligatoire.',
            'city.required' => 'La ville est obligatoire.',
            'address.required' => 'L\'adresse est obligatoire.',
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.unique'   => 'Cette adresse email est déjà utilisée.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',

            // Champs spécifiques
            'hospital_id.required' => 'L\'hôpital est obligatoire.',
            'hospital_id.exists' => 'L\'hôpital sélectionné n\'est pas valide.',
            'doctor_id.exists' => 'Le docteur sélectionné n\'est pas valide.',
        ];
    }
}
