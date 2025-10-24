<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class HospitalRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     */
    public function authorize(): bool
    {
        // Seuls les administrateurs ou super-admins peuvent gérer les hôpitaux.
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();
        return $user->hasRole('admin') || $user->hasRole('super_admin');
    }

    /**
     * Règles de validation pour les champs de la requête.
     */
    public function rules(): array
    {
        $hospitalId = $this->route('hospital') ? $this->route('hospital')->id : null;

        return [
            'nom' => ['required', 'string', 'max:255'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('hospitals', 'email')->ignore($hospitalId)],
            'ville' => ['nullable', 'string', 'max:255'],
            'type' => ['required', Rule::in(['public', 'private'])],
        ];
    }

    /**
     * Messages d'erreur personnalisés.
     */
    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom de l\'hôpital est obligatoire.',
            'nom.string' => 'Le nom doit être une chaîne de caractères.',
            'nom.max' => 'Le nom ne doit pas dépasser 255 caractères.',
            'type.required' => 'Le type d\'hôpital est obligatoire.',
            'type.in' => 'Le type d\'hôpital doit être "public" ou "private".',
            'email.email' => 'L\'adresse email doit être valide.',
            'email.unique' => 'Cette adresse email est déjà utilisée par un autre hôpital.',
        ];
    }
}
