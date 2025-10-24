<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HospitalUpdateRequest extends FormRequest
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
        'adresse' => ['required', 'string', 'max:255'],
        'phone' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'max:255'],
        'ville' => ['required', 'string', 'max:255'],
        'type' => ['required', 'string', 'max:255'], // tu peux ajouter Rule::in([...]) si besoin
    ];
}
public function messages(): array
{
    return [
        'nom.required' => 'Le champ nom est obligatoire.',
        'nom.string' => 'Le champ nom doit être une chaîne de caractères.',
        'nom.max' => 'Le champ nom ne doit pas dépasser 255 caractères.',

        'adresse.required' => 'Le champ adresse est obligatoire.',
        'adresse.string' => 'Le champ adresse doit être une chaîne de caractères.',
        'adresse.max' => 'Le champ adresse ne doit pas dépasser 255 caractères.',

        'phone.required' => 'Le champ phone est obligatoire.',
        'phone.string' => 'Le champ phone doit être une chaîne de caractères.',
        'phone.max' => 'Le champ phone ne doit pas dépasser 255 caractères.',

        'email.required' => 'Le champ email est obligatoire.',
        'email.string' => 'Le champ email doit être une chaîne de caractères.',
        'email.max' => 'Le champ email ne doit pas dépasser 255 caractères.',

        'ville.required' => 'Le champ ville est obligatoire.',
        'ville.string' => 'Le champ ville doit être une chaîne de caractères.',
        'ville.max' => 'Le champ ville ne doit pas dépasser 255 caractères.',

        'type.required' => 'Le champ type est obligatoire.',
        'type.string' => 'Le champ type doit être une chaîne de caractères.',
        'type.max' => 'Le champ type ne doit pas dépasser 255 caractères.',
    ];
}
}
