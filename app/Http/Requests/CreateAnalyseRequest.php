<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class CreateAnalyseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only allow authenticated users with the 'doctor' role to proceed.
        // return Auth::check() && Auth::user()->hasRole('doctor');
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
{
    return [
        'patient_id' => ['required', 'integer', 'exists:patients,id'],
        // Correct the table name here
        'laboratory_id' => ['required', 'integer', 'exists:laboratorys,id'],
        'consultation_id' => ['nullable', 'integer', 'exists:consultations,id'],

        'lab_technician_id' => ['nullable', 'integer', 'exists:labtechnicians,id'],

        'name' => ['required', 'string', 'max:255'],
        'type' => ['nullable', 'string', 'max:255'],
        'status' => ['required', Rule::in(['pending', 'in_progress', 'completed', 'cancelled'])],

        'requested_at' => ['nullable', 'date'],
        'completed_at' => ['nullable', 'date'],
    ];
}

    /**
     * Get the validation messages that apply to the request.
     */
    public function messages(): array
    {
        return [
            'patient_id.required' => 'Le patient est obligatoire pour la demande d\'analyse.',
            'patient_id.exists' => 'Le patient spécifié est introuvable.',
            'laboratory_id.required' => 'Le laboratoire de destination est obligatoire.',
            'laboratory_id.exists' => 'Le laboratoire spécifié est introuvable.',
            'name.required' => 'Le nom de l\'analyse est obligatoire.',
            'status.required' => 'Le statut de l\'analyse est obligatoire.',
            'status.in' => 'Le statut doit être "pending", "in_progress", "completed" ou "cancelled".',
        ];
    }
}
