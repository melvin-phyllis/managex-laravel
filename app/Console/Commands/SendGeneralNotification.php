<?php

namespace App\Console\Commands;

use App\Services\OneSignalService;
use Illuminate\Console\Command;

class SendGeneralNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'onesignal:send {message : Le message à envoyer} {--title= : Le titre de la notification (Optionnel)} {--url= : URL à ouvrir au clic (Optionnel)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envoyer une notification push générale à tous les utilisateurs via OneSignal';

    /**
     * Execute the console command.
     */
    public function handle(OneSignalService $oneSignalService)
    {
        $message = $this->argument('message');
        $title = $this->option('title') ?? config('app.name');
        $url = $this->option('url');

        $this->info("Envoi de la notification...");
        $this->line("Titre: {$title}");
        $this->line("Message: {$message}");
        if ($url) {
            $this->line("URL: {$url}");
        }

        if ($this->confirm('Confirmer l\'envoi à TOUS les utilisateurs ?', true)) {
            $success = $oneSignalService->sendToAll(
                $title,
                $message,
                ['type' => 'general_announcement'],
                $url
            );

            if ($success) {
                $this->info('Notification envoyée avec succès !');
                return Command::SUCCESS;
            } else {
                $this->error('Échec de l\'envoi de la notification. Vérifiez les logs.');
                return Command::FAILURE;
            }
        }

        $this->warn('Envoi annulé.');
        return Command::SUCCESS;
    }
}
