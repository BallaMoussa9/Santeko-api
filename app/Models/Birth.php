<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Department;
use App\Models\Nurse;

/**
 * @mixin IdeHelperBirth
 */
class Birth extends Model
{


// app/Models/Naissance.php


    protected $fillable = [
        'hospital_id',
        'patient_id',       // C'est probablement la mère
        'doctor_id',
        'department_id',
        'nurse_id',
        'firstname',
        'lastname',
        'sexe',
        'date_naissance',
        'lieu_naissance',
        'father_name',
        'poids',
        'taille',
        'heure_naissance',
        'statut',
        'numero_acte_naissance',
    ];

    protected $casts = [
        'date_naissance' => 'date',         // Juste la date
        'heure_naissance' => 'datetime',    // Date et heure pour l'heure de naissance
        'poids' => 'double',
        'taille' => 'double',
    ];

    // --- Définition des relations Eloquent ---

    /**
     * Get the hospital where the birth occurred.
     */
    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    /**
     * Get the patient (mother) associated with the birth.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the doctor who assisted in the birth.
     */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Get the department where the birth occurred.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the nurse who assisted in the birth.
     */
    public function nurse()
    {
        return $this->belongsTo(Nurse::class);
    }
}
