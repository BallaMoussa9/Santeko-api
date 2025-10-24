<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Patient;
use App\Models\Doctor;
use App\Http\Requests\StartConsultationRequest;
use App\Http\Requests\EndConsultationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ConsultationController extends Controller
{

    /**
     * Démarrer une nouvelle consultation.
     * POST /api/doctors/{doctorId}/patients/{patientId}/consultations/start
     */
    public function startConsultation(StartConsultationRequest $request, Patient $patient): JsonResponse
    {
        $user = auth()->user();

        // 1. Vérification des permissions: Seul un docteur peut démarrer une consultation.
        // Et l'ID du docteur authentifié doit correspondre à celui de la route (implicite si la route est '/doctors/{doctorId}/...')
        // ou si l'API est conçue pour que le docteur authentifié agisse en son nom.
        if (!$user->hasRole('doctor') || !isset($user->doctor) || $user->doctor->id !== $request->route('doctorId')) {
             throw ValidationException::withMessages(['doctor_id' => 'Seul le docteur authentifié peut démarrer une consultation.']);
        }
        $doctor = $user->doctor; // Le docteur authentifié

        // Vérifier s'il y a déjà une consultation en cours pour ce patient et ce docteur
        $existingConsultation = Consultation::where('patient_id', $patient->id)
            ->where('doctor_id', $doctor->id)
            ->where('status', 'in_progress')
            ->first();

        if ($existingConsultation) {
            return response()->json(['message' => 'Une consultation est déjà en cours pour ce patient avec ce docteur.'], 409); // 409 Conflict
        }

        DB::beginTransaction();
        try {
            $consultation = Consultation::create([
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'type' => $request->type,
                'motif' => $request->motif,
                'status' => 'in_progress', // Le statut passe à 'in_progress' au démarrage
                // 'date_prescription' n'est pas utilisé ici car il est lié à Prescription
                // Les autres champs comme diagnostic, traitement, notes, observations sont null au démarrage
            ]);

            DB::commit();
            return response()->json(['message' => 'Consultation démarrée avec succès.', 'consultation' => $consultation], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur lors du démarrage de la consultation: " . $e->getMessage(), ['trace' => $e->getTraceAsString(), 'request_data' => $request->all()]);
            return response()->json(['message' => 'Erreur lors du démarrage de la consultation.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Terminer une consultation.
     * PUT /api/consultations/{consultationId}/end
     */
    public function endConsultation(EndConsultationRequest $request, Consultation $consultation): JsonResponse
    {
        $user = auth()->user();

        // 1. Vérification des permissions: Seul le docteur ayant démarré la consultation peut la terminer
        // ou un administrateur.
        if (!$user->hasRole('admin') && (!isset($user->doctor) || $consultation->doctor_id !== $user->doctor->id)) {
            return response()->json(['message' => 'Accès non autorisé à la fin de cette consultation.'], 403);
        }

        // 2. Vérifier que la consultation est bien 'in_progress'
        if ($consultation->status !== 'in_progress') {
            return response()->json(['message' => 'La consultation n\'est pas en cours et ne peut pas être terminée.'], 409); // 409 Conflict
        }

        DB::beginTransaction();
        try {
            $consultation->update([
                'diagnostic' => $request->diagnostic,
                'traitement' => $request->traitement,
                'notes' => $request->notes,
                'observations' => $request->observations,
                'status' => 'completed', // Le statut passe à 'completed'
            ]);

            DB::commit();
            return response()->json(['message' => 'Consultation terminée avec succès.', 'consultation' => $consultation], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur lors de la fin de la consultation: " . $e->getMessage(), ['trace' => $e->getTraceAsString(), 'request_data' => $request->all()]);
            return response()->json(['message' => 'Erreur lors de la fin de la consultation.', 'error' => $e->getMessage()], 500);
        }

    }

    public function indexByPatient(Patient $patient): JsonResponse // Ou un autre nom comme listForPatient
    {
        $user = auth()->user();

        // --- Logique de permission ---
        // Par exemple : seul le patient lui-même, un médecin associé, ou un admin/infirmier
        if ($user->hasRole('patient') && (!isset($user->patient) || $user->patient->id !== $patient->id)) {
             return response()->json(['message' => 'Accès non autorisé. Vous ne pouvez voir que vos propres consultations.'], 403);
        }
        // Si le médecin est connecté, vérifiez s'il est autorisé à voir ce patient.
        // Cela dépendra de votre modèle de relation.
        // Ex: if ($user->hasRole('doctor') && !$user->doctor->canAccessPatient($patient->id)) { /* ... */ }


        $consultations = Consultation::with([
                'doctor.user', // Pour obtenir le nom du médecin
                'patient.user', // Pour s'assurer que le patient est chargé si besoin
                // Ajoutez ici d'autres relations nécessaires pour l'affichage des détails de consultation
                // 'attachments', 'diagnostics', 'prescriptions' etc.
            ])
            ->where('patient_id', $patient->id)
            ->latest() // Pour les trier de la plus récente à la plus ancienne
            ->get();

        return response()->json($consultations);
    }
}
