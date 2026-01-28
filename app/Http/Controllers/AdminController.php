<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Patient;
use App\Mail\MailForUser;
use App\Models\Appointment;
use Illuminate\Support\Str;
use App\Models\Consultation;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use App\Services\BrevoMailer;

// Form Requests pour l'administration (vous devrez les créer)
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\Admin\CreateUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\SOSAlert; // Supposons que vous ayez un modèle pour les alertes SOS

class AdminController extends Controller
{
public function sendMail(Request $request)
{
    // 1. Validation des données entrantes
    $request->validate([
        'recipient_ids'   => 'nullable|array',
        'recipient_ids.*' => 'exists:users,id',
        'type'            => 'required|string|max:255',
        'subject'         => 'required|string|max:255',
        'message'         => 'required|string',
        'send_to_all'     => 'required|boolean',
    ]);

    // 2. Sélection des destinataires
    $users = $request->send_to_all
        ? User::all()
        : User::whereIn('id', $request->recipient_ids)->get();

    if ($users->isEmpty()) {
        return response()->json(['status' => 'error', 'message' => 'Aucun destinataire sélectionné.'], 400);
    }

    // 3. Boucle d'envoi et de notification
    foreach ($users as $user) {
        
        // Données à transmettre à votre classe Mailable (MailForUser)
        $details = [
            'type'    => $request->type,
            'subject' => $request->subject,
            'message' => $request->message,
        ];

        try {
            // Envoi via Gmail (ou le driver configuré dans le .env)
            // On utilise la classe MailForUser déjà importée dans votre contrôleur
            \Mail::to($user->email)->send(new \App\Mail\MailForUser($details));

            // 4. Sauvegarde de la notification en base de données seulement si l'envoi réussit
            Notification::create([
                'id'              => (string) \Str::uuid(),
                'type'            => $request->type,
                'notifiable_type' => 'User',
                'notifiable_id'   => $user->id,
                'data'            => json_encode([
                    'subject' => $request->subject,
                    'message' => $request->message,
                    'type'    => $request->type,
                ]),
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

        } catch (\Exception $e) {
            // Optionnel : Loguer l'erreur si un mail échoue pour ne pas bloquer toute la boucle
            \Log::error("Échec d'envoi de mail à {$user->email} : " . $e->getMessage());
        }
    }

    return response()->json([
        'status'  => 'success',
        'message' => count($users) . ' emails traités et notifications enregistrées.'
    ]);
}

    // Méthode d'autorisation simplifiée. Dans un vrai projet, utilisez des Policies.
    private function authorizeAdmin(): JsonResponse|null
    {
        if (auth()->user() && auth()->user()->hasRole('admin')) {
            return null;
        }
        return response()->json(['message' => 'Accès non autorisé. Seuls les administrateurs sont permis.'], 403);
    }

    /**
     * Lister tous les utilisateurs (admins, doctors, patients, nurses, accountants, etc.).
     * GET /api/admin/users
     */
    public function listAllUsers(): JsonResponse
    {
        if ($response = $this->authorizeAdmin()) return $response;

        $users = User::orderBy('created_at', 'desc')->get();
        return response()->json($users);
    }

    /**
     * Récupérer le profil d'un utilisateur spécifique.
     * GET /api/admin/users/{userId}
     */
    public function getUserProfile(int $userId): JsonResponse
    {
        if ($response = $this->authorizeAdmin()) return $response;

        $user = User::find($userId);
        if (!$user) {
            return response()->json(['message' => 'Utilisateur non trouvé.'], 404);
        }
        return response()->json($user);
    }

    /**
     * Créer un nouvel utilisateur (admin, doctor, patient, nurse, accountant).
     * POST /api/admin/users
     */
   /* public function createUser(CreateUserRequest $request): JsonResponse
    {
        if ($response = $this->authorizeAdmin()) return $response;

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role, 
        ]);

        // Si le rôle est 'patient', créer aussi une entrée dans la table 'patients'
        if ($request->role === 'patient') {
            Patient::create(['user_id' => $user->id]);
        }
        // Si le rôle est 'doctor', créer aussi une entrée dans la table 'doctors'
        else if ($request->role === 'doctor') {
            // Créer un profil Doctor vide ou avec des valeurs par défaut
            // Cela dépend de votre modèle Doctor et ses fillable
            // \App\Models\Doctor::create(['user_id' => $user->id]);
        }
        // Répéter pour d'autres rôles comme 'nurse', 'accountant', etc. si vous avez des tables de profil spécifiques

        return response()->json(['message' => 'Utilisateur créé avec succès.', 'user' => $user], 201);
    }*/

    /**
     * Mettre à jour le profil d'un utilisateur.
     * PUT /api/admin/users/{userId}
     */
    /*
    public function updateUserProfile(UpdateUserRequest $request, int $userId): JsonResponse
    {
        if ($response = $this->authorizeAdmin()) return $response;

        $user = User::find($userId);
        if (!$user) {
            return response()->json(['message' => 'Utilisateur non trouvé.'], 404);
        }

        $user->name = $request->name ?? $user->name;
        $user->email = $request->email ?? $user->email;
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        $user->role = $request->role ?? $user->role;
        $user->save();

        return response()->json(['message' => 'Profil utilisateur mis à jour avec succès.', 'user' => $user], 200);
    }
*/
    /**
     * Supprimer un utilisateur.
     * DELETE /api/admin/users/{userId}
     */
    /*
    public function deleteUser(int $userId): JsonResponse
    {
        if ($response = $this->authorizeAdmin()) return $response;

        $user = User::find($userId);
        if (!$user) {
            return response()->json(['message' => 'Utilisateur non trouvé.'], 404);
        }

        $user->delete();
        return response()->json(['message' => 'Utilisateur supprimé avec succès.'], 200);
    }
*/
    /**
     * Obtenir un résumé pour le tableau de bord (nombre d'utilisateurs, consultations, etc.).
     * GET /api/admin/dashboard/summary
     */
    public function getDashboardSummary(): JsonResponse
    {
        if ($response = $this->authorizeAdmin()) return $response;

        $totalUsers = User::count();
        $totalPatients = Patient::count();
        $totalDoctors = User::where('role_id', )->count();
        $totalConsultations = Consultation::count();
        $pendingAppointments = Appointment::where('status', 'pending')->count();
        $completedConsultationsLastMonth = Consultation::where('status', 'completed')
                                                     ->count();

        return response()->json([
            'total_users' => $totalUsers,
            'total_patients' => $totalPatients,
            'total_doctors' => $totalDoctors,
            'total_consultations' => $totalConsultations,
            'pending_appointments' => $pendingAppointments,
            'completed_consultations_last_month' => $completedConsultationsLastMonth,
        ]);
    }

    /**
     * Obtenir toutes les alertes SOS.
     * GET /api/admin/dashboard/alerts
     */
    public function getAlerts(): JsonResponse
    {
        if ($response = $this->authorizeAdmin()) return $response;

        // Supposons que vous ayez un modèle SosAlert pour gérer les alertes SOS
        // avec des champs comme 'patient_id', 'location', 'status', 'created_at'
        $alerts = SOSAlert::with('patient.user') // Charge le patient et l'utilisateur lié
                          ->orderBy('created_at', 'desc')
                          ->get();

        return response()->json($alerts);
    }

    /**
     * Vue d'ensemble des rendez-vous.
     * GET /api/admin/dashboard/appointments
     */
    public function getAppointmentsOverview(): JsonResponse
    {
        if ($response = $this->authorizeAdmin()) return $response;

        $appointments = Appointment::with(['patient.user', 'doctor'])
                                   ->orderBy('appointment_date', 'desc')
                                   ->orderBy('appointment_time', 'desc')
                                   ->get();

        return response()->json($appointments);
    }
}
