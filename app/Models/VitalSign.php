<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;
use App\Models\Patient;
use App\Models\Nurse;


class VitalSign extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'nurse_id',
        'blood_pressure_systolic',
        'blood_pressure_diastolic',
        'heart_rate',
        'temperature',
        'respiratory_rate',
        'oxygen_saturation',
        'weight',
        'height',
        'recorded_at',
        'notes',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function nurse()
    {
        return $this->belongsTo(User::class, 'nurse_id'); // L'infirmier est un User
    }
}
