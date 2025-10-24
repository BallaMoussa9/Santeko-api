<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConsultationUpdateRequest extends FormRequest
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
        'prescription_id' => ['required', 'integer', 'exists:prescriptions,id'],
        'date_prescription' => ['required', 'date', 'after_or_equal:today'],
        'type' => ['required', 'string', 'max:100'],
        'motif' => ['required', 'string', 'max:100'],
        'diagnostic' => ['required', 'string'], // corrigé
        'status' => ['required', 'string', 'max:100'],
        'observations' => ['required', 'string', 'max:100'], // corrigé
    ];
}
public function messages(): array
{
    return [
        'patient_id.required' => 'Le champ patient_id est obligatoire.',
        'patient_id.integer' => 'Le champ patient_id doit être un nombre entier.',
        'patient_id.exists' => 'Le patient spécifié est introuvable dans la base de données.',

        'doctor_id.required' => 'Le champ doctor_id est obligatoire.',
        'doctor_id.integer' => 'Le champ doctor_id doit être un nombre entier.',
        'doctor_id.exists' => 'Le docteur spécifié est introuvable.',

        'prescription_id.required' => 'Le champ prescription_id est obligatoire.',
        'prescription_id.integer' => 'Le champ prescription_id doit être un nombre entier.',
        'prescription_id.exists' => 'La prescription spécifiée est introuvable.',

        'date_prescription.required' => 'La date de prescription est obligatoire.',
        'date_prescription.date' => 'Le champ date_prescription doit être une date valide.',
        'date_prescription.after_or_equal' => 'La date doit être aujourd’hui ou ultérieure.',

        'type.required' => 'Le champ type est obligatoire.',
        'type.string' => 'Le champ type doit être une chaîne de caractères.',
        'type.max' => 'Le champ type ne doit pas dépasser 100 caractères.',

        'motif.required' => 'Le champ motif est obligatoire.',
        'motif.string' => 'Le champ motif doit être une chaîne de caractères.',
        'motif.max' => 'Le champ motif ne doit pas dépasser 100 caractères.',

        'diagnostic.required' => 'Le champ diagnostic est obligatoire.',
        'diagnostic.string' => 'Le champ diagnostic doit être une chaîne de caractères.',

        'status.required' => 'Le champ status est obligatoire.',
        'status.string' => 'Le champ status doit être une chaîne de caractères.',
        'status.max' => 'Le champ status ne doit pas dépasser 100 caractères.',

        'observations.required' => 'Le champ observations est obligatoire.',
        'observations.string' => 'Le champ observations doit être une chaîne de caractères.',
        'observations.max' => 'Le champ observations ne doit pas dépasser 100 caractères.',
    ];
}
}
