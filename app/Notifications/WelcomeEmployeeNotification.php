<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Notifications\Traits\SendsWebPush;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Password;

class WelcomeEmployeeNotification extends Notification implements ShouldQueue
{
    use Queueable, SendsWebPush;

    public string $employeeName;

    protected ?string $resetToken = null;

    protected ?string $temporaryPassword = null;

    /**
     * Create a new notification instance.
     *
     * @param  string  $employeeName  Le nom de l'employé
     * @param  string|null  $resetToken  Token de réinitialisation (optionnel, généré automatiquement si null)
     * @param  string|null  $temporaryPassword  Mot de passe temporaire en clair
     */
    public function __construct(string $employeeName, ?string $resetToken = null, ?string $temporaryPassword = null)
    {
        $this->employeeName = $employeeName;
        $this->resetToken = $resetToken;
        $this->temporaryPassword = $temporaryPassword;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if ($this->shouldSendWebPush($notifiable)) {
            $channels[] = 'webpush';
        }

        $channels[] = 'mail';

        return $channels;
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

        $mail = (new MailMessage)
            ->subject("Bienvenue sur {$appName} - Vos identifiants de connexion")
            ->greeting("Bonjour {$this->employeeName},")
            ->line("Votre compte employé a été créé avec succès sur la plateforme {$appName}.")
            ->line('Voici vos identifiants de connexion :')
            ->line("**Email :** {$notifiable->email}")
            ->line("**Mot de passe temporaire :** {$this->temporaryPassword}")
            ->line('Vous pouvez vous connecter immédiatement avec ces identifiants.')
            ->line('---')
            ->line('Vous pouvez également changer votre mot de passe en cliquant sur le bouton ci-dessous :')
            ->action('Changer mon mot de passe', $resetUrl)
            ->line('⚠️ Ce lien expirera dans **5 minutes**.')
            ->line('Si vous avez des questions, n\'hésitez pas à contacter votre responsable RH.')
            ->salutation("Cordialement,\nL'équipe {$appName}");

        return $mail;
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
            'url' => route('dashboard'),
        ];
    }
}
