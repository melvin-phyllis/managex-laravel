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
                                <li><span class="text-white text-sm font-medium">Nouvelle Règle</span></li>
                            </ol>
                        </nav>
                        <h1 class="text-2xl md:text-3xl font-bold text-white flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </div>
                            Ajouter une Règle de Calcul
                        </h1>
                        <p class="text-white/80 mt-2">Configurez une nouvelle règle fiscale ou cotisation</p>
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

        <!-- Form -->
        <form action="<?php echo e(route('admin.payroll-settings.rules.store', $country)); ?>" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 animate-fade-in-up animation-delay-100">
            <?php echo csrf_field(); ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Code -->
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Code *</label>
                    <input type="text" name="code" id="code" value="<?php echo e(old('code')); ?>" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Ex: IS, CN, CNPS">
                    <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Libellé -->
                <div>
                    <label for="label" class="block text-sm font-medium text-gray-700 mb-1">Libellé *</label>
                    <input type="text" name="label" id="label" value="<?php echo e(old('label')); ?>" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Ex: Impôt sur le Salaire">
                    <?php $__errorArgs = ['label'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Type de régle -->
                <div>
                    <label for="rule_type" class="block text-sm font-medium text-gray-700 mb-1">Type de Règle *</label>
                    <select name="rule_type" id="rule_type" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="tax" <?php echo e(old('rule_type') === 'tax' ? 'selected' : ''); ?>>Taxe</option>
                        <option value="contribution" <?php echo e(old('rule_type') === 'contribution' ? 'selected' : ''); ?>>Cotisation Sociale</option>
                        <option value="allowance" <?php echo e(old('rule_type') === 'allowance' ? 'selected' : ''); ?>>Indemnité</option>
                        <option value="deduction" <?php echo e(old('rule_type') === 'deduction' ? 'selected' : ''); ?>>Retenue</option>
                        <option value="earning" <?php echo e(old('rule_type') === 'earning' ? 'selected' : ''); ?>>Revenu</option>
                    </select>
                </div>

                <!-- Catégorie -->
                <div>
                    <label for="rule_category" class="block text-sm font-medium text-gray-700 mb-1">Catégorie *</label>
                    <select name="rule_category" id="rule_category" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="employee" <?php echo e(old('rule_category', 'employee') === 'employee' ? 'selected' : ''); ?>>Employé (Retenue)</option>
                        <option value="employer" <?php echo e(old('rule_category') === 'employer' ? 'selected' : ''); ?>>Employeur (Charge)</option>
                        <option value="both" <?php echo e(old('rule_category') === 'both' ? 'selected' : ''); ?>>Les deux</option>
                    </select>
                </div>

                <!-- Type de calcul -->
                <div>
                    <label for="calculation_type" class="block text-sm font-medium text-gray-700 mb-1">Mode de Calcul *</label>
                    <select name="calculation_type" id="calculation_type" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            onchange="toggleCalculationFields()">
                        <option value="percentage" <?php echo e(old('calculation_type', 'percentage') === 'percentage' ? 'selected' : ''); ?>>Pourcentage</option>
                        <option value="fixed" <?php echo e(old('calculation_type') === 'fixed' ? 'selected' : ''); ?>>Montant Fixe</option>
                        <option value="bracket" <?php echo e(old('calculation_type') === 'bracket' ? 'selected' : ''); ?>>Barème Progressif</option>
                    </select>
                </div>

                <!-- Base de calcul -->
                <div>
                    <label for="base_field" class="block text-sm font-medium text-gray-700 mb-1">Base de Calcul *</label>
                    <select name="base_field" id="base_field" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="taxable_gross" <?php echo e(old('base_field', 'taxable_gross') === 'taxable_gross' ? 'selected' : ''); ?>>Brut Imposable</option>
                        <option value="gross_salary" <?php echo e(old('base_field') === 'gross_salary' ? 'selected' : ''); ?>>Salaire Brut</option>
                        <option value="igr_base" <?php echo e(old('base_field') === 'igr_base' ? 'selected' : ''); ?>>Base IGR (après IS, CN, CNPS)</option>
                    </select>
                </div>

                <!-- Taux -->
                <div id="rate_field">
                    <label for="rate" class="block text-sm font-medium text-gray-700 mb-1">Taux (%)</label>
                    <input type="number" name="rate" id="rate" value="<?php echo e(old('rate')); ?>" step="0.01" min="0" max="100"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Ex: 1.2">
                </div>

                <!-- Montant fixe -->
                <div id="fixed_field" style="display: none;">
                    <label for="fixed_amount" class="block text-sm font-medium text-gray-700 mb-1">Montant Fixe</label>
                    <input type="number" name="fixed_amount" id="fixed_amount" value="<?php echo e(old('fixed_amount')); ?>" step="0.01" min="0"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Ex: 25000">
                </div>

                <!-- Plafond -->
                <div>
                    <label for="ceiling" class="block text-sm font-medium text-gray-700 mb-1">Plafond</label>
                    <input type="number" name="ceiling" id="ceiling" value="<?php echo e(old('ceiling')); ?>" step="0.01" min="0"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Ex: 1647315">
                    <p class="mt-1 text-xs text-gray-500">Laisser vide si pas de plafond</p>
                </div>

                <!-- Ordre d'affichage -->
                <div>
                    <label for="display_order" class="block text-sm font-medium text-gray-700 mb-1">Ordre d'Affichage</label>
                    <input type="number" name="display_order" id="display_order" value="<?php echo e(old('display_order', 0)); ?>" min="0"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Code PDF -->
                <div>
                    <label for="pdf_code" class="block text-sm font-medium text-gray-700 mb-1">Code PDF</label>
                    <input type="text" name="pdf_code" id="pdf_code" value="<?php echo e(old('pdf_code')); ?>"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Ex: 370">
                    <p class="mt-1 text-xs text-gray-500">Code affiché sur le bulletin de paie</p>
                </div>

                <!-- Barème (JSON) -->
                <div id="brackets_field" class="md:col-span-2" style="display: none;">
                    <label for="brackets" class="block text-sm font-medium text-gray-700 mb-1">Barème (JSON)</label>
                    <textarea name="brackets" id="brackets" rows="5"
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-mono text-sm"
                              placeholder='[{"min": 0, "max": 50000, "rate": 0}, {"min": 50000, "max": 130000, "rate": 0.015}]'><?php echo e(old('brackets')); ?></textarea>
                    <p class="mt-1 text-xs text-gray-500">Format JSON avec min, max, rate, et optionnellement deduction</p>
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" id="description" rows="2"
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"><?php echo e(old('description')); ?></textarea>
                </div>

                <!-- Checkboxes -->
                <div class="md:col-span-2 flex flex-wrap gap-6">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_deductible" value="1" <?php echo e(old('is_deductible') ? 'checked' : ''); ?>

                               class="rounded border-gray-300 focus:ring-indigo-500" style="color: #5680E9;">
                        <span class="ml-2 text-sm text-gray-700">Déductible de la base IGR</span>
                    </label>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_mandatory" value="1" <?php echo e(old('is_mandatory', true) ? 'checked' : ''); ?>

                               class="rounded border-gray-300 focus:ring-indigo-500" style="color: #5680E9;">
                        <span class="ml-2 text-sm text-gray-700">Obligatoire</span>
                    </label>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_visible_on_payslip" value="1" <?php echo e(old('is_visible_on_payslip', true) ? 'checked' : ''); ?>

                               class="rounded border-gray-300 focus:ring-indigo-500" style="color: #5680E9;">
                        <span class="ml-2 text-sm text-gray-700">Visible sur le bulletin</span>
                    </label>
                </div>
            </div>

            <!-- Actions -->
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
        // Initialize on load
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
<?php /**PATH D:\ManageX\resources\views/admin/payroll-settings/rules/create.blade.php ENDPATH**/ ?>