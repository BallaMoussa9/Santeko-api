<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vaccination extends Model
{
    use HasFactory;

    protected $fillable = [
        'vaccine_id',
        'medicalrecord_id',
        'nurse_id',
        'patient_id',
        'total_required_dose',
        'administration_date',
        'status',
        'localiter',
        'notes',
    ];

    protected $casts = [
        'administration_date' => 'date',
    ];

    /**
     * Une vaccination est pour un vaccin spécifique.
     */
    public function vaccine(): BelongsTo
    {
        return $this->belongsTo(Vaccine::class);
    }

    /**
     * Une vaccination est associée à un dossier médical.
     */
    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    /**
     * Une vaccination est administrée par une infirmière.
     */
    public function nurse(): BelongsTo
    {
        return $this->belongsTo(Nurse::class);
    }

    /**
     * Une vaccination est reçue par un patient.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
