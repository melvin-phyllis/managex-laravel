<x-layouts.admin>
    <div class="space-y-6">
        <!-- Header -->
        <x-table-header title="Suivi des Stagiaires" subtitle="Dashboard d'évaluation hebdomadaire des stagiaires" class="animate-fade-in-up">
            <x-slot:icon>
                <div class="w-12 h-12 bg-gradient-to-br from-violet-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg shadow-violet-500/20">
                    <x-icon name="graduation-cap" class="w-6 h-6 text-white" />
                </div>
            </x-slot:icon>
            <x-slot:actions>
                <div class="flex gap-2">
                    @if(auth()->user()->supervisees()->interns()->exists())
                    <a href="{{ route('admin.tutor.evaluations.index') }}" class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-violet-600 to-purple-600 text-white font-medium rounded-xl hover:from-violet-700 hover:to-purple-700 transition-all shadow-lg shadow-violet-500/30 text-sm">
                        <x-icon name="edit-3" class="w-4 h-4 mr-2" />
                        Évaluer mes stagiaires
                    </a>
                    @endif
                    <a href="{{ route('admin.intern-evaluations.missing') }}" class="inline-flex items-center px-4 py-2.5 bg-amber-50 text-amber-700 font-medium rounded-xl border border-amber-200 hover:bg-amber-100 transition-all shadow-sm text-sm">
                        <x-icon name="alert-triangle" class="w-4 h-4 mr-2" />
                        Manquantes ({{ $stats['pending_evaluations'] ?? 0 }})
                    </a>
                    <a href="{{ route('admin.intern-evaluations.report') }}" class="inline-flex items-center px-4 py-2.5 bg-blue-50 text-blue-700 font-medium rounded-xl border border-blue-200 hover:bg-blue-100 transition-all shadow-sm text-sm">
                        <x-icon name="bar-chart-2" class="w-4 h-4 mr-2" />
                        Rapport
                    </a>
                </div>
            </x-slot:actions>
        </x-table-header>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 animate-fade-in-up animation-delay-100">
            <!-- Total Stagiaires -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Stagiaires</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['total_interns'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-violet-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg shadow-violet-500/30">
                        <x-icon name="users" class="w-6 h-6 text-white" />
                    </div>
                </div>
            </div>

            <!-- Avec tuteur -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Avec tuteur</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['interns_with_supervisor'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg shadow-green-500/30">
                        <x-icon name="user-check" class="w-6 h-6 text-white" />
                    </div>
                </div>
            </div>

            <!-- Évaluations cette semaine -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Cette semaine</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['evaluations_this_week'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/30">
                        <x-icon name="clipboard-check" class="w-6 h-6 text-white" />
                    </div>
                </div>
            </div>

            <!-- Score moyen -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Score moyen</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['average_score'] ?? 0 }}/10</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl flex items-center justify-center shadow-lg shadow-amber-500/30">
                        <x-icon name="star" class="w-6 h-6 text-white" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Distribution des notes (Chart) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 animate-fade-in-up animation-delay-200">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Distribution des notes</h3>
                <canvas id="gradeDistributionChart" class="w-full h-64"></canvas>
            </div>

            <!-- Évaluations récentes -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Évaluations récentes</h3>
                <div class="space-y-3 max-h-64 overflow-y-auto">
                    @forelse($recentEvaluations as $evaluation)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-violet-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                    {{ strtoupper(substr($evaluation->intern->name ?? 'S', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $evaluation->intern->name ?? 'N/A' }}</p>
                                    <p class="text-sm text-gray-500">{{ $evaluation->week_label }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-sm font-semibold
                                    @if($evaluation->grade_letter === 'A') bg-green-100 text-green-700
                                    @elseif($evaluation->grade_letter === 'B') bg-blue-100 text-blue-700
                                    @elseif($evaluation->grade_letter === 'C') bg-yellow-100 text-yellow-700
                                    @elseif($evaluation->grade_letter === 'D') bg-orange-100 text-orange-700
                                    @else bg-red-100 text-red-700
                                    @endif">
                                    {{ $evaluation->total_score }}/10 ({{ $evaluation->grade_letter }})
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-8">Aucune évaluation récente</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Liste des Stagiaires -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden animate-fade-in-up animation-delay-300">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">Liste des stagiaires</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Stagiaire</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Département</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tuteur</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Évaluations</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Dernière note</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($interns as $intern)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-violet-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                            {{ strtoupper(substr($intern->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $intern->name }}</p>
                                            <p class="text-sm text-gray-500">{{ $intern->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-600">{{ $intern->department->name ?? 'Non assigné' }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($intern->supervisor)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                            {{ $intern->supervisor->name }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                            Non assigné
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-sm font-medium text-gray-900">{{ $intern->internEvaluations->count() }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($intern->internEvaluations->first())
                                        @php $lastEval = $intern->internEvaluations->first(); @endphp
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-sm font-semibold
                                            @if($lastEval->grade_letter === 'A') bg-green-100 text-green-700
                                            @elseif($lastEval->grade_letter === 'B') bg-blue-100 text-blue-700
                                            @elseif($lastEval->grade_letter === 'C') bg-yellow-100 text-yellow-700
                                            @elseif($lastEval->grade_letter === 'D') bg-orange-100 text-orange-700
                                            @else bg-red-100 text-red-700
                                            @endif">
                                            {{ $lastEval->total_score }}/10
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.intern-evaluations.show', $intern) }}" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-violet-600 hover:text-violet-800 hover:bg-violet-50 rounded-lg transition-colors">
                                        <x-icon name="eye" class="w-4 h-4 mr-1" />
                                        Détails
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <x-icon name="users" class="w-12 h-12 text-gray-300 mx-auto mb-4" />
                                    <p class="text-gray-500">Aucun stagiaire enregistré</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('gradeDistributionChart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['A (Excellent)', 'B (Bien)', 'C (Satisfaisant)', 'D (À améliorer)', 'E (Insuffisant)'],
                        datasets: [{
                            data: [
                                {{ $scoreDistribution['A'] ?? 0 }},
                                {{ $scoreDistribution['B'] ?? 0 }},
                                {{ $scoreDistribution['C'] ?? 0 }},
                                {{ $scoreDistribution['D'] ?? 0 }},
                                {{ $scoreDistribution['E'] ?? 0 }}
                            ],
                            backgroundColor: [
                                'rgb(34, 197, 94)',
                                'rgb(59, 130, 246)',
                                'rgb(234, 179, 8)',
                                'rgb(249, 115, 22)',
                                'rgb(239, 68, 68)'
                            ],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }
        });
    </script>
    @endpush
</x-layouts.admin>
