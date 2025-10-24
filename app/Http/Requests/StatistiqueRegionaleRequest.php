<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class StatistiqueRegionaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->hasRole(['admin', 'super_admin']);
    }

    public function rules(): array
    {
        return [
            'region_id' => ['required', 'integer', 'exists:regions,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'hopital_id' => ['required', 'integer', 'exists:hospitals,id'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'region' => ['required', 'string', 'max:255'],
            'period_start' => ['required', 'date'],
            'period_end' => ['required', 'date', 'after_or_equal:period_start'],
            'total_consultations' => ['nullable', 'integer'],
            'total_teleconsultations' => ['nullable', 'integer'],
            'total_analyses' => ['nullable', 'integer'],
            'taux_prescriptions' => ['nullable', 'numeric'],
            'total_vaccinations' => ['nullable', 'integer'],
            'taux_paiement' => ['nullable', 'numeric'],
            'taux_rdv_annules' => ['nullable', 'numeric'],
            'status' => ['required', Rule::in(['published', 'draft', 'archived'])],
        ];
    }
}
