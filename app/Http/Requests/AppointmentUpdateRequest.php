<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AppointmentUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['sometimes', 'integer', 'exists:patients,id'],
            'doctor_id' => ['sometimes', 'integer', 'exists:doctors,id'],
            'appointment_date' => ['sometimes', 'date', 'after_or_equal:today'],

            'appointment_time' => [
                'sometimes',
                function ($attribute, $value, $fail) {
                    if (
                        !\DateTime::createFromFormat('H:i', $value) &&
                        !\DateTime::createFromFormat('H:i:s', $value)
                    ) {
                        $fail("Le champ $attribute doit être une heure valide (HH:MM ou HH:MM:SS).");
                    }
                }
            ],

            'type' => ['sometimes', 'string', 'max:255'],
            'motif' => ['sometimes', 'string', 'max:255'],
            'status' => ['sometimes', 'string', 'max:255'],
            'cancellation_reason' => ['nullable', 'string', 'max:255'],
            'confirmed_at' => ['nullable', 'date', 'after_or_equal:today'],
            'completed_at' => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'patient_id.integer' => 'Le champ patient_id doit être un nombre entier.',
            'patient_id.exists' => 'Le patient spécifié est introuvable.',

            'doctor_id.integer' => 'Le champ doctor_id doit être un nombre entier.',
            'doctor_id.exists' => 'Le docteur spécifié est introuvable.',

            'appointment_date.date' => 'Le champ appointment_date doit être une date valide.',
            'appointment_date.after_or_equal' => 'La date de rendez-vous doit être aujourd’hui ou ultérieure.',

            'type.string' => 'Le champ type doit être une chaîne de caractères.',
            'type.max' => 'Le champ type ne doit pas dépasser 255 caractères.',

            'motif.string' => 'Le champ motif doit être une chaîne de caractères.',
            'motif.max' => 'Le champ motif ne doit pas dépasser 255 caractères.',

            'status.string' => 'Le champ status doit être une chaîne de caractères.',
            'status.max' => 'Le champ status ne doit pas dépasser 255 caractères.',

            'cancellation_reason.string' => 'Le champ cancellation_reason doit être une chaîne de caractères.',
            'cancellation_reason.max' => 'Le champ cancellation_reason ne doit pas dépasser 255 caractères.',

            'confirmed_at.date' => 'Le champ confirmed_at doit être une date valide.',
            'confirmed_at.after_or_equal' => 'La date confirmed_at doit être aujourd’hui ou ultérieure.',

            'completed_at.date' => 'Le champ completed_at doit être une date valide.',
            'completed_at.after_or_equal' => 'La date completed_at doit être aujourd’hui ou ultérieure.',
        ];
    }
}
