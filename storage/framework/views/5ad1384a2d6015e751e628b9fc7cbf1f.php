<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e(config('app.name', 'ManageX')); ?> - Connexion</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Toastify CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex items-center justify-center bg-gray-50 relative overflow-hidden">
        
        <!-- Motifs décoratifs d'arriére-plan -->
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

            <?php echo e($slot); ?>

        </div>
    </div>


    <!-- Toastify JS -->
    <script nonce="<?php echo e($cspNonce ?? ''); ?>" type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <script nonce="<?php echo e($cspNonce ?? ''); ?>">
        document.addEventListener('DOMContentLoaded', function() {
            // Success Toast
            <?php if(session('success') || session('status')): ?>
                Toastify({
                    text: "<?php echo e(session('success') ?? session('status')); ?>",
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
            <?php endif; ?>

            // Error Toast
            <?php if(session('error')): ?>
                Toastify({
                    text: "<?php echo e(session('error')); ?>",
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
            <?php endif; ?>

            // Validation Errors (specifically auth failed)
            <?php if($errors->any()): ?>
                <?php if($errors->has('email') || $errors->has('password')): ?>
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
                <?php endif; ?>
                
                // Other general validation errors (optional or separate)
            <?php endif; ?>
        });
    </script>
</body>
</html>
<?php /**PATH D:\ManageX\resources\views/layouts/guest.blade.php ENDPATH**/ ?>