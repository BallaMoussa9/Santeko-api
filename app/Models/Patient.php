<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;



use Illuminate\Http\Request;

use App\Models\{
    MedicalReport,
    Appointment,
    MedicalRecord,
    SOSAlert,
    User,
    Consultation,
    Allergies, // Recommander de renommer en 'Allergy'
    Vaccination,
    AnalyseRequest,
    Death,
    Birth,
    VitalSign,
    Invoice,
    Analyse,
    ConsultationHistory,
    NurseActivityReport,
    Payment,
    Bed,

};
use App\Http\Filters\PatientFilter;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperPatient
 */
class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bed_id',
        'medical_record_id',
        'last_consultation_date',
        'genre',
        'group_sanguine',
        'telephone_urgence',
        'maladies_chroniques',
        'assurance_maladie',
        'numero_urgence',
        'poids',
        'taille',
        'status',
    ];

    /** Relations **/

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    // NOUVEAU : Relation vers les rapports d'activité
    public function nurseActivityReports()
    {
        return $this->hasMany(NurseActivityReport::class, 'patient_id');
    }
    public function bed(): BelongsTo // ✅ CORRECTION
    {
        // Un Patient appartient À un Lit (la clé étrangère 'bed_id' est sur la table 'patients')
        return $this->belongsTo(Bed::class);
    }

    public function medicalRecord(): HasOne
    {
        return $this->hasOne(MedicalRecord::class, 'patient_id');
    }

    // Les autres relations restent HasMany() car un patient a plusieurs de ces entités.
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'patient_id');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'patient_id');
    }

    // public function vitalsigns(): HasMany
    // {
    //     return $this->hasMany(VitalSign::class, 'patient_id');
    // }



    public function medicalReports(): HasMany
    {
        return $this->hasMany(MedicalReport::class, 'patient_id');
    }

  public function consultationHistories(): HasMany
{
    return $this->hasMany(ConsultationHistory::class, 'patient_id');
}

    public function sosalerts(): HasMany
    {
        return $this->hasMany(SOSAlert::class, 'patient_id');
    }

    public function consultations(): HasMany
    {
        return $this->hasMany(Consultation::class, 'patient_id');
    }

    public function allergies(): HasMany
    {
        return $this->hasMany(Allergies::class, 'patient_id');
    }

    public function vaccinations(): HasMany
    {
        return $this->hasMany(Vaccination::class, 'patient_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'patient_id');
    }

    public function analyses(): HasMany
    {
        return $this->hasMany(Analyse::class, 'patient_id');
    }

   public function analyseRequests(): HasMany
    {
        return $this->hasMany(AnalyseRequest::class, 'patient_id');
    }

    public function deaths(): HasOne
    {
        return $this->hasOne(Death::class, 'patient_id');
    }

    public function births(): HasOne
    {
        return $this->hasOne(Birth::class, 'patient_id');
    }
     public function vitalSigns()
    {
        return $this->hasMany(VitalSign::class);
    }

    // Relation pour obtenir directement le dernier signe vital
    public function latestVitalSign()
    {
        return $this->hasOne(VitalSign::class)->latest('recorded_at');
    }

    /** Scope de filtre **/
    public function scopeFilter(Builder $builder, Request $request)
    {
        return (new PatientFilter($request))->apply($builder);
    }
}
