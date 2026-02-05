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
                                <li><span class="text-white text-sm font-medium">Ajouter</span></li>
                            </ol>
                        </nav>
                        <h1 class="text-2xl md:text-3xl font-bold text-white">Ajouter un Document Global</h1>
                        <p class="text-white/80 mt-1">Règlement intérieur, charte, politique...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form action="<?php echo e(route('admin.global-documents.store')); ?>" method="POST" enctype="multipart/form-data"
              class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden animate-fade-in-up animation-delay-100">
            <?php echo csrf_field(); ?>

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
                <!-- Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type de document <span class="text-red-500">*</span></label>
                    <select name="type" id="type-select" required
                            class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Sélectionnez un type</option>
                        <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($key); ?>" <?php echo e(old('type') == $key ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <!-- Position (for Fiche de poste only) -->
                <div id="position-container" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Poste associé <span class="text-red-500">*</span></label>
                    <select name="position_id" id="position-select"
                            class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Sélectionnez un poste</option>
                        <?php $__currentLoopData = $positions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $position): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($position->id); ?>" <?php echo e(old('position_id') == $position->id ? 'selected' : ''); ?>>
                                <?php echo e($position->name); ?> <?php if($position->department): ?> (<?php echo e($position->department->name); ?>) <?php endif; ?>
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <!-- Titre -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Titre <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="<?php echo e(old('title')); ?>" required
                           placeholder="Ex: Règlement intérieur 2026"
                           class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3"
                              placeholder="Décrivez brièvement le contenu du document..."
                              class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"><?php echo e(old('description')); ?></textarea>
                </div>

                <!-- Fichier -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fichier <span class="text-red-500">*</span></label>
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-indigo-400 transition cursor-pointer"
                         style="background: linear-gradient(135deg, #5680E910, #84CEEB10);"
                         onclick="document.getElementById('file-input').click()">
                        <svg class="w-10 h-10 mx-auto mb-2" style="color: #5680E9;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <p class="text-gray-600">Cliquez ou glissez un fichier ici</p>
                        <p class="text-sm text-gray-400 mt-1">PDF, DOC, DOCX • Max 10 MB</p>
                        <input type="file" name="file" id="file-input" required accept=".pdf,.doc,.docx" class="hidden">
                    </div>
                    <p id="file-name" class="text-sm mt-2" style="color: #5680E9;"></p>
                </div>

                <!-- Actif -->
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="is_active" value="1" checked id="is_active"
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

    <script nonce="<?php echo e($cspNonce ?? ''); ?>">
        document.getElementById('file-input').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            document.getElementById('file-name').textContent = fileName ? '📎 ' + fileName : '';
        });

        // Toggle position dropdown for Fiche de poste
        const typeSelect = document.getElementById('type-select');
        const positionContainer = document.getElementById('position-container');
        const positionSelect = document.getElementById('position-select');

        function togglePositionField() {
            if (typeSelect.value === 'fiche_poste') {
                positionContainer.classList.remove('hidden');
                positionSelect.setAttribute('required', 'required');
            } else {
                positionContainer.classList.add('hidden');
                positionSelect.removeAttribute('required');
            }
        }

        typeSelect.addEventListener('change', togglePositionField);
        togglePositionField(); // Check on load
    </script>
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
<?php /**PATH D:\ManageX\resources\views/admin/global-documents/create.blade.php ENDPATH**/ ?>