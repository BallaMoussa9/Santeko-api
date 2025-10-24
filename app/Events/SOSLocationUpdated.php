<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SOSLocationUpdated implements ShouldBroadcast
{
    public $sos;

    public function __construct($sos)
    {
        $this->sos = $sos;
    }

    public function broadcastOn()
    {
        return ['urgentistes.sos'];
    }

    public function broadcastAs()
    {
        return 'sos.location.updated';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->sos->id,
            'location' => $this->sos->location,
            'updated_at' => now()->toDateTimeString(),
        ];
    }
}
