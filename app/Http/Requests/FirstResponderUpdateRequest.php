<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FirstResponderUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        // 🔍 On récupère l'ID de l'utilisateur lié, peu importe le paramètre de la route
        $userIdForUniqueRule = null;

        if ($this->route('user')) {
            $userIdForUniqueRule = $this->route('user')->id;
        } elseif ($this->route('emergency')) {
            $userIdForUniqueRule = $this->route('emergency')->user_id;
        }

        return [
            // Champs table users
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'phone' => ['nullable', 'string', 'max:20'],
            'city' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'email' => [
                'nullable', // L'email ne doit pas être requis si non modifié
                'string',
                'email',
                'max:255',
                // La règle unique ignore l'utilisateur actuel
                Rule::unique('users', 'email')->ignore($userIdForUniqueRule),
            ],
            // Le mot de passe ne doit être validé que s'il est fourni
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],

            // Champs table first_responders
            'speciality' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', Rule::in(['available', 'on_duty', 'off_duty', 'suspended'])],
            'location' => ['nullable', 'string', 'max:255'],
        ];
    }
}
