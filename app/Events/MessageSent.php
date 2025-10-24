<?php

namespace App\Events;

use App\Models\Message;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $message;
    public User $sender;

    public function __construct(Message $message)
    {
        $this->message = $message->load('user', 'conversation'); // Charge l'expéditeur et la conversation pour la diffusion
        $this->sender = $message->user; // L'expéditeur est le user du message
    }

    /**
     * Les canaux sur lesquels l'événement doit être diffusé.
     * Le message doit être diffusé à tous les participants de la conversation.
     * Chaque participant écoutera le canal privé de la conversation 'conversations.{id}'.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('conversations.' . $this->message->conversation->id),
        ];
    }

    /**
     * Le nom de l'événement diffusé.
     */
    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    /**
     * Les données à diffuser avec l'événement.
     * C'est ce que le frontend recevra en temps réel.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'user_id' => $this->message->user_id,
            'user_name' => $this->sender->first_name . ' ' . $this->sender->last_name, // Assurez-vous que User a first_name/last_name
            'content' => $this->message->content,
            'created_at' => $this->message->created_at->toDateTimeString(),
        ];
    }
}