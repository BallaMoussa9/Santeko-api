<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Models\BloodUnit;

class BloodUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->hasRole(['admin', 'super_admin', 'lab_technician']);
    }

    public function rules(): array
    {
        // Récupérer l'ID de l'unité de sang depuis la route (instance ou ID brut)
        $bloodUnit = $this->route('blood_unit');
        $bloodUnitId = $bloodUnit instanceof BloodUnit ? $bloodUnit->id : $bloodUnit;

        return [
            'blood_group' => ['required', 'string', 'max:5'],
            'rh_factor' => ['required', 'string', 'max:10'],
            'unit_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('blood_units', 'unit_number')->ignore($bloodUnitId),
            ],
            'collection_date' => ['required', 'date'],
            'expiration_date' => ['required', 'date', 'after:collection_date'],
            'status' => ['required', Rule::in(['available', 'used', 'expired', 'quarantined'])],
            'location' => ['nullable', 'string', 'max:255'],
            'donor_id' => ['required', 'integer', 'exists:donors,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'blood_group.required' => 'Le groupe sanguin est obligatoire.',
            'blood_group.string' => 'Le groupe sanguin doit être une chaîne de caractères.',
            'blood_group.max' => 'Le groupe sanguin ne doit pas dépasser 5 caractères.',

            'rh_factor.required' => 'Le facteur Rhésus est obligatoire.',
            'rh_factor.string' => 'Le facteur Rhésus doit être une chaîne de caractères.',
            'rh_factor.max' => 'Le facteur Rhésus ne doit pas dépasser 10 caractères.',

            'unit_number.required' => 'Le numéro de l’unité est obligatoire.',
            'unit_number.string' => 'Le numéro de l’unité doit être une chaîne de caractères.',
            'unit_number.max' => 'Le numéro de l’unité ne doit pas dépasser 255 caractères.',
            'unit_number.unique' => 'Le numéro de l’unité doit être unique.',

            'collection_date.required' => 'La date de collecte est obligatoire.',
            'collection_date.date' => 'La date de collecte doit être une date valide.',

            'expiration_date.required' => 'La date d’expiration est obligatoire.',
            'expiration_date.date' => 'La date d’expiration doit être une date valide.',
            'expiration_date.after' => 'La date d’expiration doit être postérieure à la date de collecte.',

            'status.required' => 'Le statut est obligatoire.',
            'status.in' => 'Le statut doit être l’un des suivants : available, used, expired, quarantined.',

            'location.string' => 'L’emplacement doit être une chaîne de caractères.',
            'location.max' => 'L’emplacement ne doit pas dépasser 255 caractères.',

            'donor_id.required' => 'L’identifiant du donneur est obligatoire.',
            'donor_id.integer' => 'L’identifiant du donneur doit être un entier.',
            'donor_id.exists' => 'Le donneur spécifié n’existe pas.',
        ];
    }
}
