<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WeeklyPresenceReportNotification extends Notification
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
            ->subject("{$appName} — Rapport hebdomadaire ({$d['week_label']})")
            ->greeting('Bonjour' . (isset($notifiable->name) ? " {$notifiable->name}" : '') . ',')
            ->line("Voici le rapport de présence de la semaine du **{$d['week_label']}**.");

        // ─── Résumé global ───
        $mail->line('---');
        $mail->line("**📊 Résumé de la semaine**");
        $mail->line("Effectif : **{$d['total_employees']}** employé(s)");
        $mail->line("Total présences : **{$d['total_present']}** | Absences : **{$d['total_absent']}** | Congés : **{$d['total_leave']}**");

        if ($d['total_late_minutes'] > 0) {
            $mail->line("Retards cumulés : **{$d['total_late_minutes']} minutes**");
        }

        // ─── Tableau par employé ───
        $mail->line('---');
        $mail->line("**👥 Détail par employé**");

        // En-tête des jours
        $dayHeaders = implode(' | ', $d['day_labels']);
        $mail->line("_(Colonnes : {$dayHeaders})_");
        $mail->line('');

        foreach ($d['employee_rows'] as $row) {
            $icons = collect($row['days'])->map(fn ($day) => $day['icon'])->implode(' ');
            $summary = "P:{$row['total_present']} A:{$row['total_absent']}";
            if ($row['total_leave'] > 0) {
                $summary .= " C:{$row['total_leave']}";
            }
            if ($row['late_minutes'] > 0) {
                $summary .= " R:{$row['late_minutes']}m";
            }

            $mail->line("**{$row['name']}** ({$row['department']})");
            $mail->line("{$icons} → _{$summary}_");

            // Détails horaires pour les jours présents
            $details = collect($row['days'])
                ->filter(fn ($day) => in_array($day['status'], ['present', 'absent', 'leave']))
                ->map(fn ($day) => $day['detail'])
                ->implode(' | ');

            if ($details) {
                $mail->line("↳ {$details}");
            }

            $mail->line('');
        }

        // ─── Légende ───
        $mail->line('---');
        $mail->line('_Légende : ✅ Présent | ❌ Absent | 🏖️ Congé | ⚠️ Retard | ⏳ À venir | ⬜ Repos_');

        // ─── Tâches terminées cette semaine ───
        if ($d['completed_tasks']->isNotEmpty()) {
            $mail->line('---');
            $mail->line("**✅ Tâches terminées cette semaine ({$d['completed_tasks']->count()})**");
            foreach ($d['completed_tasks'] as $t) {
                $mail->line("- {$t['title']} ({$t['employee']})");
            }
        }

        // ─── Tâches en attente ───
        if ($d['pending_tasks']->isNotEmpty()) {
            $mail->line('---');
            $mail->line("**⏳ Tâches en attente de validation ({$d['pending_tasks']->count()})**");
            foreach ($d['pending_tasks'] as $t) {
                $mail->line("- {$t['title']} — {$t['employee']} | Priorité: {$t['priority']} | Échéance: {$t['deadline']}");
            }
        }

        // ─── Congés en attente ───
        if ($d['pending_leaves']->isNotEmpty()) {
            $mail->line('---');
            $mail->line("**📋 Congés en attente d'approbation ({$d['pending_leaves']->count()})**");
            foreach ($d['pending_leaves'] as $l) {
                $mail->line("- {$l['employee']} : {$l['type']} du {$l['dates']}");
            }
        }

        $mail->line('---');
        $mail->action('Accéder au tableau de bord', route('admin.dashboard'));
        $mail->salutation("Cordialement,\nL'équipe {$appName}");

        return $mail;
    }
}
