@props([
    'events' => [],
    'month' => null,
    'year' => null
])

@php
$month = $month ?? now()->month;
$year = $year ?? now()->year;
$currentDate = \Carbon\Carbon::create($year, $month, 1);
$today = now();
@endphp

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl shadow-sm border border-gray-100 p-6 animate-fade-in-up']) }}
     x-data="miniCalendar(@js($events), {{ $month }}, {{ $year }})">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold text-gray-900" x-text="monthYear"></h3>
        <div class="flex space-x-1">
            <button @click="previousMonth()"
                    class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors touch-target"
                    title="Mois précédent">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <button @click="today()"
                    class="px-2 py-1 text-xs text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded transition-colors"
                    title="Aujourd'hui">
                Aujourd'hui
            </button>
            <button @click="nextMonth()"
                    class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors touch-target"
                    title="Mois suivant">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Day Headers --}}
    <div class="grid grid-cols-7 gap-1 mb-2">
        <template x-for="day in ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim']" :key="day">
            <div class="text-center text-xs font-medium text-gray-400 py-1" x-text="day"></div>
        </template>
    </div>

    {{-- Calendar Grid --}}
    <div class="grid grid-cols-7 gap-1">
        <template x-for="(day, index) in calendarDays" :key="index">
            <div class="relative">
                <button
                    @click="day.date && selectDate(day)"
                    @mouseenter="day.events?.length && showTooltip($event, day)"
                    @mouseleave="hideTooltip()"
                    class="calendar-day w-full aspect-square"
                    :class="{
                        'text-gray-300 cursor-default': !day.currentMonth,
                        'text-gray-700 hover:bg-gray-100 cursor-pointer': day.currentMonth && !day.isToday,
                        'calendar-day-today': day.isToday,
                        'ring-2 ring-blue-300': day.isSelected,
                        'font-medium': day.events?.length > 0
                    }"
                    :disabled="!day.date"
                    x-text="day.day">
                </button>
                {{-- Event Indicators --}}
                <template x-if="day.events?.length > 0">
                    <div class="absolute bottom-0.5 left-1/2 transform -translate-x-1/2 flex space-x-0.5">
                        <template x-for="(event, i) in day.events.slice(0, 3)" :key="i">
                            <span class="w-1 h-1 rounded-full"
                                  :class="{
                                      'bg-green-500': event.type === 'leave',
                                      'bg-blue-500': event.type === 'task',
                                      'bg-purple-500': event.type === 'birthday',
                                      'bg-yellow-500': event.type === 'deadline',
                                      'bg-gray-400': !['leave', 'task', 'birthday', 'deadline'].includes(event.type)
                                  }">
                            </span>
                        </template>
                    </div>
                </template>
            </div>
        </template>
    </div>

    {{-- Legend --}}
    <div class="mt-4 pt-4 border-t border-gray-100">
        <div class="flex flex-wrap gap-3 text-xs">
            <div class="flex items-center space-x-1">
                <span class="w-2 h-2 rounded-full bg-green-500"></span>
                <span class="text-gray-500">Congés</span>
            </div>
            <div class="flex items-center space-x-1">
                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                <span class="text-gray-500">Tâches</span>
            </div>
            <div class="flex items-center space-x-1">
                <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                <span class="text-gray-500">Anniversaires</span>
            </div>
        </div>
    </div>

    {{-- Tooltip --}}
    <div x-ref="tooltip"
         x-show="tooltipVisible"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         class="absolute z-50 bg-gray-900 text-white text-xs rounded-lg py-2 px-3 shadow-lg max-w-xs"
         :style="tooltipStyle">
        <p class="font-medium mb-1" x-text="tooltipDate"></p>
        <template x-for="event in tooltipEvents" :key="event.id">
            <div class="flex items-center space-x-2 py-0.5">
                <span class="w-1.5 h-1.5 rounded-full"
                      :class="{
                          'bg-green-400': event.type === 'leave',
                          'bg-blue-400': event.type === 'task',
                          'bg-purple-400': event.type === 'birthday',
                          'bg-yellow-400': event.type === 'deadline'
                      }">
                </span>
                <span x-text="event.title"></span>
            </div>
        </template>
    </div>

    {{-- Selected Day Events --}}
    <template x-if="selectedDay && selectedDay.events?.length > 0">
        <div class="mt-4 pt-4 border-t border-gray-100">
            <h4 class="text-sm font-medium text-gray-700 mb-2" x-text="'Événements du ' + selectedDayFormatted"></h4>
            <div class="space-y-2 max-h-32 overflow-y-auto">
                <template x-for="event in selectedDay.events" :key="event.id">
                    <a :href="event.link" class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center"
                             :class="{
                                 'bg-green-100 text-green-600': event.type === 'leave',
                                 'bg-blue-100 text-blue-600': event.type === 'task',
                                 'bg-purple-100 text-purple-600': event.type === 'birthday',
                                 'bg-yellow-100 text-yellow-600': event.type === 'deadline'
                             }">
                            <template x-if="event.type === 'leave'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </template>
                            <template x-if="event.type === 'task'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </template>
                            <template x-if="event.type === 'birthday'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.701 2.701 0 00-1.5-.454M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21v-7a2 2 0 00-2-2H5a2 2 0 00-2 2v7h18zm-3-9v-2a2 2 0 00-2-2H8a2 2 0 00-2 2v2h12z"/>
                                </svg>
                            </template>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate" x-text="event.title"></p>
                            <p class="text-xs text-gray-500" x-text="event.subtitle"></p>
                        </div>
                    </a>
                </template>
            </div>
        </div>
    </template>
