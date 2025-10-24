<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class laboratoryUpdateRequest extends FormRequest
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
        'labtech_id' => ['required', 'integer', 'exists:labtechnicians,id'],
        'department_id' => ['required', 'integer', 'exists:departments,id'],
        'name' => ['required', 'string', 'max:255'],
        'adress' => ['required', 'string', 'max:255'],
        'phone' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'max:255'],
        'opening_time' => ['required', 'date_format:H:i'],   // corrigé
        'closing_time' => ['required', 'date_format:H:i'],   // corrigé
        'status' => ['required', 'string', 'max:255']
    ];
}
public function messages(): array
{
    return [
        'labtech_id.required' => 'Le champ labtech_id est obligatoire.',
        'labtech_id.integer' => 'Le champ labtech_id doit être un entier.',
        'labtech_id.exists' => 'Le technicien spécifié est introuvable.',

        'department_id.required' => 'Le champ department_id est obligatoire.',
        'department_id.integer' => 'Le champ department_id doit être un entier.',
        'department_id.exists' => 'Le département spécifié est introuvable.',

        'name.required' => 'Le champ name est obligatoire.',
        'name.string' => 'Le champ name doit être une chaîne de caractères.',
        'name.max' => 'Le champ name ne doit pas dépasser 255 caractères.',

        'adress.required' => 'Le champ adress est obligatoire.',
        'adress.string' => 'Le champ adress doit être une chaîne de caractères.',
        'adress.max' => 'Le champ adress ne doit pas dépasser 255 caractères.',

        'phone.required' => 'Le champ phone est obligatoire.',
        'phone.string' => 'Le champ phone doit être une chaîne de caractères.',
        'phone.max' => 'Le champ phone ne doit pas dépasser 255 caractères.',

        'email.required' => 'Le champ email est obligatoire.',
        'email.string' => 'Le champ email doit être une chaîne de caractères.',
        'email.max' => 'Le champ email ne doit pas dépasser 255 caractères.',

        'opening_time.required' => 'L’heure d’ouverture est obligatoire.',
        'opening_time.date_format' => 'L’heure d’ouverture doit être au format HH:mm.',

        'closing_time.required' => 'L’heure de fermeture est obligatoire.',
        'closing_time.date_format' => 'L’heure de fermeture doit être au format HH:mm.',

        'status.required' => 'Le champ status est obligatoire.',
        'status.string' => 'Le champ status doit être une chaîne de caractères.',
        'status.max' => 'Le champ status ne doit pas dépasser 255 caractères.',
    ];
}

}
