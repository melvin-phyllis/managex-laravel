@php
    $isAdmin = auth()->user()->isAdmin();
    $routePrefix = $isAdmin ? 'admin.tutor.evaluations' : 'employee.tutor.evaluations';
@endphp

<x-dynamic-component :component="$isAdmin ? 'layouts.admin' : 'layouts.employee'">
    <div class="space-y-6">
        <!-- Header avec gradient -->
        <div class="bg-gradient-to-r from-violet-600 via-purple-600 to-fuchsia-600 rounded-2xl p-6 text-white shadow-xl">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center text-xl font-bold">
                        {{ strtoupper(substr($intern->name, 0, 2)) }}
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold mb-1">{{ $intern->name }}</h1>
                        <p class="text-violet-100">Historique des évaluations</p>
                    </div>
                </div>
                <a href="{{ route($routePrefix . '.index') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-lg transition-colors font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Retour
                </a>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Moyennes globales -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Moyennes globales
                </h3>
                @if($averages['total'] > 0)
                    <div class="text-center mb-6">
                        <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-gradient-to-br from-violet-500 to-purple-600 text-white shadow-lg">
                            <div>
                                <span class="text-3xl font-bold">{{ $averages['total'] }}</span>
                                <span class="text-sm opacity-80">/10</span>
                            </div>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">Moyenne générale</p>
                    </div>
                    <div class="space-y-3">
                        @php
                            $criteria = [
                                'discipline' => ['label' => 'Discipline', 'color' => 'violet'],
                                'behavior' => ['label' => 'Comportement', 'color' => 'blue'],
                                'skills' => ['label' => 'Compétences', 'color' => 'emerald'],
                                'communication' => ['label' => 'Communication', 'color' => 'amber'],
                            ];
                        @endphp
                        @foreach($criteria as $key => $info)
                            @php
                                $percentage = ($averages[$key] / 2.5) * 100;
                            @endphp
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">{{ $info['label'] }}</span>
                                    <span class="font-semibold text-gray-900">{{ $averages[$key] }}/2.5</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2">
                                    <div class="h-2 rounded-full bg-gradient-to-r from-{{ $info['color'] }}-500 to-{{ $info['color'] }}-600" 
                                         style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <p class="text-gray-500">Pas encore d'évaluation</p>
                    </div>
                @endif
            </div>

            <!-- Graphique de progression -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    Progression dans le temps
                </h3>
                @if($progressionData->isNotEmpty())
                    <div class="h-64">
                        <canvas id="progressionChart"></canvas>
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </div>
                        <p class="text-gray-500">Pas assez de données pour afficher le graphique</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Liste des évaluations -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-slate-50 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Historique détaillé
                </h3>
            </div>

            @if($evaluations->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-violet-100 to-purple-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Aucune évaluation</h3>
                    <p class="text-gray-500">Aucune évaluation n'a encore été enregistrée</p>
                </div>
            @else
                <!-- Vue Desktop -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Semaine</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Discipline</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Comportement</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Compétences</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Communication</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($evaluations as $evaluation)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <span class="font-medium text-gray-900">{{ $evaluation->week_label }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center text-sm text-gray-600">{{ $evaluation->discipline_score }}/2.5</td>
                                    <td class="px-6 py-4 text-center text-sm text-gray-600">{{ $evaluation->behavior_score }}/2.5</td>
                                    <td class="px-6 py-4 text-center text-sm text-gray-600">{{ $evaluation->skills_score }}/2.5</td>
                                    <td class="px-6 py-4 text-center text-sm text-gray-600">{{ $evaluation->communication_score }}/2.5</td>
                                    <td class="px-6 py-4 text-center">
                                        @php
                                            $gradeColors = [
                                                'A' => 'bg-emerald-100 text-emerald-700',
                                                'B' => 'bg-blue-100 text-blue-700',
                                                'C' => 'bg-yellow-100 text-yellow-700',
                                                'D' => 'bg-orange-100 text-orange-700',
                                                'F' => 'bg-red-100 text-red-700',
                                            ];
                                            $gradeColor = $gradeColors[$evaluation->grade_letter] ?? 'bg-gray-100 text-gray-700';
                                        @endphp
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold {{ $gradeColor }}">
                                            {{ $evaluation->total_score }}/10
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Vue Mobile -->
                <div class="md:hidden divide-y divide-gray-100">
                    @foreach($evaluations as $evaluation)
                        @php
                            $gradeColors = [
                                'A' => 'bg-emerald-100 text-emerald-700',
                                'B' => 'bg-blue-100 text-blue-700',
                                'C' => 'bg-yellow-100 text-yellow-700',
                                'D' => 'bg-orange-100 text-orange-700',
                                'F' => 'bg-red-100 text-red-700',
                            ];
                            $gradeColor = $gradeColors[$evaluation->grade_letter] ?? 'bg-gray-100 text-gray-700';
                        @endphp
                        <div class="p-4">
                            <div class="flex items-center justify-between mb-3">
                                <span class="font-semibold text-gray-900">{{ $evaluation->week_label }}</span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold {{ $gradeColor }}">
                                    {{ $evaluation->total_score }}/10
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-sm">
                                <div class="flex justify-between bg-gray-50 rounded-lg px-3 py-2">
                                    <span class="text-gray-500">Discipline</span>
                                    <span class="font-medium">{{ $evaluation->discipline_score }}/2.5</span>
                                </div>
                                <div class="flex justify-between bg-gray-50 rounded-lg px-3 py-2">
                                    <span class="text-gray-500">Comportement</span>
                                    <span class="font-medium">{{ $evaluation->behavior_score }}/2.5</span>
                                </div>
                                <div class="flex justify-between bg-gray-50 rounded-lg px-3 py-2">
                                    <span class="text-gray-500">Compétences</span>
                                    <span class="font-medium">{{ $evaluation->skills_score }}/2.5</span>
                                </div>
                                <div class="flex justify-between bg-gray-50 rounded-lg px-3 py-2">
                                    <span class="text-gray-500">Communication</span>
                                    <span class="font-medium">{{ $evaluation->communication_score }}/2.5</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('progressionChart');
            if (ctx) {
                const data = @json($progressionData);
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.map(d => d.week),
                        datasets: [{
                            label: 'Score total',
                            data: data.map(d => d.score),
                            borderColor: 'rgb(139, 92, 246)',
                            backgroundColor: 'rgba(139, 92, 246, 0.1)',
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: 'rgb(139, 92, 246)',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 7
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: { 
                            y: { 
                                min: 0, 
                                max: 10,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        },
                        plugins: { 
                            legend: { display: false }
                        }
                    }
                });
            }
        });
    </script>
    @endpush
</x-dynamic-component>
