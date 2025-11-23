<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\FirstResponder;
use App\Models\SOSAlert;
use App\Http\Requests\FirstResponderUpdateRequest;
use App\Http\Requests\FirstResponderRequest;
use App\Http\Requests\SendMessageToPatientRequest;
use App\Http\Resources\FirstResponderCollection;
use App\Http\Resources\FirstResponderResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;


class EmergencyPhysiciansController extends Controller
{
    private function authorizeUrgentist(): ?JsonResponse
    {
        $user = auth()->user();

        // Vérifie bien que l'utilisateur a le rôle urgentist
        if (!$user || !$user->hasRole('urgentist')) {
            return response()->json(['message' => 'Accès non autorisé. Seuls les urgentistes sont permis.'], 403);
        }

        return null;
    }
    
  public function createEmergencyPhysician(FirstResponderRequest $request): JsonResponse
{
    $data = $request->validated();

    // Séparer les données de l'utilisateur et de l'urgentiste
    $userData = Arr::only($data, [
        'first_name', 'last_name', 'birth_date', 'phone', 'country',
        'city', 'profile_photo', 'address', 'email', 'password', 'role_id', 'department_id'
    ]);

    // Hasher le mot de passe
    $userData['password'] = Hash::make($userData['password']);

    // Les données spécifiques à l'urgentiste
    $emergencyData = Arr::only($data, ['speciality', 'location', 'status']);

    try {
        // Transaction pour s'assurer que les deux créations se font ensemble
        DB::beginTransaction();

        // 1️⃣ Création de l'urgentiste d'abord
        $urgentist = FirstResponder::create($emergencyData);

        // 2️⃣ Création de l'utilisateur lié à l'urgentiste
        $userData['first_responder_id'] = $urgentist->id;
        $user = User::create($userData);

        // 3️⃣ Mettre à jour l'urgentiste avec l'user_id
        $urgentist->update(['user_id' => $user->id]);

        DB::commit();

        // Retourner l'objet créé avec la relation 'user'
        return response()->json([
            'message' => 'Urgentiste créé avec succès.',
            'urgentist' => $urgentist->load('user')
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'message' => 'Erreur lors de la création de l\'urgentiste.',
            'error' => $e->getMessage()
        ], 500);
    }
}

    /**
     * Tableau de bord pour les alertes SOS actives (en attente ou en cours).
     */
    public function getActiveSosAlerts(): JsonResponse
    {
        if ($response = $this->authorizeUrgentist()) return $response;

        // Statuts 'en attente' et 'in_progress' sont considérés comme actifs
        $alerts = SOSAlert::with('patient.user')
            ->whereIn('status', ['en attente', 'in_progress']) 
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($alerts);
    }
    
    /**
     * Obtenir l'historique des alertes SOS (statuts 'traite' ou 'annule').
     */
    public function getSosAlertsHistory(): JsonResponse
    {
        if ($response = $this->authorizeUrgentist()) return $response;

        Log::info('Fetching history SOS Alerts for urgentist dashboard.');

        $alerts = SOSAlert::with(['patient.user'])
            ->whereIn('status', ['traite', 'annule']) 
            ->orderBy('initiated_at', 'desc')
            ->get();

        Log::info('Found history SOS Alerts:', ['count' => $alerts->count()]);

        return response()->json($alerts);
    }

