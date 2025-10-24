<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Doctor;
use App\Models\User;
use App\Http\Resources\DoctorResource;
use App\Http\Resources\UserResource;
use App\Http\Requests\DoctorUpdateRequest;
use App\Http\Resources\DoctorCollection;

// Pour le médecin
use App\Models\Patient;
use App\Models\ConsultationHistory;
use App\Models\Prescription;
use App\Models\PrescriptionLine;
use App\Models\Appointment;
use App\Models\MedicalReport;
use App\Models\Consultation;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\PatientCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StorePrescriptionRequest;
use App\Http\Requests\UpdateDmeRequest;
use App\Http\Requests\DoctorRequest;
use App\Models\Analyse;
use App\Models\AnalyseRequest;
use Illuminate\Support\Facades\Auth;

class DoctorController extends Controller
{
    /**
     * Lister les docteurs (version paginée, accessible aux admins et patients).
     * GET /api/doctors
     */
    public function index(Request $request): JsonResponse
    {
        if (!auth()->check() || (auth()->user()->hasRole('admin') !== 'admin' && auth()->user()->hasRole('patient')!== 'patient')) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        $perPage = $request->query('per_page', 15);

        $doctors = Doctor::with('user', 'department')
            ->filter($request)
            ->paginate($perPage);

        return response()->json($doctors);
    }
 /**
     * Affiche le profil du docteur associé à un utilisateur donné.
     *
     * @param  \App\Models\User  $user L'instance de l'utilisateur.
     * @return \Illuminate\Http\JsonResponse
     */
    public function showByUser(int  $id_user)
    {
        $user = User::findOrFail($id_user);
        // Charge la relation 'doctor' pour l'utilisateur donné
        // On utilise first() car un User a une relation hasOne avec Doctor
        $doctor = $user->doctor()->first();

        // Si aucun profil de docteur n'est trouvé pour cet utilisateur
        if (!$doctor) {
            return response()->json(['message' => 'Profil médecin non trouvé pour cet utilisateur.'], 404);
        }

        // Optionnel : Si vous souhaitez inclure les détails de l'utilisateur
        // directement dans l'objet docteur renvoyé, vous pouvez charger la relation 'user' du docteur.
        // Cela est utile si le frontend a besoin des informations de l'utilisateur via l'objet docteur.
        // $doctor->load('user');

        return response()->json(['doctor' => $doctor]);
    }
    // --- Méthodes utilitaires privées ---

   private function findDoctor(Doctor $doctor): Doctor|JsonResponse
{
    // Vérifie que le docteur existe et que son utilisateur a bien le rôle "doctor"
    if (!$doctor || ($doctor->user && !$doctor->user->hasRole('doctor'))) {
        return response()->json([
            'message' => 'Médecin non trouvé ou rôle incorrect.'
        ], 404);
    }

    return $doctor;
}


    private function findPatient(Patient $patientId): Patient|JsonResponse
    {
        $patient = Patient::find($patientId);
        if (!$patient) {
            return response()->json(['message' => 'Patient non trouvé.'], 404);
        }
        return $patient;
    }

    // --- Méthodes spécifiques au médecin ---

    /**
     * Lister les patients suivis par un médecin.
     * GET /api/doctor/{id}/patients
     */
   public function listPatients(Doctor $doctorId): JsonResponse
{
    $doctor = $this->findDoctor($doctorId);

    if ($doctor instanceof JsonResponse) {
        return $doctor;
    }

    $patients = Patient::whereHas('consultations', function ($query) use ($doctor) {
        $query->where('doctor_id', $doctor->id);
    })
    ->with('user')
    ->get();

    return response()->json($patients);
}

    /**
     * Consulter le DME d’un patient.
     * GET /api/doctor/{id}/patients/{patient_id}/dme
     */
    public function getPatientDme(Doctor $doctorId, Patient $patientId): JsonResponse
    {
        $doctor = $this->findDoctor($doctorId);
        if ($doctor instanceof JsonResponse) {
            return $doctor;
        }

        $patient = $this->findPatient($patientId);
        if ($patient instanceof JsonResponse) {
            return $patient;
        }

        $hasAccess = Consultation::where('patient_id', $patient->id)
            ->where('doctor_id', $doctor->id)
            ->exists();

        if (!$hasAccess) {
            return response()->json(['message' => 'Accès au DME non autorisé.'.$hasAccess], 403);
        }

        $dme = ConsultationHistory::where('patient_id', $patient->id)
            ->with(['consultations' => function ($query) use ($doctor) {
                $query->where('doctor_id', $doctor->id);
            }])
            ->orderBy('date_consultation', 'desc')
            ->get();

        $dme = $dme->filter(fn($entry) => $entry->consultation !== null)->values();

        return response()->json($dme);
    }

