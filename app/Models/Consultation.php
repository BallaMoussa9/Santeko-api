<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Invoice;
use App\Models\Prescription;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Teleconsultation;
use App\Models\ConsultationHistory;

/**
 * @mixin IdeHelperConsultation
 */
class Consultation extends Model
{
    protected $fillable = [
        'doctor_id',
        'patient_id',
        'type',
        'motif',
        'diagnostic',
        'status',
        'traitement',
        'notes',
        'observations',
    ];

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function prescriptions() // Correction du nom
    {
        return $this->hasMany(Prescription::class);
    }

    public function patient() // SINGULIER - relation belongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function doctor() // SINGULIER - relation belongsTo
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    public function teleconsultations()
    {
        return $this->belongsTo(Teleconsultation::class);
    }

    public function consultationHistories() // Correction du nom
    {
        return $this->belongsTo(ConsultationHistory::class);
    }
}