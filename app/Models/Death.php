<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperDeath // Utile pour l'autocomplétion dans votre IDE
 */
class Death extends Model
{
    use HasFactory;

    // Spécifiez explicitement le nom de la table si le nom du modèle ne suit pas la convention de nommage de Laravel (Death -> deaths)
    // Dans votre cas, Model::class détecte 'deaths' à partir de 'Death', donc ce n'est pas strictement nécessaire mais bonne pratique.
    protected $table = 'deaths';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'department_id',
        'nurse_id',
        'date_deces',
        'lieu_deces',
        'cause_deces',
        'circonstances_deces',
        'statut',
        'numero_acte_deces',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_deces' => 'datetime', // Convertit automatiquement 'date_deces' en objet Carbon
    ];

    // --- Définition des relations Eloquent ---

    /**
     * Get the patient that owns the death record.
     */
    public function patient()
    {
        // Une instance de Death appartient à un Patient
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the doctor who recorded the death.
     */
    public function doctor()
    {
        // Une instance de Death appartient à un Doctor
        return $this->belongsTo(Doctor::class); // Assurez-vous d'avoir un modèle Doctor.php
    }

    /**
     * Get the department associated with the death.
     */
    public function department()
    {
        // Une instance de Death appartient à un Department
        return $this->belongsTo(Department::class); // Assurez-vous d'avoir un modèle Department.php
    }

    /**
     * Get the nurse associated with the death.
     */
    public function nurse()
    {
        // Une instance de Death appartient à une Nurse
        return $this->belongsTo(Nurse::class); // Assurez-vous d'avoir un modèle Nurse.php
    }
}
