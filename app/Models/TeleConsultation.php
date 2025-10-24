<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Doctor;
use App\Models\Consultation;

/**
 * @mixin IdeHelperTeleConsultation
 */
class TeleConsultation extends Model
{
    protected $table = 'teleconsultations'; // exactement le nom dans MySQL

    protected $fillable = [
        'doctor_id',
        'patient_id',
        'consultation_id',
        'scheduled_at',
        'duration',
        'status',
        'consultation_link',
        'note',
    ];

    public function doctors()
    {
        return $this->belongsTo(Doctor::class);
    }
    public function consultations(){
        return $this->hasMany(Consultation::class);
    }


}
