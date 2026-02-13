<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DailyReportNotification extends Notification
{
    public function __construct(
        protected array $data
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $appName = config('app.name', 'ManageX');
        $d = $this->data;

        $mail = (new MailMessage)
            ->subject("{$appName} - Rapport quotidien du {$d['date']}")
            ->greeting('Bonjour' . (isset($notifiable->name) ? " {$notifiable->name}" : '') . ',')
            ->line("Voici le compte rendu de la journée du **{$d['date']}**.");

        // Résumé en chiffres
        $pendingTasksCount = $d['pending_tasks']->count();
        $awaitingValidationCount = $d['tasks_awaiting_validation']->count();
        $pendingEvalsCount = $d['pending_intern_evals'] instanceof \Illuminate\Support\Collection ? $d['pending_intern_evals']->count() : 0;

        $mail->line('---');
        $mail->line("**Résumé de la journée**");
        $mail->line("Employés attendus : **{$d['total_scheduled']}** | Présents : **{$d['total_presents']}** | Absents : **{$d['total_absents']}**");

        if ($d['total_late'] > 0) {
            $mail->line("Retards : **{$d['total_late']}** employé(s) pour un total de **{$d['total_late_minutes']} minutes**");
        }

        if ($pendingTasksCount > 0 || $awaitingValidationCount > 0) {
            $mail->line("Tâches : **{$pendingTasksCount}** en attente | **{$awaitingValidationCount}** à valider");
        }

        if ($pendingEvalsCount > 0) {
            $mail->line("Évaluations stagiaires en attente : **{$pendingEvalsCount}**");
        }

        // Détail des présences
        if (! empty($d['presents'])) {
            $mail->line('---');
            $mail->line("**Présences ({$d['total_presents']})**");
            foreach ($d['presents'] as $p) {
                $line = "- {$p['name']} : arrivée {$p['check_in']}, départ {$p['check_out']}";
                if (! empty($p['late_info'])) {
                    $line .= $p['late_info'];
                }
                $mail->line($line);
            }
        }

        // Détail des absences
        if (! empty($d['absents'])) {
            $mail->line('---');
            $mail->line("**Absences ({$d['total_absents']})**");
            foreach ($d['absents'] as $a) {
                $mail->line("- {$a['name']} : {$a['reason']}");
            }
        }

        // Retards détaillés
        if (! empty($d['late_details'])) {
            $mail->line('---');
            $mail->line("**Détail des retards**");
            foreach ($d['late_details'] as $l) {
                $mail->line("- {$l['name']} : {$l['minutes']} min de retard");
            }
        }

        // Tâches en attente de validation
        if ($d['pending_tasks']->isNotEmpty()) {
            $mail->line('---');
            $mail->line("**Tâches en attente de validation ({$d['pending_tasks']->count()})**");
            foreach ($d['pending_tasks'] as $t) {
                $mail->line("- {$t['title']} - {$t['employee']} | Priorité: {$t['priority']} | Échéance: {$t['deadline']}");
            }
        }

        // Tâches terminées en attente de validation finale
        if ($d['tasks_awaiting_validation']->isNotEmpty()) {
            $mail->line('---');
            $mail->line("**Tâches terminées à valider ({$d['tasks_awaiting_validation']->count()})**");
            foreach ($d['tasks_awaiting_validation'] as $t) {
                $mail->line("- {$t['title']} - {$t['employee']} (terminée le {$t['completed_at']})");
            }
        }

        // Tâches terminées aujourd'hui
        if ($d['completed_tasks']->isNotEmpty()) {
            $mail->line('---');
            $mail->line("**Tâches terminées aujourd'hui ({$d['completed_tasks']->count()})**");
            foreach ($d['completed_tasks'] as $t) {
                $mail->line("- {$t['title']} ({$t['employee']})");
            }
        }

        // Nouvelles demandes de congés
        if ($d['new_leave_requests']->isNotEmpty()) {
            $mail->line('---');
            $mail->line("**Nouvelles demandes de congés ({$d['new_leave_requests']->count()})**");
            foreach ($d['new_leave_requests'] as $l) {
                $mail->line("- {$l['employee']} : {$l['type']} du {$l['dates']} ({$l['status']})");
            }
        }

        // Congés en attente
        if ($d['pending_leaves']->isNotEmpty()) {
            $mail->line('---');
            $mail->line("**Congés en attente d'approbation ({$d['pending_leaves']->count()})**");
            foreach ($d['pending_leaves'] as $l) {
                $mail->line("- {$l['employee']} : {$l['type']} du {$l['dates']}");
            }
        }

        // Nouveaux employés / invitations
        if ($d['new_employees']->isNotEmpty()) {
            $mail->line('---');
            $mail->line("**Nouveaux employés ({$d['new_employees']->count()})**");
            foreach ($d['new_employees'] as $e) {
                $mail->line("- {$e->name} ({$e->email})");
            }
        }

        if ($d['new_invitations']->isNotEmpty()) {
            $mail->line('---');
            $mail->line("**Invitations envoyées ({$d['new_invitations']->count()})**");
            foreach ($d['new_invitations'] as $i) {
                $status = $i->isCompleted() ? 'Complétée' : ($i->isExpired() ? 'Expirée' : 'En attente');
                $mail->line("- {$i->name} ({$i->email}) - {$status}");
            }
        }

        // Évaluations stagiaires en attente
        if ($d['pending_intern_evals'] instanceof \Illuminate\Support\Collection && $d['pending_intern_evals']->isNotEmpty()) {
            $mail->line('---');
            $mail->line("**Évaluations stagiaires en attente ({$d['pending_intern_evals']->count()})**");
            foreach ($d['pending_intern_evals'] as $e) {
                $mail->line("- {$e['intern']} (Tuteur: {$e['tutor']}) - {$e['status']}");
            }
        }

        $mail->line('---');
        $mail->action('Accéder au tableau de bord', route('admin.dashboard'));
        $mail->salutation("Cordialement,\nL'équipe {$appName}");

        return $mail;
    }
}
