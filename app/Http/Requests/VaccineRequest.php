<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class VaccineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->hasAnyRole(['admin', 'super_admin', 'pharmacist']);
    }

    public function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:255', Rule::unique('vaccines', 'nom')->ignore($this->vaccine)],
            'fabricant' => ['nullable', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', Rule::unique('vaccines', 'code')->ignore($this->vaccine)],
            'type' => ['nullable', 'string', 'max:255'],
            'duree_validite' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
        ];
    }
}
