@props([
    'activities' => [],
    'maxItems' => 10,
    'pollInterval' => 30000,
    'apiUrl' => null
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl shadow-sm border border-gray-100 animate-fade-in-up']) }}
     x-data="activityFeed(@js($activities), '{{ $apiUrl }}', {{ $pollInterval }})"
     x-init="init()">

    {{-- Header --}}
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <div class="flex items-center space-x-2">
            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
            <h3 class="font-semibold text-gray-900">Activité en temps réel</h3>
        </div>
        <div class="flex items-center space-x-2">
            <span class="text-xs text-gray-400" x-text="lastUpdateText"></span>
            <button @click="refresh()"
                    class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors touch-target"
                    :class="{ 'animate-spin': loading }"
                    title="Rafraîchir">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Activity List --}}
    <div class="max-h-96 overflow-y-auto scrollbar-thin" x-ref="feedContainer">
        <template x-if="activities.length === 0 && !loading">
            <div class="px-6 py-8 text-center">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-gray-500">Aucune activité récente</p>
            </div>
        </template>

        <template x-if="loading && activities.length === 0">
            <div class="px-6 py-4 space-y-4">
                @for($i = 0; $i < 5; $i++)
                    <div class="flex items-center space-x-3 animate-pulse">
                        <div class="w-10 h-10 bg-gray-200 rounded-full"></div>
                        <div class="flex-1">
                            <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                            <div class="h-3 bg-gray-200 rounded w-1/4"></div>
                        </div>
                    </div>
                @endfor
            </div>
        </template>

        <ul class="divide-y divide-gray-50">
            <template x-for="(activity, index) in activities.slice(0, {{ $maxItems }})" :key="activity.id || index">
                <li class="px-6 py-4 hover:bg-gray-50 transition-colors activity-item"
                    :class="{ 'bg-blue-50/50': activity.isNew }">
                    <div class="flex items-start space-x-3">
                        {{-- Icon/Avatar --}}
                        <div class="flex-shrink-0">
                            <template x-if="activity.avatar">
                                <img :src="activity.avatar" :alt="activity.user" class="w-10 h-10 rounded-full object-cover">
                            </template>
                            <template x-if="!activity.avatar">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center"
                                     :class="{
                                         'bg-green-100 text-green-600': activity.type === 'check_in',
                                         'bg-red-100 text-red-600': activity.type === 'check_out',
                                         'bg-blue-100 text-blue-600': activity.type === 'task_completed',
                                         'bg-purple-100 text-purple-600': activity.type === 'leave_requested',
                                         'bg-yellow-100 text-yellow-600': activity.type === 'task_approved',
                                         'bg-indigo-100 text-indigo-600': activity.type === 'survey_completed',
                                         'bg-gray-100 text-gray-600': !['check_in', 'check_out', 'task_completed', 'leave_requested', 'task_approved', 'survey_completed'].includes(activity.type)
                                     }">
                                    <template x-if="activity.type === 'check_in'">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                        </svg>
                                    </template>
                                    <template x-if="activity.type === 'check_out'">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                    </template>
                                    <template x-if="activity.type === 'task_completed'">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </template>
                                    <template x-if="activity.type === 'leave_requested'">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </template>
                                    <template x-if="activity.type === 'task_approved'">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </template>
                                    <template x-if="activity.type === 'survey_completed'">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                    </template>
                                </div>
                            </template>
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900">
                                <span class="font-medium" x-text="activity.user"></span>
                                <span class="text-gray-600" x-text="activity.message"></span>
                            </p>
                            <p class="text-xs text-gray-400 mt-1" x-text="activity.time"></p>
                        </div>

                        {{-- Badge --}}
                        <template x-if="activity.badge">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                                  :class="{
                                      'bg-green-100 text-green-800': activity.badge === 'success',
                                      'bg-yellow-100 text-yellow-800': activity.badge === 'warning',
                                      'bg-red-100 text-red-800': activity.badge === 'danger',
                                      'bg-blue-100 text-blue-800': activity.badge === 'info'
                                  }"
                                  x-text="activity.badgeText">
                            </span>
                        </template>
                    </div>
                </li>
            </template>
        </ul>
    </div>

    {{-- Footer --}}
    <template x-if="activities.length > {{ $maxItems }}">
        <div class="px-6 py-3 border-t border-gray-100 text-center">
            <button @click="showAll = !showAll" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                <span x-text="showAll ? 'Voir moins' : `Voir tout (${activities.length})`"></span>
            </button>
        </div>
    </template>
</div>

<script nonce="{{ $cspNonce ?? '' }}">
function activityFeed(initialActivities, apiUrl, pollInterval) {
    return {
        activities: initialActivities || [],
        loading: false,
        lastUpdate: new Date(),
        showAll: false,
        pollTimer: null,
        isPageVisible: true,

        get lastUpdateText() {
            const seconds = Math.floor((new Date() - this.lastUpdate) / 1000);
            if (seconds < 60) return 'À l\'instant';
            if (seconds < 3600) return `Il y a ${Math.floor(seconds / 60)} min`;
            return `Il y a ${Math.floor(seconds / 3600)}h`;
        },

        init() {
            // Track page visibility to avoid polling when tab is hidden
            document.addEventListener('visibilitychange', () => {
                this.isPageVisible = !document.hidden;
                if (this.isPageVisible && apiUrl) {
                    this.refresh(); // Refresh when tab becomes visible
                }
            });

            if (apiUrl) {
                this.startPolling();
            }
            // Update time display every minute
            setInterval(() => this.$forceUpdate?.(), 60000);
        },

        startPolling() {
            this.pollTimer = setInterval(() => {
                // Only poll if page is visible
                if (this.isPageVisible) {
                    this.refresh();
                }
            }, pollInterval);
        },

        async refresh() {
            if (!apiUrl || this.loading || !this.isPageVisible) return;

            this.loading = true;
            try {
                const response = await fetch(apiUrl);
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                const data = await response.json();

                // Mark new activities
                const existingIds = this.activities.map(a => a.id);
                data.forEach(activity => {
                    activity.isNew = !existingIds.includes(activity.id);
                });

                this.activities = data;
                this.lastUpdate = new Date();

                // Scroll to top if new activities
                if (data.some(a => a.isNew)) {
                    this.$refs.feedContainer?.scrollTo({ top: 0, behavior: 'smooth' });
                }
            } catch (error) {
                // Silently ignore network errors when page is hidden or network suspended
                if (error.name !== 'TypeError' && !error.message?.includes('Failed to fetch')) {
                    console.debug('Activity feed refresh skipped:', error.message);
                }
            } finally {
                this.loading = false;
            }
        },

        destroy() {
            if (this.pollTimer) {
                clearInterval(this.pollTimer);
            }
        }
    }
}
</script>
