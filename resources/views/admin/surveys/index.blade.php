<x-layouts.admin>
    <div class="space-y-6" x-data="surveyManagement()">
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
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Sondages</span>
                    </div>
                </li>
            </ol>
        </nav>
        <!-- Header -->
        <x-table-header title="Sondages Internes" subtitle="Créez et analysez les sondages de votre équipe avec des outils simples" class="animate-fade-in-up animation-delay-100">
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
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 animate-fade-in-up animation-delay-200">
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
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 animate-fade-in-up animation-delay-300">
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
                            <button type="button" 
                                    @click="confirmDelete('{{ route('admin.surveys.destroy', $survey) }}')"
                                    class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" 
                                    title="Supprimer">
                                <x-icon name="trash-2" class="w-5 h-5" />
                            </button>
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
                                <p class="text-sm text-gray-500">Êtes-vous sûr de vouloir supprimer ce sondage ? Cette action est irréversible.</p>
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

    <script>
        function surveyManagement() {
            return {
                showDeleteModal: false,
                deleteUrl: '',
                confirmDelete(url) {
                    this.deleteUrl = url;
                    this.showDeleteModal = true;
                }
            }
        }
    </script>
</x-layouts.admin>
