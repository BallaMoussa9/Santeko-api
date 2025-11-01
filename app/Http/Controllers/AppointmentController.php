<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Patient; // Assurez-vous d'importer le modèle Patient
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\AppointmentRequest;
use App\Http\Requests\AppointmentUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use App\Events\AppointmentScheduled; // Notre événement de diffusion
use App\Notifications\NewAppointmentNotification; // Notre notification persistante

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     * Accessible avec différentes vues selon le rôle.
     */
  public function index(): JsonResponse
{
    $user = auth()->user();

    try {
        $query = Appointment::query();

        // 🧩 Appliquer les restrictions selon le rôle
        if ($user->hasRole('patient')) {
            $patient = Patient::where('user_id', $user->id)->first();
            if (!$patient) {
                return response()->json(['message' => 'Patient non trouvé'], 404);
            }
            $query->where('patient_id', $patient->id);

        } elseif ($user->hasRole('doctor')) {
            $doctor = Doctor::where('user_id', $user->id)->first();
            if (!$doctor) {
                return response()->json(['message' => 'Docteur non trouvé'], 404);
            }
            $query->where('doctor_id', $doctor->id);

        } elseif (!$user->hasRole('admin')) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        // 🗓️ ❌ Suppression du filtre sur la date et l'heure
        // On récupère simplement tous les rendez-vous liés à l'utilisateur
        $appointments = $query
            ->with(['patient.user', 'doctor.user'])
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->get();

        return response()->json([
            'message' => 'Tous les rendez-vous récupérés avec succès',
            'data' => $appointments,
        ]);

    } catch (\Exception $e) {
        Log::error('Erreur lors de la récupération des rendez-vous :', ['error' => $e->getMessage()]);
        return response()->json(['message' => 'Erreur serveur'], 500);
    }
}


    /**
     * Store a newly created resource in storage.
     * Les patients peuvent créer leurs propres rendez-vous. Les admins peuvent créer pour n'importe qui.
     */
       // Signature de la méthode mise à jour : reçoit string $patientId de l'URL
      // App/Http/Controllers/AppointmentController.php (Méthode store corrigée avec logs de sécurité)

// ... imports ...
// ...

