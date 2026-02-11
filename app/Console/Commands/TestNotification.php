<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Notifications\Notification;

class TestNotification extends Command
{
    protected $signature = 'notify:test {email} {--message=Ceci est une notification de test ManageX}';
    protected $description = 'Envoyer une notification de test à un utilisateur par email';

    public function handle()
    {
        $email = $this->argument('email');
        $message = $this->option('message');

        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("Utilisateur avec l'email '{$email}' non trouvé.");
            return 1;
        }

        $this->info("Utilisateur trouvé : {$user->name} (ID: {$user->id}, Role: {$user->role})");
        $this->info("Envoi de la notification...");

        try {
            $user->notify(new TestNotificationMessage($message));
            $this->info('✅ Notification envoyée avec succès !');
            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Erreur : ' . $e->getMessage());
            return 1;
        }
    }
}

/**
 * Simple test notification class (inline)
 */
class TestNotificationMessage extends Notification
{
    use \App\Notifications\Traits\SendsOneSignal;

    public function __construct(private string $message) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $data = [
            'type' => 'test',
            'title' => '🧪 Test Notification',
            'message' => $this->message,
            'icon' => 'bell',
            'color' => 'blue',
            'url' => '/dashboard',
        ];

        $this->sendViaOneSignal($notifiable, $data);

        return $data;
    }
}
