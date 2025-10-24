<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Laboratory extends Model
{
    // Explicitly define the table name
    protected $table = 'laboratorys';

    protected $fillable = [
        'labtech_id', // Note: This field is on the LabTechnician model, not Laboratory
        'department_id',
        'name',
        'adress',
        'phone',
        'email',
        'opening_time',
        'closing_time',
        'status',
    ];

    /**
     * A laboratory has many analyses.
     */
    public function analyses(): HasMany
    {
        return $this->hasMany(Analyse::class, 'laboratory_id');
    }

    /**
     * A laboratory has many lab technicians.
     */
    public function labTechnicians(): HasMany
    {
        return $this->hasMany(LabTechnician::class, 'laboratory_id');
    }

    /**
     * A laboratory belongs to a department.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}
