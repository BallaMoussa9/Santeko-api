<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SOSAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $message;
    public ?array $location;

    public function __construct(string $message, ?array $location = null)
    {
        $this->message = $message;
        $this->location = $location;
    }

    public function via(object $notifiable): array
    {
        return ['database']; // Canal pour persister en DB
    }

    public function toDatabase(object $notifiable): array
    {
        $baseMessage = 'Votre alerte SOS a été envoyée aux services d\'urgence. Restez calme et attendez de l\'aide.';
        if ($this->location) {
            $baseMessage .= ' Localisation partagée: Lat ' . $this->location['latitude'] . ', Lon ' . $this->location['longitude'] . '.';
        }

        return [
            'title' => 'ALERTE SOS URGENTE !',
            'message' => $baseMessage,
            'type' => 'sos_alert',
            'location' => $this->location,
            'url' => '/dashboard/sos-history', // Ou une URL pertinente pour l'historique SOS
        ];
    }
}