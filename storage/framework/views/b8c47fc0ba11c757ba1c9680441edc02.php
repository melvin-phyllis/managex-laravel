<?php
$notifications = auth()->user()->unreadNotifications()->take(5)->get();
$count = auth()->user()->unreadNotifications()->count();
$isAdmin = auth()->user()->role === 'admin';

// Helper function to get notification message (use function_exists to avoid redeclare error)
if (!function_exists('getNotificationMessage')) {
    function getNotificationMessage($notification) {
    $data = $notification->data;
    
    // If message exists, use it
    if (!empty($data['message'])) {
        return $data['message'];
    }
    
    // Build message based on notification type
    $type = $data['type'] ?? '';
    
    switch ($type) {
        case 'leave_request':
            $employeeName = $data['employee_name'] ?? 'Un employé';
            return "📅 Nouvelle demande de congé de {$employeeName}";
            
        case 'leave_status':
            $status = $data['status'] ?? '';
            $statusLabel = $status === 'approved' ? 'approuvée' : 'refusée';
            $icon = $status === 'approved' ? '✅' : '❌';
            return "{$icon} Votre demande de congé a été {$statusLabel}";
            
        case 'task_assigned':
            $taskName = $data['task_name'] ?? $data['task_title'] ?? $data['titre'] ?? 'une tâche';
            return "📋 Nouvelle tâche : {$taskName}";
            
        case 'task_status':
            $taskName = $data['task_titre'] ?? 'une tâche';
            $status = $data['status'] ?? 'mise à jour';
            return "📋 Tâche {$status} : {$taskName}";
            
        case 'task_reminder':
            $taskName = $data['task_name'] ?? $data['titre'] ?? 'une tâche';
            $reminderType = $data['reminder_type'] ?? '';
            if ($reminderType === 'overdue') {
                return "⚠️ Tâche en retard : {$taskName}";
            }
            return "⏰ Rappel : {$taskName}";
            
        case 'late_arrival':
            $employeeName = $data['employee_name'] ?? 'Un employé';
            return "⚠️ Retard signalé pour {$employeeName}";
            
        case 'new_message':
            $senderName = $data['sender_name'] ?? 'Quelqu\'un';
            return "💬 Nouveau message de {$senderName}";
            
        case 'payroll_added':
            $periode = $data['periode'] ?? '';
            return "💰 Fiche de paie disponible" . ($periode ? " ({$periode})" : '');
            
        case 'new_survey':
            $titre = $data['survey_titre'] ?? 'un sondage';
            return "📊 Nouveau sondage : {$titre}";
            
        case 'new_evaluation':
            $score = $data['total_score'] ?? '';
            $weekLabel = $data['week_label'] ?? '';
            return "📝 Évaluation reçue" . ($score ? " : {$score}/10" : '') . ($weekLabel ? " ({$weekLabel})" : '');
            
        case 'welcome':
            return "👋 Bienvenue ! Votre compte a été créé.";
            
        case 'missing_evaluation':
        case 'missing_evaluation_alert':
            return "⚠️ Évaluations manquantes à compléter";
            
        case 'weekly_evaluation_reminder':
        case 'evaluation_reminder':
            return "📝 Rappel : évaluations hebdomadaires à soumettre";
            
        default:
            return 'Nouvelle notification';
    }
}
} // End function_exists check
?>

<div x-data="notifDropdown()" @new-notification.window="addNotification($event.detail)" class="relative">
    <button @click="open = !open" class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>
        <span x-show="count > 0" 
              x-text="count > 99 ? '99+' : count"
              x-transition
              class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-500 rounded-full">
        </span>
    </button>

    <div x-show="open"
         @click.away="open = false"
         x-transition
         x-cloak
         class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg py-2 z-50 border border-gray-100">

        <div class="px-4 py-2 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
            <span x-show="count > 0" class="text-xs text-gray-500" x-text="count + ' non lue(s)'"></span>
        </div>

        <div class="max-h-64 overflow-y-auto">
            <?php if($notifications->isEmpty()): ?>
                <div class="px-4 py-6 text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <p class="text-sm text-gray-500">Aucune notification</p>
                </div>
            <?php else: ?>
                <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $notifUrl = $notification->data['url'] ?? '#';
                        $notifMessage = getNotificationMessage($notification);
                        $notifType = $notification->data['type'] ?? 'default';
                        
                        // Icon based on type
                        $iconClass = match($notifType) {
                            'leave_request' => 'bg-blue-100 text-blue-600',
                            'leave_status' => 'bg-green-100 text-green-600',
                            'task_assigned', 'task_status' => 'bg-purple-100 text-purple-600',
                            'task_reminder' => 'bg-orange-100 text-orange-600',
                            'late_arrival' => 'bg-red-100 text-red-600',
                            'new_message' => 'bg-indigo-100 text-indigo-600',
                            'payroll_added' => 'bg-emerald-100 text-emerald-600',
                            'new_survey' => 'bg-cyan-100 text-cyan-600',
                            'new_evaluation' => 'bg-amber-100 text-amber-600',
                            'welcome' => 'bg-green-100 text-green-600',
                            'missing_evaluation', 'missing_evaluation_alert', 'weekly_evaluation_reminder', 'evaluation_reminder' => 'bg-yellow-100 text-yellow-600',
                            default => 'bg-gray-100 text-gray-600',
                        };
                    ?>
                    <a href="<?php echo e($notifUrl); ?>" class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-50">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full <?php echo e($iconClass); ?> flex items-center justify-center">
                                <?php if($notifType === 'leave_request'): ?>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                <?php elseif($notifType === 'leave_status'): ?>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                <?php elseif($notifType === 'task_assigned' || $notifType === 'task_reminder' || $notifType === 'task_status'): ?>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                    </svg>
                                <?php elseif($notifType === 'late_arrival'): ?>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                <?php elseif($notifType === 'new_message'): ?>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                    </svg>
                                <?php elseif($notifType === 'payroll_added'): ?>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                <?php elseif($notifType === 'new_survey'): ?>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                <?php elseif($notifType === 'new_evaluation' || $notifType === 'missing_evaluation' || $notifType === 'missing_evaluation_alert' || $notifType === 'weekly_evaluation_reminder' || $notifType === 'evaluation_reminder'): ?>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                    </svg>
                                <?php elseif($notifType === 'welcome'): ?>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                    </svg>
                                <?php else: ?>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>
                                <?php endif; ?>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-900"><?php echo e($notifMessage); ?></p>
                                <p class="text-xs text-gray-500 mt-1"><?php echo e($notification->created_at->diffForHumans()); ?></p>
                            </div>
                        </div>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </div>

        <?php if($count > 0): ?>
            <div class="px-4 py-2 border-t border-gray-100">
                <form action="<?php echo e($isAdmin ? route('admin.notifications.read-all') : route('employee.notifications.read-all')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="text-sm text-blue-600 hover:text-blue-800">
                        Tout marquer comme lu
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<script nonce="<?php echo e($cspNonce ?? ''); ?>">
function notifDropdown() {
    return {
        open: false,
        count: <?php echo e($count); ?>,
        
        addNotification(notification) {
            this.count++;
        }
    }
}
</script>
<?php /**PATH D:\ManageX\resources\views\components\notification-dropdown.blade.php ENDPATH**/ ?>