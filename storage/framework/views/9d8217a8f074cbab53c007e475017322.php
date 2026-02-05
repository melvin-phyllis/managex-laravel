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
        <!-- Header avec Tolia Blue -->
        <div class="relative overflow-hidden rounded-2xl p-6 text-white shadow-xl" style="background-color: #3B8BEB;">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full" style="transform: translate(30%, -50%);"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full" style="transform: translate(-30%, 50%);"></div>
            
            <div class="relative flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold mb-1">Mes Fiches de Paie</h1>
                    <p style="color: #C4DBF6;">Consultez et téléchargez vos bulletins de salaire</p>
                </div>
                <div class="hidden sm:flex items-center gap-3">
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <?php
            $totalPayrolls = $payrolls->total();
            $paidPayrolls = $payrolls->where('statut', 'paid')->count();
            $pendingPayrolls = $payrolls->where('statut', 'pending')->count();
            $totalAmount = $payrolls->sum('net_a_payer');
        ?>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: #3B8BEB;">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900"><?php echo e($totalPayrolls); ?></p>
                        <p class="text-xs text-gray-500">Total fiches</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: rgba(59, 139, 235, 0.15);">
                        <svg class="w-5 h-5" style="color: #3B8BEB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900"><?php echo e($paidPayrolls); ?></p>
                        <p class="text-xs text-gray-500">Payées</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: #E7E3D4;">
                        <svg class="w-5 h-5" style="color: #8590AA;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900"><?php echo e($pendingPayrolls); ?></p>
                        <p class="text-xs text-gray-500">En attente</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: rgba(178, 56, 80, 0.15);">
                        <svg class="w-5 h-5" style="color: #B23850;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-lg font-bold text-gray-900"><?php echo e(number_format($totalAmount, 0, ',', ' ')); ?></p>
                        <p class="text-xs text-gray-500">Total (FCFA)</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des fiches de paie -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100" style="background-color: rgba(231, 227, 212, 0.3);">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5" style="color: #3B8BEB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Historique des bulletins
                </h3>
            </div>

            <?php if($payrolls->isEmpty()): ?>
                <div class="p-12 text-center">
                    <div class="w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background-color: rgba(59, 139, 235, 0.1);">
                        <svg class="w-10 h-10" style="color: #3B8BEB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Aucune fiche de paie</h3>
                    <p class="text-gray-500">Vos bulletins de salaire apparaîtront ici</p>
                </div>
            <?php else: ?>
                <!-- Vue Desktop -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Période</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Salaire brut</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Net à payer</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Statut</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php $__currentLoopData = $payrolls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payroll): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $statusConfig = [
                                        'draft' => ['bg' => '#E7E3D4', 'text' => '#8590AA', 'label' => 'Brouillon'],
                                        'pending' => ['bg' => '#E7E3D4', 'text' => '#8590AA', 'label' => 'En attente'],
                                        'paid' => ['bg' => 'rgba(59, 139, 235, 0.15)', 'text' => '#3B8BEB', 'label' => 'Payé'],
                                        'cancelled' => ['bg' => 'rgba(178, 56, 80, 0.15)', 'text' => '#B23850', 'label' => 'Annulé'],
                                    ];
                                    $status = $statusConfig[$payroll->statut] ?? $statusConfig['pending'];
                                ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: rgba(59, 139, 235, 0.1);">
                                                <svg class="w-5 h-5" style="color: #3B8BEB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-900"><?php echo e($payroll->periode); ?></p>
                                                <p class="text-xs text-gray-500"><?php echo e($payroll->created_at->format('d/m/Y')); ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm text-gray-600"><?php echo e(number_format($payroll->salaire_base, 0, ',', ' ')); ?> FCFA</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-lg font-bold" style="color: #3B8BEB;"><?php echo e(number_format($payroll->net_a_payer, 0, ',', ' ')); ?> FCFA</p>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" style="background-color: <?php echo e($status['bg']); ?>; color: <?php echo e($status['text']); ?>;">
                                            <?php echo e($status['label']); ?>

                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="<?php echo e(route('employee.payrolls.download', $payroll)); ?>" 
                                           class="inline-flex items-center gap-2 px-4 py-2 text-white rounded-lg transition-all shadow-sm text-sm font-medium" style="background-color: #B23850;">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            PDF
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>

                <!-- Vue Mobile -->
                <div class="md:hidden divide-y divide-gray-100">
                    <?php $__currentLoopData = $payrolls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payroll): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $statusConfig = [
                                'draft' => ['bg' => '#E7E3D4', 'text' => '#8590AA', 'label' => 'Brouillon'],
                                'pending' => ['bg' => '#E7E3D4', 'text' => '#8590AA', 'label' => 'En attente'],
                                'paid' => ['bg' => 'rgba(59, 139, 235, 0.15)', 'text' => '#3B8BEB', 'label' => 'Payé'],
                                'cancelled' => ['bg' => 'rgba(178, 56, 80, 0.15)', 'text' => '#B23850', 'label' => 'Annulé'],
                            ];
                            $status = $statusConfig[$payroll->statut] ?? $statusConfig['pending'];
                        ?>
                        <div class="p-4">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: rgba(59, 139, 235, 0.1);">
                                        <svg class="w-5 h-5" style="color: #3B8BEB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900"><?php echo e($payroll->periode); ?></p>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" style="background-color: <?php echo e($status['bg']); ?>; color: <?php echo e($status['text']); ?>;">
                                            <?php echo e($status['label']); ?>

                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-gray-500">Net à payer</p>
                                    <p class="text-lg font-bold" style="color: #3B8BEB;"><?php echo e(number_format($payroll->net_a_payer, 0, ',', ' ')); ?> FCFA</p>
                                </div>
                                <a href="<?php echo e(route('employee.payrolls.download', $payroll)); ?>" 
                                   class="inline-flex items-center gap-2 px-4 py-2 text-white rounded-lg text-sm font-medium" style="background-color: #B23850;">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    PDF
                                </a>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <!-- Pagination -->
                <?php if($payrolls->hasPages()): ?>
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                        <?php echo e($payrolls->links()); ?>

                    </div>
                <?php endif; ?>
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
<?php /**PATH D:\ManageX\resources\views/employee/payrolls/index.blade.php ENDPATH**/ ?>