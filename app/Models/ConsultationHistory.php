<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Allergies;
use App\Models\Patient;
/**
 * @mixin IdeHelperConsultationHistory
 */
class ConsultationHistory extends Model
{
        protected $table = 'consultation_histories'; // <- nom réel de la table

    public function allergies(){
        return $this->hasMany(Allergies::class);
    }
    public function patients(){
        return $this->belongsTo(Patient::class);
    }
    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }
}
