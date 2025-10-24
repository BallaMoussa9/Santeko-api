<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConsultationHistoryUpdateRequest extends FormRequest
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
        'medicalrecored_id' => ['required', 'integer', 'exists:medicalrecords,id'],
        'consultation_id' => ['required', 'integer', 'exists:consultations,id'],
        'date_consultation' => ['required', 'date', 'after_or_equal:today'],
        'type' => ['required', 'string', 'max:100'],
        'motif' => ['required', 'string', 'max:100'],
        'diagnostic' => ['required', 'string', 'max:100'], // corrigé
        'traitement' => ['required', 'string', 'max:100'], // corrigé
        'notes' => ['required', 'string', 'max:100'],      // corrigé
    ];
}
public function messages(): array
{
    return [
        'patient_id.required' => 'Le champ patient_id est requis.',
        'patient_id.integer' => 'Le champ patient_id doit être un entier.',
        'patient_id.exists' => 'Le patient spécifié est introuvable.',

        'doctor_id.required' => 'Le champ doctor_id est requis.',
        'doctor_id.integer' => 'Le champ doctor_id doit être un entier.',
        'doctor_id.exists' => 'Le docteur spécifié est introuvable.',

        'department_id.required' => 'Le champ department_id est requis.',
        'department_id.integer' => 'Le champ department_id doit être un entier.',
        'department_id.exists' => 'Le département spécifié est introuvable.',

        'medicalrecored_id.required' => 'Le champ medicalrecored_id est requis.',
        'medicalrecored_id.integer' => 'Le champ medicalrecored_id doit être un entier.',
        'medicalrecored_id.exists' => 'Le dossier médical spécifié est introuvable.',

        'consultation_id.required' => 'Le champ consultation_id est requis.',
        'consultation_id.integer' => 'Le champ consultation_id doit être un entier.',
        'consultation_id.exists' => 'La consultation spécifiée est introuvable.',

        'date_consultation.required' => 'La date de consultation est obligatoire.',
        'date_consultation.date' => 'La date de consultation doit être valide.',
        'date_consultation.after_or_equal' => 'La date doit être aujourd’hui ou ultérieure.',

        'type.required' => 'Le champ type est obligatoire.',
        'type.string' => 'Le champ type doit être une chaîne de caractères.',
        'type.max' => 'Le champ type ne doit pas dépasser 100 caractères.',

        'motif.required' => 'Le champ motif est obligatoire.',
        'motif.string' => 'Le champ motif doit être une chaîne de caractères.',
        'motif.max' => 'Le champ motif ne doit pas dépasser 100 caractères.',

        'diagnostic.required' => 'Le champ diagnostic est obligatoire.',
        'diagnostic.string' => 'Le champ diagnostic doit être une chaîne de caractères.',
        'diagnostic.max' => 'Le champ diagnostic ne doit pas dépasser 100 caractères.',

        'traitement.required' => 'Le champ traitement est obligatoire.',
        'traitement.string' => 'Le champ traitement doit être une chaîne de caractères.',
        'traitement.max' => 'Le champ traitement ne doit pas dépasser 100 caractères.',

        'notes.required' => 'Le champ notes est obligatoire.',
        'notes.string' => 'Le champ notes doit être une chaîne de caractères.',
        'notes.max' => 'Le champ notes ne doit pas dépasser 100 caractères.',
    ];
}

}
