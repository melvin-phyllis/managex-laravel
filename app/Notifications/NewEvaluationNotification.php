<?php

namespace App\Notifications;

use App\Models\InternEvaluation;
use App\Notifications\Traits\SendsOneSignal;
use App\Notifications\Traits\SendsWebPush;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewEvaluationNotification extends Notification implements ShouldQueue
{
    use Queueable, SendsWebPush, SendsOneSignal;

    protected InternEvaluation $evaluation;

    /**
     * Create a new notification instance.
     */
    public function __construct(InternEvaluation $evaluation)
    {
        $this->evaluation = $evaluation->load(['tutor']);
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
        $grade = $this->evaluation->grade_letter;
        $gradeInfo = InternEvaluation::GRADES[$grade];
        $score = $this->evaluation->total_score;

        return (new MailMessage)
            ->subject('Nouvelle évaluation hebdomadaire disponible - ManageX')
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line('Votre tuteur **'.$this->evaluation->tutor->name.'** a soumis votre évaluation hebdomadaire.')
            ->line('**'.$this->evaluation->week_label.'**')
            ->line('')
            ->line('📊 **Résumé de votre évaluation :**')
            ->line('- Note globale : **'.$score.'/10** (Grade '.$grade.' - '.$gradeInfo['label'].')')
            ->line('- Discipline : '.$this->evaluation->discipline_score.'/2.5')
            ->line('- Comportement : '.$this->evaluation->behavior_score.'/2.5')
            ->line('- Compétences : '.$this->evaluation->skills_score.'/2.5')
            ->line('- Communication : '.$this->evaluation->communication_score.'/2.5')
            ->line('')
            ->action('Voir le détail de mon évaluation', route('employee.evaluations.show', $this->evaluation->id))
            ->line('Continuez vos efforts et n\'hésitez pas à discuter avec votre tuteur si vous avez des questions.')
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

        return [
            'type' => 'new_evaluation',
            'evaluation_id' => $this->evaluation->id,
            'tutor_id' => $this->evaluation->tutor_id,
            'tutor_name' => $this->evaluation->tutor->name,
            'week_start' => $this->evaluation->week_start->toDateString(),
            'week_label' => $this->evaluation->week_label,
            'total_score' => $this->evaluation->total_score,
            'grade' => $this->evaluation->grade_letter,
            'message' => 'Nouvelle évaluation : '.$this->evaluation->total_score.'/10 ('.$this->evaluation->week_label.')',
            'url' => route('employee.evaluations.show', $this->evaluation->id),
        ];
    }
}
