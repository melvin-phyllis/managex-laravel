<?php

namespace App\Notifications;

use App\Models\Payroll;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PayrollAddedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Payroll $payroll;

    /**
     * Create a new notification instance.
     */
    public function __construct(Payroll $payroll)
    {
        $this->payroll = $payroll;
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
        return (new MailMessage)
            ->subject('Nouvelle fiche de paie disponible - ManageX')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Votre fiche de paie pour ' . $this->payroll->periode . ' est maintenant disponible.')
            ->line('Montant : ' . $this->payroll->montant_formatted)
            ->action('Voir mes fiches de paie', route('employee.payrolls.index'))
            ->line('Merci d\'utiliser ManageX !');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'payroll_added',
            'payroll_id' => $this->payroll->id,
            'periode' => $this->payroll->periode,
            'montant' => $this->payroll->montant_formatted,
            'message' => 'Nouvelle fiche de paie disponible pour ' . $this->payroll->periode . '.',
            'url' => route('employee.payrolls.index'),
        ];
    }
}

