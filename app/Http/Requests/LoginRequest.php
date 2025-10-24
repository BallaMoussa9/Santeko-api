<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ];
    }
    public function messages():array{
        return [
            'email.required' => 'Les identifiants sont invalides.',
            'email.email' => 'Les identifiants sont invalides.',
            'email.max' => 'Les identifiants sont invalides.',
            'email.unique' => 'Les identifiants sont invalides.',

            'password.required' => 'Les identifiants sont invalides.',
            'password.string' => 'Les identifiants sont invalides.',
            'password.min' => 'Les identifiants sont invalides.',
            'password.confirmed' => 'Les identifiants sont invalides.',
        ];
    }
}
