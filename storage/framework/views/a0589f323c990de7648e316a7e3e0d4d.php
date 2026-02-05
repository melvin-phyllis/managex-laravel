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
                                <li><a href="<?php echo e(route('admin.payroll-settings.countries')); ?>" class="text-white/70 hover:text-white text-sm">Configuration Paie</a></li>
                                <li><span class="text-white/50 mx-2">/</span></li>
                                <li><a href="<?php echo e(route('admin.payroll-settings.rules', $country)); ?>" class="text-white/70 hover:text-white text-sm"><?php echo e($country->name); ?></a></li>
                                <li><span class="text-white/50 mx-2">/</span></li>
                                <li><span class="text-white text-sm font-medium">Modifier <?php echo e($rule->code); ?></span></li>
                            </ol>
                        </nav>
                        <h1 class="text-2xl md:text-3xl font-bold text-white flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </div>
                            Modifier la Règle - <?php echo e($rule->label); ?>

                        </h1>
                        <p class="text-white/80 mt-2">Modifiez les paramètres de cette règle de calcul</p>
                    </div>
                    <a href="<?php echo e(route('admin.payroll-settings.rules', $country)); ?>" 
                       class="px-4 py-2.5 bg-white font-semibold rounded-xl hover:bg-purple-50 transition-all shadow-lg flex items-center" style="color: #5680E9;">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Retour aux Règles
                    </a>
                </div>
            </div>
        </div>

        <form action="<?php echo e(route('admin.payroll-settings.rules.update', [$country, $rule])); ?>" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 animate-fade-in-up animation-delay-100">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Code</label>
                    <input type="text" value="<?php echo e($rule->code); ?>" disabled
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-gray-500">
                </div>

                <div>
                    <label for="label" class="block text-sm font-medium text-gray-700 mb-1">Libellé *</label>
                    <input type="text" name="label" id="label" value="<?php echo e(old('label', $rule->label)); ?>" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label for="rule_type" class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                    <select name="rule_type" id="rule_type" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="tax" <?php echo e(old('rule_type', $rule->rule_type) === 'tax' ? 'selected' : ''); ?>>Taxe</option>
                        <option value="contribution" <?php echo e(old('rule_type', $rule->rule_type) === 'contribution' ? 'selected' : ''); ?>>Cotisation</option>
                        <option value="allowance" <?php echo e(old('rule_type', $rule->rule_type) === 'allowance' ? 'selected' : ''); ?>>Indemnité</option>
                        <option value="deduction" <?php echo e(old('rule_type', $rule->rule_type) === 'deduction' ? 'selected' : ''); ?>>Retenue</option>
                        <option value="earning" <?php echo e(old('rule_type', $rule->rule_type) === 'earning' ? 'selected' : ''); ?>>Revenu</option>
                    </select>
                </div>

                <div>
                    <label for="rule_category" class="block text-sm font-medium text-gray-700 mb-1">Catégorie *</label>
                    <select name="rule_category" id="rule_category" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="employee" <?php echo e(old('rule_category', $rule->rule_category) === 'employee' ? 'selected' : ''); ?>>Employé</option>
                        <option value="employer" <?php echo e(old('rule_category', $rule->rule_category) === 'employer' ? 'selected' : ''); ?>>Employeur</option>
                        <option value="both" <?php echo e(old('rule_category', $rule->rule_category) === 'both' ? 'selected' : ''); ?>>Les deux</option>
                    </select>
                </div>

                <div>
                    <label for="calculation_type" class="block text-sm font-medium text-gray-700 mb-1">Mode de Calcul *</label>
                    <select name="calculation_type" id="calculation_type" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            onchange="toggleCalculationFields()">
                        <option value="percentage" <?php echo e(old('calculation_type', $rule->calculation_type) === 'percentage' ? 'selected' : ''); ?>>Pourcentage</option>
                        <option value="fixed" <?php echo e(old('calculation_type', $rule->calculation_type) === 'fixed' ? 'selected' : ''); ?>>Montant Fixe</option>
                        <option value="bracket" <?php echo e(old('calculation_type', $rule->calculation_type) === 'bracket' ? 'selected' : ''); ?>>Barème</option>
                    </select>
                </div>

                <div>
                    <label for="base_field" class="block text-sm font-medium text-gray-700 mb-1">Base de Calcul *</label>
                    <select name="base_field" id="base_field" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="taxable_gross" <?php echo e(old('base_field', $rule->base_field) === 'taxable_gross' ? 'selected' : ''); ?>>Brut Imposable</option>
                        <option value="gross_salary" <?php echo e(old('base_field', $rule->base_field) === 'gross_salary' ? 'selected' : ''); ?>>Salaire Brut</option>
                        <option value="igr_base" <?php echo e(old('base_field', $rule->base_field) === 'igr_base' ? 'selected' : ''); ?>>Base IGR</option>
                    </select>
                </div>

                <div id="rate_field">
                    <label for="rate" class="block text-sm font-medium text-gray-700 mb-1">Taux (%)</label>
                    <input type="number" name="rate" id="rate" value="<?php echo e(old('rate', $rule->rate)); ?>" step="0.01" min="0" max="100"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div id="fixed_field" style="display: none;">
                    <label for="fixed_amount" class="block text-sm font-medium text-gray-700 mb-1">Montant Fixe</label>
                    <input type="number" name="fixed_amount" id="fixed_amount" value="<?php echo e(old('fixed_amount', $rule->fixed_amount)); ?>" step="0.01" min="0"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label for="ceiling" class="block text-sm font-medium text-gray-700 mb-1">Plafond</label>
                    <input type="number" name="ceiling" id="ceiling" value="<?php echo e(old('ceiling', $rule->ceiling)); ?>" step="0.01" min="0"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label for="display_order" class="block text-sm font-medium text-gray-700 mb-1">Ordre d'Affichage</label>
                    <input type="number" name="display_order" id="display_order" value="<?php echo e(old('display_order', $rule->display_order)); ?>" min="0"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label for="pdf_code" class="block text-sm font-medium text-gray-700 mb-1">Code PDF</label>
                    <input type="text" name="pdf_code" id="pdf_code" value="<?php echo e(old('pdf_code', $rule->pdf_code)); ?>"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div id="brackets_field" class="md:col-span-2" style="display: none;">
                    <label for="brackets" class="block text-sm font-medium text-gray-700 mb-1">Barème (JSON)</label>
                    <textarea name="brackets" id="brackets" rows="5"
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-mono text-sm"><?php echo e(old('brackets', json_encode($rule->brackets, JSON_PRETTY_PRINT))); ?></textarea>
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" id="description" rows="2"
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"><?php echo e(old('description', $rule->description)); ?></textarea>
                </div>

                <div class="md:col-span-2 flex flex-wrap gap-6">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_deductible" value="1" <?php echo e(old('is_deductible', $rule->is_deductible) ? 'checked' : ''); ?>

                               class="rounded border-gray-300 focus:ring-indigo-500" style="color: #5680E9;">
                        <span class="ml-2 text-sm text-gray-700">Déductible de la base IGR</span>
                    </label>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_mandatory" value="1" <?php echo e(old('is_mandatory', $rule->is_mandatory) ? 'checked' : ''); ?>

                               class="rounded border-gray-300 focus:ring-indigo-500" style="color: #5680E9;">
                        <span class="ml-2 text-sm text-gray-700">Obligatoire</span>
                    </label>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_visible_on_payslip" value="1" <?php echo e(old('is_visible_on_payslip', $rule->is_visible_on_payslip) ? 'checked' : ''); ?>

                               class="rounded border-gray-300 focus:ring-indigo-500" style="color: #5680E9;">
                        <span class="ml-2 text-sm text-gray-700">Visible sur le bulletin</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-gray-100">
                <a href="<?php echo e(route('admin.payroll-settings.rules', $country)); ?>" 
                   class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">
                    Annuler
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 text-sm font-semibold text-white rounded-xl shadow-lg transition-all" style="background: linear-gradient(135deg, #5680E9, #5AB9EA);">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>

    <script nonce="<?php echo e($cspNonce ?? ''); ?>">
        function toggleCalculationFields() {
            const type = document.getElementById('calculation_type').value;
            document.getElementById('rate_field').style.display = (type === 'percentage') ? 'block' : 'none';
            document.getElementById('fixed_field').style.display = (type === 'fixed') ? 'block' : 'none';
            document.getElementById('brackets_field').style.display = (type === 'bracket') ? 'block' : 'none';
        }
        toggleCalculationFields();
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
<?php /**PATH D:\ManageX\resources\views/admin/payroll-settings/rules/edit.blade.php ENDPATH**/ ?>