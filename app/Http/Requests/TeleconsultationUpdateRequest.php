<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TeleconsultationUpdateRequest extends FormRequest
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
        'consultation_id' => ['required', 'integer', 'exists:consultations,id'],
        'scheduled_at' => ['required', 'date'], // corrigé
        'duration' => ['required', 'integer'],  // corrigé
        'status' => ['required', 'string'],
        'comsultation_link' => ['required', 'string'],
        'note' => ['required', 'string'],       // corrigé
    ];
}
public function messages(): array
{
    return [
        'patient_id.required' => 'Le champ patient_id est obligatoire.',
        'patient_id.integer' => 'Le champ patient_id doit être un nombre entier.',
        'patient_id.exists' => 'Le patient spécifié est introuvable.',

        'doctor_id.required' => 'Le champ doctor_id est obligatoire.',
        'doctor_id.integer' => 'Le champ doctor_id doit être un nombre entier.',
        'doctor_id.exists' => 'Le docteur spécifié est introuvable.',

        'consultation_id.required' => 'Le champ consultation_id est obligatoire.',
        'consultation_id.integer' => 'Le champ consultation_id doit être un nombre entier.',
        'consultation_id.exists' => 'La consultation spécifiée est introuvable.',

        'scheduled_at.required' => 'La date prévue est obligatoire.',
        'scheduled_at.date' => 'Le champ scheduled_at doit être une date valide.',

        'duration.required' => 'La durée est obligatoire.',
        'duration.integer' => 'La durée doit être un nombre entier.',

        'status.required' => 'Le champ status est obligatoire.',
        'status.string' => 'Le champ status doit être une chaîne de caractères.',

        'comsultation_link.required' => 'Le lien de consultation est obligatoire.',
        'comsultation_link.string' => 'Le lien de consultation doit être une chaîne de caractères.',

        'note.required' => 'La note est obligatoire.',
        'note.string' => 'La note doit être une chaîne de caractères.',
    ];
}

}
