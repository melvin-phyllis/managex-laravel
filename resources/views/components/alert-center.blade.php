@props([
    'alerts' => [],
    'apiUrl' => null
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl shadow-sm border border-gray-100 animate-fade-in-up']) }}
     x-data="alertCenter(@js($alerts), '{{ $apiUrl }}')"
     x-init="init()">

    {{-- Header --}}
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <div class="flex items-center space-x-2">
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <h3 class="font-semibold text-gray-900">Alertes à traiter</h3>
            <template x-if="totalAlerts > 0">
                <span class="alert-badge alert-badge-red" x-text="totalAlerts"></span>
            </template>
        </div>
        <button @click="expanded = !expanded"
                class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
            <svg class="w-5 h-5 transition-transform duration-200" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
    </div>

    {{-- Alert Categories --}}
    <div x-show="expanded" x-collapse>
        {{-- Late Arrivals --}}
        <div class="border-b border-gray-50">
            <button @click="sections.late = !sections.late"
                    class="w-full px-6 py-3 flex items-center justify-between hover:bg-gray-50 transition-colors">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="font-medium text-gray-700">Retards du jour</span>
                    <span class="alert-badge alert-badge-red" x-text="alerts.late?.length || 0"></span>
                </div>
                <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': sections.late }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="sections.late" x-collapse class="px-6 pb-4">
                <template x-if="!alerts.late || alerts.late.length === 0">
                    <p class="text-sm text-gray-500 py-2">Aucun retard aujourd'hui</p>
                </template>
                <div class="space-y-2">
                    <template x-for="item in alerts.late" :key="item.id">
                        <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-full bg-red-200 flex items-center justify-center text-red-700 text-sm font-medium"
                                     x-text="item.initials"></div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900" x-text="item.name"></p>
                                    <p class="text-xs text-gray-500">
                                        Arrivée à <span class="font-medium text-red-600" x-text="item.time"></span>
                                        (<span x-text="item.delay"></span> de retard)
                                    </p>
                                </div>
                            </div>
                            <a :href="item.link" class="text-xs text-red-600 hover:text-red-800 font-medium">
                                Voir
                            </a>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- Overdue Tasks --}}
        <div class="border-b border-gray-50">
            <button @click="sections.overdue = !sections.overdue"
                    class="w-full px-6 py-3 flex items-center justify-between hover:bg-gray-50 transition-colors">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <span class="font-medium text-gray-700">Tâches en retard</span>
                    <span class="alert-badge alert-badge-yellow" x-text="alerts.overdue?.length || 0"></span>
                </div>
                <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': sections.overdue }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="sections.overdue" x-collapse class="px-6 pb-4">
                <template x-if="!alerts.overdue || alerts.overdue.length === 0">
                    <p class="text-sm text-gray-500 py-2">Aucune tâche en retard</p>
                </template>
                <div class="space-y-2">
                    <template x-for="item in alerts.overdue" :key="item.id">
                        <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate" x-text="item.title"></p>
                                <p class="text-xs text-gray-500">
                                    <span x-text="item.user"></span> ·
                                    <span class="text-yellow-600 font-medium" x-text="item.daysOverdue + ' jours de retard'"></span>
                                </p>
                            </div>
                            <a :href="item.link" class="ml-3 px-3 py-1 text-xs bg-yellow-100 text-yellow-700 rounded-full hover:bg-yellow-200 transition-colors font-medium">
                                Traiter
                            </a>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- Pending Approvals --}}
        <div class="border-b border-gray-50">
            <button @click="sections.pending = !sections.pending"
                    class="w-full px-6 py-3 flex items-center justify-between hover:bg-gray-50 transition-colors">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <span class="font-medium text-gray-700">En attente +48h</span>
                    <span class="alert-badge bg-purple-100 text-purple-600" x-text="alerts.pending?.length || 0"></span>
                </div>
                <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': sections.pending }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="sections.pending" x-collapse class="px-6 pb-4">
                <template x-if="!alerts.pending || alerts.pending.length === 0">
                    <p class="text-sm text-gray-500 py-2">Aucune demande en attente depuis plus de 48h</p>
                </template>
                <div class="space-y-2">
                    <template x-for="item in alerts.pending" :key="item.id">
                        <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900" x-text="item.type"></p>
                                <p class="text-xs text-gray-500">
                                    <span x-text="item.user"></span> ·
                                    <span class="text-purple-600" x-text="'Depuis ' + item.waitingTime"></span>
                                </p>
                            </div>
                            <div class="flex space-x-2 ml-3">
                                <button @click="approve(item)"
                                        class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded hover:bg-green-200 transition-colors font-medium touch-target">
                                    Approuver
                                </button>
                                <a :href="item.link" class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition-colors font-medium">
                                    Voir
                                </a>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- Quick Summary --}}
        <div class="px-6 py-4 bg-gray-50">
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-500">Total des alertes</span>
                <span class="font-bold" :class="totalAlerts > 0 ? 'text-red-600' : 'text-green-600'" x-text="totalAlerts"></span>
            </div>
        </div>
    </div>

    {{-- Collapsed Summary --}}
    <div x-show="!expanded" class="px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex space-x-4">
                <div class="flex items-center space-x-1">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span>
                    <span class="text-sm text-gray-600" x-text="(alerts.late?.length || 0) + ' retards'"></span>
                </div>
                <div class="flex items-center space-x-1">
                    <span class="w-2 h-2 rounded-full bg-yellow-500"></span>
                    <span class="text-sm text-gray-600" x-text="(alerts.overdue?.length || 0) + ' tâches'"></span>
                </div>
                <div class="flex items-center space-x-1">
                    <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                    <span class="text-sm text-gray-600" x-text="(alerts.pending?.length || 0) + ' en attente'"></span>
                </div>
            </div>
            <span class="text-sm text-blue-600 cursor-pointer hover:underline" @click="expanded = true">Détails</span>
        </div>
    </div>
</div>

<script nonce="{{ $cspNonce ?? '' }}">
function alertCenter(initialAlerts, apiUrl) {
    return {
        alerts: initialAlerts || { late: [], overdue: [], pending: [] },
        expanded: true,
        sections: {
            late: false,
            overdue: false,
            pending: false
        },

        get totalAlerts() {
            return (this.alerts.late?.length || 0) +
                   (this.alerts.overdue?.length || 0) +
                   (this.alerts.pending?.length || 0);
        },

        init() {
            // Auto-expand section with most alerts
            const counts = {
                late: this.alerts.late?.length || 0,
                overdue: this.alerts.overdue?.length || 0,
                pending: this.alerts.pending?.length || 0
            };
            const maxSection = Object.keys(counts).reduce((a, b) => counts[a] > counts[b] ? a : b);
            if (counts[maxSection] > 0) {
                this.sections[maxSection] = true;
            }
        },

        async approve(item) {
            if (!confirm('Voulez-vous approuver cette demande ?')) return;

            try {
                const response = await fetch(item.approveUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    }
                });

                if (response.ok) {
                    // Remove from list
                    this.alerts.pending = this.alerts.pending.filter(a => a.id !== item.id);
                    // Show success message (you can integrate with your toast system)
                    alert('Demande approuvée avec succès');
                }
            } catch (error) {
                console.error('Error approving:', error);
                alert('Erreur lors de l\'approbation');
            }
        }
    }
}
</script>
