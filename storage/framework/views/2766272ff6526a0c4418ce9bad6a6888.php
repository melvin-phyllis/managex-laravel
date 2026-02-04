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
        <!-- Header -->
        <?php if (isset($component)) { $__componentOriginalebb3698994fa8942c93cfabfbefaa3eb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalebb3698994fa8942c93cfabfbefaa3eb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.table-header','data' => ['title' => ''.e($evaluation->week_label).'','subtitle' => 'Détail de mon évaluation']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('table-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => ''.e($evaluation->week_label).'','subtitle' => 'Détail de mon évaluation']); ?>
             <?php $__env->slot('icon', null, []); ?> 
                <div class="w-12 h-12 rounded-full flex items-center justify-center text-xl font-bold
                    <?php if($evaluation->grade_letter === 'A'): ?> bg-green-100 text-green-600
                    <?php elseif($evaluation->grade_letter === 'B'): ?> bg-blue-100 text-blue-600
                    <?php elseif($evaluation->grade_letter === 'C'): ?> bg-yellow-100 text-yellow-600
                    <?php elseif($evaluation->grade_letter === 'D'): ?> bg-orange-100 text-orange-600
                    <?php else: ?> bg-red-100 text-red-600
                    <?php endif; ?>">
                    <?php echo e($evaluation->grade_letter); ?>

                </div>
             <?php $__env->endSlot(); ?>
             <?php $__env->slot('actions', null, []); ?> 
                <a href="<?php echo e(route('employee.evaluations.index')); ?>" class="inline-flex items-center px-4 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 transition-all text-sm">
                    <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'arrow-left','class' => 'w-4 h-4 mr-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-left','class' => 'w-4 h-4 mr-2']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
                    Retour
                </a>
             <?php $__env->endSlot(); ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalebb3698994fa8942c93cfabfbefaa3eb)): ?>
<?php $attributes = $__attributesOriginalebb3698994fa8942c93cfabfbefaa3eb; ?>
<?php unset($__attributesOriginalebb3698994fa8942c93cfabfbefaa3eb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalebb3698994fa8942c93cfabfbefaa3eb)): ?>
<?php $component = $__componentOriginalebb3698994fa8942c93cfabfbefaa3eb; ?>
<?php unset($__componentOriginalebb3698994fa8942c93cfabfbefaa3eb); ?>
<?php endif; ?>

        <!-- Score Summary -->
        <div class="bg-gradient-to-r from-violet-500 to-purple-600 rounded-2xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-lg opacity-75">Note globale</p>
                    <p class="text-sm opacity-50">Évaluée par <?php echo e($evaluation->tutor->name ?? 'Tuteur'); ?></p>
                </div>
                <div class="text-center">
                    <div class="text-5xl font-bold"><?php echo e($evaluation->total_score); ?></div>
                    <div class="text-xl opacity-75">/10</div>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold"><?php echo e($evaluation->grade_letter); ?></div>
                    <div class="text-sm opacity-75"><?php echo e($grades[$evaluation->grade_letter]['label']); ?></div>
                </div>
            </div>
        </div>

        <!-- Criteria Breakdown -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php $__currentLoopData = $criteria; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $criterion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="font-semibold text-gray-900"><?php echo e($criterion['label']); ?></h3>
                            <p class="text-sm text-gray-500"><?php echo e($criterion['description']); ?></p>
                        </div>
                        <div class="text-2xl font-bold text-violet-600">
                            <?php echo e($evaluation->{$key.'_score'}); ?>/2.5
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="w-full bg-gray-200 rounded-full h-3 mb-4">
                        <div class="h-3 rounded-full 
                            <?php if($evaluation->{$key.'_score'} >= 2): ?> bg-green-500
                            <?php elseif($evaluation->{$key.'_score'} >= 1): ?> bg-yellow-500
                            <?php else: ?> bg-red-500
                            <?php endif; ?>" 
                            style="width: <?php echo e(($evaluation->{$key.'_score'} / 2.5) * 100); ?>%">
                        </div>
                    </div>

                    <!-- Comment -->
                    <?php if($evaluation->{$key.'_comment'}): ?>
                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="text-sm text-gray-700"><?php echo e($evaluation->{$key.'_comment'}); ?></p>
                        </div>
                    <?php else: ?>
                        <p class="text-sm text-gray-400 italic">Aucun commentaire</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <!-- General Comments -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 space-y-6">
            <?php if($evaluation->general_comment): ?>
                <div>
                    <h3 class="font-semibold text-gray-900 mb-2">Bilan de la semaine</h3>
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-gray-700"><?php echo e($evaluation->general_comment); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <?php if($evaluation->objectives_next_week): ?>
                <div>
                    <h3 class="font-semibold text-gray-900 mb-2">Objectifs pour la semaine prochaine</h3>
                    <div class="bg-violet-50 border border-violet-200 rounded-xl p-4">
                        <p class="text-violet-800"><?php echo e($evaluation->objectives_next_week); ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Meta info -->
        <div class="text-center text-sm text-gray-400">
            Évaluation soumise le <?php echo e($evaluation->submitted_at?->format('d/m/Y à H:i') ?? 'N/A'); ?>

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
<?php /**PATH D:\ManageX\resources\views\employee\evaluations\show.blade.php ENDPATH**/ ?>