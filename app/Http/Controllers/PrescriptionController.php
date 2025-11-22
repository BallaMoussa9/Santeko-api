<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use Illuminate\Http\Request;
use App\Models\PrescriptionLine;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log; // Pour le logging
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Consultation; // Utilisé pour la vérification de l'accès
use App\Models\Doctor; // Utilisé pour la résolution de modèle dans la route
use App\Models\Patient; // Utilisé pour la résolution de modèle dans la route
use App\Http\Requests\PrescriptionRequest; // C'est votre StorePrescriptionRequest

class PrescriptionController extends Controller
{
    // Méthodes utilitaires pour trouver Doctor et Patient (si vous les gardez, sinon supprimez)
    // Note : avec la résolution de modèle dans la route, ces méthodes pourraient être moins nécessaires.
    // private function findDoctor(Doctor $doctor): Doctor|JsonResponse
    // {
    //     // La résolution de modèle de Laravel gère déjà le 404
    //     return $doctor;
    // }

    // private function findPatient(Patient $patient): Patient|JsonResponse
    // {
    //     return $patient;
    // }
// Dans App\Http\Controllers\PrescriptionController.php
/**
 * Récupère toutes les prescriptions d'un patient donné.
 * GET /api/patients/{patientId}/prescriptions
 */
public function index($patientId): JsonResponse
{
    $user = auth()->user();
    
    Log::info('📥 API Index Prescriptions appelée', [
        'patient_id' => $patientId,
        'user_id' => $user->id,
        'user_roles' => $user->roles->pluck('name')->toArray() // ✅ Correction
    ]);

    // VÉRIFICATION DES PERMISSIONS AVEC VOTRE SYSTÈME DE RÔLES
    if ($user->hasRole('patient')) {
        $patient = $user->patient;
        if (!$patient || $patient->id != $patientId) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }
    }
    
    // VÉRIFICATION QUE LE PATIENT EXISTE
    $patient = Patient::find($patientId);
    if (!$patient) {
        return response()->json(['message' => 'Patient non trouvé.'], 404);
    }

