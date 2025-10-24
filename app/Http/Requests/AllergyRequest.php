<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class AllergyRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->hasRole(['admin', 'super_admin', 'doctor', 'nurse', 'receptionist']);
    }

    /**
     * Règles de validation qui s'appliquent à la requête.
     */
    public function rules(): array
    {
        return [
            'medical_record_id' => ['required', 'integer', 'exists:medicalrecords,id'],
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'substance' => ['required', 'string', 'max:255'],
            'reaction_decscription' => ['nullable', 'string', 'max:255'],
            'serverity' => ['nullable', 'string', 'max:255'],
            'recorded_by' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', Rule::in(['active', 'inactive', 'resolved'])],
            'notes' => ['nullable', 'string'],
        ];
    }
}
