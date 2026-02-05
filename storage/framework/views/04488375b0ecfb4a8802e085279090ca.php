<?php if (isset($component)) { $__componentOriginal09d149b94538c2315f503a5e890f2640 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal09d149b94538c2315f503a5e890f2640 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.employee','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.employee'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="space-y-6">
        <!-- Header avec gradient -->
        <div class="rounded-2xl p-6 text-white shadow-xl" style="background-color: #3B8BEB;">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold mb-1">Sondages</h1>
                    <p style="color: #C4DBF6;">Participez aux enquêtes de l'entreprise</p>
                </div>
                <div class="hidden sm:flex w-14 h-14 bg-white/20 rounded-xl items-center justify-center">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: #E7E3D4;">
                        <svg class="w-6 h-6" style="color: #8590AA;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold" style="color: #8590AA;"><?php echo e($surveys->where('has_responded', false)->count()); ?></p>
                        <p class="text-sm text-gray-500">À compléter</p>
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
                        <p class="text-2xl font-bold" style="color: #3B8BEB;"><?php echo e($surveys->where('has_responded', true)->count()); ?></p>
                        <p class="text-sm text-gray-500">Complétés</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="flex gap-2">
            <a href="<?php echo e(route('employee.surveys.index', ['filter' => 'pending'])); ?>" 
               class="px-4 py-2 rounded-lg font-medium transition-all <?php echo e(request('filter', 'pending') === 'pending' ? 'text-white shadow-md' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200'); ?>"
               <?php if(request('filter', 'pending') === 'pending'): ?> style="background-color: #3B8BEB;" <?php endif; ?>>
                À compléter
            </a>
            <a href="<?php echo e(route('employee.surveys.index', ['filter' => 'completed'])); ?>" 
               class="px-4 py-2 rounded-lg font-medium transition-all <?php echo e(request('filter') === 'completed' ? 'text-white shadow-md' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200'); ?>"
               <?php if(request('filter') === 'completed'): ?> style="background-color: #3B8BEB;" <?php endif; ?>>
                Complétés
            </a>
        </div>

        <!-- Surveys Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php $__empty_1 = true; $__currentLoopData = $surveys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $survey): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-all">
                    <div class="p-6">
                        <!-- Survey Header -->
                        <div class="flex items-start gap-4 mb-4">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background-color: #3B8BEB;">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-semibold text-gray-900 truncate"><?php echo e($survey->titre); ?></h3>
                                <p class="text-sm text-gray-500 line-clamp-2 mt-1"><?php echo e($survey->description ?? 'Aucune description'); ?></p>
                            </div>
                        </div>

                        <!-- Survey Info -->
                        <div class="space-y-3">
                            <div class="flex items-center gap-2 text-sm" style="color: #8590AA;">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span><?php echo e($survey->questions->count()); ?> question(s)</span>
                            </div>
                            <?php if($survey->date_limite): ?>
                                <div class="flex items-center gap-2 text-sm <?php echo e($survey->is_expired ? 'text-red-500' : ''); ?>" style="<?php echo e(!$survey->is_expired ? 'color: #8590AA;' : ''); ?>">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>Limite : <?php echo e($survey->date_limite->format('d/m/Y')); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="px-6 py-4 border-t border-gray-100" style="background-color: rgba(231, 227, 212, 0.3);">
                        <?php if($survey->has_responded): ?>
                            <span class="inline-flex items-center gap-2 text-sm font-medium" style="color: #3B8BEB;">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Déjà complété
                            </span>
                        <?php else: ?>
                            <a href="<?php echo e(route('employee.surveys.show', $survey)); ?>" 
                               class="inline-flex items-center gap-2 px-4 py-2 text-white rounded-lg text-sm font-medium transition-all shadow-sm hover:shadow-md" 
                               style="background-color: #3B8BEB;">
                                Répondre
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="col-span-full">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                        <div class="w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background-color: rgba(59, 139, 235, 0.1);">
                            <svg class="w-10 h-10" style="color: #3B8BEB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Aucun sondage</h3>
                        <p class="text-gray-500">
                            <?php echo e(request('filter') === 'completed' ? 'Vous n\'avez encore complété aucun sondage.' : 'Aucun sondage à compléter pour le moment.'); ?>

                        </p>
                        <?php if(request('filter') === 'completed'): ?>
                            <a href="<?php echo e(route('employee.surveys.index')); ?>" 
                               class="inline-flex items-center gap-2 mt-4 px-4 py-2 rounded-lg transition-colors font-medium" 
                               style="background-color: rgba(59, 139, 235, 0.1); color: #3B8BEB;">
                                Voir les sondages à compléter
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal09d149b94538c2315f503a5e890f2640)): ?>
<?php $attributes = $__attributesOriginal09d149b94538c2315f503a5e890f2640; ?>
<?php unset($__attributesOriginal09d149b94538c2315f503a5e890f2640); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal09d149b94538c2315f503a5e890f2640)): ?>
<?php $component = $__componentOriginal09d149b94538c2315f503a5e890f2640; ?>
<?php unset($__componentOriginal09d149b94538c2315f503a5e890f2640); ?>
<?php endif; ?>
<?php /**PATH D:\ManageX\resources\views/employee/surveys/index.blade.php ENDPATH**/ ?>