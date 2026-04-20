<x-layouts.admin>
    <div class="space-y-6">
        <!-- Header avec gradient bleu doux -->
        <div class="bg-gradient-to-r from-[#1B3C35] to-[#3D7A6A] rounded-2xl p-6 text-white shadow-lg">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold mb-1">Tableau de bord</h1>
                    <p class="text-[#B8D1C7]">Bienvenue, {{ auth()->user()->name }} - {{ now()->translatedFormat('l d F Y') }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.registration-codes.index') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-[#1B3C35] font-semibold rounded-lg hover:bg-[#D4BC8B] transition-colors shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Code d'inscription
                    </a>
                    <a href="{{ route('admin.analytics.index') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-white/20 backdrop-blur text-white font-semibold rounded-lg hover:bg-white/30 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Analytics
                    </a>
                </div>
            </div>
        </div>

       

        <!-- Stats Cards -->
        <div x-data="{ loaded: false }" x-init="setTimeout(() => loaded = true, 150)">
            <div x-show="!loaded" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                <x-skeleton-loader type="stat-card" :count="5" :columns="5" />
            </div>
            <div x-show="loaded" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-cloak>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                    <a href="{{ route('admin.employees.index') }}" class="bg-white rounded-xl p-4 shadow-sm border border-[#D4BC8B]/50 hover:shadow-md hover:border-[#1B3C35]/30 transition-all">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-[#1B3C35] rounded-xl flex items-center justify-center">
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

                    <a href="{{ route('admin.presences.index') }}" class="bg-white rounded-xl p-4 shadow-sm border border-[#D4BC8B]/50 hover:shadow-md hover:border-[#2D5A4E]/30 transition-all">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-[#2D5A4E] rounded-xl flex items-center justify-center">
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

                    <a href="{{ route('admin.tasks.index', ['statut' => 'pending']) }}" class="bg-white rounded-xl p-4 shadow-sm border border-[#D4BC8B]/50 hover:shadow-md hover:border-[#3D7A6A]/30 transition-all">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-[#3D7A6A] rounded-xl flex items-center justify-center">
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

                    <a href="{{ route('admin.leaves.index', ['statut' => 'pending']) }}" class="bg-white rounded-xl p-4 shadow-sm border border-[#D4BC8B]/50 hover:shadow-md hover:border-[#C8A96E]/30 transition-all">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-[#C8A96E] rounded-xl flex items-center justify-center">
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

                    <a href="{{ route('admin.surveys.index') }}" class="bg-white rounded-xl p-4 shadow-sm border border-[#D4BC8B]/50 hover:shadow-md hover:border-[#1B3C35]/30 transition-all">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-[#1B3C35] rounded-xl flex items-center justify-center">
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
            </div>
        </div>

        <!-- Alerts, Activity & Calendar Row -->
        <div x-data="{ loaded: false }" x-init="setTimeout(() => loaded = true, 400)">
            <div x-show="!loaded" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <x-skeleton-loader type="alert" :count="1" :columns="1" />
                    <x-skeleton-loader type="activity" :count="1" :columns="1" />
                    <x-skeleton-loader type="calendar" :count="1" :columns="1" />
                </div>
            </div>
            <div x-show="loaded" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-cloak>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="flex flex-col min-h-0" style="max-height: 350px;">
                        <x-alert-center :alerts="$alerts" :apiUrl="route('admin.dashboard.alerts')" class="flex flex-col min-h-0 max-h-full" />
                    </div>
                    <div style="max-height: 350px;" class="overflow-hidden flex flex-col">
                        <x-activity-feed
                            :activities="$recentActivities"
                            :apiUrl="route('admin.dashboard.activity')"
                            :pollInterval="30000"
                            :maxItems="10" />
                    </div>
                    <div style="max-height: 350px;" class="overflow-hidden flex flex-col">
                        <x-mini-calendar :events="$calendarEvents" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Planning de présence -->
        <div x-data="{ loaded: false }" x-init="setTimeout(() => loaded = true, 600)">
            <div x-show="!loaded" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                <x-skeleton-loader type="list" :count="1" :columns="1" />
            </div>
            <div x-show="loaded" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-cloak>
                <x-presence-planning :apiUrl="route('admin.dashboard.presence-planning')" />
            </div>
        </div>

        <!-- Charts Row -->
        <div x-data="{ loaded: false }" x-init="setTimeout(() => loaded = true, 800)">
            <div x-show="!loaded" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                <x-skeleton-loader type="chart" :count="3" :columns="3" />
            </div>
            <div x-show="loaded" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-cloak>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Tasks Chart -->
                    <div class="bg-white rounded-xl shadow-sm border border-[#D4BC8B]/50 overflow-hidden">
                        <div class="px-5 py-4 bg-gradient-to-r from-slate-50 to-[#D4BC8B]/20 border-b border-[#D4BC8B]/30 flex items-center justify-between">
                            <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                                <svg class="w-5 h-5 text-[#1B3C35]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                Répartition des tâches
                            </h3>
                            <a href="{{ route('admin.tasks.index') }}" class="text-sm text-[#1B3C35] hover:underline">Voir tout</a>
                        </div>
                        <div class="p-5 h-72">
                            <canvas id="taskChart"></canvas>
                        </div>
                    </div>

                    <!-- Présences Chart -->
                    <div class="bg-white rounded-xl shadow-sm border border-[#D4BC8B]/50 overflow-hidden">
                        <div class="px-5 py-4 bg-gradient-to-r from-slate-50 to-[#D4BC8B]/20 border-b border-[#D4BC8B]/30 flex items-center justify-between">
                            <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                                <svg class="w-5 h-5 text-[#2D5A4E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Présences mensuelles
                            </h3>
                            <a href="{{ route('admin.presences.index') }}" class="text-sm text-[#1B3C35] hover:underline">Voir tout</a>
                        </div>
                        <div class="p-5 h-72">
                            <canvas id="presenceChart"></canvas>
                        </div>
                    </div>

                    <!-- Leave Chart -->
                    <div class="bg-white rounded-xl shadow-sm border border-[#D4BC8B]/50 overflow-hidden">
                        <div class="px-5 py-4 bg-gradient-to-r from-slate-50 to-[#D4BC8B]/20 border-b border-[#D4BC8B]/30 flex items-center justify-between">
                            <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                                <svg class="w-5 h-5 text-[#C8A96E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Congés par mois
                            </h3>
                            <a href="{{ route('admin.leaves.index') }}" class="text-sm text-[#1B3C35] hover:underline">Voir tout</a>
                        </div>
                        <div class="p-5 h-72">
                            <canvas id="leaveChart"></canvas>
                        </div>
                    </div>
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
            primary: '#1B3C35',
            secondary: '#3D7A6A',
            tertiary: '#2D5A4E',
            accent: '#C8A96E',
            light: '#D4BC8B'
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
                    backgroundColor: 'rgba(27, 60, 53, 0.1)',
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
                    y: { beginAtZero: true, grid: { color: 'rgba(212, 188, 139, 0.3)' } },
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
                    y: { beginAtZero: true, grid: { color: 'rgba(212, 188, 139, 0.3)' } },
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

    {{-- Modal: Configurer l'email de réception du rapport quotidien --}}
    @unless($hasReportEmail ?? true)
    <div x-data="{ showReportEmailModal: true }" x-show="showReportEmailModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background: rgba(0,0,0,0.5);">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden" @click.away="showReportEmailModal = false">
            <div class="px-6 py-5" style="background: linear-gradient(135deg, #1B3C35, #3D7A6A);">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">Rapport quotidien</h3>
                        <p class="text-white/80 text-sm">Configurez votre email de réception</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.settings.update-report-email') }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <p class="text-sm text-gray-600 mb-4">
                    Chaque jour à 19h, un compte rendu complet de la journée vous sera envoyé par email (présences, retards, tâches, congés...).
                </p>

                <div>
                    <label for="modal_report_email" class="block text-sm font-medium text-gray-700 mb-1">Email de réception</label>
                    <input type="email" name="report_email" id="modal_report_email" required
                           value="{{ auth()->user()->email }}"
                           class="w-full rounded-xl border-gray-300 focus:border-[#2D5A4E] focus:ring-[#2D5A4E]"
                           placeholder="votre-email@exemple.com">
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="submit" class="flex-1 px-4 py-2.5 text-white font-medium rounded-xl transition-all" style="background: linear-gradient(135deg, #1B3C35, #2D5A4E);">
                        Enregistrer
                    </button>
                    <button type="button" @click="showReportEmailModal = false" class="px-4 py-2.5 text-gray-600 font-medium rounded-xl border border-gray-300 hover:bg-gray-50 transition-all">
                        Plus tard
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endunless
</x-layouts.admin>
