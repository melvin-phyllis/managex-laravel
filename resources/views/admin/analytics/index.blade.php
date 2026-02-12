<x-layouts.admin>
    <div class="space-y-6" x-data="analyticsPage()">

        {{-- Header amélioré --}}
        <div class="relative overflow-hidden bg-gradient-to-r from-[#5680E9] to-[#84CEEB] rounded-2xl shadow-lg">
            <div class="absolute inset-0 bg-black/10"></div>
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            
            <div class="relative p-6 md:p-8 bg-blue-400/30">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-white flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                <x-icon name="bar-chart-2" class="w-6 h-6 text-white" />
                            </div>
                            Analytics RH
                        </h1>
                        <p class="text-white/80 mt-2">Tableau de bord de performance et statistiques en temps réel</p>
                        <div class="flex items-center gap-4 mt-3">
                            <span class="px-3 py-1 bg-white/20 backdrop-blur-sm text-white text-xs font-medium rounded-full">
                                Derniére mise à jour: <span x-text="lastUpdate">-</span>
                            </span>
                            <span class="px-3 py-1 bg-emerald-500/80 text-white text-xs font-medium rounded-full flex items-center gap-1" x-show="!loading">
                                <span class="w-2 h-2 bg-white rounded-full animate-pulse"></span>
                                En direct
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button @click="exportData('pdf')" class="px-4 py-2.5 bg-white/20 backdrop-blur-sm text-white font-medium rounded-xl hover:bg-white/30 transition-all flex items-center">
                            <x-icon name="file-text" class="w-4 h-4 mr-2"/>
                            Export PDF
                        </button>
                        <button @click="exportData('excel')" class="px-4 py-2.5 bg-emerald-500/80 backdrop-blur-sm text-white font-medium rounded-xl hover:bg-emerald-600 transition-all flex items-center">
                            <x-icon name="table" class="w-4 h-4 mr-2"/>
                            Export Excel
                        </button>
                        <button @click="loadData()" class="px-4 py-2.5 bg-white text-indigo-700 font-semibold rounded-xl hover:bg-indigo-50 transition-all shadow-lg flex items-center">
                            <x-icon name="refresh-cw" class="w-4 h-4 mr-2" x-bind:class="{'animate-spin': loading}"/>
                            Actualiser
                        </button>
                    </div>
                </div>
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

        {{-- KPI Cards - Ligne 1 (Principaux) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 animate-fade-in-up animation-delay-200">
            {{-- 1. Effectif total --}}
            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition-all">
                <div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-br from-blue-500/10 to-indigo-500/10 rounded-full -mr-8 -mt-8 group-hover:scale-125 transition-transform"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/30">
                            <x-icon name="users" class="w-6 h-6 text-white"/>
                        </div>
                        <div class="flex items-center px-2 py-1 rounded-full text-xs font-semibold" 
                             :class="kpis.effectif_total?.variation >= 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'">
                            <x-icon name="trending-up" class="w-3 h-3 mr-1" x-show="kpis.effectif_total?.variation >= 0"/>
                            <x-icon name="trending-down" class="w-3 h-3 mr-1" x-show="kpis.effectif_total?.variation < 0"/>
                            <span x-text="(kpis.effectif_total?.variation > 0 ? '+' : '') + (kpis.effectif_total?.variation || 0) + '%'"></span>
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-gray-900 mt-4" x-text="kpis.effectif_total?.value || '-'"></p>
                    <p class="text-sm text-gray-500 mt-1">Effectif total</p>
                </div>
            </div>

            {{-- 2. Taux de présence --}}
            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition-all">
                <div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-br from-emerald-500/10 to-teal-500/10 rounded-full -mr-8 -mt-8 group-hover:scale-125 transition-transform"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between">
                        <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/30">
                            <x-icon name="check-circle" class="w-6 h-6 text-white"/>
                        </div>
                        <span class="text-xs font-medium text-gray-500" x-text="(kpis.presents_today?.value || 0) + '/' + (kpis.presents_today?.expected || 0)"></span>
                    </div>
                    <p class="text-3xl font-bold text-gray-900 mt-4"><span x-text="kpis.presents_today?.percentage || '0'"></span>%</p>
                    <p class="text-sm text-gray-500 mt-1">Taux de présence</p>
                    <div class="w-full bg-gray-200 rounded-full h-2 mt-3">
                        <div class="bg-gradient-to-r from-emerald-500 to-teal-500 h-2 rounded-full transition-all duration-1000" :style="'width: ' + (kpis.presents_today?.percentage || 0) + '%'"></div>
                    </div>
                </div>
            </div>

            {{-- 3. Turnover --}}
            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition-all">
                <div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-br from-amber-500/10 to-orange-500/10 rounded-full -mr-8 -mt-8 group-hover:scale-125 transition-transform"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between">
                        <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg shadow-amber-500/30">
                            <x-icon name="repeat" class="w-6 h-6 text-white"/>
                        </div>
                        <span class="text-xs font-medium px-2 py-1 rounded-full" 
                              :class="(kpis.turnover?.rate || 0) > 10 ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700'"
                              x-text="(kpis.turnover?.rate || 0) > 10 ? 'élevé' : 'Normal'"></span>
                    </div>
                    <p class="text-3xl font-bold text-gray-900 mt-4"><span x-text="kpis.turnover?.rate || '0'"></span>%</p>
                    <p class="text-sm text-gray-500 mt-1">Taux de turnover</p>
                    <p class="text-xs text-gray-400 mt-1">
                        <span class="text-emerald-600" x-text="'+' + (kpis.turnover?.entries || 0)"></span> entrées / 
                        <span class="text-red-600" x-text="'-' + (kpis.turnover?.exits || 0)"></span> sorties
                    </p>
                </div>
            </div>

            {{-- 4. Masse salariale --}}
            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition-all">
                <div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-br from-violet-500/10 to-purple-500/10 rounded-full -mr-8 -mt-8 group-hover:scale-125 transition-transform"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between">
                        <div class="w-12 h-12 bg-gradient-to-br from-violet-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg shadow-violet-500/30">
                            <x-icon name="dollar-sign" class="w-6 h-6 text-white"/>
                        </div>
                        <div class="flex items-center px-2 py-1 rounded-full text-xs font-semibold"
                             :class="kpis.masse_salariale?.variation >= 0 ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700'">
                            <span x-text="(kpis.masse_salariale?.variation > 0 ? '+' : '') + (kpis.masse_salariale?.variation || 0) + '%'"></span>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-900 mt-4 truncate" x-text="kpis.masse_salariale?.formatted || '0 FCFA'"></p>
                    <p class="text-sm text-gray-500 mt-1">Masse salariale</p>
                </div>
            </div>
        </div>

{{-- Secondary KPIs Removed --}}

{{-- Evaluation Stats Removed --}}



        {{-- Graphiques Row 1 --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fade-in-up animation-delay-400">
            {{-- Evolution presences (Line) - Takes 2 cols --}}
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <x-icon name="activity" class="w-4 h-4 text-indigo-600"/>
                        </div>
                        évolution des présences
                    </h3>
                    <div class="flex items-center gap-2">
                        <span class="flex items-center text-xs text-gray-500">
                            <span class="w-3 h-3 rounded-full bg-indigo-500 mr-1"></span>
                            Présences
                        </span>
                        <span class="flex items-center text-xs text-gray-500">
                            <span class="w-3 h-3 rounded-full bg-emerald-500 mr-1"></span>
                            Objectif
                        </span>
                    </div>
                </div>
                <div class="h-72 relative w-full">
                    <canvas id="presenceTrendChart"></canvas>
                </div>
            </div>

            {{-- Repartition departements (Donut) --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <x-icon name="pie-chart" class="w-4 h-4 text-purple-600"/>
                    </div>
                    Répartition par département
                </h3>
                <div class="h-72 relative w-full flex justify-center">
                    <canvas id="departmentChart"></canvas>
                </div>
            </div>
        </div>

{{-- Charts Row 2 Removed --}}

{{-- Charts Row 3 Removed --}}

        {{-- Heures par semaine (Area) --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 animate-fade-in-up animation-delay-550">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                    <div class="w-8 h-8 bg-cyan-100 rounded-lg flex items-center justify-center">
                        <x-icon name="clock" class="w-4 h-4 text-cyan-600"/>
                    </div>
                    Heures travaillées (5 derniéres semaines)
                </h3>
                <div class="text-sm text-gray-500">
                    Total: <span class="font-bold text-gray-900" x-text="charts.heures_travaillees_semaine?.total || 0"></span>h
                </div>
            </div>
            <div class="h-64 relative w-full">
                <canvas id="weeklyHoursChart"></canvas>
            </div>
        </div>

        {{-- Section Stagiaires --}}
        <h2 class="text-xl font-bold text-gray-800 mt-8 mb-4 flex items-center gap-2">
            <div class="w-8 h-8 bg-pink-100 rounded-lg flex items-center justify-center">
                <x-icon name="users" class="w-4 h-4 text-pink-600"/>
            </div>
            Statistiques Stagiaires
        </h2>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fade-in-up animation-delay-600">
            {{-- Evolution Stagiaires (Bar) --}}
            <div class="lg:col-span-1 bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-900 mb-4">Recrutements vs Fins de stage</h3>
                <div class="h-64 relative w-full">
                    <canvas id="internEvolutionChart"></canvas>
                </div>
            </div>

            {{-- Performance (Radar) --}}
            <div class="lg:col-span-1 bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-900 mb-4">Performance Moyenne</h3>
                <div class="h-64 relative w-full flex justify-center">
                    <canvas id="internPerformanceChart"></canvas>
                </div>
            </div>

            {{-- Répartition (Doughnut) --}}
            <div class="lg:col-span-1 bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-900 mb-4">Répartition par Département</h3>
                <div class="h-64 relative w-full flex justify-center">
                    <canvas id="internDepartmentChart"></canvas>
                </div>
            </div>

        {{-- Section Tâches --}}
        <h2 class="text-xl font-bold text-gray-800 mt-8 mb-4 flex items-center gap-2">
            <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                <x-icon name="check-square" class="w-4 h-4 text-amber-600"/>
            </div>
            Statistiques Tâches
        </h2>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fade-in-up animation-delay-700">
            {{-- Status Distribution (Donut) --}}
            <div class="lg:col-span-1 bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-900 mb-4">État des tâches</h3>
                <div class="h-64 relative w-full flex justify-center">
                    <canvas id="taskStatusChart"></canvas>
                </div>
            </div>

            {{-- Completion Evolution (Line) --}}
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-900 mb-4">Tâches créées vs terminées (6 mois)</h3>
                <div class="h-64 relative w-full">
                    <canvas id="taskCompletionChart"></canvas>
                </div>
            </div>

            {{-- Workload by Dept (Bar) --}}
             <div class="lg:col-span-3 bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-900 mb-4">Charge de travail par département (Tâches actives)</h3>
                <div class="h-64 relative w-full">
                    <canvas id="taskDepartmentChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Section Démographie --}}
        <h2 class="text-xl font-bold text-gray-800 mt-8 mb-4 flex items-center gap-2">
            <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                <x-icon name="users" class="w-4 h-4 text-indigo-600"/>
            </div>
            Démographie & Carrière
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 animate-fade-in-up animation-delay-800 mb-8">
            {{-- Gender (Pie) --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-900 mb-4">Parité (H/F)</h3>
                <div class="h-64 relative w-full flex justify-center">
                    <canvas id="genderChart"></canvas>
                </div>
            </div>

            {{-- Age Pyramid (Bar/Horizontal) --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-900 mb-4">Pyramide des âges</h3>
                <div class="h-64 relative w-full">
                    <canvas id="ageChart"></canvas>
                </div>
            </div>

            {{-- Seniority (Bar) --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-900 mb-4">Ancienneté</h3>
                <div class="h-64 relative w-full">
                    <canvas id="seniorityChart"></canvas>
                </div>
            </div>
        </div>
        </div>

{{-- AI Analysis Removed --}}

{{-- Insights Removed --}}


    </div>

    @push('scripts')
    <script nonce="{{ $cspNonce ?? '' }}" src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script nonce="{{ $cspNonce ?? '' }}">
        function analyticsPage() {
            return {
                loading: false,
                lastUpdate: '-',
                filters: { period: 'month', department_id: '', contract_type: '', custom_month: new Date().getMonth() + 1, custom_year: new Date().getFullYear() },
                kpis: {},
                charts: {},
                tables: {
                    activities: [],
                    pending: [],
                    alerts: { contracts: [], birthdays: [] },
                    latecomers: [],
                    topPerformers: { employees: [], interns: [] },
                    bestAttendance: [],
                    evaluationStats: { employees: {}, interns: {} }
                },
                chartInstances: {},
                aiInsights: { available: false, loading: false, content: null, error: null },

                init() {
                    this.loadData();
                    // Auto-refresh toutes les 5 minutes
                    setInterval(() => this.loadData(), 300000);
                },

                async loadData() {
                    this.loading = true;
                    const query = new URLSearchParams(this.filters).toString();
                    
                    try {
                        const [kpis, charts, activities, pending, alerts, latecomers, topPerformers, bestAttendance, evaluationStats] = await Promise.all([
                            fetch(`{{ route('admin.analytics.kpis') }}?${query}`).then(r => r.json()),
                            fetch(`{{ route('admin.analytics.charts') }}?${query}`).then(r => r.json()),
                            fetch(`{{ route('admin.analytics.activities') }}?${query}`).then(r => r.json()),
                            fetch(`{{ route('admin.analytics.pending') }}?${query}`).then(r => r.json()),
                            fetch(`{{ route('admin.analytics.alerts') }}?${query}`).then(r => r.json()),
                            fetch(`{{ route('admin.analytics.latecomers') }}?${query}`).then(r => r.json()),
                            fetch(`{{ route('admin.analytics.top-performers') }}?${query}`).then(r => r.json()),
                            fetch(`{{ route('admin.analytics.best-attendance') }}?${query}`).then(r => r.json()),
                            fetch(`{{ route('admin.analytics.evaluation-stats') }}?${query}`).then(r => r.json())
                        ]);

                        this.kpis = kpis;
                        this.charts = charts;
                        this.tables = { activities, pending, alerts, latecomers, topPerformers, bestAttendance, evaluationStats };
                        this.lastUpdate = new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
                        
                        this.$nextTick(() => {
                            this.updateCharts();
                        });

                        this.loadAiInsights();
                    } catch (error) {
                        console.error('Error loading analytics:', error);
                    } finally {
                        this.loading = false;
                    }
                },

                exportData(format) {
                    const queryParams = new URLSearchParams(this.filters).toString();
                    let url, filename, type;
                    
                    if (format === 'pdf') {
                        url = `{{ route('admin.analytics.export.pdf') }}?${queryParams}`;
                        filename = 'rapport-analytics.pdf';
                        type = 'pdf';
                    } else if (format === 'excel') {
                        url = `{{ route('admin.analytics.export.excel') }}?${queryParams}`;
                        filename = 'rapport-analytics.xlsx';
                        type = 'excel';
                    }
                    
                    // Utiliser l'overlay de téléchargement
                    window.dispatchEvent(new CustomEvent('start-download', {
                        detail: { url, filename, type }
                    }));
                },

                updateCharts() {
                    this.renderPresenceTrend();
                    this.renderDepartmentChart();
                    this.renderWeeklyHoursChart();
                    this.renderInternCharts();
                    this.renderTaskCharts();
                    this.renderEmployeeCharts();
                },

                renderPresenceTrend() {
                    const ctx = document.getElementById('presenceTrendChart');
                    if (!ctx || !ctx.getContext) return;
                    
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
                    if (!ctx || !ctx.getContext) return;
                    
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





                renderWeeklyHoursChart() {
                    const ctx = document.getElementById('weeklyHoursChart');
                    if (!ctx || !ctx.getContext) return;

                    if (this.chartInstances.weekly) this.chartInstances.weekly.destroy();

                    this.chartInstances.weekly = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: this.charts.heures_travaillees_semaine?.labels || [],
                            datasets: [{
                                label: 'Heures',
                                data: this.charts.heures_travaillees_semaine?.data || [],
                                borderColor: '#0EA5E9',
                                backgroundColor: 'rgba(14, 165, 233, 0.15)',
                                fill: true,
                                tension: 0.4,
                                borderWidth: 3,
                                pointBackgroundColor: '#0EA5E9',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                pointRadius: 5,
                                pointHoverRadius: 7
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

                renderInternCharts() {
                    // 1. Evolution
                    const ctxEvo = document.getElementById('internEvolutionChart');
                    if (ctxEvo && ctxEvo.getContext) {
                        if (this.chartInstances.internEvolution) this.chartInstances.internEvolution.destroy();
                        this.chartInstances.internEvolution = new Chart(ctxEvo, {
                            type: 'bar',
                            data: {
                                labels: this.charts.intern_evolution?.labels || [],
                                datasets: [
                                    {
                                        label: 'Nouveaux',
                                        data: this.charts.intern_evolution?.new || [],
                                        backgroundColor: '#10B981',
                                        borderRadius: 4
                                    },
                                    {
                                        label: 'Fins de stage',
                                        data: this.charts.intern_evolution?.ended || [],
                                        backgroundColor: '#EF4444',
                                        borderRadius: 4
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } }, x: { grid: { display: false } } }
                            }
                        });
                    }

                    // 2. Performance (Radar)
                    const ctxPerf = document.getElementById('internPerformanceChart');
                    if (ctxPerf && ctxPerf.getContext) {
                        if (this.chartInstances.internPerformance) this.chartInstances.internPerformance.destroy();
                        this.chartInstances.internPerformance = new Chart(ctxPerf, {
                            type: 'radar',
                            data: {
                                labels: this.charts.intern_performance?.labels || [],
                                datasets: [{
                                    label: 'Moyenne',
                                    data: this.charts.intern_performance?.data || [],
                                    borderColor: '#8B5CF6',
                                    backgroundColor: 'rgba(139, 92, 246, 0.2)',
                                    pointBackgroundColor: '#8B5CF6',
                                    pointBorderColor: '#fff',
                                    pointHoverBackgroundColor: '#fff',
                                    pointHoverBorderColor: '#8B5CF6'
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: { r: { min: 0, max: 10, beginAtZero: true, ticks: { stepSize: 2 } } },
                                plugins: { legend: { display: false } }
                            }
                        });
                    }

                    // 3. Department Distribution
                    const ctxDept = document.getElementById('internDepartmentChart');
                    if (ctxDept && ctxDept.getContext) {
                        if (this.chartInstances.internDept) this.chartInstances.internDept.destroy();
                        this.chartInstances.internDept = new Chart(ctxDept, {
                            type: 'doughnut',
                            data: {
                                labels: this.charts.intern_department_distribution?.labels || [],
                                datasets: [{
                                    data: this.charts.intern_department_distribution?.data || [],
                                    backgroundColor: this.charts.intern_department_distribution?.colors || ['#6366F1', '#EC4899', '#8B5CF6', '#10B981', '#F59E0B'],
                                    borderWidth: 0
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                cutout: '65%',
                                plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 6 } } }
                            }
                        });
                    }
                },

                renderTaskCharts() {
                    // 1. Status
                    const ctxStatus = document.getElementById('taskStatusChart');
                    if (ctxStatus && ctxStatus.getContext) {
                        if (this.chartInstances.taskStatus) this.chartInstances.taskStatus.destroy();
                        this.chartInstances.taskStatus = new Chart(ctxStatus, {
                            type: 'doughnut',
                            data: {
                                labels: this.charts.task_status_distribution?.labels || [],
                                datasets: [{
                                    data: this.charts.task_status_distribution?.data || [],
                                    backgroundColor: this.charts.task_status_distribution?.colors || [],
                                    borderWidth: 0
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                cutout: '60%',
                                plugins: { legend: { position: 'right', labels: { usePointStyle: true, boxWidth: 6 } } }
                            }
                        });
                    }

                    // 2. Completion
                    const ctxComp = document.getElementById('taskCompletionChart');
                    if (ctxComp && ctxComp.getContext) {
                        if (this.chartInstances.taskComp) this.chartInstances.taskComp.destroy();
                        this.chartInstances.taskComp = new Chart(ctxComp, {
                            type: 'line',
                            data: {
                                labels: this.charts.task_completion_evolution?.labels || [],
                                datasets: [
                                    {
                                        label: 'Terminées',
                                        data: this.charts.task_completion_evolution?.completed || [],
                                        borderColor: '#10B981',
                                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                        fill: true,
                                        tension: 0.4
                                    },
                                    {
                                        label: 'Créées',
                                        data: this.charts.task_completion_evolution?.created || [],
                                        borderColor: '#6366F1',
                                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                                        fill: true,
                                        tension: 0.4
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: { y: { beginAtZero: true }, x: { grid: { display: false } } }
                            }
                        });
                    }

                    // 3. Department
                    const ctxDept = document.getElementById('taskDepartmentChart');
                    if (ctxDept && ctxDept.getContext) {
                        if (this.chartInstances.taskDept) this.chartInstances.taskDept.destroy();
                        this.chartInstances.taskDept = new Chart(ctxDept, {
                            type: 'bar',
                            data: {
                                labels: this.charts.tasks_by_department?.map(d => d.label) || [],
                                datasets: [{
                                    label: 'Tâches actives',
                                    data: this.charts.tasks_by_department?.map(d => d.value) || [],
                                    backgroundColor: '#F59E0B',
                                    borderRadius: 4
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: { y: { beginAtZero: true }, x: { grid: { display: false } } },
                                indexAxis: 'y'
                            }
                        });
                    }
                },

                renderEmployeeCharts() {
                    // 1. Gender
                    const ctxGender = document.getElementById('genderChart');
                    if (ctxGender && ctxGender.getContext) {
                        if (this.chartInstances.gender) this.chartInstances.gender.destroy();
                        this.chartInstances.gender = new Chart(ctxGender, {
                            type: 'pie',
                            data: {
                                labels: this.charts.gender_distribution?.map(d => d.label) || [],
                                datasets: [{
                                    data: this.charts.gender_distribution?.map(d => d.value) || [],
                                    backgroundColor: ['#3B82F6', '#EC4899', '#9CA3AF'],
                                    borderWidth: 0
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { position: 'bottom', labels: { usePointStyle: true } } }
                            }
                        });
                    }

                    // 2. Age
                    const ctxAge = document.getElementById('ageChart');
                    if (ctxAge && ctxAge.getContext) {
                        if (this.chartInstances.age) this.chartInstances.age.destroy();
                        this.chartInstances.age = new Chart(ctxAge, {
                            type: 'bar',
                            data: {
                                labels: this.charts.age_pyramid?.labels || [],
                                datasets: [{
                                    label: 'Moyenne d\'âge',
                                    data: this.charts.age_pyramid?.data || [],
                                    backgroundColor: '#8B5CF6',
                                    borderRadius: 4
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: { y: { beginAtZero: true }, x: { grid: { display: false } } }
                            }
                        });
                    }

                    // 3. Seniority
                    const ctxSen = document.getElementById('seniorityChart');
                    if (ctxSen && ctxSen.getContext) {
                        if (this.chartInstances.seniority) this.chartInstances.seniority.destroy();
                        this.chartInstances.seniority = new Chart(ctxSen, {
                            type: 'bar',
                            data: {
                                labels: this.charts.seniority_distribution?.labels || [],
                                datasets: [{
                                    label: 'Employés',
                                    data: this.charts.seniority_distribution?.data || [],
                                    backgroundColor: '#10B981',
                                    borderRadius: 4
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: { y: { beginAtZero: true }, x: { grid: { display: false } } }
                            }
                        });
                    }
                }, // End renderEmployeeCharts
            } // End returned object
        } // End analyticsPage
    </script>
    @endpush
</x-layouts.admin>
