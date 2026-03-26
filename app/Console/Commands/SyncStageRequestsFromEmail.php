<?php

namespace App\Console\Commands;

use App\Models\StageRequest;
use App\Services\AI\MistralService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Smalot\PdfParser\Parser as PdfParser;
use ZipArchive;

class SyncStageRequestsFromEmail extends Command
{
    protected MistralService $mistral;

    protected $signature = 'recruitment:sync-stage-requests {--days=30 : Number of days to scan}';

    protected $description = 'Import stage requests from mailbox into stage_requests table';

    public function __construct(MistralService $mistral)
    {
        parent::__construct();
        $this->mistral = $mistral;
    }

    public function handle(): int
    {
        if (! function_exists('imap_open')) {
            $this->error('PHP IMAP extension is not installed.');
            return self::FAILURE;
        }

        $host = (string) env('RECRUITMENT_IMAP_HOST');
        $port = (string) env('RECRUITMENT_IMAP_PORT', '993');
        $encryption = (string) env('RECRUITMENT_IMAP_ENCRYPTION', 'ssl');
        $novalidateCert = filter_var(env('RECRUITMENT_IMAP_NOVALIDATE_CERT', false), FILTER_VALIDATE_BOOL);
        $mailbox = (string) env('RECRUITMENT_IMAP_MAILBOX', 'INBOX');
        $username = (string) env('RECRUITMENT_IMAP_USERNAME');
        $password = (string) env('RECRUITMENT_IMAP_PASSWORD');
        $onlyStage = filter_var(env('RECRUITMENT_ONLY_STAGE', true), FILTER_VALIDATE_BOOL);
        $maxAttachmentMb = (float) env('RECRUITMENT_ATTACHMENT_MAX_MB', 5);
        $maxAttachmentBytes = (int) max(1, $maxAttachmentMb * 1024 * 1024);
        $days = (int) $this->option('days');

        if ($host === '' || $username === '' || $password === '') {
            $this->error('Missing IMAP config. Please set RECRUITMENT_IMAP_* in .env');
            return self::FAILURE;
        }

        $flags = '/imap/'.$encryption;
        if ($novalidateCert) {
            $flags .= '/novalidate-cert';
        }

        $mailboxPath = sprintf('{%s:%s%s}%s', $host, $port, $flags, $mailbox);
        $this->line("Connecting to IMAP: {$host}:{$port} ({$encryption}) / {$mailbox}");

        // Avoid long silent hangs on unreachable IMAP servers.
        @imap_timeout(IMAP_OPENTIMEOUT, 12);
        @imap_timeout(IMAP_READTIMEOUT, 20);

        // Some IMAP failures trigger shutdown warnings; silence and convert to safe command errors.
        set_error_handler(static function () {
            return true;
        });
        $imap = @imap_open($mailboxPath, $username, $password, OP_READONLY);
        restore_error_handler();

        if (! $imap) {
            $errors = imap_errors() ?: [];
            $lastError = imap_last_error();
            $this->error('IMAP connection failed: '.($lastError ?: 'Unknown error'));
            foreach ($errors as $imapError) {
                $this->line('- '.$imapError);
            }
            return self::FAILURE;
        }

        $this->info('IMAP connected successfully.');

        $since = date('d-M-Y', strtotime('-'.$days.' days'));
        $emails = imap_search($imap, 'SINCE "'.$since.'"') ?: [];

        $created = 0;
        $skipped = 0;
        $attachmentsImported = 0;
        $enriched = 0;

        foreach (array_reverse($emails) as $msgNo) {
            $overviewList = imap_fetch_overview($imap, (string) $msgNo, 0);
            if (! $overviewList || ! isset($overviewList[0])) {
                $skipped++;
                continue;
            }

            $overview = $overviewList[0];
            $subject = $this->decodeHeader($overview->subject ?? '');
            $fromHeader = $overview->from ?? '';
            $messageId = trim((string) ($overview->message_id ?? ''));
            $sourceUid = $messageId !== '' ? $messageId : 'imap-'.$msgNo;
            $body = $this->extractBody($imap, $msgNo);

            $existingRequest = StageRequest::where('source_uid', $sourceUid)->first();
            if ($existingRequest) {
                // If message already imported, still try to backfill attachments (old imports may not have them).
                $attachmentsImported += $this->importAttachments($imap, $msgNo, $existingRequest, $maxAttachmentBytes);
                if ($this->enrichStageRequestFromText($existingRequest, $subject, $body)) {
                    $enriched++;
                }
                $skipped++;
                continue;
            }

            if ($onlyStage && ! $this->looksLikeStageRequest($subject, $body)) {
                $skipped++;
                continue;
            }

            [$email, $name] = $this->parseSender($fromHeader);
            if ($email === '') {
                $skipped++;
                continue;
            }

            $profile = $this->extractProfileFromText($subject, $body);

            $stageRequest = StageRequest::create([
                'full_name' => $name !== '' ? $name : $email,
                'email' => $email,
                'phone' => $profile['phone'],
                'school' => $profile['school'],
                'level' => $profile['level'],
                'desired_role' => $profile['desired_role'],
                'message' => trim($subject."\n\n".$body),
                'status' => 'pending',
                'source' => 'email',
                'source_uid' => $sourceUid,
            ]);

            $attachmentsImported += $this->importAttachments($imap, $msgNo, $stageRequest, $maxAttachmentBytes);
            if ($this->enrichStageRequestFromText($stageRequest, $subject, $body)) {
                $enriched++;
            }

            $created++;
        }

        @imap_close($imap, CL_EXPUNGE);

        $this->info("Sync complete. Created: {$created}, skipped: {$skipped}, attachments: {$attachmentsImported}, enriched: {$enriched}");
        return self::SUCCESS;
    }