    try {
        // CHARGER LES PRESCRIPTIONS AVEC LES RELATIONS
        $prescriptions = Prescription::where('patient_id', $patientId)
            ->with([
                'lines', // 🔥 CORRECTION : Utiliser 'lines' 
                'doctor.user', // ✅ Adapté à votre structure
                'consultation'
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        Log::info('✅ Prescriptions trouvées', [
            'count' => $prescriptions->count(),
            'patient_id' => $patientId
        ]);

        return response()->json($prescriptions);

    } catch (\Exception $e) {
        Log::error('❌ Erreur lors du chargement des prescriptions', [
            'patient_id' => $patientId,
            'error' => $e->getMessage()
        ]);

        return response()->json([
            'message' => 'Erreur lors du chargement des prescriptions.',
            'error' => $e->getMessage()
        ], 500);
    }
}

   /**
 * Crée une nouvelle prescription avec validation directe
 * POST /api/doctors/{doctorId}/patients/{patientId}/prescriptions
 */
/**
     * Crée une nouvelle prescription avec validation directe
     * POST /api/doctors/{doctorId}/patients/{patientId}/prescriptions
     */
    public function store(Request $request, $doctorId, $patientId): JsonResponse
    {
        $user = auth()->user();

        // LOGS DE DEBUG
        Log::info('=== STORE PRESCRIPTION - VALIDATION DIRECTE ===', [
            'user_id' => $user->id,
            'doctor_id' => $doctorId,
            'patient_id' => $patientId,
            'request_data' => $request->all()
        ]);

        // VÉRIFICATION DES PERMISSIONS
        if (!$user->hasRole('doctor')) {
            return response()->json(['message' => 'Seul un docteur peut créer une ordonnance.'], 403);
        }

        if (is_null($user->doctor)) {
            return response()->json(['message' => 'Profil docteur non trouvé.'], 403);
        }

        // VALIDATION DIRECTE DES DONNÉES
        $validator = Validator::make($request->all(), [
            'notes' => 'nullable|string|max:1000',
            'prescription_lines' => 'required|array|min:1',
            'prescription_lines.*.medication_name' => 'required|string|max:255',
            'prescription_lines.*.dosage' => 'required|string|max:255',
            'prescription_lines.*.frequency' => 'required|string|max:255',
            'prescription_lines.*.duration' => 'required|string|max:255',
            'prescription_lines.*.instructions' => 'nullable|string|max:255',
        ], [
            'prescription_lines.required' => 'Au moins un médicament est requis pour l\'ordonnance.',
            'prescription_lines.*.medication_name.required' => 'Le nom du médicament est obligatoire.',
            'prescription_lines.*.dosage.required' => 'Le dosage est obligatoire.',
            'prescription_lines.*.frequency.required' => 'La fréquence est obligatoire.',
            'prescription_lines.*.duration.required' => 'La durée est obligatoire.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Données de l\'ordonnance invalides.',
                'errors' => $validator->errors()
            ], 422);
        }

        // VÉRIFICATION CONSULTATION AVEC CRÉATION AUTOMATIQUE SI BESOIN
        $validConsultation = Consultation::where('patient_id', $patientId)
            ->where('doctor_id', $user->doctor->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->latest()
            ->first();

        // Si aucune consultation, en créer une automatiquement
        if (!$validConsultation) {
            try {
                Log::info('Création automatique de consultation pour ordonnance');
                $validConsultation = Consultation::create([
                    'patient_id' => $patientId,
                    'doctor_id' => $user->doctor->id,
                    'type' => 'consultation_ordonnance',
                    'motif' => 'Consultation pour ordonnance',
                    'status' => 'pending',
                ]);
            } catch (\Exception $e) {
                Log::error('Erreur création consultation automatique', ['error' => $e->getMessage()]);
                return response()->json([
                    'message' => 'Erreur lors de la création de la consultation.',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        DB::beginTransaction();
        try {
            // CRÉATION DE LA PRESCRIPTION
            $prescription = Prescription::create([
                'doctor_id' => $user->doctor->id,
                'patient_id' => $patientId,
                'consultation_id' => $validConsultation->id,
                'date_prescription' => now(),
                'status' => 'confirmed',
                'notes' => $request->input('notes'),
            ]);

            // CRÉATION DES LIGNES DE PRESCRIPTION
            foreach ($request->prescription_lines as $line) {
                PrescriptionLine::create([
                    'prescription_id' => $prescription->id,
                    'medication_name' => $line['medication_name'],
                    'dosage' => $line['dosage'],
                    'frequency' => $line['frequency'],
                    'duration' => $line['duration'],
                    'instructions' => $line['instructions'] ?? null,
                ]);
            }

            // MARQUER LA CONSULTATION COMME CONFIRMÉE
            $validConsultation->update(['status' => 'confirmed']);

            DB::commit();

            // CHARGER LES RELATIONS POUR LA RÉPONSE
            $prescription->load('lines');

            Log::info('Ordonnance créée avec succès', [
                'prescription_id' => $prescription->id,
                'consultation_id' => $validConsultation->id,
                'nombre_lignes' => count($request->prescription_lines)
            ]);

            return response()->json([
                'message' => 'Ordonnance créée avec succès',
                'data' => $prescription
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur création ordonnance', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Erreur lors de la création de l\'ordonnance',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Affiche la liste des prescriptions d'un patient donné. (GET /patients/{patientId}/prescriptions)
     */
// App\Http\Controllers\PrescriptionController.php


    /**
     * Affiche une prescription spécifique. (GET /patients/{patientId}/prescriptions/{prescriptionId})
     */
    public function show(Patient $patient, Prescription $prescription): JsonResponse
    {
        $user = auth()->user();

        // S'assurer que la prescription appartient bien au patient de la route
        if ($prescription->patient_id !== $patient->id) {
            throw new ModelNotFoundException("Prescription non trouvée pour ce patient.");
        }

        // --- Gestion des Permissions (idem index) ---
        if ($user->hasRole('patient') && (!isset($user->patient) || $user->patient->id !== $patient->id)) {
             return response()->json(['message' => 'Accès non autorisé.'], 403);
        }
        // ... autres vérifications de rôle ...

        // Charger les relations nécessaires pour l'affichage détaillé
        $prescription->load(['lines', 'doctor.user', 'patient.user', 'consultation']);

        return response()->json($prescription);
    }

    /**
     * Met à jour une prescription existante. (PATCH/PUT /api/prescriptions/{prescriptionId})
     */
    public function update(PrescriptionRequest $request, Prescription $prescription): JsonResponse
    {
        $user = auth()->user();
        $data = $request->validated();

        // 1. Vérification des permissions
        // Seul le docteur émetteur ou un administrateur peut modifier une ordonnance.
        if ($user->hasRole('doctor') && (!isset($user->doctor) || $prescription->doctor_id !== $user->doctor->id)) {
             return response()->json(['message' => 'Accès non autorisé. Vous ne pouvez modifier que vos propres ordonnances.'], 403);
        }
        // Si vous avez un rôle 'admin', ajoutez :
        // if (!$user->hasRole('admin') && !$user->hasRole('doctor')) { ... }
        if (!$user->hasRole('admin') && !$user->hasRole('doctor')) { // Si ni admin ni docteur
             return response()->json(['message' => 'Accès non autorisé à la modification d\'ordonnances.'], 403);
        }


        DB::beginTransaction();
        try {
            $prescription->update($data);

            // Gestion des lignes : c'est la partie délicate pour les updates.
            // Option 1 (Simple) : Supprimer toutes les anciennes lignes et en créer de nouvelles.
            // Ceci est simple mais moins performant si peu de lignes changent.
            if (isset($data['lines'])) {
                $prescription->lines()->delete(); // Supprime toutes les anciennes lignes
                $prescription->lines()->createMany($data['lines']); // Crée les nouvelles
            }
            // Option 2 (Complexe) : Comparer les lignes existantes et nouvelles (ajouter, modifier, supprimer)
            // Nécessite une clé unique pour les lignes (ex: un ID temporaire côté front-end)
            // ou une logique de synchronisation plus avancée (e.g., $prescription->lines()->sync($linesData); avec IDs).
            // Pour l'instant, l'option 1 est suffisante si les lignes sont toujours soumises en bloc.


            DB::commit();
            return response()->json(['message' => 'Ordonnance mise à jour avec succès.', 'data' => $prescription->load('lines')], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur lors de la mise à jour de l'ordonnance: " . $e->getMessage(), ['trace' => $e->getTraceAsString(), 'request_data' => $request->all()]);
            return response()->json(['message' => 'Erreur lors de la mise à jour de l\'ordonnance.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Supprime une prescription. (DELETE /api/prescriptions/{prescriptionId})
     */
    public function destroy(Prescription $prescription): JsonResponse
    {
        $user = auth()->user();

        // 1. Vérification des permissions
        // Seul le docteur émetteur ou un administrateur peut supprimer une ordonnance.
        if ($user->hasRole('doctor') && (!isset($user->doctor) || $prescription->doctor_id !== $user->doctor->id)) {
             return response()->json(['message' => 'Accès non autorisé. Vous ne pouvez supprimer que vos propres ordonnances.'], 403);
        }
        if (!$user->hasRole('admin') && !$user->hasRole('doctor')) {
             return response()->json(['message' => 'Accès non autorisé à la suppression d\'ordonnances.'], 403);
        }

        DB::beginTransaction();
        try {
            // La suppression de la prescription principale devrait cascader vers les lignes si la DB est configurée avec onDelete('cascade')
            // Sinon, il faut supprimer les lignes manuellement avant: $prescription->lines()->delete();
            $prescription->delete();

            DB::commit();
            return response()->json(['message' => 'Ordonnance supprimée avec succès.'], 204); // 204 No Content pour une suppression réussie

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur lors de la suppression de l'ordonnance: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Erreur lors de la suppression de l\'ordonnance.', 'error' => $e->getMessage()], 500);
        }
    }
}
