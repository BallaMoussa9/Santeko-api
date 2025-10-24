<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Doctor;
use App\Models\Nurse;
use App\Models\Laboratory;
use App\Models\Hospital;

/**
 * @mixin IdeHelperDepartment
 */
class Department extends Model
{
      // Assurez-vous que cette propriété existe et contient les colonnes que vous voulez assigner en masse.
    protected $fillable = [
        'name',         // <-- AJOUTEZ 'name' ICI
        'description',  // <-- AJOUTEZ 'description' ICI
        'status',       // <-- AJOUTEZ 'status' ICI
        'admin_id',     // <-- AJOUTEZ 'admin_id' ICI
        'position',
        'doctor_id'     // <-- AJOUTEZ 'doctor_id' ICI (si vous l'utilisez)
    ];

    // Nom de la table si elle est différente de la convention
    protected $table = 'departments';

    public function doctors(){
        return $this->hasMany(Doctor::class);
    }
    public function nurses(){
        return $this->hasMany(Nurse::class);
    }
    public function laboratories(){
        return $this->hasMany(Laboratory::class);
    }
    public function hopitals(){
        return $this->hasMany(Hospital::class);
    }
     public function users()
    {
        return $this->hasMany(User::class);
    }
     public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}
