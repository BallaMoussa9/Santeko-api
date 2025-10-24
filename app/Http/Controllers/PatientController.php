<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\PatientCollection;
use App\Http\Resources\PatientResource;
use App\Http\Resources\UserResource;
use App\Models\Patient;
use App\Http\Requests\PatientRequest;
use App\Models\User;
use App\Models\MedicalRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PatientUpdateRequest;
use Illuminate\Validation\Rule; // S'assurer que Rule est importé si utilisé dans d'autres méthodes comme BedController

class PatientController extends Controller
{

    /**
     * Récupère tous les patients avec les relations nécessaires pour la liste,
     * gérant la recherche, le tri et la pagination.
     * Cette méthode remplace l'ancienne `getAllPatient` et est renommée `index`
     * pour suivre les conventions RESTful.
     */
   public function getAllPatient(Request $request): JsonResponse
{
    // --- L'ajout crucial est ici : 'nurseActivityReports' ---
    // Nous demandons à Laravel de charger ces relations pour chaque patient.
    $query = Patient::with([
        'user',
        'bed.room.department',
        'nurseActivityReports' // <-- AJOUT DE LA RELATION DES RAPPORTS
    ]);

    // --- 1. Gestion de la Recherche (searchTerm) ---
    if ($request->has('search') && $request->search != '') {
        $searchTerm = $request->search;
        $query->where(function ($q) use ($searchTerm) {
            // Recherche sur l'ID du patient
            $q->where('patients.id', 'like', "%{$searchTerm}%")
              // Recherche sur le statut du patient
              ->orWhere('patients.status', 'like', "%{$searchTerm}%")
              // Recherche sur les champs de l'utilisateur associé
              ->orWhereHas('user', function ($qr) use ($searchTerm) {
                  $qr->where('first_name', 'like', "%{$searchTerm}%")
                     ->orWhere('last_name', 'like', "%{$searchTerm}%")
                     ->orWhere('email', 'like', "%{$searchTerm}%")
                     ->orWhere('phone', 'like', "%{$searchTerm}%");
              })
              // Recherche sur le nom du département
              ->orWhereHas('bed.room.department', function ($qr) use ($searchTerm) {
                  $qr->where('name', 'like', "%{$searchTerm}%");
              })
              // Recherche sur le numéro de chambre
              ->orWhereHas('bed.room', function ($qr) use ($searchTerm) {
                  $qr->where('room_number', 'like', "%{$searchTerm}%");
              })
              // Recherche sur le numéro de lit
              ->orWhereHas('bed', function ($qr) use ($searchTerm) {
                  $qr->where('bed_number', 'like', "%{$searchTerm}%");
              });
            // 🚨 OPTIONNEL : Vous pourriez aussi ajouter une recherche sur les rapports ici
            // ->orWhereHas('nurseActivityReports', function ($qr) use ($searchTerm) {
            //      $qr->where('title', 'like', "%{$searchTerm}%");
            // });
        });
    }

    // --- 2. Gestion du Tri (le reste de votre logique de tri complexe) ---
    // 'user.last_name' par défaut pour un tri alphabétique par nom
    $sortBy = $request->input('sort_by', 'user.last_name');
    $sortDirection = $request->input('sort_direction', 'asc'); // 'asc' ou 'desc'

    // Votre logique de tri est conservée et exécutée ici...
    switch ($sortBy) {
        case 'user.last_name':
        case 'user.birth_date':
            $query->join('users', 'patients.user_id', '=', 'users.id')
                  ->orderBy("users." . explode('.', $sortBy)[1], $sortDirection)
                  ->select('patients.*'); // Sélectionner les colonnes de patients pour éviter l'ambiguïté
            break;
        case 'bed.room.room_number':
        case 'bed.room.department.name':
            $query->leftJoin('beds', 'patients.bed_id', '=', 'beds.id')
                  ->leftJoin('rooms', 'beds.room_id', '=', 'rooms.id');

            if ($sortBy === 'bed.room.department.name') {
                $query->leftJoin('departments', 'rooms.department_id', '=', 'departments.id')
                      ->orderBy('departments.name', $sortDirection);
            } else { // 'bed.room.room_number'
                $query->orderBy('rooms.room_number', $sortDirection);
            }
            $query->select('patients.*'); // Sélectionner les colonnes de patients pour éviter l'ambiguïté
            break;
        default:
            // Pour les champs simples directement sur la table 'patients'
            // S'assurer que la colonne existe directement sur la table 'patients'
            $query->orderBy('patients.' . $sortBy, $sortDirection);
            break;
    }

    // --- 3. Pagination ---
    // Utilisez la méthode simple de pagination de Laravel
    $patients = $query->paginate(10);

    // Laravel paginate retourne un objet avec 'data' (les patients), 'total', 'current_page', 'last_page', etc.
    return response()->json($patients);
}

