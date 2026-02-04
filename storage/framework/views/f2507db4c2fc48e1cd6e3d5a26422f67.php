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
        <?php if (isset($component)) { $__componentOriginalebb3698994fa8942c93cfabfbefaa3eb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalebb3698994fa8942c93cfabfbefaa3eb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.table-header','data' => ['title' => 'Détails du Stagiaire','subtitle' => ''.e($intern->name).'','class' => 'animate-fade-in-up']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('table-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Détails du Stagiaire','subtitle' => ''.e($intern->name).'','class' => 'animate-fade-in-up']); ?>
             <?php $__env->slot('icon', null, []); ?> 
                <div class="w-12 h-12 bg-gradient-to-br from-violet-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg shadow-violet-500/20">
                    <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'user','class' => 'w-6 h-6 text-white']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'user','class' => 'w-6 h-6 text-white']); ?>
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
                <div class="flex gap-2">
                    <a href="<?php echo e(route('admin.intern-evaluations.index')); ?>" class="inline-flex items-center px-4 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 transition-all text-sm">
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
                    <button type="button" 
                            onclick="window.dispatchEvent(new CustomEvent('start-download', { detail: { url: <?php echo \Illuminate\Support\Js::from(route('admin.intern-evaluations.export-pdf', ['intern_id' => $intern->id]))->toHtml() ?>, filename: <?php echo \Illuminate\Support\Js::from('evaluation-' . $intern->name . '.pdf')->toHtml() ?>, type: 'pdf' } }))"
                            class="inline-flex items-center px-4 py-2.5 bg-red-50 text-red-700 font-medium rounded-xl border border-red-200 hover:bg-red-100 transition-all text-sm">
                        <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'file-text','class' => 'w-4 h-4 mr-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'file-text','class' => 'w-4 h-4 mr-2']); ?>
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
                        Export PDF
                    </button>
                </div>
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

        <!-- Info Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fade-in-up animation-delay-100">
            <!-- Profil -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-16 h-16 bg-gradient-to-br from-violet-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-2xl">
                        <?php echo e(strtoupper(substr($intern->name, 0, 1))); ?>

                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900"><?php echo e($intern->name); ?></h3>
                        <p class="text-gray-500"><?php echo e($intern->email); ?></p>
                    </div>
                </div>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Département</span>
                        <span class="font-medium"><?php echo e($intern->department->name ?? 'Non assigné'); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Poste</span>
                        <span class="font-medium"><?php echo e($intern->position->name ?? 'Stagiaire'); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Tuteur</span>
                        <?php if($intern->supervisor): ?>
                            <span class="font-medium text-green-600"><?php echo e($intern->supervisor->name); ?></span>
                        <?php else: ?>
                            <span class="font-medium text-red-600">Non assigné</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Assign Supervisor Form -->
                <div class="mt-6 pt-4 border-t border-gray-100">
                    <form action="<?php echo e(route('admin.intern-evaluations.assign-supervisor', $intern)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <?php echo e($intern->supervisor ? 'Changer le tuteur' : 'Assigner un tuteur'); ?>

                        </label>
                        <div class="flex gap-2">
                            <select name="supervisor_id" class="flex-1 px-3 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                                <option value="">Sélectionner un tuteur...</option>
                                <?php $__currentLoopData = $tutors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tutor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($tutor->id); ?>" <?php echo e($intern->supervisor_id == $tutor->id ? 'selected' : ''); ?>>
                                        <?php echo e($tutor->name); ?> (<?php echo e($tutor->role === 'admin' ? 'Admin' : 'Employé'); ?><?php echo e($tutor->department ? ' - ' . $tutor->department->name : ''); ?>)
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <button type="submit" class="px-4 py-2 bg-violet-600 text-white rounded-xl hover:bg-violet-700 transition-colors text-sm font-medium">
                                Assigner
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Moyennes -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Moyennes globales</h3>
                <?php if($averages['total'] > 0): ?>
                    <div class="text-center mb-6">
                        <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-gradient-to-br from-violet-500 to-purple-600 text-white">
                            <span class="text-3xl font-bold"><?php echo e($averages['total']); ?></span>
                            <span class="text-lg">/10</span>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <?php $__currentLoopData = ['discipline' => 'Discipline', 'behavior' => 'Comportement', 'skills' => 'Compétences', 'communication' => 'Communication']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600"><?php echo e($label); ?></span>
                                    <span class="font-medium"><?php echo e($averages[$key]); ?>/2.5</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-violet-600 h-2 rounded-full" style="width: <?php echo e(($averages[$key] / 2.5) * 100); ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500 text-center py-8">Aucune évaluation soumise</p>
                <?php endif; ?>
            </div>

            <!-- Progression Chart -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Progression</h3>
                <?php if($progressionData->isNotEmpty()): ?>
                    <canvas id="progressionChart" class="w-full h-48"></canvas>
                <?php else: ?>
                    <p class="text-gray-500 text-center py-8">Pas assez de données</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Historique des évaluations -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden animate-fade-in-up animation-delay-200">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">Historique des évaluations</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Semaine</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Discipline</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Comportement</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Compétences</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Communication</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Total</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Tuteur</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php $__empty_1 = true; $__currentLoopData = $evaluations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $evaluation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <span class="font-medium text-gray-900"><?php echo e($evaluation->week_label); ?></span>
                                </td>
                                <td class="px-6 py-4 text-center"><?php echo e($evaluation->discipline_score); ?>/2.5</td>
                                <td class="px-6 py-4 text-center"><?php echo e($evaluation->behavior_score); ?>/2.5</td>
                                <td class="px-6 py-4 text-center"><?php echo e($evaluation->skills_score); ?>/2.5</td>
                                <td class="px-6 py-4 text-center"><?php echo e($evaluation->communication_score); ?>/2.5</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-sm font-semibold
                                        <?php if($evaluation->grade_letter === 'A'): ?> bg-green-100 text-green-700
                                        <?php elseif($evaluation->grade_letter === 'B'): ?> bg-blue-100 text-blue-700
                                        <?php elseif($evaluation->grade_letter === 'C'): ?> bg-yellow-100 text-yellow-700
                                        <?php elseif($evaluation->grade_letter === 'D'): ?> bg-orange-100 text-orange-700
                                        <?php else: ?> bg-red-100 text-red-700
                                        <?php endif; ?>">
                                        <?php echo e($evaluation->total_score); ?>/10
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600"><?php echo e($evaluation->tutor->name ?? 'N/A'); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    Aucune évaluation enregistrée
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <script nonce="<?php echo e($cspNonce ?? ''); ?>">
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('progressionChart');
            if (ctx) {
                const data = <?php echo json_encode($progressionData, 15, 512) ?>;
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.map(d => d.week),
                        datasets: [{
                            label: 'Score total',
                            data: data.map(d => d.score),
                            borderColor: 'rgb(139, 92, 246)',
                            backgroundColor: 'rgba(139, 92, 246, 0.1)',
                            tension: 0.3,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                min: 0,
                                max: 10
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            }
        });
    </script>
    <?php $__env->stopPush(); ?>
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
<?php /**PATH D:\ManageX\resources\views\admin\intern-evaluations\show.blade.php ENDPATH**/ ?>