<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\OneSignalService;
use Illuminate\Console\Command;

/**
 * Commande temporaire pour tester les notifications OneSignal.
 * Usage: php artisan test:onesignal {userId}
 */
class TestOneSignalNotifications extends Command
{
    protected $signature = 'test:onesignal {userId : ID utilisateur} {--type= : Type de notification a tester (all, checkin, task, leave, message, payroll, survey, eval, document, welcome, late)}';

    protected $description = 'Tester les notifications OneSignal pour chaque type';

    public function handle(): int
    {
        $userId = $this->argument('userId');
        $user = User::find($userId);

        if (! $user) {
            $this->error("Utilisateur #{$userId} non trouve.");
            return 1;
        }

        $this->info("Test OneSignal pour: {$user->name} (ID: {$user->id})");
        $this->newLine();

        $onesignal = app(OneSignalService::class);
        $type = $this->option('type') ?? 'all';

        $notifications = $this->getNotifications($type);

        if (empty($notifications)) {
            $this->error("Type '{$type}' non reconnu.");
            $this->line('Types disponibles: all, checkin, task, leave, message, payroll, survey, eval, document, welcome, late');
            return 1;
        }

        $results = [];
        foreach ($notifications as $key => $notif) {
            $this->line("Envoi: {$notif['title']}...");

            $result = $onesignal->sendToUser(
                userId: $user->id,
                title: $notif['title'],
                body: $notif['body'],
                data: ['type' => $key],
                url: $notif['url'] ?? null
            );

            $status = $result ? '  Envoye' : '  Echec';
            $this->line($status);
            $results[$key] = $result;

            // Pause 2s entre chaque pour eviter le rate limit
            if (count($notifications) > 1) {
                sleep(2);
            }
        }

        $this->newLine();
        $this->info('=== Resultats ===');

        $success = 0;
        $failed = 0;
        foreach ($results as $key => $result) {
            $icon = $result ? 'OK' : 'FAIL';
            $label = $notifications[$key]['title'];
            $this->line("  [{$icon}] {$label}");
            $result ? $success++ : $failed++;
        }

        $this->newLine();
        $this->info("Total: {$success} OK, {$failed} echec(s)");

        return 0;
    }

    private function getNotifications(string $type): array
    {
        $all = [
            'checkin' => [
                'title' => 'Rappel de pointage',
                'body' => 'Il est 08:00. Marquez votre presence SVP.',
                'url' => route('employee.presences.index'),
            ],
            'task_assigned' => [
                'title' => 'Nouvelle tache assignee',
                'body' => 'Une nouvelle tache "Rapport mensuel" vous a ete assignee.',
                'url' => null,
            ],
            'task_status' => [
                'title' => 'Mise a jour tache',
                'body' => 'Votre tache "Rapport mensuel" a ete approuvee.',
                'url' => null,
            ],
            'task_reminder' => [
                'title' => 'Rappel tache',
                'body' => 'La tache "Rapport mensuel" est due demain.',
                'url' => null,
            ],
            'leave_request' => [
                'title' => 'Demande de conge',
                'body' => 'Nouvelle demande de conge de Jean Dupont.',
                'url' => null,
            ],
            'leave_status' => [
                'title' => 'Statut de conge',
                'body' => 'Votre demande de conge a ete approuvee.',
                'url' => null,
            ],
            'late' => [
                'title' => 'Retard signale',
                'body' => 'Jean Dupont est arrive en retard de 15 min.',
                'url' => route('admin.presences.index'),
            ],
            'message' => [
                'title' => 'Nouveau message',
                'body' => 'Nouveau message de Jean Dupont.',
                'url' => null,
            ],
            'payroll' => [
                'title' => 'Fiche de paie',
                'body' => 'Nouvelle fiche de paie disponible pour Janvier 2026.',
                'url' => route('employee.payrolls.index'),
            ],
            'survey' => [
                'title' => 'Nouveau sondage',
                'body' => 'Nouveau sondage "Satisfaction employes" disponible.',
                'url' => null,
            ],
            'eval' => [
                'title' => 'Nouvelle evaluation',
                'body' => 'Votre tuteur a soumis votre evaluation hebdomadaire.',
                'url' => null,
            ],
            'document' => [
                'title' => 'Demande de document',
                'body' => 'Votre attestation de travail est prete.',
                'url' => route('employee.document-requests.index'),
            ],
            'welcome' => [
                'title' => 'Bienvenue sur ManageX',
                'body' => 'Votre compte a ete cree. Bienvenue dans l equipe.',
                'url' => null,
            ],
        ];

        if ($type === 'all') {
            return $all;
        }

        // Map simple types to keys
        $map = [
            'checkin' => ['checkin'],
            'task' => ['task_assigned', 'task_status', 'task_reminder'],
            'leave' => ['leave_request', 'leave_status'],
            'message' => ['message'],
            'payroll' => ['payroll'],
            'survey' => ['survey'],
            'eval' => ['eval'],
            'document' => ['document'],
            'welcome' => ['welcome'],
            'late' => ['late'],
        ];

        if (isset($map[$type])) {
            return array_intersect_key($all, array_flip($map[$type]));
        }

        // Direct key match
        if (isset($all[$type])) {
            return [$type => $all[$type]];
        }

        return [];
    }
}
