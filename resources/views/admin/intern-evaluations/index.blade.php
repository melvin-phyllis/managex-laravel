<x-layouts.admin>
    <div class="space-y-6">
        <!-- Header avec gradient -->
        <div class="relative overflow-hidden rounded-2xl shadow-xl" style="background: linear-gradient(135deg, #5680E9, #84CEEB);">
            <div class="absolute inset-0 bg-black/10"></div>
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            
            <div class="relative p-6 md:p-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center border border-white/30">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M12 14l9-5-9-5-9 5 9 5z"/>
                                <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl md:text-3xl font-bold text-white">Suivi des Stagiaires</h1>
                            <p class="text-white/80 mt-1">Dashboard d'évaluation hebdomadaire des stagiaires</p>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex flex-wrap items-center gap-2">
                        @if(auth()->user()->supervisees()->interns()->exists())
                        <a href="{{ route('admin.tutor.evaluations.index') }}" 
                           class="inline-flex items-center px-4 py-2.5 bg-white font-semibold rounded-xl transition-all shadow-lg" style="color: #5680E9;" onmouseover="this.style.backgroundColor='rgba(86, 128, 233, 0.05)'" onmouseout="this.style.backgroundColor='white'">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            évaluer mes stagiaires
                        </a>
                        @endif
                        @if(($stats['pending_evaluations'] ?? 0) > 0)
                        <a href="{{ route('admin.intern-evaluations.missing') }}" 
                           class="inline-flex items-center px-4 py-2.5 bg-amber-500 text-white font-semibold rounded-xl hover:bg-amber-600 transition-all shadow-lg">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            {{ $stats['pending_evaluations'] ?? 0 }} manquantes
                        </a>
                        @endif
                        <a href="{{ route('admin.intern-evaluations.report') }}" 
                           class="inline-flex items-center px-4 py-2.5 bg-white/20 backdrop-blur-sm text-white font-semibold rounded-xl hover:bg-white/30 transition-all border border-white/30">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            Rapport
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <!-- Total Stagiaires -->
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-lg" style="background: linear-gradient(135deg, #5680E9, #5AB9EA); box-shadow: 0 10px 15px -3px rgba(86, 128, 233, 0.3);">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_interns'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500">Stagiaires</p>
                    </div>
                </div>
            </div>

            <!-- Avec tuteur -->
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-lg" style="background: linear-gradient(135deg, #5AB9EA, #84CEEB); box-shadow: 0 10px 15px -3px rgba(90, 185, 234, 0.3);">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['interns_with_supervisor'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500">Avec tuteur</p>
                    </div>
                </div>
            </div>

            <!-- évaluations cette semaine -->
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-lg" style="background: linear-gradient(135deg, #84CEEB, #5AB9EA); box-shadow: 0 10px 15px -3px rgba(132, 206, 235, 0.3);">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['evaluations_this_week'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500">Cette semaine</p>
                    </div>
                </div>
            </div>

            <!-- Score moyen -->
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-lg" style="background: linear-gradient(135deg, #5680E9, #84CEEB); box-shadow: 0 10px 15px -3px rgba(86, 128, 233, 0.3);">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['average_score'] ?? 0 }}<span class="text-base font-normal text-gray-500">/10</span></p>
                        <p class="text-xs text-gray-500">Score moyen</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Distribution des notes (Chart) + évaluations récentes -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Distribution des notes -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4" style="background: linear-gradient(135deg, #5680E9, #5AB9EA);">
                    <h3 class="text-white font-semibold flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                        </svg>
                        Distribution des notes
                    </h3>
                </div>
                <div class="p-6">
                    <div class="h-64 flex items-center justify-center">
                        <canvas id="gradeDistributionChart" class="w-full h-full"></canvas>
                    </div>
                </div>
            </div>

            <!-- évaluations récentes -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 flex items-center justify-between" style="background: linear-gradient(135deg, #5680E9, #5AB9EA);">
                    <h3 class="text-white font-semibold flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        évaluations récentes
                    </h3>
                    <span class="px-2 py-1 bg-white/20 text-white text-xs font-medium rounded-full">
                        {{ count($recentEvaluations) }} récentes
                    </span>
                </div>
                <div class="p-4">
                    <div class="space-y-3 max-h-64 overflow-y-auto pr-2">
                        @forelse($recentEvaluations as $evaluation)
                            @php
                                $gradeConfig = [
                                    'A' => [
                                        'bg' => 'rgba(90, 185, 234, 0.1)',
                                        'text' => '#5AB9EA',
                                        'gradient' => 'linear-gradient(135deg, #5AB9EA, #84CEEB)'
                                    ],
                                    'B' => [
                                        'bg' => 'rgba(132, 206, 235, 0.1)',
                                        'text' => '#84CEEB',
                                        'gradient' => 'linear-gradient(135deg, #84CEEB, #5AB9EA)'
                                    ],
                                    'C' => [
                                        'bg' => 'rgba(86, 128, 233, 0.1)',
                                        'text' => '#5680E9',
                                        'gradient' => 'linear-gradient(135deg, #5680E9, #5AB9EA)'
                                    ],
                                    'D' => [
                                        'bg' => 'rgba(136, 96, 208, 0.1)',
                                        'text' => '#8860D0',
                                        'gradient' => 'linear-gradient(135deg, #8860D0, #5680E9)'
                                    ],
                                    'E' => [
                                        'bg' => 'rgba(107, 114, 128, 0.1)',
                                        'text' => '#6B7280',
                                        'gradient' => 'linear-gradient(135deg, #6B7280, #4B5563)'
                                    ],
                                ];
                                $config = $gradeConfig[$evaluation->grade_letter] ?? $gradeConfig['E'];
                            @endphp
                            <div class="flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 rounded-xl transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold text-sm shadow-md" style="background: {{ $config['gradient'] }};">
                                        {{ strtoupper(substr($evaluation->intern->name ?? 'S', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 text-sm">{{ $evaluation->intern->name ?? 'N/A' }}</p>
                                        <p class="text-xs text-gray-500">{{ $evaluation->week_label }}</p>
                                    </div>
                                </div>
                                <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-sm font-bold" style="background-color: {{ $config['bg'] }}; color: {{ $config['text'] }};">
                                    {{ $evaluation->total_score }}/10
                                    <span class="text-xs opacity-75">({{ $evaluation->grade_letter }})</span>
                                </span>
                            </div>
                        @empty
                            <div class="flex flex-col items-center justify-center py-8">
                                <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-3">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                                <p class="text-gray-500 text-sm">Aucune évaluation récente</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des Stagiaires -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 flex items-center justify-between" style="background: linear-gradient(135deg, #5680E9, #5AB9EA);">
                <h3 class="text-white font-semibold flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Liste des stagiaires
                </h3>
                <span class="px-3 py-1 bg-white/20 text-white text-xs font-medium rounded-full">
                    {{ count($interns) }} stagiaires
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-gray-50 to-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Stagiaire</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Département</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tuteur</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">évaluations</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Derniére note</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($interns as $intern)
                            <tr class="hover:bg-blue-50/30 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold text-sm flex-shrink-0 shadow-md" style="background: linear-gradient(135deg, #5680E9, #84CEEB); box-shadow: 0 4px 6px -1px rgba(86, 128, 233, 0.3);">
                                            {{ strtoupper(substr($intern->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $intern->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $intern->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($intern->department)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-100 text-slate-700">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                            {{ $intern->department->name }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400">Non assigné</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($intern->supervisor)
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-lg flex items-center justify-center text-white font-bold text-xs" style="background: linear-gradient(135deg, #5AB9EA, #84CEEB);">
                                                {{ strtoupper(substr($intern->supervisor->name, 0, 1)) }}
                                            </div>
                                            <span class="text-sm font-medium text-gray-700">{{ $intern->supervisor->name }}</span>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium" style="background-color: rgba(136, 96, 208, 0.1); color: #8860D0;">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                            </svg>
                                            Non assigné
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg font-bold text-sm" style="background-color: rgba(90, 185, 234, 0.15); color: #5AB9EA;">
                                        {{ $intern->internEvaluations->count() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($intern->internEvaluations->first())
                                        @php 
                                            $lastEval = $intern->internEvaluations->first();
                                            $gradeConfig = [
                                                'A' => [
                                                    'bg' => 'rgba(90, 185, 234, 0.1)',
                                                    'text' => '#5AB9EA'
                                                ],
                                                'B' => [
                                                    'bg' => 'rgba(132, 206, 235, 0.1)',
                                                    'text' => '#84CEEB'
                                                ],
                                                'C' => [
                                                    'bg' => 'rgba(86, 128, 233, 0.1)',
                                                    'text' => '#5680E9'
                                                ],
                                                'D' => [
                                                    'bg' => 'rgba(136, 96, 208, 0.1)',
                                                    'text' => '#8860D0'
                                                ],
                                                'E' => [
                                                    'bg' => 'rgba(107, 114, 128, 0.1)',
                                                    'text' => '#6B7280'
                                                ],
                                            ];
                                            $config = $gradeConfig[$lastEval->grade_letter] ?? $gradeConfig['E'];
                                        @endphp
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-sm font-bold {{ $config['bg'] }} {{ $config['text'] }}">
                                            {{ $lastEval->total_score }}/10
                                            <span class="text-xs opacity-75">({{ $lastEval->grade_letter }})</span>
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.intern-evaluations.show', $intern) }}" 
                                       class="inline-flex items-center px-4 py-2 text-sm font-medium text-white rounded-lg transition-all shadow-md" style="background: linear-gradient(135deg, #5680E9, #5AB9EA); box-shadow: 0 4px 6px -1px rgba(86, 128, 233, 0.3);" onmouseover="this.style.filter='brightness(1.1)'" onmouseout="this.style.filter=''">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Détails
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-20 h-20 rounded-full flex items-center justify-center mb-4" style="background: linear-gradient(135deg, rgba(86, 128, 233, 0.1), rgba(132, 206, 235, 0.1));">
                                            <svg class="w-10 h-10 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                        </div>
                                        <p class="text-gray-500 font-medium">Aucun stagiaire enregistré</p>
                                        <p class="text-gray-400 text-sm mt-1">Les stagiaires apparaé®tront ici une fois ajoutés</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script nonce="{{ $cspNonce ?? '' }}">
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('gradeDistributionChart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['A (Excellent)', 'B (Bien)', 'C (Satisfaisant)', 'D (é€ améliorer)', 'E (Insuffisant)'],
                        datasets: [{
                            data: [
                                {{ $scoreDistribution['A'] ?? 0 }},
                                {{ $scoreDistribution['B'] ?? 0 }},
                                {{ $scoreDistribution['C'] ?? 0 }},
                                {{ $scoreDistribution['D'] ?? 0 }},
                                {{ $scoreDistribution['E'] ?? 0 }}
                            ],
                            backgroundColor: [
                                'rgb(90, 185, 234)',
                                'rgb(132, 206, 235)',
                                'rgb(86, 128, 233)',
                                'rgb(136, 96, 208)',
                                'rgb(107, 114, 128)'
                            ],
                            borderWidth: 3,
                            borderColor: '#ffffff',
                            hoverBorderWidth: 4,
                            hoverOffset: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '65%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    font: {
                                        size: 11,
                                        weight: '500'
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                titleFont: {
                                    size: 13,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 12
                                },
                                cornerRadius: 8,
                                displayColors: true,
                                boxPadding: 6
                            }
                        },
                        animation: {
                            animateScale: true,
                            animateRotate: true
                        }
                    }
                });
            }
        });
    </script>
    @endpush
</x-layouts.admin>
