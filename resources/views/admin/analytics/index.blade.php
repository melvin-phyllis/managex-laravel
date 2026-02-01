<x-layouts.admin>
    <div class="space-y-6" x-data="analyticsPage()">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 animate-fade-in-up">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/20">
                        <x-icon name="bar-chart-2" class="w-5 h-5 text-white" />
                    </div>
                    Analytics RH
                </h1>
                <p class="text-gray-500 mt-1 ml-13">Tableau de bord de performance et statistiques</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-400" x-show="loading">Actualisation...</span>
                <button @click="loadData()" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors flex items-center shadow-sm">
                    <x-icon name="refresh-cw" class="w-4 h-4 mr-2" x-bind:class="{'animate-spin': loading}"/>
                    Actualiser
                </button>
            </div>
        </div>

        {{-- Filtres --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex flex-wrap gap-4 items-center animate-fade-in-up animation-delay-100">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <x-icon name="filter" class="w-4 h-4" />
                Filtrer par :
            </div>
            
            <select x-model="filters.period" @change="loadData()" class="rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="today">Aujourd'hui</option>
                <option value="week">Cette semaine</option>
                <option value="month">Ce mois</option>
                <option value="year">Cette année</option>
                <option value="custom">Mois spécifique...</option>
            </select>

            {{-- Sélecteur de mois spécifique --}}
            <template x-if="filters.period === 'custom'">
                <div class="flex gap-2">
                    <select x-model="filters.custom_month" @change="loadData()" class="rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="1">Janvier</option>
                        <option value="2">Février</option>
                        <option value="3">Mars</option>
                        <option value="4">Avril</option>
                        <option value="5">Mai</option>
                        <option value="6">Juin</option>
                        <option value="7">Juillet</option>
                        <option value="8">Août</option>
                        <option value="9">Septembre</option>
                        <option value="10">Octobre</option>
                        <option value="11">Novembre</option>
                        <option value="12">Décembre</option>
                    </select>
                    <select x-model="filters.custom_year" @change="loadData()" class="rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="2026">2026</option>
                        <option value="2025">2025</option>
                        <option value="2024">2024</option>
                    </select>
                </div>
            </template>

            <select x-model="filters.department_id" @change="loadData()" class="rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Tous les départements</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                @endforeach
            </select>

            <select x-model="filters.contract_type" @change="loadData()" class="rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Tous les contrats</option>
                <option value="CDI">CDI</option>
                <option value="CDD">CDD</option>
                <option value="Stage">Stage</option>
                <option value="Alternance">Alternance</option>
            </select>
        </div>

        {{-- KPI Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 animate-fade-in-up animation-delay-200">
            {{-- 1. Effectif total --}}
            <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl p-5 text-white shadow-lg shadow-blue-500/20 relative overflow-hidden group">
                <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:scale-110 transition-transform">
                    <x-icon name="users" class="w-16 h-16"/>
                </div>
                <div class="relative z-10">
                    <p class="text-blue-100 text-xs font-medium uppercase tracking-wider">Effectif total</p>
                    <p class="text-3xl font-bold mt-1" x-text="kpis.effectif_total?.value || '-'"></p>
                    <div class="flex items-center mt-2 text-xs font-medium" :class="kpis.effectif_total?.variation >= 0 ? 'text-blue-100' : 'text-red-200'">
                        <span class="bg-white/20 px-1.5 py-0.5 rounded flex items-center">
                            <x-icon name="trending-up" class="w-3 h-3 mr-1" x-show="kpis.effectif_total?.variation >= 0"/>
                            <x-icon name="trending-down" class="w-3 h-3 mr-1" x-show="kpis.effectif_total?.variation < 0"/>
                            <span x-text="(kpis.effectif_total?.variation > 0 ? '+' : '') + (kpis.effectif_total?.variation || 0) + '%'"></span>
                        </span>
                        <span class="ml-2 opacity-70">vs m-1</span>
                    </div>
                </div>
            </div>

            {{-- 2. Présences du mois --}}
            <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl p-5 text-white shadow-lg shadow-emerald-500/20 relative overflow-hidden group">
                <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:scale-110 transition-transform">
                    <x-icon name="check-circle" class="w-16 h-16"/>
                </div>
                <div class="relative z-10">
                    <p class="text-emerald-100 text-xs font-medium uppercase tracking-wider">Présences mois</p>
                    <div class="flex items-end gap-2 mt-1">
                        <p class="text-3xl font-bold" x-text="kpis.presents_today?.value || '0'"></p>
                        <p class="text-sm text-emerald-100 mb-1" x-text="'/ ' + (kpis.presents_today?.expected || '0')"></p>
                    </div>
                    <div class="w-full bg-black/10 rounded-full h-1.5 mt-3">
                        <div class="bg-white h-1.5 rounded-full transition-all duration-1000" :style="'width: ' + (kpis.presents_today?.percentage || 0) + '%'"></div>
                    </div>
                </div>
            </div>

            {{-- 3. En congé --}}
            <div class="bg-gradient-to-br from-violet-500 to-purple-600 rounded-xl p-5 text-white shadow-lg shadow-violet-500/20 relative overflow-hidden group">
                <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:scale-110 transition-transform">
                    <x-icon name="coffee" class="w-16 h-16"/>
                </div>
                <div class="relative z-10">
                    <p class="text-violet-100 text-xs font-medium uppercase tracking-wider">En congé</p>
                    <p class="text-3xl font-bold mt-1" x-text="kpis.en_conge?.value || '0'"></p>
                    <div class="mt-2 flex flex-wrap gap-1">
                        <span class="text-[10px] bg-white/20 px-1.5 py-0.5 rounded" x-show="kpis.en_conge?.types?.conge > 0" x-text="'CP: ' + kpis.en_conge?.types?.conge"></span>
                        <span class="text-[10px] bg-white/20 px-1.5 py-0.5 rounded" x-show="kpis.en_conge?.types?.maladie > 0" x-text="'Mal: ' + kpis.en_conge?.types?.maladie"></span>
                    </div>
                </div>
            </div>

            {{-- 4. Absents --}}
            <div class="bg-gradient-to-br from-rose-500 to-pink-600 rounded-xl p-5 text-white shadow-lg shadow-rose-500/20 relative overflow-hidden group">
                <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:scale-110 transition-transform">
                    <x-icon name="alert-circle" class="w-16 h-16"/>
                </div>
                <div class="relative z-10">
                    <p class="text-rose-100 text-xs font-medium uppercase tracking-wider">Absents injustifiés</p>
                    <p class="text-3xl font-bold mt-1" x-text="kpis.absents_non_justifies?.value || '0'"></p>
                    <p class="text-xs text-rose-100 mt-2 opacity-80">Action requise</p>
                </div>
            </div>

            {{-- 5. Masse salariale --}}
            <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl p-5 text-white shadow-lg shadow-amber-500/20 relative overflow-hidden group">
                <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:scale-110 transition-transform">
                    <x-icon name="dollar-sign" class="w-16 h-16"/>
                </div>
                <div class="relative z-10">
                    <p class="text-amber-100 text-xs font-medium uppercase tracking-wider">Masse Salariale</p>
                    <p class="text-xl font-bold mt-2 truncate" x-text="kpis.masse_salariale?.formatted || '0 €'"></p>
                    <p class="text-xs text-amber-100 mt-1 opacity-80">Ce mois</p>
                </div>
            </div>

            {{-- 6. Heures sup --}}
            <div class="bg-gradient-to-br from-cyan-500 to-blue-600 rounded-xl p-5 text-white shadow-lg shadow-cyan-500/20 relative overflow-hidden group">
                <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:scale-110 transition-transform">
                    <x-icon name="clock" class="w-16 h-16"/>
                </div>
                <div class="relative z-10">
                    <p class="text-cyan-100 text-xs font-medium uppercase tracking-wider">Heures Sup.</p>
                    <p class="text-3xl font-bold mt-1" x-text="kpis.heures_supplementaires?.value || '0'"></p>
                    <p class="text-xs text-cyan-100 mt-2">
                        <span x-text="kpis.heures_supplementaires?.count || 0"></span> employés
                    </p>
                </div>
            </div>
        </div>

        {{-- Graphiques Row 1 --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fade-in-up animation-delay-300">
            {{-- Evolution presences (Line) - Takes 2 cols --}}
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <x-icon name="activity" class="w-4 h-4 text-gray-500"/>
                    Évolution des présences
                </h3>
                <div class="h-64 relative w-full">
                    <canvas id="presenceTrendChart"></canvas>
                </div>
            </div>

            {{-- Repartition departements (Donut) --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <x-icon name="pie-chart" class="w-4 h-4 text-gray-500"/>
                    Répartition
                </h3>
                <div class="h-64 relative w-full flex justify-center">
                    <canvas id="departmentChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Graphiques Row 2 --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 animate-fade-in-up animation-delay-400">
            {{-- Recrutements vs Departs --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <x-icon name="users" class="w-4 h-4 text-gray-500"/>
                    Recrutements vs Départs
                </h3>
                <div class="h-64 relative w-full">
                    <canvas id="recruitmentChart"></canvas>
                </div>
            </div>

            {{-- Absenteisme par service --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <x-icon name="user-x" class="w-4 h-4 text-gray-500"/>
                    Taux d'absentéisme par service
                </h3>
                <div class="h-64 relative w-full">
                    <canvas id="absenteismChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Heures par semaine (Area) --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 animate-fade-in-up animation-delay-400">
            <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <x-icon name="clock" class="w-4 h-4 text-gray-500"/>
                Heures travaillées (Total hebdomadaire)
            </h3>
            <div class="h-64 relative w-full">
                <canvas id="weeklyHoursChart"></canvas>
            </div>
        </div>

        {{-- Tableaux Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 animate-fade-in-up animation-delay-500">
            {{-- Activité récente --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h3 class="font-semibold text-gray-900">Activité Récente</h3>
                </div>
                <div class="divide-y divide-gray-100 max-h-80 overflow-y-auto">
                    <template x-for="(activity, index) in tables.activities" :key="index">
                        <div class="p-4 hover:bg-gray-50 transition-colors flex gap-3">
                            <div class="flex-shrink-0">
                                <template x-if="activity.avatar">
                                    <img :src="'/storage/' + activity.avatar" class="w-8 h-8 rounded-full object-cover">
                                </template>
                                <template x-if="!activity.avatar">
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 text-xs font-bold" x-text="activity.user.charAt(0)"></div>
                                </template>
                            </div>
                            <div>
                                <p class="text-sm text-gray-900">
                                    <span class="font-medium" x-text="activity.user"></span>
                                    <span class="text-gray-600" x-text="activity.description"></span>
                                </p>
                                <p class="text-xs text-gray-400 mt-1" x-text="activity.time"></p>
                            </div>
                        </div>
                    </template>
                    <div x-show="tables.activities.length === 0" class="p-8 text-center text-gray-500">Aucune activité récente</div>
                </div>
            </div>

            {{-- Demandes en attente --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h3 class="font-semibold text-gray-900">Demandes en attente</h3>
                    <span class="bg-yellow-100 text-yellow-700 text-xs font-bold px-2 py-0.5 rounded-full" x-show="tables.pending.length > 0" x-text="tables.pending.length"></span>
                </div>
                <div class="divide-y divide-gray-100 max-h-80 overflow-y-auto">
                    <template x-for="item in tables.pending" :key="item.id">
                        <div class="p-4 hover:bg-gray-50 flex justify-between items-center">
                            <div>
                                <p class="text-sm font-medium text-gray-900" x-text="item.user"></p>
                                <p class="text-sm text-gray-500" x-text="item.details"></p>
                                <p class="text-xs text-gray-400" x-text="item.date"></p>
                            </div>
                            <a :href="'/admin/leaves/' + item.id" class="text-blue-600 hover:bg-blue-50 p-2 rounded-lg text-sm">Voir</a>
                        </div>
                    </template>
                    <div x-show="tables.pending.length === 0" class="p-8 text-center text-gray-500">Aucune demande en attente</div>
                </div>
            </div>

            {{-- Alertes RH --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-semibold text-gray-900">Alertes RH</h3>
                </div>
                <div class="p-4 space-y-4">
                    {{-- Contracts --}}
                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Contrats expirant bientôt</h4>
                        <div class="space-y-2">
                            <template x-for="contract in tables.alerts.contracts" :key="contract.name">
                                <div class="flex justify-between items-center text-sm p-2 bg-red-50 rounded-lg text-red-700 border border-red-100">
                                    <span>
                                        <span class="font-medium" x-text="contract.name"></span>
                                        <span class="opacity-75" x-text="' (' + contract.department + ')'"></span>
                                    </span>
                                    <span class="font-bold whitespace-nowrap" x-text="'J-' + contract.days"></span>
                                </div>
                            </template>
                            <div x-show="!tables.alerts.contracts?.length" class="text-sm text-gray-400 italic">Aucune alerte contrat</div>
                        </div>
                    </div>

                    {{-- Birthdays --}}
                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Anniversaires à venir</h4>
                        <div class="space-y-2">
                            <template x-for="bd in tables.alerts.birthdays" :key="bd.name">
                                <div class="flex justify-between items-center text-sm p-2 bg-blue-50 rounded-lg text-blue-700 border border-blue-100">
                                    <span class="font-medium" x-text="bd.name"></span>
                                    <span>
                                        <span x-text="bd.date"></span>
                                        <span class="ml-1 opacity-75" x-text="'(' + bd.age + ' ans)'"></span>
                                    </span>
                                </div>
                            </template>
                            <div x-show="!tables.alerts.birthdays?.length" class="text-sm text-gray-400 italic">Aucun anniversaire proche</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Top Retardataires --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-semibold text-gray-900">Top Retards (Ce mois)</h3>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500">
                        <tr>
                            <th class="text-left py-2 px-4 font-medium">Employé</th>
                            <th class="text-center py-2 px-4 font-medium">Retards</th>
                            <th class="text-right py-2 px-4 font-medium">Moyenne</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="user in tables.latecomers" :key="user.user_id">
                            <tr>
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 text-xs font-bold" x-text="user.rank"></div>
                                        <div>
                                            <div class="font-medium text-gray-900" x-text="user.name"></div>
                                            <div class="text-xs text-gray-500" x-text="user.department"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-center font-bold text-gray-700" x-text="user.count"></td>
                                <td class="py-3 px-4 text-right text-red-600" x-text="user.avg_minutes + ' min'"></td>
                            </tr>
                        </template>
                        <tr x-show="!tables.latecomers?.length">
                            <td colspan="3" class="py-8 text-center text-gray-500">Aucun retard signalé ce mois-ci 🎉</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        function analyticsPage() {
            return {
                loading: false,
                filters: { period: 'month', department_id: '', contract_type: '', custom_month: '12', custom_year: '2025' },
                kpis: {},
                charts: {},
                tables: {
                    activities: [],
                    pending: [],
                    alerts: { contracts: [], birthdays: [] },
                    latecomers: []
                },
                chartInstances: {},

                init() {
                    this.loadData();
                },

                async loadData() {
                    this.loading = true;
                    const query = new URLSearchParams(this.filters).toString();
                    
                    try {
                        const [kpis, charts, activities, pending, alerts, latecomers] = await Promise.all([
                            fetch(`{{ route('admin.analytics.kpis') }}?${query}`).then(r => r.json()),
                            fetch(`{{ route('admin.analytics.charts') }}?${query}`).then(r => r.json()),
                            fetch(`{{ route('admin.analytics.activities') }}?${query}`).then(r => r.json()),
                            fetch(`{{ route('admin.analytics.pending') }}?${query}`).then(r => r.json()),
                            fetch(`{{ route('admin.analytics.alerts') }}?${query}`).then(r => r.json()),
                            fetch(`{{ route('admin.analytics.latecomers') }}?${query}`).then(r => r.json())
                        ]);

                        this.kpis = kpis;
                        this.charts = charts;
                        this.tables = { activities, pending, alerts, latecomers };
                        
                        this.$nextTick(() => {
                            this.updateCharts();
                        });
                    } catch (error) {
                        console.error('Error loading analytics:', error);
                        // Optional: Show error toast
                    } finally {
                        this.loading = false;
                    }
                },

                updateCharts() {
                    this.renderPresenceTrend();
                    this.renderDepartmentChart();
                    this.renderRecruitmentChart();
                    this.renderAbsenteismChart();
                    this.renderWeeklyHoursChart();
                },

                renderPresenceTrend() {
                    const ctx = document.getElementById('presenceTrendChart');
                    if (!ctx) return;
                    
                    if (this.chartInstances.presence) this.chartInstances.presence.destroy();

                    this.chartInstances.presence = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: this.charts.presence_trend?.labels || [],
                            datasets: [{
                                label: 'Présences',
                                data: this.charts.presence_trend?.data || [],
                                borderColor: '#4F46E5', // Indigo 600
                                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.4,
                                pointRadius: 3,
                                pointHoverRadius: 5
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: {
                                y: { beginAtZero: true, grid: { borderDash: [2, 4] } },
                                x: { grid: { display: false } }
                            }
                        }
                    });
                },

                renderDepartmentChart() {
                    const ctx = document.getElementById('departmentChart');
                    if (!ctx) return;
                    
                    if (this.chartInstances.department) this.chartInstances.department.destroy();

                    this.chartInstances.department = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: this.charts.department_distribution?.labels || [],
                            datasets: [{
                                data: this.charts.department_distribution?.data || [],
                                backgroundColor: this.charts.department_distribution?.colors || ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6'],
                                borderWidth: 0
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '70%',
                            plugins: {
                                legend: { position: 'right', labels: { usePointStyle: true, font: { size: 11 } } }
                            }
                        }
                    });
                },

                renderRecruitmentChart() {
                    const ctx = document.getElementById('recruitmentChart');
                    if (!ctx) return;
                    
                    if (this.chartInstances.recruitment) this.chartInstances.recruitment.destroy();

                    this.chartInstances.recruitment = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: this.charts.recruitment_turnover?.labels || [],
                            datasets: [
                                {
                                    label: 'Recrutements',
                                    data: this.charts.recruitment_turnover?.recrutements || [],
                                    backgroundColor: '#10B981',
                                    borderRadius: 4
                                },
                                {
                                    label: 'Départs',
                                    data: this.charts.recruitment_turnover?.departs || [],
                                    backgroundColor: '#EF4444',
                                    borderRadius: 4
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: { beginAtZero: true, ticks: { precision: 0 } },
                                x: { grid: { display: false } }
                            }
                        }
                    });
                },

                renderAbsenteismChart() {
                    const ctx = document.getElementById('absenteismChart');
                    if (!ctx) return;

                    if (this.chartInstances.absenteism) this.chartInstances.absenteism.destroy();

                    this.chartInstances.absenteism = new Chart(ctx, {
                        type: 'bar',
                        indexAxis: 'y',
                        data: {
                            labels: this.charts.absenteism_par_service?.labels || [],
                            datasets: [{
                                label: 'Taux (%)',
                                data: this.charts.absenteism_par_service?.rates || [],
                                backgroundColor: '#F59E0B',
                                borderRadius: 4,
                                barThickness: 20
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: { x: { beginAtZero: true, max: 100 } }
                        }
                    });
                },

                renderWeeklyHoursChart() {
                    const ctx = document.getElementById('weeklyHoursChart');
                    if (!ctx) return;

                    if (this.chartInstances.weekly) this.chartInstances.weekly.destroy();

                    this.chartInstances.weekly = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: this.charts.heures_travaillees_semaine?.labels || [],
                            datasets: [{
                                label: 'Heures',
                                data: this.charts.heures_travaillees_semaine?.data || [],
                                borderColor: '#0EA5E9',
                                backgroundColor: 'rgba(14, 165, 233, 0.2)',
                                fill: true,
                                tension: 0.4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: { y: { beginAtZero: true } }
                        }
                    });
                }
            }
        }
    </script>
    @endpush
</x-layouts.admin>
