<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperLanguage
 */
class Language extends Model
{
    protected $fillable = [
        'name',
        'code',
        'locale',
        'direction',
        'is_active',
        'is_default',
        'native_name',
    ];

    /**
     * Une langue peut être utilisée par plusieurs utilisateurs.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'language_id');
    }
}
