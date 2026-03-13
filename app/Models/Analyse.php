<?php

namespace App\Models;

use App\Models\AnalyseRequest;
use App\Models\Doctor;
use App\Models\Laboratory;
use App\Models\LabTechnician;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperAnalyse
 */
class Analyse extends Model
{
    use HasFactory;

    protected $table = 'analyses';

    protected $fillable = [
        'laboratory_id',
        'patient_id',
        'doctor_id', // Ajout de ce champ, supposé exister dans la table pour la relation doctor()
        'lab_technician_id', // Nom de la colonne, vérifiez qu'il correspond à votre table
        'consultation_id',
        'name',
        'type',
        'status',
        'requested_at',
        'completed_at',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // --- Définition des relations Eloquent ---

    /**
     * Une Analyse (demande) peut avoir plusieurs résultats (AnalyseRequest).
     * @return HasMany
     */
    public function resultats(): HasMany
    {
        // Utilise la clé étrangère 'analyse_id' par convention. Si votre colonne est 'analyses_id',
        // changez-la en 'analyse_id' dans la migration ou spécifiez-le explicitement.
        return $this->hasMany(AnalyseRequest::class, 'analyses_id','id');
    }

    /**
     * L'Analyse appartient à un Laboratoire.
     * @return BelongsTo
     */
    public function laboratory(): BelongsTo
    {
        return $this->belongsTo(Laboratory::class, 'laboratory_id');
    }

    /**
     * L'Analyse concerne un Patient.
     * @return BelongsTo
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    /**
     * L'Analyse est assignée à un technicien de laboratoire.
     * @return BelongsTo
     */
    public function labTechnician(): BelongsTo
    {
        // Assurez-vous que le modèle et la clé étrangère correspondent à votre table.
        return $this->belongsTo(LabTechnician::class, 'lab_technician_id');
    }

    /**
     * L'Analyse est demandée par un médecin.
     * @return BelongsTo
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }
}
