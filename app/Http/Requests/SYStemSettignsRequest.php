<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class SYStemSettignsRequest extends FormRequest
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
        'admin_id' => ['required', 'integer', 'exists:admins,id'], // corrigé
        'key' => ['required', 'string'],
        'value' => ['required', 'string'],
        'description' => ['required', 'string'],
        'type' => ['required', 'string'],
        'category' => ['required', 'string'],
        'status' => ['required', 'string'],
        'is_editable' => ['required', 'boolean'],       // corrigé
        'is_visible' => ['required', 'boolean'],        // corrigé
        'is_requered' => ['required', 'boolean'],       // vérifie si c’est bien le nom voulu
    ];
}
public function messages(): array
{
    return [
        'admin_id.required' => 'Le champ admin_id est obligatoire.',
        'admin_id.integer' => 'Le champ admin_id doit être un nombre entier.',
        'admin_id.exists' => 'L’administrateur spécifié est introuvable.',

        'key.required' => 'Le champ key est obligatoire.',
        'key.string' => 'Le champ key doit être une chaîne de caractères.',

        'value.required' => 'Le champ value est obligatoire.',
        'value.string' => 'Le champ value doit être une chaîne de caractères.',

        'description.required' => 'Le champ description est obligatoire.',
        'description.string' => 'Le champ description doit être une chaîne de caractères.',

        'type.required' => 'Le champ type est obligatoire.',
        'type.string' => 'Le champ type doit être une chaîne de caractères.',

        'category.required' => 'Le champ category est obligatoire.',
        'category.string' => 'Le champ category doit être une chaîne de caractères.',

        'status.required' => 'Le champ status est obligatoire.',
        'status.string' => 'Le champ status doit être une chaîne de caractères.',

        'is_editable.required' => 'Le champ is_editable est obligatoire.',
        'is_editable.boolean' => 'Le champ is_editable doit être vrai ou faux.',

        'is_visible.required' => 'Le champ is_visible est obligatoire.',
        'is_visible.boolean' => 'Le champ is_visible doit être vrai ou faux.',

        'is_requered.required' => 'Le champ is_requered est obligatoire.',
        'is_requered.boolean' => 'Le champ is_requered doit être vrai ou faux.',
    ];
}

}
