<x-layouts.employee>
    <div class="space-y-6" x-data="{ viewMode: localStorage.getItem('employeeTaskView') || 'cards' }" x-init="$watch('viewMode', v => localStorage.setItem('employeeTaskView', v))">
        <!-- Header avec Tolia Blue -->
        <div class="relative overflow-hidden rounded-2xl p-6 text-white shadow-xl" style="background-color: #3B8BEB;">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full" style="transform: translate(30%, -50%);"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full" style="transform: translate(-30%, 50%);"></div>
            
            <div class="relative flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold mb-1">Mes Taches</h1>
                    <p style="color: #C4DBF6;">Suivez et gérez vos taches assignées</p>
                </div>
                <div class="flex items-center gap-3">
                    <!-- Toggle View -->
                    <div class="bg-white/20 backdrop-blur-sm rounded-xl p-1 flex">
                        <button @click="viewMode = 'cards'" 
                                :class="viewMode === 'cards' ? 'bg-white text-[#3B8BEB]' : 'text-white hover:bg-white/20'"
                                class="px-3 py-2 rounded-lg font-medium text-sm transition-all flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                            Cartes
                        </button>
                        <button @click="viewMode = 'calendar'; $nextTick(() => initEmployeeCalendar())" 
                                :class="viewMode === 'calendar' ? 'bg-white text-[#3B8BEB]' : 'text-white hover:bg-white/20'"
                                class="px-3 py-2 rounded-lg font-medium text-sm transition-all flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Calendrier
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        @php
            $totalTasks = $tasks->total();
            $pendingTasks = $tasks->where('statut', 'pending')->count() + $tasks->where('statut', 'approved')->count();
            $completedTasks = $tasks->where('statut', 'validated')->count();
            $inProgressTasks = $tasks->where('statut', 'approved')->count();
        @endphp
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: #3B8BEB;">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalTasks }}</p>
                        <p class="text-xs text-gray-500">Total taches</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: rgba(59, 139, 235, 0.15);">
                        <svg class="w-5 h-5" style="color: #3B8BEB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $inProgressTasks }}</p>
                        <p class="text-xs text-gray-500">En cours</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: #E7E3D4;">
                        <svg class="w-5 h-5" style="color: #8590AA;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $pendingTasks }}</p>
                        <p class="text-xs text-gray-500">En attente</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: rgba(59, 139, 235, 0.2);">
                        <svg class="w-5 h-5" style="color: #3B8BEB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $completedTasks }}</p>
                        <p class="text-xs text-gray-500">Validées</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100" style="background-color: rgba(231, 227, 212, 0.3);">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <svg class="w-4 h-4" style="color: #3B8BEB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filtres
                </h3>
            </div>
            <div class="p-4">
                <form action="{{ route('employee.tasks.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                        <select name="statut" id="statut" class="w-full rounded-xl border-gray-300 focus:border-[#3B8BEB] focus:ring-[#3B8BEB]">
                            <option value="">Tous les statuts</option>
                            <option value="pending" {{ request('statut') == 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="approved" {{ request('statut') == 'approved' ? 'selected' : '' }}>En cours</option>
                            <option value="completed" {{ request('statut') == 'completed' ? 'selected' : '' }}>Terminée (à valider)</option>
                            <option value="validated" {{ request('statut') == 'validated' ? 'selected' : '' }}>Validée</option>
                            <option value="rejected" {{ request('statut') == 'rejected' ? 'selected' : '' }}>Rejetée</option>
                        </select>
                    </div>
                    <div class="flex-1">
                        <label for="priorite" class="block text-sm font-medium text-gray-700 mb-1">Priorité</label>
                        <select name="priorite" id="priorite" class="w-full rounded-xl border-gray-300 focus:border-[#3B8BEB] focus:ring-[#3B8BEB]">
                            <option value="">Toutes les priorités</option>
                            <option value="high" {{ request('priorite') == 'high' ? 'selected' : '' }}>Haute</option>
                            <option value="medium" {{ request('priorite') == 'medium' ? 'selected' : '' }}>Moyenne</option>
                            <option value="low" {{ request('priorite') == 'low' ? 'selected' : '' }}>Basse</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="px-5 py-2 text-white rounded-xl transition-all shadow-sm" style="background-color: #3B8BEB;">
                            Filtrer
                        </button>
                        @if(request('statut') || request('priorite'))
                            <a href="{{ route('employee.tasks.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors">
                                Réinitialiser
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Calendar View -->
        <div x-show="viewMode === 'calendar'" x-cloak class="animate-fade-in">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
                <div id="employeeTasksCalendar" class="min-h-[500px]"></div>
            </div>
        </div>

        <!-- Grille des taches (Cards View) -->
        <div x-show="viewMode === 'cards'" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            @forelse($tasks as $task)
                @php
                    $priorityColors = [
                        'high' => ['bg' => 'rgba(178, 56, 80, 0.1)', 'text' => '#B23850', 'border' => 'rgba(178, 56, 80, 0.2)', 'dot' => '#B23850'],
                        'medium' => ['bg' => '#E7E3D4', 'text' => '#8590AA', 'border' => 'rgba(133, 144, 170, 0.3)', 'dot' => '#8590AA'],
                        'low' => ['bg' => 'rgba(59, 139, 235, 0.1)', 'text' => '#3B8BEB', 'border' => 'rgba(59, 139, 235, 0.2)', 'dot' => '#3B8BEB'],
                    ];
                    $priority = $priorityColors[$task->priorite] ?? $priorityColors['medium'];
                    
                    $statusConfig = [
                        'pending' => ['bg' => '#E7E3D4', 'text' => '#8590AA', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'label' => 'En attente'],
                        'approved' => ['bg' => 'rgba(59, 139, 235, 0.15)', 'text' => '#3B8BEB', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'label' => 'En cours'],
                        'completed' => ['bg' => 'rgba(133, 144, 170, 0.15)', 'text' => '#8590AA', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'label' => 'A valider'],
                        'validated' => ['bg' => 'rgba(59, 139, 235, 0.2)', 'text' => '#3B8BEB', 'icon' => 'M5 13l4 4L19 7', 'label' => 'Validée'],
                        'rejected' => ['bg' => 'rgba(178, 56, 80, 0.15)', 'text' => '#B23850', 'icon' => 'M6 18L18 6M6 6l12 12', 'label' => 'Rejetée'],
                    ];
                    $status = $statusConfig[$task->statut] ?? $statusConfig['pending'];
                @endphp
                
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow group">
                    <!-- En-tête de la carte -->
                    <div class="p-5">
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <h3 class="font-semibold text-gray-900 group-hover:text-[#3B8BEB] transition-colors line-clamp-2">
                                {{ $task->titre }}
                            </h3>
                            <span class="flex-shrink-0 inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium border" 
                                  style="background-color: {{ $priority['bg'] }}; color: {{ $priority['text'] }}; border-color: {{ $priority['border'] }};">
                                <span class="w-1.5 h-1.5 rounded-full" style="background-color: {{ $priority['dot'] }};"></span>
                                {{ ucfirst($task->priorite === 'high' ? 'Haute' : ($task->priorite === 'medium' ? 'Moyenne' : 'Basse')) }}
                            </span>
                        </div>
                        
                        <p class="text-sm text-gray-500 line-clamp-2 mb-4">{{ $task->description ?? 'Aucune description disponible' }}</p>
                        
                        <!-- Barre de progression -->
                        <div class="mb-4">
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="text-gray-500">Progression</span>
                                <span class="font-semibold" style="color: #3B8BEB;">{{ $task->progression }}%</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="h-2 rounded-full transition-all duration-500" 
                                     style="width: {{ $task->progression }}%; background-color: #3B8BEB;"></div>
                            </div>
                        </div>
                        
                        <!-- Infos -->
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium" 
                                  style="background-color: {{ $status['bg'] }}; color: {{ $status['text'] }};">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $status['icon'] }}"/>
                                </svg>
                                {{ $status['label'] }}
                            </span>
                            @if($task->date_fin)
                                <span class="flex items-center gap-1 text-xs" style="color: {{ $task->date_fin->isPast() && $task->statut !== 'validated' ? '#B23850' : '#8590AA' }}; {{ $task->date_fin->isPast() && $task->statut !== 'validated' ? 'font-weight: 500;' : '' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $task->date_fin->format('d/m/Y') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Zone d'action selon statut -->
                    @if($task->statut === 'approved')
                        <div class="px-5 py-4 border-t border-gray-100" style="background-color: rgba(231, 227, 212, 0.3);">
                            <div x-data="{ progress: {{ $task->progression }}, saving: false, saved: false }" class="space-y-3">
                                <label class="text-sm font-medium" style="color: #3B8BEB;">Mettre à jour la progression</label>
                                <div class="flex items-center gap-3">
                                    <input type="range" min="0" max="100" step="5" x-model="progress" 
                                           class="flex-1 h-2 rounded-lg appearance-none cursor-pointer" style="accent-color: #3B8BEB;">
                                    <span class="text-sm font-bold w-12 text-right" style="color: #3B8BEB;" x-text="progress + '%'"></span>
                                </div>
                                <template x-if="progress == 100">
                                    <p class="text-xs p-2 rounded-lg border" style="background-color: rgba(59, 139, 235, 0.1); color: #3B8BEB; border-color: rgba(59, 139, 235, 0.2);">
                                        <span class="font-semibold">A 100%</span>, la tache sera envoyée à l'admin pour validation.
                                    </p>
                                </template>
                                <button
                                    :disabled="saving"
                                    @click="saving = true; fetch('{{ route('employee.tasks.progress', $task) }}', {
                                        method: 'PATCH',
                                        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                                        body: JSON.stringify({progression: parseInt(progress)})
                                    }).then(r => r.json()).then(data => {
                                        saving = false;
                                        if(data.success) {
                                            if(data.statut === 'completed') {
                                                location.reload();
                                            } else {
                                                saved = true;
                                                setTimeout(() => saved = false, 2000);
                                            }
                                        }
                                    }).catch(() => { saving = false; alert('Erreur lors de la sauvegarde'); })"
                                    class="w-full px-4 py-2 text-white text-sm font-medium rounded-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-sm"
                                    style="background-color: #3B8BEB;">
                                    <span x-show="!saving && !saved">Sauvegarder</span>
                                    <span x-show="saving" x-cloak>Enregistrement...</span>
                                    <span x-show="saved" x-cloak>✓ Enregistré !</span>
                                </button>
                            </div>
                        </div>
                    @elseif($task->statut === 'completed')
                        <div class="px-5 py-3 border-t border-gray-100" style="background-color: rgba(231, 227, 212, 0.3);">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(133, 144, 170, 0.15);">
                                    <svg class="w-4 h-4" style="color: #8590AA;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-medium" style="color: #8590AA;">En attente de validation admin</span>
                            </div>
                        </div>
                    @elseif($task->statut === 'validated')
                        <div class="px-5 py-3 border-t border-gray-100" style="background-color: rgba(59, 139, 235, 0.1);">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(59, 139, 235, 0.2);">
                                    <svg class="w-4 h-4" style="color: #3B8BEB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-medium" style="color: #3B8BEB;">Tache validée avec succès</span>
                            </div>
                        </div>
                    @elseif($task->statut === 'rejected')
                        <div class="px-5 py-3 border-t" style="border-color: rgba(178, 56, 80, 0.2); background-color: rgba(178, 56, 80, 0.1);">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(178, 56, 80, 0.2);">
                                    <svg class="w-4 h-4" style="color: #B23850;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-medium" style="color: #B23850;">Tache rejetée</span>
                            </div>
                        </div>
                    @elseif($task->statut === 'pending')
                        <div class="px-5 py-3 border-t border-gray-200" style="background-color: rgba(231, 227, 212, 0.3);">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: #E7E3D4;">
                                    <svg class="w-4 h-4" style="color: #8590AA;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-medium" style="color: #8590AA;">En attente d'approbation</span>
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="col-span-full">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                        <div class="w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background-color: rgba(59, 139, 235, 0.1);">
                            <svg class="w-10 h-10" style="color: #3B8BEB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Aucune tache trouvée</h3>
                        <p class="text-gray-500 mb-4">Les taches vous seront assignées par l'administration</p>
                        @if(request('statut') || request('priorite'))
                            <a href="{{ route('employee.tasks.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl transition-colors" style="background-color: rgba(59, 139, 235, 0.1); color: #3B8BEB;">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Voir toutes les taches
                            </a>
                        @endif
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination (cards view only) -->
        <div x-show="viewMode === 'cards'">
            @if($tasks->hasPages())
                <div class="flex justify-center">
                    {{ $tasks->links() }}
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css' rel='stylesheet' />
    <script nonce="{{ $cspNonce ?? '' }}" src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
    <script nonce="{{ $cspNonce ?? '' }}">
        let employeeCalendarInstance = null;
        
        function initEmployeeCalendar() {
            if (employeeCalendarInstance) {
                employeeCalendarInstance.render();
                return;
            }
            
            const calendarEl = document.getElementById('employeeTasksCalendar');
            if (!calendarEl) return;
            
            const tasks = @json($tasks->items());
            const events = tasks.map(task => {
                const statusColors = {
                    pending: { bg: '#8590AA', border: '#6B7280' },
                    approved: { bg: '#3B8BEB', border: '#2563EB' },
                    in_progress: { bg: '#3B8BEB', border: '#2563EB' },
                    completed: { bg: '#8590AA', border: '#6B7280' },
                    validated: { bg: '#3B8BEB', border: '#1D4ED8' },
                    rejected: { bg: '#B23850', border: '#9F1239' }
                };
                const colors = statusColors[task.statut] || { bg: '#8590AA', border: '#6B7280' };
                
                return {
                    id: task.id,
                    title: task.titre,
                    start: task.date_debut || task.created_at,
                    end: task.date_fin,
                    backgroundColor: colors.bg,
                    borderColor: colors.border,
                    extendedProps: {
                        status: task.statut,
                        priority: task.priorite,
                        progression: task.progression
                    }
                };
            });
            
            employeeCalendarInstance = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'fr',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,listWeek'
                },
                buttonText: {
                    today: "Aujourd'hui",
                    month: 'Mois',
                    list: 'Liste'
                },
                events: events,
                eventClick: function(info) {
                    window.location.href = '/employee/tasks/' + info.event.id;
                },
                eventDidMount: function(info) {
                    const props = info.event.extendedProps;
                    let tooltip = info.event.title;
                    if (props.progression !== undefined) tooltip += '\n📊 ' + props.progression + '%';
                    info.el.title = tooltip;
                },
                height: 'auto',
                dayMaxEvents: 3,
                moreLinkText: function(n) { return '+' + n + ' autres'; },
                noEventsText: 'Aucune tache'
            });
            
            employeeCalendarInstance.render();
        }
        
        // Initialize if calendar view is default
        document.addEventListener('DOMContentLoaded', function() {
            if (localStorage.getItem('employeeTaskView') === 'calendar') {
                setTimeout(initEmployeeCalendar, 100);
            }
        });
    </script>
    @endpush
</x-layouts.employee>
