<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DoctorRequest extends FormRequest
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
        return [
            // Règles pour le modèle User
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'birth_date' => ['nullable', 'date'],
            'phone' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],

            // Règles pour le modèle Doctor
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'speciality' => ['required', 'string', 'max:255'],
            'numero_ordre' => ['required', 'string', 'max:255', 'unique:doctors,numero_ordre'],
            'biography' => ['nullable', 'string', 'max:2000'],
            'experience' => ['required', 'integer', 'max:100'],
            'status' => ['required', 'string', 'max:255'],
            'numero_professionel' => ['required', 'string', 'max:255', 'unique:doctors,numero_professionel'],
        ];
    }

    public function messages(): array
    {
        return [
            // Messages de validation pour les champs de l'utilisateur
            'first_name.required' => 'Le prénom est obligatoire.',
            'last_name.required' => 'Le nom de famille est obligatoire.',
            'email.required' => 'L\'adresse e-mail est obligatoire.',
            'email.email' => 'L\'adresse e-mail doit être une adresse e-mail valide.',
            'email.unique' => 'Cette adresse e-mail est déjà utilisée.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',

            // Messages de validation pour les champs du docteur
            'department_id.required' => 'Le champ department_id est obligatoire.',
            'department_id.exists' => 'Le département spécifié est introuvable.',

            'speciality.required' => 'Le champ speciality est obligatoire.',
            'numero_ordre.required' => 'Le champ numero_ordre est obligatoire.',
            'numero_ordre.unique' => 'Ce numéro d’ordre est déjà utilisé.',

            'biography.max' => 'La biographie ne doit pas dépasser 2000 caractères.',

            'experience.required' => 'Le champ experience est obligatoire.',
            'experience.integer' => 'Le champ experience doit être un nombre entier.',
            'experience.max' => 'Le champ experience ne doit pas dépasser 100.',

            'status.required' => 'Le champ status est obligatoire.',
            'numero_professionel.required' => 'Le champ numero_professionel est obligatoire.',
            'numero_professionel.unique' => 'Ce numéro professionnel est déjà utilisé.',
        ];
    }
}
