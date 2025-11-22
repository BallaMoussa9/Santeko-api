<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Patient;
use Illuminate\Http\Request;
use App\Models\MedicalReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Doctor;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\MedicalReportRequest;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

class MedicalReportController extends Controller
{
    /**
     * Récupérer les rapports pour un docteur ET un patient spécifiques
     */
    public function indexByDoctorAndPatient($doctorId, $patientId): JsonResponse
    {
        $doctor = Doctor::find($doctorId);
        if (!$doctor) {
            return response()->json(['message' => 'Docteur non trouvé.'], 404);
        }

        $patient = Patient::find($patientId);
        if (!$patient) {
            return response()->json(['message' => 'Patient non trouvé.'], 404);
        }

        $authenticatedUser = Auth::user();
        if (!$authenticatedUser->hasRole('admin') && ($authenticatedUser->doctor && $authenticatedUser->doctor->id !== (int)$doctorId)) {
             return response()->json(['message' => 'Vous n\'êtes pas autorisé à accéder aux rapports de ce docteur.'], 403);
        }

        $reports = MedicalReport::where('doctor_id', $doctorId)
                                ->where('patient_id', $patientId)
                                ->with(['patient.user', 'doctor.user'])
                                ->latest()
                                ->get();

        return response()->json($reports);
    }

