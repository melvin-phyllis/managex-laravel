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
        <div class="flex items-center justify-between animate-fade-in-up">
            <div>
                <nav class="flex items-center text-sm text-gray-500 mb-2">
                    <a href="<?php echo e(route('admin.payrolls.index')); ?>" class="hover:text-red-600">Fiches de paie</a>
                    <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <span class="text-gray-900">Édition</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900">Éditer la Fiche de Paie</h1>
                <p class="text-sm text-gray-500 mt-1"><?php echo e($payroll->user->name); ?> - <?php echo e($payroll->mois_label); ?> <?php echo e($payroll->annee); ?></p>
            </div>
            <div class="flex gap-3">
                <a href="<?php echo e(route('admin.payrolls.show', $payroll)); ?>" 
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Prévisualiser
                </a>
            </div>
        </div>

        <!-- Status Badge -->
        <div class="flex items-center gap-4">
            <?php
                $statusColors = [
                    'draft' => 'bg-gray-100 text-gray-800',
                    'pending_review' => 'bg-yellow-100 text-yellow-800',
                    'validated' => 'bg-green-100 text-green-800',
                    'rejected' => 'bg-red-100 text-red-800',
                ];
                $statusLabels = [
                    'draft' => 'Brouillon',
                    'pending_review' => 'En attente de validation',
                    'validated' => 'Validée',
                    'rejected' => 'Rejetée',
                ];
            ?>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?php echo e($statusColors[$payroll->workflow_status ?? 'draft']); ?>">
                <?php echo e($statusLabels[$payroll->workflow_status ?? 'draft']); ?>

            </span>
        </div>

        <form action="<?php echo e(route('admin.payrolls.update', $payroll)); ?>" method="POST" class="space-y-6">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <!-- Employee Info Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 animate-fade-in-up animation-delay-100">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations Employé</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <span class="text-xs text-gray-500">Nom</span>
                        <p class="font-medium"><?php echo e($payroll->user->name); ?></p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500">Matricule</span>
                        <p class="font-medium"><?php echo e($payroll->user->employee_id ?? '-'); ?></p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500">Salaire de Base</span>
                        <p class="font-medium"><?php echo e(number_format($payroll->user->currentContract->base_salary ?? 0, 0, ',', ' ')); ?> FCFA</p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500">Parts Fiscales</span>
                        <p class="font-medium"><?php echo e($payroll->fiscal_parts ?? 1); ?></p>
                    </div>
                </div>
            </div>

            <!-- Gains -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 animate-fade-in-up animation-delay-200">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Gains & Indemnités</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="taxable_gross" class="block text-sm font-medium text-gray-700 mb-1">Brut Imposable</label>
                        <input type="number" name="taxable_gross" id="taxable_gross" 
                               value="<?php echo e(old('taxable_gross', $payroll->taxable_gross)); ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    </div>
                    <div>
                        <label for="transport_allowance" class="block text-sm font-medium text-gray-700 mb-1">Indemnité Transport</label>
                        <input type="number" name="transport_allowance" id="transport_allowance" 
                               value="<?php echo e(old('transport_allowance', $payroll->transport_allowance)); ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    </div>
                    <div>
                        <label for="bonuses" class="block text-sm font-medium text-gray-700 mb-1">Primes</label>
                        <input type="number" name="bonuses" id="bonuses" 
                               value="<?php echo e(old('bonuses', $payroll->bonuses)); ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    </div>
                </div>
            </div>

            <!-- Retenues (Read-only, calculated) -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Retenues Fiscales & Sociales</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label for="tax_is" class="block text-sm font-medium text-gray-700 mb-1">IS (1.2%)</label>
                        <input type="number" name="tax_is" id="tax_is" 
                               value="<?php echo e(old('tax_is', $payroll->tax_is)); ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    </div>
                    <div>
                        <label for="tax_cn" class="block text-sm font-medium text-gray-700 mb-1">CN</label>
                        <input type="number" name="tax_cn" id="tax_cn" 
                               value="<?php echo e(old('tax_cn', $payroll->tax_cn)); ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    </div>
                    <div>
                        <label for="tax_igr" class="block text-sm font-medium text-gray-700 mb-1">IGR</label>
                        <input type="number" name="tax_igr" id="tax_igr" 
                               value="<?php echo e(old('tax_igr', $payroll->tax_igr)); ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    </div>
                    <div>
                        <label for="cnps_employee" class="block text-sm font-medium text-gray-700 mb-1">CNPS (5.4%)</label>
                        <input type="number" name="cnps_employee" id="cnps_employee" 
                               value="<?php echo e(old('cnps_employee', $payroll->cnps_employee)); ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    </div>
                </div>
            </div>

            <!-- Totaux -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Totaux</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="total_deductions" class="block text-sm font-medium text-gray-700 mb-1">Total Retenues</label>
                        <input type="number" name="total_deductions" id="total_deductions" 
                               value="<?php echo e(old('total_deductions', $payroll->total_deductions)); ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 bg-gray-50">
                    </div>
                    <div>
                        <label for="net_salary" class="block text-sm font-medium text-gray-700 mb-1">Net à Payer</label>
                        <input type="number" name="net_salary" id="net_salary" 
                               value="<?php echo e(old('net_salary', $payroll->net_salary)); ?>"
                               class="w-full px-4 py-2 border border-red-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 bg-red-50 text-red-900 font-bold text-lg">
                    </div>
                    <div>
                        <label for="workflow_status" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                        <select name="workflow_status" id="workflow_status"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            <option value="draft" <?php echo e(($payroll->workflow_status ?? 'draft') === 'draft' ? 'selected' : ''); ?>>Brouillon</option>
                            <option value="pending_review" <?php echo e(($payroll->workflow_status ?? '') === 'pending_review' ? 'selected' : ''); ?>>En attente de validation</option>
                            <option value="validated" <?php echo e(($payroll->workflow_status ?? '') === 'validated' ? 'selected' : ''); ?>>Validée</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea name="notes" id="notes" rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                          placeholder="Notes internes (non visibles sur le bulletin)"><?php echo e(old('notes', $payroll->notes)); ?></textarea>
            </div>

            <!-- Actions -->
            <div class="flex justify-between">
                <a href="<?php echo e(route('admin.payrolls.index')); ?>" 
                   class="px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Annuler
                </a>
                <div class="flex gap-3">
                    <button type="submit" name="action" value="save"
                            class="px-6 py-2 text-sm font-medium text-white bg-gray-600 rounded-lg hover:bg-gray-700">
                        Enregistrer
                    </button>
                    <button type="submit" name="action" value="validate"
                            class="px-6 py-2 text-sm font-medium text-white bg-gradient-to-r from-green-600 to-green-700 rounded-lg hover:from-green-700 hover:to-green-800">
                        Valider et Générer PDF
                    </button>
                </div>
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
<?php /**PATH D:\ManageX\resources\views\admin\payrolls\edit.blade.php ENDPATH**/ ?>