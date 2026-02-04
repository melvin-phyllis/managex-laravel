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
    <div class="space-y-6">
        <!-- Flash Messages -->
        <?php if(session('success')): ?>
            <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-lg animate-fade-in" x-data="{ show: true }" x-show="show" x-transition>
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-sm font-medium text-emerald-800">Succès</h3>
                        <p class="mt-1 text-sm text-emerald-700"><?php echo e(session('success')); ?></p>
                    </div>
                    <button @click="show = false" class="ml-3 text-emerald-400 hover:text-emerald-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        <?php endif; ?>

        <!-- Urgent Announcements Banner -->
        <?php if(isset($urgentAnnouncements) && $urgentAnnouncements->count() > 0): ?>
            <div class="space-y-3">
                <?php $__currentLoopData = $urgentAnnouncements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $announcement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="relative overflow-hidden rounded-2xl shadow-lg
                        <?php if($announcement->type === 'urgent' || $announcement->priority === 'critical'): ?>
                            bg-gradient-to-r from-red-500 to-rose-600
                        <?php else: ?>
                            bg-gradient-to-r from-amber-500 to-orange-500
                        <?php endif; ?>
                        text-white p-4"
                        x-data="{ dismissed: false }" 
                        x-show="!dismissed"
                        x-transition>
                        
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur">
                                <span class="text-2xl"><?php echo e($announcement->type_icon); ?></span>
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <?php if($announcement->is_pinned): ?>
                                        <span>📌</span>
                                    <?php endif; ?>
                                    <h3 class="font-bold text-lg"><?php echo e($announcement->title); ?></h3>
                                    <?php if($announcement->priority === 'critical'): ?>
                                        <span class="px-2 py-0.5 text-xs font-medium bg-white/20 rounded-full">Critique</span>
                                    <?php endif; ?>
                                </div>
                                <p class="text-white/90 text-sm line-clamp-2">
                                    <?php echo e(Str::limit(strip_tags($announcement->content), 150)); ?>

                                </p>
                                <div class="flex items-center gap-4 mt-3">
                                    <a href="<?php echo e(route('employee.announcements.show', $announcement)); ?>" 
                                       class="text-sm font-medium underline hover:no-underline">
                                        Lire la suite →
                                    </a>
                                    <?php if($announcement->requires_acknowledgment): ?>
                                        <button onclick="acknowledgeAnnouncement(<?php echo e($announcement->id); ?>, this)"
                                                class="px-3 py-1 bg-white text-red-600 text-sm font-medium rounded-lg hover:bg-white/90 transition">
                                            ✓ J'ai pris connaissance
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <?php if(!$announcement->requires_acknowledgment): ?>
                                <button @click="dismissed = true; markAnnouncementRead(<?php echo e($announcement->id); ?>)"
                                        class="text-white/70 hover:text-white p-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>

        <!-- Header with Streak -->
        <div class="space-y-4 animate-fade-in-up">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-500/30">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Bienvenue, <?php echo e(auth()->user()->name); ?></h1>
                    <p class="text-gray-500"><?php echo e(now()->translatedFormat('l d F Y')); ?></p>
                </div>
            </div>

            <!-- Streak Counter - Full Width -->
            <?php if($streakData['current'] > 0 || $streakData['best'] > 0): ?>
                <?php if (isset($component)) { $__componentOriginal5567d31f21943183f0eb71c9a22bff23 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5567d31f21943183f0eb71c9a22bff23 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.streak-counter','data' => ['currentStreak' => $streakData['current'],'bestStreak' => $streakData['best'],'lastPresenceDate' => $streakData['last_date']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('streak-counter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['currentStreak' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($streakData['current']),'bestStreak' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($streakData['best']),'lastPresenceDate' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($streakData['last_date'])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5567d31f21943183f0eb71c9a22bff23)): ?>
