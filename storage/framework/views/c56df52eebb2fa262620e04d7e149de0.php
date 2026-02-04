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
    <div class="max-w-3xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center gap-4">
            <a href="<?php echo e(route('employee.global-documents.index')); ?>" class="p-2 text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-gray-900"><?php echo e($globalDocument->title); ?></h1>
                <p class="text-gray-600 mt-1"><?php echo e($globalDocument->type_label); ?></p>
            </div>
        </div>

        <?php if(session('success')): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <!-- Document Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6">
                <!-- Statut -->
                <div class="flex items-center justify-between mb-6">
                    <?php if($isAcknowledged): ?>
                        <div class="flex items-center gap-2 text-green-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="font-medium">Vous avez accusé réception de ce document</span>
                        </div>
                    <?php else: ?>
                        <div class="flex items-center gap-2 text-amber-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span class="font-medium">Veuillez lire ce document et accuser réception</span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Fichier -->
                <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg">
                    <div class="w-12 h-12 rounded-lg bg-emerald-100 flex items-center justify-center text-xl">
                        <?php echo e($globalDocument->file_icon); ?>

                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-900"><?php echo e($globalDocument->original_filename); ?></p>
                        <p class="text-sm text-gray-500"><?php echo e($globalDocument->file_size_formatted); ?> • Ajouté le <?php echo e($globalDocument->created_at->format('d/m/Y')); ?></p>
                    </div>
                    <a href="<?php echo e(route('employee.global-documents.download', $globalDocument)); ?>"
                       class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Télécharger
                    </a>
                </div>

                <?php if($globalDocument->description): ?>
                    <div class="mt-4 p-4 border border-gray-200 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Description</h3>
                        <p class="text-gray-600"><?php echo e($globalDocument->description); ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Action accuser réception -->
            <?php if(!$isAcknowledged): ?>
                <div class="px-6 py-4 bg-amber-50 border-t border-amber-200">
                    <form action="<?php echo e(route('employee.global-documents.acknowledge', $globalDocument)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="flex items-start gap-3">
                            <input type="checkbox" id="confirm" required
                                   class="mt-1 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                            <label for="confirm" class="text-sm text-gray-700 flex-1">
                                J'atteste avoir lu et pris connaissance de ce document. Je m'engage à en respecter les dispositions.
                            </label>
                        </div>
                        <button type="submit"
                                class="mt-4 w-full px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition">
                            ✓ Accuser réception
                        </button>
                    </form>
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
<?php /**PATH D:\ManageX\resources\views\employee\global-documents\show.blade.php ENDPATH**/ ?>