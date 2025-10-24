<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewAppointmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Appointment $appointment;
    public string $forRole; // 'patient', 'doctor', 'admin'

    public function __construct(Appointment $appointment, string $forRole)
    {
        $this->appointment = $appointment;
        $this->forRole = $forRole;
    }

    public function via(object $notifiable): array
    {
        return ['database']; // Canal pour persister en DB
    }

    public function toDatabase(object $notifiable): array
    {
        $message = '';
        if ($this->forRole === 'patient') {
            $message = 'Votre rendez-vous avec Dr. ' . $this->appointment->doctor->user->last_name . ' est planifié pour le ' . $this->appointment->appointment_date->format('d/m/Y') . ' à ' . $this->appointment->appointment_time->format('H:i') . '.';
        } elseif ($this->forRole === 'doctor') {
            $message = 'Nouveau rendez-vous avec ' . $this->appointment->patient->user->first_name . ' ' . $this->appointment->patient->user->last_name . ' le ' . $this->appointment->appointment_date->format('d/m/Y') . ' à ' . $this->appointment->appointment_time->format('H:i') . '.';
        }

        return [
            'appointment_id' => $this->appointment->id,
            'message' => $message,
            'url' => '/appointments/' . $this->appointment->id, // URL où l'utilisateur peut cliquer
            'date' => $this->appointment->appointment_date->format('Y-m-d H:i'),
            'type' => 'appointment_scheduled',
        ];
    }
}
