<?php

namespace App\Http\Controllers\Messaging;

use App\Http\Controllers\Controller;
use App\Models\Messaging\Attachment;
use App\Models\Messaging\Conversation;
use App\Models\Messaging\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttachmentController extends Controller
{
    /**
     * Allowed file types (MIME type => extensions autorisées)
     * SÉCURITÉ: Validation double MIME type + extension
     */
    protected array $allowedTypes = [
        'image/jpeg' => ['jpg', 'jpeg'],
        'image/png' => ['png'],
        'image/gif' => ['gif'],
        'image/webp' => ['webp'],
        'application/pdf' => ['pdf'],
        'application/msword' => ['doc'],
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => ['docx'],
        'application/vnd.ms-excel' => ['xls'],
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => ['xlsx'],
        'application/vnd.ms-powerpoint' => ['ppt'],
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => ['pptx'],
        'video/mp4' => ['mp4'],
        'video/webm' => ['webm'],
        'audio/mpeg' => ['mp3'],
        'audio/wav' => ['wav'],
        'audio/ogg' => ['ogg'],
    ];

    /**
     * Extensions dangereuses à bloquer absolument
     */
    protected array $dangerousExtensions = [
        'php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'phar',
        'exe', 'bat', 'cmd', 'sh', 'bash', 'ps1',
        'js', 'vbs', 'wsf', 'hta',
        'jar', 'jsp', 'asp', 'aspx',
    ];

    protected int $maxSize = 25 * 1024 * 1024; // 25 MB

    /**
     * Upload attachment(s) and send as message
     */
    public function store(Request $request, Conversation $conversation)
    {
        $user = auth()->user();

        if (!$conversation->hasParticipant($user->id)) {
            abort(403);
        }

        $request->validate([
            'files' => 'required|array|max:10',
            'files.*' => 'file|max:25600', // 25 MB in KB
            'content' => 'nullable|string|max:1000',
        ]);

        $attachments = [];

        foreach ($request->file('files') as $file) {
            $mimeType = $file->getMimeType();
            $extension = strtolower($file->getClientOriginalExtension());

            // SÉCURITÉ: Bloquer les extensions dangereuses
            if (in_array($extension, $this->dangerousExtensions)) {
                return response()->json([
                    'error' => "Extension de fichier non autorisée: .{$extension}"
                ], 422);
            }

            // SÉCURITÉ: Valider le MIME type
            if (!array_key_exists($mimeType, $this->allowedTypes)) {
                return response()->json([
                    'error' => "Type de fichier non autorisé: {$file->getClientOriginalName()}"
                ], 422);
            }

            // SÉCURITÉ: Vérifier que l'extension correspond au MIME type
            $allowedExtensions = $this->allowedTypes[$mimeType];
            if (!in_array($extension, $allowedExtensions)) {
                return response()->json([
                    'error' => "Extension '{$extension}' non valide pour ce type de fichier"
                ], 422);
            }

            // SÉCURITÉ: Générer un nom de fichier sécurisé (UUID + extension validée)
            $safeExtension = $allowedExtensions[0]; // Utiliser l'extension standard
            $filename = Str::uuid() . '.' . $safeExtension;
            
            // SÉCURITÉ: Stockage privé (non accessible publiquement)
            $path = $file->storeAs('messaging/attachments/' . date('Y/m'), $filename, 'local');

            $attachments[] = [
                'original_name' => $file->getClientOriginalName(),
                'path' => $path,
                'mime_type' => $mimeType,
                'size' => $file->getSize(),
                'metadata' => $this->getFileMetadata($file),
            ];
        }

        // Create message with attachments
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'type' => 'file',
            'content' => $request->content,
        ]);

        foreach ($attachments as $attachmentData) {
            $message->attachments()->create($attachmentData);
        }

        // Mark as read by sender
        $message->reads()->create(['user_id' => $user->id]);

        $message->load(['sender', 'attachments']);

        return response()->json([
            'message' => [
                'id' => $message->id,
                'conversation_id' => $message->conversation_id,
                'sender' => [
                    'id' => $message->sender->id,
                    'name' => $message->sender->name,
                    'avatar' => $message->sender->avatar ? avatar_url($message->sender->avatar) : null,
                ],
                'type' => $message->type,
                'content' => $message->content,
                'attachments' => $message->attachments->map(fn ($a) => [
                    'id' => $a->id,
                    'name' => $a->original_name,
                    // SÉCURITÉ: URL de téléchargement authentifiée au lieu d'accès direct
                    'url' => route('messaging.attachments.download', $a),
                    'type' => $a->mime_type,
                    'size' => $a->human_size,
                    'is_image' => $a->isImage(),
                ]),
                'created_at' => $message->created_at->toIso8601String(),
                'created_at_human' => $message->created_at->diffForHumans(),
            ],
        ], 201);
    }

    /**
     * Download an attachment (stockage privé sécurisé)
     */
    public function download(Attachment $attachment)
    {
        $user = auth()->user();
        $conversation = $attachment->message->conversation;

        // SÉCURITÉ: Vérifier que l'utilisateur a accès à cette conversation
        if (!$conversation->hasParticipant($user->id)) {
            abort(403, 'Accès non autorisé à ce fichier.');
        }

        // SÉCURITÉ: Vérifier que le fichier existe
        if (!Storage::disk('local')->exists($attachment->path)) {
            abort(404, 'Fichier introuvable.');
        }

        // Servir le fichier depuis le stockage privé
        return Storage::disk('local')->download($attachment->path, $attachment->original_name);
    }
    
    /**
     * Afficher une image (pour les previews)
     */
    public function show(Attachment $attachment)
    {
        $user = auth()->user();
        $conversation = $attachment->message->conversation;

        if (!$conversation->hasParticipant($user->id)) {
            abort(403);
        }

        if (!$attachment->isImage()) {
            abort(404);
        }

        return Storage::disk('local')->response($attachment->path);
    }

    /**
     * Delete an attachment
     */
    public function destroy(Attachment $attachment)
    {
        $user = auth()->user();
        $message = $attachment->message;

        // Only sender can delete
        if ($message->sender_id !== $user->id) {
            abort(403);
        }

        // Delete file from storage
        Storage::delete($attachment->path);

        $attachment->delete();

        // If no more attachments and no content, delete the message
        if ($message->attachments()->count() === 0 && empty($message->content)) {
            $message->delete();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Get file metadata (dimensions for images, duration for audio/video)
     */
    private function getFileMetadata($file): array
    {
        $metadata = [];

        if (str_starts_with($file->getMimeType(), 'image/')) {
            $imageInfo = getimagesize($file->getRealPath());
            if ($imageInfo) {
                $metadata['width'] = $imageInfo[0];
                $metadata['height'] = $imageInfo[1];
            }
        }

        return $metadata;
    }
}
