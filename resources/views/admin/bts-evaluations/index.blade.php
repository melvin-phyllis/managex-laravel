<x-layouts.admin>
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Fiches d'Évaluation BTS</h1>
            <p class="text-sm text-gray-500 mt-1">Évaluation officielle des stagiaires BTS (barème /20)</p>
        </div>
    </div>

    @if(session('success'))
        <div class="border-l-4 p-4 rounded-r-lg" style="background: rgba(27, 60, 53, 0.1); border-color: #1B3C35;">
            <p class="text-sm font-medium" style="color: #1B3C35;">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="text-sm text-gray-500">Total fiches</div>
            <div class="text-2xl font-bold text-gray-900 mt-1">{{ $evaluations->count() }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="text-sm text-gray-500">Brouillons</div>
            <div class="text-2xl font-bold text-amber-600 mt-1">{{ $evaluations->where('status', 'draft')->count() }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="text-sm text-gray-500">Soumises</div>
            <div class="text-2xl font-bold mt-1" style="color: #1B3C35;">{{ $evaluations->where('status', 'submitted')->count() }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="text-sm text-gray-500">Moyenne générale</div>
            <div class="text-2xl font-bold text-gray-900 mt-1">
                {{ $evaluations->count() > 0 ? number_format($evaluations->avg('total_score'), 1) : '-' }}/20
            </div>
        </div>
    </div>

    {{-- Stagiaires sans fiche BTS --}}
    @if($internsWithoutEval->count())
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-5 py-3 border-b" style="background: linear-gradient(135deg, #C8A96E, #a8884e);">
            <h2 class="text-white font-semibold">⚠️ Stagiaires sans fiche BTS ({{ $internsWithoutEval->count() }})</h2>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($internsWithoutEval as $intern)
            <div class="px-5 py-3 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold" style="background: #1B3C35;">
                        {{ strtoupper(substr($intern->name, 0, 2)) }}
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $intern->name }}</p>
                        <p class="text-xs text-gray-400">{{ $intern->department->name ?? 'Non assigné' }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.bts-evaluations.create', $intern) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition-all hover:opacity-90"
                   style="background: linear-gradient(135deg, #1B3C35, #3D7A6A);">
                    + Créer fiche BTS
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Liste des évaluations --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-5 py-3 border-b" style="background: linear-gradient(135deg, #1B3C35, #3D7A6A);">
            <h2 class="text-white font-semibold">📋 Fiches d'évaluation</h2>
        </div>

        @if($evaluations->isEmpty())
            <div class="p-12 text-center text-gray-400">
                Aucune fiche BTS créée pour le moment.
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stagiaire</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Assiduité /3</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Relations /4</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Exécution /6</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Initiative /4</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Présentation /3</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total /20</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Statut</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($evaluations as $eval)
                    @php $grade = $eval->grade_info; @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full flex items-center justify-center text-white text-[10px] font-bold" style="background: #1B3C35;">
                                    {{ strtoupper(substr($eval->intern->name ?? '?', 0, 2)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $eval->intern->name ?? 'Supprimé' }}</p>
                                    <p class="text-[10px] text-gray-400">{{ $eval->stage_start_date->format('d/m/Y') }} → {{ $eval->stage_end_date->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center text-sm font-semibold">{{ $eval->assiduity_score }}</td>
                        <td class="px-4 py-3 text-center text-sm font-semibold">{{ $eval->relations_score }}</td>
                        <td class="px-4 py-3 text-center text-sm font-semibold">{{ $eval->execution_score }}</td>
                        <td class="px-4 py-3 text-center text-sm font-semibold">{{ $eval->initiative_score }}</td>
                        <td class="px-4 py-3 text-center text-sm font-semibold">{{ $eval->presentation_score }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-bold text-white" style="background: {{ $grade['color'] }};">
                                {{ $eval->total_score }}/20
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($eval->status === 'draft')
                                <span class="text-xs px-2 py-1 rounded-full bg-amber-50 text-amber-600">Brouillon</span>
                            @elseif($eval->status === 'submitted')
                                <span class="text-xs px-2 py-1 rounded-full bg-green-50 text-green-600">Soumise</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('admin.bts-evaluations.show', $eval) }}" class="px-2 py-1 rounded text-xs bg-gray-100 text-gray-600 hover:bg-gray-200">Voir</a>
                                @if($eval->canBeEdited())
                                <a href="{{ route('admin.bts-evaluations.edit', $eval) }}" class="px-2 py-1 rounded text-xs bg-blue-50 text-blue-600 hover:bg-blue-100">Modifier</a>
                                @endif
                                <a href="{{ route('admin.bts-evaluations.pdf', $eval) }}" class="px-2 py-1 rounded text-xs bg-red-50 text-red-600 hover:bg-red-100">PDF</a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
</x-layouts.admin>
