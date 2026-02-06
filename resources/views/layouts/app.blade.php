<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ManageX') }}</title>

        <!-- PWA Meta Tags -->
        <meta name="theme-color" content="#4f46e5">
        <meta name="description" content="ManageX - Application de gestion des ressources humaines">
        <link rel="manifest" href="{{ route('manifest') }}">
        <link rel="apple-touch-icon" href="{{ asset('icons/icon-192x192.png') }}">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta name="apple-mobile-web-app-title" content="ManageX">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <!-- PWA Install Banner -->
        <div id="pwa-install-banner" class="hidden fixed bottom-4 right-4 max-w-[280px] bg-gradient-to-r from-indigo-600 to-purple-600 text-white p-3 rounded-xl shadow-lg z-50">
            <div class="flex items-center gap-2">
                <div class="flex-shrink-0 w-9 h-9 bg-white/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-sm">Installer ManageX</p>
                    <p class="text-xs text-white/80">Acces rapide</p>
                </div>
                <button id="pwa-install-btn" class="px-3 py-1.5 bg-white text-indigo-600 text-xs font-semibold rounded-md hover:bg-gray-100 transition">
                    Installer
                </button>
                <button id="pwa-close-btn" class="p-1 hover:bg-white/20 rounded-full transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <!-- PWA Service Worker & Install Prompt -->
        <script>
            let deferredPrompt;
            const installBanner = document.getElementById('pwa-install-banner');
            const installBtn = document.getElementById('pwa-install-btn');
            const closeBtn = document.getElementById('pwa-close-btn');

            // Service Worker Registration
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('{{ asset("sw.js") }}')
                        .then((registration) => {
                            console.log('ManageX SW registered:', registration.scope);
                        })
                        .catch((error) => {
                            console.log('ManageX SW registration failed:', error);
                        });
                });
            }

            // Capture install prompt
            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                deferredPrompt = e;

                // Check if user dismissed before
                if (localStorage.getItem('pwa-dismissed') !== 'true') {
                    installBanner.classList.remove('hidden');
                }
            });

            // Install button click
            if (installBtn) {
                installBtn.addEventListener('click', async () => {
                    if (deferredPrompt) {
                        deferredPrompt.prompt();
                        const { outcome } = await deferredPrompt.userChoice;
                        console.log('PWA install:', outcome);
                        deferredPrompt = null;
                        installBanner.classList.add('hidden');
                    }
                });
            }

            // Close button click
            if (closeBtn) {
                closeBtn.addEventListener('click', () => {
                    installBanner.classList.add('hidden');
                    localStorage.setItem('pwa-dismissed', 'true');
                });
            }

            // Hide banner if app is installed
            window.addEventListener('appinstalled', () => {
                installBanner.classList.add('hidden');
                console.log('ManageX installed!');
            });
        </script>
    </body>
</html>