    /**
     * Obtenir le résumé des alertes pour le dashboard (Statistiques : En Attente, En Cours, Résolues).
     * Nouvelle implémentation par GROUP BY.
     */
    public function getAlertsStatsByStatus(): JsonResponse
    {
        // Utiliser la même autorisation pour s'assurer que seul un urgentiste peut accéder aux statistiques
        if ($response = $this->authorizeUrgentist()) return $response;

        Log::info('Fetching SOS Alerts statistics by status.');

        try {
            // Compter le nombre d'alertes groupées par leur statut
            $stats = SOSAlert::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get();

            // Transformer la collection en tableau associatif pour un accès plus facile côté client
            $statsArray = $stats->keyBy('status')->map(function ($item) {
                return $item->count;
            });

            Log::info('SOS Alerts statistics generated:', ['stats' => $statsArray]);

            return response()->json([
                'message' => 'Statistiques des alertes SOS récupérées avec succès.',
                'stats' => $statsArray,
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des statistiques SOS: ' . $e->getMessage());
            return response()->json([
                'message' => 'Erreur interne lors de la récupération des statistiques.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtenir les détails d'une alerte SOS
     */
    public function getSosAlertDetails(int $alertId): JsonResponse
    {
        if ($response = $this->authorizeUrgentist()) return $response;

        $alert = SOSAlert::with('patient.user')->find($alertId);

        if (!$alert) {
            return response()->json(['message' => 'Alerte SOS non trouvée.'], 404);
        }

        return response()->json($alert);
    }
/**
 * Prendre en charge une alerte SOS
 */
public function takeChargeOfAlert(int $alertId): JsonResponse
{
    // C'est une bonne pratique de s'assurer que l'utilisateur est bien autorisé.
    if ($response = $this->authorizeUrgentist()) return $response;

    $alert = SOSAlert::find($alertId);
    if (!$alert) {
        return response()->json(['message' => 'Alerte SOS non trouvée.'], 404);
    }

    // Si l'alerte est déjà traitée ou annulée, refuser la prise en charge
    if ($alert->status === 'traite' || $alert->status === 'annule') {
        return response()->json(['message' => 'Cette alerte n\'est plus active.'], 400);
    }
    
    // 🔑 CORRECTION : Récupérer l'ID du First Responder (6) et non l'ID de l'utilisateur (3).
    // Ceci garantit de lire la valeur users.first_responder_id (qui est 6).
    $firstResponderId = User::where('id', auth()->id())->value('first_responder_id');

    if (!$firstResponderId) {
        return response()->json(['message' => 'Profil Urgentiste introuvable pour cet utilisateur.'], 403);
    }

    $alert->first_responder_id = $firstResponderId; // Utilise 6, pas 3
    $alert->status = 'in_progress';
    $alert->initiated_at = now();
    
    // Utilisez update() au lieu de modifier les propriétés et d'appeler save(), 
    // ou assurez-vous que initiated_at est dans $fillable sur le modèle SOSAlert.
    $alert->save();

    return response()->json([
        'message' => 'Alerte SOS prise en charge avec succès.',
        'alert' => $alert
    ]);
}

    /**
     * Résoudre une alerte SOS
     */
    public function resolveAlert(int $alertId): JsonResponse
    {
        if ($response = $this->authorizeUrgentist()) return $response;

        $alert = SOSAlert::find($alertId);
        if (!$alert) {
            return response()->json(['message' => 'Alerte SOS non trouvée.'], 404);
        }
        
        // Vérifie si l'alerte est déjà traitée ou annulée
        if ($alert->status === 'traite' || $alert->status === 'annule') {
            return response()->json(['message' => 'Cette alerte a déjà été traitée ou annulée.'], 400);
        }

        $alert->status = 'traite'; 
        $alert->closed_at = now();
        $alert->save();

        return response()->json([
            'message' => 'Alerte SOS résolue avec succès.',
            'alert' => $alert
        ]);
    }
    /**
     * Envoyer un message au patient
     */
    public function sendMessageToPatient(SendMessageToPatientRequest $request, int $alertId): JsonResponse
    {
        if ($response = $this->authorizeUrgentist()) return $response;

        $alert = SOSAlert::with('patient.user')->find($alertId);
        if (!$alert || !$alert->patient || !$alert->patient->user) {
            return response()->json(['message' => 'Alerte SOS ou patient non trouvé.'], 404);
        }

        $patientUser = $alert->patient->user;
        $message = $request->message;
        $urgentistName = auth()->user()->name;

        Log::info("Message de $urgentistName à {$patientUser->name} ({$patientUser->email}) pour l'alerte {$alert->id}: '{$message}'");

        return response()->json(['message' => "Message envoyé au patient {$patientUser->name}."]);
    }

   /**
     * Liste des urgentistes
     */
    public function getAllEmergencyPhysicians()
    {
        // 🔑 MODIFICATION : Suppression de la relation 'regions' pour éviter les erreurs 500.
        // On conserve 'user' qui est indispensable pour le frontend.
        $urgentics = FirstResponder::with(['user'])->paginate(50);
        
        // 🔑 NOUVEAU LOG : Pour confirmer que la fonction s'exécute côté serveur
        Log::info('Requête API /urgentist exécutée avec succès. Urgentistes trouvés:', [
            'count' => $urgentics->count(),
            'premiere_donnee' => $urgentics->first() ? $urgentics->first()->toArray() : 'Aucune donnée'
        ]);
        
        // La pagination retourne déjà un objet JSON sérialisable.
        return $urgentics;
    }

    /**
     * Détails d'un urgentiste
     */
    public function getEmergencyPhysicians(int $urgentics)
    {
        $urgentics = FirstResponder::findOrFail($urgentics);
        $urgentics->load(['regions', 'user']);
        return $urgentics;
    }

    /**
     * Supprimer un urgentiste
     */
    public function deleteEmergencyPhysicians(FirstResponder $emergency): JsonResponse
    {
        $emergency->delete();
        return response()->json(['message' => 'Urgentiste supprimé avec succès.']);
    }

    /**
     * Mettre à jour un urgentiste (user + infos spécifiques)
     */
    public function updateEmergencyPhysicians(FirstResponderUpdateRequest $request, FirstResponder $emergency): JsonResponse
    {
        $data = $request->validated();

        $user = User::findOrFail($emergency->user_id);

        $userData = Arr::only($data, [
            'first_name',
            'last_name',
            'birth_date',
            'phone',
            'city',
            'profile_photo',
            'address',
            'email',
            'password',
        ]);

        if (!empty($userData['password'])) {
            $userData['password'] = Hash::make($userData['password']);
        }

        $user->update($userData);

        $emergencyData = Arr::only($data, [
            'speciality',
            'location',
            'status',
        ]);

        $emergency->update($emergencyData);

        return response()->json([
            'message' => 'Urgentiste mis à jour avec succès.',
            'urgentics' => $emergency->load('user'),
        ]);
    }

    /**
     * Recherche d'urgentistes
     */
    public function emergencySearch(Request $request)
    {
        $urgentics = User::applyFilters($request, ['first_name', 'last_name', 'email'])->paginate(10);
        $urgentics->load(['users', 'regions']);
        return $urgentics;
    }
    
    /**
     * Récupère l'ID du profil Urgentiste (FirstResponder) par l'ID de l'utilisateur.
     */
    /**
 * Récupère le profil Urgentiste (FirstResponder) via la colonne de liaison dans la table users.
 */
public function getFirstResponderByUserId(int $userId): JsonResponse
{
    // 1. Charger l'utilisateur et son profil FirstResponder via la relation
    // (Ceci est la manière Laravel propre, SI la relation est définie)
    
    // Pour que cela fonctionne, vous devez définir la relation dans le modèle User :
    // public function firstResponder() {
    //     return $this->hasOne(FirstResponder::class, 'clé_étrangère_sur_first_responders', 'clé_locale_sur_users');
    // }
    
    // **OU, si la relation n'est pas définie, en utilisant la colonne users.first_responder_id :**

    $user = User::find($userId);

    if (!$user) {
        return response()->json(['message' => "Utilisateur ID {$userId} non trouvé."], 404);
    }
    
    // 1. Récupérer l'ID du profil Urgentiste (ex: 6) depuis la table users (ID 3)
    $firstResponderId = $user->first_responder_id; 

    if (!$firstResponderId) {
        return response()->json(['message' => "Profil Urgentiste non lié à cet utilisateur."], 404);
    }

    // 2. Trouver le profil Urgentiste (FirstResponder) en utilisant l'ID récupéré
    $firstResponder = FirstResponder::find($firstResponderId);

    if (!$firstResponder) {
        return response()->json(['message' => "Profil Urgentiste introuvable avec l'ID {$firstResponderId}."], 404);
    }

    // 3. Retourner le profil complet
    return response()->json($firstResponder); 
}
}