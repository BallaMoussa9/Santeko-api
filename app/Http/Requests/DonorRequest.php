<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DonorRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     */
    public function authorize(): bool
    {
        // Seuls les administrateurs et les techniciens de laboratoire peuvent enregistrer des donneurs.
        return Auth::check() && Auth::user()->hasRole(['admin', 'super_admin', 'lab_technician']);
    }

    /**
     * Règles de validation qui s'appliquent à la requête.
     */
    public function rules(): array
    {
        return [
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'name' => ['required', 'string', 'max:255'],
            'blood_group' => ['required', 'string', 'max:5', 'in:A,B,AB,O'],
            'rh_factor' => ['required', 'string', 'max:10', 'in:Positif,Négatif'],
            'date_of_birth' => ['required', 'date'],
            'phone' => ['nullable', 'string', 'max:255'],
        ];
    }
}
