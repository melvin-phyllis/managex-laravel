<x-layouts.admin>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">📢 Annonces</h1>
                <p class="text-gray-500 mt-1">Gérez les communications internes</p>
            </div>
            <a href="{{ route('admin.announcements.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nouvelle Annonce
            </a>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <span class="text-xl">📢</span>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                        <p class="text-sm text-gray-500">Total annonces</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <span class="text-xl">✅</span>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['active'] }}</p>
                        <p class="text-sm text-gray-500">Actives</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                        <span class="text-xl">🚨</span>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['urgent'] }}</p>
                        <p class="text-sm text-gray-500">Urgentes</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                        <span class="text-xl">📌</span>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['pinned'] }}</p>
                        <p class="text-sm text-gray-500">Épinglées</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <form method="GET" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[200px]">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Rechercher une annonce..."
                           class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                </div>
                <select name="status" class="rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                    <option value="">Tous les statuts</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actives</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactives</option>
                </select>
                <select name="type" class="rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                    <option value="">Tous les types</option>
                    <option value="info" {{ request('type') === 'info' ? 'selected' : '' }}>ℹ️ Info</option>
                    <option value="success" {{ request('type') === 'success' ? 'selected' : '' }}>✅ Succès</option>
                    <option value="warning" {{ request('type') === 'warning' ? 'selected' : '' }}>⚠️ Attention</option>
                    <option value="urgent" {{ request('type') === 'urgent' ? 'selected' : '' }}>🚨 Urgent</option>
                    <option value="event" {{ request('type') === 'event' ? 'selected' : '' }}>📅 Événement</option>
                </select>
                <select name="priority" class="rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                    <option value="">Toutes priorités</option>
                    <option value="normal" {{ request('priority') === 'normal' ? 'selected' : '' }}>Normale</option>
                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>Haute</option>
                    <option value="critical" {{ request('priority') === 'critical' ? 'selected' : '' }}>Critique</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    Filtrer
                </button>
                @if(request()->hasAny(['search', 'status', 'type', 'priority']))
                    <a href="{{ route('admin.announcements.index') }}" class="px-4 py-2 text-gray-500 hover:text-gray-700">
                        Réinitialiser
                    </a>
                @endif
            </form>
        </div>

        <!-- Announcements List -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            @forelse($announcements as $announcement)
                <div class="p-4 border-b border-gray-100 hover:bg-gray-50 transition-colors {{ !$announcement->is_active ? 'opacity-60' : '' }}">
                    <div class="flex items-start gap-4">
                        <!-- Icon -->
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center
                            @if($announcement->type === 'urgent') bg-red-100
                            @elseif($announcement->type === 'warning') bg-amber-100
                            @elseif($announcement->type === 'success') bg-green-100
                            @elseif($announcement->type === 'event') bg-purple-100
                            @else bg-blue-100 @endif">
                            <span class="text-lg">{{ $announcement->type_icon }}</span>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                @if($announcement->is_pinned)
                                    <span class="text-amber-500">📌</span>
                                @endif
                                <a href="{{ route('admin.announcements.show', $announcement) }}" 
                                   class="font-semibold text-gray-900 hover:text-green-600 truncate">
                                    {{ $announcement->title }}
                                </a>
                                @if($announcement->priority === 'critical')
                                    <span class="px-2 py-0.5 text-xs font-medium bg-red-100 text-red-700 rounded-full">Critique</span>
                                @elseif($announcement->priority === 'high')
                                    <span class="px-2 py-0.5 text-xs font-medium bg-orange-100 text-orange-700 rounded-full">Haute</span>
                                @endif
                                @if($announcement->requires_acknowledgment)
                                    <span class="px-2 py-0.5 text-xs font-medium bg-purple-100 text-purple-700 rounded-full">Accusé requis</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-4 mt-1 text-sm text-gray-500">
                                <span>{{ $announcement->target_label }}</span>
                                <span>•</span>
                                <span>Par {{ $announcement->creator?->name ?? 'Admin' }}</span>
                                <span>•</span>
                                <span>{{ $announcement->created_at->diffForHumans() }}</span>
                            </div>
                            <!-- Read Progress -->
                            <div class="mt-2 flex items-center gap-2">
                                <div class="flex-1 max-w-xs h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-green-500 rounded-full" 
                                         style="width: {{ $announcement->read_percentage }}%"></div>
                                </div>
                                <span class="text-xs text-gray-500">{{ $announcement->read_percentage }}% lu ({{ $announcement->reads_count }}/{{ $announcement->target_users_count }})</span>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <!-- Toggle Active -->
                            <button onclick="toggleAnnouncement({{ $announcement->id }})" 
                                    class="p-2 rounded-lg hover:bg-gray-100 transition-colors"
                                    title="{{ $announcement->is_active ? 'Désactiver' : 'Activer' }}">
                                @if($announcement->is_active)
                                    <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                @endif
                            </button>
                            <!-- Toggle Pin -->
                            <button onclick="togglePin({{ $announcement->id }})" 
                                    class="p-2 rounded-lg hover:bg-gray-100 transition-colors"
                                    title="{{ $announcement->is_pinned ? 'Désépingler' : 'Épingler' }}">
                                <svg class="w-5 h-5 {{ $announcement->is_pinned ? 'text-amber-500' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M5 5a2 2 0 012-2h6a2 2 0 012 2v2a2 2 0 01-2 2H7a2 2 0 01-2-2V5zM4 9v6a2 2 0 002 2h8a2 2 0 002-2V9a1 1 0 00-1-1H5a1 1 0 00-1 1z"/>
                                </svg>
                            </button>
                            <a href="{{ route('admin.announcements.edit', $announcement) }}" 
                               class="p-2 text-gray-400 hover:text-blue-600 rounded-lg hover:bg-gray-100 transition-colors"
                               title="Modifier">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <form action="{{ route('admin.announcements.destroy', $announcement) }}" method="POST" 
                                  onsubmit="return confirm('Supprimer cette annonce ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="p-2 text-gray-400 hover:text-red-600 rounded-lg hover:bg-gray-100 transition-colors"
                                        title="Supprimer">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-3xl">📢</span>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Aucune annonce</h3>
                    <p class="text-gray-500 mt-1">Créez votre première annonce pour communiquer avec vos employés.</p>
                    <a href="{{ route('admin.announcements.create') }}" 
                       class="inline-flex items-center px-4 py-2 mt-4 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        Créer une annonce
                    </a>
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
        function toggleAnnouncement(id) {
            fetch(`/admin/announcements/${id}/toggle`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }

        function togglePin(id) {
            fetch(`/admin/announcements/${id}/pin`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }
    </script>
    @endpush
</x-layouts.admin>
