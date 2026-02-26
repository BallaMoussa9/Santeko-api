<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnalyseReqRequest;
use App\Http\Requests\CreateAnalyseRequest;
use App\Http\Requests\UpdateLabRequestStatusRequest;
use App\Models\Analyse;
use App\Models\AnalyseRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
class LabController extends Controller
{
    /**
     * Middleware d'autorisation pour les techniciens de laboratoire.
     * C'est une meilleure approche que la méthode privée.
     * Vous devez définir ce middleware dans app/Http/Kernel.php
     * puis l'utiliser comme ceci : $this->middleware('lab_technician');
     * dans le constructeur de ce contrôleur.
     */
     // Exemple d'implémentation de middleware (à adapter)
    /*
    public function __construct()
    {
        $this->middleware('auth:sanctum'); // Ou une autre garde d'authentification
        $this->middleware('role:lab_technician'); // Ce middleware doit être créé
    }
    */

    /**
     * Lister toutes les demandes d'analyses de laboratoire.
     * GET /api/lab/requests
     */
    public function listLabRequests(Request $request): JsonResponse
    {
        $query = Analyse::with(['patient.user', 'doctor.user']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $analyses = $query->orderBy('created_at', 'desc')->get();
        return response()->json($analyses);
    }
 public function createAnalyseRequest(Request $request): JsonResponse
{
    $data = $request->validate([
        'patient_id'    => 'required|exists:patients,id',
        'laboratory_id' => 'required|exists:laboratorys,id', // Ajoute 'exists' ici pour debugger
        'items'         => 'required|array|min:1',
        'items.*.name'  => 'required|string',
        'items.*.type'  => 'required|string',
    ]);

    // RÉCUPÉRATION DE L'ID MÉDECIN (Pas l'ID User !)
    $doctor = Auth::user()->doctor; // On cherche la relation

    if (!$doctor) {
        return response()->json(['message' => 'Profil médecin introuvable pour cet utilisateur.'], 403);
    }

    try {
        $createdAnalyses = DB::transaction(function () use ($data, $doctor) {
            $records = [];
            foreach ($data['items'] as $item) {
                $records[] = Analyse::create([
                    'patient_id'    => $data['patient_id'],
                    'laboratory_id' => $data['laboratory_id'],
                    'name'          => $item['name'],
                    'type'          => $item['type'],
                    'status'        => 'pending',
                    'doctor_id'     => $doctor->id, // On utilise l'ID du profil médecin
                ]);
            }
            return $records;
        });

        return response()->json(['message' => 'Succès', 'data' => $createdAnalyses], 201);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Erreur SQL', 'error' => $e->getMessage()], 500);
    }
}
    /**
     * Obtenir les détails d'une demande d'analyse spécifique. 
     * GET /api/lab/requests/{analyse}
     * Utilisation de la résolution de modèle pour un code plus propre.
     */
    public function getLabRequest(Analyse $analyse): JsonResponse
    {
        $analyse->load(['patient.user', 'doctor', 'resultats']);
        return response()->json($analyse);
    }

    // LabController.php
public function updateLabRequestStatus(Request $request, $id): JsonResponse
{
    // On cherche l'analyse par l'ID reçu dans l'URL
    $analyse = Analyse::findOrFail($id); 

    // On met à jour l'EXISTANCE
    $analyse->status = $request->status;
    $analyse->save(); 

    $analyse->load(['patient.user', 'doctor.user']); 

    return response()->json([
        'message' => 'Statut mis à jour.',
        'lab_request' => $analyse
    ], 200);
}
// Récupérer le stock de sang actuel
public function getBloodStock()
{
    return \App\Models\BloodUnit::with('patient.user')
        ->orderBy('collection_date', 'desc')
        ->get();
}

// Enregistrer un nouveau prélèvement
public function storeBloodUnit(Request $request)
{
    $validated = $request->validate([
        'patient_id'       => 'required|exists:patients,id',
        'unit_number'      => 'required|unique:blood_units,unit_number',
        'collection_date'  => 'required|date',
        'expiration_date'  => 'required|date|after:collection_date',
        'location'         => 'nullable|string',
        'status'           => 'required|in:available,used,expired,quarantined',
    ]);

    try {
        // 1. Récupérer le patient pour avoir son groupe sanguin automatique
        $patient = \App\Models\Patient::findOrFail($request->patient_id);
        
        // 2. Extraire le groupe et le rhésus (Ex: "A+" -> "A" et "Positive")
        $group = preg_replace('/[^A-Z]/', '', strtoupper($patient->group_sanguine));
        $rhesus = str_contains($patient->group_sanguine, '+') ? 'Positive' : 'Negative';

        // 3. Création de l'unité
        $bloodUnit = \App\Models\BloodUnit::create([
            'patient_id'      => $validated['patient_id'],
            'unit_number'     => $validated['unit_number'],
            'blood_group'     => $group,
            'rh_factor'       => $rhesus,
            'collection_date' => $validated['collection_date'],
            'expiration_date' => $validated['expiration_date'],
            'location'        => $validated['location'],
            'status'          => $validated['status'],
        ]);

        return response()->json([
            'message' => 'Unité de sang enregistrée et liée au patient avec succès.',
            'data' => $bloodUnit->load('patient.user')
        ], 201);

    } catch (\Exception $e) {
        return response()->json(['message' => 'Erreur lors de l\'enregistrement', 'error' => $e->getMessage()], 500);
    }
}
  public function uploadLabResults(Request $request, $id): JsonResponse
{
    // On récupère l'analyse manuellement avec l'ID pour s'assurer qu'elle n'est pas vide
    $analyse = \App\Models\Analyse::findOrFail($id);


    try {
        // 1. Vérification du Technicien
        $technician = \App\Models\LabTechnician::where('user_id', auth()->id())->first();

        if (!$technician) {
            return response()->json([
                'message' => 'Profil technicien introuvable pour cet utilisateur.'
            ], 403);
        }

        // 2. Préparation des données
        // Maintenant $analyse->patient_id contient bien l'ID (ex: 13)
        $patientId = $analyse->patient_id; 
        $results = json_decode($request->result_data_json, true) ?: [];
        $patientName = $request->patient_name;
        $date = now()->format('d/m/Y H:i');

        // 3. Génération PhpWord (Inchangé mais sécurisé)
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();
        
        // Styles
        $headerStyle = ['bold' => true, 'size' => 16, 'color' => '1F4E79'];
        $titleStyle = ['bold' => true, 'size' => 12, 'color' => '2E74B5'];
        $tableStyle = ['borderSize' => 6, 'borderColor' => '999999', 'cellMargin' => 80];

        $section->addText("RESULTATS D'ANALYSES BIOMÉDICALES", $headerStyle, ['alignment' => 'center']);
        $section->addTextBreak(1);

        $infoTable = $section->addTable();
        $infoTable->addRow();
        $infoTable->addCell(5000)->addText("Patient : " . $patientName, ['bold' => true]);
        $infoTable->addCell(5000)->addText("Date : " . $date, ['bold' => true]);

        $section->addTextBreak(1);
        $section->addText("DÉTAILS DES MESURES", $titleStyle);
        $table = $section->addTable($tableStyle);
        $table->addRow();
        $table->addCell(3000)->addText("Paramètre", ['bold' => true]);
        $table->addCell(2000)->addText("Résultat", ['bold' => true]);
        $table->addCell(2000)->addText("Unité", ['bold' => true]);
        $table->addCell(3000)->addText("Référence", ['bold' => true]);

        foreach ($results as $res) {
            $table->addRow();
            $table->addCell(3000)->addText($res['parameter'] ?? '-');
            $table->addCell(2000)->addText($res['value'] ?? '-', ['bold' => true]);
            $table->addCell(2000)->addText($res['unit'] ?? '-');
            $table->addCell(3000)->addText($res['reference'] ?? '-');
        }

        // Sauvegarde physique du fichier
        $fileName = "analyse_" . $analyse->id . "_" . time() . ".docx";
        $filePath = "lab_results/" . $fileName;
        
        $tempFile = tempnam(sys_get_temp_dir(), 'lab_') . '.docx';
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($tempFile);
        \Storage::disk('public')->put($filePath, file_get_contents($tempFile));
        unlink($tempFile);

        // 4. INSERTION EN BASE DE DONNÉES
        $resultat = \App\Models\AnalyseRequest::create([
            'analyses_id'       => $analyse->id,            
            'patient_id'        => $patientId,              
            'labtechnician_id'  => $technician->id,         
            'lab_id'            => $analyse->laboratory_id, 
            'status'            => 'completed',
            'analyse_type'      => $request->analyse_type,
            'result_file'       => $filePath,
            'comments'          => $request->comments,
            'result_data_json'  => $request->result_data_json,
        ]);

        // 5. Mise à jour du statut de l'examen d'origine
        $analyse->update(['status' => 'completed']);

        Log::info("Enregistrement réussi pour l'analyse ID: {$analyse->id}");

        return response()->json([
            'message' => 'Succès',
            'resultat_id' => $resultat->id,
            'doc_url' => asset('storage/' . $filePath)
        ], 201);

    } catch (\Exception $e) {
        Log::error("ERREUR SQL/BACKEND: " . $e->getMessage());
        return response()->json([
            'message' => 'Échec de l\'enregistrement en base de données.',
            'error' => $e->getMessage()
        ], 500);
    }
}
    public function showByUser(int $userId)
{
    $user = User::findOrFail($userId);
    // On récupère le technicien lié à cet utilisateur
    $labTechnician = $user->labTechnician()->first(); 

    if (!$labTechnician) {
        return response()->json(['message' => 'Profil technicien de laboratoire non trouvé.'], 404);
    }

    return response()->json(['lab_technician' => $labTechnician]);
}
/**
 * Récupérer uniquement les demandes d'analyses dont le statut est 'completed'.
 * (Celles qui sont prêtes à recevoir leurs résultats numériques)
 * GET /api/lab/ready-analyses
 */
public function getCompletedAnalyses(): JsonResponse
{
    try {
        // On cherche dans la table 'Analyse' (via le modèle Analyse)
        // uniquement celles qui ont le statut 'completed'
        $analyses = \App\Models\Analyse::with(['patient.user', 'doctor.user'])
            ->where('status', 'completed')
            ->orderBy('updated_at', 'desc') // Les plus récemment terminées en premier
            ->get();

        return response()->json($analyses);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Erreur lors de la récupération des analyses terminées.',
            'error' => $e->getMessage()
        ], 500);
    }
}
}
