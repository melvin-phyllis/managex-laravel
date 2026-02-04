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
        <div class="flex items-center justify-between">
            <div>
                <nav class="flex items-center text-sm text-gray-500 mb-2">
                    <a href="<?php echo e(route('admin.payroll-settings.countries')); ?>" class="hover:text-red-600">Configuration Paie</a>
                    <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <span><?php echo e($country->name); ?></span>
                    <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <span class="text-gray-900">Champs Dynamiques</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900">Champs Dynamiques - <?php echo e($country->name); ?></h1>
            </div>
        </div>

        <?php if(session('success')): ?>
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <p class="text-green-800"><?php echo e(session('success')); ?></p>
            </div>
        <?php endif; ?>

        <!-- Existing Fields -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-200 bg-gray-50">
                <h2 class="font-semibold text-gray-900">Champs Configurés</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Champ</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Libellé</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Section</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Imposable</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__empty_1 = true; $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-mono text-sm bg-gray-100 px-2 py-1 rounded"><?php echo e($field->field_name); ?></span>
                                </td>
                                <td class="px-6 py-4"><?php echo e($field->field_label); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <?php echo e($field->field_type); ?>

                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e($field->section); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if($field->is_taxable): ?>
                                        <span class="text-green-600">Oui</span>
                                    <?php else: ?>
                                        <span class="text-gray-400">Non</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <form action="<?php echo e(route('admin.payroll-settings.fields.destroy', [$country, $field])); ?>" method="POST" class="inline" onsubmit="return confirm('Supprimer ce champ ?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="text-red-600 hover:text-red-900 text-sm">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    Aucun champ dynamique défini.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add New Field Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-semibold text-gray-900 mb-4">Ajouter un Champ</h2>
            <form action="<?php echo e(route('admin.payroll-settings.fields.store', $country)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="field_name" class="block text-sm font-medium text-gray-700 mb-1">Nom du Champ *</label>
                        <input type="text" name="field_name" id="field_name" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                               placeholder="transport_allowance">
                    </div>
                    <div>
                        <label for="field_label" class="block text-sm font-medium text-gray-700 mb-1">Libellé *</label>
                        <input type="text" name="field_label" id="field_label" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                               placeholder="Indemnité de Transport">
                    </div>
                    <div>
                        <label for="field_type" class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                        <select name="field_type" id="field_type" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            <option value="number">Nombre</option>
                            <option value="text">Texte</option>
                            <option value="select">Liste</option>
                            <option value="boolean">Oui/Non</option>
                            <option value="date">Date</option>
                        </select>
                    </div>
                    <div>
                        <label for="section" class="block text-sm font-medium text-gray-700 mb-1">Section *</label>
                        <select name="section" id="section" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            <option value="allowances">Indemnités</option>
                            <option value="deductions">Retenues</option>
                            <option value="earnings">Revenus</option>
                            <option value="info">Informations</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-4">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="is_taxable" value="1" checked
                                   class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                            <span class="ml-2 text-sm text-gray-700">Imposable</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="is_required" value="1"
                                   class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                            <span class="ml-2 text-sm text-gray-700">Obligatoire</span>
                        </label>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" 
                                class="w-full px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-red-600 to-red-700 rounded-lg hover:from-red-700 hover:to-red-800">
                            Ajouter le Champ
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="flex justify-start">
            <a href="<?php echo e(route('admin.payroll-settings.countries')); ?>" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour aux pays
            </a>
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
<?php /**PATH D:\ManageX\resources\views\admin\payroll-settings\fields\index.blade.php ENDPATH**/ ?>