<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $table = 'messages';

    protected $fillable = [
        'conversation_id',
        'user_id',
        'recever_id', // 🔑 AJOUTEZ CETTE LIGNE
        'content',
        'status', // Si cette colonne est toujours remplie
        'priority', // Si cette colonne est toujours remplie
        'title', // Si cette colonne est toujours remplie
        // Assurez-vous d'ajouter ici TOUTES les colonnes que vous remplissez via Message::create
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