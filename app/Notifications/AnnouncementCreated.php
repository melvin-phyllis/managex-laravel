<?php

namespace App\Notifications;

use App\Models\Announcement;
use App\Notifications\Traits\SendsOneSignal;
use App\Notifications\Traits\SendsWebPush;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class AnnouncementCreated extends Notification implements ShouldQueue
{
    use Queueable, SendsWebPush, SendsOneSignal;

    protected Announcement $announcement;

    /**
     * Create a new notification instance.
     */
    public function __construct(Announcement $announcement)
    {
        $this->announcement = $announcement;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if ($this->shouldSendWebPush($notifiable)) {
            $channels[] = 'webpush';
        }

        $channels[] = 'mail';

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $typeLabel = match ($this->announcement->type) {
            'urgent' => '⚠️ Annonce Urgente',
            'warning' => 'Important',
            'success' => 'Bonne nouvelle',
            'event' => 'Événement',
            default => 'Information',
        };

        $mail = (new MailMessage)
            ->subject($typeLabel . ' : ' . $this->announcement->title . ' - ManageX')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Une nouvelle annonce a été publiée sur votre espace : **' . $this->announcement->title . '**.');

        if ($this->announcement->priority === 'critical') {
            $mail->line('**Ceci est un message prioritaire.**');
        }

        return $mail
            ->action('Lire l\'annonce', route('employee.announcements.show', $this->announcement))
            ->line('Merci d\'en prendre connaissance.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $this->sendViaOneSignal($notifiable);

        return [
            'type' => 'new_announcement',
            'announcement_id' => $this->announcement->id,
            'title' => $this->announcement->title,
            'message' => 'Nouvelle annonce : "' . Str::limit($this->announcement->title, 50) . '".',
            'url' => route('employee.announcements.show', $this->announcement),
            'icon' => 'speakerphone',
        ];
    }
}
