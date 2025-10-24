<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Events\MessageSent;
use App\Notifications\NewMessageNotification;

class ChatController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        // authentification via sanctum pour toutes les méthodes du contrôleur
        $this->middleware('auth:sanctum');
    }

    /**
     * Liste des conversations de l'utilisateur avec le dernier message et l'autre participant.
     */
    public function indexConversations(): JsonResponse
    {
        $user = auth()->user();

        $conversations = $user->conversations()
            ->with([
                // récupérer seulement le dernier message
                'messages' => fn($q) => $q->latest()->limit(1),
                // récupérer les utilisateurs sauf l'utilisateur courant (pour afficher l'interlocuteur)
                'users' => fn($q) => $q->where('users.id', '!=', $user->id),
            ])
            ->get()
            // trier par date du dernier message (desc)
            ->sortByDesc(fn($conversation) => $conversation->messages->first()?->created_at)
            ->values();

        return response()->json($conversations);
    }

    /**
     * Trouve ou crée une conversation privée entre sender et receiver.
     */
    public function createOrGetPrivateConversation(Request $request): JsonResponse
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
        ]);

        $sender = auth()->user();
        $receiver = User::find($request->receiver_id);

        if (!$receiver) {
            return response()->json(['message' => 'Utilisateur destinataire introuvable.'], 404);
        }

        if ($sender->id === $receiver->id) {
            return response()->json(['message' => 'Vous ne pouvez pas démarrer une conversation avec vous-même.'], 400);
        }

        // Rechercher une conversation privée qui contient les deux utilisateurs
        $conversation = Conversation::where('is_private', true)
            ->whereHas('users', fn($q) => $q->where('users.id', $sender->id))
            ->whereHas('users', fn($q) => $q->where('users.id', $receiver->id))
            ->first();

        // S'assurer qu'il n'y a que les deux participants (évite d'ouvrir une conversation de groupe)
        if ($conversation && $conversation->users()->count() === 2) {
            $conversation->load(['users' => fn($q) => $q->where('users.id', '!=', $sender->id)]);
            return response()->json($conversation);
        }

        // Sinon créer une nouvelle conversation privée
        $conversation = Conversation::create(['is_private' => true]);
        $conversation->users()->attach([$sender->id, $receiver->id]);

        $conversation->load(['users' => fn($q) => $q->where('users.id', '!=', $sender->id)]);

        return response()->json($conversation, 201);
    }

    /**
     * Affiche les messages d'une conversation (pagination).
     */
   // App/Http/Controllers/ChatController.php

// ... (le code avant showConversationMessages reste le même)

/**
 * Affiche les messages d'une conversation (pagination).
 */
public function showConversationMessages(Conversation $conversation): JsonResponse
{
    $user = auth()->user();

    // Vérifier que l'utilisateur fait partie de la conversation
    if (! $conversation->users()->where('users.id', $user->id)->exists()) {
        return response()->json(['message' => 'Accès non autorisé à cette conversation.'], 403);
    }

    // Mettre à jour le pivot last_read_at
    $conversation->users()->updateExistingPivot($user->id, ['last_read_at' => now()]);

    // CHARGEZ LES MESSAGES SANS PAGINATION POUR SIMPLIFIER LE FRONTEND
    // Si vous tenez à la pagination, vous devrez la gérer dans le chatStore.js
    // Pour l'instant, simplifions pour que le système fonctionne:
    $messages = $conversation->messages()
        ->with('user')
        ->orderBy('created_at', 'desc') // Important pour le front, car il affiche en reverse
        ->get();

    // Au lieu de retourner la pagination, retournez directement le tableau de messages.
    // Si vous aviez la pagination, il faudrait retourner: return response()->json($messages);
    // et le frontend devrait lire response.data.data
    return response()->json($messages);
}

    /**
     * Envoie un message dans une conversation.
     */
    public function sendMessage(Request $request, Conversation $conversation): JsonResponse
    {
        $user = auth()->user();

        if (! $conversation->users()->where('users.id', $user->id)->exists()) {
            return response()->json(['message' => 'Accès non autorisé à cette conversation.'], 403);
        }

        $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $message = $conversation->messages()->create([
            'user_id' => $user->id,
            'content' => $request->input('content'),
        ]);

        $message->load('user', 'conversation');

        // Diffuse un event (Websockets, pusher, etc.)
        MessageSent::dispatch($message);

        // Notifier les autres participants
        foreach ($conversation->users as $participant) {
            if ($participant->id !== $user->id) {
                $participant->notify(new NewMessageNotification($message));
            }
        }

        return response()->json($message, 201);
    }
}
