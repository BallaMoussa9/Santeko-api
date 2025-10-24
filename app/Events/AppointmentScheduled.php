<?php

namespace App\Events;

use App\Models\Appointment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AppointmentScheduled implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Appointment $appointment;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * Les canaux sur lesquels l'événement doit être diffusé.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('users.' . $this->appointment->patient->user->id),
            new PrivateChannel('users.' . $this->appointment->doctor->user->id),
            // new PrivateChannel('admin-notifications'), // Optionnel pour les admins
        ];
    }

    /**
     * Le nom de l'événement diffusé.
     */
    public function broadcastAs(): string
    {
        return 'appointment.scheduled';
    }

    /**
     * Les données à diffuser avec l'événement.
     */
    public function broadcastWith(): array
    {
        return [
            'appointment_id' => $this->appointment->id,
            'patient_name' => $this->appointment->patient->user->first_name . ' ' . $this->appointment->patient->user->last_name,
            'doctor_name' => 'Dr. ' . $this->appointment->doctor->user->first_name . ' ' . $this->appointment->doctor->user->last_name,
            'date' => $this->appointment->appointment_date->format('Y-m-d') . ' ' . $this->appointment->appointment_time->format('H:i'),
            'reason' => $this->appointment->motif,
            'message' => 'Un nouveau rendez-vous a été planifié.',
            'type' => 'appointment_scheduled',
        ];
    }
}
