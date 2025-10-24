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
                $user = $this->user(); // L'utilisateur authentifié
                $routePatientId = (int) $this->route('patientId'); // L'ID patient extrait de l'URL

                // --- Validation 1: Le patientId de l'URL doit exister ---
                // C'est une vérification fondamentale pour s'assurer que la ressource patient est valide.
                if (!Patient::find($routePatientId)) {
                    $validator->errors()->add('patientId', 'Le patient spécifié dans l\'URL est introuvable.');
                    return; // Arrêter d'autres validations si le patient de la route est invalide
                }

                // --- Validation 2: Logique de permission basée sur le rôle ---
                if ($user->hasRole('patient')) {
                    // Si l'utilisateur est un patient:
                    // Il ne peut prendre rendez-vous que pour lui-même.
                    // 1. Vérifie si le patient authentifié existe et si son ID correspond à celui de la route.
                    if (!isset($user->patient) || $routePatientId !== $user->patient->id) {
                        $validator->errors()->add('patient_id', 'Les patients ne peuvent prendre rendez-vous que pour eux-mêmes.');
                    }
                    // 2. Si le patient tente d'envoyer un 'patient_id' dans le corps de la requête,
                    //    il doit correspondre à son propre ID pour éviter les tentatives de falsification.
                    if ($this->has('patient_id') && $this->input('patient_id') !== $user->patient->id) {
                        $validator->errors()->add('patient_id', 'Le patient spécifié dans la requête ne correspond pas à votre profil.');
                    }
                }
                // --- Validation 3: Pour les autres rôles (Doctor, Admin, Nurse) ---
                // Si ces rôles envoient un 'patient_id' dans le corps de la requête,
                // il doit correspondre au 'patientId' extrait de l'URL pour la cohérence.
                else {
                    if ($this->has('patient_id') && $this->input('patient_id') !== $routePatientId) {
                        $validator->errors()->add('patient_id', 'Le patient spécifié dans la requête ne correspond pas à celui de l\'URL.');
                    }
                }
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