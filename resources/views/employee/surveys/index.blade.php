<x-layouts.employee>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Sondages</h1>
        </div>

        <!-- Tabs -->
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8">
                <a href="{{ route('employee.surveys.index', ['filter' => 'pending']) }}" class="py-4 px-1 border-b-2 font-medium text-sm {{ request('filter', 'pending') === 'pending' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    À compléter
                </a>
                <a href="{{ route('employee.surveys.index', ['filter' => 'completed']) }}" class="py-4 px-1 border-b-2 font-medium text-sm {{ request('filter') === 'completed' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Complétés
                </a>
            </nav>
        </div>

        <!-- Surveys Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($surveys as $survey)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $survey->titre }}</h3>
                        <p class="mt-2 text-sm text-gray-500 line-clamp-2">{{ $survey->description ?? 'Aucune description' }}</p>

                        <div class="mt-4 space-y-2">
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $survey->questions->count() }} question(s)
                            </div>
                            @if($survey->date_limite)
                                <div class="flex items-center text-sm {{ $survey->is_expired ? 'text-red-500' : 'text-gray-500' }}">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Limite : {{ $survey->date_limite->format('d/m/Y') }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                        @if($survey->has_responded)
                            <span class="inline-flex items-center text-green-600 text-sm font-medium">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Déjà complété
                            </span>
                        @else
                            <a href="{{ route('employee.surveys.show', $survey) }}" class="inline-flex items-center text-green-600 hover:text-green-800 text-sm font-medium">
                                Répondre au sondage
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <p class="mt-4 text-gray-500">
                            {{ request('filter') === 'completed' ? 'Aucun sondage complété' : 'Aucun sondage à compléter' }}
                        </p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.employee>
