<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SOSUpdateRequest extends FormRequest
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
        'status' => ['required', 'string', 'max:100', Rule::in(['open', 'pending', 'closed'])], // exemple d'enum
        'type' => ['required', 'string', 'max:100'],
        'location' => ['required', 'string', 'max:200'],
        'description' => ['required', 'string'],
        'initiated_at' => ['required', 'date'],
        'responded_at' => ['required', 'date'],
        'closed_at' => ['required', 'date'],
    ];
}
public function messages(): array
{
    return [
        'patient_id.required' => 'Le champ patient_id est obligatoire.',
        'patient_id.integer' => 'Le champ patient_id doit être un nombre entier.',
        'patient_id.exists' => 'Le patient spécifié est introuvable.',

        'status.required' => 'Le champ status est obligatoire.',
        'status.string' => 'Le champ status doit être une chaîne de caractères.',
        'status.max' => 'Le champ status ne doit pas dépasser 100 caractères.',
        'status.in' => 'Le champ status doit contenir une valeur valide.',

        'type.required' => 'Le champ type est obligatoire.',
        'type.string' => 'Le champ type doit être une chaîne de caractères.',
        'type.max' => 'Le champ type ne doit pas dépasser 100 caractères.',

        'location.required' => 'Le champ location est obligatoire.',
        'location.string' => 'Le champ location doit être une chaîne de caractères.',
        'location.max' => 'Le champ location ne doit pas dépasser 200 caractères.',

        'description.required' => 'Le champ description est obligatoire.',
        'description.string' => 'Le champ description doit être une chaîne de caractères.',

        'initiated_at.required' => 'La date de début est obligatoire.',
        'initiated_at.date' => 'La date de début doit être une date valide.',

        'responded_at.required' => 'La date de réponse est obligatoire.',
        'responded_at.date' => 'La date de réponse doit être une date valide.',

        'closed_at.required' => 'La date de clôture est obligatoire.',
        'closed_at.date' => 'La date de clôture doit être une date valide.',
    ];
}
}
