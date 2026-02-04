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
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Modifier l'évaluation</h1>
                <p class="text-sm text-gray-500 mt-1"><?php echo e($employeeEvaluation->periode_label); ?></p>
            </div>
            <a href="<?php echo e(route('admin.employee-evaluations.index', ['month' => $employeeEvaluation->month, 'year' => $employeeEvaluation->year])); ?>" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour
            </a>
        </div>

        <form action="<?php echo e(route('admin.employee-evaluations.update', $employeeEvaluation)); ?>" method="POST" id="evaluationForm">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Formulaire principal -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Employé -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Employé</h3>
                        <div class="flex items-center gap-4 p-4 bg-indigo-50 rounded-lg border border-indigo-200">
                            <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-lg">
                                <?php echo e(strtoupper(substr($employeeEvaluation->user->name, 0, 2))); ?>

                            </div>
                            <div>
                                <p class="font-semibold text-gray-900"><?php echo e($employeeEvaluation->user->name); ?></p>
                                <p class="text-sm text-gray-500"><?php echo e($employeeEvaluation->user->poste ?? $employeeEvaluation->user->contract_type); ?> | <?php echo e($employeeEvaluation->user->email); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Critéres d'évaluation -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6">Critéres d'évaluation</h3>
                        
                        <div class="space-y-6">
                            <?php $__currentLoopData = $criteria; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $criterion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="p-4 bg-gray-50 rounded-lg">
                                    <div class="flex items-center justify-between mb-2">
                                        <label class="font-medium text-gray-900"><?php echo e($criterion['label']); ?></label>
                                        <span class="text-sm text-gray-500">Max: <?php echo e($criterion['max']); ?> pts</span>
                                    </div>
                                    <p class="text-sm text-gray-500 mb-3"><?php echo e($criterion['description']); ?></p>
                                    <div class="flex items-center gap-4">
                                        <input type="range" 
                                               name="<?php echo e($key); ?>" 
                                               id="<?php echo e($key); ?>"
                                               min="0" 
                                               max="<?php echo e($criterion['max']); ?>" 
                                               step="0.5"
                                               value="<?php echo e(old($key, $employeeEvaluation->{$key})); ?>"
                                               class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600 criteria-input"
                                               data-max="<?php echo e($criterion['max']); ?>">
                                        <div class="w-20 text-center">
                                            <input type="number" 
                                                   id="<?php echo e($key); ?>_display"
                                                   min="0" 
                                                   max="<?php echo e($criterion['max']); ?>" 
                                                   step="0.5"
                                                   value="<?php echo e(old($key, $employeeEvaluation->{$key})); ?>"
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
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Commentaires</h3>
                        <textarea name="comments" rows="4" 
                                  class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                  placeholder="Observations, points d'amélioration, félicitations..."><?php echo e(old('comments', $employeeEvaluation->comments)); ?></textarea>
                    </div>
                </div>

                <!-- Résumé -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Résumé</h3>
                        
                        <!-- Score total -->
                        <div class="text-center p-6 bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl mb-6">
                            <p class="text-sm text-gray-500 mb-1">Note totale</p>
                            <div class="flex items-baseline justify-center gap-1">
                                <span id="totalScore" class="text-4xl font-bold text-indigo-600"><?php echo e(number_format($employeeEvaluation->total_score, 1)); ?></span>
                                <span class="text-xl text-gray-400">/5,5</span>
                            </div>
                            <div class="mt-3 w-full bg-gray-200 rounded-full h-2">
                                <div id="scoreBar" class="h-2 rounded-full bg-indigo-600 transition-all duration-300" style="width: <?php echo e($employeeEvaluation->score_percentage); ?>%"></div>
                            </div>
                            <p id="scorePercentage" class="text-sm text-gray-500 mt-2"><?php echo e($employeeEvaluation->score_percentage); ?>%</p>
                        </div>

                        <!-- Salaire calculé -->
                        <div class="text-center p-6 bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl mb-6">
                            <p class="text-sm text-gray-500 mb-1">Salaire brut calculé</p>
                            <p id="calculatedSalary" class="text-3xl font-bold text-green-600"><?php echo e($employeeEvaluation->calculated_salary_formatted); ?></p>
                            <p class="text-xs text-gray-500 mt-2">SMIC minimum garanti : <?php echo e(number_format($smic, 0, ',', ' ')); ?> FCFA</p>
                        </div>

                        <!-- Actions -->
                        <div class="space-y-3">
                            <button type="submit" name="status" value="draft"
                                    class="w-full px-4 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                                Enregistrer
                            </button>
                            <button type="submit" name="status" value="validated"
                                    class="w-full px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
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

            document.getElementById('totalScore').textContent = total.toFixed(1);
            const percentage = (total / maxScore) * 100;
            document.getElementById('scoreBar').style.width = percentage + '%';
            document.getElementById('scorePercentage').textContent = percentage.toFixed(1) + '%';

            const scoreEl = document.getElementById('totalScore');
            scoreEl.classList.remove('text-green-600', 'text-yellow-600', 'text-red-600', 'text-indigo-600');
            if (total >= 4) {
                scoreEl.classList.add('text-green-600');
            } else if (total >= 2.5) {
                scoreEl.classList.add('text-yellow-600');
            } else {
                scoreEl.classList.add('text-red-600');
            }

            let salary = total * smic;
            salary = Math.max(smic, salary);
            document.getElementById('calculatedSalary').textContent = new Intl.NumberFormat('fr-FR').format(salary) + ' FCFA';
        }

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
<?php /**PATH D:\ManageX\resources\views\admin\employee-evaluations\edit.blade.php ENDPATH**/ ?>