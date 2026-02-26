<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Role;
use App\Models\Notification;
use App\Models\Language;
use App\Models\Patient;
use App\Models\Conversation;
use App\Models\Doctor;
use App\Models\Appointment;
use App\Models\Consultation;
use App\Models\Prescription;
use App\Models\Department;

use App\Models\Analyse;
use App\Models\Donor;
use App\Models\Admin;
use App\Models\Nurse;
use App\Traits\Filterable;
use App\Models\FirstResponder;
use App\Models\LabTechnician;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Importation ajoutée

/**
 * @mixin IdeHelperUser
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes, Filterable;

    protected $fillable = [
        'first_name',
        'last_name',
        'birth_date',
        'country',
        'city',
        'address',
        'profile_photo',
        'status',
        'role_id',
        'language_id',
        'department_id', // Colonne d'affectation
        'phone',
        'email',
        'password',
        'doctor_id',
        'patient_id',
        'nurse_id',
        'first_responder_id',
        'lab_technician_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
 public function donor(): HasOne
    {
        return $this->hasOne(Donor::class);
    }
    // Relations Many-to-Many
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function conversations(): BelongsToMany
    {
        return $this->belongsToMany(Conversation::class)->withPivot('last_read_at')->withTimestamps();
    }

    // Relations One-to-One
    public function patient(): HasOne
    {
    return $this->hasOne(Patient::class, 'user_id');
    }

    public function doctor(): HasOne
    {
        return $this->hasOne(Doctor::class);
    }

    public function admin(): HasOne 
    {
        return $this->hasOne(Admin::class);
    }

    public function nurse(): HasOne
    {
        return $this->hasOne(Nurse::class);
    }

   public function firstResponder(): HasOne
    {
        return $this->hasOne(FirstResponder::class, 'user_id'); 
      }

    public function labTechnician(): HasOne
    {
        return $this->hasOne(LabTechnician::class);
    }

    // 🔑 NOUVELLE RELATION D'AFFECTATION (pour la colonne department_id)
    /**
     * Le département auquel cet utilisateur est affecté. 
     * Cette méthode doit être nommée 'department' pour résoudre l'erreur 500
     * si votre API appelle User::with('department').
     */
    public function department(): BelongsTo // Anciennement "assignedDepartment"
    {
        // Utilisation de 'department' pour corriger le RelationNotFoundException
        return $this->belongsTo(Department::class, 'department_id');
    }
    
    // Relations One-to-Many (où l'utilisateur est la clé étrangère)
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'doctor_id');
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class, 'doctor_id');
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class, 'doctor_id');
    }

    public function analyses()
    {
        return $this->hasMany(Analyse::class, 'lab_technician_id');
    }

    public function languages()
    {
        return $this->hasMany(Language::class);
    }
    
    // RELATION INVERSE : Départements dont cet utilisateur est responsable
    public function responsibleDepartments() 
    {
        return $this->hasMany(Department::class, 'user_id');
    }

    // Méthodes utilitaires pour les rôles (la logique est correcte)
    public function hasRole($roleName)
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    public function assignRole($roleName)
    {
        $role = Role::where('name', $roleName)->first();
        if ($role && !$this->hasRole($roleName)) {
            $this->roles()->attach($role->id);
        }
    }

    public function removeRole($roleName)
    {
        $role = Role::where('name', $roleName)->first();
        if ($role && $this->hasRole($roleName)) {
            $this->roles()->detach($role->id);
        }
    }
}