<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnalyseReqUpdateRequest extends FormRequest
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
        'analyse_id' => ['required', 'integer', 'exists:analyses_requests,id'],
        'lab_id' => ['required', 'integer', 'exists:laboratorys,id'],
        'patient_id' => ['required', 'integer', 'exists:patients,id'],
        'labtechnicians_id' => ['required', 'integer', 'exists:labtechnicians,id'],
        'result_text' => ['required', 'string', 'max:255'],
        'result_file' => ['required', 'string', 'max:255'],
        'status' => ['required', 'string', 'max:255'],
        'analyse_type' => ['required', 'string', 'max:255'],
        'comments' => ['required', 'string', 'max:255'],
        'validated_at' => ['required', 'date', 'after_or_equal:today'],
    ];
}
public function messages(): array
{
    return [
        'analyse_id.required' => 'Le champ analyse_id est requis.',
        'analyse_id.integer' => 'Le champ analyse_id doit être un nombre entier.',
        'analyse_id.exists' => 'L’analyse spécifiée est introuvable dans la base de données.',

        'lab_id.required' => 'Le champ lab_id est requis.',
        'lab_id.integer' => 'Le champ lab_id doit être un nombre entier.',
        'lab_id.exists' => 'Le laboratoire spécifié est introuvable.',

        'patient_id.required' => 'Le champ patient_id est requis.',
        'patient_id.integer' => 'Le champ patient_id doit être un nombre entier.',
        'patient_id.exists' => 'Le patient spécifié est introuvable.',

        'labtechnicians_id.required' => 'Le champ labtechnicians_id est requis.',
        'labtechnicians_id.integer' => 'Le champ labtechnicians_id doit être un nombre entier.',
        'labtechnicians_id.exists' => 'Le technicien de laboratoire spécifié est introuvable.',

        'result_text.required' => 'Le champ result_text est obligatoire.',
        'result_text.string' => 'Le champ result_text doit être une chaîne de caractères.',
        'result_text.max' => 'Le champ result_text ne doit pas dépasser 255 caractères.',

        'result_file.required' => 'Le champ result_file est obligatoire.',
        'result_file.string' => 'Le champ result_file doit être une chaîne de caractères.',
        'result_file.max' => 'Le champ result_file ne doit pas dépasser 255 caractères.',

        'status.required' => 'Le champ status est obligatoire.',
        'status.string' => 'Le champ status doit être une chaîne de caractères.',
        'status.max' => 'Le champ status ne doit pas dépasser 255 caractères.',

        'analyse_type.required' => 'Le champ analyse_type est obligatoire.',
        'analyse_type.string' => 'Le champ analyse_type doit être une chaîne de caractères.',
        'analyse_type.max' => 'Le champ analyse_type ne doit pas dépasser 255 caractères.',

        'comments.required' => 'Le champ comments est obligatoire.',
        'comments.string' => 'Le champ comments doit être une chaîne de caractères.',
        'comments.max' => 'Le champ comments ne doit pas dépasser 255 caractères.',

        'validated_at.required' => 'Le champ validated_at est obligatoire.',
        'validated_at.date' => 'Le champ validated_at doit être une date valide.',
        'validated_at.after_or_equal' => 'Le champ validated_at doit être aujourd’hui ou une date ultérieure.',
    ];
}

}
