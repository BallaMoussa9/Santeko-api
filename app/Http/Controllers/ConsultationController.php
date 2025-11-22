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

   public function startConsultation(StartConsultationRequest $request, $doctorId, $patientId): JsonResponse
{
    $user = auth()->user();
    $patient = Patient::find($patientId);
    
    if (!$patient) {
        return response()->json(['message' => 'Patient non trouvé.'], 404);
    }

    if (!$user->hasRole('doctor') || is_null($user->doctor) || $user->doctor->id != $doctorId) {
        throw ValidationException::withMessages([
            'doctor_id' => 'Seul le docteur authentifié peut démarrer une consultation.'
        ]);
    }
    
    $doctor = $user->doctor;

    // OPTION : Terminer automatiquement les consultations en cours existantes
    $existingConsultations = Consultation::where('patient_id', $patient->id)
        ->where('doctor_id', $doctor->id)
        ->where('status', 'pending')
        ->get();

    if ($existingConsultations->count() > 0) {
        foreach ($existingConsultations as $existing) {
            $existing->update(['status' => 'confirmed']);
        }
    }

    DB::beginTransaction();
    try {
        $consultation = Consultation::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'type' => $request->type,
            'motif' => $request->motif,
            'status' => 'pending',
        ]);

        DB::commit();
        return response()->json([
            'message' => 'Consultation démarrée avec succès.', 
            'consultation' => $consultation
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'message' => 'Erreur lors du démarrage de la consultation.', 
            'error' => $e->getMessage()
        ], 500);
    }
}

    /**
     * Terminer une consultation.
     * PUT /api/consultations/{consultationId}/end
     */
    public function endConsultation(EndConsultationRequest $request, Consultation $consultation): JsonResponse
{
    // Le reste du code reste identique à votre version originale
    $user = auth()->user();

    if (!$user->hasRole('admin') && (!isset($user->doctor) || $consultation->doctor_id !== $user->doctor->id)) {
        return response()->json(['message' => 'Accès non autorisé à la fin de cette consultation.'], 403);
    }

    if ($consultation->status !== 'pending') {
        return response()->json(['message' => 'La consultation n\'est pas en cours et ne peut pas être terminée.'], 409);
    }

    DB::beginTransaction();
    try {
        $consultation->update([
            'diagnostic' => $request->diagnostic,
            'traitement' => $request->traitement,
            'notes' => $request->notes,
            'observations' => $request->observations,
            'status' => 'confirmed',
        ]);

        DB::commit();
        return response()->json(['message' => 'Consultation terminée avec succès.', 'consultation' => $consultation], 200);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error("Erreur lors de la fin de la consultation: " . $e->getMessage(), ['trace' => $e->getTraceAsString(), 'request_data' => $request->all()]);
        return response()->json(['message' => 'Erreur lors de la fin de la consultation.', 'error' => $e->getMessage()], 500);
    }
}

   public function indexByPatient(Patient $patient): JsonResponse
{
    $user = auth()->user();

    // Logique de permission
    if ($user->hasRole('patient') && (!isset($user->patient) || $user->patient->id !== $patient->id)) {
        return response()->json(['message' => 'Accès non autorisé. Vous ne pouvez voir que vos propres consultations.'], 403);
    }

    // Utilisez les relations SINGULIÈRES corrigées
    $consultations = Consultation::with([
            'doctor.user', // Relation SINGULIÈRE 'doctor'
            'patient.user', // Relation SINGULIÈRE 'patient'
        ])
        ->where('patient_id', $patient->id)
        ->latest()
        ->get();

    return response()->json($consultations);
}
    /**
 * Vérifier s'il existe une consultation valide pour émettre une ordonnance
 * GET /api/doctors/{doctorId}/patients/{patientId}/consultations/check
 */
public function checkValidConsultation($doctorId, $patientId): JsonResponse
{
    $user = auth()->user();
    
    Log::info('=== CHECK VALID CONSULTATION DEBUG ===', [
        'user_id' => $user->id,
        'user_roles' => $user->roles->pluck('name')->toArray(),
        'has_doctor' => !is_null($user->doctor),
        'user_doctor_id' => $user->doctor ? $user->doctor->id : 'null',
        'request_doctor_id' => $doctorId,
        'patient_id' => $patientId
    ]);

    // Vérifier les permissions
    if (!$user->hasRole('doctor') || !isset($user->doctor) || $user->doctor->id != $doctorId) {
        Log::warning('Permission refusée dans checkValidConsultation', [
            'is_doctor' => $user->hasRole('doctor'),
            'has_doctor_relation' => isset($user->doctor),
            'doctor_id_match' => $user->doctor ? $user->doctor->id == $doctorId : false
        ]);
        return response()->json(['message' => 'Accès non autorisé.'], 403);
    }
    
    // Chercher une consultation valide
    $validConsultation = Consultation::where('patient_id', $patientId)
        ->where('doctor_id', $doctorId)
        ->whereIn('status', ['pending', 'confirmed'])
        ->latest()
        ->first();
    
    Log::info('Résultat recherche consultation', [
        'consultation_trouvee' => $validConsultation ? $validConsultation->id : 'aucune',
        'statut' => $validConsultation ? $validConsultation->status : 'N/A'
    ]);
    
    return response()->json([
        'has_valid_consultation' => !is_null($validConsultation),
        'consultation' => $validConsultation
    ]);
}
}
