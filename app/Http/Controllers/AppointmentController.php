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
    $user = auth()->user();
    $data = $request->validated();

    // Logs Initiaux
    Log::info('AppointmentController@store - Données validées:', $data);
    Log::info('AppointmentController@store - Utilisateur authentifié:', [
        'id' => $user->id,
        'role' => $user->roles->pluck('name')->first() ?? 'N/A'
    ]);
    Log::info('AppointmentController@store - ID Patient de la route:', ['id' => $patientId]);

    // --- Gestion des Permissions par rôle ---
    if ($user->hasRole('patient')) {
        // Logique de patient
        $data['patient_id'] = $user->patient->id;
        $data['status'] = $data['status'] ?? 'pending';
        Log::info('Permission Check: OK. User is Patient booking for themselves.', ['final_patient_id' => $data['patient_id'], 'status' => $data['status']]);

    } elseif ($user->hasRole('doctor')) {
        // Logique de docteur - CORRECTION : utiliser $patientId au lieu de $routePatientId
        $data['doctor_id'] = $user->doctor->id;
        $data['patient_id'] = $patientId; // 🔥 CORRECTION ICI
        $data['status'] = $data['status'] ?? 'scheduled';
        Log::info('Permission Check: OK. User is Doctor booking for patient.', ['final_patient_id' => $data['patient_id'], 'status' => $data['status']]);

    } elseif ($user->hasRole('nurse') || $user->hasRole('admin')) {
        // Logique Admin/Infirmière - CORRECTION : utiliser $patientId au lieu de $routePatientId
        $data['patient_id'] = $patientId; // 🔥 CORRECTION ICI
        $data['status'] = $data['status'] ?? 'scheduled';
        Log::info('Permission Check: OK. User is Admin/Nurse booking for patient.', ['final_patient_id' => $data['patient_id'], 'status' => $data['status']]);

    } else {
        // Logique de refus
        Log::error('Unauthorized access to AppointmentController@store.', ['user_id' => $user->id, 'roles' => $user->roles->pluck('name')->toArray()]);
        return response()->json(['message' => 'Accès non autorisé à cette action.'], 403);
    }

    // Vérification que le patient existe
    $patient = Patient::find($data['patient_id']);
    if (!$patient) {
        Log::error('Patient not found for appointment creation.', ['patient_id' => $data['patient_id']]);
        return response()->json(['message' => 'Patient non trouvé.'], 404);
    }

    // Vérification que le docteur existe
    $doctor = Doctor::find($data['doctor_id']);
    if (!$doctor) {
        Log::error('Doctor not found for appointment creation.', ['doctor_id' => $data['doctor_id']]);
        return response()->json(['message' => 'Docteur non trouvé.'], 404);
    }

    // --- Vérification de la Disponibilité du créneau ---
    $isAvailable = Appointment::where('doctor_id', $data['doctor_id'])
        ->where('appointment_date', $data['appointment_date'])
        ->where('appointment_time', $data['appointment_time'])
        ->whereIn('status', ['scheduled', 'confirmed', 'pending'])
        ->doesntExist();

    Log::info('Disponibility Check:', ['is_available' => $isAvailable, 'doctor_id' => $data['doctor_id']]);

    if (!$isAvailable) {
        Log::warning('Appointment time slot not available.', ['doctor_id' => $data['doctor_id'], 'date' => $data['appointment_date'], 'time' => $data['appointment_time']]);
        throw ValidationException::withMessages([
            'appointment_time' => 'Ce créneau horaire est déjà pris ou en attente pour ce docteur.'
        ]);
    }

    // --- Création du rendez-vous ---
    try {
        $appointment = Appointment::create($data);
        Log::info('APPOINTMENT CREATED SUCCESSFULLY.', ['appointment_id' => $appointment->id]);
    } catch (\Exception $e) {
        Log::error('DB INSERTION FAILED:', ['error' => $e->getMessage(), 'data' => $data]);
        return response()->json(['message' => 'Erreur interne: Échec de l\'enregistrement du rendez-vous.'], 500);
    }

    // --- Notifications et Événement ---
    // ... (votre code de notifications ici)

    Log::info('AppointmentController@store - Final Response Sent.', ['appointment_id' => $appointment->id]);
    return response()->json(['appointment' => $appointment], 201);
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
