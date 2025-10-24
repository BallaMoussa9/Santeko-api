<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegionRequest extends FormRequest
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
            'nom'=> ['required','string','max:255'],
            'code'=>['required','string','max:200'],
            'pays'=>['required','string','max:200'],
            'type'=>['required','string','max:255']
        ];
    }
    public function messages(): array
{
    return [
        'nom.required' => 'Le champ nom est obligatoire.',
        'nom.string' => 'Le champ nom doit être une chaîne de caractères.',
        'nom.max' => 'Le champ nom ne doit pas dépasser 255 caractères.',

        'code.required' => 'Le champ code est obligatoire.',
        'code.string' => 'Le champ code doit être une chaîne de caractères.',
        'code.max' => 'Le champ code ne doit pas dépasser 200 caractères.',

        'pays.required' => 'Le champ pays est obligatoire.',
        'pays.string' => 'Le champ pays doit être une chaîne de caractères.',
        'pays.max' => 'Le champ pays ne doit pas dépasser 200 caractères.',

        'type.required' => 'Le champ type est obligatoire.',
        'type.string' => 'Le champ type doit être une chaîne de caractères.',
        'type.max' => 'Le champ type ne doit pas dépasser 255 caractères.',
    ];
}

}
