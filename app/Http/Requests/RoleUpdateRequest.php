<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
{
    return [
        'role_id' => ['required', 'integer', 'exists:roles,id'],
    ];
}

public function messages(): array
{
    return [
        'role_id.required' => 'Le rôle est requis.',
        'role_id.integer' => 'Le rôle doit être un identifiant valide.',
        'role_id.exists' => 'Ce rôle n\'existe pas.',
    ];
}
}
