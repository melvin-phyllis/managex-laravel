<x-layouts.admin>
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center gap-4 animate-fade-in-up">
            <a href="{{ route('admin.global-documents.index') }}" class="p-2 text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-gray-900">{{ $globalDocument->title }}</h1>
                <p class="text-gray-600 mt-1">{{ $globalDocument->type_label }}</p>
            </div>
            <a href="{{ route('admin.global-documents.download', $globalDocument) }}"
               class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-medium transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Télécharger
            </a>
        </div>

        <div class="grid md:grid-cols-3 gap-6 animate-fade-in-up animation-delay-100">
            <!-- Détails du document -->
            <div class="md:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                        <h2 class="font-semibold text-gray-800">Informations</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Fichier :</span>
                                <p class="font-medium text-gray-900 flex items-center gap-2 mt-1">
                                    <span class="text-lg">{{ $globalDocument->file_icon }}</span>
                                    {{ $globalDocument->original_filename }}
                                </p>
                            </div>
                            <div>
                                <span class="text-gray-500">Taille :</span>
                                <p class="font-medium text-gray-900 mt-1">{{ $globalDocument->file_size_formatted }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500">Ajouté le :</span>
                                <p class="font-medium text-gray-900 mt-1">{{ $globalDocument->created_at->format('d/m/Y à H:i') }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500">Par :</span>
                                <p class="font-medium text-gray-900 mt-1">{{ $globalDocument->uploader->name ?? 'N/A' }}</p>
                            </div>
                        </div>

                        @if($globalDocument->description)
                            <div class="pt-4 border-t border-gray-100">
                                <span class="text-sm text-gray-500">Description :</span>
                                <p class="text-gray-900 mt-1">{{ $globalDocument->description }}</p>
                            </div>
                        @endif

                        <div class="pt-4 border-t border-gray-100">
                            <span class="text-sm text-gray-500">Statut :</span>
                            @if($globalDocument->is_active)
                                <span class="ml-2 px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">Actif</span>
                            @else
                                <span class="ml-2 px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-500">Inactif</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistiques d'accusé -->
            <div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                        <h2 class="font-semibold text-gray-800">Accusés de réception</h2>
                    </div>
                    <div class="p-6">
                        @php
                            $acknowledgedCount = $globalDocument->acknowledgedBy->count();
                            // $totalEmployees passé depuis le contrôleur
                            $percentage = $totalEmployees > 0 ? round(($acknowledgedCount / $totalEmployees) * 100) : 0;
                        @endphp

                        <div class="text-center mb-4">
                            <div class="text-4xl font-bold text-emerald-600">{{ $acknowledgedCount }}/{{ $totalEmployees }}</div>
                            <p class="text-gray-500 text-sm mt-1">employés ont accusé réception</p>
                        </div>

                        <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
                            <div class="bg-emerald-600 h-2 rounded-full transition-all" style="width: {{ $percentage }}%"></div>
                        </div>

                        @if($usersNotAcknowledged->count() > 0)
                            <div class="border-t border-gray-100 pt-4">
                                <p class="text-sm font-medium text-gray-700 mb-2">Non lu par :</p>
                                <div class="space-y-2 max-h-40 overflow-y-auto">
                                    @foreach($usersNotAcknowledged->take(10) as $user)
                                        <div class="flex items-center gap-2 text-sm">
                                            <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-xs font-medium text-gray-600">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <span class="text-gray-700">{{ $user->name }}</span>
                                        </div>
                                    @endforeach
                                    @if($usersNotAcknowledged->count() > 10)
                                        <p class="text-xs text-gray-500 text-center">+{{ $usersNotAcknowledged->count() - 10 }} autres</p>
                                    @endif
                                </div>
                            </div>
                        @else
                            <p class="text-center text-emerald-600 text-sm">✓ Tous les employés ont lu le document</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-between">
            <a href="{{ route('admin.global-documents.edit', $globalDocument) }}"
               class="text-blue-600 hover:text-blue-800 font-medium transition">
                ✏️ Modifier ce document
            </a>
            <form action="{{ route('admin.global-documents.destroy', $globalDocument) }}" method="POST"
                  onsubmit="return confirm('Supprimer ce document ?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-800 font-medium transition">
                    🗑️ Supprimer
                </button>
            </form>
        </div>
    </div>
</x-layouts.admin>
