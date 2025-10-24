<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Department;
use App\Traits\Filterable;
use App\Models\VitalSign;
use App\Models\BloodUnit;
use Illuminate\Database\Eloquent\Builder;
use App\Models\NurseActivityReport;
use Illuminate\Http\Request;
use App\Http\Filters\NurseFilter;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperNurse
 */
class Nurse extends Model
{
    use Filterable;

    protected $fillable = [
        'user_id',
        'department_id',
        'speciality'
    ];

    /**
     * Une infirmière appartient à un utilisateur.
     * La clé étrangère est `user_id` sur la table `nurses`.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Une infirmière appartient à un département.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Une infirmière a plusieurs signes vitaux.
     */
    public function vitalSigns()
    {
        return $this->hasMany(VitalSign::class);
    }

    /**
     * Une infirmière gère plusieurs unités de sang.
     */
    public function bloodUnits()
    {
        return $this->hasMany(BloodUnit::class);
    }

    /**
     * Une infirmière a plusieurs rapports d'activités.
     */
    public function nurseActivityReports()
    {
        return $this->hasMany(NurseActivityReport::class);
    }

    public function scopeFilter(Builder $builder, Request $request)
    {
        return (new NurseFilter($request))->apply($builder);
    }

}
