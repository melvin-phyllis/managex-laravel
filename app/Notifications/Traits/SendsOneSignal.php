<?php

namespace App\Notifications\Traits;

use App\Services\OneSignalService;
use Illuminate\Support\Facades\Log;

/**
 * Trait to send push notifications via OneSignal REST API.
 * This reaches users even when their browser is completely closed.
 *
 * Usage: Add `use SendsOneSignal;` to any notification class that has a `toDatabase()` method.
 * The trait will automatically extract title, message, and url from the database payload.
 */
trait SendsOneSignal
{
    /**
     * Send notification via OneSignal REST API.
     * Call this from toDatabase() to piggyback on existing notification flow.
     */
    protected function sendViaOneSignal(object $notifiable, ?array $data = null): void
    {
        try {
            // Get the notification data
            if ($data === null) {
                $data = method_exists($this, 'toDatabase')
                    ? $this->toDatabase($notifiable)
                    : ($this->toArray($notifiable) ?? []);
            }

            $title = $data['title'] ?? $this->getOneSignalTitle($data);
            $body = $data['message'] ?? 'Nouvelle notification ManageX';
            $url = $data['url'] ?? null;
            $type = $data['type'] ?? 'notification';

            $onesignal = app(OneSignalService::class);

            $onesignal->sendToUser(
                userId: $notifiable->id,
                title: $title,
                body: $body,
                data: ['type' => $type],
                url: $url
            );
        } catch (\Exception $e) {
            Log::error('[OneSignal] Failed in notification: '.$e->getMessage());
        }
    }

    /**
     * Get a title from the notification type.
     */
    protected function getOneSignalTitle(array $data): string
    {
        $type = $data['type'] ?? 'notification';

        $titles = [
            'check_in_reminder' => '⏰ Rappel de pointage',
            'leave_request' => '📋 Demande de congé',
            'leave_status' => '📋 Statut de congé',
            'task_assigned' => '📝 Nouvelle tâche',
            'task_status' => '📝 Mise à jour tâche',
            'task_reminder' => '⏰ Rappel tâche',
            'late_arrival' => '⚠️ Retard signalé',
            'new_message' => '💬 Nouveau message',
            'payroll_added' => '💰 Fiche de paie',
            'new_survey' => '📊 Nouveau sondage',
            'new_evaluation' => '📝 Nouvelle évaluation',
            'missing_evaluation_alert' => '⚠️ Évaluations manquantes',
            'evaluation_reminder' => '📝 Rappel évaluations',
            'document_request_status' => '📄 Demande de document',
            'welcome' => '👋 Bienvenue !',
        ];

        return $titles[$type] ?? '🔔 ManageX';
    }
}
