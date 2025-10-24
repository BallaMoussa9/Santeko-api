<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nurse;
use App\Http\Requests\NurseStoreRequest;
use App\Http\Requests\NurseUpdateRequest;
use App\Models\User;
use App\Models\ConsultationHistory;
use Illuminate\Http\JsonResponse;
use App\Models\Patient;
use App\Models\VitalSign;
use App\Models\BloodUnit;
use App\Models\NurseActivityReport;
use App\Http\Requests\RecordVitalSignsRequest;
use App\Http\Requests\CreateActivitiesReportRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class NurseController extends Controller
{
    /**
     * Liste tous les infirmiers.
     * GET /api/nurse
     */
    public function index(Request $request)
    {
        if (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('doctor')) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        $perPage = $request->query('per_page', 15);

        $nurses = Nurse::with('user', 'department')
                       ->filter($request)
                       ->paginate($perPage);

        return response()->json($nurses);
    }

    /**
     * Affiche les détails d'un infirmier.
     * GET /api/nurse/{nurse}
     */
    public function show(Nurse $nurse)
    {
        if (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('doctor') && auth()->user()->id !== $nurse->user_id) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        $nurse->load(['user', 'department']);
        return $nurse;
    }

    /**
     * Crée un infirmier et son utilisateur associé.
     * POST /api/nurse/register
     */
    public function create(NurseStoreRequest $request): JsonResponse
    {
        $data = $request->validated();

        DB::beginTransaction();

        try {
            $profilePhotoPath = null;
            if ($request->hasFile('profile_photo')) {
                $profilePhotoPath = $request->file('profile_photo')->store('profile_photos', 'public');
            }

            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'birth_date' => $data['birth_date'],
                'phone' => $data['phone'],
                'city' => $data['city'],
                'address' => $data['address'],
                'profile_photo' => $profilePhotoPath,
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'country' => $data['country'] ?? null,
                'role' => 'nurse',
            ]);

            $nurse = Nurse::create([
                'user_id' => $user->id,
                'department_id' => $data['department_id'] ?? null,
            ]);
            $user->update(['nurse_id' => $nurse->id]);

            DB::commit();

            $nurse->load(['user', 'department']);

            return response()->json([
                'message' => '✅ Infirmier créé avec succès.',
                'nurse' => $nurse,
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();

            if ($profilePhotoPath && Storage::disk('public')->exists($profilePhotoPath)) {
                Storage::disk('public')->delete($profilePhotoPath);
            }

            Log::error('Erreur création infirmier : ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'message' => '❌ Erreur lors de la création de l\'infirmier.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Met à jour un infirmier.
     * PUT /api/nurse/{nurse}
     */
    public function update(NurseUpdateRequest $request, Nurse $nurse): JsonResponse
    {
        DB::beginTransaction();

        try {
            $user = $nurse->user;
            if (!$user) {
                // Si l'utilisateur associé n'existe pas, c'est une erreur critique
                return response()->json(['message' => 'Utilisateur associé introuvable.'], 404);
            }

            // Préparer les données de l'utilisateur pour la mise à jour
            $userData = $request->only([
                'first_name', 'last_name', 'birth_date', 'phone',
                'city', 'address', 'email', 'country'
            ]);

            // Logique de gestion de la photo de profil
            if ($request->hasFile('profile_photo')) {
                // Supprimer l'ancienne photo si elle existe
                if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                    Storage::disk('public')->delete($user->profile_photo);
                }
                // Stocker la nouvelle photo
                $userData['profile_photo'] = $request->file('profile_photo')->store('profile_photos', 'public');
            } elseif ($request->has('profile_photo') && is_null($request->input('profile_photo'))) {
                // Gère le cas où la photo est explicitement supprimée du formulaire (champ envoyé comme null)
                if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                    Storage::disk('public')->delete($user->profile_photo);
                }
                $userData['profile_photo'] = null; // Définir la photo de profil sur null
            }
            // Si profile_photo n'est pas fourni dans la requête et n'est pas explicitement null,
            // alors le champ ne sera pas modifié dans la base de données.

            // Mettre à jour le mot de passe si un nouveau est fourni dans la requête
            if ($request->filled('password')) {
                $userData['password'] = bcrypt($request->password);
            }

            // Effectuer une seule mise à jour sur le modèle utilisateur
            $user->update($userData);

            // Mettre à jour les données spécifiques à l'infirmier
            $nurse->update([
                // Utiliser $request->department_id directement car il est validé par NurseUpdateRequest
                'department_id' => $request->department_id,
                'speciality' => $request->speciality,
            ]);

            DB::commit();

            // Recharger les relations user et department pour la réponse
            $nurse->load(['user', 'department']);

            return response()->json([
                'message' => '✅ Infirmier mis à jour avec succès.',
                'nurse' => $nurse,
            ]);

        } catch (\Throwable $e) {
            DB::rollBack(); // Annuler la transaction en cas d'erreur
            Log::error('❌ Erreur mise à jour infirmier : ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'message' => '❌ Erreur lors de la mise à jour de l\'infirmier.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Supprime un infirmier.
     * DELETE /api/nurse/{nurse}
     */
    public function destroy(Nurse $nurse): JsonResponse
    {
        if (!auth()->user()->hasRole('admin')) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        DB::beginTransaction();

        try {
            $user = $nurse->user;

            if ($user && $user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            $nurse->delete();
            if ($user) {
                $user->delete();
            }

            DB::commit();

            return response()->json(['message' => '✅ Infirmier et utilisateur supprimés avec succès.']);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('❌ Erreur suppression infirmier : ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => '❌ Erreur lors de la suppression de l\'infirmier.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Recherche d'infirmiers.
     * GET /api/nurse/search
     */
    public function search(Request $request): JsonResponse
    {
        if (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('doctor')) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        $nurses = Nurse::with('user', 'department')
                       ->filter($request)
                       ->paginate(10);

        return response()->json($nurses);
    }

    // --------------------------------------------------------------------------------
    // Méthodes spécifiques aux actions infirmier
    // --------------------------------------------------------------------------------

    private function authorizeNurse(int $nurseId): ?JsonResponse
    {
        $user = auth()->user();

        if (!$user || $user->id !== $nurseId || !$user->hasRole('nurse')) {
            return response()->json([
                'message' => 'Accès non autorisé. Seuls les infirmiers concernés sont permis.'
            ], 403);
        }

        return null;
    }

    private function findPatient(int $patientId): Patient|JsonResponse
    {
        $patient = Patient::find($patientId);

        if (!$patient) {
            return response()->json(['message' => 'Patient non trouvé.'], 404);
        }

        return $patient;
    }

    public function getPatientDme(Nurse $nurse, int $patient_id): JsonResponse
    {
        if ($response = $this->authorizeNurse($nurse->user_id)) {
            return $response;
        }

        $patient = $this->findPatient($patient_id);
        if ($patient instanceof JsonResponse) {
            return $patient;
        }

        $dme = ConsultationHistory::where('patient_id', $patient->id)
                                  ->orderBy('date_consultation', 'desc')
                                  ->get();

        $vitalSigns = VitalSign::where('patient_id', $patient->id)
                               ->orderBy('recorded_at', 'desc')
                               ->get();

        return response()->json([
            'patient_info' => $patient->load('user'),
            'medical_history' => $dme,
            'vital_signs_history' => $vitalSigns,
        ]);
    }

        public function recordVitalSigns(RecordVitalSignsRequest $request, int $nurse, int $patient_id): JsonResponse
    {
        // 1. Récupération de l'infirmier(e)
        $nurse = Nurse::findOrFail($nurse);

        // 🔑 MODIFICATION : Ajouter un log pour afficher les informations sur l'infirmier(e)
        // Nous journalisons les informations essentielles du Nurse trouvé, en utilisant le tableau de l'objet.
        Log::info('Tentative d\'enregistrement des signes vitaux.', [
            'nurse_id_route' => $nurse,
            'nurse_data' => $nurse->toArray(),
            'patient_id' => $patient_id,
        ]);

        if ($response = $this->authorizeNurse($nurse->user_id)) return $response;

        // 2. Récupération du patient
        $patient = $this->findPatient($patient_id);
        if ($patient instanceof JsonResponse) {
            return $patient;
        }

        // 3. Création de l'enregistrement des signes vitaux
        $vitalSign = VitalSign::create([
            'patient_id' => $patient->id,
            'nurse_id' => $nurse->id,
            'blood_pressure_systolic' => $request->blood_pressure_systolic,
            'blood_pressure_diastolic' => $request->blood_pressure_diastolic,
            'heart_rate' => $request->heart_rate,
            'temperature' => $request->temperature,
            'respiratory_rate' => $request->respiratory_rate,
            'oxygen_saturation' => $request->oxygen_saturation,
            'weight' => $request->weight,
            'height' => $request->height,
            'recorded_at' => now(),
            'notes' => $request->notes,
        ]);

        // 4. Log de succès (optionnel)
        Log::info('Signes vitaux enregistrés avec succès.', [
            'nurse_id' => $nurse->id,
            'patient_id' => $patient->id,
            'vital_sign_id' => $vitalSign->id
        ]);

        return response()->json(['message' => 'Signes vitaux enregistrés.', 'vital_sign' => $vitalSign], 201);
    }


    public function createActivitiesReport(CreateActivitiesReportRequest $request, Nurse $nurse): JsonResponse
    {
        //if ($response = $this->authorizeNurse($nurse->user_id)) return $response;

        $report = NurseActivityReport::create([
            'nurse_id' => $nurse->id,
            'report_date' => $request->report_date,
            'title' => $request->title,
            'content' => $request->content,
            'patient_id' => $request->patient_id,
        ]);

        return response()->json(['message' => 'Rapport créé avec succès.', 'report' => $report], 201);
    }

    public function getBloodBankOverview(Nurse $nurse): JsonResponse
    {
        if ($response = $this->authorizeNurse($nurse->user_id)) return $response;

        $bloodUnits = BloodUnit::orderBy('expiration_date', 'asc')->get();

        $bloodSummary = BloodUnit::select('blood_group', 'rh_factor', \DB::raw('count(*) as total_units'))
                                 ->groupBy('blood_group', 'rh_factor')
                                 ->get();

        return response()->json([
            'available_blood_units' => $bloodUnits,
            'blood_bank_summary' => $bloodSummary,
        ]);
    }
    public function getProfileIdByUserId(User $user): JsonResponse
    {
        // 🔑 Assurez-vous que la relation 'nurse' est définie sur votre modèle User
        $nurse = $user->nurse;

        if (!$nurse) {
            // Le code 404 est critique car le frontend doit savoir que le profil n'existe pas
            return response()->json([
                'message' => 'Profil infirmier non trouvé pour cet utilisateur.'
            ], 404);
        }

        // 🔑 POINT CLÉ : Renvoyer l'ID de la table 'nurses' (nurseId)
        return response()->json([
            'id' => $nurse->id,
            'user_id' => $nurse->user_id
        ]);
    }
}
