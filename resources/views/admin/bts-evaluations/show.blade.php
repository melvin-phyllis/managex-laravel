<x-layouts.admin>
<div class="space-y-6 max-w-4xl mx-auto">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.bts-evaluations.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Retour aux fiches BTS
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Fiche BTS — {{ $evaluation->intern->name ?? 'Stagiaire' }}</h1>
            <p class="text-sm text-gray-500 mt-1">
                Période : {{ $evaluation->stage_start_date->format('d/m/Y') }} → {{ $evaluation->stage_end_date->format('d/m/Y') }}
                ({{ $evaluation->stage_duration }})
            </p>
        </div>
        <div class="flex items-center gap-2">
            @if($evaluation->canBeEdited())
                <a href="{{ route('admin.bts-evaluations.edit', $evaluation) }}" class="px-4 py-2 rounded-lg text-sm font-medium bg-blue-50 text-blue-600 hover:bg-blue-100">✏️ Modifier</a>
            @endif
            <a href="{{ route('admin.bts-evaluations.pdf', $evaluation) }}" class="px-4 py-2 rounded-lg text-sm font-medium bg-red-50 text-red-600 hover:bg-red-100">📄 Export PDF</a>
        </div>
    </div>

    @if(session('success'))
        <div class="border-l-4 p-4 rounded-r-lg" style="background: rgba(27, 60, 53, 0.1); border-color: #1B3C35;">
            <p class="text-sm font-medium" style="color: #1B3C35;">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="border-l-4 border-red-500 bg-red-50 p-4 rounded-r-lg">
            <p class="text-sm font-medium text-red-700">{{ session('error') }}</p>
        </div>
    @endif

    {{-- Total Score Card --}}
    @php $grade = $evaluation->grade_info; @endphp
    <div class="rounded-xl overflow-hidden" style="background: linear-gradient(135deg, #1B3C35, #3D7A6A);">
        <div class="p-6 text-center text-white">
            <div class="text-6xl font-black">{{ $evaluation->total_score }}<span class="text-2xl text-white/60">/20</span></div>
            <div class="mt-2 inline-block px-4 py-1 rounded-full text-sm font-bold bg-white/20">{{ $grade['label'] }}</div>
            <div class="mt-2 flex justify-center gap-6 text-sm text-white/70">
                <span>Statut : <strong class="text-white">{{ $evaluation->status === 'draft' ? 'Brouillon' : 'Soumise' }}</strong></span>
                <span>Évaluateur : <strong class="text-white">{{ $evaluation->evaluator->name ?? 'N/A' }}</strong></span>
            </div>
        </div>
    </div>

    {{-- Infos stagiaire --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <h3 class="font-semibold text-gray-900 mb-3">Informations</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div><span class="text-gray-400">N° BTS</span><br><strong>{{ $evaluation->intern_bts_number ?? '-' }}</strong></div>
            <div><span class="text-gray-400">Filière</span><br><strong>{{ $evaluation->intern_field ?? '-' }}</strong></div>
            <div><span class="text-gray-400">Département</span><br><strong>{{ $evaluation->intern->department->name ?? '-' }}</strong></div>
            <div><span class="text-gray-400">Durée</span><br><strong>{{ $evaluation->stage_duration }}</strong></div>
        </div>
    </div>

    {{-- Critères --}}
    <div class="space-y-3">
        {{-- Assiduité --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-semibold text-gray-900">1. Assiduité et ponctualité</h3>
                <span class="text-lg font-bold" style="color: #1B3C35;">{{ $evaluation->assiduity_score }}/3</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2 mb-2">
                <div class="h-2 rounded-full" style="width: {{ ($evaluation->assiduity_score / 3) * 100 }}%; background: #1B3C35;"></div>
            </div>
            @if($evaluation->assiduity_details)
                <p class="text-xs text-gray-500">{{ $evaluation->assiduity_details }}</p>
            @endif
        </div>

        {{-- Relations --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-semibold text-gray-900">2. Relations humaines et professionnelles</h3>
                <span class="text-lg font-bold" style="color: #1B3C35;">{{ $evaluation->relations_score }}/4</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2 mb-2">
                <div class="h-2 rounded-full" style="width: {{ ($evaluation->relations_score / 4) * 100 }}%; background: #3b82f6;"></div>
            </div>
            <div class="flex flex-wrap gap-2 mt-2">
                @foreach(\App\Models\BtsEvaluation::RELATIONS_SUBCRITERIA as $field => $label)
                    <span class="text-xs px-2 py-1 rounded-full {{ $evaluation->$field ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-400' }}">
                        {{ $evaluation->$field ? '✓' : '✗' }} {{ $label }}
                    </span>
                @endforeach
            </div>
        </div>

        {{-- Exécution --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-semibold text-gray-900">3. Intelligence d'exécution des tâches</h3>
                <span class="text-lg font-bold" style="color: #1B3C35;">{{ $evaluation->execution_score }}/6</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2 mb-2">
                <div class="h-2 rounded-full" style="width: {{ ($evaluation->execution_score / 6) * 100 }}%; background: #ea580c;"></div>
            </div>
            @if($evaluation->execution_details)
                <p class="text-xs text-gray-500">{{ $evaluation->execution_details }}</p>
            @endif
        </div>

        {{-- Initiative --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-semibold text-gray-900">4. Esprit d'initiative</h3>
                <span class="text-lg font-bold" style="color: #1B3C35;">{{ $evaluation->initiative_score }}/4</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2 mb-2">
                <div class="h-2 rounded-full" style="width: {{ ($evaluation->initiative_score / 4) * 100 }}%; background: #8b5cf6;"></div>
            </div>
            @if($evaluation->initiative_details)
                <p class="text-xs text-gray-500">{{ $evaluation->initiative_details }}</p>
            @endif
        </div>

        {{-- Présentation --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-semibold text-gray-900">5. Présentation</h3>
                <span class="text-lg font-bold" style="color: #1B3C35;">{{ $evaluation->presentation_score }}/3</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2 mb-2">
                <div class="h-2 rounded-full" style="width: {{ ($evaluation->presentation_score / 3) * 100 }}%; background: #d97706;"></div>
            </div>
            <p class="text-xs text-gray-500">🤖 Calculé automatiquement depuis les notes des tâches</p>
        </div>
    </div>

    {{-- Appréciation --}}
    @if($evaluation->appreciation)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <h3 class="font-semibold text-gray-900 mb-2">Appréciation du maître de stage</h3>
        <p class="text-sm text-gray-700 whitespace-pre-line">{{ $evaluation->appreciation }}</p>
    </div>
    @endif

    {{-- Justification --}}
    @if($evaluation->justification_report)
    <div class="bg-red-50 rounded-xl border border-red-200 p-5">
        <h3 class="font-semibold text-red-700 mb-2">⚠️ Rapport justificatif (note > 16/20)</h3>
        <p class="text-sm text-red-700 whitespace-pre-line">{{ $evaluation->justification_report }}</p>
    </div>
    @endif

    {{-- Submit action --}}
    @if($evaluation->canBeEdited())
    <div class="flex justify-end">
        <form action="{{ route('admin.bts-evaluations.submit', $evaluation) }}" method="POST"
              onsubmit="return confirm('Êtes-vous sûr de vouloir soumettre cette fiche ? Elle ne pourra plus être modifiée.');">
            @csrf
            <button type="submit" class="px-6 py-2.5 rounded-lg text-sm font-bold text-white" style="background: linear-gradient(135deg, #1B3C35, #3D7A6A);">
                ✅ Soumettre officiellement
            </button>
        </form>
    </div>
    @endif
</div>
</x-layouts.admin>
