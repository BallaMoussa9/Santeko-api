<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DepartmentRequest extends FormRequest
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
     */
    public function rules(): array
    {
        // Validation basée sur les colonnes name, description, status, position, et user_id (via responsible_user_id)
        return [
            // Colonne 'name' (Unique et requis)
            'name' => 'required|string|max:255|unique:departments,name,' . $this->department?->id,
            
            // Colonne 'description'
            'description' => 'nullable|string',
            
            // Colonne 'status' (Enum)
            'status' => ['required', Rule::in(['active', 'inactive'])],
            
            // Colonne 'position'
            'position' => 'nullable|string|max:255',
            
            // Colonne 'user_id' (gérée via le nom du champ frontend)
            'responsible_user_id' => 'nullable|integer|exists:users,id',
            
            // Si vous voulez une validation pour admin_id (bien que souvent injectée par le contrôleur)
            'admin_id' => 'nullable|integer|exists:users,id',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Le nom du service est requis.',
            'name.string' => 'Le nom du service doit être une chaîne de caractères.',
            'name.max' => 'Le nom du service ne doit pas dépasser 255 caractères.',
            'name.unique' => 'Un service avec ce nom existe déjà.',
            
            'description.string' => 'La description doit être une chaîne de caractères.',
            
            'status.required' => 'Le statut est requis.',
            'status.in' => 'Le statut doit être "active" ou "inactive".',
            
            'position.string' => 'La position doit être une chaîne de caractères.',
            'position.max' => 'La position ne doit pas dépasser 255 caractères.',
            
            'responsible_user_id.integer' => 'L\'ID du responsable doit être un entier valide.',
            'responsible_user_id.exists' => 'Le responsable sélectionné n\'existe pas dans la base de données des utilisateurs.',
        ];
    }
}