<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecordVitalSignsRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     */
    public function authorize(): bool
    {
        // En supposant que l'utilisateur est un(e) infirmier(e) connecté(e).
        return true;
    }

    /**
     * Les règles de validation pour les signes vitaux.
     * * J'ai conservé les champs comme 'required' pour vous permettre de choisir
     * quelles données sont absolument obligatoires à la soumission.
     */
    public function rules(): array
    {
        return [
            // Pression Artérielle
            'blood_pressure_systolic'  => 'required|integer|min:50|max:250',
            'blood_pressure_diastolic' => 'required|integer|min:30|max:150',

            // Fréquence Cardiaque et Température
            'heart_rate'               => 'required|integer|min:30|max:220',
            'temperature'              => 'required|numeric|min:30|max:45',

            // Autres mesures
            'respiratory_rate'         => 'required|integer|min:5|max:60',
            'oxygen_saturation'        => 'required|numeric|min:50|max:100',
            'weight'                   => 'required|numeric|min:1|max:500',

            // Note : La taille est en mètres (m) dans votre validation (0.5 à 2.5)
            'height'                   => 'required|numeric|min:0.5|max:2.5',

            // Notes
            'notes'                    => 'nullable|string|max:1000',
        ];
    }

    /**
     * Obtient les messages d'erreur de validation personnalisés.
     * Ces messages sont clairs et compréhensibles par l'utilisateur final.
     */
    public function messages(): array
    {
        return [
            'blood_pressure_systolic.required' => 'La pression systolique est obligatoire.',
            'blood_pressure_systolic.integer'  => 'La pression systolique doit être un nombre entier.',
            'blood_pressure_systolic.min'      => 'La pression systolique ne peut être inférieure à 50 mmHg.',
            'blood_pressure_systolic.max'      => 'La pression systolique ne peut dépasser 250 mmHg.',

            'blood_pressure_diastolic.required' => 'La pression diastolique est obligatoire.',
            'blood_pressure_diastolic.integer'  => 'La pression diastolique doit être un nombre entier.',
            'blood_pressure_diastolic.min'      => 'La pression diastolique ne peut être inférieure à 30 mmHg.',
            'blood_pressure_diastolic.max'      => 'La pression diastolique ne peut dépasser 150 mmHg.',

            'heart_rate.required' => 'La fréquence cardiaque est obligatoire.',
            'heart_rate.integer'  => 'La fréquence cardiaque doit être un nombre entier.',
            'heart_rate.min'      => 'La fréquence cardiaque minimale est de 30 bpm.',
            'heart_rate.max'      => 'La fréquence cardiaque maximale est de 220 bpm.',

            'temperature.required' => 'La température est obligatoire.',
            'temperature.numeric'  => 'La température doit être un nombre valide (ex: 37.1).',
            'temperature.min'      => 'La température ne peut être inférieure à 30 °C.',
            'temperature.max'      => 'La température ne peut dépasser 45 °C.',

            'respiratory_rate.required' => 'La fréquence respiratoire est obligatoire.',
            'respiratory_rate.integer'  => 'La fréquence respiratoire doit être un nombre entier.',
            'respiratory_rate.min'      => 'La fréquence respiratoire minimale est de 5 rpm.',
            'respiratory_rate.max'      => 'La fréquence respiratoire maximale est de 60 rpm.',

            'oxygen_saturation.required' => 'La saturation en oxygène est obligatoire.',
            'oxygen_saturation.numeric'  => 'La saturation en oxygène doit être un nombre valide.',
            'oxygen_saturation.min'      => 'La saturation en oxygène ne peut être inférieure à 50%.',
            'oxygen_saturation.max'      => 'La saturation en oxygène ne peut dépasser 100%.',

            'weight.required' => 'Le poids est obligatoire.',
            'weight.numeric'  => 'Le poids doit être un nombre valide.',
            'weight.min'      => 'Le poids ne peut être inférieur à 1 kg.',
            'weight.max'      => 'Le poids ne peut dépasser 500 kg.',

            'height.required' => 'La taille est obligatoire.',
            'height.numeric'  => 'La taille doit être un nombre valide (en mètres, ex: 1.75).',
            'height.min'      => 'La taille minimale est de 0.5 mètre.',
            'height.max'      => 'La taille maximale est de 2.5 mètres.',

            'notes.string' => 'Les notes doivent être du texte.',
            'notes.max'    => 'Les notes ne peuvent pas dépasser 1000 caractères.',
        ];
    }
}
