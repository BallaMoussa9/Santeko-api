<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
class MailForUser extends Mailable
{
    use Queueable, SerializesModels;

    public $type;
    public $subjectText;
    public $messageText;

    public function __construct($type, $subjectText, $messageText)
    {
        $this->type        = $type;
        $this->subjectText = $subjectText;
        $this->messageText = $messageText;
    }

    public function build()
    {
        return $this->subject($this->subjectText)
                    ->html("
                        <h2 style='color:#0040d0;'>Notification - {$this->type}</h2>
                        <p><strong>Sujet :</strong> {$this->subjectText}</p>
                        <p>{$this->messageText}</p>
                        <hr>
                        <p style='font-size:12px;color:#888;'>Ce message est généré automatiquement par SanTeko.</p>
                    ");
    }
}
