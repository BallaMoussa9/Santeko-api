<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BloodUnit extends Model
{
    use HasFactory;

    // Nom de la table si elle n'est pas la convention "blood_units" (votre structure le suggère)
    protected $table = 'blood_units';

    protected $fillable = [
        'blood_group',
        'rh_factor',
        'unit_number',
        'collection_date',
        'expiration_date',
        'status',
        'location',
        'donor_id',
    ];

    protected $casts = [
        'collection_date' => 'date',
        'expiration_date' => 'date',
    ];

    /**
     * Une unité de sang appartient à un donneur.
     */
    public function donor(): BelongsTo
    {
        return $this->belongsTo(Donor::class, 'donor_id');
    }

    // Vous pouvez aussi ajouter d'autres relations ici, par exemple avec les "Analyses" si une analyse est faite sur l'unité de sang.
    // public function analyses(): HasMany
    // {
    //     return $this->hasMany(Analyse::class, 'blood_unit_id');
    // }
}
