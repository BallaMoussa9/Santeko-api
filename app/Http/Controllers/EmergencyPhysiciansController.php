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

        // 1️⃣ Création de l'utilisateur
        $user = User::create($userData);

        // 2️⃣ Création de l'urgentiste lié à l'utilisateur
        $emergencyData['user_id'] = $user->id;
        $urgentist = FirstResponder::create($emergencyData);

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
     * Tableau de bord pour les alertes SOS actives
     */
    public function getActiveSosAlerts(): JsonResponse
    {
        if ($response = $this->authorizeUrgentist()) return $response;

        $alerts = SOSAlert::with('patient.user')
            ->where('status', 'active')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($alerts);
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
        if ($response = $this->authorizeUrgentist()) return $response;

        $alert = SOSAlert::find($alertId);
        if (!$alert) {
            return response()->json(['message' => 'Alerte SOS non trouvée.'], 404);
        }

        if ($alert->status === 'resolved') {
            return response()->json(['message' => 'Cette alerte a déjà été résolue.'], 400);
        }

        $alert->first_responder_id = auth()->id();
        $alert->status = 'in_progress';
        $alert->initiated_at = now();
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

        if ($alert->status === 'resolved') {
            return response()->json(['message' => 'Cette alerte a déjà été résolue.'], 400);
        }

        $alert->status = 'resolved';
        $alert->resolved_at = now();
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
        $urgentics = FirstResponder::with(['regions', 'user'])->paginate(50);
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
}
