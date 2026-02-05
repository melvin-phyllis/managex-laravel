<x-layouts.employee>
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Back Button -->
        <a href="{{ route('employee.announcements.index') }}" 
           class="inline-flex items-center gap-2 text-gray-500 hover:text-gray-700 transition-colors group">
            <div class="w-8 h-8 rounded-lg bg-gray-100 group-hover:bg-gray-200 flex items-center justify-center transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </div>
            <span class="font-medium">Retour aux annonces</span>
        </a>

        <!-- Main Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <!-- Header Section -->
            <div class="relative p-8" style="background-color: #3B8BEB;">
                <!-- Decorative elements -->
                <div class="absolute top-0 right-0 w-64 h-64 rounded-full opacity-10" style="background: white; transform: translate(30%, -50%);"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 rounded-full opacity-10" style="background: white; transform: translate(-30%, 50%);"></div>
                
                <div class="relative z-10">
                    <!-- Type Badge & Priority -->
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        @if($announcement->is_pinned)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-white text-sm font-medium">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.285 1.05A3.989 3.989 0 0115 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.715-5.349L11 6.477V16h2a1 1 0 110 2H7a1 1 0 110-2h2V6.477L6.237 7.582l1.715 5.349a1 1 0 01-.285 1.05A3.989 3.989 0 015 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.738-5.42-1.233-.617a1 1 0 01.894-1.788l1.599.799L9 4.323V3a1 1 0 011-1z"/>
                                </svg>
                                Épinglée
                            </span>
                        @endif
                        @if($announcement->priority === 'critical')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium" style="background-color: #B23850; color: white;">
                                🚨 Critique
                            </span>
                        @elseif($announcement->priority === 'high')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-white text-sm font-medium">
                                ⚡ Haute priorité
                            </span>
                        @endif
                        @if($announcement->requires_acknowledgment)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-white text-sm font-medium">
                                ✅ Accusé requis
                            </span>
                        @endif
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-white text-sm font-medium">
                            {{ $announcement->target_label }}
                        </span>
                    </div>

                    <!-- Title -->
                    <h1 class="text-3xl font-bold text-white mb-4">{{ $announcement->title }}</h1>
                    
                    <!-- Meta info -->
                    <div class="flex flex-wrap items-center gap-6 text-white/80 text-sm">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>{{ $announcement->created_at->translatedFormat('l d F Y') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>{{ $announcement->created_at->format('H:i') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span>{{ $announcement->creator?->name ?? 'Administration' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Section -->
            <div class="p-8">
                <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed">
                    {!! nl2br(e($announcement->content)) !!}
                </div>
            </div>

            <!-- Acknowledgment Section -->
            @if($announcement->requires_acknowledgment)
                <div class="px-8 pb-8">
                    @if($announcement->is_acknowledged)
                        <div class="p-6 rounded-2xl" style="background: linear-gradient(135deg, rgba(59, 139, 235, 0.1) 0%, rgba(196, 219, 246, 0.3) 100%); border: 1px solid rgba(59, 139, 235, 0.2);">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-xl flex items-center justify-center" style="background-color: #3B8BEB;">
                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-lg" style="color: #3B8BEB;">Accusé de réception envoyé</p>
                                    <p class="text-sm" style="color: #8590AA;">Vous avez confirmé avoir pris connaissance de cette annonce.</p>
                                </div>
                                <div class="hidden sm:block">
                                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium" style="background-color: rgba(59, 139, 235, 0.15); color: #3B8BEB;">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                        </svg>
                                        Vérifié
                                    </span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="p-6 rounded-2xl" style="background: linear-gradient(135deg, #E7E3D4 0%, rgba(231, 227, 212, 0.5) 100%); border: 1px solid rgba(133, 144, 170, 0.2);">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-14 h-14 rounded-xl flex items-center justify-center" style="background-color: #8590AA;">
                                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-lg text-gray-800">Action requise</p>
                                        <p class="text-sm" style="color: #8590AA;">Veuillez confirmer avoir lu cette annonce importante.</p>
                                    </div>
                                </div>
                                <button id="acknowledgeBtn" onclick="acknowledgeAnnouncement()"
                                        class="inline-flex items-center justify-center gap-2 px-6 py-3 text-white font-semibold rounded-xl transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5" 
                                        style="background-color: #3B8BEB;">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    J'ai pris connaissance
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Footer -->
            <div class="px-8 py-5 border-t border-gray-100" style="background-color: rgba(231, 227, 212, 0.3);">
                <div class="flex flex-wrap items-center justify-between gap-4 text-sm">
                    <div class="flex items-center gap-2" style="color: #8590AA;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <span>Lu le {{ now()->format('d/m/Y à H:i') }}</span>
                    </div>
                    @if($announcement->end_date)
                        <div class="flex items-center gap-2" style="color: #8590AA;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>Valable jusqu'au {{ $announcement->end_date->format('d/m/Y') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script nonce="{{ $cspNonce ?? '' }}">
        function acknowledgeAnnouncement() {
            const btn = document.getElementById('acknowledgeBtn');
            btn.disabled = true;
            btn.innerHTML = `
                <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Envoi en cours...
            `;

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
                    btn.innerHTML = `
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Confirmé !
                    `;
                    btn.style.backgroundColor = '#3B8BEB';
                    
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                }
            })
            .catch(() => {
                btn.innerHTML = 'Erreur - Réessayer';
                btn.disabled = false;
            });
        }
    </script>
    @endpush
</x-layouts.employee>
