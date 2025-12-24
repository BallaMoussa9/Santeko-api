<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Events\MessageSent;
use App\Notifications\NewMessageNotification;

class ChatController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    // public function __construct()
    // {
    //     $this->middleware('auth:sanctum');
    // }

    /**
     * 🔹 Récupère toutes les conversations de l'utilisateur connecté.
     */
    public function indexConversations(): JsonResponse
    {
        $user = auth()->user();

        $conversations = $user->conversations()
            ->with([
                // Charge les autres utilisateurs participants (exclut l'utilisateur courant)
                'users' => fn($q) => $q->where('users.id', '!=', $user->id),
            ])
            ->orderBy('conversations.updated_at', 'desc')
            ->get();

        $conversations->each(function ($c) use ($user) {
            // Renomme la collection d'utilisateurs à un seul utilisateur pour les conversations privées
            $c->other_user = $c->users->first();
            unset($c->users); 
        });

        return response()->json($conversations);
    }

    /**
     * 🔹 Trouve ou crée une conversation privée entre deux utilisateurs.
     */
    public function createOrGetPrivateConversation(Request $request): JsonResponse
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
        ]);

        $sender = auth()->user();
        $receiver = User::find($request->receiver_id);

        if (!$receiver || $sender->id === $receiver->id) {
            return response()->json(['message' => 'Destinataire introuvable ou action non valide.'], 400);
        }

        // Vérifier s’il existe déjà une conversation privée entre eux
        $conversation = Conversation::where('is_private', true)
            ->whereHas('users', fn($q) => $q->where('users.id', $sender->id))
            ->whereHas('users', fn($q) => $q->where('users.id', $receiver->id))
            ->first();

        if ($conversation) {
            // Si la conversation existe, on la retourne
            $conversation->load(['users' => fn($q) => $q->where('users.id', '!=', $sender->id)]);
            $conversation->other_user = $conversation->users->first();
            unset($conversation->users);

            return response()->json($conversation);
        }

        // Sinon, création
        $conversation = Conversation::create(['is_private' => true]);
        $conversation->users()->attach([$sender->id, $receiver->id]);
        
        // Assurer le format de réponse attendu par le Front
        $conversation->load(['users' => fn($q) => $q->where('users.id', '!=', $sender->id)]);
        $conversation->other_user = $conversation->users->first();
        unset($conversation->users);

        return response()->json($conversation, 201);
    }

    /**
     * 🔹 Récupère les messages d’une conversation donnée.
     * 🔑 CORRECTION CLÉ: Utilisation d'une clause WHERE OR plus robuste pour garantir la récupération des messages.
     */
    public function showConversationMessages(Conversation $conversation): JsonResponse
{
    try {
        $user = auth()->user();

        if (! $conversation->users()->where('users.id', $user->id)->exists()) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        $conversation->users()->updateExistingPivot($user->id, ['last_read_at' => now()]);

        $otherUser = $conversation->users()->where('users.id','!=',$user->id)->first();
        if (!$otherUser) {
            return response()->json(['data' => []]);
        }

        $messages = Message::where(function ($query) use ($user, $otherUser) {
                $query->where('user_id', $user->id)->where('recever_id', $otherUser->id);
            })->orWhere(function ($query) use ($user, $otherUser) {
                $query->where('user_id', $otherUser->id)->where('recever_id', $user->id);
            })->with('user')->orderBy('created_at','asc')->get();

        return response()->json(['data' => $messages]);
    } catch (\Throwable $e) {
        \Log::error('showConversationMessages error: '.$e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'conversation_id' => $conversation->id ?? null,
        ]);
        return response()->json(['message' => 'Erreur serveur interne.'], 500);
    }
}


    /**
     * 🔹 Envoie un message dans une conversation.
     * 🔑 CORRECTION CLÉ : Assure que l'objet Message renvoyé contient l'utilisateur expéditeur.
     */
    public function sendMessage(Request $request, Conversation $conversation): JsonResponse
    {
        $user = auth()->user();

        // 1. Vérification d'accès
        if (! $conversation->users()->where('users.id', $user->id)->exists()) {
            return response()->json(['message' => 'Accès non autorisé à cette conversation.'], 403);
        }

        $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        // 2. Récupérer l'autre utilisateur (destinataire)
        $otherUser = $conversation->users()->where('users.id', '!=', $user->id)->first();
        if (!$otherUser) {
            return response()->json(['message' => 'Destinataire introuvable.'], 404);
        }

        // 3. Création du message (Le message est PERSISTANT)
        $message = Message::create([
            'user_id' => $user->id,
            'recever_id' => $otherUser->id,
            'content' => $request->content,
        ]);

        // 4. Mise à jour et événements
        $conversation->touch(); // Met à jour 'updated_at' de la conversation
        
        // 🛑 CORRECTION : Charge la relation 'user' (l'expéditeur) pour l'objet Message avant de le renvoyer.
        $message->load('user'); 

       

        // 5. Retourne le message créé et chargé
        return response()->json($message, 201);
    }
}