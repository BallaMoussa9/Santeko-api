<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePrescriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Similaire à UpdateDmeRequest
        return true;
    }

    public function rules(): array
    {
        return [
            'date_prescribed' => ['required', 'date_format:Y-m-d'],
            'lines' => ['required', 'array', 'min:1'], // L'ordonnance doit avoir au moins une ligne
            'lines.*.medication_name' => ['required', 'string', 'max:255'],
            'lines.*.dosage' => ['required', 'string', 'max:255'],
            'lines.*.frequency' => ['required', 'string', 'max:255'],
        ];
    }
}
