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
        'name',
        'description',
        'status',
        'admin_id',
        'position',
        'user_id'     // 👈 CORRECT : Nouvelle clé de responsable
    ];

    // Nom de la table si elle est différente de la convention
    protected $table = 'departments';

    // public function doctors(){
    //     return $this->hasMany(Doctor::class);
    // }
    public function nurses(){
        return $this->hasMany(Nurse::class);
    }
    public function laboratories(){
        return $this->hasMany(Laboratory::class);
    }
    public function hopitals(){
        return $this->hasMany(Hospital::class);
    }
    
    // 👈 Relation avec l'utilisateur responsable
    public function user() 
    {
        return $this->belongsTo(User::class, 'user_id'); 
    }
     
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}