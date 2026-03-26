<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\StageRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StageRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = StageRequest::query()
            ->withCount('attachments')
            ->latest();

        $finalStatus = $request->string('final_status')->toString();

        if (in_array($finalStatus, ['retained', 'waitlist', 'rejected'], true)) {
            $query->where('final_status', $finalStatus);
        }

        $requests = $query->paginate(20)->withQueryString();
        $mailSettings = [
            'sender_email' => (string) env('RECRUITMENT_IMAP_USERNAME', ''),
            'cc_email' => Setting::get('recruitment_cc_email', config('recruitment.cc_email')),
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
            'sender_email' => (string) env('RECRUITMENT_IMAP_USERNAME', ''),
            'cc_email' => Setting::get('recruitment_cc_email', config('recruitment.cc_email')),
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

        $stageRequest->update([
            'status' => $validated['status'] ?? $stageRequest->status,
            'admin_note' => $validated['admin_note'] ?? null,
            'final_status' => $validated['final_status'] ?? null,
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
}

