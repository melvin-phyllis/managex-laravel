<x-layouts.admin>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Évaluations des performances</h1>
                <p class="text-sm text-gray-500 mt-1">Évaluez vos employés CDI/CDD pour calculer leur salaire mensuel</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.employee-evaluations.bulk-create', ['month' => $month, 'year' => $year]) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Évaluation groupée
                </a>
                <a href="{{ route('admin.employee-evaluations.create', ['month' => $month, 'year' => $year]) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nouvelle évaluation
                </a>
            </div>
        </div>

        <!-- Filtre période -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <form method="GET" class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mois</label>
                    <select name="month" class="rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ (int) $month == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month((int) $m)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Année</label>
                    <select name="year" class="rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach(range(now()->year - 2, now()->year + 1) as $y)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-colors">
                    Filtrer
                </button>
            </form>
        </div>

        <!-- Info SMIC -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-blue-900">SMIC Côte d'Ivoire : <span class="font-bold">{{ number_format($smic, 0, ',', ' ') }} FCFA</span></p>
                    <p class="text-xs text-blue-700">Salaire = Note (max 5,5) × SMIC | Minimum garanti = SMIC</p>
                </div>
            </div>
        </div>

        <!-- Employés en attente d'évaluation -->
        @if($pendingEmployees->isNotEmpty())
        <div class="bg-amber-50 rounded-xl border border-amber-200 p-4">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-amber-900">{{ $pendingEmployees->count() }} employé(s) en attente d'évaluation ce mois</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach($pendingEmployees->take(5) as $emp)
                            <a href="{{ route('admin.employee-evaluations.create', ['user_id' => $emp->id, 'month' => $month, 'year' => $year]) }}"
                               class="inline-flex items-center gap-1 px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-sm hover:bg-amber-200 transition-colors">
                                {{ $emp->name }}
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </a>
                        @endforeach
                        @if($pendingEmployees->count() > 5)
                            <span class="inline-flex items-center px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-sm">
                                +{{ $pendingEmployees->count() - 5 }} autres
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Liste des évaluations -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-slate-50">
                <h3 class="text-lg font-semibold text-gray-900">
                    Évaluations - {{ \Carbon\Carbon::create()->month((int) $month)->translatedFormat('F') }} {{ $year }}
                </h3>
            </div>

            @if($evaluations->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <p class="text-gray-500">Aucune évaluation pour cette période</p>
                    <a href="{{ route('admin.employee-evaluations.create', ['month' => $month, 'year' => $year]) }}" 
                       class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        Créer une évaluation
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employé</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Note</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Salaire calculé</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($evaluations as $evaluation)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold">
                                                {{ strtoupper(substr($evaluation->user->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $evaluation->user->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $evaluation->user->poste ?? $evaluation->user->contract_type }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <span class="text-lg font-bold {{ $evaluation->total_score >= 4 ? 'text-green-600' : ($evaluation->total_score >= 2.5 ? 'text-yellow-600' : 'text-red-600') }}">
                                                {{ number_format($evaluation->total_score, 1) }}
                                            </span>
                                            <span class="text-gray-400">/5,5</span>
                                        </div>
                                        <div class="w-24 mx-auto mt-1 bg-gray-200 rounded-full h-1.5">
                                            <div class="h-1.5 rounded-full {{ $evaluation->total_score >= 4 ? 'bg-green-500' : ($evaluation->total_score >= 2.5 ? 'bg-yellow-500' : 'bg-red-500') }}" 
                                                 style="width: {{ $evaluation->score_percentage }}%"></div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="text-lg font-bold text-gray-900">{{ $evaluation->calculated_salary_formatted }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $evaluation->status === 'validated' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ $evaluation->status_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.employee-evaluations.show', $evaluation) }}" 
                                               class="p-2 text-gray-400 hover:text-indigo-600 rounded-lg hover:bg-indigo-50 transition-colors"
                                               title="Voir">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>
                                            @if($evaluation->canBeEdited())
                                                <a href="{{ route('admin.employee-evaluations.edit', $evaluation) }}" 
                                                   class="p-2 text-gray-400 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition-colors"
                                                   title="Modifier">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </a>
                                                <form action="{{ route('admin.employee-evaluations.validate', $evaluation) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="p-2 text-gray-400 hover:text-green-600 rounded-lg hover:bg-green-50 transition-colors"
                                                            title="Valider"
                                                            onclick="return confirm('Valider cette évaluation ?')">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $evaluations->appends(['month' => $month, 'year' => $year])->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
