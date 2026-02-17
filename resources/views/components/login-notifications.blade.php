{{-- Composant d'alerte à la connexion : affiche les notifications non lues au premier chargement de session --}}
@php
    $unreadNotifications = auth()->user()->unreadNotifications()->take(8)->get();
    $unreadCount = auth()->user()->unreadNotifications()->count();
    $isAdmin = auth()->user()->role === 'admin';
    $markAllReadUrl = $isAdmin ? route('admin.notifications.read-all') : route('employee.notifications.read-all');
@endphp

@if($unreadCount > 0)
<div x-data="loginNotifications()" x-show="visible" x-cloak
     x-transition:enter="transition ease-out duration-500"
     x-transition:enter-start="opacity-0 -translate-y-4"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-300"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 -translate-y-4"
     class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
     style="background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px);">

    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden" @click.away="dismiss()">
        {{-- Header --}}
        <div class="px-6 py-5 flex items-center justify-between"
             style="background: linear-gradient(135deg, #3B8BEB, #2563eb);">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-white">Bonjour {{ auth()->user()->name }} 👋</h2>
                    <p class="text-sm text-white/80">
                        Vous avez <span class="font-semibold text-white">{{ $unreadCount }}</span> notification{{ $unreadCount > 1 ? 's' : '' }} en attente
                    </p>
                </div>
            </div>
            <button @click="dismiss()" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center text-white/80 hover:text-white transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Notification List --}}
        <div class="max-h-80 overflow-y-auto divide-y divide-gray-50">
            @foreach($unreadNotifications as $notification)
                @php
                    $data = $notification->data;
                    $type = $data['type'] ?? 'default';
                    $url = $data['url'] ?? '#';
                    $message = $data['message'] ?? match($type) {
                        'leave_request' => '📅 Nouvelle demande de congé',
                        'leave_status' => ($data['status'] ?? '') === 'approved' ? '✅ Congé approuvé' : '❌ Congé refusé',
                        'task_assigned' => '📋 Nouvelle tâche : ' . ($data['task_title'] ?? $data['titre'] ?? ''),
                        'task_status' => '📋 Tâche mise à jour',
                        'task_reminder' => '⏰ Rappel tâche',
                        'new_message' => '💬 Message de ' . ($data['sender_name'] ?? 'quelqu\'un'),
                        'payroll_added' => '💰 Fiche de paie disponible',
                        'new_survey' => '📊 Nouveau sondage',
                        'new_evaluation' => '📝 Nouvelle évaluation',
                        'welcome' => '👋 Bienvenue !',
                        'missing_evaluation', 'missing_evaluation_alert' => '⚠️ Évaluations manquantes',
                        'weekly_evaluation_reminder', 'evaluation_reminder' => '📝 Rappel évaluations',
                        default => 'Nouvelle notification'
                    };
                    $iconBg = match($type) {
                        'leave_request' => 'bg-blue-100 text-blue-600',
                        'leave_status' => 'bg-green-100 text-green-600',
                        'task_assigned', 'task_status' => 'bg-purple-100 text-purple-600',
                        'task_reminder' => 'bg-orange-100 text-orange-600',
                        'new_message' => 'bg-indigo-100 text-indigo-600',
                        'payroll_added' => 'bg-emerald-100 text-emerald-600',
                        'new_survey' => 'bg-cyan-100 text-cyan-600',
                        'new_evaluation', 'missing_evaluation', 'missing_evaluation_alert', 'weekly_evaluation_reminder', 'evaluation_reminder' => 'bg-amber-100 text-amber-600',
                        'welcome' => 'bg-green-100 text-green-600',
                        'late_arrival' => 'bg-red-100 text-red-600',
                        default => 'bg-gray-100 text-gray-600',
                    };
                @endphp
                <a href="{{ $url }}" class="flex items-start gap-3 px-6 py-4 hover:bg-gray-50 transition-colors group">
                    <div class="flex-shrink-0 w-9 h-9 rounded-full {{ $iconBg }} flex items-center justify-center mt-0.5">
                        @if($type === 'task_assigned' || $type === 'task_status' || $type === 'task_reminder')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        @elseif($type === 'leave_request' || $type === 'leave_status')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        @elseif($type === 'new_message')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        @elseif($type === 'payroll_added')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @else
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 group-hover:text-blue-600 transition-colors">{{ $message }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                    <svg class="w-4 h-4 text-gray-300 group-hover:text-blue-400 flex-shrink-0 mt-1 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            @endforeach
        </div>

        {{-- Footer --}}
        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between gap-3" style="background: rgba(59, 139, 235, 0.03);">
            <form action="{{ $markAllReadUrl }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-sm font-medium hover:opacity-80 transition-opacity" style="color: #3B8BEB;">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Tout marquer comme lu
                </button>
            </form>
            <button @click="dismiss()" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 rounded-lg transition-all">
                Fermer
            </button>
        </div>
    </div>
</div>

<script nonce="{{ $cspNonce ?? '' }}">
function loginNotifications() {
    return {
        visible: false,

        init() {
            // N'afficher qu'une seule fois par session navigateur
            if (sessionStorage.getItem('managex-login-notif-shown')) return;

            // Afficher après un court délai pour laisser la page se charger
            setTimeout(() => {
                this.visible = true;
                sessionStorage.setItem('managex-login-notif-shown', '1');
            }, 800);

            // Auto-dismiss après 20 secondes
            setTimeout(() => {
                this.dismiss();
            }, 20000);
        },

        dismiss() {
            this.visible = false;
        }
    }
}
</script>
@endif
