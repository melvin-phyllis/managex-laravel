<x-layouts.admin>
    <div class="max-w-5xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.announcements.index') }}" 
                   class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">{{ $announcement->type_icon }}</span>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $announcement->title }}</h1>
                    </div>
                    <p class="text-gray-500">Créée {{ $announcement->created_at->diffForHumans() }} par {{ $announcement->creator?->name }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.announcements.edit', $announcement) }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Modifier
                </a>
            </div>
        </div>

        <!-- Badges -->
        <div class="flex flex-wrap gap-2">
            @if($announcement->is_pinned)
                <span class="px-3 py-1 text-sm font-medium bg-amber-100 text-amber-700 rounded-full">📌 Épinglée</span>
            @endif
            @if($announcement->is_active)
                <span class="px-3 py-1 text-sm font-medium bg-green-100 text-green-700 rounded-full">✅ Active</span>
            @else
                <span class="px-3 py-1 text-sm font-medium bg-gray-100 text-gray-700 rounded-full">❌ Inactive</span>
            @endif
            <span class="px-3 py-1 text-sm font-medium bg-{{ $announcement->type_color }}-100 text-{{ $announcement->type_color }}-700 rounded-full">
                {{ ucfirst($announcement->type) }}
            </span>
            <span class="px-3 py-1 text-sm font-medium bg-{{ $announcement->priority_color }}-100 text-{{ $announcement->priority_color }}-700 rounded-full">
                Priorité {{ $announcement->priority }}
            </span>
            @if($announcement->requires_acknowledgment)
                <span class="px-3 py-1 text-sm font-medium bg-purple-100 text-purple-700 rounded-full">Accusé requis</span>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Announcement Content -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Contenu</h2>
                    <div class="prose max-w-none">
                        {!! nl2br(e($announcement->content)) !!}
                    </div>
                </div>

                <!-- Read Users -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-4 border-b border-gray-100">
                        <h2 class="text-lg font-semibold text-gray-900">✅ Ont lu ({{ $stats['read_count'] }})</h2>
                    </div>
                    <div class="max-h-80 overflow-y-auto">
                        @forelse($readUsers as $read)
                            <div class="flex items-center justify-between p-4 border-b border-gray-50 last:border-0">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center overflow-hidden">
                                        @if($read->user?->avatar)
                                            <img src="{{ Storage::url($read->user->avatar) }}" class="w-full h-full object-cover">
                                        @else
                                            <span class="text-gray-500 font-medium">{{ substr($read->user?->name ?? '?', 0, 1) }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $read->user?->name ?? 'Utilisateur supprimé' }}</p>
                                        <p class="text-sm text-gray-500">{{ $read->user?->email }}</p>
                                    </div>
                                </div>
                                <div class="text-right text-sm">
                                    <p class="text-gray-500">Lu le {{ $read->read_at->format('d/m/Y H:i') }}</p>
                                    @if($read->acknowledged_at)
                                        <p class="text-green-600">✓ Accusé {{ $read->acknowledged_at->format('d/m/Y H:i') }}</p>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-gray-500">
                                Personne n'a encore lu cette annonce.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Unread Users -->
                @if($unreadUsers->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">❌ N'ont pas lu ({{ $unreadUsers->count() }})</h2>
                    </div>
                    <div class="max-h-60 overflow-y-auto">
                        @foreach($unreadUsers as $user)
                            <div class="flex items-center p-4 border-b border-gray-50 last:border-0">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center overflow-hidden">
                                        @if($user->avatar)
                                            <img src="{{ Storage::url($user->avatar) }}" class="w-full h-full object-cover">
                                        @else
                                            <span class="text-gray-500 font-medium">{{ substr($user->name, 0, 1) }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar Stats -->
            <div class="space-y-6">
                <!-- Stats Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">📊 Statistiques</h2>
                    
                    <div class="space-y-4">
                        <!-- Read Progress -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm text-gray-600">Taux de lecture</span>
                                <span class="text-sm font-semibold text-gray-900">{{ $stats['read_percentage'] }}%</span>
                            </div>
                            <div class="h-3 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-green-500 rounded-full transition-all" 
                                     style="width: {{ $stats['read_percentage'] }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">{{ $stats['read_count'] }} / {{ $stats['total_target'] }} personnes</p>
                        </div>

                        @if($announcement->requires_acknowledgment)
                        <!-- Acknowledged Progress -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm text-gray-600">Accusés de réception</span>
                                <span class="text-sm font-semibold text-gray-900">{{ $stats['acknowledged_percentage'] }}%</span>
                            </div>
                            <div class="h-3 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-purple-500 rounded-full transition-all" 
                                     style="width: {{ $stats['acknowledged_percentage'] }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">{{ $stats['acknowledged_count'] }} / {{ $stats['total_target'] }} personnes</p>
                        </div>
                        @endif

                        <hr>

                        <!-- Quick Stats -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <p class="text-2xl font-bold text-gray-900">{{ $stats['read_count'] }}</p>
                                <p class="text-xs text-gray-500">Lecteurs</p>
                            </div>
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_target'] - $stats['read_count'] }}</p>
                                <p class="text-xs text-gray-500">Non lus</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">ℹ️ Informations</h2>
                    
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Ciblage</span>
                            <span class="font-medium text-gray-900">{{ $announcement->target_label }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Créée le</span>
                            <span class="font-medium text-gray-900">{{ $announcement->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        @if($announcement->start_date)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Début</span>
                            <span class="font-medium text-gray-900">{{ $announcement->start_date->format('d/m/Y') }}</span>
                        </div>
                        @endif
                        @if($announcement->end_date)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Fin</span>
                            <span class="font-medium text-gray-900">{{ $announcement->end_date->format('d/m/Y') }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-500">Vues</span>
                            <span class="font-medium text-gray-900">{{ $announcement->view_count }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
