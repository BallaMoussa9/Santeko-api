<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Department;
use App\Models\Patient;
use App\Models\Bed;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_number',
        'floor',
        'department_id',
        'capacity',
        'type',
        'is_available',
        'notes',
    ];

    // Les types de relation pour ce modèle

    /**
     * Relation vers le service auquel appartient la chambre.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class); // Assurez-vous que Service::class existe
    }

    /**
     * Relation vers les patients actuellement dans la chambre (Optionnel, selon votre structure Patient).
     */
    public function patients(): HasMany
    {
        // Supposons que la table 'patients' a une colonne 'room_id'
        return $this->hasMany(Patient::class);
    }
      public function beds(): HasMany
    {
        return $this->hasMany(Bed::class);
    }
    public function currentPatients()
    {
        return $this->hasManyThrough(Patient::class, Bed::class);
    }

}
