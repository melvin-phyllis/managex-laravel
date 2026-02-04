<?php

namespace App\Http\Controllers\Messaging;

use App\Events\NewMessage;
use App\Http\Controllers\Controller;
use App\Models\Messaging\Conversation;
use App\Models\Messaging\Mention;
use App\Models\Messaging\Message;
use App\Models\Messaging\MessageRead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    /**
     * Get messages for a conversation
     */
    public function index(Conversation $conversation, Request $request)
    {
        $user = auth()->user();

        if (! $conversation->hasParticipant($user->id)) {
            abort(403);
        }

        $query = $conversation->messages()
            ->with(['sender', 'attachments', 'reactions.user', 'parent.sender', 'mentions.user']);

        // Polling: get messages after a specific ID
        if ($request->filled('after')) {
            $query->where('id', '>', $request->after)
                ->orderBy('created_at', 'asc');

            $messages = $query->limit(50)->get();

            return response()->json([
                'messages' => $messages->map(fn ($m) => $this->formatMessage($m)),
                'has_more' => false,
            ]);
        }

        // Pagination cursor-based for infinite scroll (older messages)
        if ($request->filled('before')) {
            $query->where('id', '<', $request->before);
        }

        $query->orderBy('created_at', 'desc');
        $messages = $query->limit(50)->get()->reverse()->values();

        return response()->json([
            'messages' => $messages->map(fn ($m) => $this->formatMessage($m)),
            'has_more' => $messages->count() === 50,
        ]);
    }

    /**
     * Send a message
     */
    public function store(Request $request, Conversation $conversation)
    {
        $user = auth()->user();

        if (! $conversation->hasParticipant($user->id)) {
            abort(403);
        }

        $request->validate([
            'content' => 'required_without:attachments|nullable|string|max:10000',
            'parent_id' => 'nullable|exists:messages,id',
            'attachments' => 'nullable|array',
        ]);

        return DB::transaction(function () use ($request, $conversation, $user) {
            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $user->id,
                'parent_id' => $request->parent_id,
                'type' => 'text',
                'content' => $request->content,
                'content_html' => $this->parseContent($request->content),
            ]);

            // Parse and create mentions
            $this->createMentions($message, $request->content);

            // Mark as read by sender
            MessageRead::create([
                'message_id' => $message->id,
                'user_id' => $user->id,
            ]);

            // Update participant's last read
            $conversation->participants()
                ->where('user_id', $user->id)
                ->update(['last_read_at' => now()]);

            // Load relationships
            $message->load(['sender', 'attachments', 'reactions', 'parent.sender']);

            // Broadcast for real-time updates
            try {
                broadcast(new NewMessage($message))->toOthers();
            } catch (\Exception $e) {
                \Log::debug('Broadcasting disabled or failed: '.$e->getMessage());
            }

            // Send notifications to other participants
            $otherParticipants = $conversation->activeParticipants()
                ->where('user_id', '!=', $user->id)
                ->with('user')
                ->get();

            foreach ($otherParticipants as $participant) {
                if ($participant->user && $participant->notifications !== 'none') {
                    // Check if mentions notification setting
                    if ($participant->notifications === 'mentions') {
                        // Only notify if mentioned
                        $isMentioned = $message->mentions()
                            ->where(function ($q) use ($participant) {
                                $q->where('user_id', $participant->user_id)
                                    ->orWhere('type', 'all');
                            })->exists();

                        if (! $isMentioned) {
                            continue;
                        }
                    }

                    $participant->user->notify(new \App\Notifications\NewMessageNotification($message));
                }
            }

            return response()->json([
                'message' => $this->formatMessage($message),
            ], 201);
        });
    }

    /**
     * Update a message
     */
    public function update(Request $request, Message $message)
    {
        $user = auth()->user();

        if ($message->sender_id !== $user->id) {
            abort(403, 'Vous ne pouvez modifier que vos propres messages.');
        }

        if ($message->created_at->diffInMinutes(now()) > 60) {
            abort(403, 'Vous ne pouvez plus modifier ce message (délai dépassé).');
        }

        $request->validate([
            'content' => 'required|string|max:10000',
        ]);

        $message->update([
            'content' => $request->content,
            'content_html' => $this->parseContent($request->content),
            'is_edited' => true,
            'edited_at' => now(),
        ]);

        // Update mentions
        $message->mentions()->delete();
        $this->createMentions($message, $request->content);

        $message->load(['sender', 'attachments', 'reactions', 'parent.sender']);

        return response()->json([
            'message' => $this->formatMessage($message),
        ]);
    }

    /**
     * Delete a message
     */
    public function destroy(Request $request, Message $message)
    {
        $user = auth()->user();
        $conversation = $message->conversation;

        // Can delete own messages or if admin of conversation
        $canDelete = $message->sender_id === $user->id
            || $conversation->isAdmin($user->id)
            || $user->isAdmin();

        if (! $canDelete) {
            abort(403);
        }

        $message->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Add a reaction to a message
     */
    public function addReaction(Request $request, Message $message)
    {
        $user = auth()->user();

        if (! $message->conversation->hasParticipant($user->id)) {
            abort(403);
        }

        $request->validate([
            'emoji' => 'required|string|max:50',
        ]);

        $message->reactions()->updateOrCreate(
            ['user_id' => $user->id, 'emoji' => $request->emoji],
            []
        );

        return response()->json([
            'reactions' => $message->groupedReactions(),
        ]);
    }

    /**
     * Remove a reaction from a message
     */
    public function removeReaction(Request $request, Message $message)
    {
        $user = auth()->user();

        $request->validate([
            'emoji' => 'required|string|max:50',
        ]);

        $message->reactions()
            ->where('user_id', $user->id)
            ->where('emoji', $request->emoji)
            ->delete();

        return response()->json([
            'reactions' => $message->groupedReactions(),
        ]);
    }

    /**
     * Mark messages as read
     */
    public function markAsRead(Conversation $conversation)
    {
        $user = auth()->user();

        if (! $conversation->hasParticipant($user->id)) {
            abort(403);
        }

        // Get unread messages
        $unreadMessages = $conversation->messages()
            ->whereDoesntHave('reads', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->where('sender_id', '!=', $user->id)
            ->get();

        // Create read receipts
        foreach ($unreadMessages as $message) {
            MessageRead::firstOrCreate([
                'message_id' => $message->id,
                'user_id' => $user->id,
            ]);
        }

        // Update participant's last read
        $conversation->participants()
            ->where('user_id', $user->id)
            ->update(['last_read_at' => now()]);

        return response()->json(['success' => true]);
    }

    /**
     * Parse content for HTML (basic markdown-like)
     */
    private function parseContent(?string $content): ?string
    {
        if (! $content) {
            return null;
        }

        $html = e($content);

        // Bold **text**
        $html = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $html);

        // Italic *text*
        $html = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $html);

        // Links
        $html = preg_replace(
            '/(https?:\/\/[^\s<]+)/',
            '<a href="$1" target="_blank" rel="noopener" class="text-blue-600 hover:underline">$1</a>',
            $html
        );

        // Newlines
        $html = nl2br($html);

        return $html;
    }

    /**
     * Create mentions from message content
     * Optimisé: une seule requête au lieu de N requêtes
     */
    private function createMentions(Message $message, ?string $content): void
    {
        if (! $content) {
            return;
        }

        // @all mention
        if (str_contains($content, '@tous') || str_contains($content, '@all')) {
            Mention::create([
                'message_id' => $message->id,
                'type' => 'all',
            ]);
        }

        // @user mentions - extract usernames
        preg_match_all('/@(\w+)/', $content, $matches);

        if (! empty($matches[1])) {
            // Filtrer les mentions @tous/@all
            $usernames = array_filter($matches[1], function ($username) {
                return ! in_array(strtolower($username), ['tous', 'all']);
            });

            if (empty($usernames)) {
                return;
            }

            // Charger tous les utilisateurs potentiels en UNE seule requête
            $query = \App\Models\User::query();
            foreach ($usernames as $index => $username) {
                if ($index === 0) {
                    $query->where('name', 'like', "%{$username}%");
                } else {
                    $query->orWhere('name', 'like', "%{$username}%");
                }
            }
            $matchedUsers = $query->get()->keyBy(function ($user) {
                return strtolower($user->name);
            });

            // Créer les mentions sans requêtes supplémentaires
            $mentionsToCreate = [];
            foreach ($usernames as $username) {
                // Chercher l'utilisateur dans la collection en mémoire
                $user = $matchedUsers->first(function ($u) use ($username) {
                    return stripos($u->name, $username) !== false;
                });

                if ($user) {
                    $mentionsToCreate[] = [
                        'message_id' => $message->id,
                        'user_id' => $user->id,
                        'type' => 'user',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            // Insertion en batch si des mentions existent
            if (! empty($mentionsToCreate)) {
                Mention::insert($mentionsToCreate);
            }
        }
    }

    /**
     * Format message for API response
     */
    private function formatMessage(Message $message): array
    {
        return [
            'id' => $message->id,
            'conversation_id' => $message->conversation_id,
            'sender_id' => $message->sender_id,
            'user_id' => $message->sender_id, // Alias pour compatibilité
            'sender' => $message->sender ? [
                'id' => $message->sender->id,
                'name' => $message->sender->name,
                'avatar' => $message->sender->avatar ? avatar_url($message->sender->avatar) : null,
            ] : null,
            'user' => $message->sender ? [
                'id' => $message->sender->id,
                'name' => $message->sender->name,
                'avatar' => $message->sender->avatar ? avatar_url($message->sender->avatar) : null,
            ] : null, // Alias pour compatibilité
            'type' => $message->type,
            'content' => $message->content,
            'content_html' => $message->content_html,
            'parent' => $message->parent ? [
                'id' => $message->parent->id,
                'content' => $message->parent->content,
                'sender_name' => $message->parent->sender?->name,
            ] : null,
            'attachments' => $message->attachments->map(fn ($a) => [
                'id' => $a->id,
                'name' => $a->original_name,
                'url' => ($a->isImage() || $a->isAudio()) ? route('messaging.api.attachments.show', $a) : route('messaging.api.attachments.download', $a),
                'download_url' => route('messaging.api.attachments.download', $a),
                'type' => $a->mime_type,
                'size' => $a->human_size,
                'is_image' => $a->isImage(),
                'is_audio' => $a->isAudio(),
            ]),
            'reactions' => $message->groupedReactions(),
            'is_edited' => $message->is_edited,
            'edited_at' => $message->edited_at?->diffForHumans(),
            'created_at' => $message->created_at->toIso8601String(),
            'created_at_human' => $message->created_at->diffForHumans(),
        ];
    }
}
