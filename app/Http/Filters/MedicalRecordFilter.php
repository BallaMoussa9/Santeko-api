<?php
namespace App\Http\Filters;
use Illuminate\Database\Eloquent\Builder;
class MedicalRecordFilter extends QueryFilter
{
    public function search($value): void
    {
        $this->builder->where(function ($query) use ($value) {
            $query->where('numero_dossier', 'like', '%' . $value . '%')
                  ->orWhere('allergies', 'like', '%' . $value . '%')
                  ->orWhereHas('patient', function($q) use ($value) {
                      $q->where('first_name', 'like', '%' . $value . '%')
                        ->orWhere('last_name', 'like', '%' . $value . '%');
                  });
        });
    }
    public function patientId($value): void
    {
        $this->builder->where('patient_id', $value);
    }
    public function doctorId($value): void
    {
        $this->builder->where('doctor_id', $value);
    }
    public function status($value): void
    {
        $this->builder->where('status', $value);
    }
}