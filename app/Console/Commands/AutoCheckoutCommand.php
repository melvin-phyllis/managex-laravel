<?php

namespace App\Console\Commands;

use App\Models\Presence;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoCheckoutCommand extends Command
{
    protected $signature = 'presence:auto-checkout {--days=7 : Nombre de jours en arrière à vérifier}';

    protected $description = "Auto check-out les employés qui ont oublié de pointer leur départ (aujourd'hui et jours précédents)";

    public function handle(): int
    {
        $workEndTime = Setting::getWorkEndTime();
        $days = (int) $this->option('days');
        $today = today();

        // Chercher les présences ouvertes sur les N derniers jours
        $presences = Presence::whereBetween('date', [$today->copy()->subDays($days), $today])
            ->whereNotNull('check_in')
            ->whereNull('check_out')
            ->where('is_absent', false)
            ->with('user')
            ->get();

        if ($presences->isEmpty()) {
            $this->info('✅ Aucune présence sans check-out trouvée.');
            return self::SUCCESS;
        }

        $this->info("🔍 {$presences->count()} présence(s) ouvertes trouvées sur les {$days} derniers jours.");

        $count = 0;

        foreach ($presences as $presence) {
            $presenceDate = Carbon::parse($presence->date);
            $isToday = $presenceDate->isToday();

            // Pour les jours passés : utiliser l'heure de fin enregistrée ou la valeur par défaut
            $endTimeStr = $presence->scheduled_end ?? $workEndTime;
            // Carbon::parse handles both 'H:i' and 'H:i:s' formats
            $scheduledEnd = Carbon::parse($endTimeStr)
                ->setDate($presenceDate->year, $presenceDate->month, $presenceDate->day);

            $presence->update([
                'check_out'               => $scheduledEnd,
                'is_auto_checkout'        => true,
                'departure_type'          => 'auto',
                'overtime_minutes'        => 0,
                'is_early_departure'      => false,
                'early_departure_minutes' => null,
            ]);

            $dateLabel = $isToday ? "aujourd'hui" : $presenceDate->format('d/m/Y');
            $this->line("  → {$presence->user->name} ({$dateLabel}) → check-out à {$scheduledEnd->format('H:i')}");

            $count++;

            // Notifier l'employé
            try {
                $presence->user->notify(new \App\Notifications\AutoCheckoutNotification($presence));
            } catch (\Exception $e) {
                Log::warning("Auto-checkout: notification échouée pour user #{$presence->user_id}: {$e->getMessage()}");
            }
        }

        $this->info("✅ {$count} employé(s) auto-checké(s).");
        Log::info("Auto-checkout: {$count} employé(s) traités (lookback: {$days} jours).");

        return self::SUCCESS;
    }
}
