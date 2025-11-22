<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MedicalReportRequest extends FormRequest
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
             'report_type' => 'required| string| max:255',
            'title' => 'required| string| max:255',
            'content' => 'required| string',
        ];
    }
    public function messages(){
        return[
        'report_type.required' => 'Le type rapport est obligatoire.',
        'report_type.string'   => 'Le type rapport doit être une chaîne de caractères.',
        'report_type.max'      => 'Le type rapport ne peut pas dépasser 255 caractères.',

        'title.required' => 'Le titre est obligatoire.',
        'title.string'   => 'Le titre doit être une chaîne de caractères.',
        'title.max'      => 'Le titre ne peut pas dépasser 255 caractères.',

        'content.required' => 'Le contenu est obligatoire.',
        'content.string'   => 'Le contenu doit être une chaîne de caractères.',
        'content.max'      => 'L\e contenune peut pas dépasser 255 caractères.',
        ];
    }
}
