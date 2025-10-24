<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Nurse;
class NurseActivityReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'nurse_id',
        'report_date',
        'title',
        'content',
        'patient_id', // Facultatif, si le rapport est lié à un patient spécifique
    ];

    protected $casts = [
        'report_date' => 'date',
    ];

    public function nurse()
    {
        return $this->belongsTo(Nurse::class, 'nurse_id'); // L'infirmier est un User
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
