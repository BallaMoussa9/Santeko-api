<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SystemSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * La logique d'autorisation sera gérée dans le contrôleur ou via des Gates/Policies.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        // Détecter si c'est une requête de mise à jour (PATCH/PUT) ou de création (POST)
        $isUpdate = $this->method() === 'PUT' || $this->method() === 'PATCH';

        $rules = [
            'key' => [
                'required',
                'string',
                'max:255',
                // La clé doit être unique, sauf lors de la mise à jour de la même entrée
                // Assurez-vous que 'system_setting' correspond bien au paramètre de route
                Rule::unique('system_settings', 'key')->ignore($this->route('system_setting')),
            ],
            'value' => 'nullable|string', // La valeur peut être une chaîne, null, etc.
            'description' => 'nullable|string|max:500', // Ajout d'une limite de longueur
            'type' => ['required', 'string', Rule::in(['string', 'integer', 'boolean', 'json', 'array'])], // Ajout 'array'
            'category' => 'nullable|string|max:255', // Nouveau champ
            'status' => ['required', Rule::in(['active', 'inactive', 'maintenance'])], // Nouveau champ, valeurs d'enum
            'is_editable' => 'boolean', // Nouveau champ
            'is_visible' => 'boolean', // Nouveau champ
            'is_required' => 'boolean', // Nouveau champ
        ];

        // Pour la mise à jour, 'key' ne devrait pas être modifiable si c'est un identifiant unique fort.
        // Ou au moins, il doit être géré avec précaution. Ici, on permet sa modification mais en respectant l'unicité.
        // Si 'key' ne doit JAMAIS être modifiable après création, retirez-le des règles 'update'.
        // Ex: if ($isUpdate) { unset($rules['key']); }

        return $rules;
    }

    /**
     * Prepare the data for validation.
     * Pour s'assurer que les booléens sont correctement castés.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_editable' => filter_var($this->is_editable, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            'is_visible' => filter_var($this->is_visible, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            'is_required' => filter_var($this->is_required, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
        ]);
    }
}
