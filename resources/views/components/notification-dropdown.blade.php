@php
$notifications = auth()->user()->unreadNotifications()->take(5)->get();
$count = auth()->user()->unreadNotifications()->count();
$isAdmin = auth()->user()->role === 'admin';

// Helper function to get notification message
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
            return "Nouvelle demande de congé de {$employeeName}";
            
        case 'leave_status':
            $status = $data['status'] ?? '';
            $statusLabel = $status === 'approved' ? 'approuvée' : 'refusée';
            $icon = $status === 'approved' ? '✅' : '❌';
            return "{$icon} Votre demande de congé a été {$statusLabel}";
            
        case 'task_assigned':
            $taskName = $data['task_name'] ?? $data['titre'] ?? 'une tâche';
            return "Nouvelle tâche assignée : {$taskName}";
            
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
            
        default:
            return 'Nouvelle notification';
    }
}
@endphp

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
            @if($notifications->isEmpty())
                <div class="px-4 py-6 text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <p class="text-sm text-gray-500">Aucune notification</p>
                </div>
            @else
                @foreach($notifications as $notification)
                    @php
                        $notifUrl = $notification->data['url'] ?? '#';
                        $notifMessage = getNotificationMessage($notification);
                        $notifType = $notification->data['type'] ?? 'default';
                        
                        // Icon based on type
                        $iconClass = match($notifType) {
                            'leave_request' => 'bg-blue-100 text-blue-600',
                            'leave_status' => 'bg-green-100 text-green-600',
                            'task_assigned' => 'bg-purple-100 text-purple-600',
                            'task_reminder' => 'bg-orange-100 text-orange-600',
                            'late_arrival' => 'bg-red-100 text-red-600',
                            default => 'bg-gray-100 text-gray-600',
                        };
                    @endphp
                    <a href="{{ $notifUrl }}" class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-50">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full {{ $iconClass }} flex items-center justify-center">
                                @if($notifType === 'leave_request')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                @elseif($notifType === 'leave_status')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @elseif($notifType === 'task_assigned' || $notifType === 'task_reminder')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                    </svg>
                                @elseif($notifType === 'late_arrival')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-900">{{ $notifMessage }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </a>
                @endforeach
            @endif
        </div>

        @if($count > 0)
            <div class="px-4 py-2 border-t border-gray-100">
                <form action="{{ $isAdmin ? route('admin.notifications.read-all') : route('employee.notifications.read-all') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-sm text-blue-600 hover:text-blue-800">
                        Tout marquer comme lu
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>

<script>
function notifDropdown() {
    return {
        open: false,
        count: {{ $count }},
        
        addNotification(notification) {
            this.count++;
        }
    }
}
</script>
