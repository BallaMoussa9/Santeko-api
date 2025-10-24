<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PrescriptionUpdateRequest extends FormRequest
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
        'date_prescription' => ['required', 'date', 'after_or_equal:today'],
        'status' => ['required', 'string', 'max:255'],
        'notes' => ['required', 'string', 'max:255'], // corrigé ici
    ];
}
public function messages(): array
{
    return [
        'date_prescription.required' => 'La date de prescription est obligatoire.',
        'date_prescription.date' => 'La date de prescription doit être une date valide.',
        'date_prescription.after_or_equal' => 'La date doit être aujourd’hui ou ultérieure.',

        'status.required' => 'Le champ status est obligatoire.',
        'status.string' => 'Le champ status doit être une chaîne de caractères.',
        'status.max' => 'Le champ status ne doit pas dépasser 255 caractères.',

        'notes.required' => 'Le champ notes est obligatoire.',
        'notes.string' => 'Le champ notes doit être une chaîne de caractères.',
        'notes.max' => 'Le champ notes ne doit pas dépasser 255 caractères.',
    ];
}

}
