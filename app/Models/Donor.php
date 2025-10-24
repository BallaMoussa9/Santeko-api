<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Donor extends Model
{
    use HasFactory;

    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'blood_group',
        'rh_factor',
        'date_of_birth',
        'phone',
    ];

    /**
     * Une unité de sang appartient à un donneur.
     */
    protected $casts = [
        'date_of_birth' => 'date',
    ];

    /**
     * Un donneur peut être lié à un utilisateur (optionnel).
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Un donneur peut donner plusieurs unités de sang.
     *
     * @return HasMany
     */
    public function bloodUnits(): HasMany
    {
        return $this->hasMany(BloodUnit::class);
    }
}
