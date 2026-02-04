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
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center gap-4">
            <a href="<?php echo e(route('admin.documents.index')); ?>" 
               class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">⚠️ Documents Expirant</h1>
                <p class="text-gray-500">Documents qui expirent dans les 60 prochains jours</p>
            </div>
        </div>

        <!-- Documents List -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Document</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employé</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expiration</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $daysLeft = now()->diffInDays($document->expiry_date, false);
                        ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="text-xl"><?php echo e($document->file_icon); ?></span>
                                    <div>
                                        <p class="font-medium text-gray-900"><?php echo e($document->title); ?></p>
                                        <p class="text-sm text-gray-500"><?php echo e($document->original_filename); ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <a href="<?php echo e(route('admin.employees.show', $document->user)); ?>" 
                                   class="text-gray-900 hover:text-green-600">
                                    <?php echo e($document->user->name); ?>

                                </a>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-600">
                                <?php echo e($document->type->name); ?>

                            </td>
                            <td class="px-4 py-4">
                                <?php if($daysLeft < 0): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        ❌ Expiré
                                    </span>
                                <?php elseif($daysLeft <= 7): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        🔴 <?php echo e($daysLeft); ?> jour(s)
                                    </span>
                                <?php elseif($daysLeft <= 30): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        🟠 <?php echo e($daysLeft); ?> jours
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        🟡 <?php echo e($daysLeft); ?> jours
                                    </span>
                                <?php endif; ?>
                                <p class="text-xs text-gray-500 mt-1"><?php echo e($document->expiry_date->format('d/m/Y')); ?></p>
                            </td>
                            <td class="px-4 py-4 text-right">
                                <a href="<?php echo e(route('admin.documents.download', $document)); ?>" 
                                   class="text-blue-600 hover:underline text-sm">
                                    Télécharger
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center">
                                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <span class="text-3xl">✅</span>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900">Aucun document expirant</h3>
                                <p class="text-gray-500 mt-1">Tous les documents sont à jour !</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if($documents->hasPages()): ?>
            <div class="flex justify-center">
                <?php echo e($documents->links()); ?>

            </div>
        <?php endif; ?>
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
<?php /**PATH D:\ManageX\resources\views\admin\documents\expiring.blade.php ENDPATH**/ ?>