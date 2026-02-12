<?php

namespace App\Notifications;

use App\Models\EmployeeInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmployeeInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public EmployeeInvitation $invitation
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $appName = config('app.name');
        $url = route('invitation.show', $this->invitation->token);
        $expiresAt = $this->invitation->expires_at->format('d/m/Y à H:i');

        return (new MailMessage)
            ->subject("Invitation à rejoindre {$appName}")
            ->greeting("Bonjour {$this->invitation->name},")
            ->line("Vous êtes invité(e) à rejoindre la plateforme {$appName} en tant que **{$this->invitation->poste}**.")
            ->line('Veuillez compléter votre profil en cliquant sur le bouton ci-dessous :')
            ->action('Compléter mon profil', $url)
            ->line("Ce lien expirera le **{$expiresAt}**.")
            ->line('Si vous n\'êtes pas concerné(e) par cette invitation, vous pouvez ignorer cet email.')
            ->salutation("Cordialement,\nL'équipe {$appName}");
    }
}
