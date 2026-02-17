<?php

namespace App\Console\Commands;

use App\Models\Presence;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoCheckoutCommand extends Command
{
    protected $signature = 'presence:auto-checkout';

    protected $description = 'Auto check-out les employés qui ont oublié de pointer leur départ';

    public function handle(): int
    {
        $workEndTime = Setting::getWorkEndTime();
        $today = today();

        // Trouver toutes les présences d'aujourd'hui sans check-out
        $presences = Presence::whereDate('date', $today)
            ->whereNotNull('check_in')
            ->whereNull('check_out')
            ->where('is_absent', false)
            ->get();

        if ($presences->isEmpty()) {
            $this->info('Aucune présence sans check-out trouvée.');
            return self::SUCCESS;
        }

        $scheduledEnd = Carbon::createFromFormat('H:i', $workEndTime)
            ->setDate($today->year, $today->month, $today->day);

        $count = 0;

        foreach ($presences as $presence) {
            $presence->update([
                'check_out' => $scheduledEnd,
                'is_auto_checkout' => true,
                'departure_type' => 'auto',
                'overtime_minutes' => 0,
                'is_early_departure' => false,
                'early_departure_minutes' => null,
            ]);

            $count++;

            // Notifier l'employé
            try {
                $presence->user->notify(new \App\Notifications\AutoCheckoutNotification($presence));
            } catch (\Exception $e) {
                Log::warning("Auto-checkout: notification échouée pour user #{$presence->user_id}: {$e->getMessage()}");
            }
        }

        $this->info("✅ {$count} employé(s) auto-checké(s) à {$workEndTime}.");
        Log::info("Auto-checkout: {$count} employé(s) traités à {$workEndTime}.");

        return self::SUCCESS;
    }
}
