<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NaissanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'hospital_id' => 'required|exists:hospitals,id',
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'nullable|exists:doctors,id',
            'department_id' => 'nullable|exists:departments,id',
            'nurse_id' => 'nullable|exists:nurses,id',
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'sexe' => ['required', Rule::in(['M', 'F', 'Autre'])],
            'date_naissance' => 'required|date',
            'lieu_naissance' => 'nullable|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'poids' => 'nullable|numeric|min:0',
            'taille' => 'nullable|numeric|min:0',
            'heure_naissance' => 'required|date_format:Y-m-d H:i:s',
            'statut' => ['nullable', Rule::in(['active', 'archived'])],
            'numero_acte_naissance' => 'nullable|string|max:255|unique:births,numero_acte_naissance',
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['numero_acte_naissance'] = [
                'nullable',
                'string',
                'max:255',
                // Important: ignorer l'ID de la naissance actuelle pour la règle unique
                Rule::unique('births', 'numero_acte_naissance')->ignore($this->route('naissance'))
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'hospital_id.required' => 'L\'identifiant de l\'hôpital est obligatoire.',
            'patient_id.required' => 'L\'identifiant du patient (mère) est obligatoire.',
            'firstname.required' => 'Le prénom est obligatoire.',
            'lastname.required' => 'Le nom de famille est obligatoire.',
            'sexe.required' => 'Le sexe est obligatoire.',
            'date_naissance.required' => 'La date de naissance est obligatoire.',
            'heure_naissance.required' => 'L\'heure de naissance est obligatoire.',
            'heure_naissance.date_format' => 'L\'heure de naissance doit être au format YYYY-MM-DD HH:MM:SS.',
            'numero_acte_naissance.unique' => 'Le numéro d\'acte de naissance doit être unique.',
        ];
    }
}