</div>

<script nonce="{{ $cspNonce ?? '' }}">
function miniCalendar(events, initialMonth, initialYear) {
    const monthNames = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
                       'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];

    return {
        events: events || [],
        currentMonth: initialMonth,
        currentYear: initialYear,
        selectedDay: null,
        tooltipVisible: false,
        tooltipStyle: '',
        tooltipDate: '',
        tooltipEvents: [],

        get monthYear() {
            return `${monthNames[this.currentMonth - 1]} ${this.currentYear}`;
        },

        get selectedDayFormatted() {
            if (!this.selectedDay) return '';
            return `${this.selectedDay.day} ${monthNames[this.currentMonth - 1]}`;
        },

        get calendarDays() {
            const days = [];
            const firstDay = new Date(this.currentYear, this.currentMonth - 1, 1);
            const lastDay = new Date(this.currentYear, this.currentMonth, 0);
            const today = new Date();

            // Get the day of week (0 = Sunday, adjust for Monday start)
            let startDay = firstDay.getDay() - 1;
            if (startDay < 0) startDay = 6;

            // Previous month days
            const prevMonthLastDay = new Date(this.currentYear, this.currentMonth - 1, 0).getDate();
            for (let i = startDay - 1; i >= 0; i--) {
                days.push({
                    day: prevMonthLastDay - i,
                    currentMonth: false,
                    date: null
                });
            }

            // Current month days
            for (let i = 1; i <= lastDay.getDate(); i++) {
                const dateStr = `${this.currentYear}-${String(this.currentMonth).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
                const dayEvents = this.events.filter(e => e.date === dateStr);

                days.push({
                    day: i,
                    currentMonth: true,
                    date: dateStr,
                    isToday: today.getDate() === i &&
                            today.getMonth() === this.currentMonth - 1 &&
                            today.getFullYear() === this.currentYear,
                    isSelected: this.selectedDay?.date === dateStr,
                    events: dayEvents
                });
            }

            // Next month days
            const remainingDays = 42 - days.length; // 6 rows * 7 days
            for (let i = 1; i <= remainingDays; i++) {
                days.push({
                    day: i,
                    currentMonth: false,
                    date: null
                });
            }

            return days;
        },

        previousMonth() {
            if (this.currentMonth === 1) {
                this.currentMonth = 12;
                this.currentYear--;
            } else {
                this.currentMonth--;
            }
            this.selectedDay = null;
        },

        nextMonth() {
            if (this.currentMonth === 12) {
                this.currentMonth = 1;
                this.currentYear++;
            } else {
                this.currentMonth++;
            }
            this.selectedDay = null;
        },

        today() {
            const now = new Date();
            this.currentMonth = now.getMonth() + 1;
            this.currentYear = now.getFullYear();
            this.selectedDay = null;
        },

        selectDate(day) {
            if (day.currentMonth) {
                this.selectedDay = day;
            }
        },

        showTooltip(event, day) {
            if (!day.events || day.events.length === 0) return;

            const rect = event.target.getBoundingClientRect();
            const parentRect = this.$el.getBoundingClientRect();

            this.tooltipDate = `${day.day} ${monthNames[this.currentMonth - 1]}`;
            this.tooltipEvents = day.events;
            this.tooltipStyle = `top: ${rect.bottom - parentRect.top + 5}px; left: ${rect.left - parentRect.left}px;`;
            this.tooltipVisible = true;
        },

        hideTooltip() {
            this.tooltipVisible = false;
        }
    }
}
</script>
