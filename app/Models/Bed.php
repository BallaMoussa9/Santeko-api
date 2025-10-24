<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Bed extends Model
{
    use HasFactory;

    protected $fillable = [
        'bed_number',
        'room_id',
        'status',
        'is_private',
        'equipment_notes',

    ];

    protected $casts = [
        'is_private' => 'boolean',
    ];

    /**
     * Relation vers la chambre à laquelle appartient le lit (N:1).
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Relation vers le patient occupant actuellement ce lit (1:1).
     */
    public function patient(): HasOne // ✅ CORRECTION : Le lit PEUT AVOIR un patient
    {
        // Utilise HasOne pour pointer vers le Patient qui possède ce lit via 'bed_id'
        return $this->hasOne(Patient::class, 'bed_id');
    }
}
