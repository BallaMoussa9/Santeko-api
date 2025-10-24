<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MedicalReport;
use App\Models\User;
use App\Models\Patient;
use App\Models\Doctor; // Assurez-vous d'importer Doctor
use App\Http\Requests\MedicalReportRequest; // Utilisez votre MedicalReportRequest
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth; // Pour vérifier l'utilisateur authentifié

class MedicalReportController extends Controller
{
    /**
     * 🚨 NOUVELLE MÉTHODE : Récupérer les rapports pour un docteur ET un patient spécifiques
     * Correspond à GET /api/doctors/{doctorId}/patients/{patientId}/medical-reports
     */
    public function indexByDoctorAndPatient($doctorId, $patientId): JsonResponse
    {
        // --- Vérifications d'autorisation et d'existence (TRÈS IMPORTANT) ---
        // 1. Vérifier si le docteur fourni dans l'URL existe
        $doctor = Doctor::find($doctorId);
        if (!$doctor) {
            return response()->json(['message' => 'Docteur non trouvé.'], 404);
        }

        // 2. Vérifier si le patient fourni dans l'URL existe
        $patient = Patient::find($patientId);
        if (!$patient) {
            return response()->json(['message' => 'Patient non trouvé.'], 404);
        }

        // 3. (Optionnel mais recommandé) Vérifier que le docteur authentifié est bien le doctorId de l'URL
        //    Ceci empêche un docteur de voir les rapports d'un autre docteur en manipulant l'URL.
        $authenticatedUser = Auth::user();
        if (!$authenticatedUser->hasRole('admin') && ($authenticatedUser->doctor && $authenticatedUser->doctor->id !== (int)$doctorId)) {
             return response()->json(['message' => 'Vous n\'êtes pas autorisé à accéder aux rapports de ce docteur.'], 403);
        }
        // --- Fin des vérifications ---

        $reports = MedicalReport::where('doctor_id', $doctorId)
                                ->where('patient_id', $patientId)
                                ->with(['patient.user', 'doctor.user']) // Charger les relations pour l'affichage
                                ->latest() // Les plus récents en premier
                                ->get();

        return response()->json($reports);
    }

    /**
     * 🚨 NOUVELLE MÉTHODE : Créer un nouveau rapport médical pour un patient par un docteur spécifique
     * Correspond à POST /api/doctors/{doctorId}/patients/{patientId}/medical-reports
     */
    public function storeByDoctorAndPatient(MedicalReportRequest $request, $doctorId, $patientId): JsonResponse
    {
        // --- Vérifications d'autorisation et d'existence (TRÈS IMPORTANT) ---
        // 1. Vérifier si le docteur fourni dans l'URL existe
        $doctor = Doctor::find($doctorId);
        if (!$doctor) {
            return response()->json(['message' => 'Docteur non trouvé.'], 404);
        }

        // 2. Vérifier si le patient fourni dans l'URL existe
        $patient = Patient::find($patientId);
        if (!$patient) {
            return response()->json(['message' => 'Patient non trouvé.'], 404);
        }

        // 3. (Optionnel mais recommandé) Vérifier que le docteur authentifié est bien le doctorId de l'URL
        $authenticatedUser = Auth::user();
        if (!$authenticatedUser->hasRole('admin') && ($authenticatedUser->doctor && $authenticatedUser->doctor->id !== (int)$doctorId)) {
             return response()->json(['message' => 'Vous n\'êtes pas autorisé à créer des rapports pour ce docteur.'], 403);
        }
        // --- Fin des vérifications ---

        // Validation via FormRequest
        $data = $request->validated();

        $report = MedicalReport::create([
            'doctor_id' => $doctorId,
            'patient_id' => $patientId,
            'report_type' => $data['report_type'],
            'title' => $data['title'],
            'content' => $data['content'],
        ]);

        return response()->json([
            'message' => 'Rapport médical créé avec succès.',
            'record'  => $report->load(['patient.user', 'doctor.user']), // Charger les relations pour la réponse
        ], 201);
    }

    // --- Vos méthodes existantes, ajustées ou gardées telles quelles ---

    /**
     * Afficher les rapports médicaux selon le rôle de l'utilisateur (Méthode originale `index` si toujours nécessaire).
     * Attention: Cette méthode n'est pas directement utilisée par les nouvelles routes imbriquées.
     * Elle pourrait servir pour une liste générale ou un tableau de bord admin.
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();
        $query = MedicalReport::with(['patient.user', 'doctor.user']);

        if ($user->hasRole('patient')) {
            $patient = $user->patient;
            if (!$patient) {
                return response()->json(['message' => 'Patient non trouvé.'], 404);
            }
            $query->where('patient_id', $patient->id);
        } elseif ($user->hasRole('doctor')) {
            $doctor = $user->doctor;
            if (!$doctor) {
                return response()->json(['message' => 'Docteur non trouvé.'], 404);
            }
            // Optionnel : filtrer par doctor_id si nécessaire (par défaut, pas de filtre pour un "index générique")
            // $query->where('doctor_id', $doctor->id);
        } elseif (!$user->hasRole('admin')) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        $perPage = $request->query('per_page', 15);
        $medicalReports = $query->paginate($perPage);

        return response()->json($medicalReports);
    }

    /**
     * Votre méthode `create` originale (qui prend Patient $patient).
     * Si elle n'est plus appelée par le frontend dans ce contexte, vous pouvez la retirer ou la renommer.
     * Je la laisse ici pour référence, mais elle ne sera pas utilisée par le composant de rapports.
     */
    public function create(MedicalReportRequest $request, Patient $patient): JsonResponse
    {
        if (!$patient) {
            return response()->json(['message' => 'Patient non trouvé.'], 404);
        }
        $doctor = auth()->user()->doctor;
        if (!$doctor) {
            return response()->json(['message' => 'Docteur non trouvé.'], 403);
        }
        $data = $request->validated();
        $data['doctor_id'] = $doctor->id;
        $data['patient_id'] = $patient->id;

        $report = MedicalReport::create($data);

        return response()->json([
            'message' => 'Rapport médical créé avec succès.',
            'record'  => $report->load(['patient.user', 'doctor.user']),
        ], 201);
    }


