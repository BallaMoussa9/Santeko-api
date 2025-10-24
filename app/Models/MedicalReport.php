<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\{Patient, Doctor};

/**
 * @mixin IdeHelperMedicalReport
 */
class MedicalReport extends Model
{
    use HasFactory;

    // Indiquez explicitement à Laravel le nom de la table
   // protected $table = 'medicalreports';

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'report_type', // Ajouté d'après votre structure de table
        'title',       // Ajouté d'après votre structure de table
        'content',     // Ajouté d'après votre structure de table
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    // Correction du nom de la relation au singulier pour le médecin
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
