<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Importation des contrôleurs et actions Fortify
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Actions\Fortify\UpdateUserPassword;
use App\Http\Controllers\Auth\AuthRegisteredUserController; // Assurez-vous que c'est bien votre contrôleur d'auth personnalisé
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\PasswordResetLinkController;
use Laravel\Fortify\Http\Controllers\NewPasswordController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HospitalController;
// Importation de tous vos contrôleurs d'application
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\EmergencyPhysiciansController;
use App\Http\Controllers\LabController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\MedicalReportController;
use App\Http\Controllers\NurseController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PatientDataController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SosController;
use App\Http\Controllers\SystemSettingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\MortaliterController;
use App\Http\Controllers\NaissanceController;
use App\Http\Controllers\LaboratoryController;
use App\Http\Controllers\LabTechnicianController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\BloodUnitController;
use App\Http\Controllers\DonorController;
use App\Http\Controllers\AllergyController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BedsController;
use App\Http\Controllers\AnalyseRequestController;
use App\Http\Controllers\ReportController;



/*
|--------------------------------------------------------------------------
| Routes API
|--------------------------------------------------------------------------
|
| Ici, vous pouvez enregistrer les routes API pour votre application. Ces
| routes sont chargées par le RouteServiceProvider à l'intérieur d'un groupe
| qui a le middleware "api" et un préfixe d'espace de noms.
|
*/

// =========================================================================
// 1. ROUTES D'AUTHENTIFICATION & GESTION DE COMPTE (Non protégées ou protégées par 'auth:sanctum')
// =========================================================================

// Enregistrement et connexion
Route::post('/register', [AuthRegisteredUserController::class, 'store']);
Route::post('/login', [AuthRegisteredUserController::class, 'login']);

// Réinitialisation de mot de passe (Fortify)
Route::post('/forgot-password', [PasswordResetLinkController::class, 'store']);
Route::post('/reset-password', [NewPasswordController::class, 'store']);


