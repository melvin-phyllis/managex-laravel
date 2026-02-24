<x-layouts.admin>
<div class="space-y-6 max-w-4xl mx-auto">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.bts-evaluations.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Retour aux fiches BTS
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Fiche d'Évaluation BTS</h1>
            <p class="text-sm text-gray-500 mt-1">Barème officiel du Ministère — Total sur 20 points</p>
        </div>
    </div>

    @if($errors->any())
        <div class="border-l-4 border-red-500 bg-red-50 p-4 rounded-r-lg">
            <ul class="text-sm text-red-700 list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.bts-evaluations.store', $intern) }}" method="POST" id="btsForm">
        @csrf

        {{-- Informations stagiaire --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
            <div class="px-5 py-3" style="background: linear-gradient(135deg, #1B3C35, #3D7A6A);">
                <h2 class="text-white font-semibold">📝 Informations du stagiaire</h2>
            </div>
            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Nom et prénoms</label>
                    <input type="text" value="{{ $intern->name }}" disabled class="w-full px-3 py-2 rounded-lg bg-gray-50 border border-gray-200 text-sm text-gray-700">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Entreprise</label>
                    <input type="text" value="Ya Consulting" disabled class="w-full px-3 py-2 rounded-lg bg-gray-50 border border-gray-200 text-sm text-gray-700">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">N° BTS</label>
                    <input type="text" name="intern_bts_number" value="{{ old('intern_bts_number') }}" placeholder="Ex: BTS-2025-001"
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Filière</label>
                    <input type="text" name="intern_field" value="{{ old('intern_field') }}" placeholder="Ex: Informatique de gestion"
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Date de début de stage</label>
                    <input type="date" name="stage_start_date" value="{{ old('stage_start_date', $start->format('Y-m-d')) }}" required
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Date de fin de stage</label>
                    <input type="date" name="stage_end_date" value="{{ old('stage_end_date', $end->format('Y-m-d')) }}" required
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
            </div>
        </div>

        {{-- CRITÈRE 1: Assiduité /3 (AUTO) --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-4">
            <div class="px-5 py-3 flex items-center justify-between" style="background: linear-gradient(135deg, #065f46, #059669);">
                <h3 class="text-white font-semibold">1. Assiduité et ponctualité</h3>
                <span class="text-white text-sm font-bold">/ 3 pts</span>
            </div>
            <div class="p-5">
                <div class="flex items-center gap-2 mb-3">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-700">🤖 AUTO</span>
                    <span class="text-xs text-gray-500">Calculé depuis le module Présences</span>
                </div>
                <p class="text-xs text-gray-500 mb-3">{{ $autoScores['assiduity']['details'] }}</p>
                <div class="flex items-center gap-4">
                    <label class="text-sm font-medium text-gray-700">Note proposée :</label>
                    <input type="number" name="assiduity_score" value="{{ old('assiduity_score', $autoScores['assiduity']['score']) }}"
                           min="0" max="3" step="0.5" required
                           class="w-24 px-3 py-2 rounded-lg border-2 border-emerald-300 text-center text-lg font-bold focus:ring-2 focus:ring-emerald-500 criterion-score"
                           data-max="3">
                    <span class="text-sm text-gray-400">/ 3</span>
                    <span class="text-xs text-gray-400 italic">✏️ Vous pouvez ajuster</span>
                </div>
                <input type="hidden" name="assiduity_details" value="{{ $autoScores['assiduity']['details'] }}">
            </div>
        </div>

        {{-- CRITÈRE 2: Relations humaines /4 (MANUELLE) --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-4">
            <div class="px-5 py-3 flex items-center justify-between" style="background: linear-gradient(135deg, #1e40af, #3b82f6);">
                <h3 class="text-white font-semibold">2. Relations humaines et professionnelles</h3>
                <span class="text-white text-sm font-bold">/ 4 pts</span>
            </div>
            <div class="p-5">
                <div class="flex items-center gap-2 mb-3">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-orange-100 text-orange-700">✋ MANUELLE</span>
                    <span class="text-xs text-gray-500">Cochez les sous-critères (1 pt chacun)</span>
                </div>
                <div class="space-y-3">
                    @foreach(\App\Models\BtsEvaluation::RELATIONS_SUBCRITERIA as $field => $label)
                    <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-100 hover:bg-gray-50 cursor-pointer transition subcriteria-check" data-group="relations">
                        <input type="hidden" name="{{ $field }}" value="0">
                        <input type="checkbox" name="{{ $field }}" value="1" {{ old($field) ? 'checked' : '' }}
                               class="w-5 h-5 rounded text-emerald-600 focus:ring-emerald-500 subcriteria-input" data-group="relations">
                        <span class="text-sm text-gray-700">{{ $label }}</span>
                        <span class="ml-auto text-xs font-bold text-gray-400">+1 pt</span>
                    </label>
                    @endforeach
                </div>
                <div class="mt-3 text-right">
                    <span class="text-sm text-gray-500">Score : </span>
                    <span id="relations-total" class="text-lg font-bold" style="color: #1B3C35;">0</span>
                    <span class="text-sm text-gray-400"> / 4</span>
                </div>
            </div>
        </div>

        {{-- CRITÈRE 3: Exécution /6 (AUTO) --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-4">
            <div class="px-5 py-3 flex items-center justify-between" style="background: linear-gradient(135deg, #7c2d12, #ea580c);">
                <h3 class="text-white font-semibold">3. Intelligence d'exécution des tâches</h3>
                <span class="text-white text-sm font-bold">/ 6 pts</span>
            </div>
            <div class="p-5">
                <div class="flex items-center gap-2 mb-3">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-700">🤖 AUTO</span>
                    <span class="text-xs text-gray-500">Calculé depuis le module Tâches</span>
                </div>
                <p class="text-xs text-gray-500 mb-3">{{ $autoScores['execution']['details'] }}</p>
                <div class="flex items-center gap-4">
                    <label class="text-sm font-medium text-gray-700">Note proposée :</label>
                    <input type="number" name="execution_score" value="{{ old('execution_score', $autoScores['execution']['score']) }}"
                           min="0" max="6" step="0.5" required
                           class="w-24 px-3 py-2 rounded-lg border-2 border-orange-300 text-center text-lg font-bold focus:ring-2 focus:ring-orange-500 criterion-score"
                           data-max="6">
                    <span class="text-sm text-gray-400">/ 6</span>
                    <span class="text-xs text-gray-400 italic">✏️ Vous pouvez ajuster</span>
                </div>
                <input type="hidden" name="execution_details" value="{{ $autoScores['execution']['details'] }}">
            </div>
        </div>

        {{-- CRITÈRE 4: Initiative /4 (SEMI-AUTO) --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-4">
            <div class="px-5 py-3 flex items-center justify-between" style="background: linear-gradient(135deg, #5b21b6, #8b5cf6);">
                <h3 class="text-white font-semibold">4. Esprit d'initiative</h3>
                <span class="text-white text-sm font-bold">/ 4 pts</span>
            </div>
            <div class="p-5">
                <div class="flex items-center gap-2 mb-3">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-700">🔄 SEMI-AUTO</span>
                    <span class="text-xs text-gray-500">Suggestion basée sur les tâches, ajustable</span>
                </div>
                <p class="text-xs text-gray-500 mb-3">{{ $autoScores['initiative']['details'] }}</p>
                <div class="flex items-center gap-4">
                    <label class="text-sm font-medium text-gray-700">Note proposée :</label>
                    <input type="number" name="initiative_score" value="{{ old('initiative_score', $autoScores['initiative']['score']) }}"
                           min="0" max="4" step="0.5" required
                           class="w-24 px-3 py-2 rounded-lg border-2 border-purple-300 text-center text-lg font-bold focus:ring-2 focus:ring-purple-500 criterion-score"
                           data-max="4">
                    <span class="text-sm text-gray-400">/ 4</span>
                    <span class="text-xs text-gray-400 italic">✏️ Vous pouvez ajuster</span>
                </div>
                <input type="hidden" name="initiative_details" value="{{ $autoScores['initiative']['details'] }}">
            </div>
        </div>

        {{-- CRITÈRE 5: Présentation /3 (AUTO depuis notes tâches) --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-4">
            <div class="px-5 py-3 flex items-center justify-between" style="background: linear-gradient(135deg, #92400e, #d97706);">
                <h3 class="text-white font-semibold">5. Présentation</h3>
                <span class="text-white text-sm font-bold">/ 3 pts</span>
            </div>
            <div class="p-5">
                <div class="flex items-center gap-2 mb-3">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-700">🤖 AUTO</span>
                    <span class="text-xs text-gray-500">Calculé depuis les notes des tâches (rating moyen)</span>
                </div>
                <p class="text-xs text-gray-500 mb-3">{{ $autoScores['presentation']['details'] }}</p>
                <div class="flex items-center gap-4">
                    <label class="text-sm font-medium text-gray-700">Note proposée :</label>
                    <input type="number" name="presentation_score" value="{{ old('presentation_score', $autoScores['presentation']['score']) }}"
                           min="0" max="3" step="0.5" required
                           class="w-24 px-3 py-2 rounded-lg border-2 border-amber-300 text-center text-lg font-bold focus:ring-2 focus:ring-amber-500 criterion-score"
                           data-max="3">
                    <span class="text-sm text-gray-400">/ 3</span>
                    <span class="text-xs text-gray-400 italic">✏️ Vous pouvez ajuster</span>
                </div>
            </div>
        </div>

        {{-- TOTAL + Appréciation --}}
        <div class="bg-white rounded-xl shadow-sm border-2 overflow-hidden mb-4" style="border-color: #1B3C35;">
            <div class="px-5 py-4 text-center" style="background: linear-gradient(135deg, #1B3C35, #3D7A6A);">
                <h2 class="text-white font-bold text-lg">NOTE TOTALE</h2>
                <div class="mt-2">
                    <span id="grand-total" class="text-5xl font-black text-white">0</span>
                    <span class="text-2xl text-white/70"> / 20</span>
                </div>
                <div id="grade-badge" class="mt-2 inline-block px-4 py-1 rounded-full text-sm font-bold bg-white/20 text-white"></div>
            </div>

            {{-- Alert for > 16 --}}
            <div id="justification-alert" class="hidden p-4 bg-red-50 border-b border-red-200">
                <div class="flex items-start gap-3">
                    <span class="text-red-500 text-xl">⚠️</span>
                    <div>
                        <p class="text-sm font-bold text-red-700">Attention : Note supérieure à 16/20</p>
                        <p class="text-xs text-red-600 mt-1">Conformément au barème BTS, toute note supérieure à 16/20 doit faire l'objet d'un rapport justificatif du maître de stage sous peine d'invalidité par le jury.</p>
                    </div>
                </div>
            </div>

            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Appréciation du maître de stage</label>
                    <textarea name="appreciation" rows="4" placeholder="Saisissez votre appréciation générale..."
                              class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500">{{ old('appreciation') }}</textarea>
                </div>
                <div id="justification-field" class="hidden">
                    <label class="block text-sm font-medium text-red-700 mb-1">📋 Rapport justificatif (obligatoire si note > 16/20) *</label>
                    <textarea name="justification_report" rows="4" placeholder="Justifiez pourquoi le stagiaire mérite cette note exceptionnelle..."
                              class="w-full px-3 py-2 rounded-lg border-2 border-red-300 text-sm focus:ring-2 focus:ring-red-500">{{ old('justification_report') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Buttons --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.bts-evaluations.index') }}" class="px-5 py-2.5 rounded-lg text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 transition">
                Annuler
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-lg text-sm font-bold text-white transition-all hover:opacity-90"
                    style="background: linear-gradient(135deg, #1B3C35, #3D7A6A);">
                💾 Enregistrer la fiche BTS
            </button>
        </div>
    </form>
</div>

{{-- JavaScript for live score calculation --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('btsForm');

    function updateTotals() {
        // Auto scores
        const assiduity = parseFloat(form.querySelector('[name="assiduity_score"]').value) || 0;
        const execution = parseFloat(form.querySelector('[name="execution_score"]').value) || 0;
        const initiative = parseFloat(form.querySelector('[name="initiative_score"]').value) || 0;
        const presentation = parseFloat(form.querySelector('[name="presentation_score"]').value) || 0;

        // Sub-criteria: relations
        const relChecks = form.querySelectorAll('.subcriteria-input[data-group="relations"]:checked');
        const relTotal = relChecks.length;
        document.getElementById('relations-total').textContent = relTotal;

        // Grand total
        const total = Math.round((assiduity + relTotal + execution + initiative + presentation) * 10) / 10;
        document.getElementById('grand-total').textContent = total;

        // Grade badge
        const badge = document.getElementById('grade-badge');
        if (total >= 18) { badge.textContent = 'Excellent'; badge.style.background = 'rgba(22,163,74,0.3)'; }
        else if (total >= 16) { badge.textContent = 'Très Bien'; badge.style.background = 'rgba(5,150,105,0.3)'; }
        else if (total >= 14) { badge.textContent = 'Bien'; badge.style.background = 'rgba(37,99,235,0.3)'; }
        else if (total >= 12) { badge.textContent = 'Assez Bien'; badge.style.background = 'rgba(234,179,8,0.3)'; }
        else if (total >= 10) { badge.textContent = 'Passable'; badge.style.background = 'rgba(249,115,22,0.3)'; }
        else { badge.textContent = 'Insuffisant'; badge.style.background = 'rgba(220,38,38,0.3)'; }

        // Justification alert
        const justAlert = document.getElementById('justification-alert');
        const justField = document.getElementById('justification-field');
        if (total > 16) {
            justAlert.classList.remove('hidden');
            justField.classList.remove('hidden');
        } else {
            justAlert.classList.add('hidden');
            justField.classList.add('hidden');
        }
    }

    // Listen to all score inputs and checkboxes
    form.querySelectorAll('.criterion-score').forEach(input => input.addEventListener('input', updateTotals));
    form.querySelectorAll('.subcriteria-input').forEach(cb => cb.addEventListener('change', updateTotals));

    // Initial calculation
    updateTotals();
});
</script>
</x-layouts.admin>
