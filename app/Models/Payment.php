<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Acountant;
use App\Models\Patient;
/**
 * @mixin IdeHelperPayment
 */
class Payment extends Model
{
    public function accountants()
    {
        return $this->belongsTo(Acountant::class);
    }
    public function patients()
    {
        return $this->belongsTo(Patient::class);
    }
}
