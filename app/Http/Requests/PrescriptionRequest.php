<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Patient; // Utilisé pour la validation after

class PrescriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            // patient_id n'est plus 'required' dans le body si on le prend de la route
            // mais on le garde pour la validation interne si un patient_id est fourni.
            'patient_id' => ['nullable', 'exists:patients,id'],
            'consultation_id' => ['nullable', 'exists:consultations,id'],
            'date_prescription' => ['nullable', 'date'], // Peut être défini par le contrôleur si null
            'status' => ['nullable', Rule::in(['active', 'filled', 'expired', 'cancelled'])],
            'notes' => ['nullable', 'string', 'max:2000'],

            'lines' => ['required', 'array', 'min:1'],
            'lines.*.medication_name' => ['required', 'string', 'max:255'],
            'lines.*.dosage' => ['required', 'string', 'max:255'],
            'lines.*.frequency' => ['required', 'string', 'max:255'],
            'lines.*.duration' => ['required', 'string', 'max:255'],
            'lines.*.instructions' => ['nullable', 'string', 'max:255'],
        ];

        // Règles spécifiques pour la mise à jour
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['patient_id'] = ['sometimes', 'exists:patients,id']; // Peut changer le patient pour un admin
            $rules['doctor_id'] = ['sometimes', 'exists:doctors,id']; // Peut changer le docteur pour un admin
            $rules['date_prescription'] = ['sometimes', 'date'];
            $rules['status'] = ['sometimes', Rule::in(['active', 'filled', 'expired', 'cancelled'])];
            $rules['notes'] = ['sometimes', 'string', 'max:2000'];
            // Pour les lignes, la validation peut être plus complexe pour update (ajouter/modifier/supprimer)
            // Pour l'instant, on suppose qu'elles sont envoyées en entier si modifiées.
            // Si vous avez besoin d'une gestion plus fine des lignes en update, il faudrait une logique dédiée.
        }

        return $rules;
    }

    // Ajoutez des règles 'after' si vous avez des validations inter-champs complexes
    public function after(): array
    {
        return [
            function ($validator) {
                // Si un patient_id est fourni dans le corps de la requête, et que l'utilisateur est un patient
                // on pourrait vérifier qu'il correspond à l'utilisateur authentifié.
                // Cependant, pour l'émission par un docteur, le patient_id vient de la route.
                // Cette section est moins critique ici car le contrôleur gère la provenance de patient_id.
            }
        ];
    }

    public function messages(): array
    {
        return [
            'patient_id.required' => 'L\'ID du patient est requis.',
            'patient_id.exists' => 'Le patient spécifié n\'existe pas.',
            'lines.required' => 'Au moins une ligne de prescription (médicament) est requise.',
            'lines.array' => 'Les lignes de prescription doivent être un tableau.',
            'lines.min' => 'Au moins une ligne de prescription est requise.',
            'lines.*.medication_name.required' => 'Le nom du médicament est requis pour chaque ligne.',
            'lines.*.dosage.required' => 'La posologie est requise pour chaque médicament.',
            'lines.*.frequency.required' => 'La fréquence d\'administration est requise pour chaque médicament.',
            'lines.*.duration.required' => 'La durée du traitement est requise pour chaque médicament.',
        ];
    }
}
