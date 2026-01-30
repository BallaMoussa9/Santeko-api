<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue; 
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels; 

class MailForUser extends Mailable 
{
    use Queueable , SerializesModels;

    // On utilise un tableau pour plus de flexibilité
    public $details;

    /**
     * On change le constructeur pour accepter le tableau $details 
     * envoyé depuis le contrôleur.
     */
    public function __construct(array $details)
    {
        $this->details = $details;
    }

    public function build()
    {
        // On récupère les données via les clés du tableau
        return $this->subject($this->details['subject'] ?? 'Notification SanTeko')
                    ->html("
                        <h2 style='color:#0040d0;'>Notification - " . ($this->details['type'] ?? 'Info') . "</h2>
                        <p><strong>Sujet :</strong> " . ($this->details['subject'] ?? '') . "</p>
                        <p>" . ($this->details['message'] ?? '') . "</p>
                        <hr>
                        <p style='font-size:12px;color:#888;'>Ce message est généré automatiquement par SanTeko.</p>
                    ");
    }
}