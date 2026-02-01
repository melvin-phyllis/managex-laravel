<x-layouts.admin>
    <div class="space-y-6" x-data="analyticsPage()">

        {{-- Header amélioré --}}
        <div class="relative overflow-hidden bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 rounded-2xl shadow-xl">
            <div class="absolute inset-0 bg-black/10"></div>
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            
            <div class="relative p-6 md:p-8 bg-blue-400">
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
                                Dernière mise à jour: <span x-text="lastUpdate">-</span>
                            </span>
                            <span class="px-3 py-1 bg-emerald-500/80 text-white text-xs font-medium rounded-full flex items-center gap-1" x-show="!loading">
                                <span class="w-2 h-2 bg-white rounded-full animate-pulse"></span>
                                En direct
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button @click="exportData('pdf')" class="px-4 py-2.5 bg-white/20 backdrop-blur-sm text-white font-medium rounded-xl hover:bg-white/30 transition-all flex items-center">
                            <x-icon name="download" class="w-4 h-4 mr-2"/>
                            Export PDF
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
                              x-text="(kpis.turnover?.rate || 0) > 10 ? 'Élevé' : 'Normal'"></span>
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

        {{-- KPI Cards - Ligne 2 (Secondaires) --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 animate-fade-in-up animation-delay-250">
            {{-- En congé --}}
            <div class="bg-gradient-to-br from-violet-500 to-purple-600 rounded-xl p-4 text-white shadow-lg shadow-violet-500/20">
                <div class="flex items-center justify-between">
                    <x-icon name="coffee" class="w-5 h-5 opacity-80"/>
                    <span class="text-2xl font-bold" x-text="kpis.en_conge?.value || '0'"></span>
                </div>
                <p class="text-xs text-white/80 mt-2">En congé</p>
                <div class="flex gap-1 mt-1">
                    <span class="text-[9px] bg-white/20 px-1 rounded" x-show="kpis.en_conge?.types?.conge > 0" x-text="'CP:' + kpis.en_conge?.types?.conge"></span>
                    <span class="text-[9px] bg-white/20 px-1 rounded" x-show="kpis.en_conge?.types?.maladie > 0" x-text="'Mal:' + kpis.en_conge?.types?.maladie"></span>
                </div>
            </div>

            {{-- Absents --}}
            <div class="bg-gradient-to-br from-rose-500 to-pink-600 rounded-xl p-4 text-white shadow-lg shadow-rose-500/20">
                <div class="flex items-center justify-between">
                    <x-icon name="alert-circle" class="w-5 h-5 opacity-80"/>
                    <span class="text-2xl font-bold" x-text="kpis.absents_non_justifies?.value || '0'"></span>
                </div>
                <p class="text-xs text-white/80 mt-2">Absents injustifiés</p>
            </div>

            {{-- Heures sup --}}
            <div class="bg-gradient-to-br from-cyan-500 to-blue-600 rounded-xl p-4 text-white shadow-lg shadow-cyan-500/20">
                <div class="flex items-center justify-between">
                    <x-icon name="clock" class="w-5 h-5 opacity-80"/>
                    <span class="text-2xl font-bold" x-text="kpis.heures_supplementaires?.value || '0'"></span>
                </div>
                <p class="text-xs text-white/80 mt-2">Heures sup.</p>
                <p class="text-[9px] text-white/60"><span x-text="kpis.heures_supplementaires?.count || 0"></span> employés</p>
            </div>

            {{-- Tâches complétées --}}
            <div class="bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl p-4 text-white shadow-lg shadow-emerald-500/20">
                <div class="flex items-center justify-between">
                    <x-icon name="check-square" class="w-5 h-5 opacity-80"/>
                    <span class="text-2xl font-bold" x-text="kpis.tasks?.completed || '0'"></span>
                </div>
                <p class="text-xs text-white/80 mt-2">Tâches complétées</p>
                <p class="text-[9px] text-white/60"><span x-text="kpis.tasks?.pending || 0"></span> en attente</p>
            </div>

            {{-- Stagiaires --}}
            <div class="bg-gradient-to-br from-indigo-500 to-blue-600 rounded-xl p-4 text-white shadow-lg shadow-indigo-500/20">
                <div class="flex items-center justify-between">
                    <x-icon name="user-plus" class="w-5 h-5 opacity-80"/>
                    <span class="text-2xl font-bold" x-text="kpis.interns?.count || '0'"></span>
                </div>
                <p class="text-xs text-white/80 mt-2">Stagiaires actifs</p>
                <p class="text-[9px] text-white/60"><span x-text="kpis.interns?.to_evaluate || 0"></span> à évaluer</p>
            </div>

            {{-- Retards à rattraper --}}
            <div class="bg-[#3506a2] rounded-xl p-4 text-orange-500 shadow-lg shadow-orange-500/20">
                <div class="flex items-center justify-between text-white">
                    <x-icon name="alert-triangle" class="w-5 h-5 opacity-80"/>
                    <span class="text-2xl font-bold" x-text="kpis.late_hours?.total || '0'"></span>
                </div>
                <p class="text-xs text-white/80 mt-2">Heures de retard</p>
                <p class="text-[9px] text-white/60"><span x-text="kpis.late_hours?.employees || 0"></span> employés concernés</p>
            </div>
        </div>

        {{-- Graphiques Row 1 --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fade-in-up animation-delay-300">
            {{-- Evolution presences (Line) - Takes 2 cols --}}
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <x-icon name="activity" class="w-4 h-4 text-indigo-600"/>
                        </div>
                        Évolution des présences
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

        {{-- Graphiques Row 2 --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fade-in-up animation-delay-350">
            {{-- Recrutements vs Departs --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                        <x-icon name="users" class="w-4 h-4 text-emerald-600"/>
                    </div>
                    Recrutements vs Départs
                </h3>
                <div class="h-64 relative w-full">
                    <canvas id="recruitmentChart"></canvas>
                </div>
            </div>

            {{-- Répartition par type de contrat (NOUVEAU) --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                        <x-icon name="file-text" class="w-4 h-4 text-amber-600"/>
                    </div>
                    Types de contrats
                </h3>
                <div class="h-64 relative w-full flex justify-center">
                    <canvas id="contractTypeChart"></canvas>
                </div>
            </div>

            {{-- Performance Tâches (NOUVEAU) --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <x-icon name="check-square" class="w-4 h-4 text-blue-600"/>
                    </div>
                    Performance des tâches
                </h3>
                <div class="h-64 relative w-full flex justify-center">
                    <canvas id="taskPerformanceChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Graphiques Row 3 --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 animate-fade-in-up animation-delay-400">
            {{-- Absenteisme par service --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <div class="w-8 h-8 bg-rose-100 rounded-lg flex items-center justify-center">
                        <x-icon name="user-x" class="w-4 h-4 text-rose-600"/>
                    </div>
                    Taux d'absentéisme par service
                </h3>
                <div class="h-64 relative w-full">
                    <canvas id="absenteismChart"></canvas>
                </div>
            </div>

            {{-- Ponctualité par département (NOUVEAU) --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                        <x-icon name="clock" class="w-4 h-4 text-orange-600"/>
                    </div>
                    Ponctualité par département
                </h3>
                <div class="h-64 relative w-full">
                    <canvas id="punctualityChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Heures par semaine (Area) --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 animate-fade-in-up animation-delay-450">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                    <div class="w-8 h-8 bg-cyan-100 rounded-lg flex items-center justify-center">
                        <x-icon name="clock" class="w-4 h-4 text-cyan-600"/>
                    </div>
                    Heures travaillées (5 dernières semaines)
                </h3>
                <div class="text-sm text-gray-500">
                    Total: <span class="font-bold text-gray-900" x-text="charts.heures_travaillees_semaine?.total || 0"></span>h
                </div>
            </div>
            <div class="h-64 relative w-full">
                <canvas id="weeklyHoursChart"></canvas>
            </div>
        </div>

        {{-- Insights & Recommendations (NOUVEAU) --}}
        <div class="bg-gradient-to-r from-indigo-50 via-purple-50 to-pink-50 rounded-xl border border-indigo-100 p-6 animate-fade-in-up animation-delay-500">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center flex-shrink-0">
                    <x-icon name="zap" class="w-6 h-6 text-white"/>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 mb-2">Insights & Recommandations</h3>
                    <div class="space-y-2">
                        <div class="flex items-center gap-2 text-sm" x-show="kpis.turnover?.rate > 10">
                            <span class="w-2 h-2 rounded-full bg-red-500"></span>
                            <span class="text-gray-700">Taux de turnover élevé (<span x-text="kpis.turnover?.rate"></span>%) - Analyser les causes de départ</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm" x-show="kpis.presents_today?.percentage < 80">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            <span class="text-gray-700">Taux de présence faible (<span x-text="kpis.presents_today?.percentage"></span>%) - Vérifier les absences non justifiées</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm" x-show="kpis.interns?.to_evaluate > 0">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            <span class="text-gray-700"><span x-text="kpis.interns?.to_evaluate"></span> stagiaire(s) en attente d'évaluation cette semaine</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm" x-show="kpis.late_hours?.total > 10">
                            <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                            <span class="text-gray-700"><span x-text="kpis.late_hours?.total"></span>h de retard cumulées - Planifier des sessions de rattrapage</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm" x-show="tables.pending?.length > 5">
                            <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                            <span class="text-gray-700"><span x-text="tables.pending?.length"></span> demandes de congés en attente de validation</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm" x-show="kpis.turnover?.rate <= 10 && kpis.presents_today?.percentage >= 80">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span class="text-gray-700">Les indicateurs RH sont dans la norme. Continuez ainsi !</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tableaux Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 animate-fade-in-up animation-delay-550">
            {{-- Activité récente --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex justify-between items-center">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <x-icon name="activity" class="w-4 h-4 text-indigo-600"/>
                        </div>
                        Activité Récente
                    </h3>
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
                <div class="p-4 border-b border-gray-100 bg-gradient-to-r from-amber-50 to-white flex justify-between items-center">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                            <x-icon name="clock" class="w-4 h-4 text-amber-600"/>
                        </div>
                        Demandes en attente
                    </h3>
                    <span class="bg-amber-500 text-white text-xs font-bold px-2.5 py-1 rounded-full animate-pulse" x-show="tables.pending.length > 0" x-text="tables.pending.length"></span>
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
                <div class="p-4 border-b border-gray-100 bg-gradient-to-r from-red-50 to-white flex items-center gap-2">
                    <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                        <x-icon name="alert-triangle" class="w-4 h-4 text-red-600"/>
                    </div>
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
                <div class="p-4 border-b border-gray-100 bg-gradient-to-r from-orange-50 to-white flex items-center gap-2">
                    <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                        <x-icon name="clock" class="w-4 h-4 text-orange-600"/>
                    </div>
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
                lastUpdate: '-',
                filters: { period: 'month', department_id: '', contract_type: '', custom_month: new Date().getMonth() + 1, custom_year: new Date().getFullYear() },
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
                    // Auto-refresh toutes les 5 minutes
                    setInterval(() => this.loadData(), 300000);
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
                        this.lastUpdate = new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
                        
                        this.$nextTick(() => {
                            this.updateCharts();
                        });
                    } catch (error) {
                        console.error('Error loading analytics:', error);
                    } finally {
                        this.loading = false;
                    }
                },

                exportData(format) {
                    window.open(`{{ route('admin.presences.export.pdf') }}?${new URLSearchParams(this.filters).toString()}`, '_blank');
                },

                updateCharts() {
                    this.renderPresenceTrend();
                    this.renderDepartmentChart();
                    this.renderRecruitmentChart();
                    this.renderAbsenteismChart();
                    this.renderWeeklyHoursChart();
                    this.renderContractTypeChart();
                    this.renderTaskPerformanceChart();
                    this.renderPunctualityChart();
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

                renderContractTypeChart() {
                    const ctx = document.getElementById('contractTypeChart');
                    if (!ctx) return;

                    if (this.chartInstances.contractType) this.chartInstances.contractType.destroy();

                    this.chartInstances.contractType = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: this.charts.contract_types?.labels || [],
                            datasets: [{
                                data: this.charts.contract_types?.data || [],
                                backgroundColor: this.charts.contract_types?.colors || ['#6366F1', '#22C55E', '#F59E0B', '#EC4899'],
                                borderWidth: 0,
                                hoverOffset: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '65%',
                            plugins: {
                                legend: { 
                                    position: 'bottom', 
                                    labels: { usePointStyle: true, padding: 15, font: { size: 11 } } 
                                }
                            }
                        }
                    });
                },

                renderTaskPerformanceChart() {
                    const ctx = document.getElementById('taskPerformanceChart');
                    if (!ctx) return;

                    if (this.chartInstances.taskPerf) this.chartInstances.taskPerf.destroy();

                    const taskData = this.charts.task_performance || {};
                    this.chartInstances.taskPerf = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Complétées', 'En cours', 'Approuvées', 'En attente', 'Annulées'],
                            datasets: [{
                                data: [
                                    taskData.completed || 0,
                                    taskData.in_progress || 0,
                                    taskData.approved || 0,
                                    taskData.pending || 0,
                                    taskData.cancelled || 0
                                ],
                                backgroundColor: ['#22C55E', '#3B82F6', '#8B5CF6', '#F59E0B', '#EF4444'],
                                borderWidth: 0,
                                hoverOffset: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '65%',
                            plugins: {
                                legend: { 
                                    position: 'bottom', 
                                    labels: { usePointStyle: true, padding: 10, font: { size: 10 } } 
                                }
                            }
                        }
                    });
                },

                renderPunctualityChart() {
                    const ctx = document.getElementById('punctualityChart');
                    if (!ctx) return;

                    if (this.chartInstances.punctuality) this.chartInstances.punctuality.destroy();

                    this.chartInstances.punctuality = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: this.charts.punctuality?.labels || [],
                            datasets: [
                                {
                                    label: 'À l\'heure',
                                    data: this.charts.punctuality?.on_time || [],
                                    backgroundColor: '#22C55E',
                                    borderRadius: 4,
                                    barPercentage: 0.6
                                },
                                {
                                    label: 'En retard',
                                    data: this.charts.punctuality?.late || [],
                                    backgroundColor: '#EF4444',
                                    borderRadius: 4,
                                    barPercentage: 0.6
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { 
                                    position: 'top',
                                    labels: { usePointStyle: true, font: { size: 11 } }
                                }
                            },
                            scales: {
                                y: { 
                                    beginAtZero: true, 
                                    stacked: true,
                                    grid: { borderDash: [2, 4] }
                                },
                                x: { 
                                    stacked: true,
                                    grid: { display: false } 
                                }
                            }
                        }
                    });
                }
            }
        }
    </script>
    @endpush
</x-layouts.admin>
