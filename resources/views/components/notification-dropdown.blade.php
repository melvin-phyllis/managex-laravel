@php
$notifications = auth()->user()->unreadNotifications()->take(5)->get();
$count = auth()->user()->unreadNotifications()->count();
@endphp

<div x-data="{ open: false }" class="relative">
    <button @click="open = !open" class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>
        @if($count > 0)
            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-500 rounded-full">
                {{ $count > 99 ? '99+' : $count }}
            </span>
        @endif
    </button>

    <div x-show="open"
         @click.away="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg py-2 z-50 border border-gray-100">

        <div class="px-4 py-2 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
        </div>

        <div class="max-h-64 overflow-y-auto">
            @forelse($notifications as $notification)
                <div class="px-4 py-3 hover:bg-gray-50 border-b border-gray-50">
                    <p class="text-sm text-gray-900">{{ $notification->data['message'] ?? 'Nouvelle notification' }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                    <form action="{{ route('employee.notifications.read', $notification->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-xs text-blue-600 hover:text-blue-800 mt-1">
                            Marquer comme lu
                        </button>
                    </form>
                </div>
            @empty
                <div class="px-4 py-6 text-center">
                    <p class="text-sm text-gray-500">Aucune notification</p>
                </div>
            @endforelse
        </div>

        @if($count > 0)
            <div class="px-4 py-2 border-t border-gray-100">
                <form action="{{ route('employee.notifications.read-all') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-sm text-blue-600 hover:text-blue-800">
                        Tout marquer comme lu
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
