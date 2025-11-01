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
        Log::info('User info', ['user_id' => $user->id]);

        // 🔥 SOLUTION D'URGENCE : Création directe sans relation
        $alertData = [
            'patient_id' => 1, // ⚠️ REMPLACEZ par un ID patient qui existe
            'status' => 'en attente',
            'type' => 'urgence',
            'latitude' => $request->latitude ?? 0,
            'longitude' => $request->longitude ?? 0,
            'description' => $request->message ?? 'Urgence SOS',
            'initiated_at' => now(),
        ];

        Log::info('Creating SOS with data:', $alertData);

        $alert = SOSAlert::create($alertData);

        Log::info('=== SOS CREATED SUCCESS ===', ['alert_id' => $alert->id]);

        return response()->json([
            'alert_id' => $alert->id,
            'message' => 'Alerte SOS créée avec succès',
        ]);

    } catch (\Exception $e) {
        Log::error('=== SOS STORE FAILED ===', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'message' => 'Erreur SOS: ' . $e->getMessage()
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
