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
        <!-- Breadcrumbs -->
        <nav class="flex animate-fade-in-up" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="<?php echo e(route('admin.dashboard')); ?>" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                        <a href="<?php echo e(route('admin.leaves.index')); ?>" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">Congés</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Détails</span>
                    </div>
                </li>
            </ol>
        </nav>
        <!-- Header -->
        <div class="flex items-center justify-between animate-fade-in-up animation-delay-100">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Détails de la demande</h1>
                <p class="text-gray-500 mt-1">Demande de <?php echo e($leave->type_label); ?></p>
            </div>
            <a href="<?php echo e(route('admin.leaves.index')); ?>" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Retour
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fade-in-up animation-delay-200">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Leave Details -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-semibold text-gray-900">Informations de la demande</h2>
                        <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['status' => $leave->statut,'type' => 'leave']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($leave->statut),'type' => 'leave']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $attributes = $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $component = $__componentOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
                    </div>

                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Type de congé</dt>
                            <dd class="mt-1 text-lg text-gray-900"><?php echo e($leave->type_label); ?></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Durée</dt>
                            <dd class="mt-1 text-lg text-gray-900"><?php echo e($leave->duree); ?> jour(s)</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Date de début</dt>
                            <dd class="mt-1 text-lg text-gray-900"><?php echo e($leave->date_debut->format('d/m/Y')); ?></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Date de fin</dt>
                            <dd class="mt-1 text-lg text-gray-900"><?php echo e($leave->date_fin->format('d/m/Y')); ?></dd>
                        </div>
                    </dl>

                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <dt class="text-sm font-medium text-gray-500 mb-2">Motif</dt>
                        <dd class="text-gray-900 bg-gray-50 p-4 rounded-lg"><?php echo e($leave->motif ?? 'Aucun motif fourni.'); ?></dd>
                    </div>

                    <?php if($leave->commentaire_admin): ?>
                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <dt class="text-sm font-medium text-gray-500 mb-2">Commentaire de l'administrateur</dt>
                            <dd class="text-gray-900 bg-blue-50 p-4 rounded-lg"><?php echo e($leave->commentaire_admin); ?></dd>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Actions -->
                <?php if($leave->statut === 'pending'): ?>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Traiter la demande</h3>

                        <div class="mb-4">
                            <label for="commentaire_admin" class="block text-sm font-medium text-gray-700 mb-1">Commentaire (optionnel)</label>
                            <textarea id="commentaire_admin" name="commentaire_admin" rows="3" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Ajouter un commentaire..."></textarea>
                        </div>

                        <div class="flex items-center space-x-4">
                            <form action="<?php echo e(route('admin.leaves.approve', $leave)); ?>" method="POST" class="flex-1" id="approveForm">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="commentaire_admin" id="approveComment">
                                <button type="submit" class="w-full px-4 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Approuver
                                </button>
                            </form>
                            <form action="<?php echo e(route('admin.leaves.reject', $leave)); ?>" method="POST" class="flex-1" id="rejectForm">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="commentaire_admin" id="rejectComment">
                                <button type="submit" class="w-full px-4 py-3 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Rejeter
                                </button>
                            </form>
                        </div>
                    </div>

                    <script nonce="<?php echo e($cspNonce ?? ''); ?>">
                        document.getElementById('approveForm').addEventListener('submit', function() {
                            document.getElementById('approveComment').value = document.getElementById('commentaire_admin').value;
                        });
                        document.getElementById('rejectForm').addEventListener('submit', function() {
                            document.getElementById('rejectComment').value = document.getElementById('commentaire_admin').value;
                        });
                    </script>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Employee Info -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4">Employé</h3>
                    <div class="flex items-center">
                        <?php if($leave->user->avatar): ?>
                            <img src="<?php echo e(avatar_url($leave->user->avatar)); ?>" alt="<?php echo e($leave->user->name); ?>" class="w-12 h-12 rounded-full object-cover">
                        <?php else: ?>
                            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                                <span class="text-blue-600 font-medium"><?php echo e(strtoupper(substr($leave->user->name, 0, 2))); ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="ml-3">
                            <p class="font-medium text-gray-900"><?php echo e($leave->user->name); ?></p>
                            <p class="text-sm text-gray-500"><?php echo e($leave->user->poste ?? 'Non défini'); ?></p>
                        </div>
                    </div>
                    <a href="<?php echo e(route('admin.employees.show', $leave->user)); ?>" class="mt-4 block text-center text-sm text-blue-600 hover:text-blue-800">
                        Voir le profil complet â†’
                    </a>
                </div>

                <!-- Request Info -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4">Historique</h3>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm text-gray-500">Demande créée le</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900"><?php echo e($leave->created_at->format('d/m/Y é  H:i')); ?></dd>
                        </div>
                        <?php if($leave->updated_at != $leave->created_at): ?>
                            <div>
                                <dt class="text-sm text-gray-500">Derniére modification</dt>
                                <dd class="mt-1 text-sm font-medium text-gray-900"><?php echo e($leave->updated_at->format('d/m/Y é  H:i')); ?></dd>
                            </div>
                        <?php endif; ?>
                    </dl>
                </div>

                <!-- Leave History -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4">Autres congés de l'employé</h3>
                    <div class="space-y-3">
                        <?php $__empty_1 = true; $__currentLoopData = $leave->user->leaves()->where('id', '!=', $leave->id)->latest()->take(3)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $otherLeave): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600"><?php echo e($otherLeave->type_label); ?></span>
                                <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['status' => $otherLeave->statut,'type' => 'leave']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($otherLeave->statut),'type' => 'leave']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $attributes = $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $component = $__componentOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <p class="text-sm text-gray-500">Aucun autre congé</p>
                        <?php endif; ?>
                    </div>
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
<?php /**PATH D:\ManageX\resources\views\admin\leaves\show.blade.php ENDPATH**/ ?>