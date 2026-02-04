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
        <!-- Breadcrumb -->
        <nav class="flex animate-fade-in-up" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="<?php echo e(route('admin.dashboard')); ?>" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                        Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="<?php echo e(route('admin.announcements.index')); ?>" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">Annonces</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Modifier</span>
                    </div>
                </li>
            </ol>
        </nav>
        <!-- Header -->
        <div class="flex items-center gap-4 animate-fade-in-up animation-delay-100">
            <a href="<?php echo e(route('admin.announcements.index')); ?>" 
               class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Modifier l'Annonce</h1>
                <p class="text-gray-500"><?php echo e($announcement->title); ?></p>
            </div>
        </div>

        <!-- Form -->
        <form action="<?php echo e(route('admin.announcements.update', $announcement)); ?>" method="POST" class="space-y-6">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <!-- Main Content Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6 animate-fade-in-up animation-delay-200">
                <h2 class="text-lg font-semibold text-gray-900 border-b pb-3">ðŸ“ Contenu</h2>

                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                        Titre <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" id="title" value="<?php echo e(old('title', $announcement->title)); ?>" required
                           class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                    <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-500"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Type & Priority -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                            Type <span class="text-red-500">*</span>
                        </label>
                        <select name="type" id="type" required
                                class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                            <option value="info" <?php echo e(old('type', $announcement->type) === 'info' ? 'selected' : ''); ?>>â„¹ï¸ Information</option>
                            <option value="success" <?php echo e(old('type', $announcement->type) === 'success' ? 'selected' : ''); ?>>âœ… Bonne nouvelle</option>
                            <option value="warning" <?php echo e(old('type', $announcement->type) === 'warning' ? 'selected' : ''); ?>>âš ï¸ Attention</option>
                            <option value="urgent" <?php echo e(old('type', $announcement->type) === 'urgent' ? 'selected' : ''); ?>>ðŸš Urgent</option>
                            <option value="event" <?php echo e(old('type', $announcement->type) === 'event' ? 'selected' : ''); ?>>ðŸ“… événement</option>
                        </select>
                    </div>
                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">
                            Priorité <span class="text-red-500">*</span>
                        </label>
                        <select name="priority" id="priority" required
                                class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                            <option value="normal" <?php echo e(old('priority', $announcement->priority) === 'normal' ? 'selected' : ''); ?>>Normale</option>
                            <option value="high" <?php echo e(old('priority', $announcement->priority) === 'high' ? 'selected' : ''); ?>>Haute</option>
                            <option value="critical" <?php echo e(old('priority', $announcement->priority) === 'critical' ? 'selected' : ''); ?>>Critique (banniére)</option>
                        </select>
                    </div>
                </div>

                <!-- Content -->
                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-1">
                        Contenu <span class="text-red-500">*</span>
                    </label>
                    <textarea name="content" id="content" rows="6" required
                              class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500"><?php echo e(old('content', $announcement->content)); ?></textarea>
                    <?php $__errorArgs = ['content'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-500"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <!-- Targeting Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6 animate-fade-in-up animation-delay-300">
                <h2 class="text-lg font-semibold text-gray-900 border-b pb-3">ðŸŽ¯ Ciblage</h2>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Destinataires</label>
                    <div class="space-y-3">
                        <label class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="target_type" value="all" 
                                   <?php echo e(old('target_type', $announcement->target_type) === 'all' ? 'checked' : ''); ?>

                                   class="text-green-600 focus:ring-green-500" onchange="updateTargetFields()">
                            <span class="font-medium text-gray-900">Tous les employés</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="target_type" value="department" 
                                   <?php echo e(old('target_type', $announcement->target_type) === 'department' ? 'checked' : ''); ?>

                                   class="text-green-600 focus:ring-green-500" onchange="updateTargetFields()">
                            <span class="font-medium text-gray-900">Un département</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="target_type" value="position" 
                                   <?php echo e(old('target_type', $announcement->target_type) === 'position' ? 'checked' : ''); ?>

                                   class="text-green-600 focus:ring-green-500" onchange="updateTargetFields()">
                            <span class="font-medium text-gray-900">Un poste</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="target_type" value="custom" 
                                   <?php echo e(old('target_type', $announcement->target_type) === 'custom' ? 'checked' : ''); ?>

                                   class="text-green-600 focus:ring-green-500" onchange="updateTargetFields()">
                            <span class="font-medium text-gray-900">Utilisateurs spécifiques</span>
                        </label>
                    </div>
                </div>

                <div id="departmentField" class="<?php echo e($announcement->target_type !== 'department' ? 'hidden' : ''); ?>">
                    <select name="department_id" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                        <option value="">Sélectionner un département</option>
                        <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($dept->id); ?>" <?php echo e(old('department_id', $announcement->department_id) == $dept->id ? 'selected' : ''); ?>>
                                <?php echo e($dept->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div id="positionField" class="<?php echo e($announcement->target_type !== 'position' ? 'hidden' : ''); ?>">
                    <select name="position_id" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                        <option value="">Sélectionner un poste</option>
                        <?php $__currentLoopData = $positions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pos): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($pos->id); ?>" <?php echo e(old('position_id', $announcement->position_id) == $pos->id ? 'selected' : ''); ?>>
                                <?php echo e($pos->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div id="usersField" class="<?php echo e($announcement->target_type !== 'custom' ? 'hidden' : ''); ?>">
                    <div class="border rounded-lg max-h-60 overflow-y-auto p-2">
                        <?php $selectedUsers = $announcement->target_user_ids ?? []; ?>
                        <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label class="flex items-center gap-3 p-2 hover:bg-gray-50 rounded cursor-pointer">
                                <input type="checkbox" name="target_user_ids[]" value="<?php echo e($emp->id); ?>"
                                       <?php echo e(in_array($emp->id, $selectedUsers) ? 'checked' : ''); ?>

                                       class="rounded text-green-600 focus:ring-green-500">
                                <span class="text-gray-900"><?php echo e($emp->name); ?></span>
                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>

            <!-- Scheduling Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6">
                <h2 class="text-lg font-semibold text-gray-900 border-b pb-3">ðŸ“… Planification</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Date de début</label>
                        <input type="date" name="start_date" value="<?php echo e(old('start_date', $announcement->start_date?->format('Y-m-d'))); ?>"
                               class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Date de fin</label>
                        <input type="date" name="end_date" value="<?php echo e(old('end_date', $announcement->end_date?->format('Y-m-d'))); ?>"
                               class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                    </div>
                </div>
            </div>

            <!-- Options Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h2 class="text-lg font-semibold text-gray-900 border-b pb-3">âš™ï¸ Options</h2>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_pinned" value="1" 
                           <?php echo e(old('is_pinned', $announcement->is_pinned) ? 'checked' : ''); ?>

                           class="rounded text-green-600 focus:ring-green-500">
                    <span class="font-medium text-gray-900">ðŸ“Œ épingler en haut</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="requires_acknowledgment" value="1" 
                           <?php echo e(old('requires_acknowledgment', $announcement->requires_acknowledgment) ? 'checked' : ''); ?>

                           class="rounded text-green-600 focus:ring-green-500">
                    <span class="font-medium text-gray-900">âœ… Exiger un accusé de réception</span>
                </label>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-4">
                <a href="<?php echo e(route('admin.announcements.index')); ?>" 
                   class="px-6 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                    Annuler
                </a>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <script nonce="<?php echo e($cspNonce ?? ''); ?>">
        function updateTargetFields() {
            const targetType = document.querySelector('input[name="target_type"]:checked').value;
            document.getElementById('departmentField').classList.toggle('hidden', targetType !== 'department');
            document.getElementById('positionField').classList.toggle('hidden', targetType !== 'position');
            document.getElementById('usersField').classList.toggle('hidden', targetType !== 'custom');
        }
        document.addEventListener('DOMContentLoaded', updateTargetFields);
    </script>
    <?php $__env->stopPush(); ?>
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
<?php /**PATH D:\ManageX\resources\views\admin\announcements\edit.blade.php ENDPATH**/ ?>