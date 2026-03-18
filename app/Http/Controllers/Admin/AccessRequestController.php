<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DemoRequest;
use App\Notifications\AccessRequestStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;

class AccessRequestController extends Controller
{
    public function index()
    {
        $requests = DemoRequest::query()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.access-requests.index', compact('requests'));
    }

    public function updateStatus(Request $request, DemoRequest $demoRequest)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected,pending,needs_info',
            'admin_note' => 'required_if:status,needs_info|nullable|string|max:2000',
        ], [
            'admin_note.required_if' => 'Pour le statut « Infos requises », indiquez ce qui manque (ce texte sera envoyé au demandeur par email).',
        ]);

        $oldStatus = $demoRequest->status;
        $oldNote = $demoRequest->admin_note;
        $demoRequest->update([
            'status' => $validated['status'],
            'admin_note' => $validated['admin_note'] ?? null,
        ]);

        $newStatus = $validated['status'];
        $isNotifiableStatus = in_array($newStatus, ['approved', 'rejected', 'needs_info'], true);
        $noteChanged = ($validated['admin_note'] ?? null) !== $oldNote;
        $shouldNotifyRequester = $isNotifiableStatus && ($newStatus !== $oldStatus || $noteChanged);
        $emailSent = false;

        if ($shouldNotifyRequester && ! empty($demoRequest->email)) {
            try {
                Notification::route('mail', $demoRequest->email)
                    ->notify(new AccessRequestStatusNotification($demoRequest, $newStatus));
                $emailSent = true;
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $message = 'Demande mise à jour.';
        $mailDriver = Config::get('mail.default');
        $driverFake = in_array($mailDriver, ['log', 'array'], true);

        if ($emailSent) {
            if ($driverFake) {
                $message .= ' L’email pour le demandeur a été généré mais n’est pas envoyé (driver actuel : ' . $mailDriver . '). Configurez MAIL_MAILER=smtp dans .env pour envoyer de vrais emails (voir storage/logs en attendant).';
            } else {
                $message .= ' Un email a été envoyé au demandeur.';
            }
        } elseif ($shouldNotifyRequester && empty($demoRequest->email)) {
            $message .= ' Aucun email envoyé (adresse manquante).';
        } elseif ($isNotifiableStatus && ! $shouldNotifyRequester) {
            $message .= ' Aucun email envoyé car le statut et la note n’ont pas changé.';
        } elseif ($shouldNotifyRequester) {
            $message .= ' L’envoi de l’email a échoué (vérifiez la configuration mail et les logs).';
        }

        return redirect()
            ->route('admin.access-requests.index')
            ->with('success', $message);
    }
}

