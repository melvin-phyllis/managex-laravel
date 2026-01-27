<x-layouts.admin>
    <div class="space-y-6">
        <!-- Header -->
        <x-table-header title="Sondages Internes" subtitle="Créez et analysez les sondages de votre équipe avec des outils simples">
            <x-slot:icon>
                <div class="w-12 h-12 bg-gradient-to-br from-violet-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg shadow-violet-500/20">
                    <x-icon name="clipboard-list" class="w-6 h-6 text-white" />
                </div>
            </x-slot:icon>
            <x-slot:actions>
                <a href="{{ route('admin.surveys.create') }}" class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-violet-600 to-purple-600 text-white font-medium rounded-xl hover:from-violet-700 hover:to-purple-700 transition-all shadow-lg shadow-violet-500/25">
                    <x-icon name="plus" class="w-5 h-5 mr-2" />
                    Nouveau sondage
                </a>
            </x-slot:actions>
        </x-table-header>

        <!-- Stats Cards (Visible only if $stats is available) -->
        @if(isset($stats))
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Sondages</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['total'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-violet-500 to-purple-600 rounded-xl flex items-center justify-center">
                        <x-icon name="clipboard-list" class="w-6 h-6 text-white" />
                    </div>
                </div>
            </div>
            <!-- More stats can be added here if available -->
        </div>
        @endif

        <!-- Survey Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($surveys as $survey)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-all duration-200 group">
                    <!-- Card Header -->
                    <div class="p-5 border-b border-gray-100">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0 pr-4">
                                <h3 class="text-lg font-semibold text-gray-900 group-hover:text-violet-600 transition-colors truncate" title="{{ $survey->titre }}">
                                    {{ $survey->titre }}
                                </h3>
                                <p class="mt-1 text-sm text-gray-500 line-clamp-2">{{ $survey->description ?? 'Aucune description disponible.' }}</p>
                            </div>
                            <div class="flex-shrink-0">
                                @if($survey->is_active)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 border border-green-200">
                                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>
                                        Actif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                        <span class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1.5"></span>
                                        Inactif
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Card Stats -->
                    <div class="px-5 py-4 bg-gray-50/50 flex flex-wrap gap-4 text-sm border-b border-gray-100">
                        <div class="flex items-center text-gray-600 font-medium">
                            <x-icon name="help-circle" class="w-4 h-4 mr-1.5 text-gray-400" />
                            {{ $survey->questions->count() }} question(s)
                        </div>
                        <div class="flex items-center text-gray-600 font-medium">
                            <x-icon name="users" class="w-4 h-4 mr-1.5 text-gray-400" />
                            {{ $survey->respondents_count }} réponse(s)
                        </div>
                        @if($survey->date_limite)
                            <div class="flex items-center font-medium {{ $survey->is_expired ? 'text-red-600' : 'text-gray-600' }}">
                                <x-icon name="calendar" class="w-4 h-4 mr-1.5 {{ $survey->is_expired ? 'text-red-400' : 'text-gray-400' }}" />
                                {{ $survey->date_limite->format('d/m/Y') }}
                            </div>
                        @endif
                    </div>

                    <!-- Card Actions -->
                    <div class="px-5 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.surveys.show', $survey) }}" class="p-2 text-gray-500 hover:text-violet-600 hover:bg-violet-50 rounded-lg transition-colors" title="Voir les détails">
                                <x-icon name="eye" class="w-5 h-5" />
                            </a>
                            <a href="{{ route('admin.surveys.results', $survey) }}" class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Voir les résultats">
                                <x-icon name="bar-chart-2" class="w-5 h-5" />
                            </a>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <form action="{{ route('admin.surveys.toggle', $survey) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="p-2 text-gray-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="{{ $survey->is_active ? 'Désactiver' : 'Activer' }}">
                                    @if($survey->is_active)
                                        <x-icon name="pause-circle" class="w-5 h-5" />
                                    @else
                                        <x-icon name="play-circle" class="w-5 h-5" />
                                    @endif
                                </button>
                            </form>
                            <form action="{{ route('admin.surveys.destroy', $survey) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce sondage ? Cette action est irréversible.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Supprimer">
                                    <x-icon name="trash-2" class="w-5 h-5" />
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <x-icon name="clipboard-x" class="w-8 h-8 text-gray-400" />
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">Aucun sondage créé</h3>
                        <p class="mt-2 text-gray-500 max-w-sm mx-auto">Commencez par créer votre premier sondage pour recueillir les avis de votre équipe.</p>
                        <a href="{{ route('admin.surveys.create') }}" class="mt-6 inline-flex items-center px-4 py-2 bg-violet-600 text-white text-sm font-medium rounded-lg hover:bg-violet-700 transition-colors">
                            <x-icon name="plus" class="w-4 h-4 mr-2" />
                            Créer un sondage
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($surveys->hasPages())
            <div class="mt-6">
                {{ $surveys->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
