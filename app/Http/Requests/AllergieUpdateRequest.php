<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AllergieUpdateRequest extends FormRequest
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
    'medical_record_id' => ['required', 'integer', 'exists:medicalrecord,id'],
    'patient_id' => ['required', 'integer', 'exists:patient,id'],
    'substance' => ['required', 'string', 'max:255'],
    'reaction_description' => ['required', 'string', 'max:255'],
    'severity' => ['required', 'string', 'max:255'],
    'recorded_by' => ['required', 'string', 'max:255'],
    'status' => ['required', 'string', 'max:255'],
    'notes' => ['required', 'string', 'max:255'],
        ];

    }
    public function messages(): array
{
    return [
        'medical_record_id.required' => 'Le champ dossier médical est obligatoire.',
        'medical_record_id.integer' => 'Le dossier médical doit être un identifiant numérique valide.',
        'medical_record_id.exists' => 'Le dossier médical spécifié est introuvable.',

        'patient_id.required' => 'Le champ patient est obligatoire.',
        'patient_id.integer' => 'Le patient doit être un identifiant numérique valide.',
        'patient_id.exists' => 'Le patient spécifié est introuvable.',

        'substance.required' => 'La substance allergène est requise.',
        'substance.string' => 'La substance doit être une chaîne de caractères.',
        'substance.max' => 'La substance ne doit pas dépasser 255 caractères.',

        'reaction_description.required' => 'La description de la réaction est requise.',
        'reaction_description.string' => 'La description doit être une chaîne de caractères.',
        'reaction_description.max' => 'La description ne doit pas dépasser 255 caractères.',

        'severity.required' => 'La gravité doit être précisée.',
        'severity.string' => 'La gravité doit être une chaîne de caractères.',
        'severity.max' => 'La gravité ne doit pas dépasser 255 caractères.',

        'recorded_by.required' => 'Veuillez préciser par qui cette allergie a été enregistrée.',
        'recorded_by.string' => 'Ce champ doit être une chaîne de caractères.',
        'recorded_by.max' => 'Ce champ ne doit pas dépasser 255 caractères.',

        'status.required' => 'Le statut de l’allergie est obligatoire.',
        'status.string' => 'Le statut doit être une chaîne de caractères.',
        'status.max' => 'Le statut ne doit pas dépasser 255 caractères.',

        'notes.required' => 'Les notes sont obligatoires.',
        'notes.string' => 'Les notes doivent être une chaîne de caractères.',
        'notes.max' => 'Les notes ne doivent pas dépasser 255 caractères.',
    ];
}

}
