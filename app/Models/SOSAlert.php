<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Patient;
use App\Models\User;

/**
 * @mixin IdeHelperSOSAlert
 */
class SOSAlert extends Model
{
    // Indique à Laravel que le nom de la table est 'sosalerts' et non 's_o_s_alerts'
    protected $table = 'sosalerts';
      protected $fillable = [
        'patient_id',
        'status',
        'type',
        'latitude',
        'longitude',
        'description',
        'initiated_at',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'initiated_at' => 'datetime',
    ];

    public function patients(){
        return $this->belongsTo(Patient::class);
    }
    public  function user(){
        return $this->hasOneThrough(User::class,Patient::class,'id','id','patient_id','user_id');
    }

}
