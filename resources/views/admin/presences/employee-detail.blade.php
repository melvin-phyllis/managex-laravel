<x-layouts.admin>
    <div class="space-y-6">
        <!-- Breadcrumbs -->
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                        <a href="{{ route('admin.presences.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">Présences</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Détails ({{ $user->name }})</span>
                    </div>
                </li>
            </ol>
        </nav>
        {{-- Header avec navigation --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.presences.index') }}" 
                   class="p-2 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <x-icon name="arrow-left" class="w-5 h-5 text-gray-600" />
                </a>
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-indigo-500/30">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                        <p class="text-gray-500 flex items-center gap-2">
                            @if($user->department && is_object($user->department))
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">
                                    {{ $user->department->name ?? 'N/A' }}
                                </span>
                                <span class="text-gray-400">•</span>
                            @endif
                            <span>{{ is_string($user->position) ? $user->position : 'Employé' }}</span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3" x-data="{ showCustom: '{{ $period }}' === 'custom' }">
                {{-- Sélecteur de période --}}
                <form method="GET" action="{{ route('admin.presences.employee-show', $user->id) }}" 
                      id="filterForm" class="flex flex-wrap items-center gap-3">
                    <select name="period" 
                            @change="showCustom = ($event.target.value === 'custom'); if(!showCustom) $el.form.submit()"
                            class="rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="week" {{ $period === 'week' ? 'selected' : '' }}>Cette semaine</option>
                        <option value="month" {{ $period === 'month' ? 'selected' : '' }}>Ce mois</option>
                        <option value="quarter" {{ $period === 'quarter' ? 'selected' : '' }}>Ce trimestre</option>
                        <option value="year" {{ $period === 'year' ? 'selected' : '' }}>Cette année</option>
                        <option value="custom" {{ $period === 'custom' ? 'selected' : '' }}>Personnalisée</option>
                    </select>
                    
                    {{-- Date range picker (visible only for custom) --}}
                    <div x-show="showCustom" x-transition class="flex items-center gap-2">
                        <input type="date" name="start_date" 
                               value="{{ $startDate->format('Y-m-d') }}"
                               class="rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-gray-400">→</span>
                        <input type="date" name="end_date" 
                               value="{{ $endDate->format('Y-m-d') }}"
                               class="rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <button type="submit" 
                                class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                            Appliquer
                        </button>
                    </div>
                </form>
                
                {{-- Période affichée --}}
                <div x-show="!showCustom" class="flex items-center gap-2 px-3 py-1.5 bg-gray-100 rounded-lg">
                    <span class="text-xs text-gray-500">Période:</span>
                    <span class="text-sm font-medium text-gray-700">
                        {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Statistiques principales --}}
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
            {{-- Taux de présence --}}
            <div class="col-span-2 lg:col-span-1 bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm font-medium text-gray-500">Taux de présence</span>
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center">
                        <x-icon name="check-circle" class="w-4 h-4 text-white" />
                    </div>
                </div>
                <div class="flex items-end gap-3">
                    <div class="relative w-20 h-20">
                        <svg class="w-20 h-20 transform -rotate-90" viewBox="0 0 36 36">
                            <circle cx="18" cy="18" r="16" fill="none" class="stroke-gray-200" stroke-width="3"></circle>
                            <circle cx="18" cy="18" r="16" fill="none" 
                                class="{{ $stats['attendance_rate'] >= 95 ? 'stroke-green-500' : ($stats['attendance_rate'] >= 80 ? 'stroke-amber-500' : 'stroke-red-500') }}" 
                                stroke-width="3" 
                                stroke-dasharray="{{ $stats['attendance_rate'] }}, 100"
                                stroke-linecap="round"></circle>
                        </svg>
                        <span class="absolute inset-0 flex items-center justify-center text-lg font-bold {{ $stats['attendance_rate'] >= 95 ? 'text-green-600' : ($stats['attendance_rate'] >= 80 ? 'text-amber-600' : 'text-red-600') }}">
                            {{ $stats['attendance_rate'] }}%
                        </span>
                    </div>
                    <div class="text-sm text-gray-500 mb-2">
                        {{ $stats['total_present'] }}/{{ $stats['work_days'] }} jours
                    </div>
                </div>
            </div>

            {{-- Jours présent --}}
            <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-6 text-white">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-emerald-100 text-sm font-medium">Jours Présent</span>
                    <x-icon name="check" class="w-5 h-5 text-emerald-200" />
                </div>
                <p class="text-4xl font-bold">{{ $stats['total_present'] }}</p>
                <p class="text-emerald-200 text-sm mt-1">sur {{ $stats['work_days'] }} jours ouvrés</p>
            </div>

            {{-- Retards --}}
            <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl p-6 text-white">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-amber-100 text-sm font-medium">Retards</span>
                    <x-icon name="clock" class="w-5 h-5 text-amber-200" />
                </div>
                <p class="text-4xl font-bold">{{ $stats['total_late'] }}</p>
                <p class="text-amber-200 text-sm mt-1">jours en retard</p>
            </div>

            {{-- Absences --}}
            <div class="bg-gradient-to-br from-rose-500 to-pink-600 rounded-2xl p-6 text-white">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-rose-100 text-sm font-medium">Absences</span>
                    <x-icon name="x-circle" class="w-5 h-5 text-rose-200" />
                </div>
                <p class="text-4xl font-bold">{{ $stats['total_absent'] }}</p>
                <p class="text-rose-200 text-sm mt-1">jours d'absence</p>
            </div>

            {{-- Heures de travail --}}
            <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl p-6 text-white">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-blue-100 text-sm font-medium">Heures travaillées</span>
                    <x-icon name="briefcase" class="w-5 h-5 text-blue-200" />
                </div>
                <p class="text-4xl font-bold">{{ floor($stats['total_work_minutes'] / 60) }}h</p>
                <p class="text-blue-200 text-sm mt-1">{{ $stats['total_work_minutes'] % 60 }} min</p>
            </div>
        </div>

        {{-- Cartes détaillées --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Cumul des retards --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center">
                        <x-icon name="clock" class="w-4 h-4 text-amber-600" />
                    </div>
                    Cumul des retards
                </h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Total cumulé</span>
                        <span class="text-2xl font-bold text-amber-600">
                            {{ floor($stats['cumulative_late_minutes'] / 60) }}h {{ $stats['cumulative_late_minutes'] % 60 }}min
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Moyenne par retard</span>
                        <span class="text-lg font-medium text-gray-900">
                            {{ $stats['average_late_minutes'] }} min
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Nombre de retards</span>
                        <span class="text-lg font-medium text-gray-900">
                            {{ $stats['total_late'] }} fois
                        </span>
                    </div>
                </div>
            </div>

            {{-- Heures supplémentaires --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center">
                        <x-icon name="trending-up" class="w-4 h-4 text-green-600" />
                    </div>
                    Heures supplémentaires
                </h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Total</span>
                        <span class="text-2xl font-bold text-green-600">
                            {{ floor($stats['total_overtime_minutes'] / 60) }}h {{ $stats['total_overtime_minutes'] % 60 }}min
                        </span>
                    </div>
                    @if($stats['total_overtime_minutes'] == 0)
                        <p class="text-gray-400 text-sm italic">Aucune heure supplémentaire enregistrée</p>
                    @endif
                </div>
            </div>

            {{-- Résumé --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                        <x-icon name="bar-chart-2" class="w-4 h-4 text-indigo-600" />
                    </div>
                    Bilan
                </h3>
                <div class="space-y-3">
                    @if($stats['attendance_rate'] >= 95 && $stats['total_late'] <= 2)
                        <div class="flex items-center gap-3 p-4 bg-green-50 rounded-xl">
                            <span class="text-3xl">🏆</span>
                            <div>
                                <p class="font-bold text-green-600">Excellent !</p>
                                <p class="text-gray-500 text-sm">Performance exemplaire</p>
                            </div>
                        </div>
                    @elseif($stats['attendance_rate'] >= 80)
                        <div class="flex items-center gap-3 p-4 bg-amber-50 rounded-xl">
                            <span class="text-3xl">👍</span>
                            <div>
                                <p class="font-bold text-amber-600">Satisfaisant</p>
                                <p class="text-gray-500 text-sm">Quelques améliorations possibles</p>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center gap-3 p-4 bg-red-50 rounded-xl">
                            <span class="text-3xl">⚠️</span>
                            <div>
                                <p class="font-bold text-red-600">À surveiller</p>
                                <p class="text-gray-500 text-sm">Nécessite une attention particulière</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Historique complet --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-4">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <x-icon name="calendar" class="w-5 h-5 text-indigo-500" />
                    Historique des présences 
                    <span class="text-sm font-normal text-gray-500">({{ $pagination->total() }} jours)</span>
                </h3>
                <div class="flex items-center gap-4">
                    {{-- Sélecteur entrées par page --}}
                    <form method="GET" action="{{ route('admin.presences.employee-show', $user->id) }}" class="flex items-center gap-2">
                        <input type="hidden" name="period" value="{{ $period }}">
                        @if($period === 'custom')
                            <input type="hidden" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
                            <input type="hidden" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
                        @endif
                        <label class="text-sm text-gray-500">Afficher</label>
                        <select name="per_page" onchange="this.form.submit()" 
                                class="rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach([10, 20, 30, 40, 50, 100, 150, 200] as $option)
                                <option value="{{ $option }}" {{ $pagination->perPage() == $option ? 'selected' : '' }}>{{ $option }}</option>
                            @endforeach
                        </select>
                        <span class="text-sm text-gray-500">entrées</span>
                    </form>
                    <span class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-lg">
                        Page {{ $pagination->currentPage() }} / {{ $pagination->lastPage() }}
                    </span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left py-3 px-6 text-xs font-semibold text-gray-500 uppercase">Date</th>
                            <th class="text-left py-3 px-6 text-xs font-semibold text-gray-500 uppercase">Jour</th>
                            <th class="text-center py-3 px-6 text-xs font-semibold text-gray-500 uppercase">Arrivée</th>
                            <th class="text-center py-3 px-6 text-xs font-semibold text-gray-500 uppercase">Départ</th>
                            <th class="text-center py-3 px-6 text-xs font-semibold text-gray-500 uppercase">Heures</th>
                            <th class="text-center py-3 px-6 text-xs font-semibold text-gray-500 uppercase">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($allPresences as $presence)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-6 font-medium text-gray-900">{{ $presence['date'] }}</td>
                            <td class="py-3 px-6 text-gray-600">{{ $presence['day'] }}</td>
                            <td class="py-3 px-6 text-center font-mono">{{ $presence['check_in'] }}</td>
                            <td class="py-3 px-6 text-center font-mono">{{ $presence['check_out'] }}</td>
                            <td class="py-3 px-6 text-center font-medium">{{ $presence['work_hours'] }}h</td>
                            <td class="py-3 px-6 text-center">
                                @if($presence['is_late'])
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">
                                        ⏱ +{{ $presence['late_minutes'] }}min
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                        ✓ À l'heure
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-gray-500">
                                <p class="text-4xl mb-3">📭</p>
                                <p>Aucune présence enregistrée sur cette période</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination --}}
            @if($pagination->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
                <div class="text-sm text-gray-500">
                    Affichage de {{ $pagination->firstItem() }} à {{ $pagination->lastItem() }} sur {{ $pagination->total() }} entrées
                </div>
                <div class="flex items-center gap-2">
                    {{-- Previous --}}
                    @if($pagination->onFirstPage())
                        <span class="px-3 py-1.5 text-sm text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">← Précédent</span>
                    @else
                        <a href="{{ $pagination->previousPageUrl() }}" class="px-3 py-1.5 text-sm text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">← Précédent</a>
                    @endif
                    
                    {{-- Page numbers --}}
                    @foreach($pagination->getUrlRange(max(1, $pagination->currentPage() - 2), min($pagination->lastPage(), $pagination->currentPage() + 2)) as $page => $url)
                        @if($page == $pagination->currentPage())
                            <span class="px-3 py-1.5 text-sm font-bold text-white bg-indigo-600 rounded-lg">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="px-3 py-1.5 text-sm text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">{{ $page }}</a>
                        @endif
                    @endforeach
                    
                    {{-- Next --}}
                    @if($pagination->hasMorePages())
                        <a href="{{ $pagination->nextPageUrl() }}" class="px-3 py-1.5 text-sm text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">Suivant →</a>
                    @else
                        <span class="px-3 py-1.5 text-sm text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">Suivant →</span>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
