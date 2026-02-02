{{-- Composant de notifications temps réel avec toasts --}}
@php
    // Déterminer l'URL de polling basée sur le rôle de l'utilisateur
    $notificationCountUrl = auth()->user()?->role === 'admin' 
        ? route('admin.notifications.unread-count') 
        : route('employee.notifications.unread-count');
@endphp

<div x-data="realtimeToasts('{{ $notificationCountUrl }}')" class="fixed top-20 right-4 z-50 space-y-2 pointer-events-none" style="z-index: 99999;">
    <template x-for="(toast, index) in toasts" :key="toast.id">
        <div x-show="true"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-8"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             class="pointer-events-auto bg-white rounded-xl shadow-2xl border border-gray-100 p-4 max-w-sm w-full flex items-start gap-3"
             :class="toast.borderClass">
            
            <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center" :class="toast.iconClass">
                <span x-html="toast.icon"></span>
            </div>

            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold" :class="toast.titleClass" x-text="toast.title"></p>
                <p class="text-sm text-gray-600 mt-0.5" x-text="toast.message"></p>
                <p class="text-xs text-gray-400 mt-1">À l'instant</p>
            </div>

            <button @click="removeToast(index)" class="flex-shrink-0 text-gray-400 hover:text-gray-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </template>
</div>