    /**
     * Mettre à jour un rapport médical existant.
     */
    public function update(MedicalReportRequest $request, MedicalReport $report): JsonResponse
    {
        $doctor = auth()->user()->doctor; // Le docteur authentifié
        // Vérifier si le docteur authentifié est celui qui a créé le rapport ou un admin
        if (!$doctor || ($doctor->id !== $report->doctor_id && !Auth::user()->hasRole('admin'))) {
            return response()->json(['message' => 'Vous n’êtes pas autorisé à modifier ce rapport.'], 403);
        }

        $data = $request->validated();
        $report->update($data);

        return response()->json([
            'message' => 'Rapport médical mis à jour avec succès.',
            'record'  => $report->load(['patient.user', 'doctor.user']),
        ]);
    }

    /**
     * Afficher un rapport médical spécifique.
     */
    public function showreport(MedicalReport $report): JsonResponse
    {
        // Autorisation : seul le créateur, le patient concerné, ou un admin peut voir.
        $user = Auth::user();
        if (
            !$user->hasRole('admin') &&
            ($user->doctor && $user->doctor->id !== $report->doctor_id) &&
            ($user->patient && $user->patient->id !== $report->patient_id)
        ) {
             return response()->json(['message' => 'Vous n\'êtes pas autorisé à visualiser ce rapport.'], 403);
        }

        $report->load(['patient.user', 'doctor.user']); // Charge seulement les relations nécessaires
        return response()->json($report);
    }

    /**
     * Afficher tous les rapports médicaux paginés (généralement pour un admin ou une vue d'ensemble).
     */
    public function getAllMedicalReport(Request $request): JsonResponse
    {
        // Si cette méthode est utilisée pour les admins, elle n'a pas besoin de filtrer par docteur/patient.
        // Si elle est pour les docteurs, il faudrait ajouter un filtre si la vue est "tous mes rapports".
        // Pour l'instant, c'est une vue "admin-like".
        $query = MedicalReport::with(['patient.user', 'doctor.user']);

        // Ajoutez la logique de recherche ici si vous voulez que medicalreportSearch soit fusionné
        if ($request->filled('report_type')) {
            $query->where('report_type', $request->report_type);
        }
        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }
        if ($request->filled('content')) {
            $query->where('content', 'like', '%' . $request->content . '%');
        }
        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }
        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }


        $reports = $query->paginate(30);
        return response()->json($reports);
    }

    /**
     * Supprimer un rapport médical.
     */
    public function deleteMedicalreport(MedicalReport $report): JsonResponse
    {
        $doctor = auth()->user()->doctor;
        // Vérifier si le docteur authentifié est celui qui a créé le rapport ou un admin
        if (!$doctor || ($doctor->id !== $report->doctor_id && !Auth::user()->hasRole('admin'))) {
            return response()->json(['message' => 'Vous n’êtes pas autorisé à supprimer ce rapport.'], 403);
        }

        $report->delete();
        return response()->json(['message' => 'Rapport médical supprimé avec succès.']);
    }

    /**
     * Rechercher des rapports médicaux selon des filtres (votre méthode existante).
     * Si getAllMedicalReport inclut déjà le filtrage, cette méthode peut être redondante ou spécifiquement pour une recherche plus avancée.
     */
    public function medicalreportSearch(Request $request): JsonResponse
    {
        // Cette méthode est similaire à getAllMedicalReport si elle applique des filtres globaux.
        // Laissez-la si elle a un but distinct de getAllMedicalReport ou des indexByDoctorAndPatient.
        return $this->getAllMedicalReport($request); // Ou réimplémentez la logique spécifique
    }

    public function downloadReport(MedicalReport $report): \Symfony\Component\HttpFoundation\StreamedResponse | JsonResponse
    {
        if (!$report->file_path || !Storage::exists($report->file_path)) {
            return response()->json(['message' => 'Fichier de rapport introuvable.'], 404);
        }

        $user = auth()->user();
        // Autorisation: Le docteur qui a créé le rapport, le patient concerné, ou un admin.
        if (
            !$user->hasRole('admin') &&
            ($user->doctor && $user->doctor->id !== $report->doctor_id) &&
            ($user->patient && $user->patient->id !== $report->patient_id)
        ) {
             return response()->json(['message' => 'Accès non autorisé au téléchargement.'], 403);
        }

        $fileName = basename($report->file_path);
        $mimeType = Storage::mimeType($report->file_path);

        return Storage::download($report->file_path, $fileName, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
        ]);
    }
}