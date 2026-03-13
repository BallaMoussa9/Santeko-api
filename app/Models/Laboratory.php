<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
   public function user(): HasOne
{
    // Un technicien a un utilisateur lié dont la clé étrangère est dans la table users
    return $this->hasOne(User::class, 'lab_technician_id');
}
}
