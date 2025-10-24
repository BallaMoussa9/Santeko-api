<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
public function rules(): array
{
    return [
        'role_id' => ['required', 'integer', 'exists:roles,id'],
        'department_id' => 'nullable|exists:departments,id',

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


public function withValidator($validator)
{
    $validator->after(function ($validator) {
        $forbiddenRoleIds = [1]; // exemple : ID du rôle "super-admin"

        if ($this->has('roles')) {
            foreach ($this->roles as $roleId) {
                if (in_array($roleId, $forbiddenRoleIds)) {
                    $validator->errors()->add('roles', 'Vous ne pouvez pas attribuer ce rôle.');
                }
            }
        }
    });
}


}