    /**
     * Mettre à jour le DME d’un patient.
     * PUT /api/doctor/{id}/consultations/{consultation_id}/dme
     */
    public function updatePatientDme(UpdateDmeRequest $request, Doctor $doctorId, int $consultationId): JsonResponse
    {
        $doctor = $this->findDoctor($doctorId);
        if ($doctor instanceof JsonResponse) {
            return $doctor;
        }

        $consultation = Consultation::where('id', $consultationId)
            ->where('doctor_id', $doctor->id)
            ->first();

        if (!$consultation) {
            return response()->json(['message' => 'Consultation non trouvée ou non autorisée.'], 404);
        }

        if ($consultation->status !== 'in_progress') {
            return response()->json(['message' => 'La consultation doit être en cours pour ajouter un DME.'], 400);
        }

        $user = $doctor->user;

        $historicalEntry = ConsultationHistory::create([
            'consultation_id' => $consultation->id,
            'patient_id' => $consultation->patient_id,
            'doctor_id' => $doctor->id,
            'department_id' => $doctor->department_id,
            'date_consultation' => $request->date_consultation,
            'type' => $request->type,
            'motif' => $request->motif,
            'diagnostic' => $request->diagnostic,
            'traitement' => $request->traitement,
            'notes' => $request->notes,
            'last_updated_by' => $user ? ($user->first_name . ' ' . $user->last_name) : 'Inconnu',
        ]);

        return response()->json(['message' => 'Entrée DME ajoutée avec succès.', 'data' => $historicalEntry], 201);
    }

