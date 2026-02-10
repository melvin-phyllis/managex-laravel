<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Notifications\Traits\SendsWebPush;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class MissingEvaluationAlert extends Notification implements ShouldQueue
{
    use Queueable, SendsWebPush;

    protected Collection $missingInterns;

    protected ?User $tutor;

    protected string $weekLabel;

    /**
     * Create a new notification instance.
     *
     * @param  Collection  $missingInterns  Interns without evaluation for the week
     * @param  User|null  $tutor  The tutor (null if sent to admin about all missing)
     * @param  string  $weekLabel  The week label (e.g., "Semaine du 20/01/2025")
     */
    public function __construct(Collection $missingInterns, ?User $tutor = null, string $weekLabel = '')
    {
        $this->missingInterns = $missingInterns;
        $this->tutor = $tutor;
        $this->weekLabel = $weekLabel ?: 'Semaine du '.now()->subWeek()->startOfWeek()->format('d/m/Y');
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
        $count = $this->missingInterns->count();
        $internNames = $this->missingInterns->pluck('name')->implode(', ');

        $message = (new MailMessage)
            ->subject('Alerte : Évaluations manquantes - ManageX')
            ->greeting('Bonjour '.$notifiable->name.',');

        if ($this->tutor && $notifiable->id === $this->tutor->id) {
            // Message to tutor
            $message->line('Vous n\'avez pas encore soumis les évaluations pour la semaine passée.')
                ->line('**'.$this->weekLabel.'**')
                ->line('');

            if ($count === 1) {
                $message->line('Stagiaire non évalué : **'.$internNames.'**');
            } else {
                $message->line('Stagiaires non évalués ('.$count.') : '.$internNames);
            }

            $message->action('Compléter les évaluations', route('employee.tutor.evaluations.index'));
        } else {
            // Message to admin/HR
            if ($this->tutor) {
                $message->line('Le tuteur **'.$this->tutor->name.'** n\'a pas soumis les évaluations pour :')
                    ->line('**'.$this->weekLabel.'**')
                    ->line('');

                if ($count === 1) {
                    $message->line('Stagiaire concerné : **'.$internNames.'**');
                } else {
                    $message->line('Stagiaires concernés ('.$count.') : '.$internNames);
                }
            } else {
                $message->line('Des évaluations sont manquantes pour la semaine passée.')
                    ->line('**'.$this->weekLabel.'**')
                    ->line('Nombre de stagiaires non évalués : **'.$count.'**');
            }

            $message->action('Voir les évaluations manquantes', route('admin.intern-evaluations.missing'));
        }

        return $message
            ->line('Merci de régulariser la situation rapidement.')
            ->salutation('Cordialement, L\'équipe ManageX');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $count = $this->missingInterns->count();
        $isTutor = $this->tutor && $notifiable->id === $this->tutor->id;

        return [
            'type' => 'missing_evaluation_alert',
            'interns_count' => $count,
            'interns' => $this->missingInterns->pluck('name', 'id')->toArray(),
            'tutor_id' => $this->tutor?->id,
            'tutor_name' => $this->tutor?->name,
            'week_label' => $this->weekLabel,
            'message' => $isTutor
                ? 'Évaluation manquante pour '.($count === 1 ? $this->missingInterns->first()->name : $count.' stagiaires')
                : ($this->tutor
                    ? $this->tutor->name.' n\'a pas évalué '.$count.' stagiaire(s)'
                    : $count.' évaluation(s) manquante(s) cette semaine'),
            'url' => $isTutor
                ? route('employee.tutor.evaluations.index')
                : route('admin.intern-evaluations.missing'),
        ];
    }
}
