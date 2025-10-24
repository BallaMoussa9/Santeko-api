<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BirthRequest extends FormRequest
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
        'patient_id' => ['required', 'integer', 'exists:patients,id'],
        'doctor_id' => ['required', 'integer', 'exists:doctors,id'],
        'department_id' => ['required', 'integer', 'exists:departments,id'],
        'firstname' => ['required', 'string', 'max:255'],
        'lastname' => ['required', 'string', 'max:255'],
        'sexe' => ['required', 'string', 'max:255'],
        'date_naissance' => ['required', 'date', 'after_or_equal:today'],
        'lieu_naissance' => ['required', 'string'],
        'father_name' => ['required', 'string', 'max:255'],
        'poids' => ['required', 'numeric'], // corrigé
        'taille' => ['required', 'numeric'], // corrigé
        'heur_naissance' => ['required', 'date_format:H:i'], // corrigé
        'status' => ['required', 'string', 'max:100'],
        'numero_acte_naissancce' => ['required', 'string'],
    ];
}
public function messages(): array
{
    return [
        'patient_id.required' => 'Le champ patient_id est requis.',
        'patient_id.integer' => 'Le champ patient_id doit être un nombre entier.',
        'patient_id.exists' => 'Le patient spécifié est introuvable.',

        'doctor_id.required' => 'Le champ doctor_id est requis.',
        'doctor_id.integer' => 'Le champ doctor_id doit être un nombre entier.',
        'doctor_id.exists' => 'Le docteur spécifié est introuvable.',

        'department_id.required' => 'Le champ department_id est requis.',
        'department_id.integer' => 'Le champ department_id doit être un nombre entier.',
        'department_id.exists' => 'Le département spécifié est introuvable.',

        'firstname.required' => 'Le prénom est obligatoire.',
        'firstname.string' => 'Le prénom doit être une chaîne de caractères.',
        'firstname.max' => 'Le prénom ne doit pas dépasser 255 caractères.',

        'lastname.required' => 'Le nom est obligatoire.',
        'lastname.string' => 'Le nom doit être une chaîne de caractères.',
        'lastname.max' => 'Le nom ne doit pas dépasser 255 caractères.',

        'sexe.required' => 'Le sexe est requis.',
        'sexe.string' => 'Le sexe doit être une chaîne de caractères.',
        'sexe.max' => 'Le sexe ne doit pas dépasser 255 caractères.',

        'date_naissance.required' => 'La date de naissance est obligatoire.',
        'date_naissance.date' => 'La date de naissance doit être une date valide.',
        'date_naissance.after_or_equal' => 'La date de naissance ne peut pas être antérieure à aujourd’hui.',

        'lieu_naissance.required' => 'Le lieu de naissance est obligatoire.',
        'lieu_naissance.string' => 'Le lieu de naissance doit être une chaîne de caractères.',

        'father_name.required' => 'Le nom du père est requis.',
        'father_name.string' => 'Le nom du père doit être une chaîne de caractères.',
        'father_name.max' => 'Le nom du père ne doit pas dépasser 255 caractères.',

        'poids.required' => 'Le poids est obligatoire.',
        'poids.numeric' => 'Le poids doit être une valeur numérique.',

        'taille.required' => 'La taille est obligatoire.',
        'taille.numeric' => 'La taille doit être une valeur numérique.',

        'heur_naissance.required' => 'L’heure de naissance est obligatoire.',
        'heur_naissance.date_format' => 'L’heure de naissance doit être au format HH:mm.',

        'status.required' => 'Le champ status est obligatoire.',
        'status.string' => 'Le champ status doit être une chaîne de caractères.',
        'status.max' => 'Le champ status ne doit pas dépasser 100 caractères.',

        'numero_acte_naissancce.required' => 'Le numéro d’acte de naissance est obligatoire.',
        'numero_acte_naissancce.string' => 'Le numéro d’acte doit être une chaîne de caractères.',
    ];
}

}
