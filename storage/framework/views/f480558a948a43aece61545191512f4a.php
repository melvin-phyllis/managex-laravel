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
        <div class="animate-fade-in-up">
            <h1 class="text-2xl font-bold text-gray-900">Documents de l'Entreprise</h1>
            <p class="text-gray-600 mt-1">Règlement intérieur et documents importants</p>
        </div>

        <?php if(session('success')): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <!-- Liste documents -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden animate-fade-in-up animation-delay-100">
            <?php if($documents->isEmpty()): ?>
                <div class="p-8 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p>Aucun document disponible</p>
                </div>
            <?php else: ?>
                <div class="divide-y divide-gray-100">
                    <?php $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-lg <?php echo e($doc->is_acknowledged ? 'bg-green-100' : 'bg-amber-100'); ?> flex items-center justify-center text-xl">
                                    <?php echo e($doc->file_icon); ?>

                                </div>
                                <div>
                                    <h3 class="font-medium text-gray-900 flex items-center gap-2">
                                        <?php echo e($doc->title); ?>

                                        <?php if($doc->is_acknowledged): ?>
                                            <span class="px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-700">✓ Lu</span>
                                        <?php else: ?>
                                            <span class="px-2 py-0.5 text-xs rounded-full bg-amber-100 text-amber-700 animate-pulse">À lire</span>
                                        <?php endif; ?>
                                    </h3>
                                    <p class="text-sm text-gray-500">
                                        <?php echo e($types[$doc->type] ?? $doc->type); ?> • <?php echo e($doc->file_size_formatted); ?>

                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="<?php echo e(route('employee.global-documents.download', $doc)); ?>"
                                   class="p-2 text-gray-400 hover:text-emerald-600 transition" title="Télécharger">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                </a>
                                <?php if(!$doc->is_acknowledged): ?>
                                    <form action="<?php echo e(route('employee.global-documents.acknowledge', $doc)); ?>" method="POST" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit"
                                                class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition">
                                            J'ai lu ce document
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Note -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 animate-fade-in-up animation-delay-200">
            <p class="text-sm text-blue-800">
                <strong>💡 Important :</strong> Veuillez lire attentivement tous les documents et confirmer votre lecture en cliquant sur "J'ai lu ce document".
            </p>
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
<?php /**PATH D:\ManageX\resources\views\employee\global-documents\index.blade.php ENDPATH**/ ?>