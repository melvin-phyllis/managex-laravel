<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Demander une démo - {{ config('app.name', 'ManageX') }}</title>

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#4f46e5">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="manifest" href="{{ route('manifest') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900 bg-gray-50">
    <!-- Particles.js Background -->
    <div id="particles-js" style="position:fixed;top:0;left:0;width:100%;height:100%;z-index:0;"></div>

    <div class="min-h-screen flex items-center justify-center p-6 relative overflow-hidden">
        
        <!-- Motifs décoratifs -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl"></div>
        </div>

        <div class="w-full max-w-5xl bg-white rounded-3xl shadow-xl overflow-hidden relative z-10 grid grid-cols-1 md:grid-cols-2">
            
            <!-- Left Column: Content -->
            <div class="p-8 md:p-12 bg-gradient-to-br from-indigo-50 to-white flex flex-col justify-center">
                <a href="/" class="flex items-center gap-3 mb-8">
                    <x-application-logo class="w-10 h-10 rounded-full shadow-sm" />
                    <span class="text-2xl font-bold text-gray-900 tracking-tight">ManageX</span>
                </a>

                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6 leading-tight">
                    Transformez votre <span class="text-indigo-600">Gestion RH</span> aujourd'hui.
                </h1>
                
                <p class="text-gray-600 mb-8 text-lg">
                    Découvrez comment ManageX simplifie la paie, les congés et la performance pour les entreprises modernes.
                </p>

                <ul class="space-y-4">
                    @foreach(['Tout-en-un intuitif', 'Automatisation IA', 'Support Premium 24/7 de'] as $benefit)
                    <li class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-green-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <span class="text-gray-700 font-medium">{{ $benefit }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>

            <!-- Right Column: Form -->
            <div class="p-8 md:p-12 bg-white" x-data="{ submitting: false }">
                <div class="flex justify-end mb-4">
                     <a href="{{ route('login') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                        Déjà un compte ? Se connecter
                    </a>
                </div>

                <h2 class="text-2xl font-bold text-gray-900 mb-2">Réserver une démo</h2>
                <p class="text-gray-500 mb-6 text-sm">Remplissez ce formulaire pour une présentation sur mesure.</p>

                @if(session('success'))
                <div class="bg-green-50 border border-green-200 rounded-xl p-6 text-center animate-fade-in-up">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Demande envoyée !</h3>
                    <p class="text-gray-600 mb-4">Notre équipe vous contactera sous 24h ouvrées.</p>
                    <a href="/" class="inline-block px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">Retour à l'accueil</a>
                </div>
                @else
                <form action="{{ route('demo-request.store') }}" method="POST" @submit="submitting = true" class="space-y-4">
                    @csrf
                    
                    @if($errors->any())
                    <div class="bg-red-50 text-red-700 p-3 rounded-lg text-sm">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="grid grid-cols-2 gap-4">
                         <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nom complet</label>
                            <input type="text" name="contact_name" required class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" placeholder="Jean Dupont" value="{{ old('contact_name') }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email Pro</label>
                            <input type="email" name="email" required class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" placeholder="jean@societe.com" value="{{ old('email') }}">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Entreprise</label>
                            <input type="text" name="company_name" required class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" placeholder="Mon Entreprise" value="{{ old('company_name') }}">
                        </div>
                         <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Taille</label>
                            <select name="company_size" required class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-white">
                                <option value="" disabled selected>Sélectionner...</option>
                                <option value="1-10">1-10 employés</option>
                                <option value="11-50">11-50 employés</option>
                                <option value="51-200">51-200 employés</option>
                                <option value="200+">200+ employés</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Message (Optionnel)</label>
                        <textarea name="message" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" placeholder="Vos besoins spécifiques...">{{ old('message') }}</textarea>
                    </div>

                    <button type="submit" :disabled="submitting" class="w-full py-3 px-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all shadow-lg shadow-blue-500/25 transform hover:scale-[1.02] active:scale-[0.98] flex items-center justify-center gap-2 disabled:opacity-75 disabled:cursor-not-allowed">
                        <span x-show="!submitting">Obtenir ma démo gratuite</span>
                        <span x-show="submitting" style="display: none;">Envoi en cours...</span>
                    </button>
                    
                    <p class="text-center text-gray-400 text-xs mt-4">
                        Sans engagement. Vos données sont sécurisées.
                    </p>
                </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Particles.js Script -->
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
