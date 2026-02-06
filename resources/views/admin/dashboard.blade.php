<x-layouts.admin>
    <div class="space-y-6">
        <!-- Header avec gradient bleu doux -->
        <div class="bg-gradient-to-r from-[#5680E9] to-[#84CEEB] rounded-2xl p-6 text-white shadow-lg">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold mb-1">Tableau de bord</h1>
                    <p class="text-blue-100">Bienvenue, {{ auth()->user()->name }} - {{ now()->translatedFormat('l d F Y') }}</p>
                </div>
                <a href="{{ route('admin.analytics.index') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-white/20 backdrop-blur text-white font-semibold rounded-lg hover:bg-white/30 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Analytics
                </a>
            </div>
        </div>

       

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            <a href="{{ route('admin.employees.index') }}" class="bg-white rounded-xl p-4 shadow-sm border border-[#C1C8E4]/50 hover:shadow-md hover:border-[#5680E9]/30 transition-all">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-[#5680E9] rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-slate-800">{{ $stats['total_employees'] }}</p>
                        <p class="text-xs text-slate-500">Employés</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.presences.index') }}" class="bg-white rounded-xl p-4 shadow-sm border border-[#C1C8E4]/50 hover:shadow-md hover:border-[#5AB9EA]/30 transition-all">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-[#5AB9EA] rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-slate-800">{{ $stats['presences_today'] }}</p>
                        <p class="text-xs text-slate-500">Présents</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.tasks.index', ['statut' => 'pending']) }}" class="bg-white rounded-xl p-4 shadow-sm border border-[#C1C8E4]/50 hover:shadow-md hover:border-[#84CEEB]/30 transition-all">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-[#84CEEB] rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-slate-800">{{ $stats['pending_tasks'] }}</p>
                        <p class="text-xs text-slate-500">Tâches</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.leaves.index', ['statut' => 'pending']) }}" class="bg-white rounded-xl p-4 shadow-sm border border-[#C1C8E4]/50 hover:shadow-md hover:border-[#8860D0]/30 transition-all">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-[#8860D0] rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-slate-800">{{ $stats['pending_leaves'] }}</p>
                        <p class="text-xs text-slate-500">Congés</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.surveys.index') }}" class="bg-white rounded-xl p-4 shadow-sm border border-[#C1C8E4]/50 hover:shadow-md hover:border-[#5680E9]/30 transition-all">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-[#5680E9] rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-slate-800">{{ $stats['active_surveys'] }}</p>
                        <p class="text-xs text-slate-500">Sondages</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Quick Actions, Alerts, Activity & Calendar Row - 4 colonnes -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-[#C1C8E4]/50 overflow-hidden flex flex-col" style="max-height: 350px;">
                <div class="px-5 py-4 bg-gradient-to-r from-slate-50 to-[#C1C8E4]/20 border-b border-[#C1C8E4]/30 flex-shrink-0">
                    <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#5680E9]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Actions rapides
                    </h3>
                </div>
                <div class="p-4 space-y-3 overflow-y-auto flex-1">
                    <a href="{{ route('admin.tasks.index', ['statut' => 'pending']) }}"
                       class="flex items-center justify-between p-3 bg-[#5680E9]/5 rounded-xl hover:bg-[#5680E9]/10 transition-colors group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-[#5680E9] rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-800">Tâches à valider</p>
                                <p class="text-xs text-slate-500">{{ $stats['pending_tasks'] }} en attente</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-[#5680E9] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>

                    <a href="{{ route('admin.leaves.index', ['statut' => 'pending']) }}"
                       class="flex items-center justify-between p-3 bg-[#8860D0]/5 rounded-xl hover:bg-[#8860D0]/10 transition-colors group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-[#8860D0] rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-800">Congés à traiter</p>
                                <p class="text-xs text-slate-500">{{ $stats['pending_leaves'] }} demandes</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-[#8860D0] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>

                    <a href="{{ route('admin.employees.create') }}"
                       class="flex items-center justify-between p-3 bg-[#5AB9EA]/5 rounded-xl hover:bg-[#5AB9EA]/10 transition-colors group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-[#5AB9EA] rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-800">Nouvel employé</p>
                                <p class="text-xs text-slate-500">Ajouter un collaborateur</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-[#5AB9EA] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>

                    <a href="{{ route('admin.employee-evaluations.index') }}"
                       class="flex items-center justify-between p-3 bg-[#84CEEB]/5 rounded-xl hover:bg-[#84CEEB]/10 transition-colors group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-[#84CEEB] rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-800">Évaluations</p>
                                <p class="text-xs text-slate-500">Évaluer les performances</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-[#84CEEB] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Alert Center -->
            <div style="max-height: 350px;" class="overflow-hidden flex flex-col">
                <x-alert-center :alerts="$alerts" :apiUrl="route('admin.dashboard.alerts')" />
            </div>

            <!-- Activity Feed -->
            <div style="max-height: 350px;" class="overflow-hidden flex flex-col">
                <x-activity-feed
                    :activities="$recentActivities"
                    :apiUrl="route('admin.dashboard.activity')"
                    :pollInterval="30000"
                    :maxItems="10" />
            </div>

            <!-- Mini Calendar -->
            <div style="max-height: 350px;" class="overflow-hidden flex flex-col">
                <x-mini-calendar :events="$calendarEvents" />
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Tasks Chart -->
            <div class="bg-white rounded-xl shadow-sm border border-[#C1C8E4]/50 overflow-hidden">
                <div class="px-5 py-4 bg-gradient-to-r from-slate-50 to-[#C1C8E4]/20 border-b border-[#C1C8E4]/30 flex items-center justify-between">
                    <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#5680E9]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Répartition des tâches
                    </h3>
                    <a href="{{ route('admin.tasks.index') }}" class="text-sm text-[#5680E9] hover:underline">Voir tout</a>
                </div>
                <div class="p-5 h-72">
                    <canvas id="taskChart"></canvas>
                </div>
            </div>

            <!-- Présences Chart -->
            <div class="bg-white rounded-xl shadow-sm border border-[#C1C8E4]/50 overflow-hidden">
                <div class="px-5 py-4 bg-gradient-to-r from-slate-50 to-[#C1C8E4]/20 border-b border-[#C1C8E4]/30 flex items-center justify-between">
                    <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#5AB9EA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Présences mensuelles
                    </h3>
                    <a href="{{ route('admin.presences.index') }}" class="text-sm text-[#5680E9] hover:underline">Voir tout</a>
                </div>
                <div class="p-5 h-72">
                    <canvas id="presenceChart"></canvas>
                </div>
            </div>

            <!-- Leave Chart -->
            <div class="bg-white rounded-xl shadow-sm border border-[#C1C8E4]/50 overflow-hidden">
                <div class="px-5 py-4 bg-gradient-to-r from-slate-50 to-[#C1C8E4]/20 border-b border-[#C1C8E4]/30 flex items-center justify-between">
                    <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#8860D0]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Congés par mois
                    </h3>
                    <a href="{{ route('admin.leaves.index') }}" class="text-sm text-[#5680E9] hover:underline">Voir tout</a>
                </div>
                <div class="p-5 h-72">
                    <canvas id="leaveChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script nonce="{{ $cspNonce ?? '' }}">
        const presenceData = @json($presenceData);
        const taskData = @json($taskData);
        const leaveData = @json($leaveData);

        // Couleurs du thème
        const themeColors = {
            primary: '#5680E9',
            secondary: '#84CEEB',
            tertiary: '#5AB9EA',
            accent: '#8860D0',
            light: '#C1C8E4'
        };

        // Graphique des présences
        new Chart(document.getElementById('presenceChart'), {
            type: 'line',
            data: {
                labels: presenceData.labels,
                datasets: [{
                    label: 'Présences',
                    data: presenceData.data,
                    borderColor: themeColors.primary,
                    backgroundColor: 'rgba(86, 128, 233, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: themeColors.primary,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(17, 24, 39, 0.9)',
                        padding: 12,
                        cornerRadius: 8
                    }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(193, 200, 228, 0.3)' } },
                    x: { grid: { display: false } }
                }
            }
        });

        // Graphique des tâches avec couleurs thème
        new Chart(document.getElementById('taskChart'), {
            type: 'doughnut',
            data: {
                labels: taskData.labels,
                datasets: [{
                    data: taskData.data,
                    backgroundColor: [themeColors.primary, themeColors.secondary, themeColors.tertiary, themeColors.accent, themeColors.light],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 15, usePointStyle: true, pointStyle: 'circle' }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(17, 24, 39, 0.9)',
                        padding: 12,
                        cornerRadius: 8
                    }
                }
            }
        });

        // Graphique des congés
        new Chart(document.getElementById('leaveChart'), {
            type: 'bar',
            data: {
                labels: leaveData.labels,
                datasets: [{
                    label: 'Congés approuvés',
                    data: leaveData.data,
                    backgroundColor: themeColors.accent,
                    borderRadius: 6,
                    borderSkipped: false,
                    hoverBackgroundColor: '#7048B8'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(17, 24, 39, 0.9)',
                        padding: 12,
                        cornerRadius: 8
                    }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(193, 200, 228, 0.3)' } },
                    x: { grid: { display: false } }
                }
            }
        });

        // Auto-refresh stats
        let isPageVisible = true;
        document.addEventListener('visibilitychange', () => {
            isPageVisible = !document.hidden;
        });

        setInterval(async () => {
            if (!isPageVisible) return;
            try {
                const response = await fetch('{{ route("admin.stats") }}');
                if (!response.ok) return;
                const data = await response.json();
            } catch (error) {
                // Silently ignore
            }
        }, 60000);
    </script>
    @endpush
</x-layouts.admin>
