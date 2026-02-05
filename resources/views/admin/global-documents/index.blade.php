<x-layouts.admin>
    <div class="space-y-6">
        <!-- Header avec gradient -->
        <div class="relative overflow-hidden rounded-2xl shadow-xl animate-fade-in-up" style="background: linear-gradient(135deg, #5680E9, #84CEEB) !important;">
            <div class="absolute inset-0 bg-black/10"></div>
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            
            <div class="relative p-6 md:p-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <nav class="flex mb-3" aria-label="Breadcrumb">
                            <ol class="inline-flex items-center space-x-1">
                                <li><a href="{{ route('admin.dashboard') }}" class="text-white/70 hover:text-white text-sm">Dashboard</a></li>
                                <li><span class="text-white/50 mx-2">/</span></li>
                                <li><span class="text-white text-sm font-medium">Documents Globaux</span></li>
                            </ol>
                        </nav>
                        <h1 class="text-2xl md:text-3xl font-bold text-white flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            Documents Globaux
                        </h1>
                        <p class="text-white/80 mt-2">Gérez le règlement intérieur et les documents de l'entreprise</p>
                    </div>
                    <a href="{{ route('admin.global-documents.create') }}"
                       class="px-5 py-2.5 bg-white font-semibold rounded-xl hover:bg-purple-50 transition-all shadow-lg flex items-center gap-2" style="color: #5680E9;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Ajouter un document
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-3 animate-fade-in-up">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <!-- Documents par type -->
        @php
            $typeColors = [
                'reglement_interieur' => ['bg' => 'linear-gradient(135deg, #5680E9, #84CEEB)', 'icon' => '#5680E9'],
                'fiche_de_poste' => ['bg' => 'linear-gradient(135deg, #8860D0, #C1C8E4)', 'icon' => '#8860D0'],
                'charte_informatique' => ['bg' => 'linear-gradient(135deg, #5AB9EA, #84CEEB)', 'icon' => '#5AB9EA'],
                'politique_conges' => ['bg' => 'linear-gradient(135deg, #5680E9, #8860D0)', 'icon' => '#5680E9'],
            ];
            $typeIndex = 0;
        @endphp

        @forelse($types as $typeKey => $typeLabel)
            @php
                $colors = $typeColors[$typeKey] ?? ['bg' => 'linear-gradient(135deg, #5680E9, #84CEEB)', 'icon' => '#5680E9'];
            @endphp
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden animate-fade-in-up" style="animation-delay: {{ $typeIndex * 100 }}ms">
                <div class="px-6 py-4 border-b border-gray-100" style="background: {{ $colors['bg'] }};">
                    <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        {{ $typeLabel }}
                    </h2>
                </div>

                @if(isset($documents[$typeKey]) && $documents[$typeKey]->count() > 0)
                    <div class="divide-y divide-gray-100">
                        @foreach($documents[$typeKey] as $doc)
                            <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, {{ $colors['icon'] }}20, {{ $colors['icon'] }}30);">
                                        <svg class="w-5 h-5" style="color: {{ $colors['icon'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-medium text-gray-900">{{ $doc->title }}</h3>
                                        <p class="text-sm text-gray-500">
                                            {{ $doc->original_filename }} • {{ $doc->file_size_formatted }}
                                            • Ajouté le {{ $doc->created_at->format('d/m/Y') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if($doc->is_active)
                                        <span class="px-2 py-1 text-xs font-medium rounded-full" style="background-color: #5680E920; color: #5680E9;">Actif</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-500">Inactif</span>
                                    @endif
                                    <a href="{{ route('admin.global-documents.show', $doc) }}"
                                       class="p-2 text-gray-400 hover:text-gray-600 transition rounded-lg hover:bg-gray-100" title="Détails">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.global-documents.download', $doc) }}"
                                       class="p-2 text-gray-400 hover:text-emerald-600 transition rounded-lg hover:bg-emerald-50" title="Télécharger">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.global-documents.edit', $doc) }}"
                                       class="p-2 text-gray-400 hover:text-blue-600 transition rounded-lg hover:bg-blue-50" title="Modifier">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.global-documents.destroy', $doc) }}" method="POST"
                                          onsubmit="return confirm('Supprimer ce document ?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition rounded-lg hover:bg-red-50" title="Supprimer">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="px-6 py-8 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p>Aucun {{ strtolower($typeLabel) }} n'a été ajouté</p>
                    </div>
                @endif
            </div>
            @php $typeIndex++; @endphp
        @empty
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
                <p class="text-gray-500">Aucun type de document configuré</p>
            </div>
        @endforelse
    </div>
</x-layouts.admin>
