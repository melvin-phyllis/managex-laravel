<?php

namespace App\Notifications;

use App\Models\DemoRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DemoRequestNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected DemoRequest $demoRequest
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $size = match ($this->demoRequest->company_size) {
            '1-10' => '1 à 10 employés',
            '11-50' => '11 à 50 employés',
            '51-200' => '51 à 200 employés',
            '200+' => 'Plus de 200 employés',
            default => $this->demoRequest->company_size,
        };

        $mail = (new MailMessage)
            ->subject('Nouvelle demande de démo — ' . $this->demoRequest->company_name)
            ->greeting('Nouvelle demande de démonstration')
            ->line("**Entreprise :** {$this->demoRequest->company_name}")
            ->line("**Contact :** {$this->demoRequest->contact_name}")
            ->line("**Email :** {$this->demoRequest->email}")
            ->line("**Taille :** {$size}");

        if ($this->demoRequest->phone) {
            $mail->line("**Téléphone :** {$this->demoRequest->phone}");
        }

        if ($this->demoRequest->message) {
            $mail->line("**Message :**")
                ->line($this->demoRequest->message);
        }

        $mail->line("---")
            ->line("Reçue le " . $this->demoRequest->created_at->translatedFormat('l d F Y à H:i'))
            ->salutation('— ManageX');

        return $mail;
    }
}
