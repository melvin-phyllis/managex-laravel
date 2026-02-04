<x-layouts.employee>
    <div class="space-y-6">
        <!-- Header -->
        <x-table-header title="Mes évaluations" subtitle="Suivi de ma progression" class="animate-fade-in-up">
            <x-slot:icon>
                <div class="w-12 h-12 bg-gradient-to-br from-violet-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg shadow-violet-500/20">
                    <x-icon name="clipboard-check" class="w-6 h-6 text-white" />
                </div>
            </x-slot:icon>
        </x-table-header>

        <!-- Tutor Info -->
        @if($supervisor)
            <div class="bg-gradient-to-r from-violet-500 to-purple-600 rounded-2xl p-6 text-white animate-fade-in-up animation-delay-100">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center text-2xl font-bold">
                        {{ strtoupper(substr($supervisor->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm opacity-75">Mon tuteur</p>
                        <p class="text-xl font-semibold">{{ $supervisor->name }}</p>
                        <p class="text-sm opacity-75">{{ $supervisor->email }}</p>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4">
                <p class="text-amber-800">Aucun tuteur n'a été assigné pour l'instant.</p>
            </div>
        @endif

        <!-- Stats & Latest -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fade-in-up animation-delay-200">
            <!-- Latest Evaluation -->
            @if($latestEvaluation)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Derniére évaluation</h3>
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full 
                            @if($latestEvaluation->grade_letter === 'A') bg-green-100 text-green-600
                            @elseif($latestEvaluation->grade_letter === 'B') bg-blue-100 text-blue-600
                            @elseif($latestEvaluation->grade_letter === 'C') bg-yellow-100 text-yellow-600
                            @elseif($latestEvaluation->grade_letter === 'D') bg-orange-100 text-orange-600
                            @else bg-red-100 text-red-600
                            @endif">
                            <span class="text-3xl font-bold">{{ $latestEvaluation->total_score }}</span>
                        </div>
                        <p class="mt-2 font-medium text-gray-900">{{ $grades[$latestEvaluation->grade_letter]['label'] }}</p>
                        <p class="text-sm text-gray-500">{{ $latestEvaluation->week_label }}</p>
                        <a href="{{ route('employee.evaluations.show', $latestEvaluation) }}" class="inline-flex items-center mt-4 text-violet-600 hover:text-violet-800 text-sm font-medium">
                            Voir le détail
                            <x-icon name="arrow-right" class="w-4 h-4 ml-1" />
                        </a>
                    </div>
                </div>
            @endif

            <!-- Averages -->
            @if($averages)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Moyennes globales</h3>
                    <div class="space-y-3">
                        @foreach($criteria as $key => $criterion)
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">{{ $criterion['label'] }}</span>
                                    <span class="font-medium">{{ $averages[$key] }}/2.5</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-violet-600 h-2 rounded-full" style="width: {{ ($averages[$key] / 2.5) * 100 }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-100 text-center">
                        <p class="text-sm text-gray-500">Moyenne totale</p>
                        <p class="text-2xl font-bold text-violet-600">{{ $averages['total'] }}/10</p>
                    </div>
                </div>
            @endif

            <!-- Progression Chart -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Ma progression</h3>
                @if($progressionData->isNotEmpty())
                    <canvas id="progressionChart" class="w-full h-48"></canvas>
                @else
                    <div class="flex items-center justify-center h-48 text-gray-400">
                        <p>Pas encore assez de données</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- All Evaluations -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden animate-fade-in-up animation-delay-300">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">Historique des évaluations</h3>
            </div>
            @if($evaluations->isEmpty())
                <div class="p-12 text-center">
                    <x-icon name="clipboard" class="w-12 h-12 text-gray-300 mx-auto mb-4" />
                    <p class="text-gray-500">Aucune évaluation pour l'instant</p>
                    <p class="text-sm text-gray-400 mt-1">Votre tuteur vous évaluera chaque semaine</p>
                </div>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach($evaluations as $evaluation)
                        <a href="{{ route('employee.evaluations.show', $evaluation) }}" class="flex items-center justify-between p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg
                                    @if($evaluation->grade_letter === 'A') bg-green-100 text-green-600
                                    @elseif($evaluation->grade_letter === 'B') bg-blue-100 text-blue-600
                                    @elseif($evaluation->grade_letter === 'C') bg-yellow-100 text-yellow-600
                                    @elseif($evaluation->grade_letter === 'D') bg-orange-100 text-orange-600
                                    @else bg-red-100 text-red-600
                                    @endif">
                                    {{ $evaluation->grade_letter }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $evaluation->week_label }}</p>
                                    <p class="text-sm text-gray-500">Par {{ $evaluation->tutor->name ?? 'Tuteur' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="text-right">
                                    <p class="text-2xl font-bold text-gray-900">{{ $evaluation->total_score }}/10</p>
                                    <p class="text-sm text-gray-500">{{ $grades[$evaluation->grade_letter]['label'] }}</p>
                                </div>
                                <x-icon name="chevron-right" class="w-5 h-5 text-gray-400" />
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script nonce="{{ $cspNonce ?? '' }}">
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('progressionChart');
            if (ctx) {
                const data = @json($progressionData);
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.map(d => d.week),
                        datasets: [{
                            label: 'Score',
                            data: data.map(d => d.score),
                            borderColor: 'rgb(139, 92, 246)',
                            backgroundColor: 'rgba(139, 92, 246, 0.1)',
                            tension: 0.3,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: { y: { min: 0, max: 10 } },
                        plugins: { legend: { display: false } }
                    }
                });
            }
        });
    </script>
    @endpush
</x-layouts.employee>
