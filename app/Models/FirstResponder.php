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

     protected $fillable = [
        'user_id',
        'speciality',
        'status',
        'location',
     ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function regions()
    {
        return $this->belongsTo(Region::class);
    }
}
