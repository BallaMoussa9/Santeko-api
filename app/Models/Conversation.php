<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_private',
    ];

    protected $casts = [
        'is_private' => 'boolean',
    ];

    // Pour inclure l'autre participant dans la réponse JSON
    protected $appends = ['other_participant'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('last_read_at')->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->latest();
    }

    // Un accesseur pour trouver l'autre participant dans un chat 1-to-1
    public function getOtherParticipantAttribute(): ?User
    {
        if ($this->is_private && $this->users->count() === 2) {
            $currentUser = auth()->user();
            if ($currentUser) {
                return $this->users->first(fn($user) => $user->id !== $currentUser->id);
            }
        }
        return null;
    }
}