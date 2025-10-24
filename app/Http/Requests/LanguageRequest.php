<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class LanguageRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     */
    public function authorize(): bool
    {
        // Seuls les administrateurs et super-admins peuvent gérer les langues
        return Auth::check() && Auth::user()->hasRole(['admin', 'super_admin']);
    }

    /**
     * Règles de validation.
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('languages')->ignore($this->language),
            ],
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('languages')->ignore($this->language),
            ],
            'locale' => ['nullable', 'string', 'max:255'],
            'direction' => ['nullable', 'string', Rule::in(['ltr', 'rtl'])],
            'is_active' => ['nullable', 'boolean'],
            'is_default' => ['nullable', 'boolean'],
            'native_name' => ['nullable', 'string', 'max:255'],
        ];
    }
}
