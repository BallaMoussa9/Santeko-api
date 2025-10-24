<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Votre logique d'autorisation ici
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
            'status' => ['required', 'string', Rule::in(['canceled', 'rescheduled', 'confirmed', 'completed'])],

            'new_appointment_time' => [
                'nullable',
                'date',
                'after:now',
                'required_if:status,rescheduled'
            ],

            'cancellation_reason' => [
                'nullable',
                'string',
                'required_if:status,canceled'
            ],

        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // Messages pour le champ 'status'
            'status.required' => 'Le statut du rendez-vous  est obligatoire maintenant. donc ',
            'status.string' => 'Le statut du rendez-vous doit être une chaîne de caractères.',
            'status.in' => 'Le statut du rendez-vous n\'est pas valide. Les valeurs autorisées sont : annulé, reporté, confirmé ou terminé.',

            // Messages pour le champ 'new_appointment_time'
            'new_appointment_time.date' => 'La nouvelle date et heure de rendez-vous doivent être une date valide.',
            'new_appointment_time.after' => 'La nouvelle date et heure de rendez-vous doivent être dans le futur.',
            'new_appointment_time.required_if' => 'La nouvelle date et heure de rendez-vous est obligatoire lorsque le statut est "reporté".',

            // Messages pour le champ 'cancellation_reason'
            'cancellation_reason.string' => 'La raison de l\'annulation doit être une chaîne de caractères.',
            'cancellation_reason.required_if' => 'La raison de l\'annulation est obligatoire lorsque le statut est "annulé".',

            // Messages pour le champ 'notes'
        ];
    }
}
