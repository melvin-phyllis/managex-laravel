<?php if (isset($component)) { $__componentOriginal09d149b94538c2315f503a5e890f2640 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal09d149b94538c2315f503a5e890f2640 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.employee','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.employee'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="space-y-6" x-data="{ viewMode: localStorage.getItem('employeeTaskView') || 'cards' }" x-init="$watch('viewMode', v => localStorage.setItem('employeeTaskView', v))">
        <!-- Header avec gradient -->
        <div class="relative overflow-hidden bg-gradient-to-r from-violet-600 via-purple-600 to-indigo-600 rounded-2xl p-6 text-white shadow-xl">
            <div class="absolute inset-0 bg-grid-white/10"></div>
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
            <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
            
            <div class="relative flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold mb-1">Mes Taches</h1>
                    <p class="text-violet-100">Suivez et gérez vos taches assignées</p>
                </div>
                <div class="flex items-center gap-3">
                    <!-- Toggle View -->
                    <div class="bg-white/20 backdrop-blur-sm rounded-xl p-1 flex">
                        <button @click="viewMode = 'cards'" 
                                :class="viewMode === 'cards' ? 'bg-white text-violet-700' : 'text-white hover:bg-white/20'"
                                class="px-3 py-2 rounded-lg font-medium text-sm transition-all flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                            Cartes
                        </button>
                        <button @click="viewMode = 'calendar'; $nextTick(() => initEmployeeCalendar())" 
                                :class="viewMode === 'calendar' ? 'bg-white text-violet-700' : 'text-white hover:bg-white/20'"
                                class="px-3 py-2 rounded-lg font-medium text-sm transition-all flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Calendrier
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <?php
            $totalTasks = $tasks->total();
            $pendingTasks = $tasks->where('statut', 'pending')->count() + $tasks->where('statut', 'approved')->count();
            $completedTasks = $tasks->where('statut', 'validated')->count();
            $inProgressTasks = $tasks->where('statut', 'approved')->count();
        ?>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-violet-500 to-purple-600 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900"><?php echo e($totalTasks); ?></p>
                        <p class="text-xs text-gray-500">Total taches</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900"><?php echo e($inProgressTasks); ?></p>
                        <p class="text-xs text-gray-500">En cours</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900"><?php echo e($pendingTasks); ?></p>
                        <p class="text-xs text-gray-500">En attente</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900"><?php echo e($completedTasks); ?></p>
                        <p class="text-xs text-gray-500">Validées</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-3 bg-gradient-to-r from-gray-50 to-slate-50 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filtres
                </h3>
            </div>
            <div class="p-4">
                <form action="<?php echo e(route('employee.tasks.index')); ?>" method="GET" class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                        <select name="statut" id="statut" class="w-full rounded-lg border-gray-300 focus:border-violet-500 focus:ring-violet-500">
                            <option value="">Tous les statuts</option>
                            <option value="pending" <?php echo e(request('statut') == 'pending' ? 'selected' : ''); ?>>En attente</option>
                            <option value="approved" <?php echo e(request('statut') == 'approved' ? 'selected' : ''); ?>>En cours</option>
                            <option value="completed" <?php echo e(request('statut') == 'completed' ? 'selected' : ''); ?>>Terminée (é  valider)</option>
                            <option value="validated" <?php echo e(request('statut') == 'validated' ? 'selected' : ''); ?>>Validée</option>
                            <option value="rejected" <?php echo e(request('statut') == 'rejected' ? 'selected' : ''); ?>>Rejetée</option>
                        </select>
                    </div>
                    <div class="flex-1">
                        <label for="priorite" class="block text-sm font-medium text-gray-700 mb-1">Priorité</label>
                        <select name="priorite" id="priorite" class="w-full rounded-lg border-gray-300 focus:border-violet-500 focus:ring-violet-500">
                            <option value="">Toutes les priorités</option>
                            <option value="high" <?php echo e(request('priorite') == 'high' ? 'selected' : ''); ?>>Haute</option>
                            <option value="medium" <?php echo e(request('priorite') == 'medium' ? 'selected' : ''); ?>>Moyenne</option>
                            <option value="low" <?php echo e(request('priorite') == 'low' ? 'selected' : ''); ?>>Basse</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="px-5 py-2 bg-gradient-to-r from-violet-600 to-purple-600 text-white rounded-lg hover:from-violet-700 hover:to-purple-700 transition-all shadow-sm">
                            Filtrer
                        </button>
                        <?php if(request('statut') || request('priorite')): ?>
                            <a href="<?php echo e(route('employee.tasks.index')); ?>" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                                Réinitialiser
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- Calendar View -->
        <div x-show="viewMode === 'calendar'" x-cloak class="animate-fade-in">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
                <div id="employeeTasksCalendar" class="min-h-[500px]"></div>
            </div>
        </div>

        <!-- Grille des taches (Cards View) -->
        <div x-show="viewMode === 'cards'" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            <?php $__empty_1 = true; $__currentLoopData = $tasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $priorityColors = [
                        'high' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'border' => 'border-red-200', 'dot' => 'bg-red-500'],
                        'medium' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'dot' => 'bg-amber-500'],
                        'low' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'border' => 'border-blue-200', 'dot' => 'bg-blue-500'],
                    ];
                    $priority = $priorityColors[$task->priorite] ?? $priorityColors['medium'];
                    
                    $statusConfig = [
                        'pending' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'label' => 'En attente'],
                        'approved' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'label' => 'En cours'],
                        'completed' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'label' => ' valider'],
                        'validated' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'icon' => 'M5 13l4 4L19 7', 'label' => 'Validée'],
                        'rejected' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'icon' => 'M6 18L18 6M6 6l12 12', 'label' => 'Rejetée'],
                    ];
                    $status = $statusConfig[$task->statut] ?? $statusConfig['pending'];
                ?>
                
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow group">
                    <!-- En-téªte de la carte -->
                    <div class="p-5">
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <h3 class="font-semibold text-gray-900 group-hover:text-violet-600 transition-colors line-clamp-2">
                                <?php echo e($task->titre); ?>

                            </h3>
                            <span class="flex-shrink-0 inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium <?php echo e($priority['bg']); ?> <?php echo e($priority['text']); ?> <?php echo e($priority['border']); ?> border">
                                <span class="w-1.5 h-1.5 rounded-full <?php echo e($priority['dot']); ?>"></span>
                                <?php echo e(ucfirst($task->priorite === 'high' ? 'Haute' : ($task->priorite === 'medium' ? 'Moyenne' : 'Basse'))); ?>

                            </span>
                        </div>
                        
                        <p class="text-sm text-gray-500 line-clamp-2 mb-4"><?php echo e($task->description ?? 'Aucune description disponible'); ?></p>
                        
                        <!-- Barre de progression -->
                        <div class="mb-4">
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="text-gray-500">Progression</span>
                                <span class="font-semibold <?php echo e($task->progression == 100 ? 'text-emerald-600' : 'text-violet-600'); ?>"><?php echo e($task->progression); ?>%</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="h-2 rounded-full transition-all duration-500 <?php echo e($task->progression == 100 ? 'bg-gradient-to-r from-emerald-500 to-green-500' : 'bg-gradient-to-r from-violet-500 to-purple-500'); ?>" 
                                     style="width: <?php echo e($task->progression); ?>%"></div>
                            </div>
                        </div>
                        
                        <!-- Infos -->
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium <?php echo e($status['bg']); ?> <?php echo e($status['text']); ?>">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo e($status['icon']); ?>"/>
                                </svg>
                                <?php echo e($status['label']); ?>

                            </span>
                            <?php if($task->date_fin): ?>
                                <span class="flex items-center gap-1 text-xs <?php echo e($task->date_fin->isPast() && $task->statut !== 'validated' ? 'text-red-600 font-medium' : 'text-gray-500'); ?>">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <?php echo e($task->date_fin->format('d/m/Y')); ?>

                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Zone d'action selon statut -->
                    <?php if($task->statut === 'approved'): ?>
                        <div class="px-5 py-4 bg-gradient-to-r from-violet-50 to-purple-50 border-t border-violet-100">
                            <div x-data="{ progress: <?php echo e($task->progression); ?>, saving: false, saved: false }" class="space-y-3">
                                <label class="text-sm font-medium text-violet-700">Mettre é  jour la progression</label>
                                <div class="flex items-center gap-3">
                                    <input type="range" min="0" max="100" step="5" x-model="progress" 
                                           class="flex-1 h-2 bg-violet-200 rounded-lg appearance-none cursor-pointer accent-violet-600">
                                    <span class="text-sm font-bold text-violet-700 w-12 text-right" x-text="progress + '%'"></span>
                                </div>
                                <template x-if="progress == 100">
                                    <p class="text-xs text-amber-700 bg-amber-100 p-2 rounded-lg border border-amber-200">
                                        <span class="font-semibold">A 100%</span>, la tache sera envoyée é  l'admin pour validation.
                                    </p>
                                </template>
                                <button
                                    :disabled="saving"
                                    @click="saving = true; fetch('<?php echo e(route('employee.tasks.progress', $task)); ?>', {
                                        method: 'PATCH',
                                        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'},
                                        body: JSON.stringify({progression: parseInt(progress)})
                                    }).then(r => r.json()).then(data => {
                                        saving = false;
                                        if(data.success) {
                                            if(data.statut === 'completed') {
                                                location.reload();
                                            } else {
                                                saved = true;
                                                setTimeout(() => saved = false, 2000);
                                            }
                                        }
                                    }).catch(() => { saving = false; alert('Erreur lors de la sauvegarde'); })"
                                    class="w-full px-4 py-2 bg-gradient-to-r from-violet-600 to-purple-600 text-white text-sm font-medium rounded-lg hover:from-violet-700 hover:to-purple-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-sm">
                                    <span x-show="!saving && !saved">Sauvegarder</span>
                                    <span x-show="saving" x-cloak>Enregistrement...</span>
                                    <span x-show="saved" x-cloak> Enregistré !</span>
                                </button>
                            </div>
                        </div>
                    <?php elseif($task->statut === 'completed'): ?>
                        <div class="px-5 py-3 bg-gradient-to-r from-amber-50 to-yellow-50 border-t border-amber-200">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <span class="text-sm text-amber-700 font-medium">En attente de validation admin</span>
                            </div>
                        </div>
                    <?php elseif($task->statut === 'validated'): ?>
                        <div class="px-5 py-3 bg-gradient-to-r from-emerald-50 to-green-50 border-t border-emerald-200">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <span class="text-sm text-emerald-700 font-medium">Tache validée avec succés</span>
                            </div>
                        </div>
                    <?php elseif($task->statut === 'rejected'): ?>
                        <div class="px-5 py-3 bg-gradient-to-r from-red-50 to-rose-50 border-t border-red-200">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </div>
                                <span class="text-sm text-red-700 font-medium">Tache rejetée</span>
                            </div>
                        </div>
                    <?php elseif($task->statut === 'pending'): ?>
                        <div class="px-5 py-3 bg-gradient-to-r from-gray-50 to-slate-50 border-t border-gray-200">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <span class="text-sm text-gray-600 font-medium">En attente d'approbation</span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="col-span-full">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                        <div class="w-20 h-20 bg-gradient-to-br from-violet-100 to-purple-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Aucune tache trouvée</h3>
                        <p class="text-gray-500 mb-4">Les taches vous seront assignées par l'administration</p>
                        <?php if(request('statut') || request('priorite')): ?>
                            <a href="<?php echo e(route('employee.tasks.index')); ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-violet-100 text-violet-700 rounded-lg hover:bg-violet-200 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Voir toutes les taches
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination (cards view only) -->
        <div x-show="viewMode === 'cards'">
            <?php if($tasks->hasPages()): ?>
                <div class="flex justify-center">
                    <?php echo e($tasks->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css' rel='stylesheet' />
    <script nonce="<?php echo e($cspNonce ?? ''); ?>" src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
    <script nonce="<?php echo e($cspNonce ?? ''); ?>">
        let employeeCalendarInstance = null;
        
        function initEmployeeCalendar() {
            if (employeeCalendarInstance) {
                employeeCalendarInstance.render();
                return;
            }
            
            const calendarEl = document.getElementById('employeeTasksCalendar');
            if (!calendarEl) return;
            
            const tasks = <?php echo json_encode($tasks->items(), 15, 512) ?>;
            const events = tasks.map(task => {
                const statusColors = {
                    pending: { bg: '#fbbf24', border: '#f59e0b' },
                    approved: { bg: '#3b82f6', border: '#2563eb' },
                    in_progress: { bg: '#8b5cf6', border: '#7c3aed' },
                    completed: { bg: '#a855f7', border: '#9333ea' },
                    validated: { bg: '#22c55e', border: '#16a34a' },
                    rejected: { bg: '#ef4444', border: '#dc2626' }
                };
                const colors = statusColors[task.statut] || { bg: '#6b7280', border: '#4b5563' };
                
                return {
                    id: task.id,
                    title: task.titre,
                    start: task.date_debut || task.created_at,
                    end: task.date_fin,
                    backgroundColor: colors.bg,
                    borderColor: colors.border,
                    extendedProps: {
                        status: task.statut,
                        priority: task.priorite,
                        progression: task.progression
                    }
                };
            });
            
            employeeCalendarInstance = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'fr',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,listWeek'
                },
                buttonText: {
                    today: "Aujourd'hui",
                    month: 'Mois',
                    list: 'Liste'
                },
                events: events,
                eventClick: function(info) {
                    window.location.href = '/employee/tasks/' + info.event.id;
                },
                eventDidMount: function(info) {
                    const props = info.event.extendedProps;
                    let tooltip = info.event.title;
                    if (props.progression !== undefined) tooltip += '\né°Å¸â€œÅ  ' + props.progression + '%';
                    info.el.title = tooltip;
                },
                height: 'auto',
                dayMaxEvents: 3,
                moreLinkText: function(n) { return '+' + n + ' autres'; },
                noEventsText: 'Aucune tache'
            });
            
            employeeCalendarInstance.render();
        }
        
        // Initialize if calendar view is default
        document.addEventListener('DOMContentLoaded', function() {
            if (localStorage.getItem('employeeTaskView') === 'calendar') {
                setTimeout(initEmployeeCalendar, 100);
            }
        });
    </script>
    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal09d149b94538c2315f503a5e890f2640)): ?>
<?php $attributes = $__attributesOriginal09d149b94538c2315f503a5e890f2640; ?>
<?php unset($__attributesOriginal09d149b94538c2315f503a5e890f2640); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal09d149b94538c2315f503a5e890f2640)): ?>
<?php $component = $__componentOriginal09d149b94538c2315f503a5e890f2640; ?>
<?php unset($__componentOriginal09d149b94538c2315f503a5e890f2640); ?>
<?php endif; ?>
<?php /**PATH D:\ManageX\resources\views\employee\tasks\index.blade.php ENDPATH**/ ?>