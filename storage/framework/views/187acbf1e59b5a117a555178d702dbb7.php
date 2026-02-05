<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header avec gradient -->
        <div class="relative overflow-hidden rounded-2xl shadow-xl animate-fade-in-up" style="background: linear-gradient(135deg, #5680E9, #84CEEB) !important;">
            <div class="absolute inset-0 bg-black/10"></div>
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            
            <div class="relative p-6 md:p-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <a href="<?php echo e(route('admin.global-documents.index')); ?>" class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center text-white hover:bg-white/30 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                        </a>
                        <div>
                            <nav class="flex mb-2" aria-label="Breadcrumb">
                                <ol class="inline-flex items-center space-x-1">
                                    <li><a href="<?php echo e(route('admin.global-documents.index')); ?>" class="text-white/70 hover:text-white text-sm">Documents Globaux</a></li>
                                    <li><span class="text-white/50 mx-2">/</span></li>
                                    <li><span class="text-white text-sm font-medium">Détails</span></li>
                                </ol>
                            </nav>
                            <h1 class="text-2xl md:text-3xl font-bold text-white"><?php echo e($globalDocument->title); ?></h1>
                            <p class="text-white/80 mt-1"><?php echo e($globalDocument->type_label); ?></p>
                        </div>
                    </div>
                    <a href="<?php echo e(route('admin.global-documents.download', $globalDocument)); ?>"
                       class="px-5 py-2.5 bg-white font-semibold rounded-xl hover:bg-purple-50 transition-all shadow-lg flex items-center gap-2" style="color: #5680E9;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Télécharger
                    </a>
                </div>
            </div>
        </div>

        <div class="grid md:grid-cols-3 gap-6 animate-fade-in-up animation-delay-100">
            <!-- Détails du document -->
            <div class="md:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100" style="background: linear-gradient(135deg, #5680E9, #84CEEB);">
                        <h2 class="font-semibold text-white flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Informations
                        </h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Fichier :</span>
                                <p class="font-medium text-gray-900 flex items-center gap-2 mt-1">
                                    <svg class="w-5 h-5" style="color: #5680E9;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    <?php echo e($globalDocument->original_filename); ?>

                                </p>
                            </div>
                            <div>
                                <span class="text-gray-500">Taille :</span>
                                <p class="font-medium text-gray-900 mt-1"><?php echo e($globalDocument->file_size_formatted); ?></p>
                            </div>
                            <div>
                                <span class="text-gray-500">Ajouté le :</span>
                                <p class="font-medium text-gray-900 mt-1"><?php echo e($globalDocument->created_at->format('d/m/Y à H:i')); ?></p>
                            </div>
                            <div>
                                <span class="text-gray-500">Par :</span>
                                <p class="font-medium text-gray-900 mt-1"><?php echo e($globalDocument->uploader->name ?? 'N/A'); ?></p>
                            </div>
                        </div>

                        <?php if($globalDocument->description): ?>
                            <div class="pt-4 border-t border-gray-100">
                                <span class="text-sm text-gray-500">Description :</span>
                                <p class="text-gray-900 mt-1"><?php echo e($globalDocument->description); ?></p>
                            </div>
                        <?php endif; ?>

                        <div class="pt-4 border-t border-gray-100">
                            <span class="text-sm text-gray-500">Statut :</span>
                            <?php if($globalDocument->is_active): ?>
                                <span class="ml-2 px-2 py-1 text-xs font-medium rounded-full" style="background-color: #5680E920; color: #5680E9;">Actif</span>
                            <?php else: ?>
                                <span class="ml-2 px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-500">Inactif</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistiques d'accusé -->
            <div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100" style="background: linear-gradient(135deg, #8860D0, #C1C8E4);">
                        <h2 class="font-semibold text-white flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Accusés de réception
                        </h2>
                    </div>
                    <div class="p-6">
                        <?php
                            $acknowledgedCount = $globalDocument->acknowledgedBy->count();
                            // $totalEmployees passé depuis le contrôleur
                            $percentage = $totalEmployees > 0 ? round(($acknowledgedCount / $totalEmployees) * 100) : 0;
                        ?>

                        <div class="text-center mb-4">
                            <div class="text-4xl font-bold" style="color: #5680E9;"><?php echo e($acknowledgedCount); ?>/<?php echo e($totalEmployees); ?></div>
                            <p class="text-gray-500 text-sm mt-1">employés ont accusé réception</p>
                        </div>

                        <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
                            <div class="h-2 rounded-full transition-all" style="width: <?php echo e($percentage); ?>%; background: linear-gradient(90deg, #5680E9, #84CEEB);"></div>
                        </div>

                        <?php if($usersNotAcknowledged->count() > 0): ?>
                            <div class="border-t border-gray-100 pt-4">
                                <p class="text-sm font-medium text-gray-700 mb-2">Non lu par :</p>
                                <div class="space-y-2 max-h-40 overflow-y-auto">
                                    <?php $__currentLoopData = $usersNotAcknowledged->take(10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="flex items-center gap-2 text-sm">
                                            <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-medium text-white" style="background: linear-gradient(135deg, #5680E9, #84CEEB);">
                                                <?php echo e(strtoupper(substr($user->name, 0, 1))); ?>

                                            </div>
                                            <span class="text-gray-700"><?php echo e($user->name); ?></span>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($usersNotAcknowledged->count() > 10): ?>
                                        <p class="text-xs text-gray-500 text-center">+<?php echo e($usersNotAcknowledged->count() - 10); ?> autres</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <p class="text-center text-sm" style="color: #5680E9;">
                                <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Tous les employés ont lu le document
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-between items-center bg-white rounded-xl shadow-sm border border-gray-100 p-4 animate-fade-in-up animation-delay-200">
            <a href="<?php echo e(route('admin.global-documents.edit', $globalDocument)); ?>"
               class="flex items-center gap-2 font-medium transition hover:opacity-80" style="color: #5680E9;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Modifier ce document
            </a>
            <form action="<?php echo e(route('admin.global-documents.destroy', $globalDocument)); ?>" method="POST"
                  onsubmit="return confirm('Supprimer ce document ?')">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="submit" class="flex items-center gap-2 text-red-600 hover:text-red-800 font-medium transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Supprimer
                </button>
            </form>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3)): ?>
<?php $attributes = $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3; ?>
<?php unset($__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc8c9fd5d7827a77a31381de67195f0c3)): ?>
<?php $component = $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3; ?>
<?php unset($__componentOriginalc8c9fd5d7827a77a31381de67195f0c3); ?>
<?php endif; ?>
<?php /**PATH D:\ManageX\resources\views/admin/global-documents/show.blade.php ENDPATH**/ ?>