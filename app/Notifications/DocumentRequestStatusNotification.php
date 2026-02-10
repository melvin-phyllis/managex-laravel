<?php

namespace App\Notifications;

use App\Models\DocumentRequest;
use App\Notifications\Traits\SendsWebPush;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DocumentRequestStatusNotification extends Notification implements ShouldQueue
{
    use Queueable, SendsWebPush;

    public function __construct(
        public DocumentRequest $documentRequest,
        public string $status
    ) {}

    public function via(object $notifiable): array
    {
        $channels = ['database', 'broadcast'];

        if ($this->shouldSendWebPush($notifiable)) {
            $channels[] = 'webpush';
        }

        $channels[] = 'mail';

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $typeLabel = $this->documentRequest->type_label;

        if ($this->status === 'approved') {
            return (new MailMessage)
                ->subject('Votre demande de document a ete traitee - ManageX')
                ->greeting('Bonjour '.$notifiable->name.',')
                ->line('Bonne nouvelle ! Votre demande de **'.$typeLabel.'** a ete **approuvee**.')
                ->line('Le document est maintenant disponible en telechargement.')
                ->line('**Reponse de l\'administration :** '.$this->documentRequest->admin_response)
                ->action('Telecharger le document', route('employee.document-requests.index'))
                ->line('Merci d\'utiliser ManageX !');
        }

        $mail = (new MailMessage)
            ->subject('Votre demande de document a ete refusee - ManageX')
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line('Votre demande de **'.$typeLabel.'** a malheureusement ete **refusee**.');

        if ($this->documentRequest->admin_response) {
            $mail->line('**Motif du refus :** '.$this->documentRequest->admin_response);
        }

        return $mail
            ->action('Voir mes demandes', route('employee.document-requests.index'))
            ->line('N\'hesitez pas a contacter l\'administration pour plus d\'informations.');
    }

    public function toDatabase(object $notifiable): array
    {
        $icons = ['approved' => '✅', 'rejected' => '❌'];
        $labels = ['approved' => 'approuvee', 'rejected' => 'refusee'];

        return [
            'type' => 'document_request_status',
            'document_request_id' => $this->documentRequest->id,
            'document_type' => $this->documentRequest->type_label,
            'status' => $this->status,
            'message' => ($icons[$this->status] ?? '').' Votre demande de '.$this->documentRequest->type_label.' a ete '.($labels[$this->status] ?? 'traitee'),
            'url' => route('employee.document-requests.index'),
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
