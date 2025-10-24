<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperNotification
 */
class Notification extends Model
{
    public function users()
    {
        return $this->belongsTo(User::class);
    }
}
