<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnalyseUpdateRequest extends FormRequest
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
            'analyses_request_id' => ['required', 'integer', 'exists:analyses_requests,id'],
            'laboratory_id' =>['required', 'integer', 'exists:laboratorys,id'],
            'patient_id' =>['required', 'integer', 'exists:patients,id'],
            'consultation_id' =>['required', 'integer', 'exists:consultations,id'],
            'labtechnicians_id' =>['required', 'integer', 'exists:labtechnicians,id'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:255'],
        ];
    }
    public function messages(): array
{
    return [
        'analyses_request_id.required' => 'Le champ analyses_request_id est requis.',
        'analyses_request_id.integer' => 'Le champ analyses_request_id doit être un nombre entier.',
        'analyses_request_id.exists' => 'La requête d’analyse spécifiée est introuvable.',

        'laboratory_id.required' => 'Le champ laboratory_id est requis.',
        'laboratory_id.integer' => 'Le champ laboratory_id doit être un nombre entier.',
        'laboratory_id.exists' => 'Le laboratoire spécifié est introuvable.',

        'patient_id.required' => 'Le champ patient_id est requis.',
        'patient_id.integer' => 'Le champ patient_id doit être un nombre entier.',
        'patient_id.exists' => 'Le patient spécifié est introuvable.',

        'consultation_id.required' => 'Le champ consultation_id est requis.',
        'consultation_id.integer' => 'Le champ consultation_id doit être un nombre entier.',
        'consultation_id.exists' => 'La consultation spécifiée est introuvable.',

        'labtechnicians_id.required' => 'Le champ labtechnicians_id est requis.',
        'labtechnicians_id.integer' => 'Le champ labtechnicians_id doit être un nombre entier.',
        'labtechnicians_id.exists' => 'Le technicien spécifié est introuvable.',

        'name.required' => 'Le champ name est obligatoire.',
        'name.string' => 'Le champ name doit être une chaîne de caractères.',
        'name.max' => 'Le champ name ne doit pas dépasser 255 caractères.',

        'type.required' => 'Le champ type est obligatoire.',
        'type.string' => 'Le champ type doit être une chaîne de caractères.',
        'type.max' => 'Le champ type ne doit pas dépasser 255 caractères.',

        'status.required' => 'Le champ status est obligatoire.',
        'status.string' => 'Le champ status doit être une chaîne de caractères.',
        'status.max' => 'Le champ status ne doit pas dépasser 255 caractères.',
    ];
}

}
