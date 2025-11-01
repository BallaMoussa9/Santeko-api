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

    if ($user->hasRole('admin')) {
        // CORRECTION ADMIN : L'Admin a besoin du User lié au Patient et du User lié au Docteur.
        $appointments = Appointment::with([
            'patient.user', // Charger l'utilisateur du patient
            'doctor.user'   // Charger l'utilisateur du docteur
        ])->get();

    } elseif ($user->hasRole('patient')) {
        // Ce bloc était déjà presque correct, mais clarifions la relation du Patient
        $patient = Patient::where('user_id', $user->id)->first();

        if (!$patient) {
            return response()->json(['message' => 'Patient non trouvé pour cet utilisateur.'], 404);
        }

        // CORRECTION PATIENT : Charger le user du doctor. La relation patient est implicite.
        $appointments = Appointment::with(['doctor.user'])
                                   ->where('patient_id', $patient->id)
                                   ->get();

    } elseif ($user->hasRole('doctor')) {
        // Rendez-vous pour le docteur connecté
        $doctor = $user->doctor;

        if (!$doctor) {
            return response()->json(['message' => 'Docteur non trouvé pour cet utilisateur.'], 404);
        }

        // CORRECTION DOCTOR : Charger le user du patient pour afficher son nom.
        // Le doctor est implicitement lié par le filtre where('doctor_id', ...).
        $appointments = Appointment::with(['patient.user'])
                                   ->where('doctor_id', $doctor->id)
                                   ->get();
    } else {
        return response()->json(['message' => 'Accès non autorisé.'], 403);
    }

    // Assurez-vous que l'objet $appointments est toujours une Collection
    $appointmentsArray = $appointments instanceof \Illuminate\Support\Collection ? $appointments->toArray() : [];

    return response()->json([
        'message' => 'Liste des rendez-vous récupérée avec succès.',
        'data' => $appointmentsArray,
    ]);
}

    /**
     * Store a newly created resource in storage.
     * Les patients peuvent créer leurs propres rendez-vous. Les admins peuvent créer pour n'importe qui.
     */
       // Signature de la méthode mise à jour : reçoit string $patientId de l'URL
      // App/Http/Controllers/AppointmentController.php (Méthode store corrigée avec logs de sécurité)

// ... imports ...
// ...

public function store(AppointmentRequest $request, string $patientId): JsonResponse
{
    try {
        $user = auth()->user();
        $data = $request->validated();

        Log::info('=== DÉBUT AppointmentController@store ===');
        Log::info('User:', ['id' => $user->id, 'role' => $user->roles->pluck('name')->first() ?? 'N/A']);
        Log::info('Patient ID from route:', ['patient_id' => $patientId]);
        Log::info('Request data:', $data);

        // Vérification basique de l'utilisateur
        if (!$user) {
            Log::error('User not authenticated');
            return response()->json(['message' => 'Utilisateur non authentifié.'], 401);
        }

        // --- Gestion des Permissions par rôle ---
        if ($user->hasRole('patient')) {
            Log::info('Role: Patient detected');
            if (!$user->patient) {
                Log::error('Patient profile not found for user', ['user_id' => $user->id]);
                return response()->json(['message' => 'Profil patient non trouvé.'], 404);
            }
            $data['patient_id'] = $user->patient->id;
            $data['status'] = $data['status'] ?? 'pending';

        } elseif ($user->hasRole('doctor')) {
            Log::info('Role: Doctor detected');
            if (!$user->doctor) {
                Log::error('Doctor profile not found for user', ['user_id' => $user->id]);
                return response()->json(['message' => 'Profil docteur non trouvé.'], 404);
            }
            $data['doctor_id'] = $user->doctor->id;
            $data['patient_id'] = $patientId;
            $data['status'] = $data['status'] ?? 'scheduled';

        } elseif ($user->hasRole('nurse') || $user->hasRole('admin')) {
            Log::info('Role: Admin/Nurse detected');
            $data['patient_id'] = $patientId;
            $data['status'] = $data['status'] ?? 'scheduled';

        } else {
            Log::error('Unauthorized role', ['user_roles' => $user->roles->pluck('name')->toArray()]);
            return response()->json(['message' => 'Accès non autorisé à cette action.'], 403);
        }

        Log::info('Final appointment data before validation:', $data);

        // Vérification que le patient existe
        $patient = Patient::find($data['patient_id']);
        if (!$patient) {
            Log::error('Patient not found in database', ['patient_id' => $data['patient_id']]);
            return response()->json(['message' => 'Patient non trouvé dans la base de données.'], 404);
        }

        // Vérification que le docteur existe
        if (!isset($data['doctor_id'])) {
            Log::error('Doctor ID missing in request');
            return response()->json(['message' => 'ID docteur manquant.'], 400);
        }

        $doctor = Doctor::find($data['doctor_id']);
        if (!$doctor) {
            Log::error('Doctor not found in database', ['doctor_id' => $data['doctor_id']]);
            return response()->json(['message' => 'Docteur non trouvé dans la base de données.'], 404);
        }

        // --- Vérification de la Disponibilité du créneau ---
        Log::info('Checking availability for doctor:', ['doctor_id' => $data['doctor_id']]);

        $isAvailable = Appointment::where('doctor_id', $data['doctor_id'])
            ->where('appointment_date', $data['appointment_date'])
            ->where('appointment_time', $data['appointment_time'])
            ->whereIn('status', ['scheduled', 'confirmed', 'pending'])
            ->doesntExist();

        Log::info('Availability check result:', ['is_available' => $isAvailable]);

        if (!$isAvailable) {
            Log::warning('Time slot not available', [
                'doctor_id' => $data['doctor_id'],
                'date' => $data['appointment_date'],
                'time' => $data['appointment_time']
            ]);
            return response()->json([
                'message' => 'Ce créneau horaire est déjà pris ou en attente pour ce docteur.'
            ], 409);
        }

        // --- Création du rendez-vous ---
        Log::info('Attempting to create appointment...');

        $appointment = Appointment::create($data);

        Log::info('=== APPOINTMENT CREATED SUCCESSFULLY ===', [
            'appointment_id' => $appointment->id,
            'patient_id' => $appointment->patient_id,
            'doctor_id' => $appointment->doctor_id
        ]);

        return response()->json([
            'message' => 'Rendez-vous créé avec succès.',
            'appointment' => $appointment
        ], 201);

    } catch (\Exception $e) {
        Log::error('=== ERREUR GLOBALE AppointmentController@store ===', [
            'error_message' => $e->getMessage(),
            'error_file' => $e->getFile(),
            'error_line' => $e->getLine(),
            'error_trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'message' => 'Erreur interne du serveur lors de la création du rendez-vous.',
            'error' => config('app.debug') ? $e->getMessage() : 'Contactez l\'administrateur'
        ], 500);
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
