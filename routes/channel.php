// routes/channels.php
<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Conversation;

// Le canal privé pour un utilisateur spécifique
Broadcast::channel('users.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Optionnel : Un canal pour les administrateurs (si vous voulez des notifs globales pour eux)
Broadcast::channel('admin-notifications', function ($user) {
    return $user->role === 'admin';
});
// Canal privé pour les alertes SOS destinées aux urgentistes (si toujours pertinent)
Broadcast::channel('urgentistes.sos', function ($user) {
    return $user->role === 'urgentiste' || $user->role === 'doctor';
});

// --- Canal privé pour une conversation spécifique (NOUVEAU) ---
Broadcast::channel('conversations.{conversationId}', function ($user, $conversationId) {
    // Vérifier si l'utilisateur est un participant de cette conversation
    $conversation = Conversation::find($conversationId);

    if ($conversation && $conversation->users->contains($user->id)) {
        return ['id' => $user->id, 'name' => $user->first_name . ' ' . $user->last_name];
    }

    return false; // Accès refusé
});