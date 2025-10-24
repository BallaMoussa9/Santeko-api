<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    // Assurez-vous que le nom de la table est correct.
    // Si votre table est 'messages' (avec un 's'), pas besoin de $table.
    // Si votre table est 'message' (sans 's'), ajoutez : protected $table = 'message';
    // Basé sur notre discussion, c'est 'messages'.
    protected $table = 'messages';

    protected $fillable = [
        'conversation_id',
        'user_id',
        'content',
        // Si vous avez d'autres colonnes que vous avez conservées et qui sont pertinentes, ajoutez-les ici
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}