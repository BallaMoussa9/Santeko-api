<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperPrescription
 */
class Prescription extends Model
{
    protected $fillable = [
        'doctor_id',
        'patient_id', 
        'consultation_id', // 🔥 AJOUT IMPORTANT
        'date_prescription',
        'status',
        'notes',
    ];

    protected $casts = [
        'date_prescription' => 'date',
    ];

    // 🎯 SUPPRIMEZ une des deux relations pour éviter le conflit
    // CHOISISSEZ UN SEUL NOM :
    
    // OPTION 1 : Gardez seulement 'lines' (recommandé)
    public function lines()
    {
        return $this->hasMany(PrescriptionLine::class);
    }
    
    // OU OPTION 2 : Gardez seulement 'prescriptionLines'
    // public function prescriptionLines()
    // {
    //     return $this->hasMany(PrescriptionLine::class);
    // }

    public function consultation()
    {
        return $this->belongsTo(Consultation::class, 'consultation_id');
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