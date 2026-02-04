<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Page non trouvée - <?php echo e(config('app.name', 'ManageX')); ?></title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700 flex items-center justify-center p-6 relative overflow-hidden">
        <!-- Motifs décoratifs -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-20 left-20 w-72 h-72 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-20 right-20 w-96 h-96 bg-purple-500/20 rounded-full blur-3xl"></div>
        </div>

        <!-- Contenu -->
        <div class="relative z-10 text-center w-full max-w-md">
            <div class="bg-white rounded-3xl shadow-2xl p-10 md:p-12">
                <!-- Grand 404 -->
                <div class="mb-6">
                    <span class="text-[140px] md:text-[160px] font-extrabold leading-none bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent block">
                        404
                    </span>
                </div>

                <!-- Message -->
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Page non trouvée</h1>
                <p class="text-gray-500 mb-8 text-base">
                    Oups ! La page que vous recherchez semble avoir disparu ou n'existe pas.
                </p>

                <!-- Actions -->
                <div class="space-y-3">
                    <a href="<?php echo e(url('/')); ?>"
                       class="w-full inline-flex items-center justify-center gap-2 py-3.5 px-6 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all shadow-lg shadow-blue-500/30">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Retour à l'accueil
                    </a>

                    <button onclick="history.back()"
                            class="w-full inline-flex items-center justify-center gap-2 py-3.5 px-6 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Page précédente
                    </button>
                </div>
            </div>

            <!-- Footer -->
            <p class="text-sm text-white/70 mt-8">
                &copy; <?php echo e(date('Y')); ?> ManageX. Tous droits réservés.
            </p>
        </div>
    </div>
</body>
</html><?php /**PATH D:\ManageX\resources\views/errors/404.blade.php ENDPATH**/ ?>