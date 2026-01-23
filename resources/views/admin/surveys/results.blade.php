<x-layouts.admin>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Résultats du sondage</h1>
                <p class="text-gray-500 mt-1">{{ $survey->titre }}</p>
            </div>
            <a href="{{ route('admin.surveys.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Retour
            </a>
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total répondants</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $survey->respondents_count }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Questions</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $survey->questions->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Taux de réponse</p>
                        <p class="text-2xl font-bold text-gray-900">
                            @php
                                $totalEmployees = \App\Models\User::where('role', 'employee')->count();
                                $rate = $totalEmployees > 0 ? round(($survey->respondents_count / $totalEmployees) * 100) : 0;
                            @endphp
                            {{ $rate }}%
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Questions Results -->
        <div class="space-y-6">
            @foreach($survey->questions as $index => $question)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-start">
                            <span class="flex-shrink-0 w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-medium">
                                {{ $index + 1 }}
                            </span>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $question->question }}</h3>
                                <p class="text-sm text-gray-500">{{ $question->type_label }} - {{ $question->responses->count() }} réponse(s)</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        @if($question->type === 'choice' || $question->type === 'yesno')
                            @php
                                $stats = $question->statistics;
                            @endphp
                            <div class="space-y-4">
                                @foreach($stats['distribution'] as $option => $data)
                                    <div>
                                        <div class="flex items-center justify-between text-sm mb-1">
                                            <span class="font-medium text-gray-700">{{ $option }}</span>
                                            <span class="text-gray-500">{{ $data['count'] }} ({{ $data['percentage'] }}%)</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-4">
                                            <div class="bg-blue-600 h-4 rounded-full transition-all" style="width: {{ $data['percentage'] }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @elseif($question->type === 'rating')
                            @php
                                $responses = $question->responses->pluck('reponse');
                                $average = $responses->count() > 0 ? round($responses->avg(), 1) : 0;
                                $distribution = [];
                                for ($i = 1; $i <= 5; $i++) {
                                    $distribution[$i] = $responses->filter(fn($r) => (int)$r === $i)->count();
                                }
                            @endphp
                            <div class="flex items-center mb-6">
                                <div class="text-4xl font-bold text-gray-900">{{ $average }}</div>
                                <div class="ml-2 text-gray-500">/ 5</div>
                                <div class="ml-4 flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-6 h-6 {{ $i <= round($average) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    @endfor
                                </div>
                            </div>
                            <div class="space-y-2">
                                @for($i = 5; $i >= 1; $i--)
                                    @php
                                        $count = $distribution[$i];
                                        $percentage = $responses->count() > 0 ? round(($count / $responses->count()) * 100) : 0;
                                    @endphp
                                    <div class="flex items-center">
                                        <span class="w-4 text-sm text-gray-600">{{ $i }}</span>
                                        <svg class="w-4 h-4 ml-1 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                        <div class="flex-1 mx-3">
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                            </div>
                                        </div>
                                        <span class="w-12 text-sm text-gray-500 text-right">{{ $count }}</span>
                                    </div>
                                @endfor
                            </div>
                        @else
                            <!-- Text responses -->
                            <div class="space-y-3 max-h-96 overflow-y-auto">
                                @forelse($question->responses as $response)
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <p class="text-gray-700">{{ $response->reponse }}</p>
                                        <p class="text-xs text-gray-400 mt-2">{{ $response->user->name }} - {{ $response->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                @empty
                                    <p class="text-gray-500 text-center py-4">Aucune réponse</p>
                                @endforelse
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-layouts.admin>
