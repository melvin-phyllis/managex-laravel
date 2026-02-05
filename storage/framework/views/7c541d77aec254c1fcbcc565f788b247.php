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
        <!-- Header amélioré comme sur tasks -->
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
                                <li><span class="text-white text-sm font-medium">Évaluations</span></li>
                            </ol>
                        </nav>
                        <h1 class="text-2xl md:text-3xl font-bold text-white flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                            Évaluations des Performances
                        </h1>
                        <p class="text-white/80 mt-2">Gérez et analysez les performances de vos collaborateurs</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="<?php echo e(route('admin.employee-evaluations.bulk-create', ['month' => $month, 'year' => $year])); ?>" 
                           class="px-4 py-2.5 bg-white/20 backdrop-blur-sm text-white font-semibold rounded-xl hover:bg-white/30 transition-all flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            Évaluation groupée
                        </a>
                        <a href="<?php echo e(route('admin.employee-evaluations.create', ['month' => $month, 'year' => $year])); ?>" 
                           class="px-4 py-2.5 bg-white font-semibold rounded-xl hover:bg-purple-50 transition-all shadow-lg flex items-center" style="color: #5680E9;">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Nouvelle évaluation
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 animate-fade-in-up animation-delay-100">
            <?php
                $totalEvaluations = $evaluations->total();
                $validatedCount = $evaluations->where('status', 'validated')->count();
                $pendingCount = $pendingEmployees->count();
            ?>
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: rgba(107, 114, 128, 0.1);">
                        <svg class="w-5 h-5" style="color: #6B7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </div>
                    <span class="text-2xl font-bold text-gray-900"><?php echo e($totalEvaluations); ?></span>
                </div>
                <p class="text-sm text-gray-500 mt-2">Total évaluations</p>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: rgba(90, 185, 234, 0.15);">
                        <svg class="w-5 h-5" style="color: #5AB9EA;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <span class="text-2xl font-bold text-gray-900"><?php echo e($validatedCount); ?></span>
                </div>
                <p class="text-sm text-gray-500 mt-2">Validées</p>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: rgba(136, 96, 208, 0.15);">
                        <svg class="w-5 h-5" style="color: #8860D0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="text-2xl font-bold text-gray-900"><?php echo e($pendingCount); ?></span>
                </div>
                <p class="text-sm text-gray-500 mt-2">En attente</p>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: rgba(86, 128, 233, 0.1);">
                        <svg class="w-5 h-5" style="color: #5680E9;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="text-2xl font-bold text-gray-900"><?php echo e(number_format($smic, 0, ',', ' ')); ?></span>
                </div>
                <p class="text-sm text-gray-500 mt-2">SMIC (FCFA)</p>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 animate-fade-in-up animation-delay-200">
            <form method="GET" class="p-4 flex flex-wrap items-center gap-4">
                <!-- Période -->
                <div class="flex items-center gap-2 flex-1 min-w-[200px]">
                    <select name="month" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm bg-white">
                        <?php $__currentLoopData = range(1, 12); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($m); ?>" <?php echo e((int) $month == $m ? 'selected' : ''); ?>>
                                <?php echo e(\Carbon\Carbon::create()->month((int) $m)->translatedFormat('F')); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <select name="year" class="w-32 px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm bg-white">
                        <?php $__currentLoopData = range(now()->year - 2, now()->year + 1); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($y); ?>" <?php echo e($year == $y ? 'selected' : ''); ?>><?php echo e($y); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <button type="submit" class="px-6 py-2.5 text-white font-medium rounded-xl transition-colors shadow-lg flex items-center" style="background: linear-gradient(135deg, #5680E9, #5AB9EA);">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filtrer
                </button>
            </form>
        </div>

        <!-- Alert for pending employees -->
        <?php if($pendingEmployees->isNotEmpty()): ?>
        <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4 flex items-center gap-3 animate-fade-in-up">
            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium text-indigo-900"><?php echo e($pendingEmployees->count()); ?> employé(s) n'ont pas encore été évalués ce mois</p>
                <div class="flex flex-wrap gap-2 mt-2">
                    <?php $__currentLoopData = $pendingEmployees->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('admin.employee-evaluations.create', ['user_id' => $emp->id, 'month' => $month, 'year' => $year])); ?>"
                           class="inline-flex items-center gap-1 px-3 py-1 bg-white text-indigo-700 text-xs font-medium rounded-full border border-indigo-200 hover:bg-indigo-100 transition-colors">
                            <?php echo e($emp->name); ?>

                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php if($pendingEmployees->count() > 5): ?>
                        <span class="px-3 py-1 text-xs font-medium text-indigo-600">+<?php echo e($pendingEmployees->count() - 5); ?> autres</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Data Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden animate-fade-in-up">
            <?php if($evaluations->isEmpty()): ?>
                <div class="p-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <p class="text-lg font-medium text-gray-900">Aucune évaluation trouvée</p>
                    <p class="text-sm text-gray-500 mt-1">Modifiez vos filtres ou créez une nouvelle évaluation.</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Employé</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Note</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Salaire calculé</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Statut</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php $__currentLoopData = $evaluations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $evaluation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="hover:bg-purple-50/50 transition-colors group">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full flex items-center justify-center ring-2 ring-white shadow-sm" style="background: linear-gradient(135deg, #5680E9, #84CEEB);">
                                                <span class="text-white font-bold text-xs"><?php echo e(strtoupper(substr($evaluation->user->name, 0, 2))); ?></span>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900"><?php echo e($evaluation->user->name); ?></p>
                                                <p class="text-xs text-gray-500"><?php echo e($evaluation->user->poste ?? 'Employé'); ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col items-center gap-1">
                                            <span class="text-lg font-bold" style="color: <?php echo e($evaluation->total_score >= 4 ? '#5680E9' : ($evaluation->total_score >= 2.5 ? '#8860D0' : '#ef4444')); ?>;">
                                                <?php echo e(number_format($evaluation->total_score, 1)); ?>

                                            </span>
                                            <div class="w-20 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                                <div class="h-full rounded-full" style="width: <?php echo e($evaluation->score_percentage); ?>%; background-color: <?php echo e($evaluation->total_score >= 4 ? '#5680E9' : ($evaluation->total_score >= 2.5 ? '#8860D0' : '#ef4444')); ?>;"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="font-bold text-gray-700"><?php echo e($evaluation->calculated_salary_formatted); ?></span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <?php if($evaluation->status === 'validated'): ?>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium" style="background-color: rgba(90, 185, 234, 0.15); color: #5680E9;">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                                Validée
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium" style="background-color: rgba(136, 96, 208, 0.15); color: #8860D0;">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                En attente
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <a href="<?php echo e(route('admin.employee-evaluations.show', $evaluation)); ?>" class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Voir">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>
                                            <?php if($evaluation->canBeEdited()): ?>
                                                <a href="<?php echo e(route('admin.employee-evaluations.edit', $evaluation)); ?>" class="p-1.5 text-gray-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Modifier">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </a>
                                                <form action="<?php echo e(route('admin.employee-evaluations.validate', $evaluation)); ?>" method="POST" class="inline">
                                                    <?php echo csrf_field(); ?>
                                                    <button type="submit" onclick="return confirm('Valider cette évaluation ?')" class="p-1.5 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="Valider">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    <?php echo e($evaluations->appends(['month' => $month, 'year' => $year])->links()); ?>

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
<?php /**PATH D:\ManageX\resources\views/admin/employee-evaluations/index.blade.php ENDPATH**/ ?>