    private function decodeHeader(string $value): string
    {
        $parts = imap_mime_header_decode($value);
        if (! is_array($parts)) {
            return $this->toUtf8(trim($value));
        }

        $decoded = '';
        foreach ($parts as $part) {
            $text = (string) ($part->text ?? '');
            $charset = (string) ($part->charset ?? 'UTF-8');
            $decoded .= $this->toUtf8($text, $charset);
        }

        return trim($this->toUtf8($decoded));
    }

    private function parseSender(string $fromHeader): array
    {
        $email = '';
        $name = '';

        if (preg_match('/<([^>]+)>/', $fromHeader, $matches)) {
            $email = trim($matches[1]);
            $name = trim(str_replace($matches[0], '', $fromHeader), "\"' ");
        } elseif (filter_var($fromHeader, FILTER_VALIDATE_EMAIL)) {
            $email = trim($fromHeader);
        }

        return [$email, $name];
    }

    private function looksLikeStageRequest(string $subject, string $body): bool
    {
        $text = mb_strtolower($subject.' '.$body);

        return str_contains($text, 'stage')
            || str_contains($text, 'candidature')
            || str_contains($text, 'stagiaire')
            || str_contains($text, 'cv');
    }

    private function extractBody($imap, int $msgNo): string
    {
        $body = imap_fetchbody($imap, $msgNo, '1.1');
        if ($body === '' || $body === false) {
            $body = imap_fetchbody($imap, $msgNo, '1');
        }
        if ($body === '' || $body === false) {
            $body = imap_body($imap, $msgNo);
        }

        $decodedBody = quoted_printable_decode((string) $body);
        $decodedBody = $this->toUtf8($decodedBody);
        $clean = strip_tags($decodedBody);
        $clean = preg_replace('/\s+/', ' ', $clean) ?? $clean;

        return trim(mb_substr($clean, 0, 3000));
    }

    private function toUtf8(string $text, ?string $charset = null): string
    {
        $charset = strtoupper((string) ($charset ?: 'UTF-8'));

        if ($charset === 'DEFAULT' || $charset === 'UNKNOWN-8BIT') {
            $charset = 'ISO-8859-1';
        }

        // If already valid UTF-8, keep as-is.
        if (mb_check_encoding($text, 'UTF-8')) {
            return $text;
        }

        $sourceCandidates = array_values(array_unique([
            $charset,
            'ISO-8859-1',
            'WINDOWS-1252',
            'UTF-8',
        ]));

        foreach ($sourceCandidates as $source) {
            try {
                $converted = @mb_convert_encoding($text, 'UTF-8', $source);
                if (is_string($converted) && $converted !== '' && mb_check_encoding($converted, 'UTF-8')) {
                    return $converted;
                }
            } catch (\Throwable $e) {
                // Ignore and continue trying other charsets.
            }
        }

        return mb_convert_encoding($text, 'UTF-8', 'UTF-8,ISO-8859-1,WINDOWS-1252');
    }

