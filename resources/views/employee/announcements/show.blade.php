<x-layouts.employee>
    <div class="max-w-3xl mx-auto space-y-6">
        <!-- Back Button -->
        <a href="{{ route('employee.announcements.index') }}" 
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
                @if($announcement->type === 'urgent') bg-gradient-to-r from-red-500 to-rose-600 text-white
                @elseif($announcement->type === 'warning') bg-gradient-to-r from-amber-500 to-orange-500 text-white
                @elseif($announcement->type === 'success') bg-gradient-to-r from-green-500 to-emerald-600 text-white
                @elseif($announcement->type === 'event') bg-gradient-to-r from-purple-500 to-indigo-600 text-white
                @else bg-gradient-to-r from-blue-500 to-cyan-600 text-white @endif">
                
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center">
                        <span class="text-3xl">{{ $announcement->type_icon }}</span>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 flex-wrap mb-1">
                            @if($announcement->is_pinned)
                                <span>📌</span>
                            @endif
                            <h1 class="text-2xl font-bold">{{ $announcement->title }}</h1>
                        </div>
                        <div class="flex items-center gap-4 text-sm opacity-90">
                            <span>{{ $announcement->created_at->translatedFormat('l d F Y à H:i') }}</span>
                            <span>•</span>
                            <span>Par {{ $announcement->creator?->name ?? 'Administration' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Badges -->
                <div class="flex flex-wrap gap-2 mt-4">
                    @if($announcement->priority === 'critical')
                        <span class="px-3 py-1 text-xs font-medium bg-white/20 rounded-full">🚨 Critique</span>
                    @elseif($announcement->priority === 'high')
                        <span class="px-3 py-1 text-xs font-medium bg-white/20 rounded-full">⚡ Haute priorité</span>
                    @endif
                    @if($announcement->requires_acknowledgment)
                        <span class="px-3 py-1 text-xs font-medium bg-white/20 rounded-full">✅ Accusé requis</span>
                    @endif
                    <span class="px-3 py-1 text-xs font-medium bg-white/20 rounded-full">{{ $announcement->target_label }}</span>
                </div>
            </div>

            <!-- Content -->
            <div class="p-6">
                <div class="prose max-w-none text-gray-700">
                    {!! nl2br(e($announcement->content)) !!}
                </div>
            </div>

            <!-- Acknowledgment Section -->
            @if($announcement->requires_acknowledgment)
                <div class="px-6 pb-6">
                    @if($announcement->is_acknowledged)
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
                    @else
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
                                    ✓ J'ai pris connaissance
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                <div class="flex items-center justify-between text-sm text-gray-500">
                    <span>✓ Lu le {{ now()->format('d/m/Y à H:i') }}</span>
                    @if($announcement->end_date)
                        <span>Valable jusqu'au {{ $announcement->end_date->format('d/m/Y') }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function acknowledgeAnnouncement() {
            const btn = document.getElementById('acknowledgeBtn');
            btn.disabled = true;
            btn.textContent = 'Envoi en cours...';

            fetch('{{ route("employee.announcements.acknowledge", $announcement) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    btn.textContent = '✓ Confirmé !';
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
    @endpush
</x-layouts.employee>