<?php $attributes = $__attributesOriginal5567d31f21943183f0eb71c9a22bff23; ?>
<?php unset($__attributesOriginal5567d31f21943183f0eb71c9a22bff23); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5567d31f21943183f0eb71c9a22bff23)): ?>
<?php $component = $__componentOriginal5567d31f21943183f0eb71c9a22bff23; ?>
<?php unset($__componentOriginal5567d31f21943183f0eb71c9a22bff23); ?>
<?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 animate-fade-in-up animation-delay-100">
            <!-- Présences -->
            <a href="<?php echo e(route('employee.presences.index')); ?>" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 hover:shadow-md hover:border-emerald-200 transition-all group">
                <div class="flex items-center justify-between">
                    <div class="bg-gradient-to-br from-emerald-500 to-green-600 p-2.5 rounded-xl shadow-lg shadow-emerald-500/30 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <svg class="w-5 h-5 text-gray-300 group-hover:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-gray-900 mt-3"><?php echo e($stats['presences_month']); ?></p>
                <p class="text-xs text-gray-500">Présences ce mois</p>
                <p class="text-xs text-emerald-600 font-medium mt-1"><?php echo e($stats['heures_month']); ?>h travaillées</p>
            </a>

            <!-- Tâches -->
            <a href="<?php echo e(route('employee.tasks.index')); ?>" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 hover:shadow-md hover:border-blue-200 transition-all group">
                <div class="flex items-center justify-between">
                    <div class="bg-gradient-to-br from-blue-500 to-indigo-600 p-2.5 rounded-xl shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <svg class="w-5 h-5 text-gray-300 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-gray-900 mt-3"><?php echo e($stats['active_tasks']); ?></p>
                <p class="text-xs text-gray-500">Tâches en cours</p>
                <p class="text-xs text-blue-600 font-medium mt-1"><?php echo e($stats['tasks_completed']); ?> terminées</p>
            </a>

            <!-- Congés -->
            <a href="<?php echo e(route('employee.leaves.index')); ?>" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 hover:shadow-md hover:border-violet-200 transition-all group">
                <div class="flex items-center justify-between">
                    <div class="bg-gradient-to-br from-violet-500 to-purple-600 p-2.5 rounded-xl shadow-lg shadow-violet-500/30 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <svg class="w-5 h-5 text-gray-300 group-hover:text-violet-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-gray-900 mt-3"><?php echo e($stats['leave_days_remaining']); ?></p>
                <p class="text-xs text-gray-500">Congés restants</p>
                <p class="text-xs text-violet-600 font-medium mt-1">jours disponibles</p>
            </a>

            <!-- Sondages -->
            <a href="<?php echo e(route('employee.surveys.index')); ?>" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 hover:shadow-md hover:border-amber-200 transition-all group">
                <div class="flex items-center justify-between">
                    <div class="bg-gradient-to-br from-amber-500 to-orange-600 p-2.5 rounded-xl shadow-lg shadow-amber-500/30 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <svg class="w-5 h-5 text-gray-300 group-hover:text-amber-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-gray-900 mt-3"><?php echo e($stats['pending_surveys']); ?></p>
                <p class="text-xs text-gray-500">Sondages</p>
                <p class="text-xs text-amber-600 font-medium mt-1">à compléter</p>
            </a>
        </div>

        <!-- Presence Check-in/out & Quick Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fade-in-up animation-delay-200">
            <!-- Presence Check-in/out -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/30">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-semibold text-gray-900">Pointage du jour</h2>
                            <p class="text-sm text-gray-500"><?php echo e(now()->translatedFormat('l d F')); ?></p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <?php if($todayPresence): ?>
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="flex items-center space-x-6">
                                <div class="text-center">
                                    <p class="text-xs text-gray-500 uppercase tracking-wide">Arrivée</p>
                                    <p class="text-2xl font-bold text-emerald-600 mt-1"><?php echo e($todayPresence->check_in->format('H:i')); ?></p>
                                </div>
                                <?php if($todayPresence->check_out): ?>
                                    <div class="text-center">
                                        <p class="text-xs text-gray-500 uppercase tracking-wide">Départ</p>
                                        <p class="text-2xl font-bold text-red-600 mt-1"><?php echo e($todayPresence->check_out->format('H:i')); ?></p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-xs text-gray-500 uppercase tracking-wide">Durée</p>
                                        <p class="text-2xl font-bold text-gray-900 mt-1"><?php echo e($todayPresence->hours_worked); ?></p>
                                    </div>
                                <?php else: ?>
                                    <div class="flex items-center">
                                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-emerald-100 text-emerald-800">
                                            <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2 animate-pulse"></span>
                                            En cours
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php if(!$todayPresence->check_out): ?>
                                <form id="dashboardCheckOutForm" action="<?php echo e(route('employee.presences.check-out')); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="latitude" id="dashboardCheckOutLat">
                                    <input type="hidden" name="longitude" id="dashboardCheckOutLng">
                                    <button type="button" id="dashboardCheckOutBtn"
                                            class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-red-500 to-rose-600 text-white font-medium rounded-xl hover:from-red-600 hover:to-rose-700 transition-all duration-200 flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-red-500/30 hover:shadow-xl touch-target">
                                        <svg id="dashboardCheckOutIcon" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        <span id="dashboardCheckOutText">Pointer la sortie</span>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">Pas encore pointé</p>
                                    <p class="text-sm text-gray-500">Pointez pour commencer votre journée</p>
                                </div>
                            </div>
                            <form id="dashboardCheckInForm" action="<?php echo e(route('employee.presences.check-in')); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="latitude" id="dashboardCheckInLat">
                                <input type="hidden" name="longitude" id="dashboardCheckInLng">
                                <button type="button" id="dashboardCheckInBtn"
                                        class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-emerald-500 to-green-600 text-white font-medium rounded-xl hover:from-emerald-600 hover:to-green-700 transition-all duration-200 flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-emerald-500/30 hover:shadow-xl touch-target">
                                    <svg id="dashboardCheckInIcon" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                    </svg>
                                    <span id="dashboardCheckInText">Pointer l'arrivée</span>
                                </button>
                            </form>
                        </div>

                        <!-- Message d'erreur géolocalisation -->
                        <div id="dashboardGeoError" class="mt-4 hidden bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg animate-fade-in">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800" id="dashboardGeoErrorTitle">Géolocalisation requise</h3>
                                    <p class="mt-1 text-sm text-red-700" id="dashboardGeoErrorMessage">Vous devez activer la géolocalisation pour pointer.</p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Actions -->
            <?php if (isset($component)) { $__componentOriginal91a7b2ce618042393230d5f6675b0567 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal91a7b2ce618042393230d5f6675b0567 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.quick-actions','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('quick-actions'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal91a7b2ce618042393230d5f6675b0567)): ?>
