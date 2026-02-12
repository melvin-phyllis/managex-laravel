<x-layouts.admin>
    <div class="space-y-6">
        <!-- Header -->
        <x-table-header title="Détails du Stagiaire" subtitle="{{ $intern->name }}" class="animate-fade-in-up">
            <x-slot:icon>
                <div class="w-12 h-12 bg-gradient-to-br from-violet-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg shadow-violet-500/20">
                    <x-icon name="user" class="w-6 h-6 text-white" />
                </div>
            </x-slot:icon>
            <x-slot:actions>
                <div class="flex gap-2">
                    <a href="{{ route('admin.intern-evaluations.create', $intern) }}" class="inline-flex items-center px-4 py-2.5 bg-violet-600 text-white font-medium rounded-xl hover:bg-violet-700 transition-all text-sm shadow-md">
                        <x-icon name="plus" class="w-4 h-4 mr-2" />
                        Nouvelle évaluation
                    </a>
                    <a href="{{ route('admin.intern-evaluations.index') }}" class="inline-flex items-center px-4 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 transition-all text-sm">
                        <x-icon name="arrow-left" class="w-4 h-4 mr-2" />
                        Retour
                    </a>
                    <button type="button" 
                            onclick="window.dispatchEvent(new CustomEvent('start-download', { detail: { url: @js(route('admin.intern-evaluations.export-pdf', ['intern_id' => $intern->id])), filename: @js('evaluation-' . $intern->name . '.pdf'), type: 'pdf' } }))"
                            class="inline-flex items-center px-4 py-2.5 bg-red-50 text-red-700 font-medium rounded-xl border border-red-200 hover:bg-red-100 transition-all text-sm">
                        <x-icon name="file-text" class="w-4 h-4 mr-2" />
                        Export PDF
                    </button>
                </div>
            </x-slot:actions>
        </x-table-header>

        <!-- Info Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fade-in-up animation-delay-100">
            <!-- Profil -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-16 h-16 bg-gradient-to-br from-violet-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-2xl">
                        {{ strtoupper(substr($intern->name, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ $intern->name }}</h3>
                        <p class="text-gray-500">{{ $intern->email }}</p>
                    </div>
                </div>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Département</span>
                        <span class="font-medium">{{ $intern->department->name ?? 'Non assigné' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Poste</span>
                        <span class="font-medium">{{ $intern->position->name ?? 'Stagiaire' }}</span>
                    </div>

                </div>

                <!-- Assign Supervisor Form -->

            </div>

            <!-- Moyennes -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Moyennes globales</h3>
                @if($averages['total'] > 0)
                    <div class="text-center mb-6">
                        <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-gradient-to-br from-violet-500 to-purple-600 text-white">
                            <span class="text-3xl font-bold">{{ $averages['total'] }}</span>
                            <span class="text-lg">/10</span>
                        </div>
                    </div>
                    <div class="space-y-3">
                        @foreach(['discipline' => 'Discipline', 'behavior' => 'Comportement', 'skills' => 'Compétences', 'communication' => 'Communication'] as $key => $label)
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">{{ $label }}</span>
                                    <span class="font-medium">{{ $averages[$key] }}/2.5</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-violet-600 h-2 rounded-full" style="width: {{ ($averages[$key] / 2.5) * 100 }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">Aucune évaluation soumise</p>
                @endif
            </div>

            <!-- Progression Chart -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Progression</h3>
                @if($progressionData->isNotEmpty())
                    <canvas id="progressionChart" class="w-full h-48"></canvas>
                @else
                    <p class="text-gray-500 text-center py-8">Pas assez de données</p>
                @endif
            </div>
        </div>

        <!-- Historique des évaluations -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden animate-fade-in-up animation-delay-200">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">Historique des évaluations</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Semaine</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Discipline</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Comportement</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Compétences</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Communication</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Total</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Évaluateur</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($evaluations as $evaluation)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <span class="font-medium text-gray-900">{{ $evaluation->week_label }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">{{ $evaluation->discipline_score }}/2.5</td>
                                <td class="px-6 py-4 text-center">{{ $evaluation->behavior_score }}/2.5</td>
                                <td class="px-6 py-4 text-center">{{ $evaluation->skills_score }}/2.5</td>
                                <td class="px-6 py-4 text-center">{{ $evaluation->communication_score }}/2.5</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-sm font-semibold
                                        @if($evaluation->grade_letter === 'A') bg-green-100 text-green-700
                                        @elseif($evaluation->grade_letter === 'B') bg-blue-100 text-blue-700
                                        @elseif($evaluation->grade_letter === 'C') bg-yellow-100 text-yellow-700
                                        @elseif($evaluation->grade_letter === 'D') bg-orange-100 text-orange-700
                                        @else bg-red-100 text-red-700
                                        @endif">
                                        {{ $evaluation->total_score }}/10
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $evaluation->tutor->name ?? 'Admin' }}</td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.intern-evaluations.edit', $evaluation) }}" class="text-violet-600 hover:text-violet-900 font-medium text-sm">Modifier</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    Aucune évaluation enregistrée
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
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
                            label: 'Score total',
                            data: data.map(d => d.score),
                            borderColor: 'rgb(139, 92, 246)',
                            backgroundColor: 'rgba(139, 92, 246, 0.1)',
                            tension: 0.3,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                min: 0,
                                max: 10
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            }
        });
    </script>
    @endpush
</x-layouts.admin>
