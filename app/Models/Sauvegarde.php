<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperSauvegarde
 */
class Sauvegarde extends Model
{
    public function admins(){
        return $this->belongsTo(Admin::class);
    }
}
