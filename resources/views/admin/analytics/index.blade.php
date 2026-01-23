<x-layouts.admin>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tableau de bord analytique
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="w-full mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Filtres -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-4">
                    <div class="flex flex-wrap items-center gap-4">
                        <div>
                            <label for="period" class="block text-sm font-medium text-gray-700 mb-1">Période</label>
                            <select id="period" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="week">7 derniers jours</option>
                                <option value="month" selected>30 derniers jours</option>
                                <option value="quarter">3 derniers mois</option>
                                <option value="year">12 derniers mois</option>
                            </select>
                        </div>
                        <div>
                            <label for="department" class="block text-sm font-medium text-gray-700 mb-1">Département</label>
                            <select id="department" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Tous les départements</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button id="refreshBtn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Actualiser
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats rapides -->
            <div class="grid gap-4 md:grid-cols-4 mb-6">
                <!-- Taux de présence -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Taux de présence</p>
                            <p class="text-2xl font-bold text-gray-900" id="presenceRate">--</p>
                        </div>
                    </div>
                </div>

                <!-- Heures moyennes -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Heures moy./jour</p>
                            <p class="text-2xl font-bold text-gray-900" id="avgHours">--</p>
                        </div>
                    </div>
                </div>

                <!-- Taux de réalisation tâches -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-purple-100 rounded-lg p-3">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Tâches complétées</p>
                            <p class="text-2xl font-bold text-gray-900" id="taskRate">--</p>
                        </div>
                    </div>
                </div>

                <!-- Pointages dans la zone -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-100 rounded-lg p-3">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Pointages en zone</p>
                            <p class="text-2xl font-bold text-gray-900" id="geoRate">--</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Graphiques -->
            <div class="grid gap-6 lg:grid-cols-2 mb-6">
                <!-- Tendance de présence -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Tendance de présence</h3>
                    <div class="h-64">
                        <canvas id="presenceTrendChart"></canvas>
                    </div>
                </div>

                <!-- Répartition par département -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Répartition par département</h3>
                    <div class="h-64">
                        <canvas id="departmentChart"></canvas>
                    </div>
                </div>

                <!-- Statut des tâches -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Statut des tâches</h3>
                    <div class="h-64">
                        <canvas id="taskStatusChart"></canvas>
                    </div>
                </div>

                <!-- Types de congés -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Types de congés</h3>
                    <div class="h-64">
                        <canvas id="leaveTypesChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Top employés + Horaires moyens -->
            <div class="grid gap-6 lg:grid-cols-2">
                <!-- Top 5 employés -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Top 5 - Présence</h3>
                    <div id="topEmployees">
                        <p class="text-gray-500 text-center py-4">Chargement...</p>
                    </div>
                </div>

                <!-- Horaires moyens -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Horaires moyens</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-green-50 rounded-lg p-4 text-center">
                            <p class="text-sm text-green-600 mb-1">Arrivée moyenne</p>
                            <p class="text-3xl font-bold text-green-700" id="avgCheckIn">--:--</p>
                        </div>
                        <div class="bg-red-50 rounded-lg p-4 text-center">
                            <p class="text-sm text-red-600 mb-1">Départ moyen</p>
                            <p class="text-3xl font-bold text-red-700" id="avgCheckOut">--:--</p>
                        </div>
                    </div>
                    <div class="mt-6">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Statistiques géolocalisation</h4>
                        <div class="flex gap-2">
                            <div class="flex-1 bg-green-100 rounded-lg p-3 text-center">
                                <p class="text-xs text-green-600">Dans la zone</p>
                                <p class="text-lg font-bold text-green-700" id="geoInZone">0</p>
                            </div>
                            <div class="flex-1 bg-red-100 rounded-lg p-3 text-center">
                                <p class="text-xs text-red-600">Hors zone</p>
                                <p class="text-lg font-bold text-red-700" id="geoOutZone">0</p>
                            </div>
                            <div class="flex-1 bg-gray-100 rounded-lg p-3 text-center">
                                <p class="text-xs text-gray-600">Inconnu</p>
                                <p class="text-lg font-bold text-gray-700" id="geoUnknown">0</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        let charts = {};

        function loadData() {
            const period = document.getElementById('period').value;
            const departmentId = document.getElementById('department').value;

            fetch(`{{ route('admin.analytics.data') }}?period=${period}&department_id=${departmentId}`)
                .then(res => res.json())
                .then(data => {
                    updateStats(data);
                    updateCharts(data);
                    updateTopEmployees(data.topEmployees);
                });
        }

        function updateStats(data) {
            document.getElementById('presenceRate').textContent = data.presence.presenceRate + '%';
            document.getElementById('avgHours').textContent = data.presence.avgHoursPerDay + 'h';
            document.getElementById('taskRate').textContent = data.tasks.completionRate + '%';
            document.getElementById('geoRate').textContent = data.geolocation.inZoneRate + '%';

            document.getElementById('avgCheckIn').textContent = data.averageCheckTimes.avgCheckIn;
            document.getElementById('avgCheckOut').textContent = data.averageCheckTimes.avgCheckOut;

            document.getElementById('geoInZone').textContent = data.geolocation.inZone;
            document.getElementById('geoOutZone').textContent = data.geolocation.outOfZone;
            document.getElementById('geoUnknown').textContent = data.geolocation.unknown;
        }

        function updateCharts(data) {
            // Tendance présence
            if (charts.presenceTrend) charts.presenceTrend.destroy();
            const ctx1 = document.getElementById('presenceTrendChart').getContext('2d');
            charts.presenceTrend = new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: data.presenceTrend.map(d => d.date),
                    datasets: [{
                        label: 'Présences',
                        data: data.presenceTrend.map(d => d.count),
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            });

            // Répartition département
            if (charts.department) charts.department.destroy();
            const ctx2 = document.getElementById('departmentChart').getContext('2d');
            charts.department = new Chart(ctx2, {
                type: 'doughnut',
                data: {
                    labels: data.departmentDistribution.map(d => d.name),
                    datasets: [{
                        data: data.departmentDistribution.map(d => d.count),
                        backgroundColor: data.departmentDistribution.map(d => d.color),
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                }
            });

            // Statut tâches
            if (charts.taskStatus) charts.taskStatus.destroy();
            const ctx3 = document.getElementById('taskStatusChart').getContext('2d');
            charts.taskStatus = new Chart(ctx3, {
                type: 'bar',
                data: {
                    labels: ['En attente', 'En cours', 'Terminé', 'Validé'],
                    datasets: [{
                        label: 'Tâches',
                        data: [data.tasks.pending, data.tasks.inProgress, data.tasks.completed, data.tasks.validated],
                        backgroundColor: ['#F59E0B', '#3B82F6', '#10B981', '#8B5CF6']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            });

            // Types de congés
            if (charts.leaveTypes) charts.leaveTypes.destroy();
            const ctx4 = document.getElementById('leaveTypesChart').getContext('2d');
            const leaveLabels = {
                'annual': 'Annuel',
                'sick': 'Maladie',
                'personal': 'Personnel',
                'maternity': 'Maternité',
                'other': 'Autre'
            };
            charts.leaveTypes = new Chart(ctx4, {
                type: 'pie',
                data: {
                    labels: Object.keys(data.leaves.byType).map(k => leaveLabels[k] || k),
                    datasets: [{
                        data: Object.values(data.leaves.byType),
                        backgroundColor: ['#3B82F6', '#EF4444', '#F59E0B', '#EC4899', '#6B7280']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                }
            });
        }

        function updateTopEmployees(employees) {
            const container = document.getElementById('topEmployees');
            if (employees.length === 0) {
                container.innerHTML = '<p class="text-gray-500 text-center py-4">Aucune donnée</p>';
                return;
            }

            container.innerHTML = employees.map((emp, idx) => `
                <div class="flex items-center justify-between py-3 ${idx < employees.length - 1 ? 'border-b' : ''}">
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-sm font-medium">${idx + 1}</span>
                        <div>
                            <p class="font-medium text-gray-900">${emp.name}</p>
                            <p class="text-sm text-gray-500">${emp.department}</p>
                        </div>
                    </div>
                    <span class="text-sm font-medium text-gray-900">${emp.presences} jours</span>
                </div>
            `).join('');
        }

        document.getElementById('period').addEventListener('change', loadData);
        document.getElementById('department').addEventListener('change', loadData);
        document.getElementById('refreshBtn').addEventListener('click', loadData);

        // Chargement initial
        loadData();
    </script>
</x-layouts.admin>
