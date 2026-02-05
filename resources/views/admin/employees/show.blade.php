<x-layouts.admin>
    @php
        $statusColors = [
            'active' => ['bg' => 'bg-emerald-500', 'text' => 'text-emerald-700', 'bgLight' => 'bg-emerald-100', 'gradient' => 'from-emerald-400 to-emerald-600'],
            'on_leave' => ['bg' => 'bg-amber-500', 'text' => 'text-amber-700', 'bgLight' => 'bg-amber-100', 'gradient' => 'from-amber-400 to-amber-600'],
            'suspended' => ['bg' => 'bg-red-500', 'text' => 'text-red-700', 'bgLight' => 'bg-red-100', 'gradient' => 'from-red-400 to-red-600'],
            'terminated' => ['bg' => 'bg-gray-500', 'text' => 'text-gray-700', 'bgLight' => 'bg-gray-100', 'gradient' => 'from-gray-400 to-gray-600'],
        ];
        $statusLabels = [
            'active' => 'Actif',
            'on_leave' => 'En congé',
            'suspended' => 'Suspendu',
            'terminated' => 'Parti',
        ];
        $currentStatus = $employee->status ?? 'active';
        $statusConfig = $statusColors[$currentStatus] ?? $statusColors['active'];
        
        $contractLabels = [
            'cdi' => 'CDI',
            'cdd' => 'CDD',
            'stage' => 'Stage',
            'alternance' => 'Alternance',
            'freelance' => 'Freelance',
            'interim' => 'Intérim',
        ];
    @endphp

    <div class="space-y-6">
        <!-- Header avec profil intégré -->
        <div class="relative overflow-hidden rounded-2xl shadow-xl" style="background: linear-gradient(135deg, #5680E9, #84CEEB) !important;">
            <div class="absolute inset-0 bg-black/10"></div>
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            
            <div class="relative p-6 md:p-8" style="background-color: var(--tw-ring-color)">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                    <!-- Profil principal -->
                    <div class="flex items-center gap-5">
                        <div class="relative">
                            @if($employee->avatar)
                                <img src="{{ avatar_url($employee->avatar) }}" alt="{{ $employee->name }}" 
                                     class="w-20 h-20 md:w-24 md:h-24 rounded-2xl object-cover border-4 border-white/30 shadow-2xl">
                            @else
                                <div class="w-20 h-20 md:w-24 md:h-24 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center border-4 border-white/30 shadow-2xl">
                                    <span class="text-white font-bold text-2xl md:text-3xl">{{ strtoupper(substr($employee->name, 0, 2)) }}</span>
                                </div>
                            @endif
                            <!-- Status indicator -->
                            <div class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full {{ $statusConfig['bg'] }} border-3 border-white shadow-lg flex items-center justify-center">
                                @if($currentStatus === 'active')
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                @endif
                            </div>
                        </div>
                        <div>
                            <h1 class="text-2xl md:text-3xl font-bold text-white text-center">{{ $employee->name }}</h1>
                            <p class="text-white/80 mt-1 text-center">{{ $employee->poste ?? 'Poste non défini' }}</p>
                            <div class="flex flex-wrap items-center gap-2 mt-2 justify-center align-middle">
                                @if($employee->employee_id)
                                    <span class="px-3 py-1 bg-white/20 backdrop-blur-sm text-white text-xs font-medium rounded-full">
                                        #{{ $employee->employee_id }}
                                    </span>
                                @endif
                                <span class="px-3 py-1 bg-white/20 backdrop-blur-sm text-white text-xs font-medium rounded-full">
                                    {{ $contractLabels[$employee->contract_type ?? 'cdi'] ?? 'CDI' }}
                                </span>
                                <span class="px-3 py-1 {{ $statusConfig['bgLight'] }} {{ $statusConfig['text'] }} text-xs font-semibold rounded-full">
                                    {{ $statusLabels[$currentStatus] }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.employees.edit', $employee) }}" 
                           class="inline-flex items-center px-5 py-2.5 bg-white font-semibold rounded-xl hover:bg-purple-50 transition-all shadow-lg" style="color: #5680E9;">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Modifier
                        </a>
                        <a href="{{ request('from') === 'documents' ? route('admin.documents.index') : route('admin.employees.index') }}" 
                           class="inline-flex items-center px-5 py-2.5 bg-white/20 backdrop-blur-sm text-white font-semibold rounded-xl hover:bg-white/30 transition-all">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats rapides -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/30">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $employee->presences()->month(now()->month, now()->year)->count() }}</p>
                        <p class="text-xs text-gray-500">Présences ce mois</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center shadow-lg shadow-violet-500/30">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $employee->tasks()->where('statut', 'approved')->count() }}</p>
                        <p class="text-xs text-gray-500">Tâches en cours</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-lg shadow-emerald-500/30">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $employee->leaves()->where('statut', 'approved')->count() }}</p>
                        <p class="text-xs text-gray-500">Congés approuvés</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center shadow-lg shadow-amber-500/30">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $employee->payrolls()->count() }}</p>
                        <p class="text-xs text-gray-500">Fiches de paie</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Colonne gauche - Infos -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Coordonnées -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-5 py-3" style="background: linear-gradient(135deg, #5680E9, #84CEEB);">
                        <h3 class="text-white font-semibold flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Coordonnées
                        </h3>
                    </div>
                    <div class="p-5 space-y-4">
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                            <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs text-gray-500">Email</p>
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $employee->email }}</p>
                            </div>
                        </div>
                        
                        @if($employee->telephone)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                            <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Téléphone</p>
                                <p class="text-sm font-medium text-gray-900">{{ $employee->telephone }}</p>
                            </div>
                        </div>
                        @endif
                        
                        @if($employee->department)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                            <div class="w-10 h-10 rounded-lg bg-violet-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Département</p>
                                <p class="text-sm font-medium text-gray-900">{{ $employee->department->name }}</p>
                                @if($employee->position)
                                    <p class="text-xs text-gray-500">{{ $employee->position->name }}</p>
                                @endif
                            </div>
                        </div>
                        @endif
                        
                        @if($employee->hire_date)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                            <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Date d'embauche</p>
                                <p class="text-sm font-medium text-gray-900">{{ $employee->hire_date->format('d/m/Y') }}</p>
                                <p class="text-xs text-gray-500">{{ (int) $employee->hire_date->diffInYears(now()) }} ans d'ancienneté</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Contrat -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-5 py-3" style="background: linear-gradient(135deg, #8860D0, #C1C8E4);">
                        <h3 class="text-white font-semibold flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Informations contractuelles
                        </h3>
                    </div>
                    <div class="p-5 space-y-4">
                        <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                            <span class="text-sm text-purple-700">Type de contrat</span>
                            <span class="px-3 py-1 bg-purple-600 text-white text-sm font-semibold rounded-full">
                                {{ $contractLabels[$employee->contract_type ?? 'cdi'] ?? 'CDI' }}
                            </span>
                        </div>
                        
                        @if($employee->contract_end_date)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm text-gray-600">Fin de contrat</span>
                            <span class="font-semibold text-gray-900">{{ $employee->contract_end_date->format('d/m/Y') }}</span>
                        </div>
                        @endif
                        
                        @if($employee->base_salary)
                        <div class="flex items-center justify-between p-3 bg-emerald-50 rounded-lg">
                            <span class="text-sm text-emerald-700">Salaire brut</span>
                            <span class="font-bold text-emerald-700">{{ number_format($employee->base_salary, 0, ',', ' ') }} FCFA</span>
                        </div>
                        @endif

                        <!-- Document du contrat -->
                        <div class="pt-4 border-t border-gray-100">
                            <p class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                Document du contrat
                            </p>
                            
                            @php
                                $contract = $employee->currentContract;
                                $hasDocument = $contract && $contract->document_path;
                            @endphp

                            @if($hasDocument)
                                <div class="flex items-center justify-between p-3 bg-emerald-50 border border-emerald-200 rounded-xl">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-emerald-500 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $contract->document_original_name }}</p>
                                            <p class="text-xs text-gray-500">Uploadé le {{ $contract->document_uploaded_at?->format('d/m/Y') }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <a href="{{ route('admin.employees.contract.download', $employee) }}"
                                           class="p-2 text-emerald-600 hover:bg-emerald-100 rounded-lg transition" title="Télécharger">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.employees.contract.delete', $employee) }}" method="POST"
                                              onsubmit="return confirm('Supprimer ce document ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition" title="Supprimer">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @else
                                <form action="{{ route('admin.employees.contract.upload', $employee) }}" method="POST" 
                                      enctype="multipart/form-data" class="space-y-3">
                                    @csrf
                                    <div class="p-4 border-2 border-dashed border-gray-200 rounded-xl hover:border-purple-400 transition-colors">
                                        <input type="file" name="contract_document" accept=".pdf,.doc,.docx"
                                               class="w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-purple-100 file:text-purple-700 hover:file:bg-purple-200 cursor-pointer">
                                        <p class="text-xs text-gray-500 mt-2">PDF, DOC, DOCX • Max 10 Mo</p>
                                    </div>
                                    <button type="submit"
                                            class="w-full px-4 py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white text-sm font-semibold rounded-xl transition-all shadow-lg shadow-purple-500/30">
                                        Uploader le document
                                    </button>
                                </form>
                            @endif

                            @error('contract_document')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Solde congés -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-5 py-3" style="background: linear-gradient(135deg, #5AB9EA, #84CEEB);">
                        <h3 class="text-white font-semibold flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Solde de congés
                        </h3>
                    </div>
                    <div class="p-5 space-y-4">
                        <div class="p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-blue-700">Congés payés</span>
                                <span class="text-lg font-bold text-blue-700">{{ $employee->leave_balance ?? 25 }} jours</span>
                            </div>
                            <div class="w-full bg-blue-200 rounded-full h-2.5">
                                <div class="bg-gradient-to-r from-blue-500 to-indigo-500 h-2.5 rounded-full transition-all" style="width: {{ min(100, (($employee->leave_balance ?? 25) / 25) * 100) }}%"></div>
                            </div>
                        </div>
                        <div class="p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-purple-700">RTT</span>
                                <span class="text-lg font-bold text-purple-700">{{ $employee->rtt_balance ?? 0 }} jours</span>
                            </div>
                            <div class="w-full bg-purple-200 rounded-full h-2.5">
                                <div class="bg-gradient-to-r from-purple-500 to-pink-500 h-2.5 rounded-full transition-all" style="width: {{ min(100, (($employee->rtt_balance ?? 0) / 12) * 100) }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact d'urgence -->
                @if($employee->emergency_contact_name)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-red-500 to-rose-500 px-5 py-3">
                        <h3 class="text-white font-semibold flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Contact d'urgence
                        </h3>
                    </div>
                    <div class="p-5">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-red-400 to-rose-500 flex items-center justify-center shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900">{{ $employee->emergency_contact_name }}</p>
                                @if($employee->emergency_contact_relationship)
                                    <p class="text-sm text-gray-500">{{ $employee->emergency_contact_relationship }}</p>
                                @endif
                            </div>
                        </div>
                        @if($employee->emergency_contact_phone)
                            <div class="mt-4 p-3 bg-red-50 rounded-xl flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-red-500 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                </div>
                                <span class="font-medium text-red-700">{{ $employee->emergency_contact_phone }}</span>
                            </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <!-- Colonne droite - Activités -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Tâches récentes -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-5 py-4 flex items-center justify-between" style="background: linear-gradient(135deg, #8860D0, #5680E9);">
                        <h3 class="text-white font-semibold flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            Tâches récentes
                        </h3>
                        <span class="px-2 py-1 bg-white/20 text-white text-xs font-medium rounded-full">
                            {{ $employee->tasks()->count() }} total
                        </span>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($employee->tasks()->latest()->take(5)->get() as $task)
                            <div class="p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex items-start gap-3 flex-1 min-w-0">
                                        @php
                                            $taskColors = [
                                                'pending' => 'from-amber-400 to-orange-500',
                                                'approved' => 'from-blue-400 to-blue-600',
                                                'in_progress' => 'from-indigo-400 to-purple-500',
                                                'completed' => 'from-emerald-400 to-teal-500',
                                                'cancelled' => 'from-gray-400 to-gray-500',
                                            ];
                                        @endphp
                                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br {{ $taskColors[$task->statut] ?? 'from-gray-400 to-gray-500' }} flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-gray-900 truncate">{{ $task->titre }}</p>
                                            <p class="text-xs text-gray-500">{{ $task->created_at->diffForHumans() }}</p>
                                            <!-- Progress bar -->
                                            <div class="mt-2 flex items-center gap-2">
                                                <div class="flex-1 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                                    <div class="h-full bg-gradient-to-r {{ $taskColors[$task->statut] ?? 'from-gray-400 to-gray-500' }} rounded-full transition-all" style="width: {{ $task->progression ?? 0 }}%"></div>
                                                </div>
                                                <span class="text-xs font-medium text-gray-500">{{ $task->progression ?? 0 }}%</span>
                                            </div>
                                        </div>
                                    </div>
                                    <x-status-badge :status="$task->statut" type="task" />
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center">
                                <div class="w-16 h-16 mx-auto rounded-full bg-violet-100 flex items-center justify-center mb-3">
                                    <svg class="w-8 h-8 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                                <p class="text-gray-500">Aucune tâche assignée</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Congés récents -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-5 py-4 flex items-center justify-between" style="background: linear-gradient(135deg, #5AB9EA, #5680E9);">
                        <h3 class="text-white font-semibold flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Demandes de congés
                        </h3>
                        <span class="px-2 py-1 bg-white/20 text-white text-xs font-medium rounded-full">
                            {{ $employee->leaves()->count() }} total
                        </span>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($employee->leaves()->latest()->take(5)->get() as $leave)
                            <div class="p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        @php
                                            $leaveColors = [
                                                'pending' => 'from-amber-400 to-orange-500',
                                                'approved' => 'from-emerald-400 to-teal-500',
                                                'rejected' => 'from-red-400 to-rose-500',
                                            ];
                                        @endphp
                                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br {{ $leaveColors[$leave->statut] ?? 'from-gray-400 to-gray-500' }} flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $leave->type_label }}</p>
                                            <p class="text-xs text-gray-500">
                                                {{ $leave->date_debut->format('d/m/Y') }} 
                                                <span class="text-gray-400">→</span> 
                                                {{ $leave->date_fin->format('d/m/Y') }}
                                            </p>
                                        </div>
                                    </div>
                                    <x-status-badge :status="$leave->statut" type="leave" />
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center">
                                <div class="w-16 h-16 mx-auto rounded-full bg-teal-100 flex items-center justify-center mb-3">
                                    <svg class="w-8 h-8 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <p class="text-gray-500">Aucune demande de congé</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Fiches de paie récentes -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-5 py-4 flex items-center justify-between" style="background: linear-gradient(135deg, #84CEEB, #5AB9EA);">
                        <h3 class="text-white font-semibold flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Fiches de paie
                        </h3>
                        <span class="px-2 py-1 bg-white/20 text-white text-xs font-medium rounded-full">
                            {{ $employee->payrolls()->count() }} total
                        </span>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($employee->payrolls()->latest()->take(5)->get() as $payroll)
                            <div class="p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        @php
                                            $payrollColors = [
                                                'draft' => 'from-gray-400 to-gray-500',
                                                'pending' => 'from-amber-400 to-orange-500',
                                                'paid' => 'from-emerald-400 to-green-500',
                                                'cancelled' => 'from-red-400 to-rose-500',
                                            ];
                                        @endphp
                                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br {{ $payrollColors[$payroll->statut] ?? 'from-gray-400 to-gray-500' }} flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $payroll->periode }}</p>
                                            <p class="text-sm font-semibold text-emerald-600">{{ $payroll->montant_formatted }}</p>
                                        </div>
                                    </div>
                                    <x-status-badge :status="$payroll->statut" type="payroll" />
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center">
                                <div class="w-16 h-16 mx-auto rounded-full bg-emerald-100 flex items-center justify-center mb-3">
                                    <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <p class="text-gray-500">Aucune fiche de paie</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
