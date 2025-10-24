<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;

class PatientFilter extends QueryFilter
{
    /**
     * Recherche par nom, prénom, téléphone ou email
     */
    public function search($value): void
    {
        $this->builder->where(function (Builder $query) use ($value) {
            $query->whereHas('user', function (Builder $q) use ($value) {
                $q->where('first_name', 'like', '%' . $value . '%')
                  ->orWhere('last_name', 'like', '%' . $value . '%')
                  ->orWhere('phone', 'like', '%' . $value . '%')
                  ->orWhere('email', 'like', '%' . $value . '%');
            });
        });
    }

    /**
     * Filtre par statut
     */
    public function status($value): void
    {
        $this->builder->where('status', $value);
    }

    /**
     * Filtre par numéro de dossier médical
     */
    public function medicalRecordNumber($value): void
    {
        $this->builder->whereHas('medicalRecord', function (Builder $query) use ($value) {
            $query->where('numero_dossier', $value);
        });
    }

    // Ajoutez d'autres filtres si nécessaire
}
