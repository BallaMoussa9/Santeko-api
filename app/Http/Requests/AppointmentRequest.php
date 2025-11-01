<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Models\Patient; // Pour la validation manuelle si nécessaire

class AppointmentRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     */
    public function authorize(): bool
    {
        return auth()->check(); // Seuls les utilisateurs authentifiés peuvent faire cette requête
    }

    /**
     * Règles de validation pour la requête.
     * Ces règles s'appliquent aux données envoyées dans le corps de la requête.
     */
    public function rules(): array
    {
        // Logique pour obtenir le rôle de l'utilisateur authentifié pour le log
        // ✅ CORRECTION ICI : Utilisez la relation roles() pour extraire le nom du rôle
        $userRole = auth()->user()->roles->pluck('name')->first() ?? 'N/A';

        Log::info('AppointmentRequest rules method called.', [
            'method' => $this->method(),
            'user_role' => $userRole,
            'route_patientId' => $this->route('patientId') // Récupère le patientId de la route si disponible
        ]);

        $rules = [
            'doctor_id' => ['required', 'exists:doctors,id'],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'], // Date du rendez-vous ne peut pas être dans le passé

            // Validation du temps au format HH:MM ou HH:MM:SS
            'appointment_time' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (
                        !\DateTime::createFromFormat('H:i', $value) &&
                        !\DateTime::createFromFormat('H:i:s', $value)
                    ) {
                        $fail("Le champ $attribute doit être une heure valide (HH:MM ou HH:MM:SS).");
                    }
                }
            ],
            // Type de rendez-vous avec une liste prédéfinie de valeurs acceptables
            'type' => ['required', 'string', Rule::in(['consultation', 'suivi', 'urgence', 'vaccination', 'examen', 'teleconsultation'])],
            'motif' => ['required', 'string', 'max:1000'], // Motif plus long possible pour la description
            'cancellation_reason' => ['nullable', 'string', 'max:255'], // Raison d'annulation optionnelle
            'confirmed_at' => ['nullable', 'date'], // Date de confirmation (si applicable)
            'completed_at' => ['nullable', 'date'], // Date de complétion (si applicable)
        ];

        // --- Règles spécifiques pour la création (méthode POST) ---
        if ($this->isMethod('post')) {
            // patient_id dans le corps de la requête est facultatif ici.
            // Le contrôleur utilisera principalement le patientId de la route,
            // mais on le valide s'il est envoyé pour éviter les IDs invalides.
            $rules['patient_id'] = ['nullable', 'exists:patients,id'];

            // Le statut peut être 'pending' (pour les patients) ou 'confirmed'/'scheduled' (pour admins/doctors)
            $rules['status'] = ['sometimes', Rule::in(['pending', 'confirmed', 'scheduled'])];
        }

        // --- Règles spécifiques pour la mise à jour (méthodes PUT ou PATCH) ---
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            // Pour la mise à jour, patient_id et doctor_id peuvent être mis à jour,
            // mais ils doivent toujours exister dans la base de données.
            $rules['patient_id'] = ['sometimes', 'exists:patients,id'];
            $rules['doctor_id'] = ['sometimes', 'exists:doctors,id'];

            // Les statuts possibles pour une mise à jour sont plus nombreux
            $rules['status'] = ['required', Rule::in([
                'pending',
                'confirmed',
                'canceled',
                'rescheduled',
                'completed',
                'scheduled'
            ])];
        }

        return $rules;
    }

    /**
     * Ajout de validations conditionnelles après les règles principales.
     * Cette méthode est utilisée pour des validations plus complexes qui dépendent
     * du contexte de l'utilisateur ou d'autres paramètres non couverts par les règles simples.
     */
   public function after(): array
{
    return [
        function ($validator) {
            $user = $this->user();
            $routePatientId = (int) $this->route('patientId');

            Log::info('AppointmentRequest after() validation - User info:', [
                'user_id' => $user->id,
                'route_patient_id' => $routePatientId,
                'user_roles' => $user->roles->pluck('name')->toArray()
            ]);

            // --- Validation 1: Le patientId de l'URL doit exister ---
            if (!Patient::find($routePatientId)) {
                Log::warning('Patient not found in route', ['patient_id' => $routePatientId]);
                $validator->errors()->add('patientId', 'Le patient spécifié dans l\'URL est introuvable.');
                return;
            }

            // --- Validation 2: Logique de permission basée sur le rôle ---
            if ($user->hasRole('patient')) {
                Log::info('Patient role detected in after() validation');

                // 🔥 CORRECTION : Recherche flexible du patient
                $patient = null;

                // Essayer via la relation Eloquent
                if ($user->patient) {
                    $patient = $user->patient;
                    Log::info('Patient found via Eloquent in after()', ['patient_id' => $patient->id]);
                }
                // Essayer via la colonne patient_id
                elseif ($user->patient_id) {
                    $patient = Patient::find($user->patient_id);
                    if ($patient) {
                        Log::info('Patient found via patient_id column in after()', ['patient_id' => $patient->id]);
                    }
                }
                // Essayer via user_id
                else {
                    $patient = Patient::where('user_id', $user->id)->first();
                    if ($patient) {
                        Log::info('Patient found via user_id query in after()', ['patient_id' => $patient->id]);
                    }
                }

                if (!$patient) {
                    Log::error('Patient profile not found in after() validation', ['user_id' => $user->id]);
                    $validator->errors()->add('patient_id', 'Profil patient non trouvé pour cet utilisateur.');
                    return;
                }

                // Vérifier que le patient de la route correspond au patient connecté
                if ($routePatientId !== $patient->id) {
                    Log::warning('Patient ID mismatch in after()', [
                        'patient_id' => $patient->id,
                        'route_patient_id' => $routePatientId
                    ]);
                    $validator->errors()->add('patient_id', 'Les patients ne peuvent prendre rendez-vous que pour eux-mêmes.');
                }

                // Vérifier le patient_id dans le corps de la requête
                if ($this->has('patient_id') && $this->input('patient_id') !== $patient->id) {
                    Log::warning('Patient ID in request body mismatch', [
                        'request_patient_id' => $this->input('patient_id'),
                        'actual_patient_id' => $patient->id
                    ]);
                    $validator->errors()->add('patient_id', 'Le patient spécifié dans la requête ne correspond pas à votre profil.');
                }

            }
            // --- Validation 3: Pour les autres rôles (Doctor, Admin, Nurse) ---
            else {
                Log::info('Non-patient role detected in after()', ['role' => $user->roles->pluck('name')->first()]);

                if ($this->has('patient_id') && $this->input('patient_id') !== $routePatientId) {
                    Log::warning('Patient ID mismatch for non-patient role', [
                        'request_patient_id' => $this->input('patient_id'),
                        'route_patient_id' => $routePatientId
                    ]);
                    $validator->errors()->add('patient_id', 'Le patient spécifié dans la requête ne correspond pas à celui de l\'URL.');
                }
            }

            Log::info('AppointmentRequest after() validation completed successfully');
        }
    ];
}
    /**
     * Définir des messages d'erreur personnalisés si nécessaire.
     */
    public function messages(): array
    {
        return [
            'doctor_id.required' => 'L\'ID du docteur est requis.',
            'doctor_id.exists' => 'Le docteur spécifié n\'existe pas.',
            'appointment_date.required' => 'La date du rendez-vous est requise.',
            'appointment_date.date' => 'La date du rendez-vous doit être une date valide.',
            'appointment_date.after_or_equal' => 'La date du rendez-vous ne peut pas être dans le passé.',
            'appointment_time.required' => 'L\'heure du rendez-vous est requise.',
            'type.required' => 'Le type de rendez-vous est requis.',
            'type.in' => 'Le type de rendez-vous n\'est pas valide.',
            'motif.required' => 'Le motif du rendez-vous est requis.',
            'motif.max' => 'Le motif du rendez-vous ne peut excéder :max caractères.',
            'patient_id.required' => 'L\'ID du patient est requis pour ce rôle.',
            'patient_id.exists' => 'Le patient spécifié n\'existe pas.',
            'status.required' => 'Le statut du rendez-vous est requis.',
            'status.in' => 'Le statut du rendez-vous n\'est pas valide.',
        ];
    }
}
