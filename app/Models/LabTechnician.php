<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperLabTechnician
 */
class LabTechnician extends Model
{
    protected $table = 'labtechnicians';

    protected $fillable = [
        'user_id',
        'laboratory_id',
        'speciality',
        'qualification',
        'status',
    ];

    /**
     * Un technicien appartient à un laboratoire.
     */
    public function laboratory(): BelongsTo
    {
        return $this->belongsTo(Laboratory::class, 'laboratory_id');
    }

    /**
     * Un technicien a plusieurs analyses.
     */
    public function analyses(): HasMany
    {
        return $this->hasMany(Analyse::class, 'lab_technician_id');
    }

    /**
     * Un technicien est lié à un utilisateur.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
