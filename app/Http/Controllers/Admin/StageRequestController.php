<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\StageRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StageRequestController extends Controller
{
    private function senderEmailDefault(): string
    {
        return (string) (config('recruitment.sender_email') ?: '');
    }

    private function ccEmailDefault(): string
    {
        $stored = (string) Setting::get('recruitment_cc_email', '');
        if (trim($stored) !== '') {
            return $stored;
        }

        return (string) config('recruitment.cc_email', 'info@ya-consulting.com');
    }

    public function index(Request $request)
    {
        $senderEmail = mb_strtolower(trim($this->senderEmailDefault()));
        $query = StageRequest::query()
            ->withCount('attachments')
            ->latest();

        if ($senderEmail !== '') {
            // Hide internal recruitment outbound emails accidentally imported in older sync runs.
            $query->whereRaw('LOWER(email) != ?', [$senderEmail]);
        }

        $finalStatus = $request->string('final_status')->toString();

        if (in_array($finalStatus, ['retained', 'waitlist', 'rejected'], true)) {
            $query->where('final_status', $finalStatus);
        } else {
            // Vue par defaut: cacher les candidatures rejetees
            $query->where(function ($q) {
                $q->whereNull('final_status')
                    ->orWhere('final_status', '!=', 'rejected');
            });
        }

        $requests = $query->paginate(20)->withQueryString();
        $mailSettings = [
            'sender_email' => $this->senderEmailDefault(),
            'cc_email' => $this->ccEmailDefault(),
            'retained_mail_subject' => Setting::get('recruitment_retained_mail_subject', 'Candidature retenue - {name}'),
            'retained_mail_body' => Setting::get(
                'recruitment_retained_mail_body',
                "Bonjour {name},\n\nVotre candidature a ete retenue. Nous reviendrons vers vous tres rapidement pour la suite du processus.\n\nCordialement,\nEquipe RH"
            ),
        ];

        return view('admin.stage-requests.index', compact('requests', 'finalStatus', 'mailSettings'));
    }

    public function show(StageRequest $stageRequest)
    {
        $stageRequest->load('attachments');
        $mailSettings = [
            'sender_email' => $this->senderEmailDefault(),
            'cc_email' => $this->ccEmailDefault(),
            'retained_mail_subject' => Setting::get('recruitment_retained_mail_subject', 'Candidature retenue - {name}'),
            'retained_mail_body' => Setting::get(
                'recruitment_retained_mail_body',
                "Bonjour {name},\n\nVotre candidature a ete retenue. Nous reviendrons vers vous tres rapidement pour la suite du processus.\n\nCordialement,\nEquipe RH"
            ),
        ];

        return view('admin.stage-requests.show', compact('stageRequest', 'mailSettings'));
    }

    public function updateStatus(Request $request, StageRequest $stageRequest)
    {
        $validated = $request->validate([
            'status' => 'nullable|in:pending,reviewed,shortlisted,rejected',
            'admin_note' => 'nullable|string|max:2000',
            'final_status' => 'nullable|in:retained,waitlist,rejected',
        ]);

        $newFinalStatus = $validated['final_status'] ?? null;
        $currentFinalStatus = $stageRequest->final_status;

        // Prevent "mail sent" badge confusion when decision changes.
        // Only the explicit sendRetainedMail action should set retained_mail_sent_at.
        $retainedMailSentAt = $stageRequest->retained_mail_sent_at;
        if ($newFinalStatus !== $currentFinalStatus) {
            $retainedMailSentAt = null;
        }

        $stageRequest->update([
            'status' => $validated['status'] ?? $stageRequest->status,
            'admin_note' => $validated['admin_note'] ?? null,
            'final_status' => $newFinalStatus,
            'retained_mail_sent_at' => $retainedMailSentAt,
        ]);

        return redirect()
            ->route('admin.stage-requests.index')
            ->with('success', 'Demande de stage mise a jour.');
    }

    public function downloadAttachment(StageRequest $stageRequest, int $attachment): StreamedResponse
    {
        $file = $stageRequest->attachments()->whereKey($attachment)->firstOrFail();

        return Storage::disk($file->disk)->download($file->path, $file->original_name);
    }

    public function updateMailSettings(Request $request)
    {
        $validated = $request->validate([
            'cc_email' => 'nullable|email|max:255',
            'retained_mail_subject' => 'nullable|string|max:255',
            'retained_mail_body' => 'nullable|string|max:5000',
        ]);

        Setting::set('recruitment_cc_email', $validated['cc_email'] ?? '', 'string', 'recruitment', 'Email CC pour les emails de candidatures retenues');
        Setting::set(
            'recruitment_retained_mail_subject',
            $validated['retained_mail_subject'] ?? 'Candidature retenue - {name}',
            'string',
            'recruitment',
            'Objet du mail pour les candidatures retenues'
        );
        Setting::set(
            'recruitment_retained_mail_body',
            $validated['retained_mail_body'] ?? "Bonjour {name},\n\nVotre candidature a ete retenue. Nous reviendrons vers vous tres rapidement pour la suite du processus.\n\nCordialement,\nEquipe RH",
            'string',
            'recruitment',
            'Contenu du mail pour les candidatures retenues'
        );
        Setting::clearGroupCache('recruitment');

        return redirect()
            ->route('admin.stage-requests.index')
            ->with('success', 'Parametres email recrutement mis a jour.');
    }

    public function sendRetainedMail(StageRequest $stageRequest)
    {
        if ($stageRequest->final_status !== 'retained') {
            return back()->with('error', 'Le mail est disponible uniquement pour les candidatures retenues.');
        }

        if (empty($stageRequest->email)) {
            return back()->with('error', 'Adresse email du candidat manquante.');
        }

        $cc = $this->ccEmailDefault();
        $subjectTpl = Setting::get('recruitment_retained_mail_subject', 'Candidature retenue - {name}');
        $bodyTpl = Setting::get(
            'recruitment_retained_mail_body',
            "Bonjour {name},\n\nVotre candidature a ete retenue. Nous reviendrons vers vous tres rapidement pour la suite du processus.\n\nCordialement,\nEquipe RH"
        );
        $senderEmail = $this->senderEmailDefault();
        $mailDriver = (string) Config::get('mail.default', 'log');
        if (in_array($mailDriver, ['log', 'array'], true)) {
            return back()->with('error', "Le mailer actuel est '{$mailDriver}' : aucun vrai email n'est envoye.");
        }
        if (empty($senderEmail)) {
            return back()->with('error', 'RECRUITMENT_IMAP_USERNAME est non defini.');
        }

        $smtpHost = (string) config('recruitment.smtp.host');
        $smtpPort = (int) config('recruitment.smtp.port', 465);
        $smtpEncryption = (string) config('recruitment.smtp.encryption', 'ssl');
        $smtpUsername = (string) config('recruitment.smtp.username');
        $smtpPassword = (string) config('recruitment.smtp.password');
        $fromName = (string) config('recruitment.smtp.from_name', config('app.name', 'ManageX'));

        $missing = [];
        if (empty($smtpHost)) $missing[] = 'host';
        if (empty($smtpUsername)) $missing[] = 'username';
        if (empty($smtpPassword)) $missing[] = 'password';

        if ($missing) {
            return back()->with(
                'error',
                'Configuration SMTP recrutement incomplete : ' . implode(', ', $missing) .
                '. Verifiez RECRUITMENT_SMTP_HOST/PORT/ENCRYPTION ou RECRUITMENT_IMAP_HOST et RECRUITMENT_IMAP_USERNAME/PASSWORD dans .env (puis config:clear).'
            );
        }

        $subject = str_replace('{name}', $stageRequest->full_name, (string) $subjectTpl);
        $body = str_replace('{name}', $stageRequest->full_name, (string) $bodyTpl);

        try {
            $mailerName = 'recruitment_'.Str::random(8);
            Config::set("mail.mailers.{$mailerName}", [
                'transport' => 'smtp',
                'host' => $smtpHost,
                'port' => $smtpPort,
                'encryption' => $smtpEncryption,
                'username' => $smtpUsername,
                'password' => $smtpPassword,
                'timeout' => null,
            ]);

            Mail::mailer($mailerName)->raw($body, function ($message) use ($stageRequest, $subject, $cc, $senderEmail, $fromName) {
                $message->to($stageRequest->email)->subject($subject);
                if (! empty($cc)) {
                    $message->cc($cc);
                }
                $message->from($senderEmail, $fromName);
                $message->replyTo($senderEmail, $fromName);
            });

            $stageRequest->update([
                'retained_mail_sent_at' => now(),
            ]);

            return back()->with('success', 'Email envoye au candidat.');
        } catch (\Throwable $e) {
            report($e);
            return back()->with(
                'error',
                'Echec envoi email SMTP: '.$e->getMessage()
            );
        }
    }
}

