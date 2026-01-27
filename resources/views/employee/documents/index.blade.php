<x-layouts.employee>
    <div class="space-y-6">
        <!-- Breadcrumbs -->
        <nav class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('employee.dashboard') }}" class="flex items-center gap-1 hover:text-gray-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Accueil
            </a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="flex items-center gap-1 text-gray-900 font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Mes Documents
            </span>
        </nav>

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">📁 Mes Documents</h1>
                <p class="text-gray-500 mt-1">Vos documents contractuels et professionnels</p>
            </div>
            <a href="{{ route('employee.document-requests.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-2m-4-1v8m0 0l3-3m-3 3L9 8m-5 5h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 00.707.293h3.172a1 1 0 00.707-.293l2.414-2.414a1 1 0 01.707-.293H20"/>
                </svg>
                Demander un document
            </a>
        </div>

        <!-- Mon Contrat de Travail -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex items-center gap-3 bg-purple-50">
                <span class="text-2xl">📋</span>
                <div>
                    <h2 class="font-semibold text-gray-900">Mon Contrat de Travail</h2>
                    <p class="text-sm text-gray-500">Document de votre contrat signé</p>
                </div>
            </div>

            <div class="p-4">
                @if($hasContractDocument)
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <span class="text-xl">✅</span>
                            <div>
                                <p class="font-medium text-gray-900">{{ $contract->document_original_name }}</p>
                                <p class="text-sm text-gray-500">Ajouté le {{ $contract->document_uploaded_at?->format('d/m/Y') }}</p>
                            </div>
                        </div>
                        <a href="{{ route('employee.documents.download-contract') }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Télécharger
                        </a>
                    </div>
                @else
                    <div class="flex items-center gap-3 p-3 bg-amber-50 rounded-lg">
                        <span class="text-xl">⏳</span>
                        <p class="text-gray-600">Votre contrat sera disponible une fois uploadé par les RH.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Documents de l'Entreprise (Global Documents) -->
        @if($globalDocuments->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex items-center gap-3 bg-emerald-50">
                <span class="text-2xl">🏢</span>
                <div>
                    <h2 class="font-semibold text-gray-900">Documents de l'Entreprise</h2>
                    <p class="text-sm text-gray-500">Règlement intérieur, chartes et politiques</p>
                </div>
            </div>

            <div class="divide-y divide-gray-100">
                @foreach($globalDocuments as $doc)
                    @php
                        $isAcknowledged = in_array($doc->id, $acknowledgedIds);
                    @endphp
                    <div class="p-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center
                                    @if($isAcknowledged) bg-green-100 @else bg-amber-100 @endif">
                                    @if($isAcknowledged)
                                        <span class="text-lg">✅</span>
                                    @else
                                        <span class="text-lg">⏳</span>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $doc->title }}</p>
                                    <p class="text-sm text-gray-500">{{ $doc->type_label }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('employee.global-documents.download', $doc) }}" 
                                   class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    Télécharger
                                </a>
                                @if(!$isAcknowledged)
                                    <form action="{{ route('employee.global-documents.acknowledge', $doc) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="inline-flex items-center px-3 py-1.5 bg-emerald-600 text-white text-sm rounded-lg hover:bg-emerald-700 transition-colors">
                                            ✓ Accuser réception
                                        </button>
                                    </form>
                                @else
                                    <span class="inline-flex items-center px-3 py-1.5 bg-green-100 text-green-700 text-sm rounded-lg">
                                        ✅ Lu
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Mes Documents Personnels -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex items-center gap-3">
                <span class="text-2xl">📝</span>
                <div>
                    <h2 class="font-semibold text-gray-900">Mes Documents Personnels</h2>
                    <p class="text-sm text-gray-500">CV, pièces d'identité et autres documents</p>
                </div>
            </div>

            <div class="divide-y divide-gray-100">
                @foreach($documentTypes as $type)
                    @php
                        $doc = $userDocuments[$type->id] ?? null;
                    @endphp

                    <div class="p-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <!-- Status Icon -->
                                <div class="w-10 h-10 rounded-full flex items-center justify-center
                                    @if($doc) bg-green-100 @else bg-gray-100 @endif">
                                    @if($doc)
                                        <span class="text-lg">✅</span>
                                    @else
                                        <span class="text-lg">⚪</span>
                                    @endif
                                </div>

                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium text-gray-900">{{ $type->name }}</span>
                                        @if($type->is_required)
                                            <span class="px-1.5 py-0.5 text-xs bg-red-100 text-red-700 rounded">Requis</span>
                                        @endif
                                    </div>
                                    @if($doc)
                                        <p class="text-sm text-gray-500">
                                            {{ $doc->original_filename }}
                                            <span class="mx-1">•</span>
                                            Ajouté le {{ $doc->created_at->format('d/m/Y') }}
                                        </p>
                                    @else
                                        <p class="text-sm text-gray-400">
                                            @if($type->employee_can_upload)
                                                Non fourni
                                            @else
                                                En attente (fourni par RH)
                                            @endif
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-2">
                                @if($doc)
                                    <a href="{{ route('employee.documents.download', $doc) }}" 
                                       class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                        Télécharger
                                    </a>
                                @endif

                                @if($type->employee_can_upload && !$doc)
                                    <a href="{{ route('employee.documents.create', $type) }}" 
                                       class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Fournir
                                    </a>
                                @endif

                                @if($type->employee_can_upload && $doc && $type->employee_can_delete)
                                    <form action="{{ route('employee.documents.destroy', $doc) }}" method="POST" 
                                          onsubmit="return confirm('Voulez-vous remplacer ce document ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200 transition-colors">
                                            Modifier
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Quick Link to Payslips -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <span class="text-2xl">💰</span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Bulletins de Salaire</h3>
                        <p class="text-sm text-gray-500">Consultez vos fiches de paie</p>
                    </div>
                </div>
                <a href="{{ route('employee.payrolls.index') }}" 
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Voir mes bulletins →
                </a>
            </div>
        </div>
    </div>
</x-layouts.employee>
