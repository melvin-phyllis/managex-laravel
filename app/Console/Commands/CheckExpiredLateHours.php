<?php

namespace App\Console\Commands;

use App\Models\LatePenaltyAbsence;
use App\Models\Presence;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckExpiredLateHours extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'presence:check-expired-late 
                            {--dry-run : Simuler sans appliquer les changements}
                            {--user= : Traiter uniquement un utilisateur spécifique}';

    /**
     * The console command description.
     */
    protected $description = 'Vérifie les heures de retard expirées et applique les pénalités si nécessaire';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Vérifier si le système de pénalité est activé
        if (! Setting::get('late_penalty_enabled', true)) {
            $this->info('Le système de pénalité est désactivé.');

            return Command::SUCCESS;
        }

        $dryRun = $this->option('dry-run');
        $specificUserId = $this->option('user');

        $recoveryDays = Setting::get('late_recovery_days', 7);
        $penaltyThreshold = Setting::get('late_penalty_threshold_minutes', 480); // 8h par défaut

        $this->info('Configuration:');
        $this->info("- Délai de rattrapage: {$recoveryDays} jours");
        $this->info("- Seuil de pénalité: {$penaltyThreshold} minutes (".round($penaltyThreshold / 60, 1).'h)');
        $this->info('- Mode: '.($dryRun ? 'SIMULATION' : 'PRODUCTION'));
        $this->newLine();

        // Étape 1: Marquer les retards expirés
        $expiredCount = $this->markExpiredLateHours($recoveryDays, $dryRun, $specificUserId);
        $this->info("Retards marqués comme expirés: {$expiredCount}");

        // Étape 2: Vérifier les pénalités à appliquer
        $penaltiesCount = $this->applyPenalties($penaltyThreshold, $dryRun, $specificUserId);
        $this->info("Absences pénalités créées: {$penaltiesCount}");

        if ($dryRun) {
            $this->warn('Mode simulation - Aucune modification effectuée.');
        }

        return Command::SUCCESS;
    }

    /**
     * Marquer les retards dont le délai de rattrapage est dépassé
     */
    protected function markExpiredLateHours(int $recoveryDays, bool $dryRun, ?string $userId): int
    {
        $deadlineDate = Carbon::today()->subDays($recoveryDays);

        $query = Presence::where('is_late', true)
            ->where('is_late_expired', false)
            ->whereNotNull('late_minutes')
            ->where('late_minutes', '>', 0)
            ->where('date', '<=', $deadlineDate);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $presences = $query->get();
        $count = 0;

        foreach ($presences as $presence) {
            // Calculer les minutes non rattrapées
            $unrecoveredMinutes = max(0, $presence->late_minutes - $presence->recovery_minutes);

            if ($unrecoveredMinutes > 0) {
                $this->line("  - Présence #{$presence->id} ({$presence->date->format('d/m/Y')}): {$unrecoveredMinutes} min non rattrapées");

                if (! $dryRun) {
                    $presence->update([
                        'is_late_expired' => true,
                        'expired_late_minutes' => $unrecoveredMinutes,
                    ]);
                }

                $count++;
            }
        }

        return $count;
    }

    /**
     * Appliquer les pénalités pour les utilisateurs ayant dépassé le seuil
     */
    protected function applyPenalties(int $thresholdMinutes, bool $dryRun, ?string $userId): int
    {
        // Récupérer les utilisateurs avec des retards expirés non traités
        $query = User::where('role', 'employee')
            ->whereHas('presences', function ($q) {
                $q->where('is_late_expired', true)
                    ->where('expired_late_minutes', '>', 0);
            });

        if ($userId) {
            $query->where('id', $userId);
        }

        $users = $query->get();
        $penaltiesCreated = 0;

        foreach ($users as $user) {
            $penaltiesCreated += $this->checkUserPenalty($user, $thresholdMinutes, $dryRun);
        }

        return $penaltiesCreated;
    }

    /**
     * Vérifier et appliquer la pénalité pour un utilisateur
     */
    protected function checkUserPenalty(User $user, int $thresholdMinutes, bool $dryRun): int
    {
        // Récupérer toutes les présences avec des retards expirés non encore pénalisés
        $expiredPresences = $user->presences()
            ->where('is_late_expired', true)
            ->where('expired_late_minutes', '>', 0)
            ->whereNotIn('id', function ($query) use ($user) {
                // Exclure les présences déjà comptées dans une pénalité
                $query->select('source_presence_ids')
                    ->from('late_penalty_absences')
                    ->where('user_id', $user->id);
            })
            ->get();

        // Calculer le total des minutes expirées non pénalisées
        $totalExpiredMinutes = 0;
        $presenceIds = [];

        foreach ($expiredPresences as $presence) {
            // Vérifier si cette présence n'est pas déjà dans une pénalité
            $alreadyPenalized = LatePenaltyAbsence::where('user_id', $user->id)
                ->whereJsonContains('source_presence_ids', $presence->id)
                ->exists();

            if (! $alreadyPenalized) {
                $totalExpiredMinutes += $presence->expired_late_minutes;
                $presenceIds[] = $presence->id;
            }
        }

        $this->line("  Utilisateur {$user->name}: {$totalExpiredMinutes} min expirées non pénalisées");

        $penaltiesCreated = 0;

        // Créer des pénalités tant que le seuil est atteint
        while ($totalExpiredMinutes >= $thresholdMinutes && ! empty($presenceIds)) {
            // Sélectionner les présences pour cette pénalité (jusqu'au seuil)
            $minutesForPenalty = 0;
            $presencesForPenalty = [];

            foreach ($presenceIds as $key => $presenceId) {
                $presence = $expiredPresences->firstWhere('id', $presenceId);
                if (! $presence) {
                    continue;
                }

                $minutesForPenalty += $presence->expired_late_minutes;
                $presencesForPenalty[] = $presenceId;
                unset($presenceIds[$key]);

                if ($minutesForPenalty >= $thresholdMinutes) {
                    break;
                }
            }

            if ($minutesForPenalty >= $thresholdMinutes) {
                $this->warn("    → Création d'une absence pénalité ({$minutesForPenalty} min >= {$thresholdMinutes} min)");

                if (! $dryRun) {
                    // Trouver la prochaine date de travail comme date d'absence
                    $absenceDate = $this->getNextWorkingDay($user);

                    LatePenaltyAbsence::create([
                        'user_id' => $user->id,
                        'absence_date' => $absenceDate,
                        'total_expired_minutes' => $minutesForPenalty,
                        'source_presence_ids' => $presencesForPenalty,
                        'reason' => "Absence pénalité: {$minutesForPenalty} minutes de retard non rattrapées dans le délai imparti.",
                    ]);

                    // Log pour traçabilité
                    Log::info("Absence pénalité créée pour l'utilisateur {$user->id}", [
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'absence_date' => $absenceDate->format('Y-m-d'),
                        'total_expired_minutes' => $minutesForPenalty,
                        'source_presences' => $presencesForPenalty,
                    ]);
                }

                $penaltiesCreated++;
                $totalExpiredMinutes -= $minutesForPenalty;
            }
        }

        return $penaltiesCreated;
    }

    /**
     * Obtenir le prochain jour de travail pour l'utilisateur
     */
    protected function getNextWorkingDay(User $user): Carbon
    {
        $date = Carbon::today();
        $workDays = $user->getWorkDayNumbers();

        // Si pas de jours de travail configurés, utiliser lun-ven
        if (empty($workDays)) {
            $workDays = [1, 2, 3, 4, 5];
        }

        // Trouver le prochain jour de travail (max 14 jours)
        for ($i = 0; $i < 14; $i++) {
            $date->addDay();
            if (in_array($date->dayOfWeekIso, $workDays)) {
                // Vérifier qu'il n'y a pas déjà une pénalité ce jour
                $existingPenalty = LatePenaltyAbsence::where('user_id', $user->id)
                    ->where('absence_date', $date)
                    ->exists();

                if (! $existingPenalty) {
                    return $date;
                }
            }
        }

        // Fallback: demain
        return Carbon::tomorrow();
    }
}
