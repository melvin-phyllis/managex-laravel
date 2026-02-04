<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Password;

class WelcomeEmployeeNotification extends Notification
{
    use Queueable;

    public string $employeeName;

    protected ?string $resetToken = null;

    /**
     * Create a new notification instance.
     *
     * @param  string  $employeeName  Le nom de l'employé
     * @param  string|null  $resetToken  Token de réinitialisation (optionnel, généré automatiquement si null)
     */
    public function __construct(string $employeeName, ?string $resetToken = null)
    {
        $this->employeeName = $employeeName;
        $this->resetToken = $resetToken;
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
        $appName = config('app.name');

        // Générer le token de réinitialisation si non fourni
        $token = $this->resetToken ?? Password::broker()->createToken($notifiable);

        // URL sécurisée pour définir le mot de passe
        $resetUrl = url(route('password.reset', [
            'token' => $token,
            'email' => $notifiable->email,
        ], false));

        return (new MailMessage)
            ->subject("Bienvenue sur {$appName} - Activez votre compte")
            ->greeting("Bonjour {$this->employeeName},")
            ->line("Votre compte employé a été créé avec succès sur la plateforme {$appName}.")
            ->line('Pour activer votre compte, veuillez définir votre mot de passe en cliquant sur le bouton ci-dessous :')
            ->line("**Votre email de connexion :** {$notifiable->email}")
            ->action('Définir mon mot de passe', $resetUrl)
            ->line('Ce lien expirera dans 60 minutes.')
            ->line('Si vous n\'avez pas demandé la création de ce compte, aucune action n\'est requise.')
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
            'message' => 'Votre compte a été créé. Vérifiez votre email pour activer votre compte.',
            'icon' => 'user-plus',
            'color' => 'green',
        ];
    }
}
