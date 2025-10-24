<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeathUpdateRequest extends FormRequest
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
        'nurse_id' => ['required', 'integer', 'exists:nurses,id'],
        'date_deces' => ['required', 'date'], // corrigé
        'lieu_deces' => ['required', 'string', 'max:500'],
        'cause_deces' => ['required', 'string', 'max:500'],
        'circonstances_deces' => ['required', 'string'], // corrigé
        'status' => ['required', 'string', 'max:200'],
        'numero_acte_deces' => ['required', 'string', 'max:200'],
    ];
}
public function messages(): array
{
    return [
        'patient_id.required' => 'Le champ patient_id est obligatoire.',
        'patient_id.integer' => 'Le champ patient_id doit être un entier.',
        'patient_id.exists' => 'Le patient spécifié est introuvable.',

        'doctor_id.required' => 'Le champ doctor_id est obligatoire.',
        'doctor_id.integer' => 'Le champ doctor_id doit être un entier.',
        'doctor_id.exists' => 'Le docteur spécifié est introuvable.',

        'department_id.required' => 'Le champ department_id est obligatoire.',
        'department_id.integer' => 'Le champ department_id doit être un entier.',
        'department_id.exists' => 'Le département spécifié est introuvable.',

        'nurse_id.required' => 'Le champ nurse_id est obligatoire.',
        'nurse_id.integer' => 'Le champ nurse_id doit être un entier.',
        'nurse_id.exists' => 'L’infirmier ou infirmière spécifié(e) est introuvable.',

        'date_deces.required' => 'La date de décès est obligatoire.',
        'date_deces.date' => 'La date de décès doit être une date valide.',

        'lieu_deces.required' => 'Le lieu de décès est obligatoire.',
        'lieu_deces.string' => 'Le lieu de décès doit être une chaîne de caractères.',
        'lieu_deces.max' => 'Le lieu de décès ne doit pas dépasser 500 caractères.',

        'cause_deces.required' => 'La cause du décès est obligatoire.',
        'cause_deces.string' => 'La cause du décès doit être une chaîne de caractères.',
        'cause_deces.max' => 'La cause du décès ne doit pas dépasser 500 caractères.',

        'circonstances_deces.required' => 'Les circonstances du décès sont obligatoires.',
        'circonstances_deces.string' => 'Les circonstances doivent être une chaîne de caractères.',

        'status.required' => 'Le statut est obligatoire.',
        'status.string' => 'Le statut doit être une chaîne de caractères.',
        'status.max' => 'Le statut ne doit pas dépasser 200 caractères.',

        'numero_acte_deces.required' => 'Le numéro d’acte de décès est obligatoire.',
        'numero_acte_deces.string' => 'Le numéro d’acte de décès doit être une chaîne de caractères.',
        'numero_acte_deces.max' => 'Le numéro d’acte de décès ne doit pas dépasser 200 caractères.',
    ];
}

}
