<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\HasFactory;
/**
 * @mixin IdeHelperFirstResponder
 */
class FirstResponder extends Model
{
    use HasFactory;
        protected $table = 'firts_responders';
        protected $primaryKey = 'id';
     protected $fillable = [
        'user_id',
        'speciality',
        'status',
        'location',
     ];
    public function user()
    {
        // Un FirstResponder est référencé par plusieurs utilisateurs (un à un dans votre cas)
        return $this->belongsTo(User::class, 'user_id');
    }
    public function regions()
    {
        return $this->belongsTo(Region::class);
    }
}
