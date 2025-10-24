<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Consultation;
use App\Models\PrescriptionLine;
use App\Models\doctor;
use App\Models\patient;

/**
 * @mixin IdeHelperPrescription
 */
class Prescription extends Model
{
    protected $fillable = [
        'doctor_id',
        'patient_id',
        'date_prescription',
        'status',
        'notes',
    ];

    protected $casts = [
        'date_prescription' => 'date',
    ];
    public function consultations()
    {
        return $this->belongsTo(Consultation::class);
    }
    public function lines()
    {
        return $this->hasMany(PrescriptionLine::class);
    }
      public function prescriptionLines()
    {
        return $this->hasMany(PrescriptionLine::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
