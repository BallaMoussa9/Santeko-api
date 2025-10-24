<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Patient;
use App\Models\Allergies;
use App\Models\ConsultationHistory;
use App\Models\Vaccination;
use App\Http\Filters\MedicalRecordFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
/**
 * @mixin IdeHelperMedicalRecord
 */
class MedicalRecord extends Model
{
    use HasFactory;
    protected $table = 'medicalrecords';
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'chronic_conditions',
        'status',
        'numero_dossier',
        'hospital_id',
    ];
    public function patient(){
        return $this->belongsTo(Patient::class);
    }
    public function doctor(){
        return $this->belongsTo(Doctor::class);
    }
    public function allergies(){
        return $this->belongsTo(Allergies::class);
    }
    public function consultationHistorys()
    {
        return $this->belongsTo(ConsultationHistory::class);
    }
    public function vaccinations()
    {
        return $this->hasMany(Vaccination::class);
    }
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
     public function scopeFilter(Builder $builder, Request $request)
    {
        return (new MedicalRecordFilter($request))->apply($builder);
    }
    // Le dossier médical appartient à un patient

}
