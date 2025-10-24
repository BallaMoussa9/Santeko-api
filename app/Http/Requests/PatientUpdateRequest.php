<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PatientUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $patient = $this->route('patient');
        $user = $patient?->user;

        // Définition des valeurs ENUM autorisées pour le statut du patient
        $validStatuses = ['actif', 'en_traitement', 'stable', 'critique', 'sorti', 'archive'];

        // Règles pour l'email
        // On vérifie l'unicité uniquement si l'email est présent et différent de l'email actuel de l'utilisateur.
        $emailRules = ['sometimes', 'email', 'max:255'];
        if ($user && $this->has('email') && $this->input('email') !== $user->email) {
            $emailRules[] = Rule::unique('users', 'email');
        }

        return [
            // --- Infos Utilisateur (table 'users') ---
            // Le changement clé est l'utilisation de 'sometimes' au lieu de 'present' ou 'required'.
            // Si le champ n'est pas envoyé par le formulaire (comme pour l'infirmière), il est ignoré.
            'first_name' => ['sometimes', 'string', 'max:255'],
            'last_name' => ['sometimes', 'string', 'max:255'],
            'birth_date' => ['sometimes', 'date', 'before:today'],
            'phone' => ['sometimes', 'string', 'max:15'],
            'country' => ['nullable', 'string', 'max:255'],
            'city' => ['sometimes', 'string', 'max:255'],
            'address' => ['sometimes', 'string', 'max:255'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'email' => $emailRules,
            'password' => ['nullable', 'string', 'min:8'],

            // --- Infos Patient (table 'patients') ---
            'genre' => ['sometimes', 'string', 'max:255'],
            'group_sanguine' => ['sometimes', 'string', 'max:255'],

            // Les champs mis à jour par l'infirmière (poids, taille, status, bed_id)
            'poids' => ['nullable', 'numeric', 'min:0', 'max:999.9'], // Exemple de max
            'taille' => ['nullable', 'numeric', 'min:0', 'max:300'],
            'status' => ['nullable', Rule::in($validStatuses)],
            'bed_id' => ['nullable', 'exists:beds,id'], // Ajouté si ce champ est géré ici

            // Autres champs optionnels du patient
            'telephone_urgence' => ['nullable', 'string', 'max:255'],
            'assurance_maladie' => ['nullable', 'string', 'max:255'],
            'numero_urgence' => ['nullable', 'string', 'max:255'],

            // Attributs de date/heure
            'last_consultation_date' => ['nullable', 'date'],

            // --- Infos Dossier Médical (table 'medical_records') ---
            'maladies_chroniques' => ['nullable', 'string', 'max:255'],
            'allergies' => ['nullable', 'string', 'max:255'],
            'numero_dossier' => ['nullable', 'string', 'max:255'],
            'doctor_id' => ['nullable', 'exists:doctors,id'],
            'hospital_id' => ['nullable', 'exists:hospitals,id'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        // Les messages d'erreur ne sont nécessaires que si le champ est VRAIMENT envoyé et échoue à la validation.
        return [
            // --- Messages Utilisateur ---
            'first_name.sometimes' => 'Le prénom est obligatoire si fourni.',
            'last_name.sometimes' => 'Le nom est obligatoire si fourni.',
            'birth_date.sometimes' => 'La date de naissance est obligatoire si fournie.',
            'phone.sometimes' => 'Le numéro de téléphone est obligatoire si fourni.',
            'city.sometimes' => 'La ville est obligatoire si fournie.',
            'address.sometimes' => 'L\'adresse est obligatoire si fournie.',
            'email.sometimes' => 'L\'adresse email est obligatoire si fournie.',
            'email.unique' => 'Cette adresse email est déjà utilisée par un autre utilisateur.',

            // --- Messages Patient ---
            'genre.sometimes' => 'Le genre est obligatoire si fourni.',
            'group_sanguine.sometimes' => 'Le groupe sanguin est obligatoire si fourni.',
            'poids.string' => 'Le poids doit être une chaîne de caractères.',
            'taille.string' => 'La taille doit être une chaîne de caractères.',

            // 🔑 MESSAGES NOUVEAUX ATTRIBUTS
            'status.in' => 'Le statut du patient sélectionné est invalide.',
            'last_consultation_date.date' => 'La date de dernière consultation doit être une date valide.',

            // --- Messages Dossier Médical ---
            'doctor_id.exists' => 'Le médecin sélectionné est invalide.',
            'hospital_id.exists' => 'L\'hôpital sélectionné est invalide.',
        ];
    }
}
