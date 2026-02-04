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
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Détail de l'évaluation</h1>
                <p class="text-sm text-gray-500 mt-1"><?php echo e($employeeEvaluation->periode_label); ?></p>
            </div>
            <div class="flex gap-3">
                <?php if($employeeEvaluation->canBeEdited()): ?>
                    <a href="<?php echo e(route('admin.employee-evaluations.edit', $employeeEvaluation)); ?>" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Modifier
                    </a>
                <?php endif; ?>
                <a href="<?php echo e(route('admin.employee-evaluations.index', ['month' => $employeeEvaluation->month, 'year' => $employeeEvaluation->year])); ?>" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Retour
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Détails -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Employé -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-xl">
                            <?php echo e(strtoupper(substr($employeeEvaluation->user->name, 0, 2))); ?>

                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900"><?php echo e($employeeEvaluation->user->name); ?></h2>
                            <p class="text-gray-500"><?php echo e($employeeEvaluation->user->poste ?? $employeeEvaluation->user->contract_type); ?></p>
                            <p class="text-sm text-gray-400"><?php echo e($employeeEvaluation->user->email); ?></p>
                        </div>
                        <div class="ml-auto">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                <?php echo e($employeeEvaluation->status === 'validated' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'); ?>">
                                <?php echo e($employeeEvaluation->status_label); ?>

                            </span>
                        </div>
                    </div>
                </div>

                <!-- Critères d'évaluation -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Critères d'évaluation</h3>
                    
                    <div class="space-y-4">
                        <?php $__currentLoopData = $criteria; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $criterion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $value = $employeeEvaluation->{$key};
                                $max = $criterion['max'];
                                $percentage = ($value / $max) * 100;
                            ?>
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-medium text-gray-900"><?php echo e($criterion['label']); ?></span>
                                    <span class="font-bold <?php echo e($percentage >= 75 ? 'text-green-600' : ($percentage >= 50 ? 'text-yellow-600' : 'text-red-600')); ?>">
                                        <?php echo e(number_format($value, 1)); ?>/<?php echo e($max); ?>

                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full transition-all <?php echo e($percentage >= 75 ? 'bg-green-500' : ($percentage >= 50 ? 'bg-yellow-500' : 'bg-red-500')); ?>" 
                                         style="width: <?php echo e($percentage); ?>%"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1"><?php echo e($criterion['description']); ?></p>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                <!-- Commentaires -->
                <?php if($employeeEvaluation->comments): ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Commentaires</h3>
                    <p class="text-gray-600 whitespace-pre-line"><?php echo e($employeeEvaluation->comments); ?></p>
                </div>
                <?php endif; ?>

                <!-- Métadonnées -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations</h3>
                    <dl class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <dt class="text-gray-500">Évalué par</dt>
                            <dd class="font-medium text-gray-900"><?php echo e($employeeEvaluation->evaluator->name ?? 'N/A'); ?></dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Date de création</dt>
                            <dd class="font-medium text-gray-900"><?php echo e($employeeEvaluation->created_at->format('d/m/Y H:i')); ?></dd>
                        </div>
                        <?php if($employeeEvaluation->validated_at): ?>
                        <div>
                            <dt class="text-gray-500">Date de validation</dt>
                            <dd class="font-medium text-gray-900"><?php echo e($employeeEvaluation->validated_at->format('d/m/Y H:i')); ?></dd>
                        </div>
                        <?php endif; ?>
                    </dl>
                </div>
            </div>

            <!-- Résumé -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Résumé</h3>
                    
                    <!-- Score total -->
                    <div class="text-center p-6 bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl mb-6">
                        <p class="text-sm text-gray-500 mb-1">Note totale</p>
                        <div class="flex items-baseline justify-center gap-1">
                            <span class="text-4xl font-bold <?php echo e($employeeEvaluation->total_score >= 4 ? 'text-green-600' : ($employeeEvaluation->total_score >= 2.5 ? 'text-yellow-600' : 'text-red-600')); ?>">
                                <?php echo e(number_format($employeeEvaluation->total_score, 1)); ?>

                            </span>
                            <span class="text-xl text-gray-400">/5,5</span>
                        </div>
                        <div class="mt-3 w-full bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full <?php echo e($employeeEvaluation->total_score >= 4 ? 'bg-green-500' : ($employeeEvaluation->total_score >= 2.5 ? 'bg-yellow-500' : 'bg-red-500')); ?>" 
                                 style="width: <?php echo e($employeeEvaluation->score_percentage); ?>%"></div>
                        </div>
                        <p class="text-sm text-gray-500 mt-2"><?php echo e($employeeEvaluation->score_percentage); ?>%</p>
                    </div>

                    <!-- Salaire calculé -->
                    <div class="text-center p-6 bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl mb-6">
                        <p class="text-sm text-gray-500 mb-1">Salaire brut calculé</p>
                        <p class="text-3xl font-bold text-green-600"><?php echo e($employeeEvaluation->calculated_salary_formatted); ?></p>
                        <p class="text-xs text-gray-500 mt-2">SMIC : <?php echo e(number_format($smic, 0, ',', ' ')); ?> FCFA</p>
                    </div>

                    <!-- Formule -->
                    <div class="p-4 bg-gray-50 rounded-lg text-center text-sm">
                        <p class="text-gray-500 mb-2">Formule de calcul</p>
                        <p class="font-mono text-gray-700">
                            <?php echo e(number_format($employeeEvaluation->total_score, 1)); ?> × <?php echo e(number_format($smic, 0, ',', ' ')); ?> = 
                            <span class="font-bold"><?php echo e(number_format($employeeEvaluation->total_score * $smic, 0, ',', ' ')); ?></span>
                        </p>
                        <?php if($employeeEvaluation->calculated_salary > ($employeeEvaluation->total_score * $smic)): ?>
                            <p class="text-xs text-amber-600 mt-2">* Minimum SMIC appliqué</p>
                        <?php endif; ?>
                    </div>

                    <?php if($employeeEvaluation->canBeEdited()): ?>
                    <div class="mt-6 space-y-3">
                        <form action="<?php echo e(route('admin.employee-evaluations.validate', $employeeEvaluation)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <button type="submit" 
                                    onclick="return confirm('Valider cette évaluation ?')"
                                    class="w-full px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
                                Valider l'évaluation
                            </button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
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
<?php /**PATH D:\ManageX\resources\views\admin\employee-evaluations\show.blade.php ENDPATH**/ ?>