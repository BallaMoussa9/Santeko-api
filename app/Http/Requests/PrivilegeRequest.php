<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class PrivilegeRequest extends FormRequest
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
        'role_id' => ['required', 'integer', 'exists:roles,id'],
        'name' => ['required', 'string', 'max:255'],
        'description' => ['required', 'string', 'max:255'],
        'type' => ['required', 'string', Rule::in(['admin', 'user', 'guest'])], // exemple de valeurs
    ];
}
public function messages(): array
{
    return [
        'role_id.required' => 'Le champ role_id est obligatoire.',
        'role_id.integer' => 'Le champ role_id doit être un nombre entier.',
        'role_id.exists' => 'Le rôle spécifié est introuvable.',

        'name.required' => 'Le champ name est obligatoire.',
        'name.string' => 'Le champ name doit être une chaîne de caractères.',
        'name.max' => 'Le champ name ne doit pas dépasser 255 caractères.',

        'description.required' => 'Le champ description est obligatoire.',
        'description.string' => 'Le champ description doit être une chaîne de caractères.',
        'description.max' => 'Le champ description ne doit pas dépasser 255 caractères.',

        'type.required' => 'Le champ type est obligatoire.',
        'type.string' => 'Le champ type doit être une chaîne de caractères.',
        'type.in' => 'Le type doit être une valeur autorisée.',
    ];
}

}
