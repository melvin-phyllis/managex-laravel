<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Messaging\Conversation;
use App\Models\Messaging\Message;
use App\Models\Messaging\ConversationParticipant;
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
            'name' => 'required|string|max:100|unique:conversations,name,' . $conversation->id,
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
}
