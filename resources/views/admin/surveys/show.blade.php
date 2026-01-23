<x-layouts.admin>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $survey->titre }}</h1>
                <p class="text-gray-500 mt-1">Détails du sondage</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.surveys.results', $survey) }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Voir les résultats
                </a>
                <a href="{{ route('admin.surveys.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Retour
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Survey Info -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4">Informations</h3>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm text-gray-500">Statut</dt>
                            <dd class="mt-1">
                                @if($survey->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Actif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Inactif
                                    </span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Questions</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900">{{ $survey->questions->count() }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Répondants</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900">{{ $survey->respondents_count }}</dd>
                        </div>
                        @if($survey->date_limite)
                            <div>
                                <dt class="text-sm text-gray-500">Date limite</dt>
                                <dd class="mt-1 text-sm font-medium {{ $survey->is_expired ? 'text-red-600' : 'text-gray-900' }}">
                                    {{ $survey->date_limite->format('d/m/Y') }}
                                    @if($survey->is_expired)
                                        <span class="text-red-600">(expiré)</span>
                                    @endif
                                </dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-sm text-gray-500">Créé le</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900">{{ $survey->created_at->format('d/m/Y à H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Créé par</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900">{{ $survey->admin->name }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4">Actions</h3>
                    <div class="space-y-3">
                        <form action="{{ route('admin.surveys.toggle', $survey) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 {{ $survey->is_active ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }} font-medium rounded-lg transition-colors">
                                {{ $survey->is_active ? 'Désactiver le sondage' : 'Activer le sondage' }}
                            </button>
                        </form>
                        <form action="{{ route('admin.surveys.destroy', $survey) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce sondage ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-4 py-2 bg-red-100 text-red-700 font-medium rounded-lg hover:bg-red-200 transition-colors">
                                Supprimer le sondage
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Questions -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-6 border-b border-gray-100">
                        <h2 class="text-lg font-semibold text-gray-900">Questions</h2>
                        @if($survey->description)
                            <p class="mt-2 text-gray-600">{{ $survey->description }}</p>
                        @endif
                    </div>

                    <div class="divide-y divide-gray-100">
                        @forelse($survey->questions as $index => $question)
                            <div class="p-6">
                                <div class="flex items-start">
                                    <span class="flex-shrink-0 w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-medium">
                                        {{ $index + 1 }}
                                    </span>
                                    <div class="ml-4 flex-1">
                                        <div class="flex items-center justify-between">
                                            <h4 class="text-base font-medium text-gray-900">{{ $question->question }}</h4>
                                            @if($question->is_required)
                                                <span class="text-red-500 text-sm">*</span>
                                            @endif
                                        </div>
                                        <p class="mt-1 text-sm text-gray-500">{{ $question->type_label }}</p>

                                        @if($question->type === 'choice' && $question->options)
                                            <div class="mt-3 space-y-2">
                                                @foreach($question->options as $option)
                                                    <div class="flex items-center text-sm text-gray-600">
                                                        <span class="w-4 h-4 border border-gray-300 rounded-full mr-2"></span>
                                                        {{ $option }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        @elseif($question->type === 'rating')
                                            <div class="mt-3 flex items-center space-x-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <span class="w-8 h-8 border border-gray-300 rounded flex items-center justify-center text-sm text-gray-400">{{ $i }}</span>
                                                @endfor
                                            </div>
                                        @elseif($question->type === 'yesno')
                                            <div class="mt-3 flex items-center space-x-4">
                                                <span class="flex items-center text-sm text-gray-600">
                                                    <span class="w-4 h-4 border border-gray-300 rounded-full mr-2"></span>
                                                    Oui
                                                </span>
                                                <span class="flex items-center text-sm text-gray-600">
                                                    <span class="w-4 h-4 border border-gray-300 rounded-full mr-2"></span>
                                                    Non
                                                </span>
                                            </div>
                                        @else
                                            <div class="mt-3">
                                                <div class="w-full h-10 border border-gray-200 rounded bg-gray-50"></div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-12 text-center text-gray-500">
                                <p>Aucune question dans ce sondage</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
