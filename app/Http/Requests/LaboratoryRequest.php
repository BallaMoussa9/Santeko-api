<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LaboratoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only allow users with the 'admin' or 'super_admin' role to manage laboratories.
        return $this->user() && $this->user()->hasRole(['admin', 'super_admin']);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Note: The labtech_id column on the laboratory table is not a standard pattern.
            // A lab technician belongs to a laboratory, not the other way around.
            // Consider removing this column from your table and form request.
            // If you must keep it, the validation below will work.
            // 'labtech_id' => ['nullable', 'integer', 'exists:labtechnicians,id'],

            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'name' => ['required', 'string', 'max:255'],
            'adress' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('laboratorys')->ignore($this->laboratory),
            ],
            'opening_time' => ['nullable', 'date_format:H:i:s'],
            'closing_time' => ['nullable', 'date_format:H:i:s'],
            'status' => ['required', 'string', Rule::in(['active', 'inactive', 'under_maintenance'])],
        ];
    }
}
