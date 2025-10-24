<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VaccineUpdateRequest extends FormRequest
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
        'nom' => ['required', 'string', 'max:255'],
        'fabricant' => ['required', 'string'],
        'code' => ['required', 'string'],
        'type' => ['required', 'string'],
        'duree_validate' => ['required', 'integer'],
        'description' => ['required', 'string'], // corrigé ici
    ];
}
public function messages(): array
{
    return [
        'nom.required' => 'Le champ nom est obligatoire.',
        'nom.string' => 'Le champ nom doit être une chaîne de caractères.',
        'nom.max' => 'Le champ nom ne doit pas dépasser 255 caractères.',

        'fabricant.required' => 'Le champ fabricant est obligatoire.',
        'fabricant.string' => 'Le champ fabricant doit être une chaîne de caractères.',

        'code.required' => 'Le champ code est obligatoire.',
        'code.string' => 'Le champ code doit être une chaîne de caractères.',

        'type.required' => 'Le champ type est obligatoire.',
        'type.string' => 'Le champ type doit être une chaîne de caractères.',

        'duree_validate.required' => 'La durée de validité est obligatoire.',
        'duree_validate.integer' => 'Le champ duree_validate doit être un entier.',

        'description.required' => 'Le champ description est obligatoire.',
        'description.string' => 'Le champ description doit être une chaîne de caractères.',
    ];
}
}
