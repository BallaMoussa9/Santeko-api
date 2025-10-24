<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Doctor;

class DoctorUpdateRequest extends FormRequest
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
     *///'exists:departments,id'],
    public function rules(): array
{
    $doctorId = $this->route('doctor');

// 2. Récupère le modèle Doctor pour trouver son user_id
// Utilisez findOrFail() si vous êtes certain que le médecin existe
        $doctor = Doctor::findOrFail($doctorId);
        $userId = $doctor->user_id;
    return [
       // 'user_id' => ['required', 'integer', 'exists:users,id'],
        'department_id' => ['nullable', 'integer', 'exists:departments,id'],
        'speciality' => ['required', 'string', 'max:255'],
        'numero_ordre' => ['required', 'string', 'max:255'],
        'biography' => ['required', 'string', 'max:255'], // corrigé
        'experience' => ['required', 'integer', 'max:255'], // corrigé
        'status' => ['required', 'string', 'max:255'],
        'numero_professionel' => ['required', 'string', 'max:255'],
        'first_name'    => 'required|string|max:255',
            'last_name'     => 'required| string| max:255',
            'birth_date'    => 'required|date|before:today',
            'phone'         => 'required| string| max:15',
            'country'       => 'required| string| max:255',
            'city'          => 'required| string| max:255',
            'address'       => 'required| string| max:255',
            'profile_photo' => 'nullable| image| mimes:jpeg,png,jpg,gif,svg| max:2048',
             'email' => [
        'required',
        'email',
        'max:255',
        // ✅ Règle unique qui ignore l'ID de l'utilisateur en cours de modification
        Rule::unique('users', 'email')->ignore($userId),
    ],
            'password' => [
            'nullable', // Permet que le champ soit null ou vide
            'string',
            'min:8',
        ],
    ];
}
public function messages(): array
{
    return [
        // 'user_id.required' => 'Le champ user_id est obligatoire.',
        // 'user_id.integer' => 'Le champ user_id doit être un nombre entier.',
        // 'user_id.exists' => 'L’utilisateur spécifié est introuvable.',

        // 'department_id.required' => 'Le champ department_id est obligatoire.',
        // 'department_id.integer' => 'Le champ department_id doit être un nombre entier.',
        // 'department_id.exists' => 'Le département spécifié est introuvable.',

        'speciality.required' => 'Le champ speciality est obligatoire.',
        'speciality.string' => 'Le champ speciality doit être une chaîne de caractères.',
        'speciality.max' => 'Le champ speciality ne doit pas dépasser 255 caractères.',

        'numero_ordre.required' => 'Le champ numero_ordre est obligatoire.',
        'numero_ordre.string' => 'Le champ numero_ordre doit être une chaîne de caractères.',
        'numero_ordre.max' => 'Le champ numero_ordre ne doit pas dépasser 255 caractères.',

        'biography.required' => 'La biographie est obligatoire.',
        'biography.string' => 'Le champ biography doit être une chaîne de caractères.',
        'biography.max' => 'Le champ biography ne doit pas dépasser 255 caractères.',

        'experience.required' => 'Le champ experience est obligatoire.',
        'experience.integer' => 'Le champ experience doit être un nombre entier.',
        'experience.max' => 'Le champ experience ne doit pas dépasser 255 caractères.',

        'status.required' => 'Le champ status est obligatoire.',
        'status.string' => 'Le champ status doit être une chaîne de caractères.',
        'status.max' => 'Le champ status ne doit pas dépasser 255 caractères.',

        'numero_professionel.required' => 'Le champ numero_professionel est obligatoire.',
        'numero_professionel.string' => 'Le champ numero_professionel doit être une chaîne de caractères.',
        'numero_professionel.max' => 'Le champ numero_professionel ne doit pas dépasser 255 caractères.',
        'first_name.required' => 'Le prénom est obligatoire.',
        'first_name.string'   => 'Le prénom doit être une chaîne de caractères.',
        'first_name.max'      => 'Le prénom ne peut pas dépasser 255 caractères.',

        'last_name.required' => 'Le nom est obligatoire.',
        'last_name.string'   => 'Le nom doit être une chaîne de caractères.',
        'last_name.max'      => 'Le nom ne peut pas dépasser 255 caractères.',

        'birth_date.required' => 'La date de naissance est obligatoire.',
        'birth_date.date'     => 'La date de naissance doit être une date valide.',
        'birth_date.before'   => 'La date de naissance doit être antérieure à aujourd\'hui.',

        'country.required' => 'Le pays est obligatoire.',
        'country.string'   => 'Le pays doit être une chaîne de caractères.',
        'country.max'      => 'Le pays ne peut pas dépasser 255 caractères.',

        'city.required' => 'La ville est obligatoire.',
        'city.string'   => 'La ville doit être une chaîne de caractères.',
        'city.max'      => 'La ville ne peut pas dépasser 255 caractères.',

        'address.required' => 'L\'adresse est obligatoire.',
        'address.string'   => 'L\'adresse doit être une chaîne de caractères.',
        'address.max'      => 'L\'adresse ne peut pas dépasser 255 caractères.',

        'phone.required' => 'Le numéro de téléphone est obligatoire.',
        'phone.string'   => 'Le numéro de téléphone doit être une chaîne de caractères.',
        'phone.max'      => 'Le numéro de téléphone ne peut pas dépasser 15 caractères.',

        'profile_photo.image' => 'La photo de profil doit être une image.',
        'profile_photo.mimes' => 'La photo de profil doit être de type : jpeg, png, jpg, gif, svg.',
        'profile_photo.max'   => 'La taille maximale de la photo de profil est de 2 Mo.',

        'email.required' => 'L\'adresse email est obligatoire.',
        'email.email'    => 'L\'adresse email doit être valide.',
        'email.max'      => 'L\'adresse email ne peut pas dépasser 255 caractères.',
        'email.unique'   => 'Cette adresse email est déjà utilisée.',

        'password.required'  => 'Le mot de passe est obligatoire.',
        'password.string'    => 'Le mot de passe doit être une chaîne de caractères.',
        'password.min'       => 'Le mot de passe doit contenir au moins 8 caractères.',
        'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
    ];
}

}
