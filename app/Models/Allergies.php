<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\MedicalRecord;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperAllergies
 */
class Allergies extends Model
{
    // C'est un nom de classe pluriel, ce qui peut causer des problèmes avec les conventions Laravel.
    // Il est fortement recommandé de le renommer en 'Allergy' (au singulier).
    // Si vous ne pouvez pas le renommer, assurez-vous de définir le nom de la table :
    protected $table = 'allergies';

    // Les champs que vous avez dans votre table 'allergies'
    protected $fillable = [
        'medical_record_id',
        'patient_id',
        'substance',
        'reaction_decscription',
        'serverity',
        'recorded_by',
        'status',
        'notes',
    ];

    /**
     * Une allergie appartient à un dossier médical.
     */
    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    /**
     * Une allergie appartient à un patient.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
