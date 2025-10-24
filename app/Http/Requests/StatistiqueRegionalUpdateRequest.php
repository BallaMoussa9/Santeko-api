<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StatistiqueRegionalUpdateRequest extends FormRequest
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
        'region_id' => ['required', 'integer', 'exists:regions,id'],
        'user_id' => ['required', 'integer', 'exists:users,id'],
        'hospital_id' => ['required', 'integer', 'exists:hospitals,id'],
        'department_id' => ['required', 'integer', 'exists:department,id'], // à confirmer si le nom est bien singulier
        'region' => ['required', 'string', 'max:200'],
        'period_start' => ['required', 'date'],
        'period_end' => ['required', 'date'],
        'total_consultations' => ['required', 'integer'],
        'total_teleconsutations' => ['required', 'integer'],
        'total_analyses' => ['required', 'integer'],
        'taux_prescriptions' => ['required', 'integer'],
        'total_vaccinations' => ['required', 'integer'],
        'taux_rdv_annules' => ['required', 'numeric'], // corrigé
        'status' => ['required', 'string'],
    ];
}
public function messages(): array
{
    return [
        'region_id.required' => 'Le champ region_id est obligatoire.',
        'region_id.integer' => 'Le champ region_id doit être un entier.',
        'region_id.exists' => 'La région spécifiée est introuvable.',

        'user_id.required' => 'Le champ user_id est obligatoire.',
        'user_id.integer' => 'Le champ user_id doit être un entier.',
        'user_id.exists' => 'L’utilisateur spécifié est introuvable.',

        'hospital_id.required' => 'Le champ hospital_id est obligatoire.',
        'hospital_id.integer' => 'Le champ hospital_id doit être un entier.',
        'hospital_id.exists' => 'L’hôpital spécifié est introuvable.',

        'department_id.required' => 'Le champ department_id est obligatoire.',
        'department_id.integer' => 'Le champ department_id doit être un entier.',
        'department_id.exists' => 'Le département spécifié est introuvable.',

        'region.required' => 'Le champ region est obligatoire.',
        'region.string' => 'Le champ region doit être une chaîne de caractères.',
        'region.max' => 'Le champ region ne doit pas dépasser 200 caractères.',

        'period_start.required' => 'La date de début est obligatoire.',
        'period_start.date' => 'La date de début doit être une date valide.',

        'period_end.required' => 'La date de fin est obligatoire.',
        'period_end.date' => 'La date de fin doit être une date valide.',

        'total_consultations.required' => 'Le total des consultations est obligatoire.',
        'total_consultations.integer' => 'Le total des consultations doit être un nombre entier.',

        'total_teleconsutations.required' => 'Le total des téléconsultations est obligatoire.',
        'total_teleconsutations.integer' => 'Le total des téléconsultations doit être un nombre entier.',

        'total_analyses.required' => 'Le total des analyses est obligatoire.',
        'total_analyses.integer' => 'Le total des analyses doit être un nombre entier.',

        'taux_prescriptions.required' => 'Le taux des prescriptions est obligatoire.',
        'taux_prescriptions.integer' => 'Le taux des prescriptions doit être un nombre entier.',

        'total_vaccinations.required' => 'Le total des vaccinations est obligatoire.',
        'total_vaccinations.integer' => 'Le total des vaccinations doit être un nombre entier.',

        'taux_rdv_annules.required' => 'Le taux des rendez-vous annulés est obligatoire.',
        'taux_rdv_annules.numeric' => 'Le taux des rendez-vous annulés doit être une valeur numérique.',

        'status.required' => 'Le champ status est obligatoire.',
        'status.string' => 'Le champ status doit être une chaîne de caractères.',
    ];
}

}
