<?php

namespace App\Notifications;

use App\Models\DemoRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccessRequestStatusNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected DemoRequest $request,
        protected string $status
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $isApproved = $this->status === 'approved';
        $isRejected = $this->status === 'rejected';
        $isNeedsInfo = $this->status === 'needs_info';

        $subject = match (true) {
            $isApproved => 'Demande d’accès approuvée',
            $isNeedsInfo => 'Infos requises pour votre demande d’accès',
            $isRejected => 'Demande d’accès refusée',
            default => 'Mise à jour de votre demande d’accès',
        };

        $mail = (new MailMessage)
            ->subject($subject . ' — ManageX')
            ->greeting('Bonjour ' . ($this->request->contact_name ?: ''))
            ->line("Votre demande d’accès pour **{$this->request->company_name}** a été " . ($isApproved ? '**approuvée**.' : ($isNeedsInfo ? 'mise en attente (**informations requises**).' : ($isRejected ? '**refusée**.' : 'mise à jour.'))));

        if (! empty($this->request->admin_note)) {
            $mail->line('---')
                ->line('**Note de YA Consulting :**')
                ->line($this->request->admin_note);
        }

        if ($isApproved) {
            $mail->line('---')
                ->line('Prochaine étape : notre équipe va vous contacter pour finaliser l’activation de votre accès.');
        } elseif ($isNeedsInfo) {
            $mail->line('---')
                ->line("Merci de répondre à cet email avec les informations demandées ci-dessus afin que nous puissions traiter votre demande.");
        } else {
            $mail->line('---')
                ->line('Si vous pensez qu’il s’agit d’une erreur, vous pouvez répondre à cet email ou refaire une demande avec plus de détails.');
        }

        return $mail->salutation('— ManageX / YA Consulting');
    }
}

