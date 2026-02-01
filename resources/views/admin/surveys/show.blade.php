<x-layouts.admin>
    <div class="space-y-6">
        <!-- Breadcrumb -->
        <nav class="flex animate-fade-in-up" aria-label="Breadcrumb">
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
                        <a href="{{ route('admin.surveys.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">Sondages</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Détails</span>
                    </div>
                </li>
            </ol>
        </nav>
        <!-- Header -->
        <div class="flex items-center justify-between animate-fade-in-up animation-delay-100">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $survey->titre }}</h1>
                <p class="text-gray-500 mt-1 flex items-center gap-2">
                    <x-icon name="info" class="w-4 h-4" />
                    Détails du sondage
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.surveys.results', $survey) }}" class="inline-flex items-center px-4 py-2.5 bg-green-600 text-white text-sm font-medium rounded-xl hover:bg-green-700 transition-colors shadow-lg shadow-green-500/25">
                    <x-icon name="bar-chart-2" class="w-5 h-5 mr-2" />
                    Voir les résultats
                </a>
                <a href="{{ route('admin.surveys.index') }}" class="inline-flex items-center px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-200 transition-colors">
                    <x-icon name="arrow-left" class="w-5 h-5 mr-2" />
                    Retour
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fade-in-up animation-delay-200">
            <!-- Survey Info -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <x-icon name="clipboard-list" class="w-4 h-4" />
                        Informations
                    </h3>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm text-gray-500">Statut</dt>
                            <dd class="mt-1">
                                @if($survey->is_active)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>
                                        Actif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                        <span class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1.5"></span>
                                        Inactif
                                    </span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500 flex items-center gap-1.5">
                                <x-icon name="help-circle" class="w-4 h-4" />
                                Questions
                            </dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900 ml-5.5">{{ $survey->questions->count() }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500 flex items-center gap-1.5">
                                <x-icon name="users" class="w-4 h-4" />
                                Répondants
                            </dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900 ml-5.5">{{ $survey->respondents_count }}</dd>
                        </div>
                        @if($survey->date_limite)
                            <div>
                                <dt class="text-sm text-gray-500 flex items-center gap-1.5">
                                    <x-icon name="calendar" class="w-4 h-4" />
                                    Date limite
                                </dt>
                                <dd class="mt-1 text-sm font-medium ml-5.5 {{ $survey->is_expired ? 'text-red-600' : 'text-gray-900' }}">
                                    {{ $survey->date_limite->format('d/m/Y') }}
                                    @if($survey->is_expired)
                                        <span class="text-red-600 font-normal ml-1">(expiré)</span>
                                    @endif
                                </dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-sm text-gray-500 flex items-center gap-1.5">
                                <x-icon name="clock" class="w-4 h-4" />
                                Créé le
                            </dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900 ml-5.5">{{ $survey->created_at->format('d/m/Y à H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500 flex items-center gap-1.5">
                                <x-icon name="user" class="w-4 h-4" />
                                Créé par
                            </dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900 ml-5.5">{{ $survey->admin->name }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <x-icon name="settings" class="w-4 h-4" />
                        Actions
                    </h3>
                    <div class="space-y-3">
                        <form action="{{ route('admin.surveys.toggle', $survey) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2.5 {{ $survey->is_active ? 'bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200' : 'bg-green-50 text-green-700 hover:bg-green-100 border border-green-200' }} font-medium rounded-xl transition-colors flex items-center justify-center gap-2">
                                @if($survey->is_active)
                                    <x-icon name="pause-circle" class="w-5 h-5" />
                                    Désactiver le sondage
                                @else
                                    <x-icon name="play-circle" class="w-5 h-5" />
                                    Activer le sondage
                                @endif
                            </button>
                        </form>
                        <form action="{{ route('admin.surveys.destroy', $survey) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce sondage ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-4 py-2.5 bg-red-50 text-red-700 font-medium rounded-xl hover:bg-red-100 border border-red-200 transition-colors flex items-center justify-center gap-2">
                                <x-icon name="trash-2" class="w-5 h-5" />
                                Supprimer le sondage
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Questions -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <x-icon name="list" class="w-5 h-5 text-gray-500" />
                            Questions du sondage
                        </h2>
                        @if($survey->description)
                            <p class="mt-2 text-gray-600 text-sm">{{ $survey->description }}</p>
                        @endif
                    </div>

                    <div class="divide-y divide-gray-100">
                        @forelse($survey->questions as $index => $question)
                            <div class="p-6 hover:bg-gray-50 transition-colors">
                                <div class="flex items-start">
                                    <span class="flex-shrink-0 w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-bold shadow-sm">
                                        {{ $index + 1 }}
                                    </span>
                                    <div class="ml-4 flex-1">
                                        <div class="flex items-center justify-between">
                                            <h4 class="text-base font-medium text-gray-900">{{ $question->question }}</h4>
                                            @if($question->is_required)
                                                <span class="flex items-center gap-1 text-red-600 text-xs font-medium bg-red-50 px-2 py-0.5 rounded-full">
                                                    Obligatoire
                                                </span>
                                            @endif
                                        </div>
                                        <p class="mt-1 text-sm text-gray-500 flex items-center gap-1">
                                            <x-icon name="tag" class="w-3 h-3" />
                                            {{ $question->type_label }}
                                        </p>

                                        @if($question->type === 'choice' && $question->options)
                                            <div class="mt-4 space-y-2 pl-4 border-l-2 border-gray-200">
                                                @foreach($question->options as $option)
                                                    <div class="flex items-center text-sm text-gray-700 bg-white p-2 rounded border border-gray-100 shadow-sm">
                                                        <div class="w-4 h-4 border border-gray-300 rounded-full mr-3"></div>
                                                        {{ $option }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        @elseif($question->type === 'rating')
                                            <div class="mt-4 flex items-center space-x-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <div class="w-10 h-10 border border-gray-200 rounded-lg flex items-center justify-center text-sm font-medium text-gray-500 bg-gray-50">
                                                        {{ $i }}
                                                    </div>
                                                @endfor
                                            </div>
                                        @elseif($question->type === 'yesno')
                                            <div class="mt-4 flex items-center space-x-4">
                                                <div class="flex items-center px-3 py-2 border border-gray-200 rounded-lg bg-gray-50">
                                                    <div class="w-4 h-4 border border-gray-300 rounded-full mr-2"></div>
                                                    <span class="text-sm font-medium text-gray-700">Oui</span>
                                                </div>
                                                <div class="flex items-center px-3 py-2 border border-gray-200 rounded-lg bg-gray-50">
                                                    <div class="w-4 h-4 border border-gray-300 rounded-full mr-2"></div>
                                                    <span class="text-sm font-medium text-gray-700">Non</span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-12 text-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <x-icon name="help-circle" class="w-8 h-8 text-gray-400" />
                                </div>
                                <p class="text-gray-500 font-medium">Aucune question dans ce sondage</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
