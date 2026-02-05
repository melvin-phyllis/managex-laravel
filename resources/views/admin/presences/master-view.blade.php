<x-layouts.admin>
    <div class="space-y-6" x-data="masterViewPage()" x-init="init()">
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
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Présences</span>
                    </div>
                </li>
            </ol>
        </nav>

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-lg" style="background: linear-gradient(135deg, #5680E9, #84CEEB) !important; box-shadow: 0 10px 15px -3px rgba(86, 128, 233, 0.3) !important;">
                        <x-icon name="users" class="w-5 h-5 text-white" />
                    </div>
                    Suivi des Présences
                </h1>
                <p class="text-gray-500 mt-1 ml-13">
                    <span x-show="mode === 'today'" x-text="'Pointages du ' + data.date"></span>
                    <span x-show="mode === 'historical'" x-text="'Période : ' + data.start_date + ' - ' + data.end_date"></span>
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-400" x-show="loading">
                    <x-icon name="loader" class="w-4 h-4 animate-spin inline"/>
                    Chargement...
                </span>
                <span class="text-xs text-gray-400" x-show="mode === 'today' && !loading" x-text="'Actualisation auto: ' + autoRefreshCountdown + 's'"></span>
                <button @click="loadData()" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors flex items-center shadow-sm">
                    <x-icon name="refresh-cw" class="w-4 h-4 mr-2" x-bind:class="loading ? 'animate-spin' : ''"/>
                    Actualiser
                </button>
            </div>
        </div>

        {{-- Mode Toggle + Filtres --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex flex-wrap items-center gap-4">
                {{-- Mode Toggle --}}
                <div class="flex bg-gray-100 rounded-lg p-1">
                    <button @click="switchMode('today')" 
                        x-bind:class="mode === 'today' ? 'bg-white shadow-sm text-indigo-600' : 'text-gray-600 hover:text-gray-900'"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-all flex items-center gap-2">
                        <x-icon name="clock" class="w-4 h-4"/>
                        Aujourd'hui
                    </button>
                    <button @click="switchMode('historical')"
                        x-bind:class="mode === 'historical' ? 'bg-white shadow-sm text-indigo-600' : 'text-gray-600 hover:text-gray-900'"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-all flex items-center gap-2">
                        <x-icon name="calendar" class="w-4 h-4"/>
                        Historique
                    </button>
                </div>

                {{-- Filtres Historique --}}
                <template x-if="mode === 'historical'">
                    <div class="flex flex-wrap items-center gap-3">
                        <select x-model="filters.period" @change="loadData()" class="rounded-lg border-gray-300 text-sm">
                            <option value="week">Cette semaine</option>
                            <option value="month">Ce mois</option>
                            <option value="quarter">Ce trimestre</option>
                            <option value="custom">Personnalisée</option>
                        </select>

                        <template x-if="filters.period === 'custom'">
                            <div class="flex items-center gap-2">
                                <input type="date" x-model="filters.start_date" @change="loadData()" class="rounded-lg border-gray-300 text-sm">
                                <span class="text-gray-400"></span>
                                <input type="date" x-model="filters.end_date" @change="loadData()" class="rounded-lg border-gray-300 text-sm">
                            </div>
                        </template>
                    </div>
                </template>

                {{-- Département --}}
                <select x-model="filters.department_id" @change="loadData()" class="rounded-lg border-gray-300 text-sm">
                    <option value="">Tous les départements</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>

                {{-- Filtre é€ risque (mode historique) --}}
                <template x-if="mode === 'historical'">
                    <label class="flex items-center gap-2 cursor-pointer bg-red-50 text-red-700 px-3 py-2 rounded-lg border border-red-200 hover:bg-red-100 transition-colors">
                        <input type="checkbox" x-model="filters.risk_only" @change="loadData()" class="rounded border-red-300 text-red-600">
                        <x-icon name="alert-triangle" class="w-4 h-4"/>
                        <span class="text-sm font-medium">é€ risque</span>
                    </label>
                </template>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="rounded-xl p-4 text-white shadow-lg" style="background: linear-gradient(135deg, #5680E9, #84CEEB) !important;">
                <p class="text-white/80 text-xs font-medium uppercase">Total</p>
                <p class="text-3xl font-bold mt-1" x-text="data.stats?.total || 0"></p>
            </div>
            <div class="rounded-xl p-4 text-white shadow-lg" style="background: linear-gradient(135deg, #5AB9EA, #5680E9) !important;">
                <p class="text-white/80 text-xs font-medium uppercase" x-text="mode === 'today' ? 'Présents' : 'Performants'"></p>
                <p class="text-3xl font-bold mt-1" x-text="data.stats?.present || 0"></p>
            </div>
            <div class="rounded-xl p-4 text-white shadow-lg" style="background: linear-gradient(135deg, #84CEEB, #5AB9EA) !important;">
                <p class="text-white/80 text-xs font-medium uppercase" x-text="mode === 'today' ? 'En retard' : 'é€ surveiller'"></p>
                <p class="text-3xl font-bold mt-1" x-text="data.stats?.late || 0"></p>
            </div>
            <div class="rounded-xl p-4 text-white shadow-lg" style="background: linear-gradient(135deg, #8860D0, #5680E9) !important;">
                <p class="text-white/80 text-xs font-medium uppercase" x-text="mode === 'today' ? 'Absents' : 'é€ risque'"></p>
                <p class="text-3xl font-bold mt-1" x-text="data.stats?.absent || 0"></p>
            </div>
        </div>

        {{-- Tableau --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        {{-- Mode Aujourd'hui --}}
                        <template x-if="mode === 'today'">
                            <tr>
                                <th class="text-left py-4 px-5 font-semibold text-gray-700 text-sm">Employé</th>
                                <th class="text-left py-4 px-5 font-semibold text-gray-700 text-sm">Département</th>
                                <th class="text-center py-4 px-5 font-semibold text-gray-700 text-sm">Arrivée</th>
                                <th class="text-center py-4 px-5 font-semibold text-gray-700 text-sm">Départ</th>
                                <th class="text-center py-4 px-5 font-semibold text-gray-700 text-sm">Statut</th>
                                <th class="text-center py-4 px-5 font-semibold text-gray-700 text-sm">Retard</th>
                            </tr>
                        </template>
                        {{-- Mode Historique --}}
                        <template x-if="mode === 'historical'">
                            <tr>
                                <th class="text-left py-4 px-5 font-semibold text-gray-700 text-sm">Employé</th>
                                <th class="text-left py-4 px-5 font-semibold text-gray-700 text-sm">Département</th>
                                <th class="text-center py-4 px-5 font-semibold text-gray-700 text-sm">Taux</th>
                                <th class="text-center py-4 px-5 font-semibold text-gray-700 text-sm">Retards</th>
                                <th class="text-center py-4 px-5 font-semibold text-gray-700 text-sm">Impact</th>
                                <th class="text-center py-4 px-5 font-semibold text-gray-700 text-sm">Statut</th>
                                <th class="text-center py-4 px-5 font-semibold text-gray-700 text-sm w-10"></th>
                            </tr>
                        </template>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="emp in data.employees" :key="emp.id">
                            <tr class="hover:bg-gray-50 transition-colors">
                                {{-- Employé (commun aux deux modes) --}}
                                <td class="py-4 px-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm"
                                            style="background: linear-gradient(135deg, #5680E9, #84CEEB) !important;"
                                            x-text="emp.name.substring(0,2).toUpperCase()"></div>
                                        <div>
                                            <p class="font-medium text-gray-900" x-text="emp.name"></p>
                                            <p class="text-xs text-gray-500" x-text="emp.position || '-'"></p>
                                        </div>
                                    </div>
                                </td>
                                
                                {{-- Département (commun) --}}
                                <td class="py-4 px-5">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700"
                                        x-text="emp.department || '-'"></span>
                                </td>

                                {{-- Mode Aujourd'hui: Arrivée --}}
                                <td x-show="mode === 'today'" class="py-4 px-5 text-center">
                                    <span class="font-mono text-sm" x-bind:class="emp.check_in ? 'text-gray-900' : 'text-gray-400'" x-text="emp.check_in || '-'"></span>
                                </td>
                                
                                {{-- Mode Aujourd'hui: Départ --}}
                                <td x-show="mode === 'today'" class="py-4 px-5 text-center">
                                    <span class="font-mono text-sm" x-bind:class="emp.check_out ? 'text-gray-900' : 'text-gray-400'" x-text="emp.check_out || '-'"></span>
                                </td>
                                
                                {{-- Mode Aujourd'hui: Statut --}}
                                <td x-show="mode === 'today'" class="py-4 px-5 text-center">
                                    <span x-show="emp.status === 'present'" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold text-[#5680E9]" style="background-color: #5AB9EA20;">
                                        âœ“ Présent
                                    </span>
                                    <span x-show="emp.status === 'late'" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold text-[#5680E9]" style="background-color: #84CEEB30;">
                                         En retard
                                    </span>
                                    <span x-show="emp.status === 'absent'" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold text-[#8860D0]" style="background-color: #8860D020;">
                                        âœ• Absent
                                    </span>
                                </td>
                                
                                {{-- Mode Aujourd'hui: Retard --}}
                                <td x-show="mode === 'today'" class="py-4 px-5 text-center">
                                    <span x-show="emp.late_minutes > 0" class="font-medium text-red-600" x-text="'+' + emp.late_minutes + ' min'"></span>
                                    <span x-show="!emp.late_minutes" class="text-gray-400">-</span>
                                </td>

                                {{-- Mode Historique: Taux --}}
                                <td x-show="mode === 'historical'" class="py-4 px-5">
                                    <div class="flex flex-col items-center">
                                        <div class="relative w-12 h-12">
                                            <svg class="w-12 h-12 transform -rotate-90" viewBox="0 0 36 36">
                                                <circle cx="18" cy="18" r="16" fill="none" class="stroke-gray-200" stroke-width="3"></circle>
                                                <circle cx="18" cy="18" r="16" fill="none" 
                                                    x-bind:class="emp.attendance_rate >= 95 ? 'stroke-green-500' : (emp.attendance_rate >= 80 ? 'stroke-amber-500' : 'stroke-red-500')" 
                                                    stroke-width="3" 
                                                    x-bind:stroke-dasharray="emp.attendance_rate + ', 100'"
                                                    stroke-linecap="round"></circle>
                                            </svg>
                                            <span class="absolute inset-0 flex items-center justify-center text-[10px] font-bold"
                                                x-bind:class="emp.attendance_rate >= 95 ? 'text-green-600' : (emp.attendance_rate >= 80 ? 'text-amber-600' : 'text-red-600')"
                                                x-text="emp.attendance_rate + '%'"></span>
                                        </div>
                                    </div>
                                </td>
                                
                                {{-- Mode Historique: Retards --}}
                                <td x-show="mode === 'historical'" class="py-4 px-5 text-center">
                                    <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-sm font-bold"
                                        x-bind:class="emp.late_count === 0 ? 'bg-green-100 text-green-700' : (emp.late_count <= 5 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700')">
                                        â± <span x-text="emp.late_count"></span>
                                    </span>
                                </td>
                                
                                {{-- Mode Historique: Impact --}}
                                <td x-show="mode === 'historical'" class="py-4 px-5 text-center">
                                    <span class="font-medium" 
                                        x-bind:class="emp.late_count === 0 ? 'text-green-600' : (emp.late_count <= 5 ? 'text-amber-600' : 'text-red-600')"
                                        x-text="emp.late_impact"></span>
                                </td>
                                
                                {{-- Mode Historique: Statut risque --}}
                                <td x-show="mode === 'historical'" class="py-4 px-5 text-center">
                                    <span x-show="emp.risk_level === 'low'" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold text-[#5680E9]" style="background-color: #5AB9EA20;">
                                        âœ“ OK
                                    </span>
                                    <span x-show="emp.risk_level === 'medium'" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold text-[#5680E9]" style="background-color: #84CEEB30;">
                                        âš  Surveiller
                                    </span>
                                    <span x-show="emp.risk_level === 'high'" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold text-[#8860D0]" style="background-color: #8860D020;">
                                        âš  Risque
                                    </span>
                                </td>
                                
                                {{-- Mode Historique: Bouton détails --}}
                                <td x-show="mode === 'historical'" class="py-4 px-5 text-center">
                                    <a :href="'/admin/presences/employee/' + emp.id + '?period=' + filters.period + (filters.period === 'custom' ? '&start_date=' + filters.start_date + '&end_date=' + filters.end_date : '')" 
                                       class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium text-indigo-600 hover:text-white bg-indigo-50 hover:bg-indigo-600 rounded-lg transition-colors">
                                        Détails 
                                    </a>
                                </td>
                            </tr>
                        </template>

                        {{-- Empty state --}}
                        <tr x-show="!data.employees?.length && !loading">
                            <td colspan="7" class="py-12 text-center text-gray-500">
                                <p class="text-4xl mb-3">ðŸ‘¥</p>
                                <p>Aucun employé trouvé</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script nonce="{{ $cspNonce ?? '' }}">
        function masterViewPage() {
            return {
                mode: 'today',
                loading: false,
                loadingDetails: false,
                data: { stats: {}, employees: [] },
                filters: {
                    period: 'month',
                    department_id: '',
                    risk_only: false,
                    start_date: '',
                    end_date: ''
                },
                expandedEmployee: null,
                employeeDetails: {},
                autoRefreshInterval: null,
                autoRefreshCountdown: 30,

                init() {
                    this.loadData();
                    this.startAutoRefresh();
                },

                switchMode(newMode) {
                    this.mode = newMode;
                    this.expandedEmployee = null;
                    this.employeeDetails = {};
                    this.loadData();
                    
                    if (newMode === 'today') {
                        this.startAutoRefresh();
                    } else {
                        this.stopAutoRefresh();
                    }
                },

                startAutoRefresh() {
                    this.stopAutoRefresh();
                    this.autoRefreshCountdown = 30;
                    this.autoRefreshInterval = setInterval(() => {
                        this.autoRefreshCountdown--;
                        if (this.autoRefreshCountdown <= 0) {
                            this.loadData();
                            this.autoRefreshCountdown = 30;
                        }
                    }, 1000);
                },

                stopAutoRefresh() {
                    if (this.autoRefreshInterval) {
                        clearInterval(this.autoRefreshInterval);
                        this.autoRefreshInterval = null;
                    }
                },

                async loadData() {
                    this.loading = true;
                    const params = new URLSearchParams({
                        mode: this.mode,
                        ...this.filters
                    });

                    try {
                        const response = await fetch(`{{ route('admin.presences.master-data') }}?${params}`);
                        this.data = await response.json();
                    } catch (error) {
                        console.error('Error loading data:', error);
                    } finally {
                        this.loading = false;
                    }
                },

                async toggleDetails(employeeId) {
                    if (this.expandedEmployee === employeeId) {
                        this.expandedEmployee = null;
                        return;
                    }

                    this.expandedEmployee = employeeId;

                    if (!this.employeeDetails[employeeId]) {
                        this.loadingDetails = true;
                        const params = new URLSearchParams({
                            period: this.filters.period,
                            start_date: this.filters.start_date,
                            end_date: this.filters.end_date
                        });

                        try {
                            const response = await fetch(`/admin/presences/employee/${employeeId}/details?${params}`);
                            this.employeeDetails[employeeId] = await response.json();
                        } catch (error) {
                            console.error('Error loading details:', error);
                        } finally {
                            this.loadingDetails = false;
                        }
                    }
                }
            }
        }
    </script>
    @endpush
</x-layouts.admin>
