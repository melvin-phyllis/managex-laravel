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
    <div class="max-w-3xl mx-auto space-y-6">
        <!-- Back Button -->
        <a href="<?php echo e(route('employee.announcements.index')); ?>" 
           class="inline-flex items-center text-gray-500 hover:text-gray-700">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Retour aux annonces
        </a>

        <!-- Announcement Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Header -->
            <div class="p-6 border-b border-gray-100
                <?php if($announcement->type === 'urgent'): ?> bg-gradient-to-r from-red-500 to-rose-600 text-white
                <?php elseif($announcement->type === 'warning'): ?> bg-gradient-to-r from-amber-500 to-orange-500 text-white
                <?php elseif($announcement->type === 'success'): ?> bg-gradient-to-r from-green-500 to-emerald-600 text-white
                <?php elseif($announcement->type === 'event'): ?> bg-gradient-to-r from-purple-500 to-indigo-600 text-white
                <?php else: ?> bg-gradient-to-r from-blue-500 to-cyan-600 text-white <?php endif; ?>">
                
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center">
                        <span class="text-3xl"><?php echo e($announcement->type_icon); ?></span>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 flex-wrap mb-1">
                            <?php if($announcement->is_pinned): ?>
                                <span>ðŸ“Œ</span>
                            <?php endif; ?>
                            <h1 class="text-2xl font-bold"><?php echo e($announcement->title); ?></h1>
                        </div>
                        <div class="flex items-center gap-4 text-sm opacity-90">
                            <span><?php echo e($announcement->created_at->translatedFormat('l d F Y é  H:i')); ?></span>
                            <span>â€¢</span>
                            <span>Par <?php echo e($announcement->creator?->name ?? 'Administration'); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Badges -->
                <div class="flex flex-wrap gap-2 mt-4">
                    <?php if($announcement->priority === 'critical'): ?>
                        <span class="px-3 py-1 text-xs font-medium bg-white/20 rounded-full">ðŸš Critique</span>
                    <?php elseif($announcement->priority === 'high'): ?>
                        <span class="px-3 py-1 text-xs font-medium bg-white/20 rounded-full">âš¡ Haute priorité</span>
                    <?php endif; ?>
                    <?php if($announcement->requires_acknowledgment): ?>
                        <span class="px-3 py-1 text-xs font-medium bg-white/20 rounded-full">âœ… Accusé requis</span>
                    <?php endif; ?>
                    <span class="px-3 py-1 text-xs font-medium bg-white/20 rounded-full"><?php echo e($announcement->target_label); ?></span>
                </div>
            </div>

            <!-- Content -->
            <div class="p-6">
                <div class="prose max-w-none text-gray-700">
                    <?php echo nl2br(e($announcement->content)); ?>

                </div>
            </div>

            <!-- Acknowledgment Section -->
            <?php if($announcement->requires_acknowledgment): ?>
                <div class="px-6 pb-6">
                    <?php if($announcement->is_acknowledged): ?>
                        <div class="p-4 bg-green-50 rounded-xl border border-green-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-green-800">Accusé de réception envoyé</p>
                                    <p class="text-sm text-green-600">Vous avez confirmé avoir pris connaissance de cette annonce.</p>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="p-4 bg-purple-50 rounded-xl border border-purple-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium text-purple-800">Accusé de réception requis</p>
                                        <p class="text-sm text-purple-600">Veuillez confirmer avoir lu cette annonce.</p>
                                    </div>
                                </div>
                                <button id="acknowledgeBtn" onclick="acknowledgeAnnouncement()"
                                        class="px-6 py-2.5 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700 transition-colors">
                                    âœ“ J'ai pris connaissance
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                <div class="flex items-center justify-between text-sm text-gray-500">
                    <span>âœ“ Lu le <?php echo e(now()->format('d/m/Y é  H:i')); ?></span>
                    <?php if($announcement->end_date): ?>
                        <span>Valable jusqu'au <?php echo e($announcement->end_date->format('d/m/Y')); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <script nonce="<?php echo e($cspNonce ?? ''); ?>">
        function acknowledgeAnnouncement() {
            const btn = document.getElementById('acknowledgeBtn');
            btn.disabled = true;
            btn.textContent = 'Envoi en cours...';

            fetch('<?php echo e(route("employee.announcements.acknowledge", $announcement)); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    btn.textContent = 'âœ“ Confirmé !';
                    btn.classList.remove('bg-purple-600', 'hover:bg-purple-700');
                    btn.classList.add('bg-green-600');
                    
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                }
            })
            .catch(() => {
                btn.textContent = 'Erreur - Réessayer';
                btn.disabled = false;
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
<?php /**PATH D:\ManageX\resources\views\employee\announcements\show.blade.php ENDPATH**/ ?>