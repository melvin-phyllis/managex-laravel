@props(['apiUrl'])

<div x-data="{
    planning: [],
    loading: true,
    selectedDay: null,
    async fetchPlanning() {
        try {
            const res = await fetch('{{ $apiUrl }}');
            this.planning = await res.json();
            this.selectedDay = this.planning.find(d => d.is_today) || this.planning[0];
        } catch (e) {
            console.error('Planning fetch error:', e);
        } finally {
            this.loading = false;
        }
    },
    statusColor(status) {
        return {
            'present': 'bg-green-500',
            'done': 'bg-blue-500',
            'scheduled': 'bg-gray-400',
            'leave': 'bg-amber-500',
            'absent': 'bg-red-500',
        }[status] || 'bg-gray-400';
    },
    statusLabel(status) {
        return {
            'present': 'En poste',
            'done': 'Parti',
            'scheduled': 'Pr\u00e9vu',
            'leave': 'En cong\u00e9',
            'absent': 'Absent',
        }[status] || status;
    },
    statusIcon(status) {
        return {
            'present': '\u2705',
            'done': '\u2611\ufe0f',
            'scheduled': '\ud83d\udcc5',
            'leave': '\ud83c\udfd6\ufe0f',
            'absent': '\u274c',
        }[status] || '';
    }
}" x-init="fetchPlanning()" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

    <!-- Header -->
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="p-2 rounded-xl" style="background: rgba(49, 112, 142, 0.1);">
                <svg class="w-5 h-5" style="color: #31708E;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900 text-sm">Planning de la semaine</h3>
                <p class="text-xs text-gray-500">Qui est present chaque jour</p>
            </div>
        </div>
    </div>

    <!-- Skeleton loading -->
    <template x-if="loading">
        <div class="p-5 space-y-3 animate-pulse">
            <div class="flex gap-2">
                <template x-for="i in 5"><div class="flex-1 h-12 bg-gray-100 rounded-xl"></div></template>
            </div>
            <div class="space-y-2">
                <div class="h-10 bg-gray-100 rounded-lg"></div>
                <div class="h-10 bg-gray-100 rounded-lg"></div>
                <div class="h-10 bg-gray-100 rounded-lg"></div>
            </div>
        </div>
    </template>

    <!-- Content -->
    <template x-if="!loading">
        <div>
            <!-- Day tabs -->
            <div class="flex border-b border-gray-100">
                <template x-for="day in planning" :key="day.day">
                    <button @click="selectedDay = day"
                            class="flex-1 py-3 px-1 text-center transition-all relative"
                            :class="selectedDay?.day === day.day
                                ? 'text-white font-semibold'
                                : (day.is_today ? 'text-indigo-700 font-medium bg-indigo-50/50' : (day.is_past ? 'text-gray-400' : 'text-gray-600 hover:bg-gray-50'))">
                        <!-- Active indicator background -->
                        <div x-show="selectedDay?.day === day.day"
                             class="absolute inset-0 rounded-t-lg"
                             style="background: linear-gradient(135deg, #31708E, #5085A5);"></div>
                        <div class="relative">
                            <div class="text-xs" x-text="day.day_name.substring(0, 3)"></div>
                            <div class="text-sm font-bold" x-text="day.date"></div>
                            <div class="flex items-center justify-center gap-1 mt-0.5">
                                <span class="inline-block w-1.5 h-1.5 rounded-full"
                                      :class="selectedDay?.day === day.day ? 'bg-white/80' : (day.count > 0 ? 'bg-green-500' : 'bg-gray-300')"></span>
                                <span class="text-[10px]" x-text="day.count"></span>
                            </div>
                        </div>
                        <!-- Today dot -->
                        <div x-show="day.is_today && selectedDay?.day !== day.day"
                             class="absolute bottom-0 left-1/2 -translate-x-1/2 w-1 h-1 rounded-full bg-indigo-500"></div>
                    </button>
                </template>
            </div>

            <!-- Selected day detail -->
            <div class="p-4" x-show="selectedDay">
                <!-- Day header -->
                <div class="flex items-center justify-between mb-3">
                    <p class="text-sm font-medium text-gray-700" x-text="selectedDay?.date_full"></p>
                    <span class="text-xs px-2 py-1 rounded-full font-medium"
                          :class="selectedDay?.is_today ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-600'"
                          x-text="selectedDay?.count + ' present(s)'"></span>
                </div>

                <!-- Employee list -->
                <div class="space-y-2 max-h-64 overflow-y-auto" x-show="selectedDay?.employees?.length > 0">
                    <template x-for="emp in selectedDay?.employees" :key="emp.id">
                        <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors"
                             :class="emp.status === 'present' ? 'bg-green-50' : (emp.status === 'leave' ? 'bg-amber-50' : (emp.status === 'absent' ? 'bg-red-50' : (emp.status === 'done' ? 'bg-blue-50' : 'bg-gray-50')))">
                            <!-- Avatar -->
                            <div class="relative flex-shrink-0">
                                <template x-if="emp.avatar">
                                    <img :src="emp.avatar" class="w-9 h-9 rounded-full object-cover border-2 border-white shadow-sm" :alt="emp.name">
                                </template>
                                <template x-if="!emp.avatar">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-xs font-bold shadow-sm"
                                         style="background: linear-gradient(135deg, #31708E, #5085A5);"
                                         x-text="emp.name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase()">
                                    </div>
                                </template>
                                <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full border-2 border-white"
                                      :class="statusColor(emp.status)"></span>
                            </div>
                            <!-- Info -->
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate" x-text="emp.name"></p>
                                <p class="text-xs text-gray-500 truncate" x-text="emp.department || 'Aucun service'"></p>
                            </div>
                            <!-- Status -->
                            <span class="text-xs px-2 py-1 rounded-full font-medium flex-shrink-0"
                                  :class="{
                                      'bg-green-100 text-green-700': emp.status === 'present',
                                      'bg-blue-100 text-blue-700': emp.status === 'done',
                                      'bg-gray-100 text-gray-600': emp.status === 'scheduled',
                                      'bg-amber-100 text-amber-700': emp.status === 'leave',
                                      'bg-red-100 text-red-700': emp.status === 'absent',
                                  }"
                                  x-text="statusLabel(emp.status)">
                            </span>
                        </div>
                    </template>
                </div>

                <!-- Empty state -->
                <div x-show="!selectedDay?.employees?.length" class="text-center py-8">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <p class="text-sm text-gray-500">Aucun employe prevu ce jour</p>
                </div>

                <!-- Legend -->
                <div class="mt-4 pt-3 border-t border-gray-100 flex flex-wrap gap-3">
                    <span class="flex items-center gap-1.5 text-[10px] text-gray-500">
                        <span class="w-2 h-2 rounded-full bg-green-500"></span> En poste
                    </span>
                    <span class="flex items-center gap-1.5 text-[10px] text-gray-500">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span> Parti
                    </span>
                    <span class="flex items-center gap-1.5 text-[10px] text-gray-500">
                        <span class="w-2 h-2 rounded-full bg-gray-400"></span> Prevu
                    </span>
                    <span class="flex items-center gap-1.5 text-[10px] text-gray-500">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span> En conge
                    </span>
                    <span class="flex items-center gap-1.5 text-[10px] text-gray-500">
                        <span class="w-2 h-2 rounded-full bg-red-500"></span> Absent
                    </span>
                </div>
            </div>
        </div>
    </template>
</div>
