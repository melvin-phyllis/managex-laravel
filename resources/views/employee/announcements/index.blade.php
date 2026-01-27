<x-layouts.employee>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">📢 Annonces</h1>
                <p class="text-gray-500 mt-1">Communications de l'entreprise</p>
            </div>

            <!-- Stats Pills -->
            <div class="flex items-center gap-2">
                @if($stats['unread'] > 0)
                    <span class="px-3 py-1 text-sm font-medium bg-red-100 text-red-700 rounded-full">
                        {{ $stats['unread'] }} non lue(s)
                    </span>
                @endif
                @if($stats['pending_ack'] > 0)
                    <span class="px-3 py-1 text-sm font-medium bg-purple-100 text-purple-700 rounded-full">
                        {{ $stats['pending_ack'] }} à confirmer
                    </span>
                @endif
            </div>
        </div>

        <!-- Filters -->
        <div class="flex gap-2">
            <a href="{{ route('employee.announcements.index') }}" 
               class="px-4 py-2 rounded-lg {{ $filter === 'all' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} transition-colors">
                Toutes ({{ $stats['total'] }})
            </a>
            <a href="{{ route('employee.announcements.index', ['filter' => 'unread']) }}" 
               class="px-4 py-2 rounded-lg {{ $filter === 'unread' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} transition-colors">
                Non lues ({{ $stats['unread'] }})
            </a>
            @if($stats['pending_ack'] > 0)
            <a href="{{ route('employee.announcements.index', ['filter' => 'acknowledgment']) }}" 
               class="px-4 py-2 rounded-lg {{ $filter === 'acknowledgment' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} transition-colors">
                À confirmer ({{ $stats['pending_ack'] }})
            </a>
            @endif
        </div>

        <!-- Announcements List -->
        <div class="space-y-4">
            @forelse($announcements as $announcement)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden 
                            {{ !$announcement->is_read ? 'border-l-4 border-l-green-500' : '' }}
                            hover:shadow-md transition-shadow">
                    <div class="p-5">
                        <div class="flex items-start gap-4">
                            <!-- Icon -->
                            <div class="flex-shrink-0 w-12 h-12 rounded-xl flex items-center justify-center
                                @if($announcement->type === 'urgent') bg-red-100
                                @elseif($announcement->type === 'warning') bg-amber-100
                                @elseif($announcement->type === 'success') bg-green-100
                                @elseif($announcement->type === 'event') bg-purple-100
                                @else bg-blue-100 @endif">
                                <span class="text-2xl">{{ $announcement->type_icon }}</span>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap mb-1">
                                    @if($announcement->is_pinned)
                                        <span class="text-amber-500">📌</span>
                                    @endif
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        {{ $announcement->title }}
                                    </h3>
                                    @if($announcement->priority === 'critical')
                                        <span class="px-2 py-0.5 text-xs font-medium bg-red-100 text-red-700 rounded-full">Urgent</span>
                                    @elseif($announcement->priority === 'high')
                                        <span class="px-2 py-0.5 text-xs font-medium bg-orange-100 text-orange-700 rounded-full">Important</span>
                                    @endif
                                    @if(!$announcement->is_read)
                                        <span class="px-2 py-0.5 text-xs font-medium bg-green-100 text-green-700 rounded-full">Nouveau</span>
                                    @endif
                                </div>

                                <p class="text-gray-600 line-clamp-2">
                                    {{ Str::limit(strip_tags($announcement->content), 200) }}
                                </p>

                                <div class="flex items-center gap-4 mt-3 text-sm text-gray-500">
                                    <span>{{ $announcement->created_at->diffForHumans() }}</span>
                                    @if($announcement->is_read)
                                        <span class="text-green-600">✓ Lu</span>
                                    @endif
                                    @if($announcement->is_acknowledged)
                                        <span class="text-purple-600">✓ Accusé envoyé</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex-shrink-0">
                                <a href="{{ route('employee.announcements.show', $announcement) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                                    Lire
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </div>

                        <!-- Acknowledgment Banner -->
                        @if($announcement->requires_acknowledgment && !$announcement->is_acknowledged)
                            <div class="mt-4 p-3 bg-purple-50 rounded-lg border border-purple-200">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-purple-700">
                                        ⚠️ Cette annonce nécessite un accusé de réception
                                    </span>
                                    <button onclick="acknowledgeAnnouncement({{ $announcement->id }}, this)"
                                            class="px-4 py-1.5 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors">
                                        ✓ J'ai pris connaissance
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-3xl">📢</span>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Aucune annonce</h3>
                    <p class="text-gray-500 mt-1">
                        @if($filter === 'unread')
                            Toutes les annonces ont été lues ! 🎉
                        @elseif($filter === 'acknowledgment')
                            Aucun accusé de réception en attente.
                        @else
                            Aucune annonce pour le moment.
                        @endif
                    </p>
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
                    button.classList.remove('bg-purple-600', 'hover:bg-purple-700');
                    button.classList.add('bg-green-600');
                    
                    // Hide the banner after a delay
                    setTimeout(() => {
                        button.closest('.bg-purple-50').remove();
                    }, 1500);
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
