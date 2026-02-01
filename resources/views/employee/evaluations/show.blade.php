<x-layouts.employee>
    <div class="space-y-6">
        <!-- Header -->
        <x-table-header title="{{ $evaluation->week_label }}" subtitle="Détail de mon évaluation">
            <x-slot:icon>
                <div class="w-12 h-12 rounded-full flex items-center justify-center text-xl font-bold
                    @if($evaluation->grade_letter === 'A') bg-green-100 text-green-600
                    @elseif($evaluation->grade_letter === 'B') bg-blue-100 text-blue-600
                    @elseif($evaluation->grade_letter === 'C') bg-yellow-100 text-yellow-600
                    @elseif($evaluation->grade_letter === 'D') bg-orange-100 text-orange-600
                    @else bg-red-100 text-red-600
                    @endif">
                    {{ $evaluation->grade_letter }}
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
                    <p class="text-lg opacity-75">Note globale</p>
                    <p class="text-sm opacity-50">Évaluée par {{ $evaluation->tutor->name ?? 'Tuteur' }}</p>
                </div>
                <div class="text-center">
                    <div class="text-5xl font-bold">{{ $evaluation->total_score }}</div>
                    <div class="text-xl opacity-75">/10</div>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold">{{ $evaluation->grade_letter }}</div>
                    <div class="text-sm opacity-75">{{ $grades[$evaluation->grade_letter]['label'] }}</div>
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
                            <p class="text-sm text-gray-500">{{ $criterion['description'] }}</p>
                        </div>
                        <div class="text-2xl font-bold text-violet-600">
                            {{ $evaluation->{$key.'_score'} }}/2.5
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="w-full bg-gray-200 rounded-full h-3 mb-4">
                        <div class="h-3 rounded-full 
                            @if($evaluation->{$key.'_score'} >= 2) bg-green-500
                            @elseif($evaluation->{$key.'_score'} >= 1) bg-yellow-500
                            @else bg-red-500
                            @endif" 
                            style="width: {{ ($evaluation->{$key.'_score'} / 2.5) * 100 }}%">
                        </div>
                    </div>

                    <!-- Comment -->
                    @if($evaluation->{$key.'_comment'})
                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="text-sm text-gray-700">{{ $evaluation->{$key.'_comment'} }}</p>
                        </div>
                    @else
                        <p class="text-sm text-gray-400 italic">Aucun commentaire</p>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- General Comments -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 space-y-6">
            @if($evaluation->general_comment)
                <div>
                    <h3 class="font-semibold text-gray-900 mb-2">Bilan de la semaine</h3>
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-gray-700">{{ $evaluation->general_comment }}</p>
                    </div>
                </div>
            @endif

            @if($evaluation->objectives_next_week)
                <div>
                    <h3 class="font-semibold text-gray-900 mb-2">Objectifs pour la semaine prochaine</h3>
                    <div class="bg-violet-50 border border-violet-200 rounded-xl p-4">
                        <p class="text-violet-800">{{ $evaluation->objectives_next_week }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Meta info -->
        <div class="text-center text-sm text-gray-400">
            Évaluation soumise le {{ $evaluation->submitted_at?->format('d/m/Y à H:i') ?? 'N/A' }}
        </div>
    </div>
</x-layouts.employee>
