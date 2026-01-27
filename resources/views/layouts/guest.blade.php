<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ManageX') }} - Connexion</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex items-center justify-center bg-gray-50 relative overflow-hidden">
        
        <!-- Motifs décoratifs d'arrière-plan -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl"></div>
        </div>

        <!-- Conteneur Formulaire -->
        <div class="w-full max-w-md p-6 relative z-10">
            <!-- Logo -->
            <div class="text-center mb-8">

                <h1 class="text-3xl font-bold text-gray-900">ManageX</h1>
                <p class="text-gray-500 mt-1">Gestion RH Simplifiée</p>
            </div>

            {{ $slot }}
        </div>
    </div>
</body>
</html>
