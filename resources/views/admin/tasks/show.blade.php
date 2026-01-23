<x-layouts.admin>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Détails de la tâche</h1>
                <p class="text-gray-500 mt-1">{{ $task->titre }}</p>
            </div>
            <a href="{{ route('admin.tasks.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Retour
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Task Details -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ $task->titre }}</h2>

                    <div class="prose max-w-none text-gray-600">
                        {{ $task->description ?? 'Aucune description fournie.' }}
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <h3 class="text-sm font-medium text-gray-700 mb-3">Progression</h3>
                        <x-progress-bar :value="$task->progression" />
                    </div>
                </div>

                <!-- Actions -->
                @if($task->statut === 'pending')
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                        <div class="flex items-center space-x-4">
                            <form action="{{ route('admin.tasks.approve', $task) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full px-4 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Approuver la tâche
                                </button>
                            </form>
                            <form action="{{ route('admin.tasks.reject', $task) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full px-4 py-3 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Rejeter la tâche
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Employee Info -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4">Employé</h3>
                    <div class="flex items-center">
                        @if($task->user->avatar)
                            <img src="{{ Storage::url($task->user->avatar) }}" alt="{{ $task->user->name }}" class="w-12 h-12 rounded-full object-cover">
                        @else
                            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                                <span class="text-blue-600 font-medium">{{ strtoupper(substr($task->user->name, 0, 2)) }}</span>
                            </div>
                        @endif
                        <div class="ml-3">
                            <p class="font-medium text-gray-900">{{ $task->user->name }}</p>
                            <p class="text-sm text-gray-500">{{ $task->user->poste ?? 'Non défini' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Task Info -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4">Informations</h3>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm text-gray-500">Statut</dt>
                            <dd class="mt-1"><x-status-badge :status="$task->statut" type="task" /></dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Priorité</dt>
                            <dd class="mt-1"><x-status-badge :status="$task->priorite" type="priority" /></dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Date de début</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900">{{ $task->date_debut?->format('d/m/Y') ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Date de fin</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900">{{ $task->date_fin?->format('d/m/Y') ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Créée le</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900">{{ $task->created_at->format('d/m/Y à H:i') }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Delete -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4">Zone de danger</h3>
                    <form action="{{ route('admin.tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette tâche ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2 bg-red-100 text-red-700 font-medium rounded-lg hover:bg-red-200 transition-colors">
                            Supprimer la tâche
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
