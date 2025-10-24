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
    // C'EST LA SEULE LIGNE QUE J'AJOUTE, ELLE EST INDISPENSABLE POUR create/update
    protected $fillable = [
        // 'prescription_id', // Ce champ est dans votre table mais incohérent avec hasMany prescriptions(), donc ignoré pour mass assignment
        'doctor_id',
        'patient_id',
        // 'date_prescription', // Ce champ est dans votre table mais lié aux prescriptions, donc ignoré pour mass assignment
        'type',
        'motif',
        'diagnostic',
        'status',
        'traitement',
        'notes',
        'observations',
    ];

    public function Invoice()
    {
        return $this->hasOne(Invoice::class);
    }
    public function precriptions() // Gardé tel quel
    {
        return $this->hasMany(Prescription::class);
    }
    public function patients() // Gardé tel quel
    {
        return $this->belongsTo(Patient::class);
    }
    public function doctors() // Gardé tel quel
    {
        return $this->belongsTo(Doctor::class);
    }
    public function teleconsultations() // Gardé tel quel
    {
        return $this->belongsTo(Teleconsultation::class);
    }

    public function consultationhistorys() // Gardé tel quel
    {
        return $this->belongsTo(ConsultationHistory::class);
    }

}
