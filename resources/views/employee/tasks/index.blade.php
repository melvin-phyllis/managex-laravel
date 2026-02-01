<x-layouts.employee>
    <div class="space-y-6">
        <!-- Header avec gradient -->
        <div class="relative overflow-hidden bg-gradient-to-r from-violet-600 via-purple-600 to-indigo-600 rounded-2xl p-6 text-white shadow-xl">
            <div class="absolute inset-0 bg-grid-white/10"></div>
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
            <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
            
            <div class="relative flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold mb-1">Mes Tâches</h1>
                    <p class="text-violet-100">Suivez et gérez vos tâches assignées</p>
                </div>
                <div class="hidden sm:flex items-center gap-3">
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
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
                    <div class="w-10 h-10 bg-gradient-to-br from-violet-500 to-purple-600 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalTasks }}</p>
                        <p class="text-xs text-gray-500">Total tâches</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            <div class="px-5 py-3 bg-gradient-to-r from-gray-50 to-slate-50 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filtres
                </h3>
            </div>
            <div class="p-4">
                <form action="{{ route('employee.tasks.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                        <select name="statut" id="statut" class="w-full rounded-lg border-gray-300 focus:border-violet-500 focus:ring-violet-500">
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
                        <select name="priorite" id="priorite" class="w-full rounded-lg border-gray-300 focus:border-violet-500 focus:ring-violet-500">
                            <option value="">Toutes les priorités</option>
                            <option value="high" {{ request('priorite') == 'high' ? 'selected' : '' }}>Haute</option>
                            <option value="medium" {{ request('priorite') == 'medium' ? 'selected' : '' }}>Moyenne</option>
                            <option value="low" {{ request('priorite') == 'low' ? 'selected' : '' }}>Basse</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="px-5 py-2 bg-gradient-to-r from-violet-600 to-purple-600 text-white rounded-lg hover:from-violet-700 hover:to-purple-700 transition-all shadow-sm">
                            Filtrer
                        </button>
                        @if(request('statut') || request('priorite'))
                            <a href="{{ route('employee.tasks.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                                Réinitialiser
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Grille des tâches -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            @forelse($tasks as $task)
                @php
                    $priorityColors = [
                        'high' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'border' => 'border-red-200', 'dot' => 'bg-red-500'],
                        'medium' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'dot' => 'bg-amber-500'],
                        'low' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'border' => 'border-blue-200', 'dot' => 'bg-blue-500'],
                    ];
                    $priority = $priorityColors[$task->priorite] ?? $priorityColors['medium'];
                    
                    $statusConfig = [
                        'pending' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'label' => 'En attente'],
                        'approved' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'label' => 'En cours'],
                        'completed' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'label' => 'À valider'],
                        'validated' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'icon' => 'M5 13l4 4L19 7', 'label' => 'Validée'],
                        'rejected' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'icon' => 'M6 18L18 6M6 6l12 12', 'label' => 'Rejetée'],
                    ];
                    $status = $statusConfig[$task->statut] ?? $statusConfig['pending'];
                @endphp
                
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow group">
                    <!-- En-tête de la carte -->
                    <div class="p-5">
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <h3 class="font-semibold text-gray-900 group-hover:text-violet-600 transition-colors line-clamp-2">
                                {{ $task->titre }}
                            </h3>
                            <span class="flex-shrink-0 inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium {{ $priority['bg'] }} {{ $priority['text'] }} {{ $priority['border'] }} border">
                                <span class="w-1.5 h-1.5 rounded-full {{ $priority['dot'] }}"></span>
                                {{ ucfirst($task->priorite === 'high' ? 'Haute' : ($task->priorite === 'medium' ? 'Moyenne' : 'Basse')) }}
                            </span>
                        </div>
                        
                        <p class="text-sm text-gray-500 line-clamp-2 mb-4">{{ $task->description ?? 'Aucune description disponible' }}</p>
                        
                        <!-- Barre de progression -->
                        <div class="mb-4">
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="text-gray-500">Progression</span>
                                <span class="font-semibold {{ $task->progression == 100 ? 'text-emerald-600' : 'text-violet-600' }}">{{ $task->progression }}%</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="h-2 rounded-full transition-all duration-500 {{ $task->progression == 100 ? 'bg-gradient-to-r from-emerald-500 to-green-500' : 'bg-gradient-to-r from-violet-500 to-purple-500' }}" 
                                     style="width: {{ $task->progression }}%"></div>
                            </div>
                        </div>
                        
                        <!-- Infos -->
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium {{ $status['bg'] }} {{ $status['text'] }}">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $status['icon'] }}"/>
                                </svg>
                                {{ $status['label'] }}
                            </span>
                            @if($task->date_fin)
                                <span class="flex items-center gap-1 text-xs {{ $task->date_fin->isPast() && $task->statut !== 'validated' ? 'text-red-600 font-medium' : 'text-gray-500' }}">
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
                        <div class="px-5 py-4 bg-gradient-to-r from-violet-50 to-purple-50 border-t border-violet-100">
                            <div x-data="{ progress: {{ $task->progression }}, saving: false, saved: false }" class="space-y-3">
                                <label class="text-sm font-medium text-violet-700">Mettre à jour la progression</label>
                                <div class="flex items-center gap-3">
                                    <input type="range" min="0" max="100" step="5" x-model="progress" 
                                           class="flex-1 h-2 bg-violet-200 rounded-lg appearance-none cursor-pointer accent-violet-600">
                                    <span class="text-sm font-bold text-violet-700 w-12 text-right" x-text="progress + '%'"></span>
                                </div>
                                <template x-if="progress == 100">
                                    <p class="text-xs text-amber-700 bg-amber-100 p-2 rounded-lg border border-amber-200">
                                        <span class="font-semibold">A 100%</span>, la tâche sera envoyée à l'admin pour validation.
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
                                    class="w-full px-4 py-2 bg-gradient-to-r from-violet-600 to-purple-600 text-white text-sm font-medium rounded-lg hover:from-violet-700 hover:to-purple-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-sm">
                                    <span x-show="!saving && !saved">Sauvegarder</span>
                                    <span x-show="saving" x-cloak>Enregistrement...</span>
                                    <span x-show="saved" x-cloak>✓ Enregistré !</span>
                                </button>
                            </div>
                        </div>
                    @elseif($task->statut === 'completed')
                        <div class="px-5 py-3 bg-gradient-to-r from-amber-50 to-yellow-50 border-t border-amber-200">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <span class="text-sm text-amber-700 font-medium">En attente de validation admin</span>
                            </div>
                        </div>
                    @elseif($task->statut === 'validated')
                        <div class="px-5 py-3 bg-gradient-to-r from-emerald-50 to-green-50 border-t border-emerald-200">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <span class="text-sm text-emerald-700 font-medium">Tâche validée avec succès</span>
                            </div>
                        </div>
                    @elseif($task->statut === 'rejected')
                        <div class="px-5 py-3 bg-gradient-to-r from-red-50 to-rose-50 border-t border-red-200">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </div>
                                <span class="text-sm text-red-700 font-medium">Tâche rejetée</span>
                            </div>
                        </div>
                    @elseif($task->statut === 'pending')
                        <div class="px-5 py-3 bg-gradient-to-r from-gray-50 to-slate-50 border-t border-gray-200">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <span class="text-sm text-gray-600 font-medium">En attente d'approbation</span>
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="col-span-full">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                        <div class="w-20 h-20 bg-gradient-to-br from-violet-100 to-purple-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Aucune tâche trouvée</h3>
                        <p class="text-gray-500 mb-4">Les tâches vous seront assignées par l'administration</p>
                        @if(request('statut') || request('priorite'))
                            <a href="{{ route('employee.tasks.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-violet-100 text-violet-700 rounded-lg hover:bg-violet-200 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Voir toutes les tâches
                            </a>
                        @endif
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($tasks->hasPages())
            <div class="flex justify-center">
                {{ $tasks->links() }}
            </div>
        @endif
    </div>
</x-layouts.employee>
