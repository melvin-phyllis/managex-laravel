<x-layouts.admin>
<div class="space-y-6 max-w-4xl mx-auto">
    <div>
        <a href="{{ route('admin.bts-evaluations.show', $evaluation) }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1 mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Retour à la fiche
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Modifier — {{ $intern->name }}</h1>
    </div>

    @if($errors->any())
        <div class="border-l-4 border-red-500 bg-red-50 p-4 rounded-r-lg">
            <ul class="text-sm text-red-700 list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('admin.bts-evaluations.update', $evaluation) }}" method="POST" id="btsForm">
        @csrf @method('PUT')

        {{-- Informations --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
            <div class="px-5 py-3" style="background: linear-gradient(135deg, #1B3C35, #3D7A6A);"><h2 class="text-white font-semibold">📝 Informations</h2></div>
            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-500 mb-1">Nom</label><input type="text" value="{{ $intern->name }}" disabled class="w-full px-3 py-2 rounded-lg bg-gray-50 border border-gray-200 text-sm"></div>
                <div><label class="block text-xs font-medium text-gray-500 mb-1">Entreprise</label><input type="text" value="Ya Consulting" disabled class="w-full px-3 py-2 rounded-lg bg-gray-50 border border-gray-200 text-sm"></div>
                <div><label class="block text-xs font-medium text-gray-500 mb-1">N° BTS</label><input type="text" name="intern_bts_number" value="{{ old('intern_bts_number', $evaluation->intern_bts_number) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500"></div>
                <div><label class="block text-xs font-medium text-gray-500 mb-1">Filière</label><input type="text" name="intern_field" value="{{ old('intern_field', $evaluation->intern_field) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500"></div>
                <div><label class="block text-xs font-medium text-gray-500 mb-1">Début stage</label><input type="date" name="stage_start_date" value="{{ old('stage_start_date', $evaluation->stage_start_date->format('Y-m-d')) }}" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm"></div>
                <div><label class="block text-xs font-medium text-gray-500 mb-1">Fin stage</label><input type="date" name="stage_end_date" value="{{ old('stage_end_date', $evaluation->stage_end_date->format('Y-m-d')) }}" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm"></div>
            </div>
        </div>

        {{-- Assiduité /3 --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-4">
            <div class="px-5 py-3 flex items-center justify-between" style="background: linear-gradient(135deg, #065f46, #059669);"><h3 class="text-white font-semibold">1. Assiduité et ponctualité</h3><span class="text-white text-sm font-bold">/ 3</span></div>
            <div class="p-5">
                <p class="text-xs text-gray-500 mb-3">{{ $autoScores['assiduity']['details'] }}</p>
                <div class="flex items-center gap-4">
                    <input type="number" name="assiduity_score" value="{{ old('assiduity_score', $evaluation->assiduity_score) }}" min="0" max="3" step="0.5" required class="w-24 px-3 py-2 rounded-lg border-2 border-emerald-300 text-center text-lg font-bold criterion-score" data-max="3">
                    <span class="text-sm text-gray-400">/ 3</span>
                </div>
            </div>
        </div>

        {{-- Relations /4 --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-4">
            <div class="px-5 py-3 flex items-center justify-between" style="background: linear-gradient(135deg, #1e40af, #3b82f6);"><h3 class="text-white font-semibold">2. Relations humaines</h3><span class="text-white text-sm font-bold">/ 4</span></div>
            <div class="p-5 space-y-3">
                @foreach(\App\Models\BtsEvaluation::RELATIONS_SUBCRITERIA as $field => $label)
                <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-100 hover:bg-gray-50 cursor-pointer">
                    <input type="hidden" name="{{ $field }}" value="0">
                    <input type="checkbox" name="{{ $field }}" value="1" {{ old($field, $evaluation->$field) ? 'checked' : '' }} class="w-5 h-5 rounded text-emerald-600 subcriteria-input" data-group="relations">
                    <span class="text-sm text-gray-700">{{ $label }}</span><span class="ml-auto text-xs text-gray-400">+1 pt</span>
                </label>
                @endforeach
                <div class="text-right"><span class="text-sm text-gray-500">Score : </span><span id="relations-total" class="text-lg font-bold" style="color: #1B3C35;">{{ $evaluation->relations_score }}</span><span class="text-sm text-gray-400"> / 4</span></div>
            </div>
        </div>

        {{-- Exécution /6 --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-4">
            <div class="px-5 py-3 flex items-center justify-between" style="background: linear-gradient(135deg, #7c2d12, #ea580c);"><h3 class="text-white font-semibold">3. Exécution des tâches</h3><span class="text-white text-sm font-bold">/ 6</span></div>
            <div class="p-5">
                <p class="text-xs text-gray-500 mb-3">{{ $autoScores['execution']['details'] }}</p>
                <div class="flex items-center gap-4">
                    <input type="number" name="execution_score" value="{{ old('execution_score', $evaluation->execution_score) }}" min="0" max="6" step="0.5" required class="w-24 px-3 py-2 rounded-lg border-2 border-orange-300 text-center text-lg font-bold criterion-score" data-max="6">
                    <span class="text-sm text-gray-400">/ 6</span>
                </div>
            </div>
        </div>

        {{-- Initiative /4 --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-4">
            <div class="px-5 py-3 flex items-center justify-between" style="background: linear-gradient(135deg, #5b21b6, #8b5cf6);"><h3 class="text-white font-semibold">4. Esprit d'initiative</h3><span class="text-white text-sm font-bold">/ 4</span></div>
            <div class="p-5">
                <p class="text-xs text-gray-500 mb-3">{{ $autoScores['initiative']['details'] }}</p>
                <div class="flex items-center gap-4">
                    <input type="number" name="initiative_score" value="{{ old('initiative_score', $evaluation->initiative_score) }}" min="0" max="4" step="0.5" required class="w-24 px-3 py-2 rounded-lg border-2 border-purple-300 text-center text-lg font-bold criterion-score" data-max="4">
                    <span class="text-sm text-gray-400">/ 4</span>
                </div>
            </div>
        </div>

        {{-- Présentation /3 (AUTO) --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-4">
            <div class="px-5 py-3 flex items-center justify-between" style="background: linear-gradient(135deg, #92400e, #d97706);"><h3 class="text-white font-semibold">5. Présentation</h3><span class="text-white text-sm font-bold">/ 3</span></div>
            <div class="p-5">
                <p class="text-xs text-gray-500 mb-3">{{ $autoScores['presentation']['details'] }}</p>
                <div class="flex items-center gap-4">
                    <input type="number" name="presentation_score" value="{{ old('presentation_score', $evaluation->presentation_score) }}" min="0" max="3" step="0.5" required class="w-24 px-3 py-2 rounded-lg border-2 border-amber-300 text-center text-lg font-bold criterion-score" data-max="3">
                    <span class="text-sm text-gray-400">/ 3</span>
                    <span class="text-xs text-gray-400 italic">✏️ Vous pouvez ajuster</span>
                </div>
            </div>
        </div>

        {{-- Total --}}
        <div class="bg-white rounded-xl shadow-sm border-2 overflow-hidden mb-4" style="border-color: #1B3C35;">
            <div class="px-5 py-4 text-center" style="background: linear-gradient(135deg, #1B3C35, #3D7A6A);">
                <h2 class="text-white font-bold">NOTE TOTALE</h2>
                <span id="grand-total" class="text-5xl font-black text-white">{{ $evaluation->total_score }}</span><span class="text-2xl text-white/70"> / 20</span>
                <div id="grade-badge" class="mt-2 inline-block px-4 py-1 rounded-full text-sm font-bold bg-white/20 text-white"></div>
            </div>
            <div id="justification-alert" class="{{ $evaluation->total_score > 16 ? '' : 'hidden' }} p-4 bg-red-50 border-b border-red-200">
                <p class="text-sm font-bold text-red-700">⚠️ Note > 16/20 : rapport justificatif obligatoire</p>
            </div>
            <div class="p-5 space-y-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Appréciation</label><textarea name="appreciation" rows="3" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm">{{ old('appreciation', $evaluation->appreciation) }}</textarea></div>
                <div id="justification-field" class="{{ $evaluation->total_score > 16 ? '' : 'hidden' }}"><label class="block text-sm font-medium text-red-700 mb-1">Rapport justificatif *</label><textarea name="justification_report" rows="3" class="w-full px-3 py-2 rounded-lg border-2 border-red-300 text-sm">{{ old('justification_report', $evaluation->justification_report) }}</textarea></div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.bts-evaluations.show', $evaluation) }}" class="px-5 py-2.5 rounded-lg text-sm text-gray-700 bg-gray-100 hover:bg-gray-200">Annuler</a>
            <button type="submit" class="px-6 py-2.5 rounded-lg text-sm font-bold text-white" style="background: linear-gradient(135deg, #1B3C35, #3D7A6A);">💾 Enregistrer</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('btsForm');
    function updateTotals() {
        const a = parseFloat(form.querySelector('[name="assiduity_score"]').value) || 0;
        const e = parseFloat(form.querySelector('[name="execution_score"]').value) || 0;
        const i = parseFloat(form.querySelector('[name="initiative_score"]').value) || 0;
        const p = parseFloat(form.querySelector('[name="presentation_score"]').value) || 0;
        const r = form.querySelectorAll('.subcriteria-input[data-group="relations"]:checked').length;
        document.getElementById('relations-total').textContent = r;
        const total = Math.round((a + r + e + i + p) * 10) / 10;
        document.getElementById('grand-total').textContent = total;
        const badge = document.getElementById('grade-badge');
        if (total >= 18) badge.textContent = 'Excellent';
        else if (total >= 16) badge.textContent = 'Très Bien';
        else if (total >= 14) badge.textContent = 'Bien';
        else if (total >= 12) badge.textContent = 'Assez Bien';
        else if (total >= 10) badge.textContent = 'Passable';
        else badge.textContent = 'Insuffisant';
        document.getElementById('justification-alert').classList.toggle('hidden', total <= 16);
        document.getElementById('justification-field').classList.toggle('hidden', total <= 16);
    }
    form.querySelectorAll('.criterion-score').forEach(el => el.addEventListener('input', updateTotals));
    form.querySelectorAll('.subcriteria-input').forEach(el => el.addEventListener('change', updateTotals));
    updateTotals();
});
</script>
</x-layouts.admin>
