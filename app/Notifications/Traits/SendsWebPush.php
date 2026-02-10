<?php

namespace App\Notifications\Traits;

use NotificationChannels\WebPush\WebPushMessage;

trait SendsWebPush
{
    public function toWebPush(object $notifiable, $notification): WebPushMessage
    {
        $data = method_exists($this, 'toDatabase')
            ? $this->toDatabase($notifiable)
            : $this->toArray($notifiable);

        $type = $data['type'] ?? 'notification';
        $title = $data['title'] ?? $this->getWebPushTitle($data);
        $body = $data['message'] ?? 'Nouvelle notification ManageX';
        $url = $data['url'] ?? '/';

        return (new WebPushMessage)
            ->title($title)
            ->body($body)
            ->action('Voir', 'open_url')
            ->options([
                'TTL' => 86400,
                'urgency' => $this->getWebPushUrgency($type),
            ])
            ->data([
                'url' => $url,
                'type' => $type,
            ])
            ->tag($type)
            ->renotify();
    }

    protected function getWebPushTitle(array $data): string
    {
        $type = $data['type'] ?? 'notification';

        $titles = [
            'leave_request' => 'Demande de conge',
            'leave_status' => 'Statut de conge',
            'task_assigned' => 'Nouvelle tache',
            'task_status' => 'Mise a jour tache',
            'task_reminder' => 'Rappel tache',
            'late_arrival' => 'Retard signale',
            'new_message' => 'Nouveau message',
            'payroll_added' => 'Fiche de paie',
            'new_survey' => 'Nouveau sondage',
            'new_evaluation' => 'Nouvelle evaluation',
            'missing_evaluation_alert' => 'Evaluations manquantes',
            'evaluation_reminder' => 'Rappel evaluations',
            'document_request_status' => 'Demande de document',
            'welcome' => 'Bienvenue !',
        ];

        return $titles[$type] ?? 'ManageX';
    }

    protected function getWebPushUrgency(string $type): string
    {
        $highUrgency = ['task_assigned', 'late_arrival', 'missing_evaluation_alert', 'leave_request'];
        $normalUrgency = ['payroll_added', 'new_message', 'task_status', 'leave_status'];

        if (in_array($type, $highUrgency)) {
            return 'high';
        }

        if (in_array($type, $normalUrgency)) {
            return 'normal';
        }

        return 'normal';
    }

    protected function shouldSendWebPush(object $notifiable): bool
    {
        return method_exists($notifiable, 'pushSubscriptions')
            && $notifiable->pushSubscriptions()->exists();
    }
}