<script>
function realtimeToasts(notificationCountUrl) {
    return {
        toasts: [],
        toastId: 0,
        notificationCountUrl: notificationCountUrl,
        
        lastNotificationId: null,
        pollingInterval: null,

        init() {
            // Essayer WebSocket en premier
            if (window.Echo && window.userId) {
                window.Echo.private('App.Models.User.' + window.userId)
                    .notification((notification) => {
                        this.showToast(notification);
                        window.dispatchEvent(new CustomEvent('new-notification', { detail: notification }));
                    });
            } else {
                // Fallback: polling toutes les 30 secondes si pas de WebSocket
                this.startPolling();
            }
        },

        startPolling() {
            if (!window.userId || !this.notificationCountUrl) return;
            
            // Track page visibility
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) {
                    this.checkNewNotifications(); // Refresh when tab becomes visible
                }
            });
            
            // Polling toutes les 30 secondes (only when page is visible)
            this.pollingInterval = setInterval(() => {
                if (!document.hidden) {
                    this.checkNewNotifications();
                }
            }, 30000);
        },

        async checkNewNotifications() {
            if (document.hidden) return; // Skip if tab is hidden
            
            try {
                const response = await fetch(this.notificationCountUrl);
                if (response.ok) {
                    const data = await response.json();
                    // Mettre à jour le compteur dans la navbar si différent
                    window.dispatchEvent(new CustomEvent('notification-count-updated', { 
                        detail: { count: data.count }
                    }));
                }
            } catch (error) {
                // Silently ignore - network errors are expected when tab is hidden
            }
        },

        getNotificationConfig(type) {
            const configs = {
                'leave_request': {
                    title: 'Demande de congé',
                    iconClass: 'bg-blue-100 text-blue-600',
                    titleClass: 'text-blue-900',
                    borderClass: 'border-l-4 border-l-blue-500',
                    icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>'
                },
                'leave_status': {
                    title: 'Statut congé',
                    iconClass: 'bg-green-100 text-green-600',
                    titleClass: 'text-green-900',
                    borderClass: 'border-l-4 border-l-green-500',
                    icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
                },
                'task_assigned': {
                    title: 'Nouvelle tâche',
                    iconClass: 'bg-purple-100 text-purple-600',
                    titleClass: 'text-purple-900',
                    borderClass: 'border-l-4 border-l-purple-500',
                    icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>'
                },
                'task_status': {
                    title: 'Mise à jour tâche',
                    iconClass: 'bg-purple-100 text-purple-600',
                    titleClass: 'text-purple-900',
                    borderClass: 'border-l-4 border-l-purple-500',
                    icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>'
                },
                'task_reminder': {
                    title: 'Rappel tâche',
                    iconClass: 'bg-orange-100 text-orange-600',
                    titleClass: 'text-orange-900',
                    borderClass: 'border-l-4 border-l-orange-500',
                    icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
                },
                'late_arrival': {
                    title: 'Retard signalé',
                    iconClass: 'bg-red-100 text-red-600',
                    titleClass: 'text-red-900',
                    borderClass: 'border-l-4 border-l-red-500',
                    icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
                },
                'new_message': {
                    title: 'Nouveau message',
                    iconClass: 'bg-indigo-100 text-indigo-600',
                    titleClass: 'text-indigo-900',
                    borderClass: 'border-l-4 border-l-indigo-500',
                    icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>'
                },
                'payroll_added': {
                    title: 'Fiche de paie',
                    iconClass: 'bg-emerald-100 text-emerald-600',
                    titleClass: 'text-emerald-900',
                    borderClass: 'border-l-4 border-l-emerald-500',
                    icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
                },
                'new_survey': {
                    title: 'Nouveau sondage',
                    iconClass: 'bg-cyan-100 text-cyan-600',
                    titleClass: 'text-cyan-900',
                    borderClass: 'border-l-4 border-l-cyan-500',
                    icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>'
                },
                'new_evaluation': {
                    title: 'Nouvelle évaluation',
                    iconClass: 'bg-amber-100 text-amber-600',
                    titleClass: 'text-amber-900',
                    borderClass: 'border-l-4 border-l-amber-500',
                    icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>'
                },
                'missing_evaluation_alert': {
                    title: 'Évaluations manquantes',
                    iconClass: 'bg-yellow-100 text-yellow-600',
                    titleClass: 'text-yellow-900',
                    borderClass: 'border-l-4 border-l-yellow-500',
                    icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>'
                },
                'evaluation_reminder': {
                    title: 'Rappel évaluations',
                    iconClass: 'bg-yellow-100 text-yellow-600',
                    titleClass: 'text-yellow-900',
                    borderClass: 'border-l-4 border-l-yellow-500',
                    icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
                },
                'welcome': {
                    title: 'Bienvenue !',
                    iconClass: 'bg-green-100 text-green-600',
                    titleClass: 'text-green-900',
                    borderClass: 'border-l-4 border-l-green-500',
                    icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>'
                },
                'default': {
                    title: 'Notification',
                    iconClass: 'bg-gray-100 text-gray-600',
                    titleClass: 'text-gray-900',
                    borderClass: 'border-l-4 border-l-gray-400',
                    icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>'
                }
            };
            return configs[type] || configs['default'];
        },

        formatMessage(notification) {
            if (notification.message) return notification.message;
            
            const type = notification.type || '';
            switch (type) {
                case 'leave_request':
                    return `Demande de ${notification.employee_name || 'un employé'}`;
                case 'leave_status':
                    const status = notification.status === 'approved' ? 'approuvée' : 'refusée';
                    return `Votre demande a été ${status}`;
                case 'task_assigned':
                    return notification.task_title || notification.titre || 'Nouvelle tâche assignée';
                case 'task_status':
                    return notification.task_titre || 'Statut mis à jour';
                case 'new_message':
                    return `De ${notification.sender_name || 'quelqu\'un'}`;
                case 'payroll_added':
                    return notification.periode ? `Période : ${notification.periode}` : 'Disponible';
                case 'new_survey':
                    return notification.survey_titre || 'Nouveau sondage';
                case 'new_evaluation':
                    return notification.total_score ? `Score : ${notification.total_score}/10` : 'Voir détails';
                case 'missing_evaluation_alert':
                    return notification.interns_count ? `${notification.interns_count} stagiaire(s) non évalué(s)` : 'À compléter';
                case 'evaluation_reminder':
                    return notification.interns_count ? `${notification.interns_count} évaluation(s) à faire` : 'À compléter';
                default:
                    return 'Nouvelle notification';
            }
        },

        showToast(notification) {
            const config = this.getNotificationConfig(notification.type);
            this.toastId++;
            
            this.toasts.push({
                id: this.toastId,
                title: config.title,
                message: this.formatMessage(notification),
                iconClass: config.iconClass,
                titleClass: config.titleClass,
                borderClass: config.borderClass,
                icon: config.icon
            });
            
            setTimeout(() => {
                if (this.toasts.length > 0) {
                    this.toasts.shift();
                }
            }, 6000);
        },

        removeToast(index) {
            this.toasts.splice(index, 1);
        }
    }
}
</script>
