<x-layouts.employee>
    <div class="space-y-6">
        <!-- Header -->
        <div class="relative overflow-hidden rounded-2xl p-6 text-white shadow-xl" style="background-color: #3B8BEB;">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full" style="transform: translate(30%, -50%);"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full" style="transform: translate(-30%, 50%);"></div>

            <div class="relative flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <a href="{{ route('employee.tasks.index') }}" class="inline-flex items-center text-white/80 hover:text-white text-sm mb-2 transition-colors">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Retour aux taches
                    </a>
                    <h1 class="text-2xl font-bold">{{ $task->titre }}</h1>
                </div>
                @php
                    $statusConfig = [
                        'pending' => ['bg' => 'rgba(255,255,255,0.2)', 'label' => 'En attente'],
                        'approved' => ['bg' => 'rgba(255,255,255,0.3)', 'label' => 'En cours'],
                        'in_progress' => ['bg' => 'rgba(255,255,255,0.3)', 'label' => 'En cours'],
                        'completed' => ['bg' => 'rgba(255,255,255,0.2)', 'label' => 'A valider'],
                        'validated' => ['bg' => 'rgba(255,255,255,0.3)', 'label' => 'Validee'],
                        'rejected' => ['bg' => 'rgba(178,56,80,0.5)', 'label' => 'Rejetee'],
                    ];
                    $status = $statusConfig[$task->statut] ?? $statusConfig['pending'];
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium text-white" style="background-color: {{ $status['bg'] }};">
                    {{ $status['label'] }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Description -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Description</h2>
                    <div class="prose max-w-none text-gray-600">
                        {{ $task->description ?? 'Aucune description fournie.' }}
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <h3 class="text-sm font-medium text-gray-700 mb-3">Progression</h3>
                        <div class="flex items-center gap-3">
                            <div class="flex-1 bg-gray-100 rounded-full h-3">
                                <div class="h-3 rounded-full transition-all duration-500" style="width: {{ $task->progression }}%; background-color: #3B8BEB;"></div>
                            </div>
                            <span class="text-sm font-bold" style="color: #3B8BEB;">{{ $task->progression }}%</span>
                        </div>
                    </div>
                </div>

                <!-- Documents joints -->
                @if($task->documents->count() > 0)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                            </svg>
                            Documents joints ({{ $task->documents->count() }})
                        </h3>
                        <div class="space-y-3">
                            @foreach($task->documents as $doc)
                                @php
                                    $iconColors = [
                                        'pdf' => ['bg' => 'bg-red-100', 'text' => 'text-red-600'],
                                        'doc' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-600'],
                                        'docx' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-600'],
                                        'xls' => ['bg' => 'bg-green-100', 'text' => 'text-green-600'],
                                        'xlsx' => ['bg' => 'bg-green-100', 'text' => 'text-green-600'],
                                        'ppt' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-600'],
                                        'pptx' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-600'],
                                        'jpg' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-600'],
                                        'jpeg' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-600'],
                                        'png' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-600'],
                                    ];
                                    $ext = $doc->file_extension;
                                    $color = $iconColors[$ext] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-600'];
                                @endphp
                                <div class="flex items-center justify-between p-3 rounded-lg border border-gray-100 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center min-w-0">
                                        <div class="w-10 h-10 rounded-lg {{ $color['bg'] }} flex items-center justify-center flex-shrink-0">
                                            <span class="{{ $color['text'] }} text-xs font-bold uppercase">{{ $ext }}</span>
                                        </div>
                                        <div class="ml-3 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">{{ $doc->original_name }}</p>
                                            <p class="text-xs text-gray-500">{{ $doc->file_size_formatted }}</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('employee.tasks.document.download', $doc) }}" class="p-2 text-gray-400 hover:text-blue-600 transition-colors flex-shrink-0" title="Telecharger">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Zone d'action - Progression -->
                @if($task->statut === 'approved')
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" x-data="{ progress: {{ $task->progression }}, saving: false, saved: false }">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Mettre a jour la progression</h3>
                        <div class="space-y-4">
                            <div class="flex items-center gap-3">
                                <input type="range" min="0" max="100" step="5" x-model="progress"
                                       class="flex-1 h-2 rounded-lg appearance-none cursor-pointer" style="accent-color: #3B8BEB;">
                                <span class="text-sm font-bold w-12 text-right" style="color: #3B8BEB;" x-text="progress + '%'"></span>
                            </div>
                            <template x-if="progress == 100">
                                <p class="text-xs p-2 rounded-lg border" style="background-color: rgba(59, 139, 235, 0.1); color: #3B8BEB; border-color: rgba(59, 139, 235, 0.2);">
                                    <span class="font-semibold">A 100%</span>, la tache sera envoyee a l'admin pour validation.
                                </p>
                            </template>
                            <button
                                :disabled="saving"
                                @click="saving = true; fetch('{{ route('employee.tasks.progress', $task) }}', {
                                    method: 'POST',
                                    headers: {'Accept': 'application/json'},
                                    body: new URLSearchParams({progression: parseInt(progress), _token: '{{ csrf_token() }}'})
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
                                style="background-color: #3B8BEB;"
                                data-no-submit-guard>
                                <span x-show="!saving && !saved">Sauvegarder</span>
                                <span x-show="saving" x-cloak>Enregistrement...</span>
                                <span x-show="saved" x-cloak>Enregistre !</span>
                            </button>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Informations -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4">Informations</h3>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm text-gray-500">Priorite</dt>
                            <dd class="mt-1">
                                @php
                                    $priorityLabels = ['high' => 'Haute', 'medium' => 'Moyenne', 'low' => 'Basse'];
                                    $priorityColors = [
                                        'high' => 'background-color: rgba(178, 56, 80, 0.1); color: #B23850;',
                                        'medium' => 'background-color: #E7E3D4; color: #8590AA;',
                                        'low' => 'background-color: rgba(59, 139, 235, 0.1); color: #3B8BEB;',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" style="{{ $priorityColors[$task->priorite] ?? '' }}">
                                    {{ $priorityLabels[$task->priorite] ?? $task->priorite }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Date de debut</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900">{{ $task->date_debut?->format('d/m/Y') ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Date de fin</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900">
                                @if($task->date_fin)
                                    <span class="{{ $task->date_fin->isPast() && !in_array($task->statut, ['validated', 'completed']) ? 'text-red-600 font-semibold' : '' }}">
                                        {{ $task->date_fin->format('d/m/Y') }}
                                        @if($task->date_fin->isPast() && !in_array($task->statut, ['validated', 'completed']))
                                            (en retard)
                                        @endif
                                    </span>
                                @else
                                    -
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Creee le</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900">{{ $task->created_at->format('d/m/Y a H:i') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</x-layouts.employee>
