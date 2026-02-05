<x-layouts.admin>
    <div class="space-y-6">
        <!-- Breadcrumb -->
        <nav class="flex" aria-label="Breadcrumb">
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
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Résultats</span>
                    </div>
                </li>
            </ol>
        </nav>
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Résultats du sondage</h1>
                <p class="text-gray-500 mt-1 flex items-center gap-2">
                    <x-icon name="clipboard-list" class="w-4 h-4" />
                    {{ $survey->titre }}
                </p>
            </div>
            <a href="{{ route('admin.surveys.index') }}" class="inline-flex items-center px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-200 transition-colors">
                <x-icon name="arrow-left" class="w-5 h-5 mr-2" />
                Retour
            </a>
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="p-4 rounded-xl" style="background-color: rgba(90, 185, 234, 0.15);">
                        <x-icon name="users" class="w-8 h-8" style="color: #5AB9EA;" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total répondants</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $survey->respondents_count }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="p-4 rounded-xl" style="background-color: rgba(132, 206, 235, 0.15);">
                        <x-icon name="help-circle" class="w-8 h-8" style="color: #84CEEB;" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Questions</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $survey->questions->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="p-4 rounded-xl" style="background-color: rgba(86, 128, 233, 0.15);">
                        <x-icon name="activity" class="w-8 h-8" style="color: #5680E9;" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Taux de réponse</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">
                            @php
                                // $totalEmployees passé depuis le contrôleur
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
                    <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                        <div class="flex items-start">
                            <span class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold shadow-sm" style="background-color: rgba(90, 185, 234, 0.15); color: #5AB9EA;">
                                {{ $index + 1 }}
                            </span>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $question->question }}</h3>
                                <p class="text-sm text-gray-500 flex items-center gap-1 mt-1">
                                    <x-icon name="tag" class="w-3 h-3" />
                                    {{ $question->type_label }}
                                    <span class="mx-1">•</span>
                                    <x-icon name="message-square" class="w-3 h-3" />
                                    {{ $question->responses->count() }} réponse(s)
                                </p>
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
                                            <span class="text-gray-500 font-mono">{{ $data['count'] }} ({{ $data['percentage'] }}%)</span>
                                        </div>
                                        <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                                            <div class="h-3 rounded-full transition-all duration-500" style="width: {{ $data['percentage'] }}%; background: linear-gradient(90deg, #5680E9, #5AB9EA);"></div>
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
                            <div class="flex flex-col sm:flex-row gap-8">
                                <div class="flex flex-col items-center justify-center rounded-xl p-6 min-w-[200px]" style="background-color: rgba(90, 185, 234, 0.08);">
                                    <div class="text-5xl font-bold text-gray-900">{{ $average }}</div>
                                    <div class="text-sm text-gray-500 mt-1 uppercase tracking-wide font-medium">Moyenne / 5</div>
                                    <div class="mt-3 flex items-center gap-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <x-icon name="star" class="w-6 h-6" style="color: {{ $i <= round($average) ? '#5AB9EA' : '#D1D5DB' }}; fill: {{ $i <= round($average) ? '#5AB9EA' : 'none' }};" />
                                        @endfor
                                    </div>
                                </div>
                                <div class="flex-1 space-y-3">
                                    @for($i = 5; $i >= 1; $i--)
                                        @php
                                            $count = $distribution[$i];
                                            $percentage = $responses->count() > 0 ? round(($count / $responses->count()) * 100) : 0;
                                        @endphp
                                        <div class="flex items-center gap-3">
                                            <div class="flex items-center gap-1 w-12 justify-end">
                                                <span class="text-sm font-medium text-gray-600">{{ $i }}</span>
                                                <x-icon name="star" class="w-4 h-4" style="color: #5AB9EA; fill: #5AB9EA;" />
                                            </div>
                                            <div class="flex-1 h-2.5 bg-gray-100 rounded-full overflow-hidden">
                                                <div class="h-2.5 rounded-full transition-all duration-500" style="width: {{ $percentage }}%; background: linear-gradient(90deg, #5680E9, #5AB9EA);"></div>
                                            </div>
                                            <span class="w-12 text-sm text-gray-500 text-right font-mono">{{ $count }}</span>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        @else
                            <!-- Text responses -->
                            <div class="space-y-3 max-h-96 overflow-y-auto custom-scrollbar pr-2">
                                @forelse($question->responses as $response)
                                    <div class="bg-gray-50/80 rounded-xl p-4 border border-gray-100">
                                        <p class="text-gray-800 leading-relaxed">{{ $response->reponse }}</p>
                                        <div class="flex items-center gap-2 mt-3 pt-3 border-t border-gray-100">
                                            <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold" style="background-color: rgba(90, 185, 234, 0.15); color: #5AB9EA;">
                                                {{ strtoupper(substr($response->user->name, 0, 1)) }}
                                            </div>
                                            <p class="text-xs text-gray-500 font-medium">{{ $response->user->name }}</p>
                                            <span class="text-xs text-gray-400">•</span>
                                            <p class="text-xs text-gray-400">{{ $response->created_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-8 text-gray-500 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                                        <p>Aucune réponse textuelle enregistrée.</p>
                                    </div>
                                @endforelse
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-layouts.admin>