// Routes protégées par 'auth:sanctum'
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']); // Déconnexion

    // Gestion du profil utilisateur (Fortify)
    Route::put('/user/profile-information', [UpdateUserProfileInformation::class, '__invoke']);
    Route::put('/user/password', [UpdateUserPassword::class, '__invoke']);
    Route::post('/profile/photo', [UserController::class, 'updatePhoto']);
     Route::get('/admin/roles', [UserController::class, 'getAvailableRoles']);

    // Gestion des notifications de l'utilisateur connecté
    Route::get('/user/notifications', [UserController::class, 'notifications']);
    Route::get('/admin/users/search', [UserController::class, 'searchUsers']);
    Route::get('/users/all', [UserController::class, 'getAllUsers']);
    Route::delete('/admin/users/{user}', [UserController::class, 'destroy']);
    Route::post('/user/notifications/{id}/mark-as-read', [UserController::class, 'markAsRead']);
    Route::post('/user/notifications/mark-all-as-read', [UserController::class, 'markAllAsRead']);
    Route::get('/user', [UserController::class, 'fetchCurrentUser'])->name('user.current');
    Route::post('/report/export', [ReportController::class, 'exportNurseReport']);


    // Gestion des utilisateurs et des rôles (peut nécessiter des middlewares de rôle supplémentaires)
    Route::delete('/user/{user}/delete', [UserController::class, 'delete']);
    Route::get('/role', [RoleController::class, 'listRole']);
    Route::put('/admin/users/{user}/role', [UserController::class, 'updateUserRole']);
    Route::post('/user/{user}/role/{roleName}/assign', [RoleController::class, 'addRoleToUser']);
    Route::post('/user/{user}/role/{roleName}/remove', [RoleController::class, 'removeRoleFromUser']);

    // =========================================================================
    // 2. ROUTES PUBLIQUES (accessibles sans authentification)
    // =========================================================================
    // Attention: le groupe 'public' est ici dans le middleware 'auth:sanctum'.
    // Si ces routes doivent être *réellement* publiques, elles devraient être déplacées
    // en dehors de ce bloc 'middleware('auth:sanctum')'.
    // J'ai mis votre définition originale.
    Route::group(['prefix' => 'public'], function () {
        Route::get('/departments', [DepartmentController::class, 'index']); // La liste des départements
        Route::get('public-settings', [SystemSettingController::class, 'publicSettings']); // Paramètres publics du système
        // Route::post('/doctors/register', [DoctorController::class, 'store']); // Exemple de route publique pour l'inscription docteur
    });


    // =========================================================================
    // 3. ROUTES POUR L'ADMINISTRATEUR (protégées par 'auth:sanctum' et 'admin')
    // =========================================================================
    Route::middleware(['admin'])->group(function () {
    });
        // Gestion des rendez-vous (Admin a tous les accès)
        Route::apiResource('appointments', AppointmentController::class);

        // Gestion des sauvegardes
        Route::apiResource('backups', BackupController::class);
        Route::post('backups/trigger', [BackupController::class, 'triggerBackup']); // Déclenchement manuel

        // Gestion des paramètres système (autres que ceux publics)
        Route::apiResource('system-settings', SystemSettingController::class);

        // Gestion des départements (toutes opérations sauf l'index public)
        Route::apiResource('departments', DepartmentController::class)->except(['index']);


        // Routes de gestion des utilisateurs par l'Admin (plus détaillées)
        Route::prefix('admin')->group(function () {
            Route::get('users', [AdminController::class, 'listAllUsers']); // Lister tous les utilisateurs
            // Tableau de bord de supervision
            Route::get('dashboard/summary', [AdminController::class, 'getDashboardSummary']);
            Route::get('dashboard/alerts', [AdminController::class, 'getAlerts']);
            Route::get('dashboard/appointments', [AdminController::class, 'getAppointmentsOverview']);

            // Exemples de routes pour la gestion des utilisateurs et des centres de santé (décommenter si besoin)
            // Route::get('users/{userId}', [AdminController::class, 'getUserProfile']);
            // Route::put('users/{userId}', [AdminController::class, 'updateUserProfile']);
            // Route::delete('users/{userId}', [AdminController::class, 'deleteUser']);
            // Route::post('users', [AdminController::class, 'createUser']);

            // Route::get('health-centers', [AdminController::class, 'listHealthCenters']);
            // Route::post('health-centers', [AdminController::class, 'createHealthCenter']);
            // Route::put('health-centers/{id}', [AdminController::class, 'updateHealthCenter']);
        });


    // =========================================================================
    // 4. ROUTES POUR L'URGENTISTE (`EmergencyPhysicians`)
    // =========================================================================
     Route::post('/sos', [SosController::class, 'store']);

    Route::put('/sos/{id}/update-location', [SOSController::class, 'updateLocation']);
   Route::prefix('urgentist')->controller(EmergencyPhysiciansController::class)->group(function () {
    // 🚨 Gestion des alertes SOS géolocalisées
    Route::get('alerts/active', 'getActiveSosAlerts'); // Tableau de bord en temps réel >
    Route::get('alerts/{alertId}', 'getSosAlertDetails'); // Détails d'une alerte spécifique
    Route::put('alerts/{alertId}/take-charge', 'takeChargeOfAlert'); // Marquer comme pris en charge
    Route::put('alerts/{alertId}/resolve', 'resolveAlert'); // Marquer comme résolue
    Route::post('alerts/{alertId}/message-patient', 'sendMessaEmergencyPhysiciansnt'); // Envoyer un message au patient

    // 👨‍⚕️ CRUD et recherche pour les urgentistes
    Route::post('register', 'createEmergencyPhysician'); // Crée / met à jour un urgentiste (⚠️ vérifie le nom de la méthode)
    Route::get('search', 'emergencySearch'); // Recherche d'urgentistes
    Route::get('/', 'getAllEmergencyPhysicians'); // Lister tous les urgentistes
    Route::get('{emergency}', 'getEmergencyPhysicians'); // Afficher un urgentiste spécifique
    Route::delete('{emergency}', 'deleteEmergencyPhysicians'); // Supprimer un urgentiste
    Route::put('{emergency}', 'updateEmergencyPhysicians'); // Mettre à jour un urgentiste
});


    // =========================================================================
    // 5. ROUTES POUR LES PATIENTS
    // =========================================================================

    // Routes génériques pour la gestion des profils patients (CRUD et recherche)
    Route::prefix('/patients')->controller(PatientController::class)->group(function(){
        Route::post('/', 'create'); // Créer un patient
        Route::get('/search', 'search'); // Pour la recherche
        Route::get('/', 'getAllPatient'); // Lister tous les patients
        Route::get('/search', 'patientSearch'); // Rechercher un patient
        Route::get('/{patient}', 'getPatient'); // Afficher un patient spécifique
        Route::put('/{patient}', 'update'); // Mettre à jour un patient
        Route::delete('/{patient}', 'deletePatient'); // Supprimer un patient
        Route::get('/profile/patient', [PatientController::class, 'getPatientProfile']);
        Route::get('/by-user/{userId}', [PatientController::class, 'showByUserId']);
    });
    // Routes CRUD pour les chambres
    Route::apiResource('rooms', RoomController::class);
    Route::apiResource('beds', BedsController::class);
    // Routes spécifiques au contexte d'un patient donné
    Route::prefix('patient/{patientId}')->group(function () {
        // Gestion des rendez-vous (du point de vue du patient)
        Route::post('appointments', [AppointmentController::class, 'store']); // Créer un rendez-vous
        Route::get('appointments', [AppointmentController::class, 'index']); // Lister ses rendez-vous
        Route::put('appointments/{appointmentId}', [AppointmentController::class, 'update']); // Mettre à jour un rendez-vous

        // Accès aux données médicales du patient
        Route::get('medical-history', [PatientDataController::class, 'getMedicalHistory']);
        Route::get('prescriptions', [PatientDataController::class, 'getPrescriptions']);
        Route::get('notifications', [PatientDataController::class, 'getNotifications']);

        // Envoi d'alerte SOS par le patient


        // Routes pour le système de messagerie (chat)

    });
    Route::prefix('chat')->group(function () {
                Route::get('/conversations', [ChatController::class, 'indexConversations']); // Lister les conversations du patient
                Route::post('/conversations', [ChatController::class, 'createOrGetPrivateConversation']); // Créer/obtenir un chat
                Route::get('/conversations/{conversation}/messages', [ChatController::class, 'showConversationMessages']); // Messages d'une conversation
                Route::post('/conversations/{conversation}/messages', [ChatController::class, 'sendMessage']); // Envoyer un message
            });

    // =========================================================================
    // 6. ROUTES POUR LES DOCTEURS
    // =========================================================================

    // Routes génériques pour la gestion des profils docteurs (CRUD et recherche)
    Route::prefix('/doctors')->controller(DoctorController::class)->group(function(){
        Route::get('/search', 'doctorSearch'); // Rechercher un docteur
        Route::post('/register', 'register'); // Enregistrer un nouveau docteur (peut être admin-only ou public)
        Route::get('/', 'getAllDoctor'); // Lister tous les docteurs
        Route::get('/{doctor}', 'getDoctor'); // Afficher un docteur spécifique
        Route::delete('/{doctor}', 'deleteDoctor'); // Supprimer un docteur
        Route::put('/{doctor}', 'updateDoctor'); // Mettre à jour un docteur
        Route::get('/by-user/{user}', [DoctorController::class, 'showByUser']);

    });

    // Routes spécifiques au contexte d'un docteur donné
    Route::prefix('doctor/{doctorId}')->group(function () {
        // Gestion des patients et de leurs dossiers
        Route::get('patients', [DoctorController::class, 'listPatients']); // Lister les patients du docteur
        Route::get('patients/{patientId}/dme', [DoctorController::class, 'getPatientDme']); // Accéder au dossier médical d'un patient

        // Gestion des consultations
        Route::post('patients/{patientId}/consultations/start', [ConsultationController::class, 'startConsultation']); // Démarrer une consultation
        Route::put('consultations/{consultationId}/end', [ConsultationController::class, 'endConsultation']); // Terminer une consultation
        Route::put('consultations/{consultationId}/dme', [DoctorController::class, 'updatePatientDme']); // Mettre à jour le DME durant la consultation
        // Autres fonctionnalités
        // Route::post('patients/{patientId}/prescriptions', [DoctorController::class, 'issuePrescription']); // Émettre une prescription
        Route::get('appointments', [DoctorController::class, 'getAppointments']); // Lister les rendez-vous du docteur
        Route::put('appointments/{appointmentId}/status', [AppointmentController::class, 'updateStatusByDoctor']);

        // Route::get('reports', [DoctorController::class, 'getReports']); // Rapports (décommenter si besoin)
      });
      Route::get('patients/{patient}/consultations', [ConsultationController::class, 'indexByPatient']);


    // --- POUR LES PATIENTS ---
    // Les patients consultent leurs propres ordonnances.
    Route::post('doctors/{doctorId}/patients/{patientId}/prescriptions', [PrescriptionController::class, 'store']);
    // GET /api/patients/{patientId}/prescriptions
    Route::get('patients/{patientId}/prescriptions', [PrescriptionController::class, 'index']);
    // GET /api/patients/{patientId}/prescriptions/{prescriptionId}
    Route::get('patients/{patientId}/prescriptions/{prescriptionId}', [PrescriptionController::class, 'show']);

    // --- POUR LA MODIFICATION/SUPPRESSION (par les docteurs ou admins) ---
    // PATCH/PUT /api/prescriptions/{prescriptionId}
    Route::patch('prescriptions/{prescriptionId}', [PrescriptionController::class, 'update']);
    Route::delete('prescriptions/{prescriptionId}', [PrescriptionController::class, 'destroy']);
    // Route::apiResource('analyse-requests', AnalyseRequestController::class);
     Route::apiResource('naissances', NaissanceController::class);
     Route::apiResource('mortalites', MortaliterController::class);
     Route::apiResource('regions', RegionController::class);
     Route::apiResource('hospitals', HospitalController::class);
     Route::apiResource('laboratories', LaboratoryController::class);
     Route::apiResource('labtechnicians', LabTechnicianController::class);
     Route::apiResource('languages', LanguageController::class);
     Route::apiResource('statistiquesregionales', StatisticController::class);
     Route::apiResource('bloodunits', BloodUnitController::class);
     Route::apiResource('donors', DonorController::class);
     Route::apiResource('allergies', AllergyController::class);





    // 7. ROUTES POUR LES INFIRMIERS (`Nurse`)
    // =========================================================================

    // Routes génériques pour la gestion des profils infirmiers (CRUD et recherche)
    Route::prefix('/nurse')->controller(NurseController::class)->group(function(){
        Route::post('/register', 'create'); // Créer un nouvel infirmier
        Route::get('/', 'index'); // Lister tous les infirmiers
        Route::get('/search', 'search'); // Rechercher un infirmier
        Route::get('/{nurse}', 'show'); // Afficher un infirmier spécifique
        Route::delete('/{nurse}', 'destroy'); // Supprimer un infirmier
        Route::put('/{nurse}', 'update'); // Mettre à jour un infirmier
        Route::get('/user/{user}', [NurseController::class, 'getProfileIdByUserId']);

        // Routes spécifiques à un infirmier donné (utilisant le Route Model Binding {nurse})
        Route::get('/{nurse}/patients/{patientId}/dme', 'getPatientDme'); // Accéder au dossier d'un patient
        Route::post('/{nurse}/patients/{patientId}/dme/vitalsigns', 'recordVitalSigns'); // Saisir les signes vitaux
        Route::post('/{nurse}/activities-report', 'createActivitiesReport'); // Créer un rapport d'activités
        Route::get('/{nurse}/blood-bank', 'getBloodBankOverview'); // Accéder au module de gestion du sang

        // Optionnel: Gérer les réserves de sang (décommenter si besoin)
        // Route::post('/{nurse}/blood-bank/units', 'addBloodUnit');
        // Route::delete('/{nurse}/blood-bank/units/{unitId}', 'removeBloodUnit');
    });


    // =========================================================================
    // 8. ROUTES POUR LE LABORATOIRE (`LabTechnician`)
    // =========================================================================
    // Ces routes sont généralement protégées par un middleware de rôle 'lab_tech'
    Route::prefix('lab')->group(function () {
        Route::post('analyses', [LabController::class, 'createAnalyseRequest']);
        // Gestion des demandes d'analyses
        Route::get('requests', [LabController::class, 'listLabRequests']); // Lister les demandes
        Route::get('requests/{requestId}', [LabController::class, 'getLabRequest']); // Détails d'une demande
        Route::put('requests/{requestId}/status', [LabController::class, 'updateLabRequestStatus']); // Mettre à jour le statut
        // Enregistrer les résultats des examens
        Route::post('requests/{requestId}/results', [LabController::class, 'uploadLabResults']); // Attacher les résultats
    });




    // =========================================================================
    // 10. ROUTES POUR LES RAPPORTS MÉDICAUX (`MedicalReport`)
    // =========================================================================
    Route::prefix('/medicalreports')->controller(MedicalReportController::class)->group(function(){
        Route::post('/register/{patient}', 'create'); // Créer un rapport médical pour un patient
        Route::get('/', 'getAllMedicalReport'); // Lister tous les rapports médicaux
        Route::get('/search', 'medicalreportSearch'); // Rechercher un rapport médical
        Route::get('/{report}', 'showreport'); // Afficher un rapport médical spécifique
        Route::delete('/{report}', 'deleteMedicalReport'); // Supprimer un rapport médical
        Route::put('/{report}', 'update'); // Mettre à jour un rapport médical
        Route::get('/{report}/download', 'downloadReport'); // Télécharger un rapport spécifique

    });
 Route::prefix('/doctors/{doctorId}/patients/{patientId}/medical-reports')->controller(MedicalReportController::class)->group(function () {
        Route::get('/', 'indexByDoctorAndPatient'); // Récupérer les rapports pour un docteur et patient spécifique
        Route::post('/', 'storeByDoctorAndPatient'); // Créer un rapport pour un docteur et patient spécifique
    });
     // =========================================================================
        // =========================================================================
        // 9. ROUTES POUR LES DOSSIERS MÉDICAUX (`MedicalRecord`)
        // =========================================================================
        Route::prefix('medicalrecord')->controller(MedicalRecordController::class)->group(function(){
            Route::post('register/{patient}', 'create'); // Créer un dossier médical pour un patient
            Route::put('/update/{record}', 'update'); // Mettre à jour un dossier médical
            Route::get('/', 'getAllMedicalrecord'); // Lister tous les dossiers médicaux
            Route::get('/{record}', 'showrecord'); // Afficher un dossier médical spécifique
            Route::delete('/{medicalrecord}', 'deleteMedicalrecord'); // Supprimer un dossier médical
            Route::get('/medicalrecord/search', 'medicalrecordSearch');
            Route::get('/patient/{patientId}/medicalrecord', 'findrecordAssocietedPatient'); // Rechercher un dossier médical
            Route::get('/doctor/{doctorId}/medicalrecord', 'findrecordAssocietedDoctor'); // Rechercher un dossier médical

        });

}); // Fin du groupe middleware('auth:sanctum')
Route::get('/', function () {
    return 'Bienvenue sur l’API Santeko';
});