    private function importAttachments($imap, int $msgNo, StageRequest $stageRequest, int $maxAttachmentBytes): int
    {
        $structure = imap_fetchstructure($imap, $msgNo);
        if (! $structure) {
            return 0;
        }

        $attachments = [];
        if (! empty($structure->parts) && is_array($structure->parts)) {
            $this->walkPartsForAttachments($structure->parts, '', $attachments);
        } else {
            // Single-part emails can still contain attachments.
            $this->walkPartsForAttachments([$structure], '', $attachments);
        }

        $imported = 0;

        foreach ($attachments as $partInfo) {
            $partNumber = $partInfo['part_number'];
            $filename = $partInfo['filename'];
            if ($filename === '') {
                continue;
            }

            $raw = imap_fetchbody($imap, $msgNo, $partNumber);
            if ($raw === false || $raw === '') {
                continue;
            }

            $decoded = $this->decodePartContent($raw, (int) $partInfo['encoding']);
            if ($decoded === '') {
                continue;
            }

            $originalSize = strlen($decoded);
            if ($originalSize > $maxAttachmentBytes) {
                $this->line("Skipping large attachment: {$filename} (".round($originalSize / 1024 / 1024, 2)." MB)");
                continue;
            }

            $optimized = $this->optimizeAttachmentBinary($decoded, (string) ($partInfo['mime_type'] ?? ''), $filename);
            if ($optimized !== '') {
                $decoded = $optimized;
            }

            $alreadyExists = $stageRequest->attachments()
                ->where('original_name', $this->toUtf8($filename))
                ->where('size', strlen($decoded))
                ->exists();
            if ($alreadyExists) {
                continue;
            }

            $safeName = Str::slug(pathinfo($filename, PATHINFO_FILENAME));
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $safeExt = $ext !== '' ? '.'.$ext : '';
            $storedName = now()->format('YmdHis').'-'.Str::random(8).'-'.($safeName !== '' ? $safeName : 'piece-jointe').$safeExt;
            $path = 'stage-requests/'.$stageRequest->id.'/'.$storedName;

            Storage::disk('public')->put($path, $decoded);

            $stageRequest->attachments()->create([
                'original_name' => $this->toUtf8($filename),
                'stored_name' => $storedName,
                'disk' => 'public',
                'path' => $path,
                'mime_type' => $partInfo['mime_type'] ?: null,
                'size' => strlen($decoded),
            ]);
            $imported++;
        }

        return $imported;
    }

    private function walkPartsForAttachments(array $parts, string $prefix, array &$attachments): void
    {
        foreach ($parts as $index => $part) {
            $partNumber = $prefix === '' ? (string) ($index + 1) : $prefix.'.'.($index + 1);

            $filename = '';
            $disposition = strtolower((string) ($part->disposition ?? ''));
            if (! empty($part->dparameters)) {
                foreach ($part->dparameters as $param) {
                    if (strtolower($param->attribute ?? '') === 'filename') {
                        $filename = $this->decodeHeader((string) ($param->value ?? ''));
                    }
                }
            }
            if ($filename === '' && ! empty($part->parameters)) {
                foreach ($part->parameters as $param) {
                    if (strtolower($param->attribute ?? '') === 'name') {
                        $filename = $this->decodeHeader((string) ($param->value ?? ''));
                    }
                }
            }

            $hasAttachmentDisposition = in_array($disposition, ['attachment', 'inline'], true);
            if ($filename !== '' || $hasAttachmentDisposition) {
                $subtype = strtoupper((string) ($part->subtype ?? 'OCTET-STREAM'));
                $mimeType = $this->resolveMimeType((int) ($part->type ?? 3), $subtype);
                if ($filename === '') {
                    $filename = 'piece-jointe-'.$partNumber.'.'.($this->extensionFromMime($mimeType) ?: 'bin');
                }
                $attachments[] = [
                    'part_number' => $partNumber,
                    'filename' => $filename,
                    'encoding' => (int) ($part->encoding ?? 0),
                    'mime_type' => $mimeType,
                ];
            }

            if (! empty($part->parts) && is_array($part->parts)) {
                $this->walkPartsForAttachments($part->parts, $partNumber, $attachments);
            }
        }
    }

