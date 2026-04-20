<?php

namespace App\Console\Commands;

use App\Models\Setting;
use App\Models\User;
use App\Notifications\InternshipAbandonedNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckInternshipAbandonment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'interns:check-abandonment {--dry-run : Simmons the changes without applying them}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Détecte les stagiaires qui ont abandonné leur stage sur la base des absences prolongées.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Début de la vérification des abandons de stage...');

        $thresholdDays = Setting::getInternAbandonmentDays();
        $isDryRun = $this->option('dry-run');

        // On récupère les stagiaires actifs
        $interns = User::where('contract_type', 'stage')
            ->where('status', User::STATUS_ACTIVE)
            ->get();

        $this->info(count($interns) . ' stagiaires actifs à vérifier.');

        $count = 0;

        foreach ($interns as $intern) {
            // Trouver la date de dernière activité
            $lastPresence = $intern->presences()->latest('date')->first();
            $lastActivityDate = $lastPresence ? Carbon::parse($lastPresence->date) : $intern->hire_date;

            if (!$lastActivityDate) {
                // Si aucune date d'embauche, on prend la date de création du compte
                $lastActivityDate = $intern->created_at;
            }

            $diffInDays = Carbon::now()->diffInDays($lastActivityDate);

            if ($diffInDays >= $thresholdDays) {
                // Vérifier s'il y a des congés approuvés durant cette période
                $hasApprovedLeave = $intern->leaves()
                    ->where('statut', 'approved')
                    ->where(function($query) use ($lastActivityDate) {
                        $query->whereBetween('date_debut', [$lastActivityDate, Carbon::now()])
                              ->orWhereBetween('date_fin', [$lastActivityDate, Carbon::now()])
                              ->orWhere(function($q) use ($lastActivityDate) {
                                  $q->where('date_debut', '<=', $lastActivityDate)
                                    ->where('date_fin', '>=', Carbon::now());
                              });
                    })
                    ->exists();

                if (!$hasApprovedLeave) {
                    $this->warn("Abandon détecté pour: {$intern->name} (Absence de {$diffInDays} jours)");

                    if (!$isDryRun) {
                        try {
                            $intern->status = User::STATUS_ABANDONED;
                            $intern->save();

                            // Notification
                            $intern->notify(new InternshipAbandonedNotification($diffInDays));

                            Log::info("Stagiaire marqué comme abandonné par tâche automatique: {$intern->name} (ID: {$intern->id})");
                            $count++;
                        } catch (\Exception $e) {
                            $this->error("Erreur lors de la mise à jour de {$intern->name}: " . $e->getMessage());
                            Log::error("Erreur détection abandon stage: " . $e->getMessage());
                        }
                    } else {
                        $this->info("[DRY RUN] Stagiaire {$intern->name} serait marqué comme abandonné.");
                        $count++;
                    }
                }
            }
        }

        $this->info("Terminé. {$count} stagiaire(s) traité(s).");
    }
}