public function store(Request $request, string $patientId): JsonResponse
{
    try {
        $data = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
            'type' => 'required|in:consultation,suivi,urgence,vaccination,examen,teleconsultation',
            'motif' => 'required|string|max:1000',
        ]);

        $user = auth()->user();

        // 🔥 CORRECTION : Formater le temps correctement
        $time = $data['appointment_time'];
        if (strlen($time) === 5) { // Format "HH:MM"
            $data['appointment_time'] = $time . ':00'; // Devient "HH:MM:00"
        }

        $data['patient_id'] = $patientId;
        $data['status'] = 'pending';

        Log::info('Final data before creation:', $data);

        $appointment = Appointment::create($data);

        return response()->json([
            'message' => 'Rendez-vous créé avec succès.',
            'appointment' => $appointment
        ], 201);

    } catch (\Exception $e) {
        Log::error('STORE ERROR:', ['error' => $e->getMessage()]);
        return response()->json(['message' => $e->getMessage()], 500);
    }
}
    /**
     * Display the specified resource.
     * Les patients et docteurs peuvent voir leurs rendez-vous respectifs. Les admins voient tout.
     */
    public function show(Appointment $appointment): JsonResponse
    {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            return response()->json($appointment->load(['patient.user', 'doctor.user']));
        }

        if ($user->hasRole('patient')) {
            $patient = Patient::where('user_id', $user->id)->first();
            if (!$patient || $appointment->patient_id !== $patient->id) {
                return response()->json(['message' => 'Accès non autorisé à ce rendez-vous.'], 403);
            }
            return response()->json($appointment->load(['patient.user', 'doctor.user']));
        }

        if ($user->hasRole('doctor')) {
            $doctor = $user->doctors;
            if (!$doctor || $appointment->doctor_id !== $doctor->id) {
                return response()->json(['message' => 'Accès non autorisé à ce rendez-vous.'], 403);
            }
            return response()->json($appointment->load(['patient.user', 'doctor.user']));
        }

        return response()->json(['message' => 'Accès non autorisé.'], 403);
    }

    /**
     * Update the specified resource in storage.
     * Les patients peuvent annuler/reprogrammer. Les docteurs peuvent changer le statut. Les admins modifient tout.
     */
    public function update(appointmentUpdateRequest $request, Appointment $appointment): JsonResponse
    {
        $user = auth()->user();
        $data = $request->validated();

        if ($user->hasRole('admin')) {
            $appointment->update($data);
            return response()->json($appointment);
        }

        if ($user->hasRole('patient')) {
            $patient = Patient::where('user_id', $user->id)->first();
            if (!$patient || $appointment->patient_id !== $patient->id) {
                return response()->json(['message' => 'Accès non autorisé à modifier ce rendez-vous.'], 403);
            }
            // Les patients peuvent seulement modifier le statut vers 'canceled' ou 'rescheduled'
            if (isset($data['status']) && !in_array($data['status'], ['canceled', 'rescheduled'])) {
                return response()->json(['message' => 'Vous ne pouvez que annuler ou reprogrammer ce rendez-vous.'], 403);
            }
            $appointment->update($data);
            return response()->json($appointment);
        }

        if ($user->hasRole('doctor')) {
            $doctor = $user->doctors;
            if (!$doctor || $appointment->doctor_id !== $doctor->id) {
                return response()->json(['message' => 'Accès non autorisé à modifier ce rendez-vous.'], 403);
            }
            // Les docteurs peuvent modifier le statut (confirmer, compléter, annuler)
            if (isset($data['status']) && !in_array($data['status'], ['confirmed', 'completed', 'canceled'])) {
                return response()->json(['message' => 'Vous ne pouvez que confirmer, compléter ou annuler ce rendez-vous.'], 403);
            }
            $appointment->update($data);
            return response()->json($appointment);
        }

        return response()->json(['message' => 'Accès non autorisé.'], 403);
    }

    /**
     * Remove the specified resource from storage.
     * Accessible uniquement aux administrateurs.
     */
    public function destroy(Appointment $appointment): JsonResponse
    {
        // Vérification du rôle de l'utilisateur
        $user= auth()->user();
         if ($user->hasRole('admin')) {
            return response()->json(['message' => 'Accès non autorisé.'.auth()->id()], 403);
        }

        $appointment->delete();
        return response()->json(['message' => 'Rendez-vous supprimé avec succès.']);
    }
  public function updateStatusByDoctor(Request $request, int $doctorId, int $appointmentId): JsonResponse
    {
        // 1. Vérification de l'utilisateur et de la correspondance des rôles/IDs
        $user = auth()->user();

        // Sécurité 1 : S'assurer que le docteur connecté correspond bien à l'ID dans l'URL
        if (!$user->hasRole('doctor') || !$user->doctor || $user->doctor->id !== $doctorId) {
             return response()->json(['message' => 'Accès refusé. ID Docteur non valide pour l\'utilisateur connecté.'], 403);
        }

        // 2. Validation du statut
        $request->validate([
            'status' => 'required|in:confirmed,canceled,completed,rescheduled',
        ]);

        $status = $request->input('status');

        // 3. Recherche du rendez-vous
        $appointment = Appointment::find($appointmentId);

        if (!$appointment) {
            return response()->json(['message' => 'Rendez-vous non trouvé.'], 404);
        }

        // 4. Sécurité 2 : Vérification de la propriété du rendez-vous
        // Assurez-vous que ce rendez-vous est bien attribué au docteur spécifié
        if ($appointment->doctor_id !== $doctorId) {
            return response()->json(['message' => 'Ce rendez-vous n\'appartient pas au docteur spécifié.'], 403);
        }

        // 5. Mise à jour du statut et des timestamps
        $updateData = ['status' => $status];

        if ($status === 'confirmed') {
            $updateData['confirmed_at'] = now();
            // Vous pouvez ajouter une notification au patient ici
        } elseif ($status === 'completed') {
            $updateData['completed_at'] = now();
        }

        $appointment->update($updateData);

        // Retourne le rendez-vous mis à jour
        return response()->json($appointment, 200);
    }
}