    /**
     * Émettre une ordonnance.
     * POST /api/doctor/{id}/patients/{patient_id}/prescriptions
     */
    public function issuePrescription(StorePrescriptionRequest $request, Doctor $doctorId, Patient $patientId): JsonResponse
    {
        $doctor = $this->findDoctor($doctorId);
        if ($doctor instanceof JsonResponse) {
            return $doctor;
        }

        $patient = $this->findPatient($patientId);
        if ($patient instanceof JsonResponse) {
            return $patient;
        }

        $hasAccess = Consultation::where('patient_id', $patient->id)
            ->where('doctor_id', $doctor->id)
            ->whereIn('status', ['completed', 'in_progress'])
            ->exists();

        if (!$hasAccess) {
            return response()->json(['message' => 'Accès refusé pour émettre une ordonnance.'], 403);
        }

        DB::beginTransaction();
        try {
            $prescription = Prescription::create([
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'date_prescription' => $request->date_prescription,
                'notes' => $request->notes,
                'status' => 'active',
                'consultation_id' => $request->consultation_id,
            ]);

            foreach ($request->lines as $lineData) {
                $prescription->lines()->create([
                    'prescription_id' => $prescription->id,
                    'dosage' => $lineData['dosage'],
                    'frequency' => $lineData['frequency'],
                    'duration' => $lineData['duration'],
                    'instructions' => $lineData['instructions'],
                    'medication_name' => $lineData['medication_name'] ?? null,
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Ordonnance émise avec succès.', 'data' => $prescription->load('lines')], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur lors de l'émission de l'ordonnance: " . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de l\'émission de l\'ordonnance.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Lister les rendez-vous du médecin.
     * GET /api/doctor/{id}/appointments
     */
     /**
     * Récupère tous les rendez-vous du docteur connecté.
     */
    /**
     * Récupère tous les rendez-vous pour le docteur dont l'ID est dans l'URL.
     * * @param int $doctorId L'ID du profil Docteur passé dans l'URL
     */
    public function getAppointments(int $doctorId): JsonResponse
    {
        // 1. Récupération de l'utilisateur authentifié pour la sécurité
        $user = auth()->user();

        // 2. Recherche du profil Docteur par l'ID de la route
        $doctor = Doctor::findOrFail($doctorId);

        // 3. Vérification de la propriété et de l'existence
        if (!$doctor) {
            return response()->json(['message' => 'Profil de docteur non trouvé.'], 404);
        }

        // // 4. Sécurité : S'assurer que le docteur demandé est bien le docteur connecté
        // if (!$user || $doctor->user_id !== $user->id) {
        //     return response()->json(['message' => 'Accès refusé. Vous ne pouvez pas consulter les rendez-vous d\'un autre docteur.'], 403);
        // }

        // 5. Récupération des rendez-vous
        $appointments = $doctor->appointments()
            ->with('patient.user') // Pour obtenir les noms des patients
            ->orderBy('appointment_date', 'asc')
            ->orderBy('appointment_time', 'asc')
            ->get();

        return response()->json($appointments);
    }
    // --- Méthodes CRUD pour l’admin ---

    /**
     * Lister tous les docteurs (admin).
     * GET /api/doctors
     */
    public function getAllDoctor()
{
    $doctors = Doctor::with(['user', 'department', 'consultations'])->paginate(15);
    return $doctors; // ça renvoie directement la pagination avec data + meta
}


    /**
     * Supprimer un docteur.
     * DELETE /api/doctors/{doctor}
     */
    public function deleteDoctor(Doctor $doctor): JsonResponse
    {
        $user = $doctor->user;

        if ($doctor->delete()) {
            if ($user) {
                $user->delete();
            }
            return response()->json(['message' => 'Médecin et utilisateur supprimés avec succès.'], 200);
        }
        return response()->json(['message' => 'Impossible de supprimer le médecin.'], 500);
    }

    /**
     * Mettre à jour un docteur.
     * PUT /api/doctors/{doctor}
     */


/**
 * Enregistre un nouveau docteur.
 *
 * @param  \App\Http\Requests\DoctorRequest  $request
 * @return \Illuminate\Http\JsonResponse
 */
public function register(DoctorRequest $request): JsonResponse
{
    $data = $request->validated();

    DB::beginTransaction();

    try {
        // 1️⃣ Créer l'utilisateur en premier
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'birth_date' => $data['birth_date'] ?? null,
            'phone' => $data['phone'] ?? null,
            'city' => $data['city'] ?? null,
            'address' => $data['address'] ?? null,
            'profile_photo' => $data['profile_photo'] ?? null,
            'role' => 'doctor',
        ]);

        // 2️⃣ Créer le docteur et associer l'user_id
        $doctor = Doctor::create([
            'user_id' => $user->id, // <-- essentiel
            'department_id' => $data['department_id'] ?? null,
            'speciality' => $data['speciality'],
            'numero_ordre' => $data['numero_ordre'],
            'biography' => $data['biography'] ?? null,
            'experience' => $data['experience'] ?? null,
            'status' => $data['status'] ?? 'active',
            'numero_professionel' => $data['numero_professionel'],
        ]);

        // 3️⃣ Mettre à jour le User avec le doctor_id si tu veux garder cette relation
        $user->update(['doctor_id' => $doctor->id]);

        DB::commit();

        // Charger les relations pour la réponse JSON
        $doctor->load(['user', 'department', 'teleconsultations', 'consultations']);

        return response()->json([
            'message' => 'Médecin enregistré avec succès.',

        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error("Erreur lors de l'enregistrement du docteur : " . $e->getMessage());

        return response()->json([
            'message' => "Échec de l'enregistrement du docteur.",
            'error' => $e->getMessage()
        ], 500);
    }
}


 public function updateDoctor(DoctorUpdateRequest $request, $doctorId): JsonResponse
{
    $data = $request->validated();

    // On récupère le doctor manuellement par ID
    $doctor = Doctor::find($doctorId);

    if (!$doctor) {
        return response()->json(['message' => "Médecin introuvable."], 404);
    }

    // On récupère ou crée l'utilisateur associé
    $user = $doctor->user;
    if (!$user) {
        // Crée un utilisateur vide avec le doctor_id
        $user = User::create([
            'doctor_id' => $doctor->id,
            'first_name' => $data['first_name'] ?? 'Inconnu',
            'last_name' => $data['last_name'] ?? 'Inconnu',
            'email' => $data['email'] ?? "doctor{$doctor->id}@example.com",
            'password' => bcrypt($data['password'] ?? 'password'),
            'birth_date' => $data['birth_date'] ?? null,
            'phone' => $data['phone'] ?? null,
            'city' => $data['city'] ?? null,
            'address' => $data['address'] ?? null,
            'profile_photo' => $data['profile_photo'] ?? null,
            'role' => 'doctor',
        ]);
    } else {
        // Met à jour l'utilisateur existant
        $user->update([
            'first_name' => $data['first_name'] ?? $user->first_name,
            'last_name' => $data['last_name'] ?? $user->last_name,
            'birth_date' => $data['birth_date'] ?? $user->birth_date,
            'phone' => $data['phone'] ?? $user->phone,
            'city' => $data['city'] ?? $user->city,
            'address' => $data['address'] ?? $user->address,
            'profile_photo' => $data['profile_photo'] ?? $user->profile_photo,
            'email' => $data['email'] ?? $user->email,
            'password' => $request->filled('password') ? bcrypt($data['password']) : $user->password,
        ]);
    }

    // Met à jour les champs du doctor
    $doctor->update([
        'department_id' => $data['department_id'] ?? $doctor->department_id,
        'speciality' => $data['speciality'] ?? $doctor->speciality,
        'numero_ordre' => $data['numero_ordre'] ?? $doctor->numero_ordre,
        'biography' => $data['biography'] ?? $doctor->biography,
        'experience' => $data['experience'] ?? $doctor->experience,
        'status' => $data['status'] ?? $doctor->status,
        'numero_professionel' => $data['numero_professionel'] ?? $doctor->numero_professionel,
    ]);

    // On met à jour la relation user_id dans doctor si elle était null
    if (!$doctor->user_id) {
        $doctor->update(['user_id' => $user->id]);
    }

    return response()->json([
        'message' => 'Médecin mis à jour avec succès.',
        'doctor' => $doctor,
        'user' => $user
    ]);
}



    /**
     * Créer un nouveau docteur.
     * POST /api/doctors/register
     */
    // public function create(DoctorUpdateRequest $request): JsonResponse
    // {
    //     $data = $request->validated();

    //     DB::beginTransaction();
    //     try {
    //         $user = User::create([
    //             'first_name' => $data['first_name'],
    //             'last_name' => $data['last_name'],
    //             'birth_date' => $data['birth_date'],
    //             'phone' => $data['phone'],
    //             'country' => $data['country'] ?? 'Mali',
    //             'city' => $data['city'],
    //             'profile_photo' => $data['profile_photo'] ?? null,
    //             'address' => $data['address'],
    //             'email' => $data['email'],
    //             'password' => bcrypt($data['password']),
    //             'role' => 'doctor',
    //         ]);

    //         $doctor = Doctor::create([
    //             'user_id' => $user->id,
    //             'department_id' => $data['department_id'],
    //             'speciality' => $data['speciality'],
    //             'numero_ordre' => $data['numero_ordre'],
    //             'biography' => $data['biography'],
    //             'experience' => $data['experience'],
    //             'status' => $data['status'],
    //             'numero_professionel' => $data['numero_professionel'],
    //         ]);

    //         DB::commit();



    //         return response()->json([
    //             'message' => 'Médecin créé avec succès.',
    //            // 'doctor' => new DoctorResource($doctor),
    //         ], 201);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error("Erreur lors de la création du médecin: " . $e->getMessage(), ['exception' => $e]);
    //         return response()->json(['message' => 'Erreur lors de la création du médecin.', 'error' => $e->getMessage()], 500);
    //     }
    // }

    /**
     * Rechercher des médecins.
     * GET /api/doctors/search
     */
public function doctorSearch(Request $request)
{
    $doctors = Doctor::filter($request)
        ->with(['user', 'department', 'teleconsultations', 'consultations'])
        ->paginate(10);

    return $doctors; // renvoie un JSON brut
}


    public function getDoctor(Doctor $doctor)
{
    $doctor->load(['user', 'department', 'consultations']);
    return $doctor;
}

}
