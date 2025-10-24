<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SauvegardesUpdateRequest extends FormRequest
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
        'admin_id' => ['required', 'integer', 'exists:admins,id'],
        'type' => ['required', 'string'],
        'status' => ['required', 'string'],
        'file_path' => ['required', 'string'],
        'file_size' => ['required', 'numeric'],           // corrigé
        'start_at' => ['required'],
        'completed_at' => ['required'],
        'notes' => ['required', 'string'],                // corrigé
    ];
}
public function messages(): array
{
    return [
        'admin_id.required' => 'Le champ admin_id est obligatoire.',
        'admin_id.integer' => 'Le champ admin_id doit être un nombre entier.',
        'admin_id.exists' => 'L’administrateur spécifié est introuvable.',

        'type.required' => 'Le champ type est obligatoire.',
        'type.string' => 'Le champ type doit être une chaîne de caractères.',

        'status.required' => 'Le champ status est obligatoire.',
        'status.string' => 'Le champ status doit être une chaîne de caractères.',

        'file_path.required' => 'Le champ file_path est obligatoire.',
        'file_path.string' => 'Le champ file_path doit être une chaîne de caractères.',

        'file_size.required' => 'Le champ file_size est obligatoire.',
        'file_size.numeric' => 'Le champ file_size doit être une valeur numérique.',

        'start_at.required' => 'Le champ start_at est obligatoire.',
        'completed_at.required' => 'Le champ completed_at est obligatoire.',

        'notes.required' => 'Le champ notes est obligatoire.',
        'notes.string' => 'Le champ notes doit être une chaîne de caractères.',
    ];
}

}
