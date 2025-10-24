<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class VaccinationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->hasAnyRole(['admin', 'super_admin', 'nurse']);
    }

    public function rules(): array
    {
        return [
            'vaccine_id' => ['required', 'integer', 'exists:vaccines,id'],
            'medicalrecord_id' => ['required', 'integer', 'exists:medical_records,id'],
            'nurse_id' => ['required', 'integer', 'exists:nurses,id'],
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'total_required_dose' => ['nullable', 'integer', 'min:0'],
            'administration_date' => ['required', 'date'],
            'status' => ['required', 'string', Rule::in(['completed', 'missed', 'scheduled'])],
            'localiter' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
