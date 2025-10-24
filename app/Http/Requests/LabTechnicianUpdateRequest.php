<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Models\LabTechnician; // Assurez-vous d'importer le modèle LabTechnician

class LabTechnicianUpdateRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé.
     */
    public function authorize(): bool
    {
        // Seuls les admins et super_admins peuvent mettre à jour
        return Auth::check() && Auth::user()->hasRole(['admin', 'super_admin']);
    }

    /**
     * Règles de validation pour la mise à jour.
     */
    public function rules(): array
    {
        // Récupérer l'ID du technicien de laboratoire à partir de la route.
        // C'est l'ID brut passé dans l'URL (ex: '1').
        $labTechnicianRouteId = $this->route('labtechnician');

        $userIdToIgnore = null;

        // Tenter de trouver l'objet LabTechnician correspondant à cet ID.
        // C'est nécessaire car FormRequest ne fait pas la résolution de modèle par défaut dans rules().
        if ($labTechnicianRouteId) {
            $labTechnician = LabTechnician::find($labTechnicianRouteId);
            if ($labTechnician) {
                $userIdToIgnore = $labTechnician->user_id;
            }
        }

        return [
            // Règles pour le modèle User
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name'  => ['nullable', 'string', 'max:255'],
            'email'      => [
                'nullable', // L'email n'est pas obligatoire pour une mise à jour (si on ne veut pas le changer)
                'string',
                'email',
                'max:255',
                // Règle d'unicité qui ignore l'ID de l'utilisateur associé au technicien en cours de mise à jour.
                // Cela permet à l'utilisateur de conserver son propre email sans erreur d'unicité.
                Rule::unique('users', 'email')->ignore($userIdToIgnore),
            ],
            'birth_date'    => ['nullable', 'date'],
            'phone'         => ['nullable', 'string', 'max:20'],
            'country'       => ['nullable', 'string', 'max:255'],
            'city'          => ['nullable', 'string', 'max:255'],
            'address'       => ['nullable', 'string', 'max:255'],
            'password'      => ['nullable', 'string', 'min:8'], // Le mot de passe est optionnel pour la mise à jour
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],

            // Règles pour le modèle LabTechnician
            'laboratory_id' => ['nullable', 'integer', 'exists:laboratorys,id'],
            'speciality'    => ['nullable', 'string', 'max:255'],
            'qualification' => ['nullable', 'string', 'max:255'],
            'status'        => ['nullable', 'string', Rule::in(['active', 'on_leave', 'resigned', 'suspended'])],
        ];
    }

    /**
     * Préparer les données pour la validation.
     * Cette méthode est exécutée avant la validation des règles.
     */
    protected function prepareForValidation()
    {
        // Si le champ 'password' est présent dans la requête mais vide,
        // nous le retirons des données validées pour ne pas écraser l'ancien mot de passe
        // par une valeur vide.
        if ($this->has('password') && (is_null($this->password) || $this->password === '')) {
            $this->offsetUnset('password');
        }
    }
}
