<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Region;
use App\Models\User;
use App\Models\Hopital;
use App\Models\Department;


class StatistiqueRegionale extends Model
{
    protected $table = 'statistiqueregionales';

    protected $fillable = [
        'region_id',
        'user_id',
        'hopital_id',
        'department_id',
        'region',
        'period_start',
        'period_end',
        'total_consultations',
        'total_teleconsultations',
        'total_analyses',
        'taux_prescriptions',
        'total_vaccinations',
        'taux_paiement',
        'taux_rdv_annules',
        'status',
    ];

    /**
     * Une statistique appartient à une région.
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    /**
     * Une statistique est créée par un utilisateur.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Une statistique est liée à un hôpital.
     */
    public function hopital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class, 'hopital_id');
    }

    /**
     * Une statistique est liée à un département.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}
