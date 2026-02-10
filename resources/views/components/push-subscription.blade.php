{{-- Push Notification Subscription Manager --}}
<div x-data="pushManager()" x-init="init()" x-show="showPrompt" x-cloak
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-y-2"
     x-transition:enter-end="opacity-100 translate-y-0"
     class="fixed bottom-4 right-4 z-50 max-w-sm">
    <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 p-5">
        <div class="flex items-start gap-3">
            <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </div>
            <div class="flex-1">
                <p class="text-sm font-semibold text-gray-900">Activer les notifications</p>
                <p class="text-xs text-gray-500 mt-1">Recevez des alertes meme quand ManageX est ferme.</p>
            </div>
            <button @click="dismiss()" class="flex-shrink-0 text-gray-400 hover:text-gray-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="mt-4 flex gap-2">
            <button @click="subscribe()" :disabled="subscribing"
                    class="flex-1 px-4 py-2 text-sm font-medium text-white rounded-xl transition-colors disabled:opacity-50"
                    style="background-color: #3B8BEB;">
                <span x-show="!subscribing">Activer</span>
                <span x-show="subscribing" x-cloak>Activation...</span>
            </button>
            <button @click="dismiss()"
                    class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">
                Plus tard
            </button>
        </div>
    </div>
</div>

<script>
function pushManager() {
    return {
        showPrompt: false,
        subscribing: false,
        vapidPublicKey: null,

        async init() {
            if (!('serviceWorker' in navigator) || !('PushManager' in window)) return;
            if (Notification.permission === 'denied') return;
            if (Notification.permission === 'granted') {
                await this.ensureSubscription();
                return;
            }
            const dismissed = localStorage.getItem('managex_push_dismissed');
            if (dismissed) {
                const dismissedAt = parseInt(dismissed, 10);
                if (Date.now() - dismissedAt < 7 * 24 * 60 * 60 * 1000) return;
            }
            setTimeout(() => { this.showPrompt = true; }, 5000);
        },

        async getVapidKey() {
            if (this.vapidPublicKey) return this.vapidPublicKey;
            const resp = await fetch('{{ route("push.key") }}');
            const data = await resp.json();
            this.vapidPublicKey = data.key;
            return this.vapidPublicKey;
        },

        async subscribe() {
            this.subscribing = true;
            try {
                const permission = await Notification.requestPermission();
                if (permission !== 'granted') {
                    this.showPrompt = false;
                    this.subscribing = false;
                    return;
                }
                const vapidKey = await this.getVapidKey();
                const registration = await navigator.serviceWorker.ready;
                const subscription = await registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: this.urlBase64ToUint8Array(vapidKey)
                });
                await this.saveSubscription(subscription);
                this.showPrompt = false;
            } catch (err) {
                console.error('Push subscription failed:', err);
            }
            this.subscribing = false;
        },

        async ensureSubscription() {
            try {
                console.log('[Push] ensureSubscription started');
                const registration = await navigator.serviceWorker.ready;
                console.log('[Push] SW ready');
                let subscription = await registration.pushManager.getSubscription();
                console.log('[Push] Existing subscription:', subscription);
                if (!subscription) {
                    const vapidKey = await this.getVapidKey();
                    console.log('[Push] Got VAPID key:', vapidKey ? 'OK' : 'MISSING');
                    subscription = await registration.pushManager.subscribe({
                        userVisibleOnly: true,
                        applicationServerKey: this.urlBase64ToUint8Array(vapidKey)
                    });
                    console.log('[Push] New subscription created:', subscription);
                }
                await this.saveSubscription(subscription);
            } catch (err) {
                console.error('[Push] ensureSubscription failed:', err);
            }
        },

        async saveSubscription(subscription) {
            console.log('[Push] saveSubscription called');
            const key = subscription.getKey('p256dh');
            const auth = subscription.getKey('auth');
            const body = new URLSearchParams({
                endpoint: subscription.endpoint,
                'keys[p256dh]': btoa(String.fromCharCode.apply(null, new Uint8Array(key))),
                'keys[auth]': btoa(String.fromCharCode.apply(null, new Uint8Array(auth))),
                content_encoding: (PushManager.supportedContentEncodings || ['aesgcm'])[0],
                _token: document.querySelector('meta[name="csrf-token"]').content
            });
            console.log('[Push] Sending to /push/subscribe');
            const resp = await fetch('{{ route("push.subscribe") }}', {
                method: 'POST',
                body: body
            });
            console.log('[Push] Response status:', resp.status);
            const text = await resp.text();
            console.log('[Push] Response body:', text);
        },

        dismiss() {
            this.showPrompt = false;
            localStorage.setItem('managex_push_dismissed', Date.now().toString());
        },

        urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
            const raw = atob(base64);
            const arr = new Uint8Array(raw.length);
            for (let i = 0; i < raw.length; i++) arr[i] = raw.charCodeAt(i);
            return arr;
        }
    }
}
</script>
