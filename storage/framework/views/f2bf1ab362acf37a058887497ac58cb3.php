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
    <div class="max-w-2xl mx-auto space-y-6">
        <!-- Header avec gradient -->
        <div class="relative overflow-hidden rounded-2xl shadow-xl animate-fade-in-up" style="background: linear-gradient(135deg, #5680E9, #84CEEB) !important;">
            <div class="absolute inset-0 bg-black/10"></div>
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            
            <div class="relative p-6 md:p-8">
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
                                <li><span class="text-white text-sm font-medium">Modifier</span></li>
                            </ol>
                        </nav>
                        <h1 class="text-2xl md:text-3xl font-bold text-white">Modifier le Document</h1>
                        <p class="text-white/80 mt-1"><?php echo e($globalDocument->type_label); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form action="<?php echo e(route('admin.global-documents.update', $globalDocument)); ?>" method="POST" enctype="multipart/form-data"
              class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden animate-fade-in-up animation-delay-100">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <?php if($errors->any()): ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-3 m-6 rounded-lg">
                    <ul class="list-disc list-inside">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="p-6 space-y-6">
                <!-- Titre -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Titre <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="<?php echo e(old('title', $globalDocument->title)); ?>" required
                           class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3"
                              class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"><?php echo e(old('description', $globalDocument->description)); ?></textarea>
                </div>

                <!-- Fichier actuel -->
                <div class="p-4 rounded-xl" style="background: linear-gradient(135deg, #5680E910, #84CEEB10);">
                    <p class="text-sm text-gray-600 mb-2">Fichier actuel :</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #5680E930, #84CEEB30);">
                            <svg class="w-5 h-5" style="color: #5680E9;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <span class="font-medium text-gray-800"><?php echo e($globalDocument->original_filename); ?></span>
                        <span class="text-sm text-gray-500">(<?php echo e($globalDocument->file_size_formatted); ?>)</span>
                        <a href="<?php echo e(route('admin.global-documents.download', $globalDocument)); ?>"
                           class="hover:underline text-sm ml-auto font-medium" style="color: #5680E9;">Télécharger</a>
                    </div>
                </div>

                <!-- Nouveau fichier -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Remplacer le fichier (optionnel)</label>
                    <input type="file" name="file" accept=".pdf,.doc,.docx"
                           class="w-full text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 file:font-medium file:cursor-pointer">
                </div>

                <!-- Actif -->
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="is_active" value="1" id="is_active"
                           <?php echo e($globalDocument->is_active ? 'checked' : ''); ?>

                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="is_active" class="text-sm text-gray-700">Document actif (visible par les employés)</label>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50">
                <a href="<?php echo e(route('admin.global-documents.index')); ?>"
                   class="px-4 py-2.5 text-gray-600 hover:text-gray-800 font-medium transition">Annuler</a>
                <button type="submit"
                        class="px-6 py-2.5 text-white font-semibold rounded-xl transition-all shadow-lg" style="background: linear-gradient(135deg, #5680E9, #5AB9EA);">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Enregistrer
                    </span>
                </button>
            </div>
        </form>
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
<?php /**PATH D:\ManageX\resources\views/admin/global-documents/edit.blade.php ENDPATH**/ ?>