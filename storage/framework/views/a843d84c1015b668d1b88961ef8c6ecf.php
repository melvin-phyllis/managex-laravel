<?php
    $isAdmin = auth()->user()->isAdmin();
    $routePrefix = $isAdmin ? 'admin.tutor.evaluations' : 'employee.tutor.evaluations';
?>

<?php if (isset($component)) { $__componentOriginal511d4862ff04963c3c16115c05a86a9d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal511d4862ff04963c3c16115c05a86a9d = $attributes; } ?>
<?php $component = Illuminate\View\DynamicComponent::resolve(['component' => $isAdmin ? 'layouts.admin' : 'layouts.employee'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dynamic-component'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\DynamicComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="space-y-6" x-data="evaluationForm()">
        <!-- Header -->
        <?php if (isset($component)) { $__componentOriginalebb3698994fa8942c93cfabfbefaa3eb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalebb3698994fa8942c93cfabfbefaa3eb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.table-header','data' => ['title' => 'Modifier l\'évaluation','subtitle' => ''.e($evaluation->week_label).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('table-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Modifier l\'évaluation','subtitle' => ''.e($evaluation->week_label).'']); ?>
             <?php $__env->slot('icon', null, []); ?> 
                <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg shadow-amber-500/20">
                    <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'edit-3','class' => 'w-6 h-6 text-white']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'edit-3','class' => 'w-6 h-6 text-white']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
                </div>
             <?php $__env->endSlot(); ?>
             <?php $__env->slot('actions', null, []); ?> 
                <a href="<?php echo e(route($routePrefix . '.index')); ?>" class="inline-flex items-center px-4 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 transition-all text-sm">
                    <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'arrow-left','class' => 'w-4 h-4 mr-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-left','class' => 'w-4 h-4 mr-2']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
                    Retour
                </a>
             <?php $__env->endSlot(); ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalebb3698994fa8942c93cfabfbefaa3eb)): ?>
<?php $attributes = $__attributesOriginalebb3698994fa8942c93cfabfbefaa3eb; ?>
<?php unset($__attributesOriginalebb3698994fa8942c93cfabfbefaa3eb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalebb3698994fa8942c93cfabfbefaa3eb)): ?>
<?php $component = $__componentOriginalebb3698994fa8942c93cfabfbefaa3eb; ?>
<?php unset($__componentOriginalebb3698994fa8942c93cfabfbefaa3eb); ?>
<?php endif; ?>

        <!-- Draft Warning -->
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 flex items-center gap-3">
            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'alert-circle','class' => 'w-5 h-5 text-amber-600']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'alert-circle','class' => 'w-5 h-5 text-amber-600']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
            <p class="text-amber-800 text-sm">
                Cette évaluation est un brouillon. Modifiez-la et soumettez-la pour qu'elle soit visible par le stagiaire.
            </p>
        </div>

        <form action="<?php echo e(route($routePrefix . '.update', $evaluation)); ?>" method="POST" class="space-y-6">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <!-- Criteria Cards -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <?php $__currentLoopData = $criteria; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $criterion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900"><?php echo e($criterion['label']); ?></h3>
                                <p class="text-sm text-gray-500"><?php echo e($criterion['description']); ?></p>
                            </div>
                            <div class="text-2xl font-bold text-violet-600" x-text="scores.<?php echo e($key); ?>.toFixed(1) + '/2.5'"></div>
                        </div>

                        <div class="mb-4">
                            <input type="range" 
                                   name="<?php echo e($key); ?>_score" 
                                   min="0" 
                                   max="2.5" 
                                   step="0.5" 
                                   x-model="scores.<?php echo e($key); ?>"
                                   value="<?php echo e(old($key.'_score', $evaluation->{$key.'_score'})); ?>"
                                   class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-violet-600">
                            <div class="flex justify-between text-xs text-gray-400 mt-1">
                                <span>0</span>
                                <span>0.5</span>
                                <span>1.0</span>
                                <span>1.5</span>
                                <span>2.0</span>
                                <span>2.5</span>
                            </div>
                        </div>

                        <textarea name="<?php echo e($key); ?>_comment" 
                                  rows="3" 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 resize-none"
                                  placeholder="Commentaire..."><?php echo e(old($key.'_comment', $evaluation->{$key.'_comment'})); ?></textarea>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <!-- Total Score Preview -->
            <div class="bg-gradient-to-r from-violet-500 to-purple-600 rounded-2xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium opacity-90">Note totale</h3>
                        <p class="text-sm opacity-75">Somme des 4 critères</p>
                    </div>
                    <div class="text-center">
                        <div class="text-5xl font-bold" x-text="totalScore.toFixed(1)"></div>
                        <div class="text-lg opacity-75">/10</div>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold" x-text="gradeLetter"></div>
                        <div class="text-sm opacity-75" x-text="gradeLabel"></div>
                    </div>
                </div>
            </div>

            <!-- General Comments -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 space-y-4">
                <h3 class="text-lg font-semibold text-gray-900">Commentaires généraux</h3>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bilan de la semaine</label>
                    <textarea name="general_comment" 
                              rows="4" 
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 resize-none"><?php echo e(old('general_comment', $evaluation->general_comment)); ?></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Objectifs pour la semaine prochaine</label>
                    <textarea name="objectives_next_week" 
                              rows="3" 
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 resize-none"><?php echo e(old('objectives_next_week', $evaluation->objectives_next_week)); ?></textarea>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <p class="text-sm text-gray-500">
                    <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'info','class' => 'w-4 h-4 inline mr-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'info','class' => 'w-4 h-4 inline mr-1']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
                    Une fois soumise, l'évaluation ne pourra plus être modifiée.
                </p>
                <div class="flex gap-3">
                    <button type="submit" name="action" value="draft" class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 transition-colors">
                        Sauvegarder brouillon
                    </button>
                    <button type="submit" name="action" value="submit" class="px-6 py-2.5 bg-violet-600 text-white rounded-xl font-medium hover:bg-violet-700 transition-colors">
                        Soumettre l'évaluation
                    </button>
                </div>
            </div>
        </form>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <script nonce="<?php echo e($cspNonce ?? ''); ?>">
        function evaluationForm() {
            return {
                scores: {
                    discipline: <?php echo e(old('discipline_score', $evaluation->discipline_score ?? 0)); ?>,
                    behavior: <?php echo e(old('behavior_score', $evaluation->behavior_score ?? 0)); ?>,
                    skills: <?php echo e(old('skills_score', $evaluation->skills_score ?? 0)); ?>,
                    communication: <?php echo e(old('communication_score', $evaluation->communication_score ?? 0)); ?>

                },
                get totalScore() {
                    return parseFloat(this.scores.discipline) + parseFloat(this.scores.behavior) + parseFloat(this.scores.skills) + parseFloat(this.scores.communication);
                },
                get gradeLetter() {
                    const score = this.totalScore;
                    if (score >= 9) return 'A';
                    if (score >= 7) return 'B';
                    if (score >= 5) return 'C';
                    if (score >= 3) return 'D';
                    return 'E';
                },
                get gradeLabel() {
                    const labels = { 'A': 'Excellent', 'B': 'Bien', 'C': 'Satisfaisant', 'D': 'À améliorer', 'E': 'Insuffisant' };
                    return labels[this.gradeLetter];
                }
            }
        }
    </script>
    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $attributes = $__attributesOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $component = $__componentOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__componentOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>

<?php /**PATH D:\ManageX\resources\views\tutor\evaluations\edit.blade.php ENDPATH**/ ?>