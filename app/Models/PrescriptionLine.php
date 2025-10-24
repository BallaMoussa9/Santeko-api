<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Prescription;
/**
 * @mixin IdeHelperPrescriptionLine
 */
class PrescriptionLine extends Model
{
    protected $fillable = [
        'prescription_id',
        'dosage',
        'medication_name',
        'frequency',
        'duration',
        'instructions',
    ];

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }
}
