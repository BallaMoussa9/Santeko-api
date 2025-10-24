<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Patient;
use App\Models\Doctor;

/**
 * @mixin IdeHelperAppointment
 */
class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id', 'doctor_id', 'appointment_date', 'appointment_time',
        'type', 'motif', 'status', 'cancellation_reason', 'confirmed_at', 'completed_at',
    ];

    protected $casts = [
        'appointment_date' => 'datetime:Y-m-d',
        'appointment_time' => 'datetime:H:i:s',
        'confirmed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Relation vers le patient
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    // Relation vers le docteur
    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }
}
