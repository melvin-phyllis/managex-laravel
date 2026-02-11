<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ManageX') }} - Espace Employé</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- PWA -->
    <meta name="theme-color" content="#3B8BEB">
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
            serviceWorkerParam: { scope: "{{ asset('') }}" },
        });
        // Associate this browser with the authenticated user
        @auth
        await OneSignal.login("{{ auth()->id() }}");
        @endauth
    });
    </script>
</head>
<body class="font-sans antialiased" x-data="{ showLogoutModal: false }">
    <script nonce="{{ $cspNonce ?? '' }}">window.userId = {{ auth()->id() ?? 'null' }};</script>
    <x-realtime-notifications />
    <div class="min-h-screen bg-gray-50">
        <!-- Decorative Background -->
        <div class="fixed inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-80 h-80 rounded-full blur-3xl" style="background-color: rgba(59, 139, 235, 0.05);"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 rounded-full blur-3xl" style="background-color: rgba(178, 56, 80, 0.05);"></div>
        </div>

        <!-- Mobile Sidebar Backdrop -->
        <div id="sidebarBackdrop" onclick="document.getElementById('sidebar').classList.add('-translate-x-full'); document.getElementById('sidebarBackdrop').classList.add('hidden')" class="fixed inset-0 z-40 bg-gray-900/50 backdrop-blur-sm lg:hidden hidden transition-opacity opacity-100"></div>

        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 transform transition-transform duration-200 ease-in-out lg:translate-x-0 -translate-x-full flex flex-col" id="sidebar">
            <div class="flex items-center justify-between h-16 px-4" style="background-color: #3B8BEB;">
                <span class="text-xl font-bold text-white">ManageX</span>
                <button class="lg:hidden p-2 rounded-xl bg-white/10 hover:bg-white/20 text-white transition-colors" 
                        onclick="document.getElementById('sidebar').classList.add('-translate-x-full'); document.getElementById('sidebarBackdrop').classList.add('hidden')">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <nav class="mt-6 flex-1 overflow-y-auto">
                <div class="px-4 space-y-2">
                    <x-sidebar-link :href="route('employee.dashboard')" :active="request()->routeIs('employee.dashboard')">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Tableau de bord
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('employee.messaging.index')" :active="request()->routeIs('employee.messaging.*')">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        Messagerie
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('employee.presences.index')" :active="request()->routeIs('employee.presences.*')">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Mes Présences
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('employee.tasks.index')" :active="request()->routeIs('employee.tasks.*')">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        Mes Taches
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('employee.leaves.index')" :active="request()->routeIs('employee.leaves.*')">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Mes Congés
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('employee.payrolls.index')" :active="request()->routeIs('employee.payrolls.*')">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Mes Fiches de paie
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('employee.surveys.index')" :active="request()->routeIs('employee.surveys.*')">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Sondages
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('employee.announcements.index')" :active="request()->routeIs('employee.announcements.*')">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                        </svg>
                        Annonces
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('employee.documents.index')" :active="request()->routeIs('employee.documents.*')">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Mes Documents
                    </x-sidebar-link>

                    @if(auth()->user()->isIntern())
                    <x-sidebar-link :href="route('employee.evaluations.index')" :active="request()->routeIs('employee.evaluations.*')">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        Mes évaluations
                    </x-sidebar-link>
                    @endif
                </div>

                @if(auth()->user()->supervisees()->interns()->exists())
                <div class="px-4 mt-6">
                    <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Mes Stagiaires</p>
                    <x-sidebar-link :href="route('employee.tutor.evaluations.index')" :active="request()->routeIs('employee.tutor.evaluations.*')">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        évaluations hebdomadaires
                    </x-sidebar-link>
                </div>
                @endif
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

                    <div class="flex items-center space-x-4 ">
                        <!-- Notifications -->
                        <x-notification-dropdown />

                        <!-- User Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" @click.away="open = false" 
                                    class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-100 transition-colors">
                                <!-- Avatar -->
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
                            <div x-show="open" 
                                 x-cloak
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50">
                                
                                <!-- User Info -->
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                                </div>

                                <!-- Menu Items -->
                                <div class="py-1">
                                    <a href="{{ route('employee.profile.index') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        Mon Profil
                                    </a>
                                    <a href="{{ route('employee.settings.index') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        Paramétres
                                    </a>
                                </div>

                                <!-- Logout -->
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
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-6">
                <!-- Flash Messages -->
               

                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- Logout Confirmation Modal -->
    <div x-show="showLogoutModal" 
         class="fixed inset-0 z-[100] overflow-y-auto" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true"
         style="display: none;">
        
        <!-- Backdrop -->
        <div x-show="showLogoutModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
             @click="showLogoutModal = false"></div>

        <div class="flex min-h-screen items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div x-show="showLogoutModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">Déconnexion</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">Êtes-vous sûr de vouloir vous déconnecter ?</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="button" 
                            @click="$refs.logoutForm.submit()"
                            class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto">
                        Déconnexion
                    </button>
                    <button type="button" 
                            @click="showLogoutModal = false"
                            class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Logout Form -->
    <form method="POST" action="{{ route('logout') }}" x-ref="logoutForm" class="hidden">
        @csrf
    </form>
    
    <!-- Custom Toast Container - Must be outside all other containers -->
    <div id="toast-container" class="fixed top-20 right-4 flex flex-col gap-3 pointer-events-none" style="z-index: 99999;"></div>

    <style>
        /* Toast Animations */
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
        // Beautiful Toast System
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
            
            // Auto-close
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
        });
    </script>

    {{-- Overlay de téléchargement global --}}
    <x-download-overlay />

    {{-- Assistant IA RH --}}
    <x-ai-chat-widget />

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
                if (link.target === '_blank' || link.dataset.downloadHandled) return;
                if (link.dataset.noOverlay === 'true') return;
                
                const href = link.getAttribute('href');
                if (!href) return;
                
                // Ne traiter que si c'est explicitement marqué OU si c'est un vrai pattern de téléchargement
                const hasExplicitDownload = link.hasAttribute('data-download');
                if (!hasExplicitDownload && !isRealDownloadLink(href)) {
                    return; // Ce n'est pas un lien de téléchargement
                }
                
                let fileType = link.dataset.fileType || '';
                let fileName = link.dataset.fileName || '';
                
                if (!fileType) {
                    if (href.includes('pdf')) fileType = 'pdf';
                    else if (href.includes('excel') || href.includes('xlsx')) fileType = 'excel';
                    else if (href.includes('csv')) fileType = 'csv';
                }
                
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
                
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.dispatchEvent(new CustomEvent('start-download', {
                        detail: { url: href, filename: fileName, type: fileType }
                    }));
                });
                
                link.dataset.downloadHandled = 'true';
            }
            
            const exportLinks = document.querySelectorAll('a[href*="export"], a[href*="download"], a[data-download]');
            exportLinks.forEach(setupDownloadLink);
            
            // Observer pour les liens ajoutés dynamiquement
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1) {
                            if (node.matches && node.matches('a[href*="export"], a[href*="download"], a[data-download]')) {
                                setupDownloadLink(node);
                            }
                            const childLinks = node.querySelectorAll && node.querySelectorAll('a[href*="export"], a[href*="download"], a[data-download]');
                            if (childLinks) childLinks.forEach(setupDownloadLink);
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

    {{-- Modale obligatoire d'acceptation du contrat de travail --}}
    @if(isset($needsContractAcceptance) && $needsContractAcceptance && isset($pendingContract))
    <div x-data="{ loading: false }"
         class="fixed inset-0 flex items-center justify-center p-4"
         style="z-index: 99999; background: rgba(0, 0, 0, 0.7); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);">

        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[95vh] flex flex-col overflow-hidden">

            {{-- Header --}}
            <div class="px-6 py-5 border-b border-gray-200 flex-shrink-0"
                 style="background: linear-gradient(135deg, rgba(59, 139, 235, 0.08), rgba(196, 219, 246, 0.15));">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                         style="background: linear-gradient(135deg, #3B8BEB, #2563eb);">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Action Requise : Contrat de Travail</h2>
                        <p class="text-sm text-gray-500 mt-0.5">Veuillez lire attentivement votre contrat avant de continuer</p>
                    </div>
                </div>
            </div>

            {{-- Contract Actions --}}
            <div class="flex-1 min-h-0 bg-gray-100 p-6 flex flex-col items-center justify-center">
                <div class="bg-white rounded-xl border border-gray-200 p-8 max-w-md w-full text-center shadow-sm">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center" style="background-color: rgba(59, 139, 235, 0.1);">
                        <svg class="w-8 h-8" style="color: #3B8BEB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Votre Contrat de Travail</h3>
                    <p class="text-sm text-gray-500 mb-6">Téléchargez ou consultez votre contrat avant de l'accepter.</p>
                    
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <a href="{{ route('employee.contract.view-pdf') }}" 
                           target="_blank"
                           class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-medium rounded-xl border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Lire le contrat
                        </a>
                        <a href="{{ route('employee.documents.download-contract') }}" 
                           class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-medium rounded-xl text-white transition-all"
                           style="background: linear-gradient(135deg, #3B8BEB, #2563eb);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Télécharger
                        </a>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex-shrink-0">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                    <p class="text-xs text-gray-400 text-center sm:text-left">
                        En cliquant "Lu et Accepté", vous confirmez avoir lu et accepté les termes du contrat.
                    </p>
                    <div class="flex items-center gap-3 flex-shrink-0">
                        <form method="POST" action="{{ route('employee.contract.refuse') }}">
                            @csrf
                            <button type="submit"
                                    :disabled="loading"
                                    class="px-5 py-2.5 text-sm font-medium rounded-xl border border-gray-300 text-gray-700 bg-white hover:bg-red-50 hover:text-red-600 hover:border-red-300 transition-all disabled:opacity-50">
                                Refuser et Se Déconnecter
                            </button>
                        </form>

                        <form method="POST" action="{{ route('employee.contract.accept') }}" @submit="loading = true">
                            @csrf
                            <button type="submit"
                                    :disabled="loading"
                                    class="px-6 py-2.5 text-sm font-medium rounded-xl text-white transition-all disabled:opacity-50"
                                    style="background: linear-gradient(135deg, #3B8BEB, #2563eb); box-shadow: 0 4px 14px rgba(59, 139, 235, 0.4);">
                                <span x-show="!loading">Lu et Accepté</span>
                                <span x-show="loading" x-cloak class="flex items-center gap-2">
                                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                    </svg>
                                    Traitement...
                                </span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
    @endif

    <x-push-subscription />

    {{-- 🔔 Alarme globale pré-pointage - fonctionne sur toutes les pages --}}
    <script nonce="{{ $cspNonce ?? '' }}">
    (function() {
        let alarmPlaying = false;
        let bannerShown = false;
        let permBannerShown = false;

        // ===== 1. NOTIFICATION PERMISSION =====
        function showNotifPermissionBanner() {
            if (permBannerShown) return;
            if (!('Notification' in window)) return;
            if (Notification.permission !== 'default') return;

            permBannerShown = true;
            const bar = document.createElement('div');
            bar.id = 'notif-permission-bar';
            bar.style.cssText = 'position:fixed;top:0;left:0;right:0;z-index:100001;';
            bar.innerHTML = `
                <div style="background:linear-gradient(135deg,#3B8BEB,#2563eb);padding:10px 20px;display:flex;align-items:center;justify-content:space-between;gap:12px;box-shadow:0 4px 15px rgba(59,139,235,0.4);">
                    <div style="display:flex;align-items:center;gap:10px;color:white;">
                        <span style="font-size:20px;">🔔</span>
                        <p style="font-size:13px;margin:0;font-weight:500;">Activez les notifications pour recevoir les alertes de pointage sur votre bureau</p>
                    </div>
                    <div style="display:flex;gap:8px;flex-shrink:0;">
                        <button id="notif-perm-btn" style="background:white;color:#2563eb;padding:6px 16px;border-radius:8px;font-weight:600;font-size:12px;border:none;cursor:pointer;white-space:nowrap;">
                            ✅ Activer
                        </button>
                        <button id="notif-perm-dismiss" style="background:rgba(255,255,255,0.2);color:white;padding:6px 12px;border-radius:8px;font-size:12px;border:none;cursor:pointer;">
                            ✕
                        </button>
                    </div>
                </div>
            `;
            document.body.prepend(bar);

            document.getElementById('notif-perm-btn').addEventListener('click', function() {
                Notification.requestPermission().then(function(perm) {
                    console.log('[ManageX] Notification permission result:', perm);
                    bar.remove();
                    if (perm === 'granted') {
                        if (typeof showToast === 'function') {
                            showToast('success', 'Notifications activées ! Vous recevrez les alertes de pointage.', 5000);
                        }
                        // Tester immédiatement
                        new Notification('✅ Notifications ManageX activées', {
                            body: 'Vous recevrez les alertes de pointage directement sur votre bureau.',
                            icon: '{{ asset("icons/icon-192x192.png") }}'
                        });
                    } else if (perm === 'denied') {
                        if (typeof showToast === 'function') {
                            showToast('warning', 'Notifications bloquées. Pour les activer : cliquez sur l\'icône 🔒 dans la barre d\'adresse → Notifications → Autoriser.', 10000);
                        }
                    }
                });
            });

            document.getElementById('notif-perm-dismiss').addEventListener('click', function() {
                bar.remove();
            });
        }

        // ===== 2. PRÉ-CHECK-IN STATUS =====
        async function checkPreCheckInStatus() {
            try {
                const resp = await fetch('{{ route("employee.presences.pre-check-in-status") }}', {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!resp.ok) { console.log('[ManageX Alarm] API error:', resp.status); return; }
                const data = await resp.json();
                console.log('[ManageX Alarm] Status:', JSON.stringify(data));

                if (!data.has_pre_checkin) {
                    stopAlarm();
                    removeBanner();
                    return;
                }

                // Afficher le banner de permission si pas encore accordée
                if ('Notification' in window && Notification.permission === 'default') {
                    showNotifPermissionBanner();
                }

                if (data.is_past_start && !alarmPlaying) {
                    triggerAlarm(data);
                } else if (!data.is_past_start && !bannerShown) {
                    showWaitingBanner(data);
                }
            } catch (e) {
                console.log('[ManageX Alarm] Check failed:', e);
            }
        }

        // ===== 3. ALARM TRIGGER =====
        function triggerAlarm(data) {
            alarmPlaying = true;
            console.log('[ManageX Alarm] 🔔 TRIGGERING ALARM!');

            // Son
            playAlarmSound();

            // Notification bureau
            sendDesktopNotification(data);

            // Banner visuel
            showAlarmBanner(data);

            // Toast comme backup supplémentaire
            if (typeof showToast === 'function') {
                showToast('warning', '⏰ Il est ' + data.work_start_time + ' — Confirmez votre présence ! Arrivé(e) à ' + data.pre_check_in_time, 15000);
            }

            // Relancer le son toutes les 60 secondes
            window._alarmRepeat = setInterval(function() {
                playAlarmSound();
                sendDesktopNotification(data);
            }, 60000);
        }

        function playAlarmSound() {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                let i = 0;
                function beep() {
                    if (i >= 5) return;
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.frequency.value = i % 2 === 0 ? 880 : 660;
                    gain.gain.setValueAtTime(0.4, ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.4);
                    osc.start(ctx.currentTime);
                    osc.stop(ctx.currentTime + 0.4);
                    i++;
                    if (i < 5) setTimeout(beep, 500);
                }
                beep();
            } catch(e) {
                console.log('[ManageX Alarm] Audio error:', e);
            }
        }

        // ===== 4. DESKTOP NOTIFICATION =====
        function sendDesktopNotification(data) {
            if (!('Notification' in window)) {
                console.log('[ManageX Alarm] Notification API not available');
                return;
            }
            console.log('[ManageX Alarm] Notification.permission =', Notification.permission);

            if (Notification.permission !== 'granted') {
                console.log('[ManageX Alarm] Permission not granted, skipping notification');
                return;
            }

            try {
                // Service Worker method (best for background)
                if ('serviceWorker' in navigator) {
                    navigator.serviceWorker.ready.then(function(reg) {
                        console.log('[ManageX Alarm] Sending SW notification');
                        reg.showNotification('⏰ ManageX — Confirmez votre présence !', {
                            body: 'Il est ' + data.work_start_time + '. Vous êtes arrivé(e) à ' + data.pre_check_in_time + '. Cliquez pour confirmer.',
                            icon: '{{ asset("icons/icon-192x192.png") }}',
                            badge: '{{ asset("icons/icon-72x72.png") }}',
                            tag: 'pre-checkin-confirm',
                            renotify: true,
                            requireInteraction: true,
                            vibrate: [300, 100, 300, 100, 300],
                            data: { url: data.confirm_url }
                        }).then(function() {
                            console.log('[ManageX Alarm] SW notification sent!');
                        }).catch(function(e) {
                            console.log('[ManageX Alarm] SW notification failed, using fallback:', e);
                            sendFallbackNotification(data);
                        });
                    }).catch(function(e) {
                        console.log('[ManageX Alarm] SW not ready:', e);
                        sendFallbackNotification(data);
                    });
                } else {
                    sendFallbackNotification(data);
                }
            } catch(e) {
                console.log('[ManageX Alarm] Notification error:', e);
                sendFallbackNotification(data);
            }
        }

        function sendFallbackNotification(data) {
            try {
                console.log('[ManageX Alarm] Sending fallback Notification');
                const n = new Notification('⏰ ManageX — Confirmez votre présence !', {
                    body: 'Il est ' + data.work_start_time + '. Arrivé(e) à ' + data.pre_check_in_time + '.',
                    icon: '{{ asset("icons/icon-192x192.png") }}',
                    tag: 'pre-checkin-confirm',
                    requireInteraction: true
                });
                n.onclick = function() { window.focus(); window.location.href = data.confirm_url; n.close(); };
                console.log('[ManageX Alarm] Fallback notification sent!');
            } catch(e) {
                console.log('[ManageX Alarm] Fallback also failed:', e);
            }
        }

        // ===== 5. BANNERS =====
        function showAlarmBanner(data) {
            removeBanner();
            bannerShown = true;
            const b = document.createElement('div');
            b.id = 'global-precheckin-alarm';
            b.style.cssText = 'position:fixed;top:0;left:0;right:0;z-index:100000;';
            b.innerHTML = `
                <div style="background:linear-gradient(135deg,#059669,#10b981);padding:14px 20px;display:flex;align-items:center;justify-content:space-between;gap:12px;box-shadow:0 4px 20px rgba(5,150,105,0.4);">
                    <div style="display:flex;align-items:center;gap:12px;color:white;">
                        <span style="font-size:28px;animation:pulse 1s infinite;">⏰</span>
                        <div>
                            <p style="font-weight:700;font-size:15px;margin:0;">Il est ${data.work_start_time} — Confirmez votre présence !</p>
                            <p style="font-size:12px;opacity:0.9;margin:3px 0 0;">Arrivé(e) à ${data.pre_check_in_time} · Cliquez pour confirmer votre pointage</p>
                        </div>
                    </div>
                    <a href="${data.confirm_url}" style="background:white;color:#059669;padding:10px 24px;border-radius:12px;font-weight:700;font-size:14px;text-decoration:none;white-space:nowrap;box-shadow:0 2px 10px rgba(0,0,0,0.2);">
                        ✅ Confirmer
                    </a>
                </div>
            `;
            document.body.prepend(b);
        }

        function showWaitingBanner(data) {
            if (bannerShown) return;
            bannerShown = true;
            const b = document.createElement('div');
            b.id = 'global-precheckin-waiting';
            b.style.cssText = 'position:fixed;bottom:20px;right:20px;z-index:100000;';
            b.innerHTML = `
                <div style="background:linear-gradient(135deg,#6366f1,#8b5cf6);padding:12px 16px;border-radius:14px;display:flex;align-items:center;gap:10px;color:white;box-shadow:0 8px 30px rgba(99,102,241,0.4);max-width:320px;">
                    <span style="font-size:20px;">🌅</span>
                    <div style="flex:1;">
                        <p style="font-weight:600;font-size:12px;margin:0;">Pré-pointage actif</p>
                        <p style="font-size:11px;opacity:0.85;margin:2px 0 0;">Arrivé(e) à ${data.pre_check_in_time} · Alarme à ${data.work_start_time}</p>
                    </div>
                    <button onclick="this.closest('#global-precheckin-waiting').remove()" style="background:rgba(255,255,255,0.2);border:none;color:white;width:24px;height:24px;border-radius:6px;cursor:pointer;font-size:14px;">✕</button>
                </div>
            `;
            document.body.appendChild(b);
        }

        function stopAlarm() {
            alarmPlaying = false;
            if (window._alarmRepeat) { clearInterval(window._alarmRepeat); window._alarmRepeat = null; }
        }

        function removeBanner() {
            bannerShown = false;
            ['global-precheckin-alarm', 'global-precheckin-waiting', 'notif-permission-bar'].forEach(function(id) {
                const el = document.getElementById(id);
                if (el) el.remove();
            });
        }

        // ===== 6. INIT =====
        document.addEventListener('DOMContentLoaded', function() {
            console.log('[ManageX Alarm] Init. Notification support:', 'Notification' in window,
                        'Permission:', ('Notification' in window) ? Notification.permission : 'N/A');
            checkPreCheckInStatus();
            setInterval(checkPreCheckInStatus, 30000);
        });

        // Pulse animation
        const s = document.createElement('style');
        s.textContent = '@keyframes pulse{0%,100%{opacity:1;transform:scale(1)}50%{opacity:0.6;transform:scale(1.1)}}';
        document.head.appendChild(s);
    })();
    </script>

    <script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('{{ asset("sw.js") }}')
            .then(r => console.log('ManageX SW registered:', r.scope))
            .catch(e => console.log('ManageX SW failed:', e));

        // Écouter les messages du SW pour jouer des sons d'alarme
        navigator.serviceWorker.addEventListener('message', function(event) {
            if (event.data && event.data.type === 'PLAY_ALARM_SOUND') {
                console.log('[ManageX] SW demande de jouer le son:', event.data.soundType);
                try {
                    const ctx = new (window.AudioContext || window.webkitAudioContext)();
                    const isUrgent = event.data.soundType === 'urgent';
                    let i = 0;
                    const max = isUrgent ? 8 : 5;
                    function beep() {
                        if (i >= max) return;
                        const osc = ctx.createOscillator();
                        const gain = ctx.createGain();
                        osc.connect(gain);
                        gain.connect(ctx.destination);
                        osc.frequency.value = i % 2 === 0 ? (isUrgent ? 1000 : 880) : (isUrgent ? 750 : 660);
                        gain.gain.setValueAtTime(isUrgent ? 0.5 : 0.4, ctx.currentTime);
                        gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.4);
                        osc.start(ctx.currentTime);
                        osc.stop(ctx.currentTime + 0.4);
                        i++;
                        if (i < max) setTimeout(beep, isUrgent ? 400 : 500);
                    }
                    beep();
                } catch(e) {
                    console.log('[ManageX] Erreur son SW:', e);
                }
            }
        });
    }
    </script>
</body>
</html>
