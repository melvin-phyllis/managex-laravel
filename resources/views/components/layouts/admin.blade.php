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

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Chart.js -->
    <script nonce="{{ $cspNonce ?? '' }}" src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                    <x-sidebar-link :href="route('admin.employees.index')" :active="request()->routeIs('admin.employees.*')">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        Employés
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('admin.presences.index')" :active="request()->routeIs('admin.presences.*')">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Présences
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('admin.tasks.index')" :active="request()->routeIs('admin.tasks.*')">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        Taches
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('admin.leaves.index')" :active="request()->routeIs('admin.leaves.*')">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Congés
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('admin.intern-evaluations.index')" :active="request()->routeIs('admin.intern-evaluations.*')">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        Suivi Stagiaires
                    </x-sidebar-link>

                    <!-- Paie Dropdown -->
                    <div x-data="{ open: {{ (request()->routeIs('admin.payrolls.*') || request()->routeIs('admin.payroll-settings.*')) ? 'true' : 'false' }} }">
                        <button @click="open = !open" 
                                class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-200 group {{ (request()->routeIs('admin.payrolls.*') || request()->routeIs('admin.payroll-settings.*')) ? 'text-blue-700 bg-gradient-to-r from-blue-50 to-indigo-50 font-medium border-l-4 border-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 hover:translate-x-1' }}">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <span>Paie</span>
                            </div>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        
                        <div x-show="open" x-cloak x-transition class="space-y-1 mt-1">
                            <x-sidebar-link :href="route('admin.employee-evaluations.index')" :active="request()->routeIs('admin.employee-evaluations.*')" class="pl-12 text-sm">
                                évaluations performances
                            </x-sidebar-link>
                            <x-sidebar-link :href="route('admin.payrolls.index')" :active="request()->routeIs('admin.payrolls.*')" class="pl-12 text-sm">
                                Fiches de paie
                            </x-sidebar-link>
                            <x-sidebar-link :href="route('admin.payroll-settings.countries')" :active="request()->routeIs('admin.payroll-settings.*')" class="pl-12 text-sm">
                                Config. Paie Pays
                            </x-sidebar-link>
                        </div>
                    </div>
                   
                    <x-sidebar-link :href="route('admin.surveys.index')" :active="request()->routeIs('admin.surveys.*')">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Sondages
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('admin.announcements.index')" :active="request()->routeIs('admin.announcements.*')">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                        </svg>
                        Annonces
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('admin.documents.index')" :active="request()->routeIs('admin.documents.*')">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Documents
                    </x-sidebar-link>

                    <!-- Messagerie Dropdown -->
                    <div x-data="{ open: {{ (request()->routeIs('messaging.admin.chat') || request()->routeIs('admin.messaging.*')) ? 'true' : 'false' }} }">
                        <button @click="open = !open" 
                                class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-200 group {{ (request()->routeIs('messaging.admin.chat') || request()->routeIs('admin.messaging.*')) ? 'text-blue-700 bg-gradient-to-r from-blue-50 to-indigo-50 font-medium border-l-4 border-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 hover:translate-x-1' }}">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <span>Messagerie</span>
                            </div>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        
                        <div x-show="open" x-cloak x-transition class="space-y-1 mt-1">
                            <x-sidebar-link :href="route('messaging.admin.chat')" :active="request()->routeIs('messaging.admin.chat')" class="pl-12 text-sm">
                                Chat
                            </x-sidebar-link>
                            <x-sidebar-link :href="route('admin.messaging.index')" :active="request()->routeIs('admin.messaging.*')" class="pl-12 text-sm">
                                Gestion Messages
                            </x-sidebar-link>
                        </div>
                    </div>

                    <!-- Séparateur Organisation -->
                    <div class="pt-4 mt-4 border-t border-gray-200">
                        <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Organisation</p>
                    </div>

                    <x-sidebar-link :href="route('admin.geolocation-zones.index')" :active="request()->routeIs('admin.geolocation-zones.*')">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Zones Géoloc.
                    </x-sidebar-link>

                    
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
                                <p class="text-sm text-gray-500">éŠtes-vous sé»r de vouloir vous déconnecter ?</p>
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
                // Ignorer les liens qui ouvrent dans un nouvel onglet ou qui ont déjé  un handler
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

    @stack('scripts')
</body>
</html>
