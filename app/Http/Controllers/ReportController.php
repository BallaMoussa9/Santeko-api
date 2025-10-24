<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Facades\Response;
use App\Models\NurseActivityReport;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    /**
     * Génère et télécharge le rapport Word (DOCX) basé sur un ID de rapport d'activité.
     * La requête DOIT inclure un 'report_id'.
     */
    public function exportNurseReport(Request $request)
    {
        Log::info('--- Début exportNurseReport ---');
        $reportId = $request->input('report_id');
        Log::info('ID du rapport reçu: ' . $reportId);

        if (!$reportId) {
            Log::warning('L\'ID du rapport est manquant.');
            return response()->json(['message' => 'L\'ID du rapport est manquant.'], 400);
        }

        // 1. Récupération des données avec les jointures
        try {
            $activityReport = NurseActivityReport::with([
                'nurse.user',
                'patient.user',
                'patient.hospitalPatient.hospital'
            ])->findOrFail($reportId);
            Log::info('Rapport d\'activité trouvé: ' . $activityReport->id);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Rapport d\'activité non trouvé pour l\'ID: ' . $reportId, ['exception' => $e->getMessage()]);
            return response()->json(['message' => 'Rapport d\'activité non trouvé.'], 404);
        } catch (\Throwable $e) {
            Log::error('Erreur inattendue lors de la récupération du rapport ou de ses relations pour l\'ID: ' . $reportId, ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Erreur interne lors de la récupération des données du rapport.'], 500);
        }

        // --- Extraction des données ---
        Log::info('Début de l\'extraction des données pour le rapport ID: ' . $activityReport->id);

        // Données du Rapport
        $title = $activityReport->title ?? 'Rapport d\'Activité';
        $reportContent = $activityReport->content ?? 'Contenu du rapport non spécifié.';
        Log::debug('Titre du rapport: ' . $title);

        // Données de l'Infirmier
        $nurse = $activityReport->nurse;
        $nurseUser = $nurse->user;
        $nurseName = "{$nurseUser->first_name} {$nurseUser->last_name}";
        Log::debug('Infirmier: ' . $nurseName);

        // Données du Patient
        $patientUser = $activityReport->patient->user;
        $patientName = "{$patientUser->first_name} {$patientUser->last_name}";
        Log::debug('Patient: ' . $patientName);

        // Données de l'Hôpital (CORRECTION APPLIQUÉE ICI)
        $hospitalPatientPivot = $activityReport->patient->hospitalPatient->first();
        $hospital = $hospitalPatientPivot ? $hospitalPatientPivot->hospital : null;

        $hospitalName = $hospital ? "{$hospital->nom} ({$hospital->ville})" : 'Hôpital non spécifié';
        Log::debug('Hôpital: ' . $hospitalName);
        Log::debug('Contenu du rapport (extrait): ' . substr($reportContent, 0, 100) . '...');

        // 2. Initialisation de PHPWord
        Log::info('Initialisation de PhpWord.');
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        // Styles
        $headingStyle = ['name' => 'Arial', 'size' => 16, 'bold' => true, 'color' => '002580'];
        $infoStyle = ['name' => 'Arial', 'size' => 11];
        $footerStyle = ['name' => 'Arial', 'size' => 9, 'italic' => true, 'color' => '002580'];
        $footerParagraphStyle = ['align' => 'right'];

        // --- Contenu du Document ---
        $section->addText("Rapport d'Activité Santéko : {$title}", $headingStyle);
        $section->addTextBreak(1);
        $section->addText("Généré le : " . now()->format('d/m/Y H:i'), $infoStyle);
        // Utilisation directe du format Carbon si report_date est un objet Carbon
        $section->addText("Date du Rapport : " . $activityReport->report_date->format('d/m/Y'), $infoStyle);
        $section->addText("Patient concerné : {$patientName}", $infoStyle);
        $section->addTextBreak(1);
        $section->addText("Rapporté par : {$nurseName}", ['name' => 'Arial', 'size' => 11, 'bold' => true]);
        $section->addText("Hôpital/Structure : {$hospitalName}", $infoStyle);
        $section->addTextBreak(2);
        $section->addTitle('Contenu Détaillé du Rapport', 2);

        if ($reportContent) {
            \PhpOffice\PhpWord\Shared\Html::addHtml($section, $reportContent);
            Log::debug('Contenu HTML ajouté au document Word.');
        } else {
             $section->addText("Pas de contenu détaillé.", $infoStyle);
             Log::debug('Aucun contenu HTML à ajouter (vide).');
        }

        $section->addTextBreak(3);
        $section->addText(
            "Document généré par la plateforme Santéko.",
            $footerStyle,
            $footerParagraphStyle
        );
        Log::info('Contenu du document Word créé.');

        // 3. Préparation et Téléchargement du Fichier
        $filename = 'Rapport_SanteKo_' . $nurseUser->last_name . '_' . $activityReport->id . '_' . now()->format('Ymd') . '.docx';
        $tempFile = tempnam(sys_get_temp_dir(), 'phpword');
        Log::info('Fichier temporaire créé: ' . $tempFile);

        try {
            $writer = IOFactory::createWriter($phpWord, 'Word2007');
            $writer->save($tempFile);
            Log::info('Document Word enregistré dans le fichier temporaire.');
        } catch (\Throwable $e) {
            Log::error('Erreur lors de l\'enregistrement du document Word: ' . $e->getMessage(), ['exception' => $e, 'trace' => $e->getTraceAsString()]);
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
            return response()->json(['message' => 'Échec de la création du document Word.'], 500);
        }

        // Retourner le fichier pour le téléchargement
        Log::info('Déclenchement du téléchargement du fichier: ' . $filename);
        Log::info('--- Fin exportNurseReport ---');

        return Response::download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'Access-Control-Allow-Origin' => 'http://localhost:5173',
            'Access-Control-Allow-Credentials' => 'true',
        ])->deleteFileAfterSend(true);
    }
}
