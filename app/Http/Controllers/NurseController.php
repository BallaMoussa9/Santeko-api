<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Nurse;
use App\Models\Patient;
use App\Models\BloodUnit;
use App\Models\VitalSign;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\PhpWord;

use PhpOffice\PhpWord\IOFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\ConsultationHistory;
use App\Models\NurseActivityReport;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Storage;
use App\Http\Requests\NurseStoreRequest;
use Illuminate\Support\Facades\Response;
use App\Http\Requests\NurseUpdateRequest;
use App\Http\Requests\RecordVitalSignsRequest;
use App\Http\Requests\CreateActivitiesReportRequest;

class NurseController extends Controller
{
    // ============================
    // CRUD STANDARD
    // ============================

    public function index(Request $request)
    {
        if (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('doctor')) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        $perPage = $request->query('per_page', 15);

        return response()->json(
            Nurse::with('user', 'department')
                ->filter($request)
                ->paginate($perPage)
        );
    }

    public function show(Nurse $nurse)
    {
        if (!auth()->user()->hasRole('admin') &&
            !auth()->user()->hasRole('doctor') &&
            auth()->user()->id !== $nurse->user_id) {

            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        $nurse->load(['user', 'department']);
        return response()->json($nurse);
    }

    public function create(NurseStoreRequest $request): JsonResponse
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $profilePhotoPath = null;

            if ($request->hasFile('profile_photo')) {
                $profilePhotoPath = $request
                    ->file('profile_photo')
                    ->store('profile_photos', 'public');
            }

            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'birth_date' => $data['birth_date'],
                'phone' => $data['phone'],
                'city' => $data['city'],
                'address' => $data['address'],
                'profile_photo' => $profilePhotoPath,
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'country' => $data['country'] ?? null,
                'role' => 'nurse',
            ]);

            $nurse = Nurse::create([
                'user_id' => $user->id,
                'department_id' => $data['department_id'] ?? null,
            ]);

            $user->update(['nurse_id' => $nurse->id]);

            DB::commit();

            $nurse->load(['user', 'department']);

            return response()->json([
                'message' => 'Infirmier créé avec succès.',
                'nurse' => $nurse,
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();

            if ($profilePhotoPath && Storage::disk('public')->exists($profilePhotoPath)) {
                Storage::disk('public')->delete($profilePhotoPath);
            }

            Log::error('Erreur création infirmier : ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Erreur lors de la création de l\'infirmier.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(NurseUpdateRequest $request, Nurse $nurse): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user = $nurse->user;

            if (!$user) {
                return response()->json(['message' => 'Utilisateur associé introuvable.'], 404);
            }

            $userData = $request->only([
                'first_name', 'last_name', 'birth_date', 'phone',
                'city', 'address', 'email', 'country'
            ]);

            if ($request->hasFile('profile_photo')) {
                if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                    Storage::disk('public')->delete($user->profile_photo);
                }

                $userData['profile_photo'] = $request
                    ->file('profile_photo')
                    ->store('profile_photos', 'public');
            }

            if ($request->filled('password')) {
                $userData['password'] = bcrypt($request->password);
            }

            $user->update($userData);

            $nurse->update([
                'department_id' => $request->department_id,
                'speciality' => $request->speciality,
            ]);

            DB::commit();

            $nurse->load(['user', 'department']);

            return response()->json([
                'message' => 'Infirmier mis à jour avec succès.',
                'nurse' => $nurse,
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Erreur mise à jour infirmier : ' . $e->getMessage());

            return response()->json([
                'message' => 'Erreur lors de la mise à jour.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Nurse $nurse): JsonResponse
    {
        if (!auth()->user()->hasRole('admin')) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        DB::beginTransaction();
        try {
            $user = $nurse->user;

            if ($user && $user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            $nurse->delete();
            if ($user) $user->delete();

            DB::commit();

            return response()->json(['message' => 'Infirmier supprimé avec succès.']);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erreur suppression infirmier : ' . $e->getMessage());

            return response()->json([
                'message' => 'Erreur lors de la suppression.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function search(Request $request): JsonResponse
    {
        if (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('doctor')) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        return response()->json(
            Nurse::with('user', 'department')
                ->filter($request)
                ->paginate(10)
        );
    }

    // ============================
    //  UTILITAIRES
    // ============================

    private function authorizeNurse(int $nurseId): ?JsonResponse
    {
        $user = auth()->user();

        if (!$user || $user->id !== $nurseId || !$user->hasRole('nurse')) {
            return response()->json([
                'message' => 'Accès non autorisé.'
            ], 403);
        }

        return null;
    }

    private function findPatient(int $patientId): Patient|JsonResponse
    {
        $patient = Patient::find($patientId);

        return $patient ?: response()->json(['message' => 'Patient non trouvé.'], 404);
    }

    // ============================
    //  MODULE DME / SIGNES VITAUX
    // ============================

    public function getPatientDme(Nurse $nurse, int $patient_id): JsonResponse
    {
        if ($response = $this->authorizeNurse($nurse->user_id)) return $response;

        $patient = $this->findPatient($patient_id);
        if ($patient instanceof JsonResponse) return $patient;

        return response()->json([
            'patient_info' => $patient->load('user'),
            'medical_history' => ConsultationHistory::where('patient_id', $patient->id)->latest()->get(),
            'vital_signs_history' => VitalSign::where('patient_id', $patient->id)->latest('recorded_at')->get(),
        ]);
    }

    public function recordVitalSigns(RecordVitalSignsRequest $request, Nurse $nurse, int $patient_id): JsonResponse
    {
        Log::info('Tentative d’enregistrement des signes vitaux', [
            'nurse_id' => $nurse->id,
            'patient_id' => $patient_id,
        ]);

        if ($response = $this->authorizeNurse($nurse->user_id)) return $response;

        $patient = $this->findPatient($patient_id);
        if ($patient instanceof JsonResponse) return $patient;

        $vitalSign = VitalSign::create([
            'patient_id' => $patient->id,
            'nurse_id' => $nurse->id,
            'blood_pressure_systolic' => $request->blood_pressure_systolic,
            'blood_pressure_diastolic' => $request->blood_pressure_diastolic,
            'heart_rate' => $request->heart_rate,
            'temperature' => $request->temperature,
            'respiratory_rate' => $request->respiratory_rate,
            'oxygen_saturation' => $request->oxygen_saturation,
            'weight' => $request->weight,
            'height' => $request->height,
            'recorded_at' => now(),
            'notes' => $request->notes,
        ]);

        Log::info('Signes vitaux enregistrés', [
            'vital_sign_id' => $vitalSign->id
        ]);

        return response()->json([
            'message' => 'Signes vitaux enregistrés.',
            'vital_sign' => $vitalSign
        ], 201);
    }

    // ============================
    //  RAPPORTS D’ACTIVITÉ
    // ============================

    public function createActivitiesReport(CreateActivitiesReportRequest $request, Nurse $nurse): JsonResponse
    {
        $report = NurseActivityReport::create([
            'nurse_id' => $nurse->id,
            'report_date' => $request->report_date,
            'title' => $request->title,
            'content' => $request->content,
            'patient_id' => $request->patient_id,
        ]);

        return response()->json([
            'message' => 'Rapport créé avec succès.',
            'report' => $report
        ], 201);
    }

    // ============================
    //  BANQUE DE SANG
    // ============================

    public function getBloodBankOverview(Nurse $nurse): JsonResponse
    {
        if ($response = $this->authorizeNurse($nurse->user_id)) return $response;

        return response()->json([
            'available_blood_units' => BloodUnit::orderBy('expiration_date')->get(),
            'blood_bank_summary' => BloodUnit::select('blood_group', 'rh_factor', DB::raw('count(*) as total_units'))
                ->groupBy('blood_group', 'rh_factor')
                ->get(),
        ]);
    }

    // ============================
    //  PROFILE BY USER
    // ============================

    public function getProfileIdByUserId(User $user): JsonResponse
    {
        $nurse = $user->nurse;

        if (!$nurse) {
            return response()->json(['message' => 'Profil infirmier non trouvé.'], 404);
        }

        return response()->json([
            'id' => $nurse->id,
            'user_id' => $nurse->user_id
        ]);
    }

    // ============================
    //  DOWNLOAD REPORT
    // ============================

   /** Téléchargement d’un rapport d’activités */
  /**
 * 🔥 TÉLÉCHARGEMENT PROFESSIONNEL : Rapport d'activité infirmier
 */
public function downloadActivityReport(Nurse $nurse, NurseActivityReport $report)
{
    Log::info("Tentative de téléchargement du rapport d'activité ID: {$report->id}");

    try {
        // Vérifier l'autorisation
        if ($report->nurse_id !== $nurse->id) {
            return response()->json(['message' => 'Accès refusé à ce rapport.'], 403);
        }

        // Charger toutes les relations nécessaires
        $report->load([
            'nurse.user',
            'patient.user'
        ]);

        // Créer le document Word
        $phpWord = new PhpWord();
        
        // Styles professionnels
        $titleStyle = ['bold' => true, 'size' => 16, 'color' => '1F4E79', 'spaceAfter' => 300];
        $headerStyle = ['bold' => true, 'size' => 12, 'color' => '2E74B5', 'spaceAfter' => 150];
        $labelStyle = ['bold' => true, 'size' => 11, 'color' => '444444'];
        $valueStyle = ['size' => 11, 'color' => '000000', 'spaceAfter' => 100];
        $contentStyle = ['size' => 11, 'color' => '333333', 'spaceAfter' => 100];

        // Section principale
        $section = $phpWord->addSection();

        // ====================
        // EN-TÊTE PROFESSIONNEL
        // ====================
        $section->addText('RAPPORT D\'ACTIVITÉ INFIRMIER', $titleStyle);
        $section->addText('HÔPITAL - SERVICE DE SOINS', ['bold' => true, 'size' => 12, 'color' => '666666']);
        $section->addTextBreak(1);

        // ====================
        // INFORMATIONS DU RAPPORT
        // ====================
        $section->addText('INFORMATIONS GÉNÉRALES', $headerStyle);
        
        // Tableau des informations
        $table = $section->addTable([
            'borderSize' => 0,
            'borderColor' => 'FFFFFF',
            'cellMargin' => 50
        ]);

        // Ligne 1 : ID et Titre
        $table->addRow();
        $table->addCell(3000)->addText('ID du rapport:', $labelStyle);
        $table->addCell(5000)->addText($report->id, $valueStyle);

        $table->addRow();
        $table->addCell(3000)->addText('Titre:', $labelStyle);
        $table->addCell(5000)->addText($report->title, $valueStyle);

        $table->addRow();
        $table->addCell(3000)->addText('Date du rapport:', $labelStyle);
        $table->addCell(5000)->addText($report->report_date->format('d/m/Y'), $valueStyle);

        $table->addRow();
        $table->addCell(3000)->addText('Date de génération:', $labelStyle);
        $table->addCell(5000)->addText(now()->format('d/m/Y à H:i'), $valueStyle);

        $section->addTextBreak(1);

        // ====================
        // INFORMATIONS PERSONNEL
        // ====================
        $section->addText('INFORMATIONS DU PERSONNEL', $headerStyle);
        
        $table2 = $section->addTable([
            'borderSize' => 0,
            'borderColor' => 'FFFFFF',
            'cellMargin' => 50
        ]);

        // Infirmier
        $nurseName = 'Non spécifié';
        if ($report->nurse && $report->nurse->user) {
            $firstName = $report->nurse->user->first_name ?? '';
            $lastName = $report->nurse->user->last_name ?? '';
            $nurseName = trim($firstName . ' ' . $lastName) ?: 'Infirmier non renseigné';
        }

        $table2->addRow();
        $table2->addCell(3000)->addText('Infirmier:', $labelStyle);
        $table2->addCell(5000)->addText($nurseName, $valueStyle);

        // ID infirmier
        $table2->addRow();
        $table2->addCell(3000)->addText('Numero Infirmier:', $labelStyle);
        $table2->addCell(5000)->addText($report->nurse_id, $valueStyle);

        // Patient (si lié)
        if ($report->patient_id && $report->patient) {
            $patientName = 'Non spécifié';
            if ($report->patient->user) {
                $firstName = $report->patient->user->first_name ?? '';
                $lastName = $report->patient->user->last_name ?? '';
                $patientName = trim($firstName . ' ' . $lastName) ?: 'Patient non renseigné';
            }

            $table2->addRow();
            $table2->addCell(3000)->addText('Patient concerné:', $labelStyle);
            $table2->addCell(5000)->addText($patientName, $valueStyle);

            $table2->addRow();
            $table2->addCell(3000)->addText('Numero Patient:', $labelStyle);
            $table2->addCell(5000)->addText($report->patient_id, $valueStyle);
        } else {
            $table2->addRow();
            $table2->addCell(3000)->addText('Patient concerné:', $labelStyle);
            $table2->addCell(5000)->addText('Rapport général (non lié à un patient spécifique)', $valueStyle);
        }

        $section->addTextBreak(1);

        // ====================
        // CONTENU DU RAPPORT
        // ====================
        $section->addText('CONTENU DU RAPPORT', $headerStyle);
        
        // Nettoyer et formater le contenu
        $cleanedContent = strip_tags($report->content);
        $contentLines = explode("\n", $cleanedContent);
        
        foreach ($contentLines as $line) {
            if (trim($line) !== '') {
                $section->addText(trim($line), $contentStyle);
            } else {
                $section->addTextBreak(1);
            }
        }

        $section->addTextBreak(2);

        // ====================
        // PIED DE PAGE PROFESSIONNEL
        // ====================
        $section->addText('_________________________', ['size' => 11, 'color' => '000000']);
        $section->addText($nurseName, ['bold' => true, 'size' => 11, 'color' => '000000']);
        $section->addText('Infirmier', ['italic' => true, 'size' => 10, 'color' => '666666']);
        
        $section->addTextBreak(1);
        $section->addText('Document généré électroniquement', [
            'italic' => true, 
            'size' => 9, 
            'color' => '999999'
        ]);
        $section->addText('Hôpital - Service de Soins', [
            'italic' => true, 
            'size' => 9, 
            'color' => '999999'
        ]);

        // ====================
        // GÉNÉRATION DU FICHIER
        // ====================
        $safeTitle = $report->title ? preg_replace('/[^a-zA-Z0-9_-]/', '_', $report->title) : 'rapport_activite';
        $fileName = "rapport_activite_{$report->id}_{$safeTitle}.docx";
        $tempFile = tempnam(sys_get_temp_dir(), 'nurse_report') . '.docx';

        // Sauvegarder le document
        $phpWord->save($tempFile);

        // Vérifier que le fichier a été créé
        if (!file_exists($tempFile)) {
            throw new \Exception("Échec de la génération du fichier Word.");
        }

        // Lire le contenu
        $fileContent = file_get_contents($tempFile);
        $fileSize = filesize($tempFile);

        Log::info("Fichier Word généré avec succès - Rapport ID: {$report->id}, Taille: {$fileSize} bytes");

        // Retourner la réponse de téléchargement
        $response = response()->make($fileContent, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Content-Length' => $fileSize,
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);

        // Nettoyer le fichier temporaire après envoi
        register_shutdown_function(function() use ($tempFile) {
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        });

        return $response;

    } catch (\Throwable $e) {
        Log::error('ERREUR GÉNÉRATION RAPPORT INFIRMIER', [
            'report_id' => $report->id,
            'nurse_id' => $nurse->id,
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'message' => 'Erreur lors de la génération du document Word.',
            'error' => config('app.debug') ? $e->getMessage() : 'Erreur technique',
        ], 500);
    }
}
}
