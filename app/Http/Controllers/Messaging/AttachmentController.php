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
     * Allowed file types and max size
     */
    protected array $allowedTypes = [
        'image/jpeg', 'image/png', 'image/gif', 'image/webp',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'video/mp4', 'video/webm',
        'audio/mpeg', 'audio/wav', 'audio/ogg',
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
            // Validate mime type
            if (!in_array($file->getMimeType(), $this->allowedTypes)) {
                return response()->json([
                    'error' => "Type de fichier non autorisé: {$file->getClientOriginalName()}"
                ], 422);
            }

            // Generate unique filename
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('messaging/attachments/' . date('Y/m'), $filename, 'public');

            $attachments[] = [
                'original_name' => $file->getClientOriginalName(),
                'path' => $path,
                'mime_type' => $file->getMimeType(),
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
                    'avatar' => $message->sender->avatar,
                ],
                'type' => $message->type,
                'content' => $message->content,
                'attachments' => $message->attachments->map(fn ($a) => [
                    'id' => $a->id,
                    'name' => $a->original_name,
                    'url' => Storage::url($a->path),
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
     * Download an attachment
     */
    public function download(Attachment $attachment)
    {
        $user = auth()->user();
        $conversation = $attachment->message->conversation;

        if (!$conversation->hasParticipant($user->id)) {
            abort(403);
        }

        return Storage::download($attachment->path, $attachment->original_name);
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
