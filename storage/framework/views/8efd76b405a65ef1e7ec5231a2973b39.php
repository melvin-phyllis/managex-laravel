<?php
    $isAdmin = auth()->user()->isAdmin();
    $routePrefix = $isAdmin ? 'admin.tutor.evaluations' : 'employee.tutor.evaluations';
?>

<?php if (isset($component)) { $__componentOriginal511d4862ff04963c3c16115c05a86a9d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal511d4862ff04963c3c16115c05a86a9d = $attributes; } ?>
<?php $component = Illuminate\View\DynamicComponent::resolve(['component' => $isAdmin ? 'layouts.admin' : 'layouts.employee'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dynamic-component'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\DynamicComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="space-y-6">
        <!-- Header avec gradient -->
        <div class="rounded-2xl p-6 text-white shadow-xl" style="background-color: #3B8BEB;">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold mb-1">Mes Stagiaires à Évaluer</h1>
                    <p style="color: #C4DBF6;">Semaine du <?php echo e(now()->startOfWeek()->format('d/m/Y')); ?> au <?php echo e(now()->endOfWeek()->format('d/m/Y')); ?></p>
                </div>
                <div class="hidden sm:flex w-14 h-14 bg-white/20 rounded-xl items-center justify-center">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: #3B8BEB;">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900"><?php echo e($stats['total_interns']); ?></p>
                        <p class="text-sm text-gray-500">Total stagiaires</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: rgba(59, 139, 235, 0.15);">
                        <svg class="w-6 h-6" style="color: #3B8BEB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold" style="color: #3B8BEB;"><?php echo e($stats['evaluated_this_week']); ?></p>
                        <p class="text-sm text-gray-500">Évalués cette semaine</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: #E7E3D4;">
                        <svg class="w-6 h-6" style="color: #8590AA;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold" style="color: #8590AA;"><?php echo e($stats['pending']); ?></p>
                        <p class="text-sm text-gray-500">En attente</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des stagiaires -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100" style="background-color: rgba(59, 139, 235, 0.03);">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5" style="color: #3B8BEB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Stagiaires supervisés
                </h3>
            </div>

            <?php if($interns->isEmpty()): ?>
                <div class="p-12 text-center">
                    <div class="w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background-color: rgba(59, 139, 235, 0.1);">
                        <svg class="w-10 h-10" style="color: #3B8BEB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Aucun stagiaire assigné</h3>
                    <p class="text-gray-500">Vous n'avez pas encore de stagiaire à superviser</p>
                </div>
            <?php else: ?>
                <div class="divide-y divide-gray-100">
                    <?php $__currentLoopData = $interns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $intern): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="p-5 hover:bg-gray-50 transition-colors">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-md" style="background-color: #3B8BEB;">
                                        <?php echo e(strtoupper(substr($intern->name, 0, 2))); ?>

                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900"><?php echo e($intern->name); ?></p>
                                        <p class="text-sm text-gray-500"><?php echo e($intern->department->name ?? 'Sans département'); ?></p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 flex-wrap">
                                    <?php if($intern->current_week_evaluation): ?>
                                        <?php if($intern->current_week_evaluation->status === 'submitted'): ?>
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium" style="background-color: rgba(59, 139, 235, 0.1); color: #3B8BEB;">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                Évalué (<?php echo e($intern->current_week_evaluation->total_score); ?>/10)
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium" style="background-color: rgba(133, 144, 170, 0.15); color: #8590AA;">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                                Brouillon
                                            </span>
                                            <a href="<?php echo e(route($routePrefix . '.edit', $intern->current_week_evaluation)); ?>" 
                                               class="inline-flex items-center gap-2 px-4 py-2 text-white rounded-lg text-sm font-medium transition-all shadow-sm" style="background-color: #3B8BEB;">
                                                Continuer
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </a>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium" style="background-color: #E7E3D4; color: #8590AA;">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            À évaluer
                                        </span>
                                        <a href="<?php echo e(route($routePrefix . '.create', $intern)); ?>" 
                                           class="inline-flex items-center gap-2 px-4 py-2 text-white rounded-lg text-sm font-medium transition-all shadow-sm" style="background-color: #3B8BEB;">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Évaluer
                                        </a>
                                    <?php endif; ?>

                                    <a href="<?php echo e(route($routePrefix . '.history', $intern)); ?>" 
                                       class="inline-flex items-center gap-1 px-3 py-2 text-gray-600 rounded-lg transition-colors text-sm" style="hover:color: #3B8BEB;"
                                       title="Historique">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="hidden sm:inline">Historique</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $attributes = $__attributesOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $component = $__componentOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__componentOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
<?php /**PATH D:\ManageX\resources\views/tutor/evaluations/index.blade.php ENDPATH**/ ?>