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
    <div class="space-y-6" x-data="announcementManagement()">
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
                                <li><span class="text-white text-sm font-medium">Annonces</span></li>
                            </ol>
                        </nav>
                        <h1 class="text-2xl md:text-3xl font-bold text-white flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                                </svg>
                            </div>
                            Annonces
                        </h1>
                        <p class="text-white/80 mt-2">Gérez les communications internes</p>
                    </div>
                    <a href="<?php echo e(route('admin.announcements.create')); ?>" 
                       class="px-4 py-2.5 bg-white font-semibold rounded-xl hover:bg-purple-50 transition-all shadow-lg flex items-center" style="color: #5680E9;">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Nouvelle Annonce
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 animate-fade-in-up animation-delay-100">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: rgba(86, 128, 233, 0.15);">
                        <svg class="w-6 h-6" style="color: #5680E9;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900"><?php echo e($stats['total']); ?></p>
                        <p class="text-sm text-gray-500">Total annonces</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: rgba(90, 185, 234, 0.15);">
                        <svg class="w-6 h-6" style="color: #5AB9EA;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900"><?php echo e($stats['active']); ?></p>
                        <p class="text-sm text-gray-500">Actives</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: rgba(136, 96, 208, 0.15);">
                        <svg class="w-6 h-6" style="color: #8860D0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900"><?php echo e($stats['urgent']); ?></p>
                        <p class="text-sm text-gray-500">Urgentes</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: rgba(132, 206, 235, 0.15);">
                        <svg class="w-6 h-6" style="color: #84CEEB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900"><?php echo e($stats['pinned']); ?></p>
                        <p class="text-sm text-gray-500">Épinglées</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 animate-fade-in-up animation-delay-200">
            <form method="GET" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[200px]">
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" 
                           placeholder="Rechercher une annonce..."
                           class="w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <select name="status" class="rounded-xl border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Tous les statuts</option>
                    <option value="active" <?php echo e(request('status') === 'active' ? 'selected' : ''); ?>>Actives</option>
                    <option value="inactive" <?php echo e(request('status') === 'inactive' ? 'selected' : ''); ?>>Inactives</option>
                </select>
                <select name="type" class="rounded-xl border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Tous les types</option>
                    <option value="info" <?php echo e(request('type') === 'info' ? 'selected' : ''); ?>>ℹ️ Info</option>
                    <option value="success" <?php echo e(request('type') === 'success' ? 'selected' : ''); ?>>✅ Succès</option>
                    <option value="warning" <?php echo e(request('type') === 'warning' ? 'selected' : ''); ?>>⚠️ Attention</option>
                    <option value="urgent" <?php echo e(request('type') === 'urgent' ? 'selected' : ''); ?>>🚨 Urgent</option>
                    <option value="event" <?php echo e(request('type') === 'event' ? 'selected' : ''); ?>>📅 Événement</option>
                </select>
                <select name="priority" class="rounded-xl border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Toutes priorités</option>
                    <option value="normal" <?php echo e(request('priority') === 'normal' ? 'selected' : ''); ?>>Normale</option>
                    <option value="high" <?php echo e(request('priority') === 'high' ? 'selected' : ''); ?>>Haute</option>
                    <option value="critical" <?php echo e(request('priority') === 'critical' ? 'selected' : ''); ?>>Critique</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors">
                    Filtrer
                </button>
                <?php if(request()->hasAny(['search', 'status', 'type', 'priority'])): ?>
                    <a href="<?php echo e(route('admin.announcements.index')); ?>" class="px-4 py-2 text-gray-500 hover:text-gray-700">
                        Réinitialiser
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Announcements List -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden animate-fade-in-up animation-delay-300">
            <?php $__empty_1 = true; $__currentLoopData = $announcements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $announcement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="p-4 border-b border-gray-100 hover:bg-purple-50/50 transition-colors group <?php echo e(!$announcement->is_active ? 'opacity-60' : ''); ?>">
                    <div class="flex items-start gap-4">
                        <!-- Icon -->
                        <div class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center"
                            style="<?php if($announcement->type === 'urgent'): ?> background-color: rgba(136, 96, 208, 0.15);
                            <?php elseif($announcement->type === 'warning'): ?> background-color: rgba(245, 158, 11, 0.15);
                            <?php elseif($announcement->type === 'success'): ?> background-color: rgba(90, 185, 234, 0.15);
                            <?php elseif($announcement->type === 'event'): ?> background-color: rgba(86, 128, 233, 0.15);
                            <?php else: ?> background-color: rgba(132, 206, 235, 0.15); <?php endif; ?>">
                            <span class="text-lg"><?php echo e($announcement->type_icon); ?></span>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <?php if($announcement->is_pinned): ?>
                                    <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                    </svg>
                                <?php endif; ?>
                                <a href="<?php echo e(route('admin.announcements.show', $announcement)); ?>" 
                                   class="font-semibold text-gray-900 truncate hover:text-indigo-600 transition-colors">
                                    <?php echo e($announcement->title); ?>

                                </a>
                                <?php if($announcement->priority === 'critical'): ?>
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full" style="background-color: rgba(239, 68, 68, 0.1); color: #EF4444;">Critique</span>
                                <?php elseif($announcement->priority === 'high'): ?>
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full" style="background-color: rgba(245, 158, 11, 0.1); color: #F59E0B;">Haute</span>
                                <?php endif; ?>
                                <?php if($announcement->requires_acknowledgment): ?>
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full" style="background-color: rgba(86, 128, 233, 0.1); color: #5680E9;">Accusé requis</span>
                                <?php endif; ?>
                            </div>
                            <div class="flex items-center gap-4 mt-1 text-sm text-gray-500">
                                <span><?php echo e($announcement->target_label); ?></span>
                                <span>•</span>
                                <span>Par <?php echo e($announcement->creator?->name ?? 'Admin'); ?></span>
                                <span>•</span>
                                <span><?php echo e($announcement->created_at->diffForHumans()); ?></span>
                            </div>
                            <!-- Read Progress -->
                            <div class="mt-2 flex items-center gap-2">
                                <div class="flex-1 max-w-xs h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full" style="width: <?php echo e($announcement->read_percentage); ?>%; background: linear-gradient(90deg, #5680E9, #5AB9EA);"></div>
                                </div>
                                <span class="text-xs text-gray-500"><?php echo e($announcement->read_percentage); ?>% lu (<?php echo e($announcement->reads_count); ?>/<?php echo e($announcement->target_users_count); ?>)</span>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-1 flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                            <!-- Toggle Active -->
                            <button onclick="toggleAnnouncement(<?php echo e($announcement->id); ?>)" 
                                    class="p-2 rounded-lg hover:bg-gray-100 transition-colors"
                                    title="<?php echo e($announcement->is_active ? 'Désactiver' : 'Activer'); ?>">
                                <?php if($announcement->is_active): ?>
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" style="color: #5AB9EA;">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                <?php else: ?>
                                    <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                <?php endif; ?>
                            </button>
                            <!-- Toggle Pin -->
                            <button onclick="togglePin(<?php echo e($announcement->id); ?>)" 
                                    class="p-2 rounded-lg hover:bg-gray-100 transition-colors"
                                    title="<?php echo e($announcement->is_pinned ? 'Désépingler' : 'Épingler'); ?>">
                                <svg class="w-5 h-5 <?php echo e($announcement->is_pinned ? 'text-amber-500' : 'text-gray-400'); ?>" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M5 5a2 2 0 012-2h6a2 2 0 012 2v2a2 2 0 01-2 2H7a2 2 0 01-2-2V5zM4 9v6a2 2 0 002 2h8a2 2 0 002-2V9a1 1 0 00-1-1H5a1 1 0 00-1 1z"/>
                                </svg>
                            </button>
                            <a href="<?php echo e(route('admin.announcements.edit', $announcement)); ?>" 
                               class="p-2 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"
                               title="Modifier">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <button type="button" 
                                    @click="confirmDelete('<?php echo e(route('admin.announcements.destroy', $announcement)); ?>')"
                                    class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                    title="Supprimer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="p-12 text-center">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4" style="background-color: rgba(86, 128, 233, 0.15);">
                        <svg class="w-8 h-8" style="color: #5680E9;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Aucune annonce</h3>
                    <p class="text-gray-500 mt-1">Créez votre première annonce pour communiquer avec vos employés.</p>
                    <a href="<?php echo e(route('admin.announcements.create')); ?>" 
                       class="inline-flex items-center px-4 py-2.5 mt-4 text-white font-semibold rounded-xl shadow-lg transition-all" style="background: linear-gradient(135deg, #5680E9, #5AB9EA);">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Créer une annonce
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if($announcements->hasPages()): ?>
            <div class="flex justify-center">
                <?php echo e($announcements->links()); ?>

            </div>
        <?php endif; ?>


    <!-- Delete Confirmation Modal -->
    <div x-show="showDeleteModal" 
         class="fixed inset-0 z-[100] overflow-y-auto" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true"
         style="display: none;">
        
        <!-- Backdrop -->
        <div x-show="showDeleteModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
             @click="showDeleteModal = false"></div>

        <div class="flex min-h-screen items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div x-show="showDeleteModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">Confirmer la suppression</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">Êtes-vous sûr de vouloir supprimer cette annonce ? Cette action est irréversible.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <form :action="deleteUrl" method="POST" class="inline">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" 
                                class="inline-flex w-full justify-center rounded-xl bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto">
                            Supprimer
                        </button>
                    </form>
                    <button type="button" 
                            @click="showDeleteModal = false"
                            class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <script nonce="<?php echo e($cspNonce ?? ''); ?>">
        function announcementManagement() {
            return {
                showDeleteModal: false,
                deleteUrl: '',
                confirmDelete(url) {
                    this.deleteUrl = url;
                    this.showDeleteModal = true;
                }
            }
        }

        function toggleAnnouncement(id) {
            fetch(`/admin/announcements/${id}/toggle`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }

        function togglePin(id) {
            fetch(`/admin/announcements/${id}/pin`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }
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
<?php /**PATH D:\ManageX\resources\views/admin/announcements/index.blade.php ENDPATH**/ ?>