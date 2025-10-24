<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\ConsultationHistory;
use App\Models\Prescription;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PatientDataController extends Controller
{
    /**
     * Un helper pour trouver le patient et retourner une erreur si non trouvé.
     */
    private function findPatient(int $patientId): Patient|JsonResponse
    {
        $patient = Patient::find($patientId);
        if (!$patient) {
            return response()->json(['message' => 'Patient non trouvé.'], 404);
        }
        return $patient;
    }

    /**
     * Accéder à l'historique médical d'un patient.
     * GET /api/patient/{id}/medical-history
     */
    public function getMedicalHistory(int $patientId): JsonResponse
    {
        $patient = $this->findPatient($patientId);
        if ($patient instanceof JsonResponse) {
            return $patient;
        }

        $medicalHistory = ConsultationHistory::where('patient_id', $patient->id)
                                             ->orderBy('date_consultation', 'desc')
                                             ->get();
        return response()->json($medicalHistory);
    }

    /**
     * Consulter les ordonnances d'un patient.
     * GET /api/patient/{id}/prescriptions
     */
    public function getPrescriptions(int $patientId): JsonResponse
    {
        $patient = $this->findPatient($patientId);
        if ($patient instanceof JsonResponse) {
            return $patient;
        }
        $prescriptions = Prescription::with('lines')
                                     ->where('patient_id', $patient->id)
                                     ->orderBy('created_at', 'desc')
                                     ->get();
        return response()->json($prescriptions);
    }

    /**
     * Afficher les notifications d'un patient.
     * GET /api/patient/{id}/notifications
     */
    public function getNotifications(int $patientId): JsonResponse
    {
        $patient = $this->findPatient($patientId);
        if ($patient instanceof JsonResponse) {
            return $patient;
        }

        $notifications = Notification::where('patient_id', $patient->id)
                                     ->orderBy('created_at', 'desc')
                                     ->get();

        return response()->json($notifications);
    }
}
