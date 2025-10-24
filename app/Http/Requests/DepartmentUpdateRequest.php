<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DepartmentUpdateRequest extends FormRequest
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
            'name'=>['required','string','max:255'],
            'description'=>['required','string','max:255'],
            'status'=>['required','string','max:255'],
            'position'=>['required','string','max:255']
        ];
    }
    public function messages(): array
{
    return [
        'admin_id.required' => 'Le champ admin_id est obligatoire.',
        'admin_id.integer' => 'Le champ admin_id doit être un entier.',
        'admin_id.exists' => 'L’admin spécifié est introuvable dans la base de données.',

        'name.required' => 'Le champ name est obligatoire.',
        'name.string' => 'Le champ name doit être une chaîne de caractères.',
        'name.max' => 'Le champ name ne doit pas dépasser 255 caractères.',

        'description.required' => 'Le champ description est obligatoire.',
        'description.string' => 'Le champ description doit être une chaîne de caractères.',
        'description.max' => 'Le champ description ne doit pas dépasser 255 caractères.',

        'status.required' => 'Le champ status est obligatoire.',
        'status.string' => 'Le champ status doit être une chaîne de caractères.',
        'status.max' => 'Le champ status ne doit pas dépasser 255 caractères.',

        'position.required' => 'Le champ position est obligatoire.',
        'position.string' => 'Le champ position doit être une chaîne de caractères.',
        'position.max' => 'Le champ position ne doit pas dépasser 255 caractères.',
    ];
}
}
