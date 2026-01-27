<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeEmployeeNotification extends Notification
{
    use Queueable;

    public string $password;
    public string $employeeName;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $password, string $employeeName)
    {
        $this->password = $password;
        $this->employeeName = $employeeName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $appUrl = config('app.url');
        $appName = config('app.name');

        return (new MailMessage)
            ->subject("Bienvenue sur {$appName} - Vos identifiants de connexion")
            ->greeting("Bonjour {$this->employeeName},")
            ->line("Votre compte employé a été créé avec succès sur la plateforme {$appName}.")
            ->line('Voici vos identifiants de connexion :')
            ->line("**Email :** {$notifiable->email}")
            ->line("**Mot de passe :** {$this->password}")
            ->action('Se connecter', url('/login'))
            ->line('Pour des raisons de sécurité, nous vous recommandons de changer votre mot de passe après votre première connexion.')
            ->line('Si vous avez des questions, n\'hésitez pas à contacter votre responsable RH.')
            ->salutation("Cordialement,\nL'équipe {$appName}");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'welcome',
            'title' => 'Bienvenue !',
            'message' => 'Votre compte a été créé. Vérifiez votre email pour vos identifiants de connexion.',
            'icon' => 'user-plus',
            'color' => 'green',
        ];
    }
}
