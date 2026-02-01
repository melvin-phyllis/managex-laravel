<x-layouts.employee>
    <div class="space-y-6">
        <!-- Header avec gradient -->
        <div class="bg-gradient-to-r from-indigo-600 via-blue-600 to-cyan-600 rounded-2xl p-6 text-white shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold mb-1">Annonces</h1>
                    <p class="text-indigo-100">Communications et actualités de l'entreprise</p>
                </div>
                <div class="hidden sm:flex w-14 h-14 bg-white/20 rounded-xl items-center justify-center">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                        <p class="text-xs text-gray-500">Total annonces</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-rose-500 to-red-600 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['unread'] }}</p>
                        <p class="text-xs text-gray-500">Non lues</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['pending_ack'] }}</p>
                        <p class="text-xs text-gray-500">À confirmer</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] - $stats['unread'] }}</p>
                        <p class="text-xs text-gray-500">Lues</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('employee.announcements.index') }}" 
               class="px-4 py-2 rounded-lg font-medium transition-all {{ $filter === 'all' ? 'bg-gradient-to-r from-indigo-600 to-blue-600 text-white shadow-md' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">
                Toutes ({{ $stats['total'] }})
            </a>
            <a href="{{ route('employee.announcements.index', ['filter' => 'unread']) }}" 
               class="px-4 py-2 rounded-lg font-medium transition-all {{ $filter === 'unread' ? 'bg-gradient-to-r from-indigo-600 to-blue-600 text-white shadow-md' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">
                Non lues ({{ $stats['unread'] }})
            </a>
            @if($stats['pending_ack'] > 0)
            <a href="{{ route('employee.announcements.index', ['filter' => 'acknowledgment']) }}" 
               class="px-4 py-2 rounded-lg font-medium transition-all {{ $filter === 'acknowledgment' ? 'bg-gradient-to-r from-amber-500 to-orange-500 text-white shadow-md' : 'bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200' }}">
                À confirmer ({{ $stats['pending_ack'] }})
            </a>
            @endif
        </div>

        <!-- Liste des annonces -->
        <div class="space-y-4">
            @forelse($announcements as $announcement)
                @php
                    $typeColors = [
                        'urgent' => 'from-red-500 to-rose-600',
                        'warning' => 'from-amber-500 to-orange-600',
                        'success' => 'from-emerald-500 to-green-600',
                        'event' => 'from-purple-500 to-violet-600',
                        'info' => 'from-blue-500 to-indigo-600',
                    ];
                    $typeColor = $typeColors[$announcement->type] ?? $typeColors['info'];
                @endphp
                
                <div class="bg-white rounded-xl shadow-sm border {{ !$announcement->is_read ? 'border-indigo-300 bg-indigo-50/30' : 'border-gray-100' }} overflow-hidden hover:shadow-md transition-all">
                    <div class="p-5">
                        <div class="flex flex-col sm:flex-row sm:items-start gap-4">
                            <!-- Icône -->
                            <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br {{ $typeColor }} rounded-xl flex items-center justify-center text-white shadow-sm">
                                @if($announcement->type === 'urgent')
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                @elseif($announcement->type === 'warning')
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @elseif($announcement->type === 'success')
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @elseif($announcement->type === 'event')
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                @else
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                                    </svg>
                                @endif
                            </div>

                            <!-- Contenu -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap mb-2">
                                    @if($announcement->is_pinned)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-amber-100 text-amber-700 rounded-full text-xs font-medium">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.285 1.05A3.989 3.989 0 0115 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.715-5.349L11 6.477V16h2a1 1 0 110 2H7a1 1 0 110-2h2V6.477L6.237 7.582l1.715 5.349a1 1 0 01-.285 1.05A3.989 3.989 0 015 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.738-5.42-1.233-.617a1 1 0 01.894-1.788l1.599.799L9 4.323V3a1 1 0 011-1z"/>
                                            </svg>
                                            Épinglée
                                        </span>
                                    @endif
                                    @if($announcement->priority === 'critical')
                                        <span class="px-2 py-0.5 text-xs font-semibold bg-red-100 text-red-700 rounded-full">Urgent</span>
                                    @elseif($announcement->priority === 'high')
                                        <span class="px-2 py-0.5 text-xs font-semibold bg-orange-100 text-orange-700 rounded-full">Important</span>
                                    @endif
                                    @if(!$announcement->is_read)
                                        <span class="px-2 py-0.5 text-xs font-semibold bg-indigo-100 text-indigo-700 rounded-full">Nouveau</span>
                                    @endif
                                </div>

                                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                    {{ $announcement->title }}
                                </h3>

                                <p class="text-gray-600 text-sm line-clamp-2 mb-3">
                                    {{ Str::limit(strip_tags($announcement->content), 150) }}
                                </p>

                                <div class="flex items-center gap-4 text-sm">
                                    <span class="flex items-center gap-1 text-gray-500">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $announcement->created_at->diffForHumans() }}
                                    </span>
                                    @if($announcement->is_read)
                                        <span class="flex items-center gap-1 text-emerald-600 font-medium">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Lu
                                        </span>
                                    @endif
                                    @if($announcement->is_acknowledged)
                                        <span class="flex items-center gap-1 text-purple-600 font-medium">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                            </svg>
                                            Confirmé
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Action -->
                            <div class="flex-shrink-0 self-start">
                                <a href="{{ route('employee.announcements.show', $announcement) }}" 
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-indigo-600 to-blue-600 text-white rounded-lg hover:from-indigo-700 hover:to-blue-700 transition-all shadow-sm font-medium text-sm">
                                    Lire
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </div>

                        <!-- Bannière d'accusé de réception -->
                        @if($announcement->requires_acknowledgment && !$announcement->is_acknowledged)
                            <div class="mt-4 p-4 bg-amber-50 rounded-xl border border-amber-200">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                            </svg>
                                        </div>
                                        <span class="text-sm font-medium text-amber-800">
                                            Cette annonce nécessite un accusé de réception
                                        </span>
                                    </div>
                                    <button onclick="acknowledgeAnnouncement({{ $announcement->id }}, this)"
                                            class="px-4 py-2 bg-gradient-to-r from-amber-500 to-orange-500 text-white text-sm font-semibold rounded-lg hover:from-amber-600 hover:to-orange-600 transition-all shadow-sm whitespace-nowrap">
                                        J'ai pris connaissance
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-indigo-100 to-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Aucune annonce</h3>
                    <p class="text-gray-500">
                        @if($filter === 'unread')
                            Toutes les annonces ont été lues !
                        @elseif($filter === 'acknowledgment')
                            Aucun accusé de réception en attente.
                        @else
                            Aucune annonce pour le moment.
                        @endif
                    </p>
                    @if($filter !== 'all')
                        <a href="{{ route('employee.announcements.index') }}" 
                           class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 transition-colors font-medium">
                            Voir toutes les annonces
                        </a>
                    @endif
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($announcements->hasPages())
            <div class="flex justify-center">
                {{ $announcements->links() }}
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        function acknowledgeAnnouncement(id, button) {
            button.disabled = true;
            button.textContent = 'Envoi...';

            fetch(`/employee/announcements/${id}/acknowledge`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    button.textContent = '✓ Confirmé';
                    button.classList.remove('from-amber-500', 'to-orange-500');
                    button.classList.add('from-emerald-500', 'to-green-500');
                    
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    button.textContent = 'Erreur';
                    button.disabled = false;
                }
            })
            .catch(() => {
                button.textContent = 'Erreur';
                button.disabled = false;
            });
        }
    </script>
    @endpush
</x-layouts.employee>
