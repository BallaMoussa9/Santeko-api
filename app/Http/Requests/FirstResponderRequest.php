<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FirstResponderRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Obtenir les règles de validation qui s'appliquent à la requête.
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
        'first_name' => ['required', 'string', 'max:255'],
        'last_name' => ['required', 'string', 'max:255'],
        'birth_date' => ['nullable', 'date'],
        'phone' => ['required', 'string', 'max:20'],
        'city' => ['nullable', 'string', 'max:255'],
        'address' => ['nullable', 'string', 'max:255'],
        'email' => [
            'required',
            'string',
            'email',
            'max:255',
            Rule::unique('users', 'email')->ignore($userIdForUniqueRule),
        ],
        'password' => ['nullable', 'string', 'min:8'],
        'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],

        // Champs table first_responders
        'speciality' => ['required', 'string', 'max:255'],
        'status' => ['required', 'string', Rule::in(['available', 'on_duty', 'off_duty', 'suspended'])],
        'location' => ['nullable', 'string', 'max:255'],
    ];
}

}
