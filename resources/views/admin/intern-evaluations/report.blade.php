<x-layouts.admin>
    <div class="space-y-6">
        <!-- Header avec gradient -->
        <div class="relative overflow-hidden bg-gradient-to-r from-[#1B3C35] via-[#2D5A4E] to-[#2D5A4E] rounded-2xl shadow-xl">
            <div class="absolute inset-0 bg-black/10"></div>
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            
            <div class="relative p-6 md:p-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center border border-white/30">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl md:text-3xl font-bold text-white">Rapport des Évaluations</h1>
                            <p class="text-white/80 mt-1">Historique complet avec filtres avancés</p>
                        </div>
                    </div>
                    
                    <!-- Stats rapides -->
                    <div class="flex items-center gap-3">
                        <div class="px-4 py-2 bg-white/20 backdrop-blur-sm rounded-xl border border-white/30">
                            <p class="text-white/70 text-xs">Total évaluations</p>
                            <p class="text-white font-bold text-xl">{{ $evaluations->total() }}</p>
                        </div>
                        <a href="{{ route('admin.intern-evaluations.index') }}" 
                           class="inline-flex items-center px-5 py-2.5 bg-white text-[#163530] font-semibold rounded-xl hover:bg-[#F0F5F3] transition-all shadow-lg">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-gray-50 to-slate-50 px-5 py-3 border-b border-gray-100">
                <h3 class="font-semibold text-gray-700 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filtres de recherche
                </h3>
            </div>
            <form action="{{ route('admin.intern-evaluations.report') }}" method="GET" class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Département</label>
                        <select name="department_id" class="w-full rounded-xl border-gray-300 focus:border-[#2D5A4E] focus:ring-[#2D5A4E] text-sm">
                            <option value="">Tous les départements</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tuteur</label>
                        <select name="tutor_id" class="w-full rounded-xl border-gray-300 focus:border-[#2D5A4E] focus:ring-[#2D5A4E] text-sm">
                            <option value="">Tous les tuteurs</option>
                            @foreach($tutors as $tutor)
                                <option value="{{ $tutor->id }}" {{ request('tutor_id') == $tutor->id ? 'selected' : '' }}>{{ $tutor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date début</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" 
                               class="w-full rounded-xl border-gray-300 focus:border-[#2D5A4E] focus:ring-[#2D5A4E] text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date fin</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" 
                               class="w-full rounded-xl border-gray-300 focus:border-[#2D5A4E] focus:ring-[#2D5A4E] text-sm">
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 px-4 py-2.5 bg-[#2D5A4E] text-white font-medium rounded-xl hover:bg-[#1B3C35] transition-all shadow-lg shadow-[#1B3C35]/30">
                            <span class="flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Filtrer
                            </span>
                        </button>
                        @if(request()->hasAny(['department_id', 'tutor_id', 'date_from', 'date_to']))
                            <a href="{{ route('admin.intern-evaluations.report') }}" class="px-4 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-gray-50 to-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Stagiaire</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Semaine</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <span class="inline-flex items-center gap-1" title="Discipline">
                                    <span class="w-2 h-2 rounded-full bg-[#2D5A4E]"></span>
                                    Disc.
                                </span>
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <span class="inline-flex items-center gap-1" title="Comportement">
                                    <span class="w-2 h-2 rounded-full bg-[#2D5A4E]"></span>
                                    Comp.
                                </span>
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <span class="inline-flex items-center gap-1" title="Compétences Techniques">
                                    <span class="w-2 h-2 rounded-full bg-[#C8A96E]"></span>
                                    Tech.
                                </span>
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <span class="inline-flex items-center gap-1" title="Communication">
                                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                    Com.
                                </span>
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tuteur</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($evaluations as $evaluation)
                            @php
                                $gradeConfig = [
                                    'A' => ['bg' => 'bg-[#E8F0ED]', 'text' => 'text-[#163530]', 'gradient' => 'from-[#3D7A6A] to-[#2D5A4E]'],
                                    'B' => ['bg' => 'bg-[#E8F0ED]', 'text' => 'text-[#163530]', 'gradient' => 'from-[#3D7A6A] to-[#2D5A4E]'],
                                    'C' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'gradient' => 'from-[#D4BC8B] to-[#C8A96E]'],
                                    'D' => ['bg' => 'bg-[#FAF3E8]', 'text' => 'text-[#B8955A]', 'gradient' => 'from-[#D4BC8B] to-[#2D5A4E]'],
                                    'E' => ['bg' => 'bg-[#E8F0ED]', 'text' => 'text-[#163530]', 'gradient' => 'from-[#2D5A4E] to-[#2D5A4E]'],
                                ];
                                $config = $gradeConfig[$evaluation->grade_letter] ?? $gradeConfig['E'];
                            @endphp
                            <tr class="hover:bg-[#F0F5F3]/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-gradient-to-br {{ $config['gradient'] }} rounded-xl flex items-center justify-center text-white font-bold text-sm shadow-md">
                                            {{ strtoupper(substr($evaluation->intern->name ?? 'S', 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $evaluation->intern->name ?? 'N/A' }}</p>
                                            <p class="text-xs text-gray-500">{{ $evaluation->intern->department->name ?? '' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-100 text-slate-700">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        {{ $evaluation->week_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-[#E8F0ED] text-[#163530] font-bold text-sm">
                                        {{ $evaluation->discipline_score }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-[#E8F0ED] text-[#163530] font-bold text-sm">
                                        {{ $evaluation->behavior_score }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-[#FAF3E8] text-[#B8955A] font-bold text-sm">
                                        {{ $evaluation->skills_score }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-amber-100 text-amber-700 font-bold text-sm">
                                        {{ $evaluation->communication_score }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-sm font-bold {{ $config['bg'] }} {{ $config['text'] }}">
                                        {{ $evaluation->total_score }}/10
                                        <span class="text-xs opacity-75">({{ $evaluation->grade_letter }})</span>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($evaluation->tutor)
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 bg-gradient-to-br from-[#3D7A6A] to-[#3D7A6A] rounded-lg flex items-center justify-center text-white font-bold text-xs">
                                                {{ strtoupper(substr($evaluation->tutor->name, 0, 1)) }}
                                            </div>
                                            <span class="text-sm font-medium text-gray-700">{{ $evaluation->tutor->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400">N/A</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-20 h-20 rounded-full bg-gradient-to-br from-[#E8F0ED] to-[#E8F0ED] flex items-center justify-center mb-4">
                                            <svg class="w-10 h-10 text-[#3D7A6A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                        </div>
                                        <p class="text-gray-500 font-medium">Aucune évaluation trouvée</p>
                                        <p class="text-gray-400 text-sm mt-1">Modifiez vos filtres ou attendez de nouvelles évaluations</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($evaluations->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $evaluations->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
