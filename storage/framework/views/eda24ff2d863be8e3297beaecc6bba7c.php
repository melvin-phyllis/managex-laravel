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
        <!-- Header comme sur tasks -->
        <div class="relative overflow-hidden rounded-2xl shadow-xl animate-fade-in-up" style="background: linear-gradient(135deg, #5680E9, #84CEEB) !important;">
            <div class="absolute inset-0 bg-black/10"></div>
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            
            <div class="relative p-6 md:p-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <nav class="flex mb-3" aria-label="Breadcrumb">
                            <ol class="inline-flex items-center space-x-1">
                                <li><a href="<?php echo e(route('admin.dashboard')); ?>" class="text-white/70 hover:text-white text-sm">Dashboard</a></li>
                                <li><span class="text-white/50 mx-2">/</span></li>
                                <li><a href="<?php echo e(route('admin.documents.index')); ?>" class="text-white/70 hover:text-white text-sm">Documents</a></li>
                                <li><span class="text-white/50 mx-2">/</span></li>
                                <li><span class="text-white text-sm font-medium">Demandes</span></li>
                            </ol>
                        </nav>
                        <h1 class="text-2xl md:text-3xl font-bold text-white flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                </svg>
                            </div>
                            Demandes de Documents
                        </h1>
                        <p class="text-white/80 mt-2">Gérez les demandes des employés</p>
                    </div>
                    <a href="<?php echo e(route('admin.documents.index')); ?>" 
                       class="px-4 py-2.5 bg-white font-semibold rounded-xl hover:bg-purple-50 transition-all shadow-lg flex items-center" style="color: #5680E9;">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Retour
                    </a>
                </div>
            </div>
        </div>

        <?php if(session('success')): ?>
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-center gap-3 animate-fade-in-up">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <p class="text-green-800 font-medium"><?php echo e(session('success')); ?></p>
            </div>
        <?php endif; ?>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 animate-fade-in-up animation-delay-100">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: rgba(245, 158, 11, 0.15);">
                        <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-amber-500"><?php echo e($stats['pending']); ?></p>
                        <p class="text-sm text-gray-500">En attente</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: rgba(90, 185, 234, 0.15);">
                        <svg class="w-6 h-6" style="color: #5AB9EA;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold" style="color: #5AB9EA;"><?php echo e($stats['approved']); ?></p>
                        <p class="text-sm text-gray-500">Approuvées</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: rgba(86, 128, 233, 0.15);">
                        <svg class="w-6 h-6" style="color: #5680E9;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold" style="color: #5680E9;"><?php echo e($stats['total']); ?></p>
                        <p class="text-sm text-gray-500">Total</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 animate-fade-in-up animation-delay-200">
            <form method="GET" class="flex items-center gap-4">
                <select name="status" class="rounded-xl border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">En attente</option>
                    <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php echo e(request('status') == $key ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors">
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
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Employé</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Message</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Date</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Statut</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-purple-50/50 transition-colors group">
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold" style="background: linear-gradient(135deg, #5680E9, #84CEEB);">
                                            <?php echo e(strtoupper(substr($request->user->name, 0, 1))); ?>

                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900"><?php echo e($request->user->name); ?></p>
                                            <p class="text-sm text-gray-500"><?php echo e($request->user->position->name ?? 'Non défini'); ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full" style="background-color: rgba(136, 96, 208, 0.15); color: #8860D0;">
                                        <?php echo e($request->type_label); ?>

                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <p class="text-sm text-gray-600"><?php echo e(Str::limit($request->message ?? '-', 50)); ?></p>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="text-sm text-gray-500"><?php echo e($request->created_at->format('d/m/Y')); ?></span>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <?php if($request->status === 'approved'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-full" style="background-color: rgba(90, 185, 234, 0.15); color: #5AB9EA;">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            <?php echo e($request->status_label); ?>

                                        </span>
                                    <?php elseif($request->status === 'rejected'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-full bg-red-50 text-red-600">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                            <?php echo e($request->status_label); ?>

                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-full bg-amber-50 text-amber-600">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <?php echo e($request->status_label); ?>

                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <?php if($request->isPending()): ?>
                                        <a href="<?php echo e(route('admin.document-requests.show', $request)); ?>" 
                                           class="inline-flex items-center px-3 py-1.5 text-white text-sm font-medium rounded-xl shadow-lg transition-all" style="background: linear-gradient(135deg, #5680E9, #5AB9EA);">
                                            Traiter
                                        </a>
                                    <?php else: ?>
                                        <a href="<?php echo e(route('admin.document-requests.show', $request)); ?>" 
                                           class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-200 transition-colors">
                                            Voir
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                <div class="px-4 py-3 border-t border-gray-100">
                    <?php echo e($requests->links()); ?>

                </div>
            <?php else: ?>
                <div class="p-12 text-center">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4" style="background-color: rgba(86, 128, 233, 0.15);">
                        <svg class="w-8 h-8" style="color: #5680E9;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                    </div>
                    <p class="text-lg font-medium text-gray-900">Aucune demande</p>
                    <p class="text-gray-500 mt-1"><?php echo e(request('status') ? 'pour ce filtre' : 'en attente'); ?></p>
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
<?php /**PATH D:\ManageX\resources\views/admin/document-requests/index.blade.php ENDPATH**/ ?>