    private function decodePartContent(string $raw, int $encoding): string
    {
        return match ($encoding) {
            3 => base64_decode($raw, true) ?: '',
            4 => quoted_printable_decode($raw),
            default => $raw,
        };
    }

    private function resolveMimeType(int $type, string $subtype): string
    {
        $main = match ($type) {
            0 => 'text',
            1 => 'multipart',
            2 => 'message',
            3 => 'application',
            4 => 'audio',
            5 => 'image',
            6 => 'video',
            default => 'application',
        };

        return strtolower($main.'/'.$subtype);
    }

    private function extensionFromMime(string $mime): ?string
    {
        return match ($mime) {
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.ms-excel' => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            default => null,
        };
    }

    private function optimizeAttachmentBinary(string $binary, string $mime, string $filename): string
    {
        $mime = strtolower($mime);
        $name = strtolower($filename);

        $isJpeg = str_contains($mime, 'image/jpeg') || str_ends_with($name, '.jpg') || str_ends_with($name, '.jpeg');
        $isPng = str_contains($mime, 'image/png') || str_ends_with($name, '.png');
        $isWebp = str_contains($mime, 'image/webp') || str_ends_with($name, '.webp');

        if (! $isJpeg && ! $isPng && ! $isWebp) {
            return $binary; // PDFs/DOCX are kept as-is (safe and lossless).
        }

        if (! function_exists('imagecreatefromstring')) {
            return $binary;
        }

        $image = @imagecreatefromstring($binary);
        if (! $image) {
            return $binary;
        }

        ob_start();
        $ok = false;
        if ($isJpeg && function_exists('imagejpeg')) {
            // Good trade-off quality/size for recruitment docs photos.
            $ok = @imagejpeg($image, null, 75);
        } elseif ($isPng && function_exists('imagepng')) {
            $ok = @imagepng($image, null, 8);
        } elseif ($isWebp && function_exists('imagewebp')) {
            $ok = @imagewebp($image, null, 75);
        }
        $out = ob_get_clean();
        imagedestroy($image);

        if (! $ok || ! is_string($out) || $out === '') {
            return $binary;
        }

        // Keep optimized file only if smaller.
        return strlen($out) < strlen($binary) ? $out : $binary;
    }

    private function enrichStageRequestFromText(StageRequest $stageRequest, string $subject, string $body): bool
    {
        $profile = $this->extractProfileFromText($subject, $body);
        $aiProfile = $this->extractProfileWithMistral($stageRequest, $subject, $body);
        if (is_array($aiProfile)) {
            $profile = array_merge($profile, array_filter($aiProfile, fn ($v) => ! empty($v)));
        }
        $dirty = false;

        foreach (['phone', 'school', 'level', 'desired_role'] as $field) {
            if (empty($stageRequest->{$field}) && ! empty($profile[$field])) {
                $stageRequest->{$field} = $profile[$field];
                $dirty = true;
            }
        }

        if ($dirty) {
            $stageRequest->save();
        }

        return $dirty;
    }

    private function extractProfileFromText(string $subject, string $body): array
    {
        $text = trim($subject."\n".$body);

        $phone = null;
        if (preg_match('/(\+?\d[\d\s\-\.\(\)]{7,}\d)/u', $text, $m)) {
            $phone = preg_replace('/\s+/', ' ', trim($m[1]));
        }

        $level = null;
        if (preg_match('/\b(BTS|DUT|Licence\s*\d?|Master\s*\d?|M1|M2|L1|L2|L3|Ingenieur|Doctorat|Terminale)\b/iu', $text, $m)) {
            $level = $this->normalizeSpaces($m[1]);
        }

        $school = null;
        if (preg_match('/\b(?:universite|ecole|institut|faculte|college|lycee)\b[^\n\r,;]{0,120}/iu', $text, $m)) {
            $school = $this->normalizeSpaces($m[0]);
        }

        $desiredRole = null;
        if (preg_match('/(?:poste|stage|candidature)\s*(?:de|du|pour)?\s*[:\-]?\s*([^\n\r,]{4,120})/iu', $subject, $m)
            || preg_match('/(?:poste|stage)\s*(?:de|du|pour)?\s*[:\-]?\s*([^\n\r,]{4,120})/iu', $body, $m)) {
            $candidate = $this->normalizeSpaces($m[1]);
            $desiredRole = mb_substr($candidate, 0, 120);
        }

        return [
            'phone' => $phone,
            'school' => $school,
            'level' => $level,
            'desired_role' => $desiredRole,
        ];
    }

