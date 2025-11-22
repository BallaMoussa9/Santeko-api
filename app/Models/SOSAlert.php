<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperSOSAlert
 */
class SOSAlert extends Model
{
    protected $table = 'sosalerts';

    protected $fillable = [
        'patient_id',
        'status',
        'type',
        'latitude',
        'longitude',
        'description',
        'initiated_at',
        'responded_at',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'initiated_at' => 'datetime',
    ];

    // 🔥 CORRECTION : Relation au singulier avec clé étrangère explicite
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    // 🔥 CORRECTION : Relation user simplifiée
    public function user()
    {
        return $this->hasOneThrough(
            User::class,
            Patient::class,
            'id',          // Clé sur patients
            'id',          // Clé sur users
            'patient_id',  // Clé locale sur sosalerts
            'user_id'      // Clé sur patients qui référence users
        );
    }
}
