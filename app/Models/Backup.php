<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Backup extends Model
{
    use HasFactory;

    /**
     * Les champs qui peuvent être remplis en masse (mass assignable).
     */
    protected $fillable = [
        'status',
        'filename',
        'path',
        'size',
        'type',
        'last_run_at',
        'notes',
    ];

    /**
     * Les attributs qui doivent être castés automatiquement.
     */
    protected $casts = [
        'last_run_at' => 'datetime',
        'size' => 'integer', // 👈 Cast utile si tu stockes la taille en octets
    ];

    /**
     * Accessor : retourne la taille formatée (KB, MB, GB…).
     */
    public function getFormattedSizeAttribute(): string
    {
        $size = $this->size ?? 0;

        if ($size >= 1073741824) {
            return number_format($size / 1073741824, 2) . ' GB';
        } elseif ($size >= 1048576) {
            return number_format($size / 1048576, 2) . ' MB';
        } elseif ($size >= 1024) {
            return number_format($size / 1024, 2) . ' KB';
        }

        return $size . ' B';
    }

    /**
     * Scope pour filtrer les backups par type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope pour récupérer uniquement les backups réussis.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope pour récupérer les backups échoués.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
}
