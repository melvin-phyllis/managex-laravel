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
        <!-- Header -->
        <div class="flex items-center gap-4 animate-fade-in-up">
            <a href="<?php echo e(route('admin.global-documents.index')); ?>" class="p-2 text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Ajouter un Document Global</h1>
                <p class="text-gray-600 mt-1">Réglement intérieur, charte, politique...</p>
            </div>
        </div>

        <!-- Form -->
        <form action="<?php echo e(route('admin.global-documents.store')); ?>" method="POST" enctype="multipart/form-data"
              class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6 animate-fade-in-up animation-delay-100">
            <?php echo csrf_field(); ?>

            <?php if($errors->any()): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul class="list-disc list-inside">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Type de document *</label>
                <select name="type" id="type-select" required
                        class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">Sélectionnez un type</option>
                    <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php echo e(old('type') == $key ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <!-- Position (for Fiche de poste only) -->
            <div id="position-container" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-2">Poste associé *</label>
                <select name="position_id" id="position-select"
                        class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
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
                <label class="block text-sm font-medium text-gray-700 mb-2">Titre *</label>
                <input type="text" name="title" value="<?php echo e(old('title')); ?>" required
                       placeholder="Ex: Réglement intérieur 2026"
                       class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="3"
                          placeholder="Décrivez briévement le contenu du document..."
                          class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"><?php echo e(old('description')); ?></textarea>
            </div>

            <!-- Fichier -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fichier *</label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-emerald-400 transition cursor-pointer"
                     onclick="document.getElementById('file-input').click()">
                    <svg class="w-10 h-10 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    <p class="text-gray-600">Cliquez ou glissez un fichier ici</p>
                    <p class="text-sm text-gray-400 mt-1">PDF, DOC, DOCX â€¢ Max 10 MB</p>
                    <input type="file" name="file" id="file-input" required accept=".pdf,.doc,.docx" class="hidden">
                </div>
                <p id="file-name" class="text-sm text-emerald-600 mt-2"></p>
            </div>

            <!-- Actif -->
            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_active" value="1" checked id="is_active"
                       class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                <label for="is_active" class="text-sm text-gray-700">Document actif (visible par les employés)</label>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="<?php echo e(route('admin.global-documents.index')); ?>"
                   class="px-4 py-2 text-gray-600 hover:text-gray-800 transition">Annuler</a>
                <button type="submit"
                        class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>

    <script nonce="<?php echo e($cspNonce ?? ''); ?>">
        document.getElementById('file-input').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            document.getElementById('file-name').textContent = fileName ? 'ðŸ“Ž ' + fileName : '';
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
<?php /**PATH D:\ManageX\resources\views\admin\global-documents\create.blade.php ENDPATH**/ ?>