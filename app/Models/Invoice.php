<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Consultation;
use App\Models\Payment;
use App\Models\Patient;
/**
 * @mixin IdeHelperInvoice
 */
class Invoice extends Model
{


    protected $fillable = [
        'consultation_id',
        'amount',
        'status',
        'due_date',
    ];

    public function constultations()
    {
        return $this->belongsTo(Consultation::class);
    }

    public function Payments()
    {
        return $this->hasOne(Payment::class);
    }
    public function patients(){
        return $this->belongsTo(Patient::class);
    }



}
