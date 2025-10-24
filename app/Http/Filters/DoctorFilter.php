<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;

class DoctorFilter extends QueryFilter
{
    public function search($value): void
    {
        $this->builder->where(function ($query) use ($value) {
            $query->where('numero_professionel', 'like', '%' . $value . '%')
                  ->orWhere('speciality', 'like', '%' . $value . '%')
                  ->orWhereHas('user', function ($q) use ($value) {
                      $q->where('first_name', 'like', '%' . $value . '%')
                        ->orWhere('last_name', 'like', '%' . $value . '%')
                        ->orWhere('email', 'like', '%' . $value . '%');
                  });
        });
    }

    public function speciality($value): void
    {
        $this->builder->where('speciality', $value);
    }

    public function departmentId($value): void
    {
        $this->builder->where('department_id', $value);
    }

    public function status($value): void
    {
        $this->builder->where('status', $value);
    }

    // Vous pouvez ajouter d'autres méthodes de filtrage ou de tri si nécessaire
}