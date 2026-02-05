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
                                <li><span class="text-white text-sm font-medium">Configuration Paie</span></li>
                            </ol>
                        </nav>
                        <h1 class="text-2xl md:text-3xl font-bold text-white flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
                                </svg>
                            </div>
                            Configuration Paie Multi-Pays
                        </h1>
                        <p class="text-white/80 mt-2">Gérez les réglementations fiscales par pays</p>
                    </div>
                    <a href="<?php echo e(route('admin.payroll-settings.countries.create')); ?>" 
                       class="px-4 py-2.5 bg-white font-semibold rounded-xl hover:bg-purple-50 transition-all shadow-lg flex items-center" style="color: #5680E9;">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Ajouter un Pays
                    </a>
                </div>
            </div>
        </div>

        <!-- Success Message -->
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

        <!-- Countries Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 animate-fade-in-up animation-delay-100">
            <?php $__empty_1 = true; $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all group">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold text-lg" style="background: linear-gradient(135deg, #5680E9, #84CEEB);">
                                    <?php echo e($country->code); ?>

                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900"><?php echo e($country->name); ?></h3>
                                    <p class="text-sm text-gray-500"><?php echo e($country->currency); ?> (<?php echo e($country->currency_symbol); ?>)</p>
                                </div>
                            </div>
                            <?php if($country->is_active): ?>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium" style="background-color: rgba(90, 185, 234, 0.15); color: #5680E9;">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Actif
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                    Inactif
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="grid grid-cols-3 gap-3 mb-4">
                            <div class="text-center p-3 bg-gray-50 rounded-xl">
                                <div class="text-2xl font-bold" style="color: #5680E9;"><?php echo e($country->rules_count); ?></div>
                                <div class="text-xs text-gray-500">Règles</div>
                            </div>
                            <div class="text-center p-3 bg-gray-50 rounded-xl">
                                <div class="text-2xl font-bold" style="color: #84CEEB;"><?php echo e($country->fields_count); ?></div>
                                <div class="text-xs text-gray-500">Champs</div>
                            </div>
                            <div class="text-center p-3 bg-gray-50 rounded-xl">
                                <div class="text-2xl font-bold" style="color: #8860D0;"><?php echo e($country->templates_count); ?></div>
                                <div class="text-xs text-gray-500">Templates</div>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <a href="<?php echo e(route('admin.payroll-settings.rules', $country)); ?>" 
                               class="flex-1 inline-flex items-center justify-center px-3 py-2.5 text-sm font-medium rounded-xl transition-colors" style="background-color: rgba(86, 128, 233, 0.1); color: #5680E9;">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                Règles
                            </a>
                            <a href="<?php echo e(route('admin.payroll-settings.fields', $country)); ?>" 
                               class="flex-1 inline-flex items-center justify-center px-3 py-2.5 text-sm font-medium rounded-xl transition-colors" style="background-color: rgba(132, 206, 235, 0.15); color: #5680E9;">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                                </svg>
                                Champs
                            </a>
                            <a href="<?php echo e(route('admin.payroll-settings.countries.edit', $country)); ?>" 
                               class="inline-flex items-center justify-center px-3 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="col-span-full">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">Aucun pays configuré</h3>
                        <p class="mt-1 text-sm text-gray-500">Commencez par ajouter un pays pour configurer les règles de paie.</p>
                        <a href="<?php echo e(route('admin.payroll-settings.countries.create')); ?>" 
                           class="mt-4 inline-flex items-center px-4 py-2.5 text-white font-semibold rounded-xl shadow-lg" style="background: linear-gradient(135deg, #5680E9, #5AB9EA);">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Ajouter un Pays
                        </a>
                    </div>
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
<?php /**PATH D:\ManageX\resources\views/admin/payroll-settings/countries/index.blade.php ENDPATH**/ ?>