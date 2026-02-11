<?php

namespace App\Notifications;

use App\Notifications\Traits\SendsOneSignal;
use App\Notifications\Traits\SendsWebPush;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class WeeklyEvaluationReminder extends Notification implements ShouldQueue
{
    use Queueable, SendsWebPush, SendsOneSignal;

    protected Collection $interns;

    /**
     * Create a new notification instance.
     */
    public function __construct(Collection $interns)
    {
        $this->interns = $interns;
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
        $count = $this->interns->count();
        $internNames = $this->interns->pluck('name')->implode(', ');
        $weekLabel = 'Semaine du '.now()->startOfWeek()->format('d/m/Y');

        $message = (new MailMessage)
            ->subject('Rappel : Évaluations hebdomadaires à compléter - ManageX')
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line('C\'est le moment de compléter les évaluations hebdomadaires de vos stagiaires.')
            ->line('**'.$weekLabel.'**')
            ->line('');

        if ($count === 1) {
            $message->line('Stagiaire à évaluer : **'.$internNames.'**');
        } else {
            $message->line('Stagiaires à évaluer ('.$count.') : '.$internNames);
        }

        return $message
            ->action('Évaluer mes stagiaires', route('employee.tutor.evaluations.index'))
            ->line('Merci de compléter les évaluations avant la fin de la journée.')
            ->salutation('Cordialement, L\'équipe ManageX');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $this->sendViaOneSignal($notifiable);

        $count = $this->interns->count();

        return [
            'type' => 'evaluation_reminder',
            'interns_count' => $count,
            'interns' => $this->interns->pluck('name', 'id')->toArray(),
            'week_start' => now()->startOfWeek()->toDateString(),
            'message' => $count === 1
                ? 'Évaluation hebdomadaire à compléter pour '.$this->interns->first()->name
                : $count.' évaluations hebdomadaires à compléter',
            'url' => route('employee.tutor.evaluations.index'),
        ];
    }
}
