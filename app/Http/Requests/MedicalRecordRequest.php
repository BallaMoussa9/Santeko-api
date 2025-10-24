<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MedicalRecordRequest extends FormRequest
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
            'status'         => 'required| string| max:15',
            'group_sanguin'  => 'required| string| max:255',
        ];
    }
    public function messages(){
        return[



        'status.required' => 'Le status est obligatoire.',
        'status.string'   => 'Le status doit être une chaîne de caractères.',
        'status.max'      => 'Le status ne peut pas dépasser 255 caractères.',

        'group_sanguin.required' => 'Le groupe sanguine est obligatoire.',
        'group_sanguin.string'   => 'Le groupe sanguine doit être une chaîne de caractères.',
        'group_sanguin.max'      => 'Le groupe sanguine ne peut pas dépasser 15 caractères.',
        ];
    }
}
