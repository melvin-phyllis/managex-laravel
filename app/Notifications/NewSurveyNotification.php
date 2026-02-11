<?php

namespace App\Notifications;

use App\Models\Survey;
use App\Notifications\Traits\SendsOneSignal;
use App\Notifications\Traits\SendsWebPush;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewSurveyNotification extends Notification implements ShouldQueue
{
    use Queueable, SendsWebPush, SendsOneSignal;

    protected Survey $survey;

    /**
     * Create a new notification instance.
     */
    public function __construct(Survey $survey)
    {
        $this->survey = $survey;
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
        $mail = (new MailMessage)
            ->subject('Nouveau sondage disponible - ManageX')
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line('Un nouveau sondage "'.$this->survey->titre.'" est disponible et attend votre participation.');

        if ($this->survey->date_limite) {
            $mail->line('Date limite de participation : '.$this->survey->date_limite->format('d/m/Y'));
        }

        return $mail
            ->action('Participer au sondage', route('employee.surveys.show', $this->survey))
            ->line('Merci de prendre quelques minutes pour y répondre !');
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
            'type' => 'new_survey',
            'survey_id' => $this->survey->id,
            'survey_titre' => $this->survey->titre,
            'date_limite' => $this->survey->date_limite?->format('d/m/Y'),
            'message' => 'Nouveau sondage disponible : "'.$this->survey->titre.'".',
            'url' => route('employee.surveys.show', $this->survey),
        ];
    }
}
