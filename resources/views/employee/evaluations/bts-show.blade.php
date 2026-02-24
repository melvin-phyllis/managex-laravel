<x-layouts.employee>
    <div class="space-y-6">
        <!-- Header -->
        <x-table-header title="Détail de l'évaluation BTS" subtitle="{{ $evaluation->stage_start_date->format('d/m/Y') }} - {{ $evaluation->stage_end_date->format('d/m/Y') }}">
            <x-slot:icon>
                @php
                    $gradeInfo = $evaluation->grade_info;
                    $scorePercent = ($evaluation->total_score / 20) * 100;
                @endphp
                <div class="w-12 h-12 rounded-full flex items-center justify-center text-sm font-bold
                    @if($scorePercent >= 80) bg-green-100 text-green-600
                    @elseif($scorePercent >= 60) bg-blue-100 text-blue-600
                    @elseif($scorePercent >= 50) bg-yellow-100 text-yellow-600
                    @elseif($scorePercent >= 40) bg-orange-100 text-orange-600
                    @else bg-red-100 text-red-600
                    @endif">
                    {{ number_format($evaluation->total_score, 1) }}
                </div>
            </x-slot:icon>
            <x-slot:actions>
                <a href="{{ route('employee.evaluations.index') }}" class="inline-flex items-center px-4 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 transition-all text-sm">
                    <x-icon name="arrow-left" class="w-4 h-4 mr-2" />
                    Retour
                </a>
            </x-slot:actions>
        </x-table-header>

        <!-- Score Summary -->
        <div class="bg-gradient-to-r from-violet-500 to-purple-600 rounded-2xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-lg opacity-75">Note globale BTS</p>
                    <p class="text-sm opacity-50">Évaluée par {{ $evaluation->evaluator->name ?? 'Évaluateur' }}</p>
                </div>
                <div class="text-center">
                    <div class="text-5xl font-bold">{{ number_format($evaluation->total_score, 1) }}</div>
                    <div class="text-xl opacity-75">/20</div>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold">{{ $gradeInfo['label'] }}</div>
                    @if($evaluation->appreciation)
                        <div class="text-sm opacity-75 mt-1">{{ $evaluation->appreciation }}</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Criteria Breakdown -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($criteria as $key => $criterion)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ $criterion['label'] }}</h3>
                            <p class="text-xs text-gray-400">{{ $criterion['type'] === 'auto' ? 'Calculé automatiquement' : ($criterion['type'] === 'manual' ? 'Évalué manuellement' : 'Semi-automatique') }}</p>
                        </div>
                        <div class="text-2xl font-bold text-violet-600">
                            {{ number_format($evaluation->{$key.'_score'}, 1) }}/{{ $criterion['max'] }}
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="w-full bg-gray-200 rounded-full h-3 mb-3">
                        @php
                            $percent = $criterion['max'] > 0 ? ($evaluation->{$key.'_score'} / $criterion['max']) * 100 : 0;
                        @endphp
                        <div class="h-3 rounded-full
                            @if($percent >= 75) bg-green-500
                            @elseif($percent >= 50) bg-yellow-500
                            @else bg-red-500
                            @endif"
                            style="width: {{ $percent }}%">
                        </div>
                    </div>

                    <!-- Details -->
                    @if($key === 'relations')
                        <div class="space-y-2 mt-3">
                            @foreach(\App\Models\BtsEvaluation::RELATIONS_SUBCRITERIA as $subKey => $subLabel)
                                <div class="flex items-center gap-2 text-sm">
                                    @if($evaluation->$subKey)
                                        <x-icon name="check-circle" class="w-5 h-5 text-green-500" />
                                    @else
                                        <x-icon name="x-circle" class="w-5 h-5 text-red-400" />
                                    @endif
                                    <span class="text-gray-600">{{ $subLabel }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if($evaluation->{$key.'_details'})
                        <div class="bg-gray-50 rounded-xl p-3 mt-3">
                            <p class="text-sm text-gray-700">{{ $evaluation->{$key.'_details'} }}</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Appreciation -->
        @if($evaluation->appreciation)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-900 mb-2">Appréciation générale</h3>
                <div class="bg-violet-50 border border-violet-200 rounded-xl p-4">
                    <p class="text-violet-800">{{ $evaluation->appreciation }}</p>
                </div>
            </div>
        @endif

        <!-- Meta info -->
        <div class="text-center text-sm text-gray-400">
            Évaluation soumise le {{ $evaluation->submitted_at?->format('d/m/Y à H:i') ?? 'N/A' }}
        </div>
    </div>
</x-layouts.employee>