    /**
     * Récupère un Patient en utilisant son user_id.
     */
    public function showByUserId(int $userId): JsonResponse
    {
        // 1. Trouver l'utilisateur et charger la relation 'patient'
        $user = User::with(['patient' => function ($query) {
             // 🔑 ESSENTIEL : Charger les relations de localisation directement sur le patient
            $query->with(['bed.room.department']);
        }])->find($userId);

        if (!$user) {
            return response()->json(['message' => "Utilisateur ID: {$userId} non trouvé."], 404);
        }

        $patientData = $user->patient;

        if (!$patientData) {
            return response()->json([
                'message' => "Dossier patient non trouvé pour l'utilisateur ID: {$userId}."
            ], 404);
        }

        // On s'assure que l'objet patient retourné contient les données de l'utilisateur
        // pour simplifier l'accès côté frontend (Patient.user.email, etc.)
        $responsePatient = $patientData->toArray();
        $responsePatient['user'] = $user->toArray();

        return response()->json([
            'message' => "Profil patient chargé avec succès via ID utilisateur.",
            'patient' => $responsePatient,
        ], 200);
    }

    /**
     * Récupère un patient spécifique (pour la vue détail).
     * Cette méthode utilise le Route Model Binding (Patient $patient).
     */
    public function getPatient(Patient $patient): JsonResponse
    {
        try {
            // ✅ MISE À JOUR : Ajout de 'latestVitalSign' aux relations chargées !
            $patient = $patient->load([
                'user',
                'bed.room.department', // Localisation complète
                'latestVitalSign', // <--- NOUVEAU : Le dernier signe vital
                'appointments',
                'medicalRecord',
                'analyses',
                'allergies',
                'vaccinations',
                'invoices',
                'consultationHistories',
                'consultations',
                'sosalerts',
                'vitalsigns', // Si vous voulez l'historique complet (mais attention à la taille)
                'nurseActivityReports',
                'deaths',
                'births',
                'medicalReports',
            ]);

            // Renomme la clé de relation 'latest_vital_sign' en 'latest_vitals' pour le frontend
            $patientArray = $patient->toArray();
            if (isset($patientArray['latest_vital_sign'])) {
                $patientArray['latest_vitals'] = $patientArray['latest_vital_sign'];
                unset($patientArray['latest_vital_sign']);
            } else {
                 $patientArray['latest_vitals'] = null;
            }

            return response()->json($patientArray);

        } catch (\Exception $e) {
            \Log::error('Erreur en chargeant le patient : ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Erreur lors du chargement du patient',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deletePatient(Patient $patient): JsonResponse
    {
        if (!$patient) {
            return response()->json(['message'=>'Patient not found'],404);
        }

        $patient->delete();
        return response()->json(['message'=>'Patient supprimé avec succès']);
    }

   /**
     * Met à jour les informations d'un patient et de son dossier médical.
     * Crée le dossier médical si celui-ci n'existe pas.
     *
     * @param PatientUpdateRequest $request
     * @param Patient $patient
     * @return JsonResponse
     */
    public function update(PatientUpdateRequest $request, Patient $patient): JsonResponse
    {
        Log::info('Requête update patient - all()', $request->all());

        try {
            $validatedData = $request->validated();
            Log::info('Requête update patient - validated()', $validatedData);

            DB::beginTransaction();

            $user = $patient->user;
            if (!$user) {
                return response()->json(['message' => 'Utilisateur associé introuvable.'], 404);
            }

            // --- 1️⃣ MISE À JOUR DE L'UTILISATEUR (USER) ---
            $userUpdateData = [];
            if (isset($validatedData['first_name'])) $userUpdateData['first_name'] = $validatedData['first_name'];
            if (isset($validatedData['last_name'])) $userUpdateData['last_name'] = $validatedData['last_name'];
            if (isset($validatedData['birth_date'])) $userUpdateData['birth_date'] = $validatedData['birth_date'];
            if (isset($validatedData['phone'])) $userUpdateData['phone'] = $validatedData['phone'];
            if (isset($validatedData['city'])) $userUpdateData['city'] = $validatedData['city'];
            if (isset($validatedData['address'])) $userUpdateData['address'] = $validatedData['address'];
            if (isset($validatedData['email'])) $userUpdateData['email'] = $validatedData['email'];
            if (isset($validatedData['country'])) $userUpdateData['country'] = $validatedData['country'];
            if (isset($validatedData['doctor_id'])) $userUpdateData['doctor_id'] = $validatedData['doctor_id'];

            $profilePhotoPath = $user->profile_photo;
            if ($request->hasFile('profile_photo')) {
                if ($profilePhotoPath && Storage::disk('public')->exists($profilePhotoPath)) {
                    Storage::disk('public')->delete($profilePhotoPath);
                }
                $profilePhotoPath = $request->file('profile_photo')->store('profile_photos', 'public');
                $userUpdateData['profile_photo'] = $profilePhotoPath;
            } elseif ($request->has('profile_photo') && $request->file('profile_photo') === null) {
                if ($profilePhotoPath && Storage::disk('public')->exists($profilePhotoPath)) {
                    Storage::disk('public')->delete($profilePhotoPath);
                }
                $userUpdateData['profile_photo'] = null;
            }

            if (!empty($userUpdateData)) {
                $user->update($userUpdateData);
            }

            if (isset($validatedData['password']) && !empty($validatedData['password'])) {
                $user->password = bcrypt($validatedData['password']);
                $user->save();
            }

            // --- 2️⃣ CRÉATION/MISE À JOUR DU DOSSIER MÉDICAL (MEDICAL RECORD) ---
            $medicalRecord = $patient->medicalRecord;
            $isNewRecord = false;
            if (!$medicalRecord) {
                $isNewRecord = true;
            }

            $numeroDossier = $validatedData['numero_dossier'] ?? ($medicalRecord->numero_dossier ?? null);
            if ($isNewRecord && is_null($numeroDossier)) {
                $numeroDossier = 'MR-' . str_pad($patient->id, 6, '0', STR_PAD_LEFT);
                Log::info("Génération d'un nouveau numero_dossier pour le patient {$patient->id}: {$numeroDossier}");
            }

            $medicalRecordData = [];
            $currentChronicConditions = $medicalRecord->chronic_conditions ?? null;
            $currentDoctorId = $medicalRecord->doctor_id ?? null;
            $currentHospitalId = $medicalRecord->hospital_id ?? null;

            $medicalRecordData['chronic_conditions'] = $validatedData['maladies_chroniques'] ?? $currentChronicConditions;
            $medicalRecordData['doctor_id'] = $validatedData['doctor_id'] ?? $currentDoctorId;
            $medicalRecordData['hospital_id'] = $validatedData['hospital_id'] ?? $currentHospitalId;
            $medicalRecordData['numero_dossier'] = $numeroDossier;

            if (!$isNewRecord && $medicalRecord) {
                $mrUpdateData = array_filter($medicalRecordData, function($value, $key) use ($validatedData, $medicalRecord) {
                     if (array_key_exists($key, $validatedData) || $key === 'numero_dossier') {
                         return true;
                     }
                     return false;
                }, ARRAY_FILTER_USE_BOTH);
            } else {
                 $mrUpdateData = $medicalRecordData;
            }

            if ($isNewRecord) {
                $medicalRecord = $patient->medicalRecord()->create($medicalRecordData);
                Log::info("Nouveau dossier médical créé pour le patient {$patient->id}, ID MR: " . $medicalRecord->id);
            } elseif (!empty($mrUpdateData)) {
                $medicalRecord->update($mrUpdateData);
            }

            // --- 3️⃣ MISE À JOUR DU PATIENT (PATIENT) ---
            $patientUpdateData = [];
            if (isset($validatedData['genre'])) $patientUpdateData['genre'] = $validatedData['genre'];
            if (isset($validatedData['group_sanguine'])) $patientUpdateData['group_sanguine'] = $validatedData['group_sanguine'];
            if (isset($validatedData['telephone_urgence'])) $patientUpdateData['telephone_urgence'] = $validatedData['telephone_urgence'];
            if (isset($validatedData['maladies_chroniques'])) $patientUpdateData['maladies_chroniques'] = $validatedData['maladies_chroniques'];
            if (isset($validatedData['assurance_maladie'])) $patientUpdateData['assurance_maladie'] = $validatedData['assurance_maladie'];
            if (isset($validatedData['numero_urgence'])) $patientUpdateData['numero_urgence'] = $validatedData['numero_urgence'];
            if (isset($validatedData['poids'])) $patientUpdateData['poids'] = $validatedData['poids'];
            if (isset($validatedData['taille'])) $patientUpdateData['taille'] = $validatedData['taille'];
            if (isset($validatedData['status'])) $patientUpdateData['status'] = $validatedData['status'];
            if (isset($validatedData['last_consultation_date'])) $patientUpdateData['last_consultation_date'] = $validatedData['last_consultation_date'];
            if (isset($validatedData['bed_id'])) $patientUpdateData['bed_id'] = $validatedData['bed_id'];
            if (isset($validatedData['doctor_id'])) $patientUpdateData['doctor_id'] = $validatedData['doctor_id'];

            if (isset($medicalRecord)) {
                $patientUpdateData['medical_record_id'] = $medicalRecord->id;
            }

            if (!empty($patientUpdateData)) {
                 $patient->update($patientUpdateData);
            }

            DB::commit();

            $patient->load(['user', 'bed.room.department', 'medicalRecord', 'appointments', 'analyses', 'allergies', 'analyseRequests', 'vaccinations', 'invoices']);

            return response()->json([
                'message' => '✅ Patient et son dossier médical mis à jour avec succès.',
                'patient' => $patient,
            ]);

        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Erreur de validation lors de la mise à jour du patient : ' . $e->getMessage(), [
                'validation_errors' => $e->errors(),
            ]);

            return response()->json([
                'message' => '❌ Erreur de validation lors de la mise à jour du patient.',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erreur inattendue lors de la mise à jour du patient : ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'message' => '❌ Erreur interne du serveur lors de la mise à jour du patient.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

 public function create(PatientRequest $request): JsonResponse
{
    $data = $request->validated();
    \Log::info('Patient create validated data', $data);

    DB::beginTransaction();

    try {
        // 1️⃣ Créer l'utilisateur
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'birth_date' => $data['birth_date'],
            'phone' => $data['phone'],
            'city' => $data['city'],
            'address' => $data['address'],
            'profile_photo' => $data['profile_photo'] ?? null,
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'country' => $data['country'] ?? null,
        ]);

        // 2️⃣ Créer le patient
        $patient = Patient::create([
            'user_id' => $user->id,
            'genre' => $data['genre'],
            'group_sanguine' => $data['group_sanguine'],
            'telephone_urgence' => $data['telephone_urgence'] ?? null,
            'maladies_chroniques' => $data['maladies_chroniques'] ?? null,
            'assurance_maladie' => $data['assurance_maladie'] ?? null,
            'numero_urgence' => $data['numero_urgence'] ?? null,
            'poids' => $data['poids'] ?? null,
            'taille' => $data['taille'] ?? null,
            'status' => $data['status'],
            'last_consultation_date' => $data['last_consultation_date'] ?? null,
            'bed_id' => $data['bed_id'] ?? null,
            'doctor_id' => $data['doctor_id'] ?? null,
        ]);

        // ⚡ Mettre à jour la relation inverse dans l'utilisateur
        $user->patient_id = $patient->id;
        $doctorId = $data['doctor_id'] ?? null;
        $user->doctor_id = $doctorId;
        $user->save();

        // 3️⃣ Créer le dossier médical associé
        $medicalRecord = MedicalRecord::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctorId,
            'chronic_conditions' => $data['maladies_chroniques'] ?? '',
            'numero_dossier' => 'MR-' . str_pad($patient->id, 6, '0', STR_PAD_LEFT),
            'hospital_id' => $data['hospital_id'] ?? null,
        ]);

        DB::commit();

        $patient->load(['user', 'bed.room.department', 'medicalRecord']);

        return response()->json([
            'message' => '✅ Patient créé avec succès avec son dossier médical et mis à jour.',
            'patient' => $patient,
        ], 201);

    } catch (\Throwable $e) {
        DB::rollBack();
        \Log::error('Erreur création patient : '.$e->getMessage());

        return response()->json([
            'message' => '❌ Erreur lors de la création du patient.',
            'error' => $e->getMessage(),
        ], 500);
    }
}


    /**
     * Cette méthode `patientSearch` est maintenue intacte comme demandé.
     * Si elle n'est pas appelée par une route, elle n'aura aucun impact.
     */
    public function patientSearch(Request $request)
    {
        // ✅ MISE À JOUR : Ajout de la relation 'bed.room.department' à la recherche
        $patients = Patient::with(['user', 'bed.room.department'])
            ->filter($request) // Cette méthode filter() n'est pas définie ici, elle devrait être sur le modèle Patient
            ->paginate(10);

        return $patients;
    }

    public function getPatientProfile(): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non authentifié.'], 401);
        }

        $userWithPatient = User::with(['patient' => function ($query) {
            $query->with(['bed.room.department']);
        }])->find($user->id);

        $patientData = $userWithPatient->patient;

        if (!$patientData) {
            return response()->json(['message' => 'Dossier patient non trouvé pour cet utilisateur.'], 404);
        }

        return response()->json([
            'message' => 'Profil patient chargé avec succès.',
            'patient' => $patientData
        ]);
    }
}
