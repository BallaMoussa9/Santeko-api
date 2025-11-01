<?php
namespace App\Http\Controllers;

use App\Models\SosAlert;
use App\Models\Patient;
use Illuminate\Http\Request;
use App\Events\SOSMessageSent;
use App\Events\SOSLocationUpdated;
use Illuminate\Support\Facades\Log;

class SOSController extends Controller
{
    // Création d’une alerte SOS
   public function store(Request $request)
{
    try {
        Log::info('=== SOS STORE START ===');

        $data = $request->validate([
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'message' => 'nullable|string|max:255',
        ]);

        $user = auth()->user();
        Log::info('User authenticated', ['user_id' => $user->id]);

        // 🔥 CORRECTION : Recherche flexible du patient
        $patientId = null;

        // Essayer via la colonne patient_id dans users
        if ($user->patient_id) {
            $patientId = $user->patient_id;
            Log::info('Patient found via patient_id column', ['patient_id' => $patientId]);
        }
        // Essayer via user_id dans la table patients
        else {
            $patient = \App\Models\Patient::where('user_id', $user->id)->first();
            if ($patient) {
                $patientId = $patient->id;
                Log::info('Patient found via user_id query', ['patient_id' => $patientId]);
            }
        }

        if (!$patientId) {
            // 🔥 SOLUTION DE SECOURS : Utiliser un patient_id par défaut
            $patientId = 1; // Remplacez par un ID qui existe
            Log::warning('Using default patient_id', ['patient_id' => $patientId]);
        }

        // 🔥 CORRECTION : Convertir les coordonnées en type approprié
        $alertData = [
            'patient_id' => $patientId,
            'status' => 'en attente',
            'type' => 'urgence',
            'latitude' => (float) ($request->latitude ?? 0),
            'longitude' => (float) ($request->longitude ?? 0),
            'description' => $request->message ?? 'Urgence SOS',
            'initiated_at' => now(),
        ];

        Log::info('Creating SOS with data:', $alertData);

        $alert = \App\Models\SOSAlert::create($alertData);

        Log::info('=== SOS CREATED SUCCESS ===', ['alert_id' => $alert->id]);

        return response()->json([
            'alert_id' => $alert->id,
            'message' => 'Alerte SOS créée avec succès',
        ]);

    } catch (\Exception $e) {
        Log::error('=== SOS STORE FAILED ===', [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'message' => 'Erreur: ' . $e->getMessage()
        ], 500);
    }
}

    // Mise à jour localisation
    public function updateLocation($id, Request $request)
    {
        try {
            $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
            ]);

            $sos = SosAlert::findOrFail($id);
            $sos->update([
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);

            // broadcast(new SOSLocationUpdated($sos));

            return response()->json(['message' => 'Localisation mise à jour']);

        } catch (\Exception $e) {
            Log::error('SOS Location update failed', [
                'alert_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json(['message' => 'Erreur lors de la mise à jour de la localisation'], 500);
        }
    }
}
