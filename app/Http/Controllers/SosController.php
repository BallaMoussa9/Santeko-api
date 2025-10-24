<?php
namespace App\Http\Controllers;

use App\Models\SosAlert;
use Illuminate\Http\Request;
use App\Events\SOSMessageSent;
use App\Events\SOSLocationUpdated;

class SOSController extends Controller
{
    // Création d’une alerte SOS
  public function store(Request $request)
    {
        $request->validate([
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'message' => 'nullable|string|max:255',
        ]);

        $user = $request->user();
        $patientId = $user->patient?->id;

        // if (!$patientId) {
        //     return response()->json(['message' => 'Utilisateur patient non trouvé.'], 404);
        // }

        $alert = SosAlert::create([
            'patient_id' => $patientId,
            'status' => 'en attente',
            'type' => 'urgence',
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'description' => $request->message ?? 'Urgence SOS',
            'initiated_at' => now(),
        ]);

        return response()->json([
            'alert_id' => $alert->id,
            'message' => 'Alerte SOS créée avec succès',
        ]);
    }
    // Mise à jour localisation
    public function updateLocation($id, Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $sos = SosAlert::findOrFail($id);
        $sos->location = [
            'lat' => $request->latitude,
            'lng' => $request->longitude,
        ];
        $sos->save();

        broadcast(new SOSLocationUpdated($sos));

        return response()->json(['message' => 'Localisation mise à jour']);
    }
}
