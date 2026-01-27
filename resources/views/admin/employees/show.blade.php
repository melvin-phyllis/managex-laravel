<x-layouts.admin>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Profil employé</h1>
                <p class="text-gray-500 mt-1">Détails et activités de {{ $employee->name }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.employees.edit', $employee) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white text-sm font-medium rounded-lg hover:bg-yellow-600 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Modifier
                </a>
                <a href="{{ request('from') === 'documents' ? route('admin.documents.index') : route('admin.employees.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Retour
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Profile Card -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="text-center">
                        @if($employee->avatar)
                            <img src="{{ Storage::url($employee->avatar) }}" alt="{{ $employee->name }}" class="w-24 h-24 rounded-full object-cover mx-auto border-4 border-white shadow-lg">
                        @else
                            <div class="w-24 h-24 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center mx-auto shadow-lg">
                                <span class="text-white font-bold text-2xl">{{ strtoupper(substr($employee->name, 0, 2)) }}</span>
                            </div>
                        @endif
                        <h2 class="mt-4 text-xl font-bold text-gray-900">{{ $employee->name }}</h2>
                        <p class="text-gray-500">{{ $employee->poste ?? 'Non défini' }}</p>
                        @if($employee->employee_id)
                            <span class="inline-block mt-2 text-xs bg-gray-100 text-gray-600 px-3 py-1 rounded-full">{{ $employee->employee_id }}</span>
                        @endif

                        <!-- Status Badge -->
                        <div class="mt-3">
                            @php
                                $statusColors = [
                                    'active' => 'bg-green-100 text-green-800',
                                    'on_leave' => 'bg-yellow-100 text-yellow-800',
                                    'suspended' => 'bg-red-100 text-red-800',
                                    'terminated' => 'bg-gray-100 text-gray-800',
                                ];
                                $statusLabels = [
                                    'active' => 'Actif',
                                    'on_leave' => 'En congé',
                                    'suspended' => 'Suspendu',
                                    'terminated' => 'Parti',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$employee->status ?? 'active'] }}">
                                <span class="w-2 h-2 rounded-full mr-2 {{ $employee->status === 'active' ? 'bg-green-500' : ($employee->status === 'on_leave' ? 'bg-yellow-500' : ($employee->status === 'suspended' ? 'bg-red-500' : 'bg-gray-500')) }}"></span>
                                {{ $statusLabels[$employee->status ?? 'active'] }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-6 space-y-4">
                        <div class="flex items-center text-gray-600">
                            <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            {{ $employee->email }}
                        </div>
                        @if($employee->telephone)
                            <div class="flex items-center text-gray-600">
                                <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                {{ $employee->telephone }}
                            </div>
                        @endif
                        @if($employee->department)
                            <div class="flex items-center text-gray-600">
                                <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                {{ $employee->department->name }}
                                @if($employee->position)
                                    <span class="text-gray-400 mx-1">•</span>
                                    {{ $employee->position->name }}
                                @endif
                            </div>
                        @endif
                        @if($employee->hire_date)
                            <div class="flex items-center text-gray-600">
                                <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Embauché le {{ $employee->hire_date->format('d/m/Y') }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Contract Info -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Contrat
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Type</span>
                            <span class="font-medium text-gray-900">
                                @php
                                    $contractLabels = [
                                        'cdi' => 'CDI',
                                        'cdd' => 'CDD',
                                        'stage' => 'Stage',
                                        'alternance' => 'Alternance',
                                        'freelance' => 'Freelance',
                                        'interim' => 'Intérim',
                                    ];
                                @endphp
                                {{ $contractLabels[$employee->contract_type ?? 'cdi'] ?? 'CDI' }}
                            </span>
                        </div>
                        @if($employee->contract_end_date)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Fin de contrat</span>
                                <span class="font-medium text-gray-900">{{ $employee->contract_end_date->format('d/m/Y') }}</span>
                            </div>
                        @endif
                        @if($employee->base_salary)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Salaire brut</span>
                                <span class="font-medium text-gray-900">{{ number_format($employee->base_salary, 2, ',', ' ') }} €</span>
                            </div>
                        @endif
                    </div>

                    <!-- Document du contrat -->
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <p class="text-sm font-medium text-gray-700 mb-3">📄 Document du contrat</p>
                        
                        @php
                            $contract = $employee->currentContract;
                            $hasDocument = $contract && $contract->document_path;
                        @endphp

                        @if($hasDocument)
                            <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <span class="text-xl">📋</span>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $contract->document_original_name }}</p>
                                        <p class="text-xs text-gray-500">Uploadé le {{ $contract->document_uploaded_at?->format('d/m/Y') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.employees.contract.download', $employee) }}"
                                       class="p-2 text-emerald-600 hover:text-emerald-700 transition" title="Télécharger">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.employees.contract.delete', $employee) }}" method="POST"
                                          onsubmit="return confirm('Supprimer ce document ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-red-500 hover:text-red-600 transition" title="Supprimer">
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
                                <div class="flex items-center gap-3">
                                    <input type="file" name="contract_document" accept=".pdf,.doc,.docx"
                                           class="flex-1 text-sm text-gray-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-sm file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                                    <button type="submit"
                                            class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition">
                                        Uploader
                                    </button>
                                </div>
                                <p class="text-xs text-gray-500">PDF, DOC, DOCX • Max 10 Mo</p>
                            </form>
                        @endif

                        @error('contract_document')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Leave Balance -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-gray-600">Congés payés</span>
                                <span class="font-medium text-gray-900">{{ $employee->leave_balance ?? 25 }} jours</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min(100, (($employee->leave_balance ?? 25) / 25) * 100) }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-gray-600">RTT</span>
                                <span class="font-medium text-gray-900">{{ $employee->rtt_balance ?? 0 }} jours</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-purple-600 h-2 rounded-full" style="width: {{ min(100, (($employee->rtt_balance ?? 0) / 12) * 100) }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Emergency Contact -->
                @if($employee->emergency_contact_name)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Contact d'urgence
                    </h3>
                    <div class="space-y-2">
                        <p class="font-medium text-gray-900">{{ $employee->emergency_contact_name }}</p>
                        @if($employee->emergency_contact_relationship)
                            <p class="text-sm text-gray-500">{{ $employee->emergency_contact_relationship }}</p>
                        @endif
                        @if($employee->emergency_contact_phone)
                            <p class="text-gray-600 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                {{ $employee->emergency_contact_phone }}
                            </p>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Stats -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistiques</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Présences ce mois</span>
                            <span class="font-semibold text-gray-900">{{ $employee->presences()->month(now()->month, now()->year)->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Tâches en cours</span>
                            <span class="font-semibold text-gray-900">{{ $employee->tasks()->where('statut', 'approved')->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Congés approuvés</span>
                            <span class="font-semibold text-gray-900">{{ $employee->leaves()->where('statut', 'approved')->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Recent Tasks -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Tâches récentes</h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($employee->tasks()->latest()->take(5)->get() as $task)
                            <div class="p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $task->titre }}</p>
                                        <p class="text-sm text-gray-500">{{ $task->created_at->diffForHumans() }}</p>
                                    </div>
                                    <x-status-badge :status="$task->statut" type="task" />
                                </div>
                                <x-progress-bar :value="$task->progression" class="mt-2" />
                            </div>
                        @empty
                            <div class="p-6 text-center text-gray-500">Aucune tâche</div>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Leaves -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Demandes de congés récentes</h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($employee->leaves()->latest()->take(5)->get() as $leave)
                            <div class="p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $leave->type_label }}</p>
                                        <p class="text-sm text-gray-500">{{ $leave->date_debut->format('d/m/Y') }} - {{ $leave->date_fin->format('d/m/Y') }}</p>
                                    </div>
                                    <x-status-badge :status="$leave->statut" type="leave" />
                                </div>
                            </div>
                        @empty
                            <div class="p-6 text-center text-gray-500">Aucune demande de congé</div>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Payrolls -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Fiches de paie récentes</h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($employee->payrolls()->latest()->take(5)->get() as $payroll)
                            <div class="p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $payroll->periode }}</p>
                                        <p class="text-sm text-gray-500">{{ $payroll->montant_formatted }}</p>
                                    </div>
                                    <x-status-badge :status="$payroll->statut" type="payroll" />
                                </div>
                            </div>
                        @empty
                            <div class="p-6 text-center text-gray-500">Aucune fiche de paie</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
