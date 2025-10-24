<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MortaliteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // By default, this returns false for security.
        // For now, we'll set it to true to allow the request to proceed.
        // In a real application, you'd add logic here to check if the user
        // has permission to create or update a death record.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        // Define the base rules for both creation and updates
        $rules = [
            // patient_id must be present and exist in the 'patients' table.
            'patient_id' => 'required|exists:patients,id',
            // doctor_id is optional but if provided, must exist in the 'doctors' table.
            'doctor_id' => 'nullable|exists:doctors,id',
            'department_id' => 'nullable|exists:departments,id',
            'nurse_id' => 'nullable|exists:nurses,id',
            // date_deces is required and must be a valid date format.
            'date_deces' => 'required|date',
            'lieu_deces' => 'nullable|string|max:255',
            'cause_deces' => 'required|string|max:255',
            'circonstances_deces' => 'nullable|string',
            // statut is required and must be one of the specified enum values.
            'statut' => ['required', Rule::in(['recorded', 'verified', 'archived'])],
            // numero_acte_deces must be a unique string in the 'deaths' table.
            'numero_acte_deces' => 'nullable|string|max:255|unique:deaths,numero_acte_deces',
        ];

        // For update requests (PUT or PATCH), we need a special rule for 'numero_acte_deces'
        // to ignore the current record's ID to avoid unique validation errors.
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['numero_acte_deces'] = [
                'nullable',
                'string',
                'max:255',
                Rule::unique('deaths', 'numero_acte_deces')->ignore($this->route('mortalite'))
            ];
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'patient_id.required' => 'L\'identifiant du patient est obligatoire.',
            'patient_id.exists' => 'Le patient n\'existe pas.',
            'date_deces.required' => 'La date de décès est obligatoire.',
            'cause_deces.required' => 'La cause du décès est obligatoire.',
            'statut.required' => 'Le statut est obligatoire.',
            'numero_acte_deces.unique' => 'Le numéro d\'acte de décès doit être unique.',
        ];
    }
}
