<?php

namespace App\Events;

use App\Models\User; // Le patient qui envoie le SOS
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel; // Ou PublicChannel si tous les urgentistes l'écoutent
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SOSMessageSent implements ShouldBroadcast
{
    public $user;
    public $message;
    public $location;

    public function __construct($user, $message, $location)
    {
        $this->user = $user;
        $this->message = $message;
        $this->location = $location;
    }

    public function broadcastOn()
    {
        return ['urgentistes.sos'];
    }

    public function broadcastAs()
    {
        return 'sos.received';
    }
}

