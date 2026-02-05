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
                                <li><span class="text-white text-sm font-medium">Nouveau</span></li>
                            </ol>
                        </nav>
                        <h1 class="text-2xl md:text-3xl font-bold text-white flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </div>
                            Ajouter un Pays
                        </h1>
                        <p class="text-white/80 mt-2">Configurez un nouveau pays pour la gestion de paie</p>
                    </div>
                    <a href="<?php echo e(route('admin.payroll-settings.countries')); ?>" 
                       class="px-4 py-2.5 bg-white font-semibold rounded-xl hover:bg-purple-50 transition-all shadow-lg flex items-center" style="color: #5680E9;">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Retour
                    </a>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form action="<?php echo e(route('admin.payroll-settings.countries.store')); ?>" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 animate-fade-in-up animation-delay-100">
            <?php echo csrf_field(); ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Code ISO (3 lettres) *</label>
                    <input type="text" name="code" id="code" value="<?php echo e(old('code')); ?>" required maxlength="3"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 uppercase"
                           placeholder="CIV, FRA, SEN...">
                    <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nom du Pays *</label>
                    <input type="text" name="name" id="name" value="<?php echo e(old('name')); ?>" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Côte d'Ivoire">
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label for="currency" class="block text-sm font-medium text-gray-700 mb-1">Code Devise (3 lettres) *</label>
                    <input type="text" name="currency" id="currency" value="<?php echo e(old('currency')); ?>" required maxlength="3"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 uppercase"
                           placeholder="XOF, EUR...">
                </div>

                <div>
                    <label for="currency_symbol" class="block text-sm font-medium text-gray-700 mb-1">Symbole Devise *</label>
                    <input type="text" name="currency_symbol" id="currency_symbol" value="<?php echo e(old('currency_symbol')); ?>" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="FCFA, €...">
                </div>

                <div class="md:col-span-2">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" <?php echo e(old('is_active', true) ? 'checked' : ''); ?>

                               class="rounded border-gray-300 focus:ring-indigo-500" style="color: #5680E9;">
                        <span class="ml-2 text-sm text-gray-700">Actif</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-gray-100">
                <a href="<?php echo e(route('admin.payroll-settings.countries')); ?>" 
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
<?php /**PATH D:\ManageX\resources\views/admin/payroll-settings/countries/create.blade.php ENDPATH**/ ?>