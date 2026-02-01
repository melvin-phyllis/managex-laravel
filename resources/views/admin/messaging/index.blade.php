<x-layouts.admin>
    <div class="space-y-6" x-data="{ showCreateModal: false }">
        <!-- Header -->
        <x-table-header title="Messagerie & Canaux" subtitle="Gérez les canaux de communication, les groupes et modérez les échanges" class="animate-fade-in-up">
            <x-slot:icon>
                <div class="w-12 h-12 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-cyan-500/20">
                    <x-icon name="messages-square" class="w-6 h-6 text-white" />
                </div>
            </x-slot:icon>
            <x-slot:actions>
                <button @click="showCreateModal = true" class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-cyan-600 to-blue-600 text-white font-medium rounded-xl hover:from-cyan-700 hover:to-blue-700 transition-all shadow-lg shadow-cyan-500/25">
                    <x-icon name="plus" class="w-5 h-5 mr-2" />
                    Nouveau canal
                </button>
            </x-slot:actions>
        </x-table-header>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Messages -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow animate-fade-in-up animation-delay-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Messages</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_messages'] ?? 0) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/30">
                        <x-icon name="message-circle" class="w-6 h-6 text-white" />
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <span class="text-green-600 bg-green-50 px-2 py-0.5 rounded-full font-medium flex items-center">
                        <x-icon name="trending-up" class="w-3 h-3 mr-1" />
                        +{{ $stats['messages_today'] ?? 0 }}
                    </span>
                    <span class="text-gray-500 ml-2">aujourd'hui</span>
                </div>
            </div>

            <!-- Conversations -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow animate-fade-in-up animation-delay-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Conversations</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['total_conversations'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg shadow-green-500/30">
                        <x-icon name="copy" class="w-6 h-6 text-white" />
                    </div>
                </div>
                <div class="mt-4 text-xs text-gray-500 flex items-center gap-2">
                    <span class="px-2 py-1 bg-gray-100 rounded-md">{{ $stats['channels'] ?? 0 }} canaux</span>
                    <span class="px-2 py-1 bg-gray-100 rounded-md">{{ $stats['groups'] ?? 0 }} groupes</span>
                </div>
            </div>

            <!-- Active Users -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow animate-fade-in-up animation-delay-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Utilisateurs actifs</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['active_users'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-fuchsia-600 rounded-xl flex items-center justify-center shadow-lg shadow-purple-500/30">
                        <x-icon name="users" class="w-6 h-6 text-white" />
                    </div>
                </div>
                <div class="mt-4 text-xs text-gray-500">
                    Participants aux échanges
                </div>
            </div>

            <!-- This Week -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow animate-fade-in-up animation-delay-400">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Cette semaine</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['messages_this_week'] ?? 0) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-400 to-amber-500 rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/30">
                        <x-icon name="bar-chart" class="w-6 h-6 text-white" />
                    </div>
                </div>
                <div class="mt-4 text-xs text-gray-500">
                    Messages envoyés
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fade-in-up animation-delay-300">
            <!-- Channels List -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden lg:col-span-1">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <x-icon name="hash" class="w-4 h-4 text-gray-500" />
                        Canaux & Annonces
                    </h3>
                    <span class="text-xs font-medium bg-gray-200 text-gray-600 px-2 py-0.5 rounded-full">{{ count($channels) }}</span>
                </div>
                <div class="divide-y divide-gray-100 max-h-[500px] overflow-y-auto custom-scrollbar">
                    @forelse($channels as $channel)
                        <div class="px-5 py-3 hover:bg-gray-50 flex items-center justify-between group transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 {{ $channel->type === 'announcement' ? 'bg-amber-100 text-amber-600' : 'bg-blue-100 text-blue-600' }}">
                                    @if($channel->type === 'announcement')
                                        <x-icon name="megaphone" class="w-5 h-5" />
                                    @else
                                        <x-icon name="hash" class="w-5 h-5" />
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="font-medium text-gray-900 truncate">#{{ $channel->name }}</p>
                                    <p class="text-xs text-gray-500 truncate flex items-center gap-1">
                                        <x-icon name="users" class="w-3 h-3" /> {{ $channel->active_participants_count }}
                                        <span class="mx-1">•</span>
                                        {{ $channel->messages_count }} msgs
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('admin.messaging.show', $channel) }}" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                    <x-icon name="eye" class="w-4 h-4" />
                                </a>
                                <form action="{{ route('admin.messaging.destroy', $channel) }}" method="POST" onsubmit="return confirm('Supprimer ce canal ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                        <x-icon name="trash-2" class="w-4 h-4" />
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500">
                            Aucun canal actif
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Messages & Top Conversations -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Recent Messages -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                            <x-icon name="clock" class="w-4 h-4 text-gray-500" />
                            Messages Récents
                        </h3>
                    </div>
                    <div class="divide-y divide-gray-100 max-h-96 overflow-y-auto custom-scrollbar">
                        @forelse($recentMessages as $message)
                            <div class="px-5 py-3 hover:bg-gray-50 transition-colors">
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0 shadow-sm">
                                        {{ $message->sender ? strtoupper(substr($message->sender->name, 0, 1)) : 'S' }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $message->sender?->name ?? 'Système' }}
                                                <span class="font-normal text-gray-400 mx-1">dans</span>
                                                <span class="font-medium text-blue-600">#{{ $message->conversation->name ?? 'DM' }}</span>
                                            </p>
                                            <span class="text-xs text-gray-400 whitespace-nowrap">{{ $message->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-sm text-gray-600 truncate mt-0.5">{{ Str::limit($message->content, 80) }}</p>
                                    </div>
                                    <form action="{{ route('admin.messaging.message.delete', $message) }}" method="POST" onsubmit="return confirm('Supprimer ce message ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                            <x-icon name="trash-2" class="w-4 h-4" />
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-gray-500">
                                Aucun message récent
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Top Conversations Table -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                            <x-icon name="trending-up" class="w-4 h-4 text-gray-500" />
                            Conversations les plus actives
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Conversation</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Messages</th>
                                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($topConversations as $conv)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-5 py-3 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                @if($conv->type === 'channel')
                                                    <x-icon name="hash" class="w-4 h-4 text-blue-500" />
                                                @elseif($conv->type === 'announcement')
                                                    <x-icon name="megaphone" class="w-4 h-4 text-amber-500" />
                                                @else
                                                    <x-icon name="users" class="w-4 h-4 text-gray-500" />
                                                @endif
                                                <span class="font-medium text-gray-900 text-sm">{{ $conv->name ?? 'Message Direct' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-5 py-3 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $conv->type === 'channel' ? 'bg-blue-100 text-blue-700' : ($conv->type === 'announcement' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-700') }}">
                                                {{ ucfirst($conv->type) }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3 whitespace-nowrap text-sm text-gray-600 font-mono">
                                            {{ number_format($conv->messages_count) }}
                                        </td>
                                        <td class="px-5 py-3 whitespace-nowrap text-right">
                                            <a href="{{ route('admin.messaging.show', $conv) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium hover:underline">Voir</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Channel Modal -->
        <div x-show="showCreateModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             style="display: none;"
             class="fixed inset-0 z-50 flex items-center justify-center p-4">
             
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showCreateModal = false"></div>

            <!-- Modal Content -->
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 transform transition-all"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-4">
                 
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <x-icon name="plus" class="w-5 h-5 text-blue-600" />
                        </div>
                        Nouveau canal
                    </h3>
                    <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-lg transition-colors">
                        <x-icon name="x" class="w-5 h-5" />
                    </button>
                </div>

                <form action="{{ route('admin.messaging.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nom du canal</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold">#</span>
                            <input type="text" name="name" required class="w-full pl-7 pr-4 py-2.5 rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="general">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="3" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="À quoi sert ce canal ?"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type de canal</label>
                        <select name="type" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="channel">Canal public (discussion ouverte)</option>
                            <option value="announcement">Annonces (lecture seule pour les membres)</option>
                        </select>
                    </div>

                    <div class="flex items-center pt-2">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="add_all_users" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-5 h-5">
                            <span class="ml-2 text-sm text-gray-700">Ajouter automatiquement tous les utilisateurs</span>
                        </label>
                    </div>

                    <div class="flex gap-3 mt-6 pt-4 border-t border-gray-100">
                        <button type="button" @click="showCreateModal = false" class="flex-1 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-colors">
                            Annuler
                        </button>
                        <button type="submit" class="flex-1 px-4 py-2.5 bg-blue-600 text-white font-medium rounded-xl hover:bg-blue-700 transition-colors shadow-lg shadow-blue-500/25">
                            Créer le canal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.admin>
