<?php

namespace App\Http\Controllers\Admin;

use App\Events\NewMessage;
use App\Http\Controllers\Controller;
use App\Models\Messaging\Conversation;
use App\Models\Messaging\ConversationParticipant;
use App\Models\Messaging\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessagingController extends Controller
{
    /**
     * Display messaging admin dashboard
     */
    public function index()
    {
        $stats = [
            'total_conversations' => Conversation::count(),
            'total_messages' => Message::count(),
            'messages_today' => Message::whereDate('created_at', today())->count(),
            'messages_this_week' => Message::whereBetween('created_at', [now()->startOfWeek(), now()])->count(),
            'active_users' => ConversationParticipant::distinct('user_id')->count('user_id'),
            'channels' => Conversation::whereIn('type', ['channel', 'announcement'])->count(),
            'groups' => Conversation::where('type', 'group')->count(),
            'direct' => Conversation::where('type', 'direct')->count(),
        ];

        $recentMessages = Message::with(['sender', 'conversation'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        $topConversations = Conversation::withCount('messages')
            ->orderBy('messages_count', 'desc')
            ->limit(10)
            ->get();

        $channels = Conversation::whereIn('type', ['channel', 'announcement'])
            ->withCount(['activeParticipants', 'messages'])
            ->orderBy('name')
            ->get();

        return view('admin.messaging.index', compact('stats', 'recentMessages', 'topConversations', 'channels'));
    }

    /**
     * Display the chat interface
     */
    public function chat()
    {
        $user = auth()->user();

        $conversationsData = Conversation::forUser($user->id)
            ->with(['activeParticipants.user', 'latestMessage.sender'])
            ->get();

        $conversations = $conversationsData->map(function ($conv) use ($user) {
            $otherUser = null;
            if ($conv->type === 'direct') {
                $otherParticipant = $conv->activeParticipants
                    ->where('user_id', '!=', $user->id)
                    ->first();
                $otherUser = $otherParticipant?->user;
            }

            return [
                'id' => $conv->id,
                'type' => $conv->type,
                'name' => $conv->name,
                'description' => $conv->description,
                'avatar' => $conv->avatar ? avatar_url($conv->avatar) : null,
                'unread_count' => $conv->unreadCountFor($user->id),
                'last_message' => $conv->latestMessage?->content,
                'last_message_at' => $conv->latestMessage?->created_at?->toIso8601String(),
                'other_user' => $otherUser ? [
                    'id' => $otherUser->id,
                    'name' => $otherUser->name,
                    'avatar' => $otherUser->avatar ? avatar_url($otherUser->avatar) : null,
                ] : null,
                'participants' => $conv->activeParticipants->map(fn ($p) => [
                    'id' => $p->user_id,
                    'name' => $p->user?->name,
                    'role' => $p->role,
                ])->values(),
            ];
        })
            ->sortByDesc('last_message_at')
            ->values();

        $users = User::where('id', '!=', $user->id)
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        return view('admin.messaging.chat', compact('conversations', 'users'));
    }

    /**
     * View a specific conversation
     */
    public function show(Conversation $conversation)
    {
        $conversation->load(['activeParticipants.user', 'creator']);

        $messages = $conversation->messages()
            ->with(['sender', 'attachments'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('admin.messaging.show', compact('conversation', 'messages'));
    }

    /**
     * Create a new channel/announcement
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:conversations,name',
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:channel,announcement',
        ]);

        $conversation = Conversation::create([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'created_by' => auth()->id(),
        ]);

        // Add admin as participant
        $conversation->participants()->create([
            'user_id' => auth()->id(),
            'role' => 'admin',
            'joined_at' => now(),
        ]);

        // If "add all users" is checked
        if ($request->boolean('add_all_users')) {
            $users = User::where('id', '!=', auth()->id())->get();
            foreach ($users as $user) {
                $conversation->participants()->create([
                    'user_id' => $user->id,
                    'role' => 'member',
                    'joined_at' => now(),
                ]);
            }
        }

        return redirect()->route('admin.messaging.index')
            ->with('success', "Canal '{$conversation->name}' créé avec succès.");
    }

    /**
     * Update a channel
     */
    public function update(Request $request, Conversation $conversation)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:conversations,name,'.$conversation->id,
            'description' => 'nullable|string|max:500',
        ]);

        $conversation->update($request->only(['name', 'description']));

        return redirect()->back()->with('success', 'Canal mis à jour.');
    }

    /**
     * Delete a conversation
     */
    public function destroy(Conversation $conversation)
    {
        $name = $conversation->name;
        $conversation->delete();

        return redirect()->route('admin.messaging.index')
            ->with('success', "Canal '{$name}' supprimé.");
    }

    /**
     * Add user to a channel
     */
    public function addParticipant(Request $request, Conversation $conversation)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $conversation->participants()->firstOrCreate(
            ['user_id' => $request->user_id],
            ['role' => 'member', 'joined_at' => now()]
        );

        return redirect()->back()->with('success', 'Participant ajouté.');
    }

    /**
     * Remove user from a channel
     */
    public function removeParticipant(Conversation $conversation, User $user)
    {
        $conversation->participants()
            ->where('user_id', $user->id)
            ->update(['left_at' => now()]);

        return redirect()->back()->with('success', 'Participant retiré.');
    }

    /**
     * Delete a message (moderation)
     */
    public function deleteMessage(Message $message)
    {
        $message->delete();

        return redirect()->back()->with('success', 'Message supprimé.');
    }

    /**
     * Mark conversation as read for current user
     */
    public function markAsRead(Conversation $conversation)
    {
        $userId = auth()->id();

        $participant = $conversation->participants()
            ->where('user_id', $userId)
            ->whereNull('left_at')
            ->first();

        if ($participant) {
            $participant->update(['last_read_at' => now()]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Get messages for a conversation (API)
     */
    public function getMessages(Request $request, Conversation $conversation)
    {
        $query = $conversation->messages()
            ->with(['sender', 'attachments'])
            ->orderBy('created_at', 'asc');

        // Support polling with 'after' parameter
        if ($request->filled('after')) {
            $query->where('id', '>', $request->after);
            $messages = $query->limit(50)->get();

            return response()->json([
                'data' => $messages->map(fn ($m) => [
                    'id' => $m->id,
                    'conversation_id' => $m->conversation_id,
                    'sender_id' => $m->sender_id,
                    'user_id' => $m->sender_id, // Alias pour compatibilité
                    'sender' => $m->sender ? [
                        'id' => $m->sender->id,
                        'name' => $m->sender->name,
                        'avatar' => $m->sender->avatar ? avatar_url($m->sender->avatar) : null,
                    ] : null,
                    'user' => $m->sender ? [
                        'id' => $m->sender->id,
                        'name' => $m->sender->name,
                        'avatar' => $m->sender->avatar ? avatar_url($m->sender->avatar) : null,
                    ] : null,
                    'content' => $m->content,
                    'attachments' => $this->formatAttachments($m->attachments),
                    'created_at' => $m->created_at->toIso8601String(),
                ]),
            ]);
        }

        $messages = $query->paginate(50);

        // Transform data for consistent format
        $messages->getCollection()->transform(fn ($m) => [
            'id' => $m->id,
            'conversation_id' => $m->conversation_id,
            'sender_id' => $m->sender_id,
            'user_id' => $m->sender_id,
            'sender' => $m->sender ? [
                'id' => $m->sender->id,
                'name' => $m->sender->name,
                'avatar' => $m->sender->avatar ? avatar_url($m->sender->avatar) : null,
            ] : null,
            'user' => $m->sender ? [
                'id' => $m->sender->id,
                'name' => $m->sender->name,
                'avatar' => $m->sender->avatar ? avatar_url($m->sender->avatar) : null,
            ] : null,
            'content' => $m->content,
            'attachments' => $this->formatAttachments($m->attachments),
            'created_at' => $m->created_at->toIso8601String(),
        ]);

        return response()->json($messages);
    }

    /**
     * Store a new message (API)
     */
    public function storeMessage(Request $request, Conversation $conversation)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $message = $conversation->messages()->create([
            'sender_id' => auth()->id(),
            'content' => $request->content,
        ]);

        $message->load('sender');

        // Broadcast the message (if broadcasting is configured)
        try {
            broadcast(new NewMessage($message))->toOthers();
        } catch (\Exception $e) {
            \Log::debug('Broadcasting disabled or failed: '.$e->getMessage());
        }

        return response()->json([
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
            ] : null,
            'content' => $message->content,
            'created_at' => $message->created_at->toIso8601String(),
        ]);
    }

    /**
     * Format attachments for JSON (urls for display/download)
     */
    private function formatAttachments($attachments): array
    {
        return $attachments->map(fn ($a) => [
            'id' => $a->id,
            'name' => $a->original_name,
            'url' => ($a->isImage() || $a->isAudio()) ? route('messaging.api.attachments.show', $a) : route('messaging.api.attachments.download', $a),
            'download_url' => route('messaging.api.attachments.download', $a),
            'type' => $a->mime_type,
            'size' => $a->human_size,
            'is_image' => $a->isImage(),
            'is_audio' => $a->isAudio(),
        ])->toArray();
    }
}
