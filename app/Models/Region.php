<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\FirstResponder;
use App\Models\Hospital;
use App\Models\StatistiqueRegionale;
/**
 * @mixin IdeHelperRegion
 */
class Region extends Model
{
    protected $fillable = [
        'nom',
        'code',
        'pays',
        'type',
    ];
    public function firstresponders()
    {
        return $this->hasMany(FirstResponder::class);
    }
    public function hospitals()
    {
        return $this->hasMany(Hospital::class);
    }
    public function statistiqueregionales(){
        return $this->hasMany(StatistiqueRegionale::class);
    }

}
