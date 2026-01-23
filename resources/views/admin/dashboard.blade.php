<x-layouts.admin>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Tableau de bord</h1>
                <p class="text-sm text-gray-500 mt-1">Bienvenue, {{ auth()->user()->name }}</p>
            </div>
            <div class="flex items-center space-x-4">
                <p class="text-sm text-gray-500">{{ now()->translatedFormat('l d F Y') }}</p>
                <a href="{{ route('admin.analytics.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Analytics
                </a>
            </div>
        </div>
  <!-- KPIs avancés -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-5 text-white animate-fade-in-up">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm">Taux de présence</p>
                        <p class="text-3xl font-bold mt-1">{{ $advancedStats['presence_rate'] }}%</p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl p-5 text-white animate-fade-in-up animation-delay-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-100 text-sm">Taux d'absentéisme</p>
                        <p class="text-3xl font-bold mt-1">{{ $advancedStats['absenteeism_rate'] }}%</p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-5 text-white animate-fade-in-up animation-delay-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm">Tâches terminées</p>
                        <p class="text-3xl font-bold mt-1">{{ $advancedStats['tasks_completed_this_week'] }}</p>
                        <p class="text-green-200 text-xs mt-1">cette semaine</p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-5 text-white animate-fade-in-up animation-delay-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm">Heures moy. travaillées</p>
                        <p class="text-3xl font-bold mt-1">{{ $advancedStats['avg_hours_today'] }}h</p>
                        <p class="text-purple-200 text-xs mt-1">aujourd'hui</p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        <!-- Stats Cards avec KPIs avancés -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 lg:gap-6">
            <x-stat-card
                title="Employés"
                :value="$stats['total_employees']"
                color="blue"
                :link="route('admin.employees.index')">
                <x-slot:icon>
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </x-slot:icon>
            </x-stat-card>

            <x-stat-card
                title="Présences aujourd'hui"
                :value="$stats['presences_today']"
                color="green"
                :subtitle="$advancedStats['presence_rate'] . '% de présence'"
                :trend="$advancedStats['presences_trend']"
                :link="route('admin.presences.index')">
                <x-slot:icon>
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </x-slot:icon>
            </x-stat-card>

            <x-stat-card
                title="Tâches en attente"
                :value="$stats['pending_tasks']"
                color="yellow"
                :trend="$advancedStats['tasks_trend']"
                trendLabel="vs semaine dernière"
                :link="route('admin.tasks.index', ['statut' => 'pending'])">
                <x-slot:icon>
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </x-slot:icon>
            </x-stat-card>

            <x-stat-card
                title="Congés en attente"
                :value="$stats['pending_leaves']"
                color="purple"
                :link="route('admin.leaves.index', ['statut' => 'pending'])">
                <x-slot:icon>
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </x-slot:icon>
            </x-stat-card>

            <x-stat-card
                title="Sondages actifs"
                :value="$stats['active_surveys']"
                color="indigo"
                :link="route('admin.surveys.index')">
                <x-slot:icon>
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </x-slot:icon>
            </x-stat-card>

          
        </div>

        <!-- Quick Actions & Alerts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 animate-fade-in-up">
                <h3 class="font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Actions rapides
                </h3>
                <div class="space-y-3">
                    <a href="{{ route('admin.tasks.index', ['statut' => 'pending']) }}"
                       class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors group">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-yellow-200 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-yellow-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Tâches à valider</p>
                                <p class="text-xs text-gray-500">{{ $stats['pending_tasks'] }} en attente</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>

                    <a href="{{ route('admin.leaves.index', ['statut' => 'pending']) }}"
                       class="flex items-center justify-between p-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors group">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-purple-200 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Congés à traiter</p>
                                <p class="text-xs text-gray-500">{{ $stats['pending_leaves'] }} demandes</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>

                    <a href="{{ route('admin.employees.create') }}"
                       class="flex items-center justify-between p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors group">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-200 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Nouvel employé</p>
                                <p class="text-xs text-gray-500">Ajouter un collaborateur</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Alert Center -->
            <div class="lg:col-span-2">
                <x-alert-center :alerts="$alerts" :apiUrl="route('admin.dashboard.alerts')" />
            </div>
        </div>

        <!-- Activity Feed & Calendar Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Activity Feed -->
            <div class="lg:col-span-2">
                <x-activity-feed
                    :activities="$recentActivities"
                    :apiUrl="route('admin.dashboard.activity')"
                    :pollInterval="30000"
                    :maxItems="10" />
            </div>

            <!-- Mini Calendar -->
            <x-mini-calendar :events="$calendarEvents" />
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Présences Chart -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 animate-fade-in-up">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Présences mensuelles</h3>
                    <a href="{{ route('admin.presences.index') }}" class="text-sm text-blue-600 hover:underline">Voir tout</a>
                </div>
                <canvas id="presenceChart" height="200"></canvas>
            </div>

            <!-- Tasks Chart -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 animate-fade-in-up">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Répartition des tâches</h3>
                    <a href="{{ route('admin.tasks.index') }}" class="text-sm text-blue-600 hover:underline">Voir tout</a>
                </div>
                <canvas id="taskChart" height="200"></canvas>
            </div>
        </div>

        <!-- Department Stats & Leave Chart -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Department Stats -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 animate-fade-in-up">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Présence par département</h3>
                <div class="space-y-4">
                    @forelse($departmentStats as $dept)
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium text-gray-700">{{ $dept['name'] }}</span>
                                <span class="text-sm text-gray-500">{{ $dept['presences_today'] }}/{{ $dept['employees'] }}</span>
                            </div>
                            <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500"
                                     style="width: {{ $dept['presence_rate'] }}%; background-color: {{ $dept['color'] }}"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4">Aucun département configuré</p>
                    @endforelse
                </div>
            </div>

            <!-- Leave Chart -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6 border border-gray-100 animate-fade-in-up">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Congés approuvés par mois</h3>
                    <a href="{{ route('admin.leaves.index') }}" class="text-sm text-blue-600 hover:underline">Voir tout</a>
                </div>
                <canvas id="leaveChart" height="100"></canvas>
            </div>
        </div>

        
    </div>

    @push('scripts')
    <script>
        // Données pour les graphiques
        const presenceData = @json($presenceData);
        const taskData = @json($taskData);
        const leaveData = @json($leaveData);

        // Graphique des présences
        new Chart(document.getElementById('presenceChart'), {
            type: 'line',
            data: {
                labels: presenceData.labels,
                datasets: [{
                    label: 'Présences',
                    data: presenceData.data,
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#3B82F6',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(17, 24, 39, 0.9)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        padding: 12,
                        cornerRadius: 8
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });

        // Graphique des tâches
        new Chart(document.getElementById('taskChart'), {
            type: 'doughnut',
            data: {
                labels: taskData.labels,
                datasets: [{
                    data: taskData.data,
                    backgroundColor: taskData.colors,
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
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
                    backgroundColor: 'rgba(139, 92, 246, 0.8)',
                    borderRadius: 6,
                    borderSkipped: false,
                    hoverBackgroundColor: '#8B5CF6'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(17, 24, 39, 0.9)',
                        padding: 12,
                        cornerRadius: 8
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Polling AJAX pour mise à jour automatique (toutes les 60 secondes)
        setInterval(async () => {
            try {
                const response = await fetch('{{ route("admin.stats") }}');
                const data = await response.json();
                console.log('Stats updated:', data);
                // Les composants gèrent leur propre mise à jour
            } catch (error) {
                console.error('Error fetching stats:', error);
            }
        }, 60000);
    </script>
    @endpush
</x-layouts.admin>
