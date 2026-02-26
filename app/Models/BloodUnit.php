<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BloodUnit extends Model
{
    use HasFactory;

    protected $table = 'blood_units';

    protected $fillable = [
        'patient_id',    // Changé : donor_id -> patient_id
        'blood_group',
        'rh_factor',
        'unit_number',
        'collection_date',
        'expiration_date',
        'status',
        'location',
    ];

    protected $casts = [
        'collection_date' => 'date',
        'expiration_date' => 'date',
    ];

    /**
     * Une unité de sang appartient à un patient (donneur).
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    /**
     * Scope pour récupérer uniquement le sang disponible
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')
                     ->where('expiration_date', '>', now());
    }
}