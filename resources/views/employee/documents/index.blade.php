<x-layouts.employee>
    <div class="space-y-4">
        <!-- Header avec icône colorée -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 animate-fade-in-up">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-lg" style="background-color: #3B8BEB; box-shadow: 0 10px 15px -3px rgba(59, 139, 235, 0.3);">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Mes Documents</h1>
                    <p class="text-gray-500 text-sm">Gérez vos documents personnels et professionnels</p>
                </div>
            </div>
            <a href="{{ route('employee.document-requests.index') }}"
               class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-white text-sm font-medium rounded-xl transition-all duration-200 shadow-lg" style="background-color: #3B8BEB; box-shadow: 0 10px 15px -3px rgba(59, 139, 235, 0.3);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Demander un document
            </a>
        </div>

        <!-- Stats rapides -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 animate-fade-in-up animation-delay-100">
            <!-- Contrat -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div class="p-2 rounded-xl shadow-lg" style="background-color: #B23850; box-shadow: 0 10px 15px -3px rgba(178, 56, 80, 0.3);">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-2xl font-bold mt-3" style="color: {{ $hasContractDocument ? '#B23850' : '#d1d5db' }}">
                    {{ $hasContractDocument ? '✓' : '—' }}
                </p>
                <p class="text-xs text-gray-500">Contrat</p>
            </div>

            <!-- Documents entreprise -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div class="p-2 rounded-xl shadow-lg" style="background-color: #3B8BEB; box-shadow: 0 10px 15px -3px rgba(59, 139, 235, 0.3);">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                        </svg>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-900 mt-3">{{ $globalDocuments->count() }}</p>
                <p class="text-xs text-gray-500">Docs entreprise</p>
            </div>

            <!-- Documents personnels -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div class="p-2 rounded-xl shadow-lg" style="background-color: #8590AA; box-shadow: 0 10px 15px -3px rgba(133, 144, 170, 0.3);">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                </div>
                @php
                    $uploadedCount = collect($userDocuments)->count();
                    $requiredCount = $documentTypes->where('is_required', true)->count();
                @endphp
                <p class="text-2xl font-bold text-gray-900 mt-3">{{ $uploadedCount }}/{{ $documentTypes->count() }}</p>
                <p class="text-xs text-gray-500">Docs perso</p>
            </div>

            <!-- Documents lus -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div class="p-2 rounded-xl shadow-lg" style="background-color: #E7E3D4; box-shadow: 0 10px 15px -3px rgba(133, 144, 170, 0.2);">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-900 mt-3">{{ count($acknowledgedIds) }}/{{ $globalDocuments->count() }}</p>
                <p class="text-xs text-gray-500">Docs lus</p>
            </div>
        </div>

        <!-- Mon Contrat de Travail -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden animate-fade-in-up animation-delay-200">
            <div class="px-6 py-4 border-b border-gray-100" style="background-color: rgba(178, 56, 80, 0.05);">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-lg" style="background-color: #B23850; box-shadow: 0 10px 15px -3px rgba(178, 56, 80, 0.3);">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-semibold text-gray-900">Contrat de Travail</h2>
                        <p class="text-sm text-gray-500">Votre document contractuel</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                @if($hasContractDocument)
                    <div class="flex items-center justify-between p-4 rounded-xl border" style="background-color: rgba(178, 56, 80, 0.05); border-color: rgba(178, 56, 80, 0.1);">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-white border flex items-center justify-center shadow-sm" style="border-color: rgba(178, 56, 80, 0.2);">
                                <svg class="w-6 h-6" style="color: #B23850;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $contract->document_original_name }}</p>
                                <p class="text-sm text-gray-500">Ajouté le {{ $contract->document_uploaded_at?->format('d/m/Y') }}</p>
                            </div>
                        </div>
                        <a href="{{ route('employee.documents.download-contract') }}" 
                           class="inline-flex items-center gap-2 px-4 py-2 text-white text-sm font-medium rounded-xl transition-all duration-200 shadow-sm" style="background-color: #B23850;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Télécharger
                        </a>
                    </div>
                @else
                    <div class="flex items-center gap-4 p-4 rounded-xl border" style="background-color: #E7E3D4; border-color: rgba(133, 144, 170, 0.2);">
                        <div class="w-12 h-12 rounded-xl bg-white border flex items-center justify-center" style="border-color: rgba(133, 144, 170, 0.2);">
                            <svg class="w-6 h-6" style="color: #8590AA;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">Contrat en attente</p>
                            <p class="text-sm text-gray-600">Votre contrat sera disponible une fois uploadé par les RH.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-6">
            <!-- Documents de l'Entreprise -->
            @if($globalDocuments->count() > 0)
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden animate-fade-in-up animation-delay-300">
                <div class="px-6 py-4 border-b border-gray-100" style="background-color: rgba(59, 139, 235, 0.05);">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-lg" style="background-color: #3B8BEB; box-shadow: 0 10px 15px -3px rgba(59, 139, 235, 0.3);">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-semibold text-gray-900">Documents de l'Entreprise</h2>
                            <p class="text-sm text-gray-500">Règlement intérieur et chartes</p>
                        </div>
                    </div>
                </div>

                <div class="divide-y divide-gray-100">
                    @foreach($globalDocuments as $doc)
                        @php
                            $isAcknowledged = in_array($doc->id, $acknowledgedIds);
                        @endphp
                        <div class="p-4 hover:bg-gray-50 transition-colors duration-200">
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex items-center gap-4 flex-1 min-w-0">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background-color: {{ $isAcknowledged ? 'rgba(59, 139, 235, 0.1)' : 'rgba(178, 56, 80, 0.1)' }}">
                                        @if($isAcknowledged)
                                            <svg class="w-5 h-5" style="color: #3B8BEB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5" style="color: #B23850;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="font-medium text-gray-900 truncate">{{ $doc->title }}</p>
                                        <p class="text-sm text-gray-500">{{ $doc->type_label }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <a href="{{ route('employee.global-documents.download', $doc) }}" 
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200" style="color: #3B8BEB;">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                        Télécharger
                                    </a>
                                    @if(!$isAcknowledged)
                                        <form action="{{ route('employee.global-documents.acknowledge', $doc) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-sm" style="background-color: #3B8BEB;">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                Valider
                                            </button>
                                        </form>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-lg" style="background-color: rgba(59, 139, 235, 0.1); color: #3B8BEB;">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Lu
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Documents Personnels -->
            <div class="{{ $globalDocuments->count() > 0 ? 'lg:col-span-1' : 'lg:col-span-3' }} bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden animate-fade-in-up animation-delay-400">
                <div class="px-6 py-4 border-b border-gray-100" style="background-color: rgba(133, 144, 170, 0.05);">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-lg" style="background-color: #8590AA; box-shadow: 0 10px 15px -3px rgba(133, 144, 170, 0.3);">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-semibold text-gray-900">Documents Personnels</h2>
                            <p class="text-sm text-gray-500">{{ $documentTypes->count() }} types de documents</p>
                        </div>
                    </div>
                </div>

                <div class="divide-y divide-gray-100 max-h-96 overflow-y-auto">
                    @foreach($documentTypes as $type)
                        @php
                            $doc = $userDocuments[$type->id] ?? null;
                        @endphp

                        <div class="p-4 hover:bg-gray-50 transition-colors duration-200">
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                    <!-- Status -->
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background-color: {{ $doc ? 'rgba(59, 139, 235, 0.1)' : ($type->is_required ? 'rgba(178, 56, 80, 0.1)' : 'rgba(231, 227, 212, 0.5)') }}">
                                        @if($doc)
                                            <svg class="w-4 h-4" style="color: #3B8BEB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        @elseif($type->is_required)
                                            <svg class="w-4 h-4 text-red-500" style="color: #B23850;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                            </svg>
                                        @else
                                            <span class="w-2 h-2 rounded-full border border-gray-300"></span>
                                        @endif
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="font-medium text-gray-900 text-sm truncate">{{ $type->name }}</span>
                                            @if($type->is_required && !$doc)
                                                <span class="text-xs font-medium" style="color: #B23850;">Requis</span>
                                            @endif
                                        </div>
                                        @if($doc)
                                            <p class="text-xs mt-0.5" style="color: #3B8BEB;">
                                                ✓ Fourni le {{ $doc->created_at->format('d/m/Y') }}
                                            </p>
                                        @else
                                            <p class="text-xs text-gray-400 mt-0.5">
                                                {{ $type->employee_can_upload ? 'À fournir' : 'En attente RH' }}
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center gap-1 flex-shrink-0">
                                    @if($doc)
                                        <a href="{{ route('employee.documents.download', $doc) }}" 
                                           class="p-2 text-violet-600 hover:text-violet-800 rounded-lg hover:bg-violet-50 transition-colors"
                                           title="Télécharger">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                        </a>
                                    @endif

                                    @if($type->employee_can_upload && !$doc)
                                        <a href="{{ route('employee.documents.create', $type) }}" 
                                           class="p-2 text-white rounded-lg transition-colors shadow-sm" style="background-color: #3B8BEB;"
                                           title="Ajouter">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                        </a>
                                    @endif

                                    @if($type->employee_can_upload && $doc && $type->employee_can_delete)
                                        <form action="{{ route('employee.documents.destroy', $doc) }}" method="POST" 
                                              onsubmit="return confirm('Voulez-vous remplacer ce document ?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="p-2 text-gray-400 hover:text-red-600 rounded-lg hover:bg-gray-50 transition-colors"
                                                    title="Remplacer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Lien vers Bulletins -->
        <div class="rounded-2xl shadow-lg overflow-hidden animate-fade-in-up animation-delay-500" style="background-color: #3B8BEB;">
            <div class="p-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-white">Bulletins de Salaire</h3>
                        <p class="text-sm" style="color: #C4DBF6;">Consultez et téléchargez vos fiches de paie</p>
                    </div>
                </div>
                <a href="{{ route('employee.payrolls.index') }}" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-sm font-medium rounded-xl hover:bg-gray-50 transition-colors duration-200 shadow-sm" style="color: #3B8BEB;">
                    Voir mes bulletins
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</x-layouts.employee>