    /**
     * Créer un nouveau rapport médical pour un patient par un docteur spécifique
     */
    public function storeByDoctorAndPatient(MedicalReportRequest $request, $doctorId, $patientId): JsonResponse
    {
        $doctor = Doctor::find($doctorId);
        if (!$doctor) {
            return response()->json(['message' => 'Docteur non trouvé.'], 404);
        }

        $patient = Patient::find($patientId);
        if (!$patient) {
            return response()->json(['message' => 'Patient non trouvé.'], 404);
        }

        $authenticatedUser = Auth::user();
        if (!$authenticatedUser->hasRole('admin') && ($authenticatedUser->doctor && $authenticatedUser->doctor->id !== (int)$doctorId)) {
             return response()->json(['message' => 'Vous n\'êtes pas autorisé à créer des rapports pour ce docteur.'], 403);
        }

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
            'record'  => $report->load(['patient.user', 'doctor.user']),
        ], 201);
    }

    /**
     * Afficher les rapports médicaux selon le rôle de l'utilisateur
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
        } elseif (!$user->hasRole('admin')) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        $perPage = $request->query('per_page', 15);
        $medicalReports = $query->paginate($perPage);

        return response()->json($medicalReports);
    }

    /**
     * Créer un rapport médical (legacy)
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
     * Mettre à jour un rapport médical existant
     */
    public function update(MedicalReportRequest $request, MedicalReport $report): JsonResponse
    {
        $doctor = auth()->user()->doctor;
        if (!$doctor || ($doctor->id !== $report->doctor_id && !Auth::user()->hasRole('admin'))) {
            return response()->json(['message' => 'Vous n\'êtes pas autorisé à modifier ce rapport.'], 403);
        }

        $data = $request->validated();
        $report->update($data);

        return response()->json([
            'message' => 'Rapport médical mis à jour avec succès.',
            'record'  => $report->load(['patient.user', 'doctor.user']),
        ]);
    }

    /**
     * Afficher un rapport médical spécifique
     */
    public function showreport(MedicalReport $report): JsonResponse
    {
        $user = Auth::user();
        if (
            !$user->hasRole('admin') &&
            ($user->doctor && $user->doctor->id !== $report->doctor_id) &&
            ($user->patient && $user->patient->id !== $report->patient_id)
        ) {
             return response()->json(['message' => 'Vous n\'êtes pas autorisé à visualiser ce rapport.'], 403);
        }

        $report->load(['patient.user', 'doctor.user']);
        return response()->json($report);
    }

    /**
     * Afficher tous les rapports médicaux paginés
     */
    public function getAllMedicalReport(Request $request): JsonResponse
    {
        $query = MedicalReport::with(['patient.user', 'doctor.user']);

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
     * Supprimer un rapport médical
     */
    public function deleteMedicalreport(MedicalReport $report): JsonResponse
    {
        $doctor = auth()->user()->doctor;
        if (!$doctor || ($doctor->id !== $report->doctor_id && !Auth::user()->hasRole('admin'))) {
            return response()->json(['message' => 'Vous n\'êtes pas autorisé à supprimer ce rapport.'], 403);
        }

        $report->delete();
        return response()->json(['message' => 'Rapport médical supprimé avec succès.']);
    }

    /**
     * Rechercher des rapports médicaux
     */
    public function medicalreportSearch(Request $request): JsonResponse
    {
        return $this->getAllMedicalReport($request);
    }

  /**
 * 🔥 CORRECTION IMMÉDIATE : Télécharger un rapport médical en PDF/Texte
 */
public function downloadReport(MedicalReport $report)
{
    Log::info("Tentative de téléchargement Word du rapport ID: {$report->id}");

    $user = auth()->user();
    
    // Autorisation
    if (
        !$user->hasRole('admin') &&
        ($user->doctor && $user->doctor->id !== $report->doctor_id) &&
        ($user->patient && $user->patient->id !== $report->patient_id)
    ) {
        return response()->json(['message' => 'Accès non autorisé au téléchargement.'], 403);
    }

    try {
        $report->load(['patient.user', 'doctor.user']);

        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();

        // Styles personnalisés
        $headerStyle = ['bold' => true, 'size' => 14, 'color' => '1F4E79', 'spaceAfter' => 300];
        $labelStyle = ['bold' => true, 'size' => 11, 'color' => '2E74B5'];
        $valueStyle = ['size' => 11, 'color' => '000000'];
        $contentStyle = ['size' => 11, 'color' => '000000', 'spaceAfter' => 100];

        // En-tête
        $section->addText('RAPPORT MÉDICAL', $headerStyle);
        $section->addText('', $valueStyle);

        // Tableau des informations
        $table = $section->addTable([
            'borderSize' => 6,
            'borderColor' => 'FFFFFF',
            'cellMargin' => 50
        ]);

        // Informations générales
        $table->addRow();
        $table->addCell(2000)->addText('Numero:', $labelStyle);
        $table->addCell(4000)->addText($report->id, $valueStyle);

        $table->addRow();
        $table->addCell(2000)->addText('Titre:', $labelStyle);
        $table->addCell(4000)->addText($report->title, $valueStyle);

        $table->addRow();
        $table->addCell(2000)->addText('Type:', $labelStyle);
        $table->addCell(4000)->addText(ucfirst($report->report_type), $valueStyle);

        // Informations patient
        $patientName = 'Non spécifié';
        if ($report->patient && $report->patient->user) {
            $firstName = $report->patient->user->first_name ?? '';
            $lastName = $report->patient->user->last_name ?? '';
            $patientName = trim($firstName . ' ' . $lastName) ?: 'Nom non renseigné';
        }

        $table->addRow();
        $table->addCell(2000)->addText('Patient:', $labelStyle);
        $table->addCell(4000)->addText($patientName, $valueStyle);

        // Informations docteur
        $doctorName = 'Non spécifié';
        if ($report->doctor && $report->doctor->user) {
            $firstName = $report->doctor->user->first_name ?? '';
            $lastName = $report->doctor->user->last_name ?? '';
            $doctorName = trim($firstName . ' ' . $lastName) ?: 'Docteur non renseigné';
        }

        $table->addRow();
        $table->addCell(2000)->addText('Docteur:', $labelStyle);
        $table->addCell(4000)->addText($doctorName, $valueStyle);

        // Dates
        $reportDate = $report->created_at ? $report->created_at->format('d/m/Y') : 'Date inconnue';
        $table->addRow();
        $table->addCell(2000)->addText('Date rapport:', $labelStyle);
        $table->addCell(4000)->addText($reportDate, $valueStyle);

        $section->addText('', $valueStyle);

        // Contenu médical
        $section->addText('CONTENU MÉDICAL', ['bold' => true, 'size' => 12, 'color' => '1F4E79', 'spaceAfter' => 200]);
        
        $content = $report->content ?? "Aucun contenu disponible.";
        $section->addText($content, $contentStyle);

        $section->addText('', $valueStyle);
        $section->addText('', $valueStyle);

        // Signature
        $section->addText('Signature et cachet:', ['italic' => true, 'size' => 10, 'color' => '666666']);
        $section->addText('', $valueStyle);
        $section->addText('_________________________', $valueStyle);
        $section->addText($doctorName, ['bold' => true, 'size' => 10]);

        // Générer le fichier
        $safeTitle = $report->title ? preg_replace('/[^a-zA-Z0-9_-]/', '_', $report->title) : 'sans_titre';
        $fileName = "rapport_medical_{$report->id}_{$safeTitle}.docx";

        $tempFile = tempnam(sys_get_temp_dir(), 'medical_report') . '.docx';
        $phpWord->save($tempFile);
        $fileContent = file_get_contents($tempFile);
        unlink($tempFile);

        Log::info("Fichier Word professionnel généré pour le rapport ID: {$report->id}");

        return response()->make($fileContent, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);

    } catch (\Exception $e) {
        Log::error("Erreur génération Word rapport {$report->id}: " . $e->getMessage());
        return response()->json([
            'message' => 'Erreur lors de la génération du document Word.',
            'error' => config('app.debug') ? $e->getMessage() : null
        ], 500);
    }
}
}