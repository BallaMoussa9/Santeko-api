<?php
namespace App\Http\Filters;
use Illuminate\Database\Eloquent\Builder;
class NurseFilter extends QueryFilter
{
    public function search($value): void
    {
        $this->builder->where(function ($query) use ($value) {
            $query->where('registration_number', 'like', '%' . $value . '%');
            $query->orWhereHas('user', function ($q) use ($value) {
                $q->where('first_name', 'like', '%' . $value . '%')
                  ->orWhere('last_name', 'like', '%' . $value . '%')
                  ->orWhere('email', 'like', '%' . $value . '%');
            });
        });
    }
    public function departmentId($value): void
    {
        $this->builder->where('department_id', $value);
    }
    public function status($value): void
    {
        $this->builder->where('status', $value);
    }
}