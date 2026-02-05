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
    <div class="max-w-5xl mx-auto space-y-6">
        <!-- Header comme sur tasks -->
        <div class="relative overflow-hidden rounded-2xl shadow-xl animate-fade-in-up" style="background: linear-gradient(135deg, #5680E9, #84CEEB) !important;">
            <div class="absolute inset-0 bg-black/10"></div>
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            
            <div class="relative p-6 md:p-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <nav class="flex mb-3" aria-label="Breadcrumb">
                            <ol class="inline-flex items-center space-x-1">
                                <li><a href="<?php echo e(route('admin.dashboard')); ?>" class="text-white/70 hover:text-white text-sm">Dashboard</a></li>
                                <li><span class="text-white/50 mx-2">/</span></li>
                                <li><a href="<?php echo e(route('admin.announcements.index')); ?>" class="text-white/70 hover:text-white text-sm">Annonces</a></li>
                                <li><span class="text-white/50 mx-2">/</span></li>
                                <li><span class="text-white text-sm font-medium">Détails</span></li>
                            </ol>
                        </nav>
                        <h1 class="text-2xl md:text-3xl font-bold text-white flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                <span class="text-2xl"><?php echo e($announcement->type_icon); ?></span>
                            </div>
                            <?php echo e($announcement->title); ?>

                        </h1>
                        <p class="text-white/80 mt-2">Créée <?php echo e($announcement->created_at->diffForHumans()); ?> par <?php echo e($announcement->creator?->name); ?></p>
                    </div>
                    <a href="<?php echo e(route('admin.announcements.edit', $announcement)); ?>" 
                       class="px-4 py-2.5 bg-white font-semibold rounded-xl hover:bg-purple-50 transition-all shadow-lg flex items-center" style="color: #5680E9;">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Modifier
                    </a>
                </div>
            </div>
        </div>

        <!-- Badges -->
        <div class="flex flex-wrap gap-2 animate-fade-in-up animation-delay-100">
            <?php if($announcement->is_pinned): ?>
                <span class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium rounded-full" style="background-color: rgba(245, 158, 11, 0.15); color: #D97706;">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5 5a2 2 0 012-2h6a2 2 0 012 2v2a2 2 0 01-2 2H7a2 2 0 01-2-2V5zM4 9v6a2 2 0 002 2h8a2 2 0 002-2V9a1 1 0 00-1-1H5a1 1 0 00-1 1z"/>
                    </svg>
                    Épinglée
                </span>
            <?php endif; ?>
            <?php if($announcement->is_active): ?>
                <span class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium rounded-full" style="background-color: rgba(90, 185, 234, 0.15); color: #5680E9;">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Active
                </span>
            <?php else: ?>
                <span class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium rounded-full bg-gray-100 text-gray-600">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    Inactive
                </span>
            <?php endif; ?>
            <span class="px-3 py-1.5 text-sm font-medium rounded-full" style="background-color: rgba(86, 128, 233, 0.15); color: #5680E9;">
                <?php echo e(ucfirst($announcement->type)); ?>

            </span>
            <span class="px-3 py-1.5 text-sm font-medium rounded-full bg-gray-100 text-gray-700">
                Priorité <?php echo e($announcement->priority); ?>

            </span>
            <?php if($announcement->requires_acknowledgment): ?>
                <span class="px-3 py-1.5 text-sm font-medium rounded-full" style="background-color: rgba(136, 96, 208, 0.15); color: #8860D0;">
                    Accusé requis
                </span>
            <?php endif; ?>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fade-in-up animation-delay-200">
            <!-- Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Announcement Content -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(86, 128, 233, 0.15);">
                            <svg class="w-4 h-4" style="color: #5680E9;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        Contenu
                    </h2>
                    <div class="prose max-w-none text-gray-700">
                        <?php echo nl2br(e($announcement->content)); ?>

                    </div>
                </div>

                <!-- Read Users -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-4 border-b border-gray-100 flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(90, 185, 234, 0.15);">
                            <svg class="w-4 h-4" style="color: #5AB9EA;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <h2 class="text-lg font-semibold text-gray-900">Ont lu (<?php echo e($stats['read_count']); ?>)</h2>
                    </div>
                    <div class="max-h-80 overflow-y-auto">
                        <?php $__empty_1 = true; $__currentLoopData = $readUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $read): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="flex items-center justify-between p-4 border-b border-gray-50 last:border-0 hover:bg-purple-50/50 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center overflow-hidden" style="background: linear-gradient(135deg, #5680E9, #84CEEB);">
                                        <?php if($read->user?->avatar): ?>
                                            <img src="<?php echo e(avatar_url($read->user->avatar)); ?>" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <span class="text-white font-medium"><?php echo e(substr($read->user?->name ?? '?', 0, 1)); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900"><?php echo e($read->user?->name ?? 'Utilisateur supprimé'); ?></p>
                                        <p class="text-sm text-gray-500"><?php echo e($read->user?->email); ?></p>
                                    </div>
                                </div>
                                <div class="text-right text-sm">
                                    <p class="text-gray-500">Lu le <?php echo e($read->read_at->format('d/m/Y H:i')); ?></p>
                                    <?php if($read->acknowledged_at): ?>
                                        <p style="color: #5AB9EA;">✓ Accusé <?php echo e($read->acknowledged_at->format('d/m/Y H:i')); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="p-8 text-center text-gray-500">
                                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m9 5.197v-1a6 6 0 00-3-5.197"/>
                                    </svg>
                                </div>
                                Personne n'a encore lu cette annonce.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Unread Users -->
                <?php if($unreadUsers->count() > 0): ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-4 border-b border-gray-100 flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(239, 68, 68, 0.15);">
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                        <h2 class="text-lg font-semibold text-gray-900">N'ont pas lu (<?php echo e($unreadUsers->count()); ?>)</h2>
                    </div>
                    <div class="max-h-60 overflow-y-auto">
                        <?php $__currentLoopData = $unreadUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex items-center p-4 border-b border-gray-50 last:border-0 hover:bg-purple-50/50 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gray-200 rounded-xl flex items-center justify-center overflow-hidden">
                                        <?php if($user->avatar): ?>
                                            <img src="<?php echo e(avatar_url($user->avatar)); ?>" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <span class="text-gray-500 font-medium"><?php echo e(substr($user->name, 0, 1)); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900"><?php echo e($user->name); ?></p>
                                        <p class="text-sm text-gray-500"><?php echo e($user->email); ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar Stats -->
            <div class="space-y-6">
                <!-- Stats Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(132, 206, 235, 0.15);">
                            <svg class="w-4 h-4" style="color: #84CEEB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        Statistiques
                    </h2>
                    
                    <div class="space-y-4">
                        <!-- Read Progress -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm text-gray-600">Taux de lecture</span>
                                <span class="text-sm font-semibold" style="color: #5680E9;"><?php echo e($stats['read_percentage']); ?>%</span>
                            </div>
                            <div class="h-3 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all" 
                                     style="width: <?php echo e($stats['read_percentage']); ?>%; background: linear-gradient(90deg, #5680E9, #5AB9EA);"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1"><?php echo e($stats['read_count']); ?> / <?php echo e($stats['total_target']); ?> personnes</p>
                        </div>

                        <?php if($announcement->requires_acknowledgment): ?>
                        <!-- Acknowledged Progress -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm text-gray-600">Accusés de réception</span>
                                <span class="text-sm font-semibold" style="color: #8860D0;"><?php echo e($stats['acknowledged_percentage']); ?>%</span>
                            </div>
                            <div class="h-3 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all" 
                                     style="width: <?php echo e($stats['acknowledged_percentage']); ?>%; background: linear-gradient(90deg, #8860D0, #C1C8E4);"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1"><?php echo e($stats['acknowledged_count']); ?> / <?php echo e($stats['total_target']); ?> personnes</p>
                        </div>
                        <?php endif; ?>

                        <hr class="border-gray-100">

                        <!-- Quick Stats -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center p-3 bg-gray-50 rounded-xl">
                                <p class="text-2xl font-bold" style="color: #5680E9;"><?php echo e($stats['read_count']); ?></p>
                                <p class="text-xs text-gray-500">Lecteurs</p>
                            </div>
                            <div class="text-center p-3 bg-gray-50 rounded-xl">
                                <p class="text-2xl font-bold" style="color: #8860D0;"><?php echo e($stats['total_target'] - $stats['read_count']); ?></p>
                                <p class="text-xs text-gray-500">Non lus</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(86, 128, 233, 0.15);">
                            <svg class="w-4 h-4" style="color: #5680E9;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        Informations
                    </h2>
                    
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Ciblage</span>
                            <span class="font-medium text-gray-900"><?php echo e($announcement->target_label); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Créée le</span>
                            <span class="font-medium text-gray-900"><?php echo e($announcement->created_at->format('d/m/Y H:i')); ?></span>
                        </div>
                        <?php if($announcement->start_date): ?>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Début</span>
                            <span class="font-medium text-gray-900"><?php echo e($announcement->start_date->format('d/m/Y')); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if($announcement->end_date): ?>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Fin</span>
                            <span class="font-medium text-gray-900"><?php echo e($announcement->end_date->format('d/m/Y')); ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Vues</span>
                            <span class="font-medium text-gray-900"><?php echo e($announcement->view_count); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Back Button -->
                <a href="<?php echo e(route('admin.announcements.index')); ?>" class="flex items-center justify-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Retour aux annonces
                </a>
            </div>
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
<?php /**PATH D:\ManageX\resources\views/admin/announcements/show.blade.php ENDPATH**/ ?>