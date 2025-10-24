<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StartConsultationRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Seuls les docteurs authentifiés peuvent démarrer une consultation
        return auth()->check() && auth()->user()->hasRole('doctor');
    }

    public function rules(): array
    {
        return [
            // 'patient_id' est pris de la route, pas du corps de la requête
            'type' => ['required', 'string', 'max:255'],
            'motif' => ['required', 'string', 'max:1000'],
            // D'autres champs peuvent être ajoutés au démarrage si nécessaire
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Le type de consultation est requis.',
            'motif.required' => 'Le motif de consultation est requis.',
        ];
    }
}
