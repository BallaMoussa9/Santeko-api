<?php

namespace App\Http\Controllers;

use App\Models\Analyse;
use App\Models\AnalyseRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UpdateLabRequestStatusRequest;
use App\Http\Requests\CreateAnalyseRequest;
use App\Http\Requests\AnalyseReqRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

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
        $query = Analyse::with(['patient.user', 'doctor']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $analyses = $query->orderBy('created_at', 'desc')->get();
        return response()->json($analyses);
    }
 public function createAnalyseRequest(CreateAnalyseRequest $request): JsonResponse
    {
        // 1. Récupération de l'utilisateur authentifié (le médecin)
        $doctor = Auth::user();

        // 2. Création de la demande d'analyse en utilisant les données validées de la requête
        // et en y ajoutant les informations du médecin.
        $analyse = Analyse::create([
            'patient_id' => $request->validated('patient_id'),
            'laboratory_id' => $request->validated('laboratory_id'),
            'consultation_id' => $request->validated('consultation_id'),
            'lab_technician_id' => $request->validated('lab_technician_id'), // Peut être null
            'name' => $request->validated('name'),
            'type' => $request->validated('type'),
            'status' => $request->validated('status', 'pending'), // Utilise 'pending' comme valeur par défaut si non spécifié
            'requested_at' => now(), // Définit la date de la demande à l'instant présent
            'doctor_id' => $doctor->id, // Assigne l'ID du médecin qui fait la demande
        ]);

        // 3. Retourne la nouvelle demande d'analyse et un message de succès
        return response()->json([
            'message' => 'Demande d\'analyse créée avec succès.',
            'data' => $analyse
        ], 201);
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

    /**
     * Mettre à jour le statut d'une demande d'analyse.
     * PUT /api/lab/requests/{analyse}/status
     * Utilisation de la résolution de modèle.
     */
    public function updateLabRequestStatus(UpdateLabRequestStatusRequest $request, Analyse $analyse): JsonResponse
    {
        $analyse->status = $request->status;
        $analyse->save();
        return response()->json(['message' => 'Statut de la demande d\'analyse mis à jour.', 'lab_request' => $analyse], 200);
    }

    /**
     * Télécharger les résultats d'analyse pour une demande spécifique.
     * POST /api/lab/requests/{analyse}/results
     * Utilisation de la résolution de modèle et de la requête de formulaire corrigée.
     */
    public function uploadLabResults(AnalyseReqRequest $request, Analyse $analyse): JsonResponse
    {
        $filePath = null;
        if ($request->hasFile('result_file')) {
            $filePath = $request->file('result_file')->store('lab_results', 'public');
        }

        $resultat = AnalyseRequest::create([
            'analyse_id' => $analyse->id,
            'labtechnician_id' => auth()->id(),
            'lab_id' => $request->lab_id,
            'resultat_text' => $request->resultat_text,
            'status' => 'completed', // Le statut de la demande est "completed" si des résultats sont téléversés
            'analyse_type' => $request->analyse_type,
            'result_file' => $filePath,
            'comments' => $request->comments,
            'result_data_json' => $request->result_data_json,
        ]);

        // Mettre à jour le statut de la demande parente
        $analyse->status = 'completed';
        $analyse->save();

        return response()->json(['message' => 'Résultats d\'analyse téléchargés avec succès.', 'lab_result' => $resultat], 201);
    }
}
