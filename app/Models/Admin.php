<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Privilege;
use App\Models\Sauvegarde;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
/**
 * @mixin IdeHelperAdmin
 */
class Admin extends Model
{

    use HasFactory;

    protected $fillable = [
        // La table admins n'a que ces colonnes (selon votre capture)
        'department_id',
        'user_id',
    ];

    // Vous pouvez aussi définir les casts pour les booléens
    protected $casts = [
        'is_editable' => 'boolean',
        'is_visible' => 'boolean',
        'is_required' => 'boolean',
        // 'value' => 'json', // Si vous voulez caster la valeur comme JSON quand type est 'json'
    ];

    // Relation avec l'administrateur qui a créé/mis à jour le paramètre (facultatif)
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
    public function privileges(){
        return $this->belongsToMany(Privilege::class);
    }
    public function users(){
        return $this->hasOne(User::class);
    }
    public function sauvegardes()
    {
        return $this->belongsTo(Sauvegarde::class);
    }

}
