<?php

namespace App\Http\Controllers\Messaging;

use App\Http\Controllers\Controller;
use App\Models\Messaging\Conversation;
use App\Models\Messaging\ConversationParticipant;
use App\Models\Messaging\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConversationController extends Controller
{
    /**
     * Display the messaging interface
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $conversations = Conversation::forUser($user->id)
            ->with(['latestMessage.sender', 'activeParticipants.user'])
            ->withCount(['messages as unread_count' => function ($query) use ($user) {
                $query->whereDoesntHave('reads', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->where('sender_id', '!=', $user->id);
            }])
            ->get()
            ->sortByDesc(fn ($c) => $c->latestMessage?->created_at ?? $c->created_at);

        return view('messaging.index', compact('conversations'));
    }

    /**
     * Display the messaging interface with admin layout
     */
    public function adminChat(Request $request)
    {
        $user = auth()->user();
        
        $conversations = Conversation::forUser($user->id)
            ->with(['latestMessage.sender', 'activeParticipants.user'])
            ->withCount(['messages as unread_count' => function ($query) use ($user) {
                $query->whereDoesntHave('reads', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->where('sender_id', '!=', $user->id);
            }])
            ->get()
            ->map(function ($conv) use ($user) {
                $otherUser = $conv->type === 'direct' 
                    ? $conv->activeParticipants->where('user_id', '!=', $user->id)->first()?->user 
                    : null;
                return [
                    'id' => $conv->id,
                    'type' => $conv->type,
                    'name' => $conv->name ?? $otherUser?->name ?? 'Conversation',
                    'other_user' => $otherUser ? ['name' => $otherUser->name, 'avatar' => $otherUser->avatar] : null,
                    'last_message' => $conv->latestMessage?->content,
                    'last_message_at' => $conv->latestMessage?->created_at,
                    'unread_count' => $conv->unread_count ?? 0,
                ];
            })
            ->sortByDesc('last_message_at')
            ->values();

        $users = User::where('id', '!=', $user->id)->get(['id', 'name', 'email']);

        return view('admin.messaging.chat', compact('conversations', 'users'));
    }

    /**
     * Get conversation list (API)
     */
    public function list(Request $request)
    {
        $user = auth()->user();
        
        $query = Conversation::forUser($user->id)
            ->with(['latestMessage.sender', 'activeParticipants.user']);

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter archived
        if ($request->boolean('archived')) {
            $query->whereHas('participants', function ($q) use ($user) {
                $q->where('user_id', $user->id)->where('is_archived', true);
            });
        } else {
            $query->whereHas('participants', function ($q) use ($user) {
                $q->where('user_id', $user->id)->where('is_archived', false);
            });
        }

        $conversations = $query->get()
            ->map(function ($conversation) use ($user) {
                $otherUser = null;
                if ($conversation->type === 'direct') {
                    $otherParticipant = $conversation->activeParticipants
                        ->where('user_id', '!=', $user->id)
                        ->first();
                    $otherUser = $otherParticipant?->user;
                }

                return [
                    'id' => $conversation->id,
                    'type' => $conversation->type,
                    'name' => $this->getConversationName($conversation, $user),
                    'avatar' => $this->getConversationAvatar($conversation, $user),
                    'last_message' => $conversation->latestMessage?->content,
                    'last_message_at' => $conversation->latestMessage?->created_at?->toIso8601String(),
                    'unread_count' => $conversation->unreadCountFor($user->id),
                    'is_pinned' => $conversation->participants->where('user_id', $user->id)->first()?->is_pinned ?? false,
                    'other_user' => $otherUser ? [
                        'id' => $otherUser->id,
                        'name' => $otherUser->name,
                        'avatar' => $otherUser->avatar ?? null,
                    ] : null,
                    'participants' => $conversation->activeParticipants->map(fn($p) => [
                        'id' => $p->user_id,
                        'name' => $p->user?->name,
                        'role' => $p->role,
                    ])->values(),
                ];
            })
            ->sortByDesc('is_pinned')
            ->values();

        return response()->json($conversations);
    }

    /**
     * Show a conversation
     */
    public function show(Conversation $conversation)
    {
        $user = auth()->user();

        if (!$conversation->hasParticipant($user->id)) {
            abort(403, 'Vous n\'êtes pas membre de cette conversation.');
        }

        $messages = $conversation->messages()
            ->with(['sender', 'attachments', 'reactions.user', 'parent.sender'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        // Mark as read
        $participant = $conversation->participants()->where('user_id', $user->id)->first();
        $participant?->markAsRead();

        $participants = $conversation->activeParticipants()->with('user')->get();

        return response()->json([
            'conversation' => [
                'id' => $conversation->id,
                'type' => $conversation->type,
                'name' => $this->getConversationName($conversation, $user),
                'description' => $conversation->description,
                'avatar' => $this->getConversationAvatar($conversation, $user),
            ],
            'messages' => $messages,
            'participants' => $participants->map(fn ($p) => [
                'id' => $p->user->id,
                'name' => $p->user->name,
                'avatar' => $p->user->avatar,
                'role' => $p->role,
            ]),
        ]);
    }

    /**
     * Create a new conversation
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:direct,group,channel',
            'name' => 'required_if:type,group,channel|nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'participants' => 'required|array|min:1',
            'participants.*' => 'exists:users,id',
        ]);

        $user = auth()->user();
        $type = $request->type;
        $participantIds = collect($request->participants)->push($user->id)->unique();

        // For direct messages, check if conversation already exists
        if ($type === 'direct' && $participantIds->count() === 2) {
            $existingConversation = $this->findDirectConversation($participantIds->toArray());
            if ($existingConversation) {
                return response()->json(['conversation' => $existingConversation], 200);
            }
        }

        return DB::transaction(function () use ($request, $user, $type, $participantIds) {
            $conversation = Conversation::create([
                'type' => $type,
                'name' => $request->name,
                'description' => $request->description,
                'created_by' => $user->id,
            ]);

            // Add participants
            foreach ($participantIds as $participantId) {
                ConversationParticipant::create([
                    'conversation_id' => $conversation->id,
                    'user_id' => $participantId,
                    'role' => $participantId === $user->id ? 'admin' : 'member',
                    'joined_at' => now(),
                ]);
            }

            // Create system message
            if ($type !== 'direct') {
                Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => null,
                    'type' => 'system',
                    'content' => "{$user->name} a créé la conversation",
                ]);
            }

            return response()->json(['conversation' => $conversation->load('activeParticipants.user')], 201);
        });
    }

    /**
     * Update a conversation
     */
    public function update(Request $request, Conversation $conversation)
    {
        $user = auth()->user();

        if (!$conversation->isAdmin($user->id) && !$user->isAdmin()) {
            abort(403, 'Vous n\'avez pas les droits pour modifier cette conversation.');
        }

        $request->validate([
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $conversation->update($request->only(['name', 'description']));

        return response()->json(['conversation' => $conversation]);
    }

    /**
     * Leave or delete a conversation
     */
    public function destroy(Conversation $conversation)
    {
        $user = auth()->user();
        $participant = $conversation->participants()->where('user_id', $user->id)->first();

        if (!$participant) {
            abort(403);
        }

        if ($conversation->type === 'direct') {
            // Archive for this user
            $participant->update(['is_archived' => true]);
        } else {
            // Leave the conversation
            $participant->update(['left_at' => now()]);

            // Create system message
            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => null,
                'type' => 'system',
                'content' => "{$user->name} a quitté la conversation",
            ]);

            // If no active participants left, soft delete
            if ($conversation->activeParticipants()->count() === 0) {
                $conversation->delete();
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Toggle pin status
     */
    public function togglePin(Conversation $conversation)
    {
        $user = auth()->user();
        $participant = $conversation->participants()->where('user_id', $user->id)->first();

        if (!$participant) {
            abort(403);
        }

        $participant->togglePin();

        return response()->json(['is_pinned' => $participant->fresh()->is_pinned]);
    }

    /**
     * Toggle archive status
     */
    public function toggleArchive(Conversation $conversation)
    {
        $user = auth()->user();
        $participant = $conversation->participants()->where('user_id', $user->id)->first();

        if (!$participant) {
            abort(403);
        }

        $participant->toggleArchive();

        return response()->json(['is_archived' => $participant->fresh()->is_archived]);
    }

    /**
     * Get conversation name for a user
     */
    private function getConversationName(Conversation $conversation, User $user): string
    {
        if ($conversation->type === 'direct') {
            $otherParticipant = $conversation->activeParticipants
                ->where('user_id', '!=', $user->id)
                ->first();
            return $otherParticipant?->user?->name ?? 'Utilisateur supprimé';
        }

        return $conversation->name ?? 'Conversation sans nom';
    }

    /**
     * Get conversation avatar for a user
     */
    private function getConversationAvatar(Conversation $conversation, User $user): ?string
    {
        if ($conversation->avatar) {
            return $conversation->avatar;
        }

        if ($conversation->type === 'direct') {
            $otherParticipant = $conversation->activeParticipants
                ->where('user_id', '!=', $user->id)
                ->first();
            return $otherParticipant?->user?->avatar;
        }

        return null;
    }

    /**
     * Find existing direct conversation between users
     */
    private function findDirectConversation(array $userIds): ?Conversation
    {
        return Conversation::where('type', 'direct')
            ->whereHas('participants', function ($q) use ($userIds) {
                $q->whereIn('user_id', $userIds);
            }, '=', count($userIds))
            ->whereDoesntHave('participants', function ($q) use ($userIds) {
                $q->whereNotIn('user_id', $userIds);
            })
            ->first();
    }
}