<?php $attributes = $__attributesOriginal91a7b2ce618042393230d5f6675b0567; ?>
<?php unset($__attributesOriginal91a7b2ce618042393230d5f6675b0567); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal91a7b2ce618042393230d5f6675b0567)): ?>
<?php $component = $__componentOriginal91a7b2ce618042393230d5f6675b0567; ?>
<?php unset($__componentOriginal91a7b2ce618042393230d5f6675b0567); ?>
<?php endif; ?>
        </div>

        <!-- Monthly Goals -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden animate-fade-in-up">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-violet-50 to-purple-50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center shadow-lg shadow-violet-500/30">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-semibold text-gray-900">Objectifs du mois</h2>
                        <p class="text-sm text-gray-500">Votre progression mensuelle</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-8">
                    <div class="text-center">
                        <?php if (isset($component)) { $__componentOriginal964f412edd06b1e9f7f056b84f94533f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal964f412edd06b1e9f7f056b84f94533f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.circular-progress','data' => ['value' => $monthlyGoals['presence']['current'],'max' => $monthlyGoals['presence']['target'],'color' => 'auto','size' => 'lg']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('circular-progress'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($monthlyGoals['presence']['current']),'max' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($monthlyGoals['presence']['target']),'color' => 'auto','size' => 'lg']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal964f412edd06b1e9f7f056b84f94533f)): ?>
<?php $attributes = $__attributesOriginal964f412edd06b1e9f7f056b84f94533f; ?>
<?php unset($__attributesOriginal964f412edd06b1e9f7f056b84f94533f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal964f412edd06b1e9f7f056b84f94533f)): ?>
<?php $component = $__componentOriginal964f412edd06b1e9f7f056b84f94533f; ?>
<?php unset($__componentOriginal964f412edd06b1e9f7f056b84f94533f); ?>
<?php endif; ?>
                        <p class="mt-3 font-medium text-gray-900">Présences</p>
                        <p class="text-sm text-gray-500"><?php echo e($monthlyGoals['presence']['current']); ?>/<?php echo e($monthlyGoals['presence']['total']); ?> jours</p>
                    </div>
                    <div class="text-center">
                        <?php if (isset($component)) { $__componentOriginal964f412edd06b1e9f7f056b84f94533f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal964f412edd06b1e9f7f056b84f94533f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.circular-progress','data' => ['value' => $monthlyGoals['hours']['current'],'max' => $monthlyGoals['hours']['expected'],'color' => 'auto','size' => 'lg']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('circular-progress'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($monthlyGoals['hours']['current']),'max' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($monthlyGoals['hours']['expected']),'color' => 'auto','size' => 'lg']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal964f412edd06b1e9f7f056b84f94533f)): ?>
<?php $attributes = $__attributesOriginal964f412edd06b1e9f7f056b84f94533f; ?>
<?php unset($__attributesOriginal964f412edd06b1e9f7f056b84f94533f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal964f412edd06b1e9f7f056b84f94533f)): ?>
<?php $component = $__componentOriginal964f412edd06b1e9f7f056b84f94533f; ?>
<?php unset($__componentOriginal964f412edd06b1e9f7f056b84f94533f); ?>
<?php endif; ?>
                        <p class="mt-3 font-medium text-gray-900">Heures</p>
                        <p class="text-sm text-gray-500"><?php echo e($monthlyGoals['hours']['current']); ?>/<?php echo e($monthlyGoals['hours']['target']); ?>h</p>
                    </div>
                    <div class="text-center">
                        <?php if (isset($component)) { $__componentOriginal964f412edd06b1e9f7f056b84f94533f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal964f412edd06b1e9f7f056b84f94533f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.circular-progress','data' => ['value' => $monthlyGoals['tasks']['completed'],'max' => max($monthlyGoals['tasks']['assigned'], 1),'color' => 'auto','size' => 'lg']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('circular-progress'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($monthlyGoals['tasks']['completed']),'max' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(max($monthlyGoals['tasks']['assigned'], 1)),'color' => 'auto','size' => 'lg']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal964f412edd06b1e9f7f056b84f94533f)): ?>
<?php $attributes = $__attributesOriginal964f412edd06b1e9f7f056b84f94533f; ?>
<?php unset($__attributesOriginal964f412edd06b1e9f7f056b84f94533f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal964f412edd06b1e9f7f056b84f94533f)): ?>
<?php $component = $__componentOriginal964f412edd06b1e9f7f056b84f94533f; ?>
<?php unset($__componentOriginal964f412edd06b1e9f7f056b84f94533f); ?>
<?php endif; ?>
                        <p class="mt-3 font-medium text-gray-900">Tâches</p>
                        <p class="text-sm text-gray-500"><?php echo e($monthlyGoals['tasks']['completed']); ?>/<?php echo e($monthlyGoals['tasks']['assigned']); ?> terminées</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Documents Quick Access Row -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Mes Demandes de Documents -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow animate-fade-in-up">
                <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-amber-50 to-orange-50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center shadow-lg shadow-amber-500/30">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Mes Demandes</h3>
                            <p class="text-xs text-gray-500">Attestations & certificats</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-5">
                    <?php if($documentRequests->count() > 0): ?>
                        <div class="space-y-2 mb-4">
                            <?php $__currentLoopData = $documentRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="flex items-center justify-between p-2 rounded-lg bg-gray-50">
                                    <div class="flex items-center gap-2">
                                        <?php if($request->status === 'approved'): ?>
                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                        <?php elseif($request->status === 'rejected'): ?>
                                            <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                        <?php else: ?>
                                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                        <?php endif; ?>
                                        <span class="text-sm text-gray-700 truncate"><?php echo e(Str::limit($request->type_label, 18)); ?></span>
                                    </div>
                                    <?php if($request->hasDocument()): ?>
                                        <a href="<?php echo e(route('employee.document-requests.download', $request)); ?>" 
                                           class="text-amber-600 hover:text-amber-800">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-xs px-2 py-0.5 rounded-full 
                                            <?php if($request->status === 'approved'): ?> bg-emerald-100 text-emerald-700
                                            <?php elseif($request->status === 'rejected'): ?> bg-red-100 text-red-700
                                            <?php else: ?> bg-amber-100 text-amber-700 <?php endif; ?>">
                                            <?php echo e($request->status_label); ?>

                                        </span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <p class="text-sm text-gray-400 mb-4">Aucune demande récente</p>
                    <?php endif; ?>
                    
                    <a href="<?php echo e(route('employee.document-requests.create')); ?>" 
                       class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-gradient-to-r from-amber-500 to-orange-600 text-white text-sm font-medium rounded-xl hover:from-amber-600 hover:to-orange-700 transition-all shadow-lg shadow-amber-500/30">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Nouvelle demande
                    </a>
                </div>
            </div>

            <!-- Documents à Consulter -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow animate-fade-in-up">
                <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-cyan-50 to-teal-50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-cyan-500 to-teal-600 flex items-center justify-center shadow-lg shadow-cyan-500/30">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Documents Entreprise</h3>
                            <p class="text-xs text-gray-500">Règlements & chartes</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-5">
                    <?php if($unreadGlobalDocs->count() > 0): ?>
                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 mb-4">
                            <div class="flex items-center gap-2 text-amber-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <span class="text-sm font-medium"><?php echo e($unreadGlobalDocs->count()); ?> document(s) à consulter</span>
                            </div>
                            <ul class="mt-2 space-y-1">
                                <?php $__currentLoopData = $unreadGlobalDocs->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="text-sm text-amber-600 flex items-center gap-1">
                                        <span>•</span>
                                        <span class="truncate"><?php echo e(Str::limit($doc->title, 25)); ?></span>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php else: ?>
                        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-3 mb-4">
                            <div class="flex items-center gap-2 text-emerald-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span class="text-sm font-medium">Tous les documents lus</span>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <a href="<?php echo e(route('employee.documents.index')); ?>" 
                       class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-gradient-to-r from-cyan-500 to-teal-600 text-white text-sm font-medium rounded-xl hover:from-cyan-600 hover:to-teal-700 transition-all shadow-lg shadow-cyan-500/30">
                        Consulter
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Mon Contrat -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow animate-fade-in-up">
                <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-emerald-50 to-green-50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-green-600 flex items-center justify-center shadow-lg shadow-emerald-500/30">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Mon Contrat</h3>
                            <p class="text-xs text-gray-500">Document de travail</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-5">
                    <?php if($contract): ?>
                        <div class="space-y-2 mb-4">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">Type</span>
                                <span class="font-medium text-gray-900"><?php echo e($contract->type ?? 'CDI'); ?></span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">Depuis</span>
                                <span class="font-medium text-gray-900"><?php echo e($contract->start_date?->format('d/m/Y') ?? '-'); ?></span>
                            </div>
                        </div>
                        
                        <?php if($hasContractDocument): ?>
                            <a href="<?php echo e(route('employee.documents.download-contract')); ?>" 
                               class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-green-600 text-white text-sm font-medium rounded-xl hover:from-emerald-600 hover:to-green-700 transition-all shadow-lg shadow-emerald-500/30">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Télécharger
                            </a>
                        <?php else: ?>
                            <div class="text-center py-2 text-sm text-gray-400">
                                Document non disponible
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <div class="w-12 h-12 mx-auto bg-gray-100 rounded-xl flex items-center justify-center mb-2">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <p class="text-sm text-gray-400">Aucun contrat trouvé</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Weekly Hours Chart -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden animate-fade-in-up flex flex-col">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-emerald-50 to-green-50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-green-600 flex items-center justify-center shadow-lg shadow-emerald-500/30">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Heures cette semaine</h3>
                            <p class="text-sm text-gray-500">Vos heures de travail quotidiennes</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 flex-1 flex items-center">
                    <div class="w-full h-72">
                        <canvas id="weeklyHoursChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Tasks Distribution Chart -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden animate-fade-in-up flex flex-col">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/30">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Mes tâches par statut</h3>
                            <p class="text-sm text-gray-500">Répartition de vos tâches</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 flex-1 flex items-center justify-center">
                    <div class="w-full max-w-sm h-72">
                        <canvas id="tasksChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tasks & Events Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Tasks -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden animate-fade-in-up">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-green-50 to-emerald-50 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center shadow-lg shadow-green-500/30">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900">Mes tâches en cours</h3>
                    </div>
                    <a href="<?php echo e(route('employee.tasks.index')); ?>" class="text-sm text-green-600 hover:text-green-800 font-medium">Voir tout</a>
                </div>
                <div class="divide-y divide-gray-50">
                    <?php $__empty_1 = true; $__currentLoopData = $recentTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <a href="<?php echo e(route('employee.tasks.show', $task)); ?>"
                           class="block p-4 hover:bg-gray-50 transition-colors"
                           style="animation-delay: <?php echo e($index * 50); ?>ms">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-medium text-gray-900"><?php echo e(Str::limit($task->titre, 30)); ?></span>
                                <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['status' => $task->priorite,'type' => 'priority']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($task->priorite),'type' => 'priority']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $attributes = $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $component = $__componentOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
                            </div>
                            <?php if (isset($component)) { $__componentOriginalc1838dab69175fa625a76ca35492c358 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc1838dab69175fa625a76ca35492c358 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.progress-bar','data' => ['value' => $task->progression,'size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('progress-bar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($task->progression),'size' => 'sm']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc1838dab69175fa625a76ca35492c358)): ?>
<?php $attributes = $__attributesOriginalc1838dab69175fa625a76ca35492c358; ?>
<?php unset($__attributesOriginalc1838dab69175fa625a76ca35492c358); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc1838dab69175fa625a76ca35492c358)): ?>
<?php $component = $__componentOriginalc1838dab69175fa625a76ca35492c358; ?>
<?php unset($__componentOriginalc1838dab69175fa625a76ca35492c358); ?>
<?php endif; ?>
                            <?php if($task->date_fin): ?>
                                <p class="text-xs text-gray-400 mt-2">
                                    Échéance: <?php echo e($task->date_fin->format('d/m/Y')); ?>

                                    <?php if($task->date_fin->isPast()): ?>
                                        <span class="text-red-500 font-medium">(En retard)</span>
                                    <?php elseif($task->date_fin->isToday()): ?>
                                        <span class="text-yellow-500 font-medium">(Aujourd'hui)</span>
                                    <?php endif; ?>
                                </p>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="p-8 text-center">
                            <div class="w-12 h-12 mx-auto bg-gray-100 rounded-xl flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <p class="text-gray-500">Aucune tâche en cours</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Upcoming Events -->
            <?php if (isset($component)) { $__componentOriginal91ae4ca5277cdec5192a2d8229bbe61b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal91ae4ca5277cdec5192a2d8229bbe61b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.upcoming-events','data' => ['events' => $upcomingEvents,'maxItems' => 5]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('upcoming-events'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['events' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($upcomingEvents),'maxItems' => 5]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal91ae4ca5277cdec5192a2d8229bbe61b)): ?>
<?php $attributes = $__attributesOriginal91ae4ca5277cdec5192a2d8229bbe61b; ?>
<?php unset($__attributesOriginal91ae4ca5277cdec5192a2d8229bbe61b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal91ae4ca5277cdec5192a2d8229bbe61b)): ?>
<?php $component = $__componentOriginal91ae4ca5277cdec5192a2d8229bbe61b; ?>
<?php unset($__componentOriginal91ae4ca5277cdec5192a2d8229bbe61b); ?>
<?php endif; ?>
        </div>

        <!-- Surveys & Notifications Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Pending Surveys -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden animate-fade-in-up">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg shadow-indigo-500/30">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900">Sondages à compléter</h3>
                    </div>
                    <a href="<?php echo e(route('employee.surveys.index')); ?>" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">Voir tout</a>
                </div>
                <div class="divide-y divide-gray-50">
                    <?php $__empty_1 = true; $__currentLoopData = $pendingSurveys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $survey): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <a href="<?php echo e(route('employee.surveys.show', $survey)); ?>" class="block p-4 hover:bg-gray-50 transition-colors group">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center group-hover:bg-indigo-200 transition-colors">
                                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 group-hover:text-indigo-600 transition-colors"><?php echo e($survey->titre); ?></p>
                                        <p class="text-sm text-gray-500"><?php echo e($survey->questions->count()); ?> question(s)</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <?php if($survey->date_limite): ?>
                                        <span class="text-xs px-2 py-1 rounded-full <?php echo e($survey->date_limite->isPast() ? 'bg-red-100 text-red-600' : ($survey->date_limite->diffInDays(now()) <= 3 ? 'bg-yellow-100 text-yellow-600' : 'bg-gray-100 text-gray-600')); ?>">
                                            <?php echo e($survey->date_limite->format('d/m')); ?>

                                        </span>
                                    <?php endif; ?>
                                    <svg class="w-5 h-5 text-gray-400 group-hover:text-indigo-600 mt-1 ml-auto transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="p-8 text-center">
                            <div class="w-12 h-12 mx-auto bg-emerald-100 rounded-xl flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <p class="text-gray-500">Tous les sondages sont complétés</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Notifications -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden animate-fade-in-up">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-rose-50 to-pink-50 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-rose-500 to-pink-600 flex items-center justify-center shadow-lg shadow-rose-500/30">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900">Notifications récentes</h3>
                    </div>
                    <?php if($recentNotifications->count() > 0): ?>
                        <form action="<?php echo e(route('employee.notifications.read-all')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="text-sm text-rose-500 hover:text-rose-700 font-medium">Tout marquer comme lu</button>
                        </form>
                    <?php endif; ?>
                </div>
                <div class="divide-y divide-gray-50 max-h-80 overflow-y-auto scrollbar-thin">
                    <?php $__empty_1 = true; $__currentLoopData = $recentNotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $notifData = $notification->data;
                            $notifType = $notifData['type'] ?? 'default';
                            $notifUrl = $notifData['url'] ?? '#';
                            
                            // Get message with fallback
                            $notifMessage = $notifData['message'] ?? match($notifType) {
                                'leave_request' => '📅 Nouvelle demande de congé',
                                'leave_status' => ($notifData['status'] ?? '') === 'approved' ? '✅ Congé approuvé' : '❌ Congé refusé',
                                'task_assigned' => '📋 Nouvelle tâche : ' . ($notifData['task_title'] ?? $notifData['titre'] ?? ''),
                                'task_status' => '📋 Tâche mise à jour',
                                'task_reminder' => '⏰ Rappel tâche',
                                'new_message' => '💬 Message de ' . ($notifData['sender_name'] ?? 'quelqu\'un'),
                                'payroll_added' => '💰 Fiche de paie disponible',
                                'new_survey' => '📊 Nouveau sondage',
                                'new_evaluation' => '📝 Nouvelle évaluation',
                                'welcome' => '👋 Bienvenue !',
                                default => 'Nouvelle notification'
                            };
                            
                            // Icon classes by type
                            $iconClass = match($notifType) {
                                'leave_request' => 'bg-blue-100 text-blue-600',
                                'leave_status' => 'bg-green-100 text-green-600',
                                'task_assigned', 'task_status' => 'bg-purple-100 text-purple-600',
                                'task_reminder' => 'bg-orange-100 text-orange-600',
                                'new_message' => 'bg-indigo-100 text-indigo-600',
                                'payroll_added' => 'bg-emerald-100 text-emerald-600',
                                'new_survey' => 'bg-cyan-100 text-cyan-600',
                                'new_evaluation' => 'bg-amber-100 text-amber-600',
                                'welcome' => 'bg-green-100 text-green-600',
                                default => 'bg-gray-100 text-gray-600'
                            };
                        ?>
                        <a href="<?php echo e($notifUrl); ?>" class="block p-4 <?php echo e($notification->read_at ? 'opacity-60' : ''); ?> hover:bg-gray-50 transition-colors">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full <?php echo e($iconClass); ?> flex items-center justify-center">
                                    <?php if($notifType === 'leave_request' || $notifType === 'leave_status'): ?>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <?php elseif($notifType === 'task_assigned' || $notifType === 'task_status' || $notifType === 'task_reminder'): ?>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                    <?php elseif($notifType === 'new_message'): ?>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                    <?php elseif($notifType === 'payroll_added'): ?>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <?php elseif($notifType === 'new_survey'): ?>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                    <?php elseif($notifType === 'new_evaluation'): ?>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                    <?php else: ?>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-900 <?php echo e(!$notification->read_at ? 'font-medium' : ''); ?>"><?php echo e($notifMessage); ?></p>
                                    <p class="text-xs text-gray-500 mt-1"><?php echo e($notification->created_at->diffForHumans()); ?></p>
                                </div>
                                <?php if(!$notification->read_at): ?>
                                    <div class="flex-shrink-0 w-2 h-2 rounded-full bg-rose-500 animate-pulse"></div>
                                <?php endif; ?>
                            </div>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="p-8 text-center">
                            <div class="w-12 h-12 mx-auto bg-gray-100 rounded-xl flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                            </div>
                            <p class="text-gray-500">Aucune notification</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <script nonce="<?php echo e($cspNonce ?? ''); ?>">
        // Chart Data
        const chartData = <?php echo json_encode($chartData, 15, 512) ?>;

        // Weekly Hours Chart
        new Chart(document.getElementById('weeklyHoursChart'), {
            type: 'bar',
            data: {
                labels: chartData.weekly_hours.labels,
                datasets: [{
                    label: 'Heures',
                    data: chartData.weekly_hours.data,
                    backgroundColor: 'rgba(16, 185, 129, 0.8)',
                    borderRadius: 8,
                    borderSkipped: false,
                    hoverBackgroundColor: '#10B981'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(17, 24, 39, 0.9)',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y + 'h travaillées';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 10,
                        grid: { color: 'rgba(0, 0, 0, 0.05)' },
                        ticks: {
                            callback: function(value) {
                                return value + 'h';
                            }
                        }
                    },
                    x: { grid: { display: false } }
                }
            }
        });

        // Tasks Distribution Chart
        new Chart(document.getElementById('tasksChart'), {
            type: 'doughnut',
            data: {
                labels: chartData.tasks_by_status.labels,
                datasets: [{
                    data: chartData.tasks_by_status.data,
                    backgroundColor: chartData.tasks_by_status.colors,
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: { size: 12 }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(17, 24, 39, 0.9)',
                        padding: 12,
                        cornerRadius: 8
                    }
                }
            }
        });

        // Geolocation handling
        document.addEventListener('DOMContentLoaded', function() {
            const geoError = document.getElementById('dashboardGeoError');
            const geoErrorTitle = document.getElementById('dashboardGeoErrorTitle');
            const geoErrorMessage = document.getElementById('dashboardGeoErrorMessage');

            const checkInBtn = document.getElementById('dashboardCheckInBtn');
            const checkInForm = document.getElementById('dashboardCheckInForm');
            const checkInLat = document.getElementById('dashboardCheckInLat');
            const checkInLng = document.getElementById('dashboardCheckInLng');
            const checkInText = document.getElementById('dashboardCheckInText');
            const checkInIcon = document.getElementById('dashboardCheckInIcon');

            const checkOutBtn = document.getElementById('dashboardCheckOutBtn');
            const checkOutForm = document.getElementById('dashboardCheckOutForm');
            const checkOutLat = document.getElementById('dashboardCheckOutLat');
            const checkOutLng = document.getElementById('dashboardCheckOutLng');
            const checkOutText = document.getElementById('dashboardCheckOutText');
            const checkOutIcon = document.getElementById('dashboardCheckOutIcon');

            const spinnerSVG = `<svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>`;

            function showError(title, message) {
                if (geoError) {
                    geoError.classList.remove('hidden');
                    geoErrorTitle.textContent = title;
                    geoErrorMessage.textContent = message;
                }
            }

            function hideError() {
                if (geoError) geoError.classList.add('hidden');
            }

            function handlePointage(btn, form, latInput, lngInput, textEl, iconEl, originalText, originalIconHTML) {
                if (!navigator.geolocation) {
                    showError('Navigateur non compatible', 'Votre navigateur ne supporte pas la géolocalisation.');
                    return;
                }

                btn.disabled = true;
                textEl.textContent = 'Localisation...';
                iconEl.outerHTML = spinnerSVG;
                hideError();

                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        latInput.value = position.coords.latitude;
                        lngInput.value = position.coords.longitude;
                        textEl.textContent = 'Envoi...';
                        form.submit();
                    },
                    function(error) {
                        btn.disabled = false;
                        textEl.textContent = originalText;
                        const newIcon = document.createElement('svg');
                        newIcon.id = iconEl.id;
                        newIcon.className = 'w-5 h-5 mr-2';
                        newIcon.setAttribute('fill', 'none');
                        newIcon.setAttribute('stroke', 'currentColor');
                        newIcon.setAttribute('viewBox', '0 0 24 24');
                        newIcon.innerHTML = originalIconHTML;
                        btn.insertBefore(newIcon, btn.firstChild);

                        const messages = {
                            1: ['Géolocalisation refusée', 'Veuillez autoriser l\'accès à votre position.'],
                            2: ['Position indisponible', 'Vérifiez que le GPS est activé.'],
                            3: ['Délai dépassé', 'La recherche de position a pris trop de temps.']
                        };
                        const [title, msg] = messages[error.code] || ['Erreur', 'Une erreur s\'est produite.'];
                        showError(title, msg);
                    },
                    { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
                );
            }

            const checkInOriginalIcon = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>`;
            const checkOutOriginalIcon = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>`;

            if (checkInBtn) {
                checkInBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    handlePointage(checkInBtn, checkInForm, checkInLat, checkInLng, checkInText, checkInIcon, 'Pointer l\'arrivée', checkInOriginalIcon);
                });
            }

            if (checkOutBtn) {
                checkOutBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    handlePointage(checkOutBtn, checkOutForm, checkOutLat, checkOutLng, checkOutText, checkOutIcon, 'Pointer la sortie', checkOutOriginalIcon);
                });
            }
        });

        // Announcement functions
        function markAnnouncementRead(id) {
            fetch(`/employee/announcements/${id}/read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
        }

        function acknowledgeAnnouncement(id, button) {
            button.disabled = true;
            button.textContent = 'Envoi...';

            fetch(`/employee/announcements/${id}/acknowledge`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    button.textContent = '✓ Confirmé';
                    button.classList.remove('bg-white', 'text-red-600');
                    button.classList.add('bg-green-500', 'text-white');
                    setTimeout(() => {
                        button.closest('.rounded-2xl').remove();
                    }, 1500);
                }
            })
            .catch(() => {
                button.textContent = 'Erreur';
                button.disabled = false;
            });
        }
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
<?php /**PATH D:\ManageX\resources\views\employee\dashboard.blade.php ENDPATH**/ ?>