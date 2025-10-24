<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class LabTechnicianRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->hasRole(['admin', 'super_admin']);
    }

    /**
     * Règles de validation.
     */
    public function rules(): array
    {
        // Par défaut, nous utilisons les règles pour la création.
        $rules = [
            // Règles pour le modèle User
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'birth_date'    => ['nullable', 'date'],
            'phone'         => ['nullable', 'string', 'max:20'],
            'country'       => ['nullable', 'string', 'max:255'],
            'city'          => ['nullable', 'string', 'max:255'],
            'address'       => ['nullable', 'string', 'max:255'],
            'password'      => ['required', 'string', 'min:8'], // Requis pour la création
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],

            // Règles pour le modèle LabTechnician
            'laboratory_id' => ['required', 'integer', 'exists:laboratorys,id'],
            'speciality'    => ['nullable', 'string', 'max:255'],
            'qualification' => ['nullable', 'string', 'max:255'],
            'status'        => ['required', 'string', Rule::in(['active', 'on_leave', 'resigned', 'suspended'])],
        ];

        // S'il s'agit d'une mise à jour (PUT/PATCH), nous adaptons les règles.
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            // Récupère l'ID du technicien et de l'utilisateur associé
            $labTechnician = $this->route('labtechnician');
            $userId = $labTechnician ? $labTechnician->user_id : null;

            $rules['email'] = [
                'required',
                'string',
                'email',
                'max:255',
                // Indique à la règle unique d'ignorer l'ID de l'utilisateur en cours de modification
                Rule::unique('users', 'email')->ignore($userId),
            ];

            // Le mot de passe n'est pas requis pour la mise à jour
            $rules['password'] = ['nullable', 'string', 'min:8'];
        }

        return $rules;
    }

    protected function prepareForValidation()
    {
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            // Uniquement pour la mise à jour, si le mot de passe est vide, on le supprime
            if ($this->password === null || $this->password === '') {
                $this->offsetUnset('password');
            }
        }
    }
}
