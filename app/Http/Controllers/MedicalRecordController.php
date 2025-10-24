<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\User;
use App\Models\MedicalRecord;
use App\Http\Requests\MedicalRecordRequest;
use App\Http\Resrouces\UserResource;
use App\Http\Resrouces\MedicalRecordResource;
use App\Models\Doctor;

class MedicalRecordController extends Controller
{


    public function create(MedicalRecordRequest $request,Patient $patient)
    {
        $record=$request->validated();
        $record['doctor_id']=auth()->user()->id;
        $record['patient_id']=$patient->id;

        $record =MedicalRecord::create($record);
        return response()->json([
            'Message' => 'Dossier cree avec succes',
            'record'=>$record,
        ]);
    }
    public function update(MedicalRecordRequest $request, MedicalRecord $medicalRecord) // Ajout de JsonResponse
    {
        // 1. Récupérer les données validées du formulaire.
        $validatedData = $request->validated();

        // 2. Mettre à jour l'instance du dossier médical.
        // La méthode update() retourne true/false. L'instance $medicalRecord elle-même est modifiée.
        $medicalRecord->update($validatedData);

        // 3. Recharger le modèle pour s'assurer que toutes les relations et attributs
        // (y compris ceux qui pourraient être modifiés par des mutators ou des événements) sont à jour.
        // C'est facultatif mais recommandé si d'autres logiques peuvent affecter le modèle après la simple mise à jour.
        // Sinon, $medicalRecord est déjà à jour avec les attributs $validatedData.
        $medicalRecord->fresh();

        // 4. Retourner une réponse JSON avec le message de succès et le dossier mis à jour.
        return response()->json([
            'message' => 'Dossier médical mis à jour avec succès', // Message plus précis
            'record' => $medicalRecord, // Retourne l'instance du modèle mise à jour
        ], 200); // Code HTTP 200 OK pour une mise à jour réussie
    }
    public function findrecordAssocietedPatient(int $patient_id)
{
    // Utiliser find() est plus idiomatique pour la clé primaire
    // Charger la relation 'user' directement avec 'with()'
    $patient = Patient::with('user')->find($patient_id);

    // Vérifier si le patient existe
    if (!$patient) {
        return response()->json([
            'message' => 'Patient non trouvé.'
        ], 404);
    }

    // Vérifier si une relation 'user' existe pour ce patient
    // La relation 'user' est une 'belongsTo' (comme corrigé précédemment),
    // donc elle peut être null si le user_id n'est pas défini ou l'utilisateur n'existe plus.
    if (!$patient->user) {
        return response()->json([
            'message' => 'Aucun utilisateur associé à ce patient.'
        ], 404);
    }

    return response()->json($patient);
}
   public function findrecordAssocietedDoctor(int $doctor_id)
{
    // Charger la relation 'user' directement avec 'with()'
    $doctor = Doctor::with('user')->find($doctor_id);

    // Vérifier si le docteur existe
    if (!$doctor) {
        return response()->json([
            'message' => 'Médecin non trouvé.'
        ], 404);
    }

    // Optionnel : Vérifier si une relation 'user' existe pour ce docteur
    // Si la relation 'user' est une 'belongsTo' sur le modèle Doctor
    // elle peut être null si le user_id n'est pas défini ou l'utilisateur n'existe plus.
    if (!$doctor->user) {
        return response()->json([
            'message' => 'Aucun utilisateur associé à ce médecin.'
        ], 404);
    }

    return response()->json($doctor);
}
    public function showrecord(MedicalRecord $record){

        $record=$record->load(['patient','allergies','consultationHistorys']);
        return ($record);
    }
    public function getAllMedicalrecord()
    {
        // 1. Récupérer tous les dossiers médicaux avec leurs relations patient.user et doctor.user
        $medicalRecords = MedicalRecord::with(['patient.user', 'doctor.user'])->paginate(30);

        // 2. Transformer la collection pour ajouter les noms
        $medicalRecords->getCollection()->transform(function ($record) {
            // Ajouter le nom complet du patient
            $record->patient_name = 'Patient inconnu';
            if ($record->patient && $record->patient->user) {
                $record->patient_name = $record->patient->user->first_name . ' ' . $record->patient->user->last_name;
            }

            // Ajouter le nom complet du médecin
            $record->doctor_name = 'Médecin inconnu';
            if ($record->doctor && $record->doctor->user) {
                $record->doctor_name = $record->doctor->user->first_name . ' ' . $record->doctor->user->last_name;
            }

            // Optionnel: Pour ne pas envoyer toutes les données du patient/médecin/user si elles ne sont pas nécessaires
            // unset($record->patient);
            // unset($record->doctor);

            return $record;
        });

        // 3. Retourner la collection modifiée
        return response()->json($medicalRecords);
    }

    public function deleteMedicalrecord(MedicalRecord $record){
        $record->delete();
    }
    public function medicalrecordSearch(Request $request){
        $patients = User::applyFilters($request, ['first_name', 'last_name', 'email','telephone_urgence'])->paginate(10);
          $patient=$patient->load(['appointments','medicalrecords','analyses','allergies','analysesrequests','vaccinations','invoices']);
            return ($patients);
    }
}

