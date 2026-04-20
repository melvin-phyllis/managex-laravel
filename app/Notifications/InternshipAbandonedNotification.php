<?php

namespace App\Notifications;

use App\Mail\InternshipAbandonedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class InternshipAbandonedNotification extends Notification
{
    use Queueable;

    public $daysOfAbsence;

    /**
     * Create a new notification instance.
     */
    public function __construct(int $daysOfAbsence)
    {
        $this->daysOfAbsence = $daysOfAbsence;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): InternshipAbandonedMail
    {
        return (new InternshipAbandonedMail($notifiable, $this->daysOfAbsence))
            ->to($notifiable->email);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'internship_abandoned',
            'message' => 'Votre stage a été marqué comme abandonné suite à une absence prolongée.',
            'days_of_absence' => $this->daysOfAbsence,
            'statut' => 'abandoned',
        ];
    }
}
