<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ManageX') }} - Admin</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- PWA -->
    <meta name="theme-color" content="#4f46e5">
    <link rel="manifest" href="{{ route('manifest') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192x192.png') }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Chart.js -->
    <script nonce="{{ $cspNonce ?? '' }}" src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- OneSignal Push Notifications -->
    <script src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js" defer></script>
    <script>
    window.OneSignalDeferred = window.OneSignalDeferred || [];
    OneSignalDeferred.push(async function(OneSignal) {
        await OneSignal.init({
            appId: "bd7969c6-0b7c-4bf4-8ad4-6209607959cd",
            path: "/managex/public/",
            serviceWorkerParam: { scope: "/managex/public/" },
            autoPrompt: false,
            notifyButton: { enable: false },
            safari_web_id: "{{ config('services.onesignal.safari_web_id', '') }}",
        });
        @auth
        await OneSignal.login("{{ auth()->id() }}");
        @endauth

        showNotificationPromptIfNeeded(OneSignal);
    });

    function showNotificationPromptIfNeeded(OneSignal) {
        const dismissed = localStorage.getItem('managex-notif-dismissed');
        if (dismissed && (Date.now() - parseInt(dismissed)) < 7 * 24 * 60 * 60 * 1000) {
            return;
        }

        const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
        const isStandalone = window.navigator.standalone || window.matchMedia('(display-mode: standalone)').matches;

        if (isIOS && !isStandalone) {
            setTimeout(() => {
                const modal = document.getElementById('onesignal-custom-prompt');
                const iosMsg = document.getElementById('ios-pwa-message');
                const defaultMsg = document.getElementById('default-notif-message');
                if (modal) {
                    if (iosMsg) iosMsg.classList.remove('hidden');
                    if (defaultMsg) defaultMsg.classList.add('hidden');
                    modal.classList.remove('hidden');
                }
            }, 3000);
            return;
        }

        if (!('Notification' in window) && !('safari' in window)) {
            return;
        }

        let permission;
        try {
            permission = OneSignal.Notifications.permission;
        } catch (e) {
            permission = ('Notification' in window) ? Notification.permission === 'granted' : false;
        }

        if (!permission) {
            setTimeout(() => {
                const modal = document.getElementById('onesignal-custom-prompt');
                if (modal) modal.classList.remove('hidden');
            }, 3000);
        }
    }

    function acceptNotifications() {
        const modal = document.getElementById('onesignal-custom-prompt');
        if (modal) modal.classList.add('hidden');

        const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
        const isStandalone = window.navigator.standalone || window.matchMedia('(display-mode: standalone)').matches;
        if (isIOS && !isStandalone) {
            return;
        }

        OneSignalDeferred.push(async function(OneSignal) {
            try {
                await OneSignal.Notifications.requestPermission();
            } catch (e) {
                console.warn('Notification permission request failed:', e);
                if ('Notification' in window && Notification.permission === 'default') {
                    try {
                        await Notification.requestPermission();
                    } catch (e2) {
                        console.warn('Native notification permission failed:', e2);
                    }
                }
            }
        });
    }

    function dismissNotifications() {
        const modal = document.getElementById('onesignal-custom-prompt');
        if (modal) modal.classList.add('hidden');
        localStorage.setItem('managex-notif-dismissed', Date.now());
    }
    </script>

    <!-- Custom OneSignal Notification Prompt Modal -->
    <style>
    #onesignal-custom-prompt {
        position: fixed; bottom: 24px; right: 24px; z-index: 99999;
        max-width: 380px; width: calc(100% - 32px);
        animation: slideUp 0.5s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(40px) scale(0.95); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    #onesignal-custom-prompt .notif-card {
        background: linear-gradient(135deg, #312e81 0%, #4338ca 50%, #6366f1 100%);
        border-radius: 20px; padding: 28px 24px; color: white;
        box-shadow: 0 25px 60px -12px rgba(67, 56, 202, 0.5), 0 0 0 1px rgba(255,255,255,0.1) inset;
        backdrop-filter: blur(10px); position: relative; overflow: hidden;
    }
    #onesignal-custom-prompt .notif-card::before {
        content: ''; position: absolute; top: -50%; right: -50%;
        width: 200%; height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 60%);
        pointer-events: none;
    }
    #onesignal-custom-prompt .bell-icon {
        width: 56px; height: 56px; border-radius: 16px;
        background: rgba(255,255,255,0.15); backdrop-filter: blur(8px);
        display: flex; align-items: center; justify-content: center;
        margin-bottom: 16px; animation: bellRing 2s ease-in-out infinite;
        border: 1px solid rgba(255,255,255,0.2);
    }
    @keyframes bellRing {
        0%, 100% { transform: rotate(0deg); }
        10%, 30% { transform: rotate(8deg); }
        20% { transform: rotate(-8deg); }
        40% { transform: rotate(0deg); }
    }
    #onesignal-custom-prompt h3 {
        font-size: 18px; font-weight: 700; margin-bottom: 8px;
        letter-spacing: -0.02em;
    }
    #onesignal-custom-prompt p {
        font-size: 14px; color: rgba(255,255,255,0.8); line-height: 1.5;
        margin-bottom: 20px;
    }
    #onesignal-custom-prompt .btn-accept {
        width: 100%; padding: 12px 20px; border-radius: 12px;
        background: white; color: #4338ca; font-weight: 600;
        font-size: 15px; border: none; cursor: pointer;
        transition: all 0.2s ease; box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    #onesignal-custom-prompt .btn-accept:hover {
        transform: translateY(-1px); box-shadow: 0 6px 20px rgba(0,0,0,0.2);
    }
    #onesignal-custom-prompt .btn-dismiss {
        width: 100%; padding: 10px; margin-top: 8px;
        background: transparent; border: none; color: rgba(255,255,255,0.6);
        font-size: 13px; cursor: pointer; transition: color 0.2s;
    }
    #onesignal-custom-prompt .btn-dismiss:hover { color: rgba(255,255,255,0.9); }
    #onesignal-custom-prompt .close-btn {
        position: absolute; top: 12px; right: 12px;
        background: rgba(255,255,255,0.1); border: none; color: rgba(255,255,255,0.6);
        width: 28px; height: 28px; border-radius: 8px; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        font-size: 16px; transition: all 0.2s;
    }
    #onesignal-custom-prompt .close-btn:hover {
        background: rgba(255,255,255,0.2); color: white;
    }
    @media (max-width: 480px) {
        #onesignal-custom-prompt { bottom: 16px; right: 16px; left: 16px; width: auto; }
    }
    </style>
    <div id="onesignal-custom-prompt" class="hidden">
        <div class="notif-card">
            <button class="close-btn" onclick="dismissNotifications()">✕</button>
            <div class="bell-icon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                </svg>
            </div>
            <div id="default-notif-message">
                <h3>🔔 Restez informé !</h3>
                <p>Activez les notifications pour ne rien manquer : pointage, tâches, congés et messages importants.</p>
                <button class="btn-accept" onclick="acceptNotifications()">
                    ✓ Activer les notifications
                </button>
            </div>
            <div id="ios-pwa-message" class="hidden">
                <h3>📱 Installer ManageX</h3>
                <p>Pour recevoir les notifications sur iPhone/iPad, ajoutez ManageX à votre écran d'accueil :<br>
                <strong>Appuyez sur</strong> <span style="font-size:18px">⬆️</span> <strong>puis "Sur l'écran d'accueil"</strong></p>
                <button class="btn-accept" onclick="dismissNotifications()">
                    ✓ J'ai compris
                </button>
            </div>
            <button class="btn-dismiss" onclick="dismissNotifications()">
                Plus tard
            </button>
        </div>
    </div>
