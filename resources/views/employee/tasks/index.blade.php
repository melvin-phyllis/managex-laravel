<x-layouts.employee>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Mes tâches</h1>
            <p class="text-gray-500">Tâches assignées par l'administration</p>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <form action="{{ route('employee.tasks.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
                <div>
                    <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                    <select name="statut" id="statut" class="rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                        <option value="">Tous</option>
                        <option value="pending" {{ request('statut') == 'pending' ? 'selected' : '' }}>En attente</option>
                        <option value="approved" {{ request('statut') == 'approved' ? 'selected' : '' }}>En cours</option>
                        <option value="completed" {{ request('statut') == 'completed' ? 'selected' : '' }}>Terminée (à valider)</option>
                        <option value="validated" {{ request('statut') == 'validated' ? 'selected' : '' }}>Validée</option>
                        <option value="rejected" {{ request('statut') == 'rejected' ? 'selected' : '' }}>Rejetée</option>
                    </select>
                </div>
                <div>
                    <label for="priorite" class="block text-sm font-medium text-gray-700 mb-1">Priorité</label>
                    <select name="priorite" id="priorite" class="rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                        <option value="">Toutes</option>
                        <option value="high" {{ request('priorite') == 'high' ? 'selected' : '' }}>Haute</option>
                        <option value="medium" {{ request('priorite') == 'medium' ? 'selected' : '' }}>Moyenne</option>
                        <option value="low" {{ request('priorite') == 'low' ? 'selected' : '' }}>Basse</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        Filtrer
                    </button>
                </div>
            </form>
        </div>

        <!-- Tasks Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($tasks as $task)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">{{ Str::limit($task->titre, 40) }}</h3>
                            <x-status-badge :status="$task->priorite" type="priority" />
                        </div>

                        <p class="text-sm text-gray-500 mb-4 line-clamp-2">{{ $task->description ?? 'Aucune description' }}</p>

                        <x-progress-bar :value="$task->progression" class="mb-4" />

                        <div class="flex items-center justify-between text-sm text-gray-500">
                            <x-status-badge :status="$task->statut" type="task" />
                            @if($task->date_fin)
                                <span>{{ $task->date_fin->format('d/m/Y') }}</span>
                            @endif
                        </div>
                    </div>

                    @if($task->statut === 'approved')
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                            <div x-data="{ progress: {{ $task->progression }}, saving: false, saved: false }" class="space-y-2">
                                <label class="text-sm text-gray-600">Mettre à jour la progression</label>
                                <div class="flex items-center space-x-3">
                                    <input type="range" min="0" max="100" step="5" x-model="progress" class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                                    <span class="text-sm font-medium text-gray-700 w-10" x-text="progress + '%'"></span>
                                </div>
                                <template x-if="progress == 100">
                                    <p class="text-xs text-amber-600 bg-amber-50 p-2 rounded-lg">
                                        <span class="font-medium">⚠️ À 100%</span>, la tâche sera marquée comme terminée et envoyée à l'admin pour validation.
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
                                    class="w-full mt-2 px-3 py-1.5 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span x-show="!saving && !saved">Sauvegarder</span>
                                    <span x-show="saving" x-cloak>Enregistrement...</span>
                                    <span x-show="saved" x-cloak>✓ Enregistré !</span>
                                </button>
                            </div>
                        </div>
                    @elseif($task->statut === 'completed')
                        <div class="px-6 py-4 bg-amber-50 border-t border-amber-100">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-sm text-amber-700 font-medium">Terminée - En attente de validation par l'admin</span>
                            </div>
                        </div>
                    @elseif($task->statut === 'validated')
                        <div class="px-6 py-4 bg-emerald-50 border-t border-emerald-100">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-sm text-emerald-700 font-medium">✓ Tâche validée par l'admin</span>
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="col-span-full">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <p class="mt-4 text-gray-500">Aucune tâche trouvée</p>
                        <p class="mt-2 text-sm text-gray-400">Les tâches vous seront assignées par l'administration</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($tasks->hasPages())
            <div class="mt-6">
                {{ $tasks->links() }}
            </div>
        @endif
    </div>
</x-layouts.employee>
