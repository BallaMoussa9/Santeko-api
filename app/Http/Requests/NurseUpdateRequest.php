<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NurseUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Assurez-vous que c'est bien votre logique d'autorisation
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        // 💡 Assurez-vous que la route d'édition passe l'ID de l'infirmier
        $nurse = $this->route('nurse');

        return [
            // --- Informations de l'utilisateur (User Model) ---
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'birth_date' => ['required', 'date', 'before:today'],
            'phone' => ['required', 'string', 'max:15'],
            'country' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],

            // 💡 Règle unique avec une exception pour l'email de l'utilisateur actuel
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($nurse->user_id),
            ],

            // 💡 Règle de mot de passe facultative pour la mise à jour
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],

            // --- Informations spécifiques à l'infirmier (Nurse Model) ---
            'department_id' => ['nullable', 'exists:departments,id'],
        ];
    }

    /**
     * Obtenir les messages d'erreur personnalisés pour les règles de validation.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'Cette adresse email est déjà utilisée par un autre utilisateur.',
            'password.nullable' => 'Le mot de passe peut être laissé vide pour ne pas le changer.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            // ... autres messages
        ];
    }
}
