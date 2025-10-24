<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateActivitiesReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // L'autorisation réelle est faite dans le contrôleur
    }

    public function rules(): array
    {
        return [
            'report_date' => ['required', 'date'],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'patient_id' => ['nullable', 'exists:patients,id'],
        ];
    }
}
