<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Acountant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Acountant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Acountant query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAcountant {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $department_id
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAdmin {}
}

namespace App\Models{
/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminPrivilege newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminPrivilege newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminPrivilege query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAdminPrivilege {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $medical_record_id
 * @property int|null $patient_id
 * @property string|null $substance
 * @property string|null $reaction_decscription
 * @property string|null $serverity
 * @property string|null $recorded_by
 * @property string|null $status
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MedicalRecord> $medicalRecords
 * @property-read int|null $medical_records_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Allergies newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Allergies newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Allergies query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Allergies whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Allergies whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Allergies whereMedicalRecordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Allergies whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Allergies wherePatientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Allergies whereReactionDecscription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Allergies whereRecordedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Allergies whereServerity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Allergies whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Allergies whereSubstance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Allergies whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAllergies {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $analyses_request_id
 * @property int|null $laboratory_id
 * @property int|null $patient_id
 * @property int|null $consultation_id
 * @property int|null $labtechnicians_id
 * @property string|null $name
 * @property string|null $type
 * @property string|null $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AnalyseRequest|null $analyseRequests
 * @property-read \App\Models\Laboratory|null $laboratorys
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analyse newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analyse newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analyse query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analyse whereAnalysesRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analyse whereConsultationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analyse whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analyse whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analyse whereLaboratoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analyse whereLabtechniciansId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analyse whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analyse wherePatientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analyse whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analyse whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analyse whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAnalyse {}
}

namespace App\Models{
/**
 * 
 *
 * @property-read \App\Models\Analyse|null $analyses
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnalyseRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnalyseRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnalyseRequest query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAnalyseRequest {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $patient_id
 * @property int|null $doctor_id
 * @property string|null $appointment_date
 * @property string|null $appointment_time
 * @property string|null $type
 * @property string|null $modif
 * @property string|null $status
 * @property string|null $cancellation_reason
 * @property string|null $confirmed_at
 * @property string|null $completed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Patient> $patients
 * @property-read int|null $patients_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment whereAppointmentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment whereAppointmentTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment whereCancellationReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment whereConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment whereDoctorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment whereModif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment wherePatientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAppointment {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $hospital_id
 * @property int|null $patient_id
 * @property int|null $doctor_id
 * @property int|null $department_id
 * @property string|null $firstname
 * @property string|null $lastname
 * @property string|null $sexe
 * @property string|null $date_naissance
 * @property string|null $lieu_naissance
 * @property string|null $father_name
 * @property float|null $poids
 * @property float|null $taille
 * @property string|null $heure_naissance
 * @property string|null $statut
 * @property string|null $numero_acte_naissance
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $nurse_id
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Birth newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Birth newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Birth query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Birth whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Birth whereDateNaissance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Birth whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Birth whereDoctorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Birth whereFatherName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Birth whereFirstname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Birth whereHeureNaissance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Birth whereHospitalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Birth whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Birth whereLastname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Birth whereLieuNaissance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Birth whereNumeroActeNaissance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Birth whereNurseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Birth wherePatientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Birth wherePoids($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Birth whereSexe($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Birth whereStatut($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Birth whereTaille($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Birth whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperBirth {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $prescription_id
 * @property int|null $doctor_id
 * @property int|null $patient_id
 * @property string|null $date_prescription
 * @property string|null $type
 * @property string|null $motif
 * @property string|null $diagnostic
 * @property string|null $status
 * @property string|null $traitement
 * @property string|null $notes
 * @property string|null $observations
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Invoice|null $Invoice
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Prescription> $precriptions
 * @property-read int|null $precriptions_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consultation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consultation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consultation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consultation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consultation whereDatePrescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consultation whereDiagnostic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consultation whereDoctorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consultation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consultation whereMotif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consultation whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consultation whereObservations($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consultation wherePatientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consultation wherePrescriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consultation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consultation whereTraitement($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consultation whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consultation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperConsultation {}
}

namespace App\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Allergies> $allergies
 * @property-read int|null $allergies_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsultationHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsultationHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsultationHistory query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperConsultationHistory {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $patient_id
 * @property int|null $doctor_id
 * @property int|null $department_id
 * @property int|null $nurse_id
 * @property string|null $date_deces
 * @property string|null $lieu_deces
 * @property string|null $cause_deces
 * @property string|null $circonstances_deces
 * @property string|null $statut
 * @property string|null $numero_acte_deces
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Death newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Death newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Death query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Death whereCauseDeces($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Death whereCirconstancesDeces($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Death whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Death whereDateDeces($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Death whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Death whereDoctorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Death whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Death whereLieuDeces($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Death whereNumeroActeDeces($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Death whereNurseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Death wherePatientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Death whereStatut($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Death whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperDeath {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $admin_id
 * @property string|null $name
 * @property string|null $description
 * @property string|null $status
 * @property string|null $position
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperDepartment {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $department_id
 * @property string|null $speciality
 * @property string|null $numero_ordre
 * @property string|null $biography
 * @property int|null $experince
 * @property string|null $status
 * @property string|null $numero_professionel
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TeleConsultation> $teleconsutations
 * @property-read int|null $teleconsutations_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereBiography($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereExperince($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereNumeroOrdre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereNumeroProfessionel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereSpeciality($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperDoctor {}
}

namespace App\Models{
/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FirstResponder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FirstResponder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FirstResponder query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperFirstResponder {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string|null $nom
 * @property string|null $adresse
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $ville
 * @property string|null $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hospital newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hospital newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hospital query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hospital whereAdresse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hospital whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hospital whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hospital whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hospital whereNom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hospital wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hospital whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hospital whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hospital whereVille($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperHospital {}
}

namespace App\Models{
/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HospitalPatient newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HospitalPatient newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HospitalPatient query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperHospitalPatient {}
}

namespace App\Models{
/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Infirmier newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Infirmier newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Infirmier query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperInfirmier {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $hospital_id
 * @property int|null $consultation_id
 * @property int|null $payments_id
 * @property int|null $patient_id
 * @property int|null $user_id
 * @property string|null $amount
 * @property string|null $status
 * @property string|null $paid_date
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Payment|null $Payment
 * @property-read \App\Models\Consultation|null $constultation
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereConsultationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereHospitalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice wherePaidDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice wherePatientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice wherePaymentsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperInvoice {}
}

namespace App\Models{
/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabTechnician newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabTechnician newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabTechnician query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperLabTechnician {}
}

namespace App\Models{
/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laboratory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laboratory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laboratory query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperLaboratory {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $code
 * @property string|null $locale
 * @property string|null $direction
 * @property int|null $is_active
 * @property int|null $is_default
 * @property string|null $native_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereDirection($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereNativeName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperLanguage {}
}

namespace App\Models{
/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Medecin newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Medecin newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Medecin query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperMedecin {}
}

namespace App\Models{
/**
 * 
 *
 * @property-read \App\Models\Allergies|null $allergies
 * @property-read \App\Models\ConsultationHistory|null $consultationHistory
 * @property-read \App\Models\Patient|null $patient
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Vaccination> $vaccinations
 * @property-read int|null $vaccinations_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalRecord query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperMedicalRecord {}
}

namespace App\Models{
/**
 * 
 *
 * @property-read \App\Models\Patient|null $patient
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalReport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalReport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalReport query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperMedicalReport {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $admin_id
 * @property int|null $recever_id
 * @property string|null $title
 * @property string|null $content
 * @property string|null $status
 * @property string|null $priority
 * @property string|null $start_time
 * @property string|null $end_time
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereReceverId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperMessage {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $sender_id
 * @property int|null $receiver_id
 * @property string|null $type
 * @property string|null $message
 * @property string|null $title
 * @property int|null $seen
 * @property string|null $channel
 * @property string|null $reference_type
 * @property string|null $sent_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereChannel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereReceiverId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereReferenceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereSeen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereSenderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperNotification {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $department_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nurse newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nurse newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nurse query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nurse whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nurse whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nurse whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nurse whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nurse whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperNurse {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $medical_record_id
 * @property int|null $user_id
 * @property string|null $genre
 * @property string|null $group-sanguine
 * @property string|null $telephone_urgence
 * @property string|null $maladies_chroniques
 * @property string|null $assurance_maladie
 * @property string|null $numero_urgence
 * @property string|null $poids
 * @property string|null $taille
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Appointment|null $appointments
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MedicalReport> $medicalReports
 * @property-read int|null $medical_reports_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MedicalRecord> $medicalrecords
 * @property-read int|null $medicalrecords_count
 * @property-read \App\Models\Payment|null $payments
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SOSAlert> $sosalert
 * @property-read int|null $sosalert_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient whereAssuranceMaladie($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient whereGenre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient whereGroupSanguine($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient whereMaladiesChroniques($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient whereMedicalRecordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient whereNumeroUrgence($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient wherePoids($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient whereTaille($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient whereTelephoneUrgence($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPatient {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $account_id
 * @property int|null $patient_id
 * @property int|null $invoice_id
 * @property string|null $method
 * @property string|null $amount
 * @property string|null $purpose
 * @property string|null $reference_type
 * @property string|null $status
 * @property string|null $paid_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Acountant|null $accountant
 * @property-read \App\Models\Patient|null $patient
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment wherePatientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment wherePurpose($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereReferenceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPayment {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $role_id
 * @property string|null $name
 * @property string|null $display_name
 * @property string|null $description
 * @property string|null $module
 * @property string|null $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereModule($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPermission {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string|null $date_prescription
 * @property string|null $status
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $doctor_id
 * @property int|null $patient_id
 * @property int|null $consultation_id
 * @property-read \App\Models\Consultation|null $consultation
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PrescriptionLine> $prescriptionLines
 * @property-read int|null $prescription_lines_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription whereConsultationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription whereDatePrescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription whereDoctorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription wherePatientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPrescription {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $prescription_id
 * @property string|null $dosage
 * @property string|null $frequency
 * @property string|null $duration
 * @property string|null $instructions
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Prescription|null $prescription
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrescriptionLine newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrescriptionLine newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrescriptionLine query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrescriptionLine whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrescriptionLine whereDosage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrescriptionLine whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrescriptionLine whereFrequency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrescriptionLine whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrescriptionLine whereInstructions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrescriptionLine wherePrescriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrescriptionLine whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPrescriptionLine {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $role_id
 * @property string|null $name
 * @property string|null $description
 * @property string|null $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Privilege newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Privilege newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Privilege query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Privilege whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Privilege whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Privilege whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Privilege whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Privilege whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Privilege whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Privilege whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPrivilege {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string|null $nom
 * @property string|null $code
 * @property string|null $pays
 * @property string|null $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereNom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region wherePays($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperRegion {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $user_id
 * @property string|null $name
 * @property string|null $display_name
 * @property string|null $description
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperRole {}
}

namespace App\Models{
/**
 * 
 *
 * @property-read \App\Models\Patient|null $sosalerts
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SOSAlert newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SOSAlert newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SOSAlert query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSOSAlert {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $admin_id
 * @property string|null $type
 * @property string|null $status
 * @property string|null $file_path
 * @property float|null $file_size
 * @property string|null $started_at
 * @property string|null $completed_at
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sauvegarde newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sauvegarde newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sauvegarde query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sauvegarde whereAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sauvegarde whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sauvegarde whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sauvegarde whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sauvegarde whereFileSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sauvegarde whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sauvegarde whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sauvegarde whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sauvegarde whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sauvegarde whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sauvegarde whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSauvegarde {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $user_id
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string $payload
 * @property int $last_activity
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session whereLastActivity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSession {}
}

namespace App\Models{
/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StatistiqueRegionale newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StatistiqueRegionale newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StatistiqueRegionale query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperStatistiqueRegionale {}
}

namespace App\Models{
/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSettging newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSettging newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSettging query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSystemSettging {}
}

namespace App\Models{
/**
 * 
 *
 * @property-read \App\Models\Doctor|null $doctors
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeleConsultation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeleConsultation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeleConsultation query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTeleConsultation {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $language_id
 * @property int|null $role_id
 * @property int|null $department_id
 * @property string|null $first-name
 * @property string|null $last-name
 * @property string|null $birth-date
 * @property string|null $phone
 * @property string|null $country
 * @property string|null $city
 * @property string|null $profile-photo
 * @property string|null $status
 * @property string|null $address
 * @property string|null $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property string|null $two_factor_confirmed_at
 * @property string|null $last_login
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Role|null $roles
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBirthDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereProfilePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorRecoveryCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUser {}
}

namespace App\Models{
/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vaccin newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vaccin newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vaccin query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperVaccin {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $vaccine_id
 * @property int|null $medicalrecord_id
 * @property int|null $nurse_id
 * @property int|null $patient_id
 * @property int|null $total_required_dose
 * @property string|null $administration_date
 * @property string|null $status
 * @property string|null $localiter
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\MedicalRecord|null $medicalrecords
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vaccination newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vaccination newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vaccination query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vaccination whereAdministrationDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vaccination whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vaccination whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vaccination whereLocaliter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vaccination whereMedicalrecordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vaccination whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vaccination whereNurseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vaccination wherePatientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vaccination whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vaccination whereTotalRequiredDose($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vaccination whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vaccination whereVaccineId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperVaccination {}
}

