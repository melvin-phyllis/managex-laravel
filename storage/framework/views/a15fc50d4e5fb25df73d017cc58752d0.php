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
                                <li><a href="<?php echo e(route('admin.employee-evaluations.index', ['month' => $month, 'year' => $year])); ?>" class="text-white/70 hover:text-white text-sm">Évaluations</a></li>
                                <li><span class="text-white/50 mx-2">/</span></li>
                                <li><span class="text-white text-sm font-medium">Nouvelle</span></li>
                            </ol>
                        </nav>
                        <h1 class="text-2xl md:text-3xl font-bold text-white flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                            </div>
                            Nouvelle évaluation
                        </h1>
                        <p class="text-white/80 mt-2"><?php echo e(\Carbon\Carbon::create()->month((int) $month)->translatedFormat('F')); ?> <?php echo e($year); ?></p>
                    </div>
                    <a href="<?php echo e(route('admin.employee-evaluations.index', ['month' => $month, 'year' => $year])); ?>" 
                       class="px-4 py-2.5 bg-white font-semibold rounded-xl hover:bg-purple-50 transition-all shadow-lg flex items-center" style="color: #5680E9;">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Retour
                    </a>
                </div>
            </div>
        </div>

        <form action="<?php echo e(route('admin.employee-evaluations.store')); ?>" method="POST" id="evaluationForm">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="month" value="<?php echo e($month); ?>">
            <input type="hidden" name="year" value="<?php echo e($year); ?>">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Formulaire principal -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Sélection employé -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 animate-fade-in-up animation-delay-100">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Employé à évaluer</h3>
                        
                        <?php if($selectedEmployee): ?>
                            <input type="hidden" name="user_id" value="<?php echo e($selectedEmployee->id); ?>">
                            <div class="flex items-center gap-4 p-4 rounded-xl border" style="background-color: rgba(86, 128, 233, 0.05); border-color: rgba(86, 128, 233, 0.2);">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-semibold text-lg" style="background: linear-gradient(135deg, #5680E9, #84CEEB);">
                                    <?php echo e(strtoupper(substr($selectedEmployee->name, 0, 2))); ?>

                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900"><?php echo e($selectedEmployee->name); ?></p>
                                    <p class="text-sm text-gray-500"><?php echo e($selectedEmployee->poste ?? $selectedEmployee->contract_type); ?> | <?php echo e($selectedEmployee->email); ?></p>
                                </div>
                            </div>
                        <?php else: ?>
                            <select name="user_id" required class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">-- Sélectionner un employé --</option>
                                <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($employee->id); ?>">
                                        <?php echo e($employee->name); ?> (<?php echo e($employee->poste ?? $employee->contract_type); ?>)
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php if($employees->isEmpty()): ?>
                                <p class="mt-2 text-sm text-amber-600">Tous les employés ont déjà été évalués ce mois.</p>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php $__errorArgs = ['user_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Critères d'évaluation -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 animate-fade-in-up animation-delay-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6">Critères d'évaluation</h3>
                        
                        <div class="space-y-6">
                            <?php $__currentLoopData = $criteria; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $criterion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="p-4 bg-gray-50 rounded-xl">
                                    <div class="flex items-center justify-between mb-2">
                                        <label class="font-medium text-gray-900"><?php echo e($criterion['label']); ?></label>
                                        <span class="text-sm px-2 py-0.5 rounded-full" style="background-color: rgba(86, 128, 233, 0.1); color: #5680E9;">Max: <?php echo e($criterion['max']); ?> pts</span>
                                    </div>
                                    <p class="text-sm text-gray-500 mb-3"><?php echo e($criterion['description']); ?></p>
                                    <div class="flex items-center gap-4">
                                        <input type="range" 
                                               name="<?php echo e($key); ?>" 
                                               id="<?php echo e($key); ?>"
                                               min="0" 
                                               max="<?php echo e($criterion['max']); ?>" 
                                               step="0.5"
                                               value="<?php echo e(old($key, 0)); ?>"
                                               class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer criteria-input"
                                               style="accent-color: #5680E9;"
                                               data-max="<?php echo e($criterion['max']); ?>">
                                        <div class="w-20 text-center">
                                            <input type="number" 
                                                   id="<?php echo e($key); ?>_display"
                                                   min="0" 
                                                   max="<?php echo e($criterion['max']); ?>" 
                                                   step="0.5"
                                                   value="<?php echo e(old($key, 0)); ?>"
                                                   class="w-full text-center font-bold text-lg border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500 criteria-display"
                                                   data-target="<?php echo e($key); ?>">
                                        </div>
                                    </div>
                                    <?php $__errorArgs = [$key];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>

                    <!-- Commentaires -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 animate-fade-in-up animation-delay-300">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Commentaires</h3>
                        <textarea name="comments" rows="4" 
                                  class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                  placeholder="Observations, points d'amélioration, félicitations..."><?php echo e(old('comments')); ?></textarea>
                    </div>
                </div>

                <!-- Résumé -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sticky top-6 animate-fade-in-up animation-delay-100">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Résumé</h3>
                        
                        <!-- Score total -->
                        <div class="text-center p-6 rounded-xl mb-6" style="background: linear-gradient(135deg, rgba(86, 128, 233, 0.1), rgba(132, 206, 235, 0.1));">
                            <p class="text-sm text-gray-500 mb-1">Note totale</p>
                            <div class="flex items-baseline justify-center gap-1">
                                <span id="totalScore" class="text-4xl font-bold" style="color: #5680E9;">0.0</span>
                                <span class="text-xl text-gray-400">/5,5</span>
                            </div>
                            <div class="mt-3 w-full bg-gray-200 rounded-full h-2">
                                <div id="scoreBar" class="h-2 rounded-full transition-all duration-300" style="width: 0%; background: linear-gradient(90deg, #5680E9, #84CEEB);"></div>
                            </div>
                            <p id="scorePercentage" class="text-sm text-gray-500 mt-2">0%</p>
                        </div>

                        <!-- Salaire calculé -->
                        <div class="text-center p-6 rounded-xl mb-6" style="background: linear-gradient(135deg, rgba(90, 185, 234, 0.1), rgba(86, 128, 233, 0.1));">
                            <p class="text-sm text-gray-500 mb-1">Salaire brut calculé</p>
                            <p id="calculatedSalary" class="text-3xl font-bold" style="color: #5680E9;"><?php echo e(number_format($smic, 0, ',', ' ')); ?> FCFA</p>
                            <p class="text-xs text-gray-500 mt-2">SMIC minimum garanti : <?php echo e(number_format($smic, 0, ',', ' ')); ?> FCFA</p>
                        </div>

                        <!-- Actions -->
                        <div class="space-y-3">
                            <button type="submit" name="status" value="draft"
                                    class="w-full px-4 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors font-medium border border-gray-200">
                                Enregistrer en brouillon
                            </button>
                            <button type="submit" name="status" value="validated"
                                    class="w-full px-4 py-3 text-white rounded-xl transition-all font-semibold shadow-lg" style="background: linear-gradient(135deg, #5680E9, #5AB9EA);">
                                Valider l'évaluation
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script nonce="<?php echo e($cspNonce ?? ''); ?>">
        const smic = <?php echo e($smic); ?>;
        const maxScore = 5.5;

        function updateCalculations() {
            let total = 0;
            document.querySelectorAll('.criteria-input').forEach(input => {
                total += parseFloat(input.value) || 0;
            });
            total = Math.min(total, maxScore);

            // Update display
            document.getElementById('totalScore').textContent = total.toFixed(1);
            const percentage = (total / maxScore) * 100;
            document.getElementById('scoreBar').style.width = percentage + '%';
            document.getElementById('scorePercentage').textContent = percentage.toFixed(1) + '%';

            // Update score color
            const scoreEl = document.getElementById('totalScore');
            if (total >= 4) {
                scoreEl.style.color = '#5680E9';
            } else if (total >= 2.5) {
                scoreEl.style.color = '#8860D0';
            } else {
                scoreEl.style.color = '#ef4444';
            }

            // Calculate salary
            let salary = total * smic;
            salary = Math.max(smic, salary);
            document.getElementById('calculatedSalary').textContent = new Intl.NumberFormat('fr-FR').format(salary) + ' FCFA';
        }

        // Sync range and number inputs
        document.querySelectorAll('.criteria-input').forEach(range => {
            const display = document.getElementById(range.id + '_display');
            
            range.addEventListener('input', function() {
                display.value = this.value;
                updateCalculations();
            });
        });

        document.querySelectorAll('.criteria-display').forEach(display => {
            const target = display.dataset.target;
            const range = document.getElementById(target);
            
            display.addEventListener('input', function() {
                const max = parseFloat(range.dataset.max);
                let value = parseFloat(this.value) || 0;
                value = Math.min(Math.max(value, 0), max);
                this.value = value;
                range.value = value;
                updateCalculations();
            });
        });

        // Initial calculation
        updateCalculations();
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
<?php /**PATH D:\ManageX\resources\views/admin/employee-evaluations/create.blade.php ENDPATH**/ ?>