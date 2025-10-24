<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Department;
use App\Models\Consultation;
use App\Models\MedicalReport;
use App\Models\TeleConsultation;
use App\Models\Analyse;
use App\Models\Appointment;
use App\Models\User;
use App\Http\Filters\DoctorFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * @mixin IdeHelperDoctor
 */
class Doctor extends Model
{
    protected $fillable = [
        'user_id',
        'department_id',
        'speciality',
        'numero_ordre',
        'biography',
        'experience',
        'status',
        'numero_professionel'
    ];

    public function teleconsultations()
    {
        return $this->hasMany(TeleConsultation::class);
    }
 public function appointments(): HasMany
    {
        // Laravel cherche les rendez-vous où appointment.doctor_id = doctor.id
        return $this->hasMany(Appointment::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }

    public function medicalreports()
    {
        return $this->hasMany(MedicalReport::class);
    }

    public function analyses()
    {
        return $this->hasMany(Analyse::class);
    }

    public function scopeFilter(Builder $builder, Request $request)
    {
        return (new DoctorFilter($request))->apply($builder);
    }
}
