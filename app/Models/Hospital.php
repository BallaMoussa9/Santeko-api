<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Department;
use App\Models\Region;
/**
 * @mixin IdeHelperHospital
 */
class Hospital extends Model
{
    protected $fillable = [
        'nom',
        'adresse',
        'phone',
        'email',
        'ville',
        'type',
    ];
    public function departments()
    {
        return $this->hasMany(Department::class);
    }
    public function regions()
    {
        return $this->belongsTo(Region::class);
    }
}
