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
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 animate-fade-in-up">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">📋 Demandes de Documents</h1>
                <p class="text-gray-500 mt-1">Gérez les demandes des employés</p>
            </div>
        </div>

        <?php if(session('success')): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 animate-fade-in-up animation-delay-100">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">⏳</span>
                    <div>
                        <p class="text-2xl font-bold text-amber-700"><?php echo e($stats['pending']); ?></p>
                        <p class="text-sm text-amber-600">En attente</p>
                    </div>
                </div>
            </div>
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 animate-fade-in-up animation-delay-200">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">✅</span>
                    <div>
                        <p class="text-2xl font-bold text-green-700"><?php echo e($stats['approved']); ?></p>
                        <p class="text-sm text-green-600">Approuvées</p>
                    </div>
                </div>
            </div>
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 animate-fade-in-up animation-delay-300">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">📊</span>
                    <div>
                        <p class="text-2xl font-bold text-blue-700"><?php echo e($stats['total']); ?></p>
                        <p class="text-sm text-blue-600">Total</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 animate-fade-in-up animation-delay-200">
            <form method="GET" class="flex items-center gap-4">
                <select name="status" class="rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">En attente</option>
                    <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php echo e(request('status') == $key ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    Filtrer
                </button>
                <?php if(request('status')): ?>
                    <a href="<?php echo e(route('admin.document-requests.index')); ?>" class="text-gray-500 hover:text-gray-700">
                        Réinitialiser
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Liste des demandes -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden animate-fade-in-up animation-delay-300">
            <?php if($requests->count() > 0): ?>
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employé</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Message</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Statut</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-green-500 rounded-full flex items-center justify-center text-white font-bold">
                                            <?php echo e(strtoupper(substr($request->user->name, 0, 1))); ?>

                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900"><?php echo e($request->user->name); ?></p>
                                            <p class="text-sm text-gray-500"><?php echo e($request->user->position->name ?? 'Non défini'); ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="font-medium text-gray-900"><?php echo e($request->type_label); ?></span>
                                </td>
                                <td class="px-4 py-4">
                                    <p class="text-sm text-gray-600"><?php echo e(Str::limit($request->message ?? '-', 50)); ?></p>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="text-sm text-gray-500"><?php echo e($request->created_at->format('d/m/Y')); ?></span>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="px-2 py-1 text-xs rounded-full
                                        <?php if($request->status === 'approved'): ?> bg-green-100 text-green-700
                                        <?php elseif($request->status === 'rejected'): ?> bg-red-100 text-red-700
                                        <?php else: ?> bg-amber-100 text-amber-700 <?php endif; ?>">
                                        <?php echo e($request->status_label); ?>

                                    </span>
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <?php if($request->isPending()): ?>
                                        <a href="<?php echo e(route('admin.document-requests.show', $request)); ?>" 
                                           class="inline-flex items-center px-3 py-1.5 bg-emerald-600 text-white text-sm rounded-lg hover:bg-emerald-700">
                                            Traiter
                                        </a>
                                    <?php else: ?>
                                        <a href="<?php echo e(route('admin.document-requests.show', $request)); ?>" 
                                           class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200">
                                            Voir
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                <div class="px-4 py-3 border-t">
                    <?php echo e($requests->links()); ?>

                </div>
            <?php else: ?>
                <div class="p-12 text-center">
                    <span class="text-4xl">📭</span>
                    <p class="text-gray-500 mt-4">Aucune demande <?php echo e(request('status') ? '' : 'en attente'); ?></p>
                </div>
            <?php endif; ?>
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
<?php /**PATH D:\ManageX\resources\views\admin\document-requests\index.blade.php ENDPATH**/ ?>