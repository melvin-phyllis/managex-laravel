{{-- Composant de notifications temps réel avec toasts --}}
<div x-data="realtimeToasts()" class="fixed top-20 right-4 z-50 space-y-2 pointer-events-none">
    <template x-for="(toast, index) in toasts" :key="index">
        <div x-show="true"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-8"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             class="pointer-events-auto bg-white rounded-xl shadow-2xl border border-gray-100 p-4 max-w-sm w-full flex items-start gap-3">
            
            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </div>

            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900" x-text="toast.message"></p>
                <p class="text-xs text-gray-500 mt-1">À l'instant</p>
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
function realtimeToasts() {
    return {
        toasts: [],
        
        init() {
            if (window.Echo && window.userId) {
                window.Echo.private('App.Models.User.' + window.userId)
                    .notification((notification) => {
                        this.showToast(notification);
                        window.dispatchEvent(new CustomEvent('new-notification', { detail: notification }));
                    });
            }
        },

        showToast(notification) {
            this.toasts.push({
                message: notification.message || 'Nouvelle notification'
            });
            
            setTimeout(() => {
                if (this.toasts.length > 0) {
                    this.toasts.shift();
                }
            }, 5000);
        },

        removeToast(index) {
            this.toasts.splice(index, 1);
        }
    }
}
</script>
