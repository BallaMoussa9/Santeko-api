<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HospitalPatientRequest extends FormRequest
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
        'user_id' => ['required', 'integer', 'exists:users,id'],
        'laboratory_id' => ['required', 'integer', 'exists:laboratorys,id'],
        'speciality' => ['required', 'string', 'max:255'],
        'qualification' => ['required', 'string', 'max:255'],
        'status' => ['required', 'string', 'max:255'],
    ];
}
public function messages(): array
{
    return [
        'user_id.required' => 'Le champ user_id est obligatoire.',
        'user_id.integer' => 'Le champ user_id doit être un nombre entier.',
        'user_id.exists' => 'L’utilisateur spécifié est introuvable.',

        'laboratory_id.required' => 'Le champ laboratory_id est obligatoire.',
        'laboratory_id.integer' => 'Le champ laboratory_id doit être un nombre entier.',
        'laboratory_id.exists' => 'Le laboratoire spécifié est introuvable.',

        'speciality.required' => 'Le champ speciality est obligatoire.',
        'speciality.string' => 'Le champ speciality doit être une chaîne de caractères.',
        'speciality.max' => 'Le champ speciality ne doit pas dépasser 255 caractères.',

        'qualification.required' => 'Le champ qualification est obligatoire.',
        'qualification.string' => 'Le champ qualification doit être une chaîne de caractères.',
        'qualification.max' => 'Le champ qualification ne doit pas dépasser 255 caractères.',

        'status.required' => 'Le champ status est obligatoire.',
        'status.string' => 'Le champ status doit être une chaîne de caractères.',
        'status.max' => 'Le champ status ne doit pas dépasser 255 caractères.',
    ];
}

}
