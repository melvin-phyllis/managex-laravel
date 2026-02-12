<x-layouts.admin>
    <div class="space-y-6">
        <!-- Breadcrumbs -->
        <nav class="flex animate-fade-in-up" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                        <a href="{{ route('admin.tasks.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">Tâches</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Détails</span>
                    </div>
                </li>
            </ol>
        </nav>
        <!-- Header -->
        <div class="flex items-center justify-between animate-fade-in-up animation-delay-100">
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fade-in-up animation-delay-200">
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
                                    <div class="flex items-center space-x-2 flex-shrink-0 ml-3">
                                        <a href="{{ route('admin.tasks.document.download', $doc) }}" class="p-2 text-gray-400 hover:text-blue-600 transition-colors" title="Télécharger">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.tasks.document.delete', $doc) }}" method="POST" onsubmit="return confirm('Supprimer ce document ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition-colors" title="Supprimer">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

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
                
                @if($task->statut === 'completed')
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Validation de la tâche</h3>
                        <form action="{{ route('admin.tasks.validate', $task) }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="rating" class="block text-sm font-medium text-gray-700">Note</label>
                                    <div class="mt-1 flex items-center">
                                        <input type="number" name="rating" id="rating" min="0" max="10" required
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                               placeholder="0-10">
                                        <span class="ml-2 text-gray-500 text-sm">/ 10</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <label for="rating_comment" class="block text-sm font-medium text-gray-700">Commentaire (Optionnel)</label>
                                <textarea name="rating_comment" id="rating_comment" rows="3"
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                          placeholder="Pourquoi cette note ?"></textarea>
                            </div>

                            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Valider et Noter
                            </button>
                        </form>

                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <form action="{{ route('admin.tasks.reject', $task) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full flex justify-center py-2 px-4 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors" onclick="return confirm('Êtes-vous sûr de vouloir rejeter cette tâche ? Cela la marquera comme rejetée.')">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    Rejeter (Non satisfaisant)
                                </button>
                            </form>
                        </div>
                    </div>
                @endif

                @if($task->statut === 'validated' && $task->rating !== null)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Évaluation</h3>
                        <div class="flex items-start space-x-4">
                            <div class="flex items-center justify-center w-16 h-16 rounded-full {{ $task->rating >= 7 ? 'bg-emerald-100 text-emerald-700' : ($task->rating >= 5 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }} text-2xl font-bold flex-shrink-0">
                                {{ $task->rating }}<span class="text-sm font-normal ml-0.5">/10</span>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">Commentaire du manager</h4>
                                <p class="mt-1 text-gray-600 text-sm">{{ $task->rating_comment ?? 'Aucun commentaire.' }}</p>
                            </div>
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
                            <img src="{{ avatar_url($task->user->avatar) }}" alt="{{ $task->user->name }}" class="w-12 h-12 rounded-full object-cover">
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
