<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use App\Models\Doctor; // Utilisé pour la résolution de modèle dans la route
use App\Models\Patient; // Utilisé pour la résolution de modèle dans la route
use App\Models\Consultation; // Utilisé pour la vérification de l'accès
use App\Http\Requests\PrescriptionRequest; // C'est votre StorePrescriptionRequest
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log; // Pour le logging

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


    /**
     * Crée une nouvelle prescription et ses lignes. (POST /doctors/{doctorId}/patients/{patientId}/prescriptions)
     * Cette méthode remplace et améliore votre `issuePrescription`.
     */
    public function store(PrescriptionRequest $request, int $doctor, int $patient): JsonResponse
    {
        $doctor = Doctor::findOrFail($doctor);
        $patient = Patient::findOrFail($patient);
        $user = auth()->user();
        $data = $request->validated();
        $linesData = $data['lines'];
        unset($data['lines']);

        // 1. Vérification des permissions
        // S'assurer que le docteur dans l'URL est bien le docteur authentifié.
        if (!$user->hasRole('doctor') || !isset($user->doctor) || $user->doctor->id !== $doctor->id) {
            throw ValidationException::withMessages(['doctor_id' => 'Seul le docteur authentifié peut émettre une ordonnance en son nom.']);
        }

        // 2. Vérification de l'accès métier (existence d'une consultation)
        $hasAccess = Consultation::where('patient_id', $patient->id)
            ->where('doctor_id', $doctor->id)
            ->whereIn('status', ['completed', 'in_progress']) // Assurez-vous des statuts corrects
            ->exists();

        if (!$hasAccess) {
            return response()->json(['message' => 'Accès refusé. Aucune consultation appropriée trouvée pour émettre une ordonnance.'], 403);
        }

        // 3. Préparation des données de prescription
        $data['patient_id'] = $patient->id; // Patient de la route
        $data['doctor_id'] = $doctor->id; // Docteur de la route (authentifié)
        $data['date_prescription'] = $data['date_prescription'] ?? now(); // Utilise la date fournie ou l'actuelle
        $data['status'] = $data['status'] ?? 'active'; // Statut par défaut si non fourni

        DB::beginTransaction();
        try {
            $prescription = Prescription::create($data);

            foreach ($linesData as $lineData) {
                // Note : Pas besoin de 'prescription_id' ici car create() l'ajoute automatiquement via la relation
                $prescription->lines()->create($lineData);
            }

            DB::commit();

            return response()->json(['message' => 'Ordonnance émise avec succès.', 'data' => $prescription->load('lines')], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur lors de l'émission de l'ordonnance: " . $e->getMessage(), ['trace' => $e->getTraceAsString(), 'request_data' => $request->all()]);
            return response()->json(['message' => 'Erreur lors de l\'émission de l\'ordonnance.', 'error' => $e->getMessage()], 500);
        }
    }


    /**
     * Affiche la liste des prescriptions d'un patient donné. (GET /patients/{patientId}/prescriptions)
     */
// App\Http\Controllers\PrescriptionController.php

public function index(int $patientId): JsonResponse
{
    $user = auth()->user();

    // 1. Charger le patient, renvoie 404 si ID non trouvé.
    try {
        $patient = Patient::findOrFail($patientId);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json(['message' => 'Patient non trouvé.'], 404);
    }

    // 2. Logique de Permission : Assurer que le patient ne voit que ses propres données.
    if ($user->hasRole('patient')) {
        // Vérifie si l'utilisateur est bien le patient cible de l'URL
        if (!isset($user->patient) || $user->patient->id !== $patient->id) {
             return response()->json(['message' => 'Accès non autorisé à ces prescriptions.'], 403);
        }
    }
    // Optionnel : ajouter ici la logique de permission pour Docteur et Admin
    // ...

    // 3. Récupération des données.
    // Utilisation de la relation 'prescriptionLines' et 'doctor.user' pour le frontend.
    $prescriptions = Prescription::where('patient_id', $patient->id)
                                 ->with(['doctor.user', 'lines'])
                                 ->get(); // ->get() retourne toujours une Collection (même vide)

    // 4. Retourner les prescriptions.
    // La méthode response()->json() prend la Collection et la sérialise en JSON.
    // Si $prescriptions est vide, elle retourne '[]', ce qui résout le problème 'undefined'.
    return response()->json($prescriptions);
}
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
