<x-layouts.admin>
    <div class="space-y-6" x-data="announcementManagement()">
        <!-- Breadcrumb -->
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                        Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Annonces</span>
                    </div>
                </li>
            </ol>
        </nav>
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
                            <button type="button" 
                                    @click="confirmDelete('{{ route('admin.announcements.destroy', $announcement) }}')"
                                    class="p-2 text-gray-400 hover:text-red-600 rounded-lg hover:bg-gray-100 transition-colors"
                                    title="Supprimer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
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


    <!-- Delete Confirmation Modal -->
    <div x-show="showDeleteModal" 
         class="fixed inset-0 z-[100] overflow-y-auto" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true"
         style="display: none;">
        
        <!-- Backdrop -->
        <div x-show="showDeleteModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
             @click="showDeleteModal = false"></div>

        <div class="flex min-h-screen items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div x-show="showDeleteModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">Confirmer la suppression</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">Êtes-vous sûr de vouloir supprimer cette annonce ? Cette action est irréversible.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <form :action="deleteUrl" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto">
                            Supprimer
                        </button>
                    </form>
                    <button type="button" 
                            @click="showDeleteModal = false"
                            class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>
    </div>

    @push('scripts')
    <script>
        function announcementManagement() {
            return {
                showDeleteModal: false,
                deleteUrl: '',
                confirmDelete(url) {
                    this.deleteUrl = url;
                    this.showDeleteModal = true;
                }
            }
        }

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
