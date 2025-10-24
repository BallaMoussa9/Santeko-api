<?php

namespace App\Notifications;

use App\Models\Message;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Message $message;
    public User $sender;

    public function __construct(Message $message)
    {
        $this->message = $message->load('user', 'conversation');
        $this->sender = $message->user;
    }

    /**
     * Obtenez les canaux de notification.
     * Nous voulons stocker cette notification en base de données.
     */
    public function via(object $notifiable): array
    {
        return ['database']; // Peut inclure 'mail' si vous voulez aussi un email
    }

    /**
     * Obtenez la représentation de la notification stockée dans la base de données.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'conversation_id' => $this->message->conversation_id,
            'sender_id' => $this->sender->id,
            'sender_name' => $this->sender->first_name . ' ' . $this->sender->last_name,
            'message_id' => $this->message->id,
            'message_content' => $this->message->content,
            'title' => 'Nouveau message de ' . $this->sender->first_name,
            'message' => $this->message->content,
            'type' => 'new_message',
            'url' => '/chat/' . $this->message->conversation_id, // Lien vers la conversation
        ];
    }

    /*
    // Exemple si vous voulez envoyer par email
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('Vous avez reçu un nouveau message de ' . $this->sender->first_name . ' ' . $this->sender->last_name . '.')
                    ->action('Voir le message', url('/chat/' . $this->message->conversation_id))
                    ->line('Contenu: ' . $this->message->content);
    }
    */
}