</head>
<body class="font-sans antialiased" x-data="{ showLogoutModal: false }">
    <script nonce="{{ $cspNonce ?? '' }}">window.userId = {{ auth()->id() ?? 'null' }};</script>
    <x-realtime-notifications />
    <div class="min-h-screen bg-slate-50">
        <!-- Decorative Background -->
        <div class="fixed inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-100 rounded-full blur-3xl opacity-50"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-sky-100 rounded-full blur-3xl opacity-50"></div>
        </div>

        <!-- Mobile Sidebar Backdrop -->
        <div id="sidebarBackdrop" onclick="document.getElementById('sidebar').classList.add('-translate-x-full'); document.getElementById('sidebarBackdrop').classList.add('hidden')" class="fixed inset-0 z-40 bg-gray-900/50 backdrop-blur-sm lg:hidden hidden transition-opacity opacity-100"></div>

        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200 transform transition-transform duration-200 ease-in-out lg:translate-x-0 -translate-x-full flex flex-col items-center" id="sidebar">
            <div class="flex items-center justify-center h-20 w-full border-b border-slate-100">
                <span class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-700 to-sky-500">ManageX</span>
            </div>

            <nav class="mt-6 flex-1 overflow-y-auto">
                <div class="px-4 space-y-2">
                    <x-sidebar-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Tableau de bord
                    </x-sidebar-link>
                    
                    <x-sidebar-link :href="route('admin.analytics.index')" :active="request()->routeIs('admin.analytics.*')">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                        </svg>
                        Analytics
                    </x-sidebar-link>

                    <!-- Gestion RH Dropdown -->
                    <div x-data="{ open: {{ (request()->routeIs('admin.employees.*') || request()->routeIs('admin.presences.*') || request()->routeIs('admin.tasks.*') || request()->routeIs('admin.leaves.*') || request()->routeIs('admin.intern-evaluations.*')) ? 'true' : 'false' }} }">
                        <button @click="open = !open" 
                                class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-200 group {{ (request()->routeIs('admin.employees.*') || request()->routeIs('admin.presences.*') || request()->routeIs('admin.tasks.*') || request()->routeIs('admin.leaves.*') || request()->routeIs('admin.intern-evaluations.*')) ? 'text-blue-700 bg-gradient-to-r from-blue-50 to-indigo-50 font-medium border-l-4 border-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 hover:translate-x-1' }}">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                <span>Gestion RH</span>
                            </div>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        
                        <div x-show="open" x-cloak x-transition class="space-y-1 mt-1">
                            <x-sidebar-link :href="route('admin.employees.index')" :active="request()->routeIs('admin.employees.*')" class="pl-12 text-sm">Employés</x-sidebar-link>
                            <x-sidebar-link :href="route('admin.presences.index')" :active="request()->routeIs('admin.presences.*')" class="pl-12 text-sm">Présences</x-sidebar-link>
                            <x-sidebar-link :href="route('admin.tasks.index')" :active="request()->routeIs('admin.tasks.*')" class="pl-12 text-sm">Tâches</x-sidebar-link>
                            <x-sidebar-link :href="route('admin.leaves.index')" :active="request()->routeIs('admin.leaves.*')" class="pl-12 text-sm">Congés</x-sidebar-link>
                            <x-sidebar-link :href="route('admin.intern-evaluations.index')" :active="request()->routeIs('admin.intern-evaluations.*')" class="pl-12 text-sm">Stagiaires</x-sidebar-link>
                        </div>
                    </div>

                    <!-- Paie Dropdown -->
                    <div x-data="{ open: {{ (request()->routeIs('admin.payrolls.*') || request()->routeIs('admin.payroll-settings.*') || request()->routeIs('admin.employee-evaluations.*')) ? 'true' : 'false' }} }">
                        <button @click="open = !open" 
                                class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-200 group {{ (request()->routeIs('admin.payrolls.*') || request()->routeIs('admin.payroll-settings.*') || request()->routeIs('admin.employee-evaluations.*')) ? 'text-blue-700 bg-gradient-to-r from-blue-50 to-indigo-50 font-medium border-l-4 border-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 hover:translate-x-1' }}">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <span>Paie & Perf.</span>
                            </div>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        
                        <div x-show="open" x-cloak x-transition class="space-y-1 mt-1">
                            <x-sidebar-link :href="route('admin.employee-evaluations.index')" :active="request()->routeIs('admin.employee-evaluations.*')" class="pl-12 text-sm">
                                Évaluations
                            </x-sidebar-link>
                            <x-sidebar-link :href="route('admin.payrolls.index')" :active="request()->routeIs('admin.payrolls.*')" class="pl-12 text-sm">
                                Fiches de paie
                            </x-sidebar-link>
                            <x-sidebar-link :href="route('admin.payroll-settings.countries')" :active="request()->routeIs('admin.payroll-settings.*')" class="pl-12 text-sm">
                                Config. Paie
                            </x-sidebar-link>
                        </div>
                    </div>
                   
                    <!-- Communication Dropdown -->
                    <div x-data="{ open: {{ (request()->routeIs('admin.surveys.*') || request()->routeIs('admin.announcements.*') || request()->routeIs('admin.documents.*') || request()->routeIs('messaging.*') || request()->routeIs('admin.messaging.*')) ? 'true' : 'false' }} }">
                        <button @click="open = !open" 
                                class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-200 group {{ (request()->routeIs('admin.surveys.*') || request()->routeIs('admin.announcements.*') || request()->routeIs('admin.documents.*') || request()->routeIs('messaging.*') || request()->routeIs('admin.messaging.*')) ? 'text-blue-700 bg-gradient-to-r from-blue-50 to-indigo-50 font-medium border-l-4 border-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 hover:translate-x-1' }}">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <span>Communication</span>
                            </div>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        
                        <div x-show="open" x-cloak x-transition class="space-y-1 mt-1">
                            <x-sidebar-link :href="route('admin.surveys.index')" :active="request()->routeIs('admin.surveys.*')" class="pl-12 text-sm">Sondages</x-sidebar-link>
                            <x-sidebar-link :href="route('admin.announcements.index')" :active="request()->routeIs('admin.announcements.*')" class="pl-12 text-sm">Annonces</x-sidebar-link>
                            <x-sidebar-link :href="route('admin.documents.index')" :active="request()->routeIs('admin.documents.*')" class="pl-12 text-sm">Documents</x-sidebar-link>
                            <x-sidebar-link :href="route('messaging.admin.chat')" :active="request()->routeIs('messaging.admin.chat')" class="pl-12 text-sm">Chat</x-sidebar-link>
                            <x-sidebar-link :href="route('admin.messaging.index')" :active="request()->routeIs('admin.messaging.*')" class="pl-12 text-sm">Gestion Messages</x-sidebar-link>
                        </div>
                    </div>

                    <!-- Organisation Dropdown -->
                     <div x-data="{ open: {{ (request()->routeIs('admin.geolocation-zones.*')) ? 'true' : 'false' }} }">
                        <button @click="open = !open" 
                                class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-200 group {{ (request()->routeIs('admin.geolocation-zones.*')) ? 'text-blue-700 bg-gradient-to-r from-blue-50 to-indigo-50 font-medium border-l-4 border-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 hover:translate-x-1' }}">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <span>Organisation</span>
                            </div>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        
                        <div x-show="open" x-cloak x-transition class="space-y-1 mt-1">
                            <x-sidebar-link :href="route('admin.geolocation-zones.index')" :active="request()->routeIs('admin.geolocation-zones.*')" class="pl-12 text-sm">Zones Géoloc.</x-sidebar-link>
                        </div>
                    </div>

                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="lg:pl-64">
            <!-- Top Navigation -->
            <header class="bg-white/80 backdrop-blur-sm border-b border-gray-200 sticky top-0 z-40">
                <div class="flex items-center justify-between h-16 px-4">
                    <div>
                        <button class="lg:hidden p-2 rounded-xl hover:bg-gray-100 transition-colors" 
                                onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full'); document.getElementById('sidebarBackdrop').classList.toggle('hidden')">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <x-notification-dropdown />

                        <!-- User Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" @click.away="open = false" 
                                    class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-sm font-semibold overflow-hidden">
                                    @if(auth()->user()->avatar && avatar_url(auth()->user()->avatar))
                                        <img src="{{ avatar_url(auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover" onerror="this.style.display='none'; this.parentElement.textContent=@js(strtoupper(substr(auth()->user()->name, 0, 1)));">
                                    @else
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    @endif
                                </div>
                                <span class="text-sm font-medium text-gray-700 hidden sm:block">{{ auth()->user()->name }}</span>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="open" x-cloak x-transition class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50">
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                                    <span class="inline-flex items-center px-2 py-0.5 mt-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Administrateur
                                    </span>
                                </div>
                                <div class="py-1">
                                    <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        Paramétres
                                    </a>
                                </div>
                                <div class="border-t border-gray-100 pt-1">
                                    <button type="button" @click="showLogoutModal = true" class="flex items-center gap-3 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        Déconnexion
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden Logout Form -->
                    <form method="POST" action="{{ route('logout') }}" x-ref="logoutForm" class="hidden">
                        @csrf
                    </form>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-6">
                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- Logout Confirmation Modal -->
    <div x-show="showLogoutModal" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
        <div x-show="showLogoutModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showLogoutModal = false"></div>
        <div class="flex min-h-screen items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div x-show="showLogoutModal" x-transition class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-base font-semibold leading-6 text-gray-900">Déconnexion</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">Êtes-vous sûr de vouloir vous déconnecter ?</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="button" @click="$refs.logoutForm.submit()" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto">Déconnexion</button>
                    <button type="button" @click="showLogoutModal = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Annuler</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Toast Container - Must be outside all other containers -->
    <div id="toast-container" class="fixed top-20 right-4 flex flex-col gap-3 pointer-events-none" style="z-index: 99999;"></div>

    <style>
        @keyframes toast-slide-in {
            0% { transform: translateX(100%); opacity: 0; }
            100% { transform: translateX(0); opacity: 1; }
        }
        @keyframes toast-slide-out {
            0% { transform: translateX(0); opacity: 1; }
            100% { transform: translateX(100%); opacity: 0; }
        }
        @keyframes toast-progress {
            0% { width: 100%; }
            100% { width: 0%; }
        }
        .toast-enter { animation: toast-slide-in 0.4s cubic-bezier(0.21, 1.02, 0.73, 1) forwards; }
        .toast-exit { animation: toast-slide-out 0.3s cubic-bezier(0.21, 1.02, 0.73, 1) forwards; }
        .toast-progress { animation: toast-progress linear forwards; }
    </style>

    <script nonce="{{ $cspNonce ?? '' }}">
        function showToast(type, message, duration = 5000) {
            const container = document.getElementById('toast-container');
            const id = 'toast-' + Date.now();
            
            const configs = {
                success: {
                    bg: 'bg-gradient-to-r from-emerald-500 to-green-600',
                    icon: `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`,
                    title: 'Succés',
                    progressBg: 'bg-emerald-300/50'
                },
                error: {
                    bg: 'bg-gradient-to-r from-red-500 to-rose-600',
                    icon: `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`,
                    title: 'Erreur',
                    progressBg: 'bg-red-300/50'
                },
                warning: {
                    bg: 'bg-gradient-to-r from-amber-500 to-orange-600',
                    icon: `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>`,
                    title: 'Attention',
                    progressBg: 'bg-amber-300/50'
                },
                info: {
                    bg: 'bg-gradient-to-r from-blue-500 to-indigo-600',
                    icon: `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`,
                    title: 'Info',
                    progressBg: 'bg-blue-300/50'
                }
            };
            
            const config = configs[type] || configs.info;
            
            const toast = document.createElement('div');
            toast.id = id;
            toast.className = `pointer-events-auto max-w-sm w-full ${config.bg} rounded-2xl shadow-2xl overflow-hidden toast-enter`;
            toast.innerHTML = `
                <div class="p-4">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-10 h-10 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center text-white">
                            ${config.icon}
                        </div>
                        <div class="flex-1 min-w-0 pt-0.5">
                            <p class="text-sm font-semibold text-white">${config.title}</p>
                            <p class="text-sm text-white/90 mt-1 break-words">${message}</p>
                        </div>
                        <button onclick="closeToast('${id}')" class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 hover:bg-white/20 text-white/80 hover:text-white transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </div>
                <div class="h-1 ${config.progressBg}">
                    <div class="h-full bg-white/60 toast-progress" style="animation-duration: ${duration}ms;"></div>
                </div>
            `;
            
            container.appendChild(toast);
            setTimeout(() => closeToast(id), duration);
        }
        
        function closeToast(id) {
            const toast = document.getElementById(id);
            if (toast) {
                toast.classList.remove('toast-enter');
                toast.classList.add('toast-exit');
                setTimeout(() => toast.remove(), 300);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success') || session('status'))
                showToast('success', @json(session('success') ?? session('status')));
            @endif

            @if(session('error'))
                showToast('error', @json(session('error')));
            @endif
            
            @if(session('warning'))
                showToast('warning', @json(session('warning')));
            @endif
            
            @if(session('info'))
                showToast('info', @json(session('info')));
            @endif
            
            @if($errors->any())
                showToast('error', 'Une erreur est survenue. Veuillez vérifier vos entrées.');
            @endif
        });
    </script>

    {{-- Overlay de téléchargement global --}}
    <x-download-overlay />

    {{-- Assistant IA Admin --}}
    <x-ai-chat-widget :is-admin="true" />

    {{-- Script pour transformer les liens d'export en téléchargements avec overlay --}}
    <script nonce="{{ $cspNonce ?? '' }}">
        document.addEventListener('DOMContentLoaded', function() {
            // Patterns qui indiquent un vrai lien de téléchargement de fichier
            const downloadPatterns = [
                /\/export\/(pdf|excel|csv)/i,      // /export/pdf, /export/excel, /export/csv
                /\/download\/(pdf|excel|csv)/i,    // /download/pdf, etc.
                /\.(pdf|xlsx|xls|csv)(\?|$)/i,     // Fichiers avec extension
                /export.*\?(.*format=|.*type=)/i,  // export?format= ou export?type=
            ];
            
            // Vérifie si un lien est un vrai téléchargement
            function isRealDownloadLink(href) {
                return downloadPatterns.some(pattern => pattern.test(href));
            }
            
            // Fonction pour configurer un lien de téléchargement
            function setupDownloadLink(link) {
                // Ignorer les liens qui ouvrent dans un nouvel onglet ou qui ont déjà un handler
                if (link.target === '_blank' || link.dataset.downloadHandled) return;
                
                // Ignorer si marqué explicitement pour ne pas utiliser l'overlay
                if (link.dataset.noOverlay === 'true') return;
                
                // Détecter le type de fichier
                const href = link.getAttribute('href');
                if (!href) return;
                
                // Ne traiter que si c'est explicitement marqué OU si c'est un vrai pattern de téléchargement
                const hasExplicitDownload = link.hasAttribute('data-download');
                if (!hasExplicitDownload && !isRealDownloadLink(href)) {
                    return; // Ce n'est pas un lien de téléchargement
                }
                
                let fileType = link.dataset.fileType || '';
                let fileName = link.dataset.fileName || '';
                
                // Auto-détecter le type si non spécifié
                if (!fileType) {
                    if (href.includes('pdf')) fileType = 'pdf';
                    else if (href.includes('excel') || href.includes('xlsx')) fileType = 'excel';
                    else if (href.includes('csv')) fileType = 'csv';
                }
                
                // Auto-détecter le nom si non spécifié
                if (!fileName) {
                    const linkText = link.textContent.trim();
                    if (linkText && !['Télécharger', 'Download', 'Export', 'Exporter', 'PDF', 'Excel', 'CSV'].includes(linkText)) {
                        fileName = linkText;
                    } else {
                        fileName = fileType === 'pdf' ? 'document.pdf' : 
                                   fileType === 'excel' ? 'export.xlsx' : 
                                   fileType === 'csv' ? 'export.csv' : 'fichier';
                    }
                }
                
                // Ajouter le handler de clic
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Déclencher l'événement pour l'overlay
                    window.dispatchEvent(new CustomEvent('start-download', {
                        detail: {
                            url: href,
                            filename: fileName,
                            type: fileType
                        }
                    }));
                });
                
                // Marquer comme traité
                link.dataset.downloadHandled = 'true';
            }
            
            // Sélectionner tous les liens potentiels d'export
            const exportLinks = document.querySelectorAll(
                'a[href*="export"], a[href*="download"], a[data-download]'
            );
            
            exportLinks.forEach(setupDownloadLink);
            
            // Observer pour les liens ajoutés dynamiquement (AJAX, Alpine, etc.)
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1) { // Element node
                            if (node.matches && node.matches('a[href*="export"], a[href*="download"], a[data-download]')) {
                                setupDownloadLink(node);
                            }
                            // Chercher aussi dans les enfants
                            const childLinks = node.querySelectorAll && node.querySelectorAll('a[href*="export"], a[href*="download"], a[data-download]');
                            if (childLinks) {
                                childLinks.forEach(setupDownloadLink);
                            }
                        }
                    });
                });
            });
            
            observer.observe(document.body, { childList: true, subtree: true });
        });
    </script>

    {{-- Protection anti-double-clic sur tous les formulaires --}}
    <script nonce="{{ $cspNonce ?? '' }}">
    document.addEventListener('DOMContentLoaded', function() {
        document.body.addEventListener('submit', function(e) {
            const form = e.target;
            if (form.tagName !== 'FORM' || form.dataset.noSubmitGuard) return;

            const btn = form.querySelector('button[type="submit"], input[type="submit"]');
            if (!btn || btn.dataset.submitting) {
                e.preventDefault();
                return;
            }

            btn.dataset.submitting = '1';
            const originalHTML = btn.innerHTML;
            const originalWidth = btn.offsetWidth;
            btn.style.minWidth = originalWidth + 'px';
            btn.disabled = true;
            btn.innerHTML = '<svg class="animate-spin h-5 w-5 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> <span class="ml-1">Envoi...</span>';
            btn.classList.add('opacity-75', 'cursor-not-allowed');

            // Restaurer apres 8s en cas d'erreur reseau
            setTimeout(function() {
                btn.disabled = false;
                btn.innerHTML = originalHTML;
                btn.classList.remove('opacity-75', 'cursor-not-allowed');
                delete btn.dataset.submitting;
            }, 8000);
        });
    });
    </script>

    @stack('scripts')

    {{-- OneSignal handles push notifications and service worker --}}
</body>
</html>
