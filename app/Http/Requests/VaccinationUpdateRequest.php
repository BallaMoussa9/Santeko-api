<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VaccinationUpdateRequest extends FormRequest
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
        'vaccine_id' => ['required', 'integer', 'exists:vaccines,id'],
        'nurse_id' => ['required', 'integer', 'exists:nurses,id'],
        'patient_id' => ['required', 'integer', 'exists:patients,id'],
        'medicalrecord_id' => ['required', 'integer', 'exists:medicalrecords,id'],
        'total_required_dose' => ['required', 'integer'],
        'administration_date' => ['required', 'date'],
        'status' => ['required', 'string'],
        'localiter' => ['required', 'string'],
        'notes' => ['required', 'string'], // corrigé ici
    ];
}
public function messages(): array
{
    return [
        'vaccine_id.required' => 'Le champ vaccine_id est obligatoire.',
        'vaccine_id.integer' => 'Le champ vaccine_id doit être un entier.',
        'vaccine_id.exists' => 'Le vaccin spécifié est introuvable.',

        'nurse_id.required' => 'Le champ nurse_id est obligatoire.',
        'nurse_id.integer' => 'Le champ nurse_id doit être un entier.',
        'nurse_id.exists' => 'L’infirmier spécifié est introuvable.',

        'patient_id.required' => 'Le champ patient_id est obligatoire.',
        'patient_id.integer' => 'Le champ patient_id doit être un entier.',
        'patient_id.exists' => 'Le patient spécifié est introuvable.',

        'medicalrecord_id.required' => 'Le champ medicalrecord_id est obligatoire.',
        'medicalrecord_id.integer' => 'Le champ medicalrecord_id doit être un entier.',
        'medicalrecord_id.exists' => 'Le dossier médical spécifié est introuvable.',

        'total_required_dose.required' => 'Le nombre de doses requises est obligatoire.',
        'total_required_dose.integer' => 'Le champ total_required_dose doit être un entier.',

        'administration_date.required' => 'La date d’administration est obligatoire.',
        'administration_date.date' => 'La date d’administration doit être une date valide.',

        'status.required' => 'Le champ status est obligatoire.',
        'status.string' => 'Le champ status doit être une chaîne de caractères.',

        'localiter.required' => 'Le champ localiter est obligatoire.',
        'localiter.string' => 'Le champ localiter doit être une chaîne de caractères.',

        'notes.required' => 'Le champ notes est obligatoire.',
        'notes.string' => 'Le champ notes doit être une chaîne de caractères.',
    ];
}

}
