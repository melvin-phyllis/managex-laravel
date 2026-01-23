<x-layouts.admin>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.messaging.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        @if($conversation->type === 'channel' || $conversation->type === 'announcement')
                            #{{ $conversation->name }}
                        @else
                            {{ $conversation->name ?? 'Conversation' }}
                        @endif
                    </h1>
                    <p class="text-gray-500 mt-1">{{ $conversation->description ?? ucfirst($conversation->type) }}</p>
                </div>
            </div>
            <span class="px-3 py-1 text-sm rounded-full {{ $conversation->type === 'channel' ? 'bg-blue-100 text-blue-700' : ($conversation->type === 'announcement' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700') }}">
                {{ ucfirst($conversation->type) }}
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Participants -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="font-semibold text-gray-900 mb-4">Participants ({{ $conversation->activeParticipants->count() }})</h2>
                
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @foreach($conversation->activeParticipants as $participant)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-xs font-semibold">
                                    {{ strtoupper(substr($participant->user->name ?? 'U', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $participant->user->name ?? 'Utilisateur inconnu' }}</p>
                                    <p class="text-xs text-gray-500">{{ $participant->role }}</p>
                                </div>
                            </div>
                            @if($participant->user_id !== auth()->id())
                                <form action="{{ route('admin.messaging.participants.remove', [$conversation, $participant->user]) }}" method="POST" onsubmit="return confirm('Retirer ce participant ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 text-gray-400 hover:text-red-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Add participant form -->
                <form action="{{ route('admin.messaging.participants.add', $conversation) }}" method="POST" class="mt-4 pt-4 border-t border-gray-100">
                    @csrf
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ajouter un participant</label>
                    <div class="flex gap-2">
                        <select name="user_id" required class="flex-1 rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Sélectionner...</option>
                            @foreach(\App\Models\User::whereNotIn('id', $conversation->activeParticipants->pluck('user_id'))->get() as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                            Ajouter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Messages -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-semibold text-gray-900">Messages ({{ $messages->total() }})</h2>
                </div>

                <div class="divide-y divide-gray-100 max-h-[500px] overflow-y-auto">
                    @forelse($messages as $message)
                        <div class="p-4 hover:bg-gray-50">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-xs font-semibold flex-shrink-0">
                                        {{ $message->sender ? strtoupper(substr($message->sender->name, 0, 1)) : 'S' }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <p class="text-sm font-medium text-gray-900">{{ $message->sender?->name ?? 'Système' }}</p>
                                            <span class="text-xs text-gray-400">{{ $message->created_at->format('d/m/Y H:i') }}</span>
                                            @if($message->is_edited)
                                                <span class="text-xs text-gray-400">(modifié)</span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">{{ $message->content }}</p>
                                        
                                        @if($message->attachments->count() > 0)
                                            <div class="mt-2 flex flex-wrap gap-2">
                                                @foreach($message->attachments as $attachment)
                                                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">
                                                        📎 {{ $attachment->original_name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <form action="{{ route('admin.messaging.message.delete', $message) }}" method="POST" onsubmit="return confirm('Supprimer ce message ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 text-gray-400 hover:text-red-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500">
                            Aucun message dans cette conversation
                        </div>
                    @endforelse
                </div>

                @if($messages->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $messages->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Danger Zone -->
        <div class="bg-red-50 rounded-xl border border-red-200 p-6">
            <h3 class="text-lg font-semibold text-red-800 mb-2">Zone de danger</h3>
            <p class="text-sm text-red-600 mb-4">Cette action est irréversible. Tous les messages et participants seront supprimés.</p>
            <form action="{{ route('admin.messaging.destroy', $conversation) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette conversation ? Cette action est irréversible.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Supprimer cette conversation
                </button>
            </form>
        </div>
    </div>
</x-layouts.admin>