    private function normalizeSpaces(string $value): string
    {
        $value = preg_replace('/\s+/u', ' ', trim($value)) ?? trim($value);
        return trim($value, " \t\n\r\0\x0B-:;");
    }

    private function extractProfileWithMistral(StageRequest $stageRequest, string $subject, string $body): ?array
    {
        if (! $this->mistral->isAvailable()) {
            return null;
        }

        $attachmentTexts = [];
        foreach ($stageRequest->attachments as $file) {
            $disk = Storage::disk($file->disk);
            if (! $disk->exists($file->path)) {
                continue;
            }

            $absolutePath = $disk->path($file->path);
            $text = $this->extractTextFromAttachment($absolutePath, $file->original_name, (string) $file->mime_type);
            if ($text !== '') {
                $attachmentTexts[] = "Fichier: {$file->original_name}\n".$text;
            }
        }

        $candidateText = mb_substr(implode("\n\n---\n\n", $attachmentTexts), 0, 7000);
        $mailText = mb_substr($subject."\n\n".$body, 0, 3000);

        $system = <<<PROMPT
Tu es un assistant de recrutement.
Extrait uniquement ces champs depuis le texte d'une candidature:
- school
- level
- desired_role
- phone

Réponds STRICTEMENT en JSON valide:
{"school":"","level":"","desired_role":"","phone":""}
Si une info est absente, laisse une chaîne vide.
PROMPT;

        $userContent = "Mail:\n{$mailText}\n\nCV/lettre (texte extrait):\n{$candidateText}";
        $response = $this->mistral->chat($system, [
            ['role' => 'user', 'content' => $userContent],
        ], 512);

        if (! is_string($response) || trim($response) === '') {
            return null;
        }

        $json = trim($response);
        $json = preg_replace('/^```json\s*/i', '', $json) ?? $json;
        $json = preg_replace('/```$/', '', $json) ?? $json;

        $decoded = json_decode($json, true);
        if (! is_array($decoded)) {
            return null;
        }

        return [
            'school' => $this->normalizeSpaces((string) ($decoded['school'] ?? '')),
            'level' => $this->normalizeSpaces((string) ($decoded['level'] ?? '')),
            'desired_role' => $this->normalizeSpaces((string) ($decoded['desired_role'] ?? '')),
            'phone' => $this->normalizeSpaces((string) ($decoded['phone'] ?? '')),
        ];
    }

    private function extractTextFromAttachment(string $absolutePath, string $filename, string $mime): string
    {
        $lowerName = strtolower($filename);
        $lowerMime = strtolower($mime);

        if (str_ends_with($lowerName, '.txt') || str_contains($lowerMime, 'text/plain')) {
            $raw = @file_get_contents($absolutePath);
            if (! is_string($raw)) {
                return '';
            }
            return trim(mb_substr($this->toUtf8($raw), 0, 7000));
        }

        if (str_ends_with($lowerName, '.docx') || str_contains($lowerMime, 'wordprocessingml')) {
            return $this->extractTextFromDocx($absolutePath);
        }

        if (str_ends_with($lowerName, '.pdf') || str_contains($lowerMime, 'application/pdf')) {
            return $this->extractTextFromPdf($absolutePath);
        }

        return '';
    }

    private function extractTextFromDocx(string $path): string
    {
        $zip = new ZipArchive;
        if ($zip->open($path) !== true) {
            return '';
        }

        $xml = $zip->getFromName('word/document.xml') ?: '';
        $zip->close();
        if ($xml === '') {
            return '';
        }

        $text = strip_tags($xml);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_XML1, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        return trim(mb_substr($text, 0, 7000));
    }

    private function extractTextFromPdf(string $path): string
    {
        try {
            $parser = new PdfParser;
            $pdf = $parser->parseFile($path);
            $text = $pdf->getText();
            $text = $this->toUtf8($text);
            $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

            return trim(mb_substr($text, 0, 7000));
        } catch (\Throwable $e) {
            return '';
        }
    }
}

