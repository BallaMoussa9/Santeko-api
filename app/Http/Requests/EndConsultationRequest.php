<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EndConsultationRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Seuls le docteur ayant initié la consultation ou un admin peut la terminer
        // La logique sera implémentée dans le contrôleur pour la consultation spécifique.
        return auth()->check() && (auth()->user()->hasRole('doctor') || auth()->user()->hasRole('admin'));
    }

    public function rules(): array
    {
        return [
            'diagnostic' => ['required', 'string', 'max:2000'],
            'traitement' => ['required', 'string', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'observations' => ['nullable', 'string', 'max:2000'],
            // Le statut sera défini à 'completed' par le contrôleur
        ];
    }

    public function messages(): array
    {
        return [
            'diagnostic.required' => 'Le diagnostic est requis pour terminer la consultation.',
            'traitement.required' => 'Le traitement est requis pour terminer la consultation.',
        ];
    }
}
