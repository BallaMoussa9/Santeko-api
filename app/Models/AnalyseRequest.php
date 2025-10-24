<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Analyse;
use App\Models\Patient;
use App\Models\Laboratory;
use App\Models\User; // Assuming technicians are users

/**
 * @mixin IdeHelperAnalyseRequest
 */
class AnalyseRequest extends Model
{
    protected $table = 'analyse_requests';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'analyse_id',          // Changed from 'analyses_id' for consistency
        'patient_id',          // Kept for now, but can be removed for normalization
        'labtechnician_id',    // Corrected to singular
        'lab_id',
        'result_text',
        'result_file',
        'status',
        'analyse_type',
        'validated_at',
        'comments',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'validated_at' => 'datetime',
    ];

    /**
     * Get the parent analysis request.
     */
    public function analyse(): BelongsTo
    {
        return $this->belongsTo(Analyse::class, 'analyse_id');
    }

    /**
     * Get the patient associated with the analysis request result.
     *
     * Note: This relationship can be considered redundant if accessed via ->analyse->patient
     * but is kept here for direct access.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    /**
     * Get the laboratory that processed the analysis request.
     */
    public function laboratory(): BelongsTo
    {
        return $this->belongsTo(Laboratory::class, 'lab_id');
    }

    /**
     * Get the lab technician who processed the analysis request.
     * Assumes a LabTechnician model exists, otherwise, link to User model.
     */
    public function labTechnician(): BelongsTo
    {
        // If you have a dedicated LabTechnician model
        return $this->belongsTo(LabTechnician::class, 'labtechnician_id');

        // OR if technicians are users in the 'users' table
        // return $this->belongsTo(User::class, 'labtechnician_id');
    }
}
