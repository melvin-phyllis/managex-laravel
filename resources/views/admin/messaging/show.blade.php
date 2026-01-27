<x-layouts.admin>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.messaging.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                    <x-icon name="arrow-left" class="w-5 h-5" />
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                        @if($conversation->type === 'channel')
                            <x-icon name="hash" class="w-6 h-6 text-blue-500" />
                        @elseif($conversation->type === 'announcement')
                            <x-icon name="megaphone" class="w-6 h-6 text-amber-500" />
                        @else
                            <x-icon name="users" class="w-6 h-6 text-gray-500" />
                        @endif
                        
                        @if($conversation->type === 'channel' || $conversation->type === 'announcement')
                            #{{ $conversation->name }}
                        @else
                            {{ $conversation->name ?? 'Conversation' }}
                        @endif
                    </h1>
                    <p class="text-gray-500 mt-1 flex items-center gap-1.5">
                        <x-icon name="info" class="w-3 h-3" />
                        {{ $conversation->description ?? ucfirst($conversation->type) }}
                    </p>
                </div>
            </div>
            
            <span class="inline-flex items-center px-3 py-1 text-sm rounded-full font-medium {{ $conversation->type === 'channel' ? 'bg-blue-100 text-blue-700' : ($conversation->type === 'announcement' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-700') }}">
                {{ ucfirst($conversation->type) }}
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Participants -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col h-[calc(100vh-16rem)] lg:h-auto">
                <h2 class="font-semibold text-gray-900 mb-4 flex items-center justify-between">
                    <span class="flex items-center gap-2">
                        <x-icon name="users" class="w-5 h-5 text-gray-500" />
                        Participants
                    </span>
                    <span class="bg-gray-100 text-gray-600 text-xs py-0.5 px-2 rounded-full">{{ $conversation->activeParticipants->count() }}</span>
                </h2>
                
                <div class="space-y-3 flex-1 overflow-y-auto custom-scrollbar pr-2 mb-4">
                    @foreach($conversation->activeParticipants as $participant)
                        <div class="flex items-center justify-between group p-2 hover:bg-gray-50 rounded-lg transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-xs font-semibold shadow-sm">
                                    {{ strtoupper(substr($participant->user->name ?? 'U', 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $participant->user->name ?? 'Utilisateur inconnu' }}</p>
                                    <p class="text-xs text-gray-500">{{ $participant->role }}</p>
                                </div>
                            </div>
                            @if($participant->user_id !== auth()->id())
                                <form action="{{ route('admin.messaging.participants.remove', [$conversation, $participant->user]) }}" method="POST" onsubmit="return confirm('Retirer ce participant ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-gray-300 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors opacity-0 group-hover:opacity-100">
                                        <x-icon name="trash-2" class="w-4 h-4" />
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Add participant form -->
                <form action="{{ route('admin.messaging.participants.add', $conversation) }}" method="POST" class="pt-4 border-t border-gray-100">
                    @csrf
                    <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center gap-1.5">
                        <x-icon name="user-plus" class="w-4 h-4" />
                        Ajouter un participant
                    </label>
                    <div class="flex gap-2">
                        <select name="user_id" required class="flex-1 rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Sélectionner...</option>
                            @foreach(\App\Models\User::whereNotIn('id', $conversation->activeParticipants->pluck('user_id'))->get() as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm transition-colors shadow-sm">
                            <x-icon name="plus" class="w-5 h-5" />
                        </button>
                    </div>
                </form>
            </div>

            <!-- Messages -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col h-[600px] lg:h-auto">
                <div class="p-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50 rounded-t-xl">
                    <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                        <x-icon name="message-square" class="w-5 h-5 text-gray-500" />
                        Messages 
                        <span class="text-sm font-normal text-gray-500">({{ $messages->total() }})</span>
                    </h2>
                </div>

                <div class="flex-1 divide-y divide-gray-100 overflow-y-auto custom-scrollbar p-2">
                    @forelse($messages as $message)
                        <div class="p-3 hover:bg-gray-50 rounded-lg transition-colors group">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start gap-3">
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0 shadow-sm mt-0.5">
                                        {{ $message->sender ? strtoupper(substr($message->sender->name, 0, 1)) : 'S' }}
                                    </div>
                                    <div class="min-w-0 max-w-2xl">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <p class="text-sm font-bold text-gray-900">{{ $message->sender?->name ?? 'Système' }}</p>
                                            <span class="text-xs text-gray-400">{{ $message->created_at->format('d/m/Y H:i') }}</span>
                                            @if($message->is_edited)
                                                <span class="text-xs text-gray-400 italic">(modifié)</span>
                                            @endif
                                        </div>
                                        <div class="text-sm text-gray-800 mt-1 leading-relaxed whitespace-pre-wrap">{{ $message->content }}</div>
                                        
                                        @if($message->attachments->count() > 0)
                                            <div class="mt-2 flex flex-wrap gap-2">
                                                @foreach($message->attachments as $attachment)
                                                    <a href="#" class="inline-flex items-center gap-1.5 text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-2.5 py-1.5 rounded-lg transition-colors border border-gray-200">
                                                        <x-icon name="paperclip" class="w-3 h-3" />
                                                        {{ $attachment->original_name }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <form action="{{ route('admin.messaging.message.delete', $message) }}" method="POST" onsubmit="return confirm('Supprimer ce message ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-gray-300 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors opacity-0 group-hover:opacity-100">
                                        <x-icon name="trash-2" class="w-4 h-4" />
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="h-full flex flex-col items-center justify-center text-center p-8 text-gray-500">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                <x-icon name="message-circle-off" class="w-8 h-8 text-gray-300" />
                            </div>
                            <p>Aucun message dans cette conversation</p>
                        </div>
                    @endforelse
                </div>

                @if($messages->hasPages())
                    <div class="p-4 border-t border-gray-100 bg-gray-50/30 rounded-b-xl">
                        {{ $messages->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Danger Zone -->
        <div class="bg-red-50 rounded-xl border border-red-200 p-6 mt-8">
            <div class="flex items-start gap-4">
                <div class="p-3 bg-red-100 rounded-lg">
                    <x-icon name="alert-triangle" class="w-6 h-6 text-red-600" />
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-red-800 mb-1">Zone de danger</h3>
                    <p class="text-sm text-red-600 mb-4">Cette action est irréversible. Tous les messages, fichiers et l'historique des participants seront définitivement supprimés.</p>
                    <form action="{{ route('admin.messaging.destroy', $conversation) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette conversation ? Cette action est irréversible.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2.5 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors shadow-lg shadow-red-500/20">
                            <x-icon name="trash-2" class="w-4 h-4 mr-2" />
                            Supprimer cette conversation
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
