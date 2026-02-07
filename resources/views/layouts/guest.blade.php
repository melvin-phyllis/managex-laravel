<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ManageX') }} - Connexion</title>

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#4f46e5">
    <meta name="description" content="ManageX - Application de gestion des ressources humaines">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="manifest" href="{{ route('manifest') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="ManageX">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Toastify CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <!-- Particles.js Background -->
    <div id="particles-js" style="position:fixed;top:0;left:0;width:100%;height:100%;z-index:0;"></div>

    <div class="min-h-screen flex items-center justify-center bg-gray-50 relative overflow-hidden">

        <!-- Motifs décoratifs d'arriére-plan -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl"></div>
        </div>

        <!-- Conteneur Formulaire -->
        <div class="w-full max-w-md p-6 relative z-10">
            <!-- Logo -->
            <div class="flex flex-row items-center justify-center mb-8 gap-4">
                <a href="/">
                    <x-application-logo class="w-16 h-16 rounded-full object-cover shadow-md" />
                </a>
                <span class="text-3xl font-bold text-gray-900 tracking-tight">ManageX</span>
            </div>

            {{ $slot }}
        </div>
    </div>


    <!-- Toastify JS -->
    <script nonce="{{ $cspNonce ?? '' }}" type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <script nonce="{{ $cspNonce ?? '' }}">
        document.addEventListener('DOMContentLoaded', function() {
            // Success Toast
            @if(session('success') || session('status'))
                Toastify({
                    text: "{{ session('success') ?? session('status') }}",
                    duration: 4000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    stopOnFocus: true,
                    style: {
                        background: "linear-gradient(to right, #10b981, #059669)", // Emerald gradient
                        borderRadius: "10px",
                        boxShadow: "0 4px 6px -1px rgba(0, 0, 0, 0.1)",
                    },
                }).showToast();
            @endif

            // Error Toast
            @if(session('error'))
                Toastify({
                    text: "{{ session('error') }}",
                    duration: 4000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    stopOnFocus: true,
                    style: {
                        background: "linear-gradient(to right, #ef4444, #b91c1c)", // Red gradient
                        borderRadius: "10px",
                        boxShadow: "0 4px 6px -1px rgba(0, 0, 0, 0.1)",
                    },
                }).showToast();
            @endif

            // Validation Errors (specifically auth failed)
            @if($errors->any())
                @if($errors->has('email') || $errors->has('password'))
                    Toastify({
                        text: "Identification de connexion incorrecte", // Custom message for login failure
                        duration: 4000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        stopOnFocus: true,
                        style: {
                            background: "linear-gradient(to right, #ef4444, #b91c1c)", // Red gradient
                            borderRadius: "10px",
                            boxShadow: "0 4px 6px -1px rgba(0, 0, 0, 0.1)",
                        },
                    }).showToast();
                @endif
                
                // Other general validation errors (optional or separate)
            @endif
        });
    </script>

    <!-- PWA Service Worker Registration -->
    <script>
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
    </script>

    <!-- Particles.js -->
    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof particlesJS !== 'undefined') {
                particlesJS('particles-js', {
                    particles: {
                        number: { value: 50, density: { enable: true, value_area: 800 } },
                        color: { value: '#6c4cec' },
                        shape: { type: 'circle' },
                        opacity: { value: 0.12, random: true, anim: { enable: true, speed: 0.6, opacity_min: 0.04, sync: false } },
                        size: { value: 3, random: true, anim: { enable: true, speed: 2, size_min: 0.5, sync: false } },
                        line_linked: { enable: true, distance: 130, color: '#6c4cec', opacity: 0.07, width: 1 },
                        move: { enable: true, speed: 1, direction: 'none', random: true, straight: false, out_mode: 'out', bounce: false }
                    },
                    interactivity: {
                        detect_on: 'window',
                        events: { onhover: { enable: true, mode: 'grab' }, onclick: { enable: true, mode: 'push' }, resize: true },
                        modes: { grab: { distance: 120, line_linked: { opacity: 0.15 } } }
                    },
                    retina_detect: true
                });
            }
        });
    </script>
</body>
</html>
