<x-layouts.admin>
    <div class="space-y-6" x-data="employeeTable()">
        <!-- Breadcrumbs -->
        <nav class="flex animate-fade-in-up" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Dashboard
                    </a>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Employés</span>
                    </div>
                </li>
            </ol>
        </nav>
        <!-- Header -->
        <x-table-header title="Gestion des Employés" subtitle="Gérez et suivez toute votre équipe de maniére centralisée" class="animate-fade-in-up animation-delay-100">
            <x-slot:icon>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-lg transform transition-transform" style="background-image: linear-gradient(135deg, #5680E9, #84CEEB) !important; box-shadow: 0 10px 15px -3px rgba(86, 128, 233, 0.3) !important;">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </x-slot:icon>
            <x-slot:actions>
                <div class="flex gap-3">
                    <a href="{{ route('admin.employees.export', request()->only(['department_id', 'status', 'contract_type'])) }}" class="inline-flex items-center px-4 py-2.5 bg-white text-gray-700 font-medium rounded-xl border border-gray-200 hover:bg-gray-50 transition-all shadow-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Exporter CSV
                    </a>
                    @if(Route::has('admin.employee-invitations.create'))
                    <a href="{{ route('admin.employee-invitations.create') }}" class="inline-flex items-center px-4 py-2.5 bg-white text-blue-700 font-medium rounded-xl border border-blue-200 hover:bg-blue-50 transition-all shadow-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Inviter par email
                    </a>
                    @endif
                    <a href="{{ route('admin.employees.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all shadow-lg shadow-blue-500/25">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Ajouter un employe
                    </a>
                </div>
            </x-slot:actions>
        </x-table-header>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            <!-- Total Employés -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow group animate-fade-in-up animation-delay-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total employés</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform" style="background-image: linear-gradient(135deg, #5680E9, #5AB9EA) !important; box-shadow: 0 10px 15px -3px rgba(86, 128, 233, 0.3) !important;">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <span class="text-[#5AB9EA] flex items-center bg-[#5AB9EA]/10 px-2 py-0.5 rounded-full">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                        Actifs
                    </span>
                    <span class="text-gray-400 mx-2"></span>
                    <span class="text-gray-500">Effectif total</span>
                </div>
            </div>



            <!-- En Congé -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow group animate-fade-in-up animation-delay-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">En congé</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['on_leave'] }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform" style="background-image: linear-gradient(135deg, #8860D0, #5680E9) !important; box-shadow: 0 10px 15px -3px rgba(136, 96, 208, 0.3) !important;">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <span class="text-[#8860D0] bg-[#8860D0]/10 px-2 py-0.5 rounded-full font-medium">
                        Planifiés
                    </span>
                    <span class="text-gray-400 mx-2"></span>
                    <span class="text-gray-500">Absences validées</span>
                </div>
            </div>

            <!-- Nouveaux -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow group animate-fade-in-up animation-delay-400">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Nouveaux (Mois)</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['new_this_month'] }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform" style="background-image: linear-gradient(135deg, #C1C8E4, #84CEEB) !important; box-shadow: 0 10px 15px -3px rgba(193, 200, 228, 0.3) !important;">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <span class="text-[#5680E9] bg-[#C1C8E4]/20 px-2 py-0.5 rounded-full font-medium">
                        +{{ $stats['new_this_month'] }}
                    </span>
                    <span class="text-gray-400 mx-2">•</span>
                    <span class="text-gray-500">Recrutements</span>
                </div>
            </div>
        </div>

        <!-- Filter Bar -->
        <x-filter-bar :hasActiveFilters="request()->hasAny(['search', 'department_id', 'status', 'contract_type'])" class="animate-fade-in-up animation-delay-200">
            <x-slot:filters>
                <input type="hidden" name="sort" value="{{ request('sort') }}">
                <input type="hidden" name="direction" value="{{ request('direction') }}">
                
                <!-- Search -->
                <div class="flex-1 min-w-[200px] relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Rechercher un employé..." 
                           class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>

                <!-- Department -->
                <div class="w-full sm:w-auto">
                    <select name="department_id" class="w-full sm:w-48 px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white">
                        <option value="">Tous les départements</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status -->
                <div class="w-full sm:w-auto">
                    <select name="status" class="w-full sm:w-40 px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white">
                        <option value="">Tous statuts</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                        <option value="on_leave" {{ request('status') == 'on_leave' ? 'selected' : '' }}>En congé</option>
                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspendu</option>
                        <option value="terminated" {{ request('status') == 'terminated' ? 'selected' : '' }}>Terminé</option>
                    </select>
                </div>

                <!-- Contract -->
                <div class="w-full sm:w-auto">
                    <select name="contract_type" class="w-full sm:w-40 px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white">
                        <option value="">Type contrat</option>
                        <option value="cdi" {{ request('contract_type') == 'cdi' ? 'selected' : '' }}>CDI</option>
                        <option value="cdd" {{ request('contract_type') == 'cdd' ? 'selected' : '' }}>CDD</option>
                        <option value="stage" {{ request('contract_type') == 'stage' ? 'selected' : '' }}>Stage</option>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="flex gap-2">
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-xl hover:bg-blue-700 transition-colors shadow-lg shadow-blue-500/25 flex items-center">
                        Filtrer
                    </button>
                    @if(request()->hasAny(['search', 'department_id', 'status', 'contract_type']))
                        <a href="{{ route('admin.employees.index') }}" class="px-4 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 transition-colors flex items-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </a>
                    @endif
                </div>
            </x-slot:filters>

            <x-slot:activeFilters>
                @if(request('search'))
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                        Recherche: {{ request('search') }}
                    </span>
                @endif
                @if(request('department_id'))
                    @php $dept = $departments->find(request('department_id')); @endphp
                    @if($dept)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[#5680E9]/10 text-[#5680E9] border border-[#5680E9]/20">
                            Dépt: {{ $dept->name }}
                        </span>
                    @endif
                @endif
                @if(request('status'))
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[#5AB9EA]/10 text-[#5AB9EA] border border-[#5AB9EA]/20">
                        Statut: {{ request('status') }}
                    </span>
                @endif
            </x-slot:activeFilters>
        </x-filter-bar>

        <!-- Table -->
        <x-data-table class="animate-fade-in-up animation-delay-300">
            <x-slot:bulkActions>
                <button @click="deleteSelected" class="text-red-600 hover:text-red-800 font-medium text-sm flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Supprimer la sélection
                </button>
                <div class="h-4 w-px bg-blue-200 mx-3"></div>
                <button @click="exportSelected" class="text-blue-700 hover:text-blue-900 font-medium text-sm flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Exporter
                </button>
            </x-slot:bulkActions>

            <x-slot:header>
                <tr>
                    <th class="w-12 px-6 py-4">
                        <input type="checkbox" @change="toggleAll" x-model="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                        <div class="flex items-center gap-1">
                            Employé
                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Département & Poste</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Contrat</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Statut</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Contact</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
            </x-slot:header>

            <x-slot:body>
                @forelse($employees as $employee)
                    <tr class="hover:bg-blue-50/50 transition-colors group">
                        <td class="px-6 py-4">
                            <input type="checkbox" value="{{ $employee->id }}" x-model="selected" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0 h-10 w-10 relative">
                                    @if($employee->avatar)
                                        <img class="h-10 w-10 rounded-full object-cover ring-2 ring-white shadow-sm" src="{{ avatar_url($employee->avatar) }}" alt="{{ $employee->name }}">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center ring-2 ring-white shadow-sm">
                                            <span class="text-white font-bold text-xs">{{ strtoupper(substr($employee->name, 0, 2)) }}</span>
                                        </div>
                                    @endif
                                    <!-- Status Dot -->
                                    <span class="absolute -bottom-0.5 -right-0.5 block h-3 w-3 rounded-full ring-2 ring-white
                                        @if($employee->presence_status === 'present') bg-green-500
                                        @elseif($employee->presence_status === 'on_leave') bg-amber-500
                                        @else bg-gray-300
                                        @endif">
                                    </span>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium text-gray-900 {{ in_array($employee->status, ['suspended', 'terminated']) ? 'line-through opacity-60' : '' }}">{{ $employee->name }}</span>
                                        @if($employee->status === 'suspended')
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-orange-100 text-orange-700 border border-orange-200" title="Ce compte est suspendu">
                                                Suspendu
                                            </span>
                                        @elseif($employee->status === 'terminated')
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-red-100 text-red-700 border border-red-200" title="Ce compte est désactivé">
                                                Parti
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-500 font-mono">{{ $employee->employee_id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($employee->department)
                                <div class="text-sm text-gray-900">{{ $employee->department->name }}</div>
                            @else
                                <div class="text-sm text-gray-400">-</div>
                            @endif
                            <div class="text-xs text-gray-500">{{ $employee->position->name ?? 'Aucun poste' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $badges = [
                                    'cdi' => 'bg-[#5680E9]/10 text-[#5680E9] border-[#5680E9]/20',
                                    'cdd' => 'bg-[#84CEEB]/10 text-[#5680E9] border-[#84CEEB]/20',
                                    'stage' => 'bg-[#C1C8E4]/20 text-[#5680E9] border-[#C1C8E4]/30',
                                    'alternance' => 'bg-[#5AB9EA]/10 text-[#5AB9EA] border-[#5AB9EA]/20',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $badges[$employee->contract_type] ?? 'bg-gray-100 text-gray-700 border-gray-200' }}">
                                {{ strtoupper($employee->contract_type ?? 'N/A') }}
                            </span>
                            <div class="text-xs text-gray-400 mt-1">
                                {{ $employee->hire_date ? 'Depuis ' . $employee->hire_date->format('M Y') : '-' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($employee->presence_status === 'present')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-[#5AB9EA]/10 text-[#5AB9EA] border border-[#5AB9EA]/20">
                                    <span class="relative flex h-2 w-2">
                                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#5AB9EA] opacity-75"></span>
                                      <span class="relative inline-flex rounded-full h-2 w-2 bg-[#5AB9EA]"></span>
                                    </span>
                                    Présent
                                </span>
                            @elseif($employee->presence_status === 'on_leave')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-[#8860D0]/10 text-[#8860D0] border border-[#8860D0]/20">
                                    <span class="h-2 w-2 bg-[#8860D0] rounded-full"></span>
                                    En congé
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-50 text-gray-600 border border-gray-100">
                                    <span class="h-2 w-2 bg-gray-400 rounded-full"></span>
                                    Absent
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-1">
                                @if($employee->email)
                                    <a href="mailto:{{ $employee->email }}" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="{{ $employee->email }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 00-2-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    </a>
                                @endif
                                @if($employee->telephone)
                                    <a href="tel:{{ $employee->telephone }}" class="p-1.5 text-gray-400 hover:text-[#5AB9EA] hover:bg-[#5AB9EA]/10 rounded-lg transition-colors" title="{{ $employee->telephone }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    </a>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('admin.employees.show', $employee) }}" class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Voir">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('admin.employees.edit', $employee) }}" class="p-1.5 text-gray-500 hover:text-[#8860D0] hover:bg-[#8860D0]/10 rounded-lg transition-colors" title="éditer">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </a>
                                {{-- Bouton Activer/Suspendre --}}
                                @if($employee->status === 'active')
                                    <button type="button" 
                                            @click="confirmStatusChange('{{ route('admin.employees.toggle-status', $employee) }}', 'suspend', {{ Js::from($employee->name) }})"
                                            class="p-1.5 text-gray-500 hover:text-[#5680E9] hover:bg-[#5680E9]/10 rounded-lg transition-colors" 
                                            title="Suspendre le compte">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                    </button>
                                @else
                                    <button type="button" 
                                            @click="confirmStatusChange('{{ route('admin.employees.toggle-status', $employee) }}', 'activate', {{ Js::from($employee->name) }})"
                                            class="p-1.5 text-[#5AB9EA] hover:text-[#5AB9EA] hover:bg-[#5AB9EA]/10 rounded-lg transition-colors" 
                                            title="Activer le compte">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </button>
                                @endif
                                <button type="button" 
                                        @click="confirmDelete('{{ route('admin.employees.destroy', $employee) }}')"
                                        class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" 
                                        title="Supprimer">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <p class="text-lg font-medium text-gray-900">Aucun employé trouvé</p>
                                <p class="text-sm text-gray-500 mt-1">Essayez de modifier vos filtres ou d'ajouter un nouvel employé.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </x-slot:body>

            <x-slot:pagination>
                {{ $employees->links() }}
            </x-slot:pagination>
        </x-data-table>

    <!-- Scripts pour la gestion du tableau -->
    <script nonce="{{ $cspNonce ?? '' }}">
        function employeeTable() {
            return {
                selected: [],
                selectAll: false,

                get selectedCount() {
                    return this.selected.length;
                },

                toggleAll() {
                    // Récupérer toutes les checkboxes du tableau (excluant celle du header)
                    const checks = document.querySelectorAll('input[type="checkbox"][x-model="selected"]');
                    if (this.selectAll) {
                        this.selected = Array.from(checks).map(el => el.value);
                    } else {
                        this.selected = [];
                    }
                },

                deleteSelected() {
                    if (confirm(`Voulez-vous vraiment supprimer ${this.selectedCount} employé(s) ?`)) {
                        // Ici, vous pourriez faire un appel AJAX ou soumettre un formulaire invisible
                        alert('Fonctionnalité en cours de développement');
                    }
                },

                exportSelected() {
                    alert('Exportation de ' + this.selectedCount + ' éléments...');
                },

                // Delete Modal Logic
                showDeleteModal: false,
                deleteUrl: '',
                confirmDelete(url) {
                    this.deleteUrl = url;
                    this.showDeleteModal = true;
                },

                // Status Change Modal Logic
                showStatusModal: false,
                statusUrl: '',
                statusAction: '', // 'suspend' ou 'activate'
                statusEmployeeName: '',
                confirmStatusChange(url, action, employeeName) {
                    this.statusUrl = url;
                    this.statusAction = action;
                    this.statusEmployeeName = employeeName;
                    this.showStatusModal = true;
                }
            }
        }
    </script>

    <!-- Delete Confirmation Modal -->
    <div x-show="showDeleteModal" 
         class="fixed inset-0 z-[100] overflow-y-auto" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true"
         style="display: none;">
        
        <!-- Backdrop -->
        <div x-show="showDeleteModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
             @click="showDeleteModal = false"></div>

        <div class="flex min-h-screen items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div x-show="showDeleteModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">Confirmer la suppression</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">Êtes-vous sûr de vouloir supprimer cet employé ? Cette action est irréversible.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <form :action="deleteUrl" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto">
                            Supprimer
                        </button>
                    </form>
                    <button type="button" 
                            @click="showDeleteModal = false"
                            class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Change Confirmation Modal -->
    <div x-show="showStatusModal" 
         class="fixed inset-0 z-[100] overflow-y-auto" 
         aria-labelledby="status-modal-title" 
         role="dialog" 
         aria-modal="true"
         style="display: none;">
        
        <!-- Backdrop -->
        <div x-show="showStatusModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
             @click="showStatusModal = false"></div>

        <div class="flex min-h-screen items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div x-show="showStatusModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <!-- Icône dynamique selon l'action -->
                        <template x-if="statusAction === 'suspend'">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-orange-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                </svg>
                            </div>
                        </template>
                        <template x-if="statusAction === 'activate'">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </template>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-base font-semibold leading-6 text-gray-900" id="status-modal-title">
                                <span x-show="statusAction === 'suspend'">Suspendre le compte</span>
                                <span x-show="statusAction === 'activate'">Activer le compte</span>
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500" x-show="statusAction === 'suspend'">
                                    Êtes-vous sûr de vouloir suspendre le compte de <span class="font-medium text-gray-700" x-text="statusEmployeeName"></span> ?
                                    <br><span class="text-orange-600">L'employé ne pourra plus se connecter à l'application.</span>
                                </p>
                                <p class="text-sm text-gray-500" x-show="statusAction === 'activate'">
                                    Êtes-vous sûr de vouloir réactiver le compte de <span class="font-medium text-gray-700" x-text="statusEmployeeName"></span> ?
                                    <br><span class="text-green-600">L'employé pourra à nouveau se connecter.</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <form :action="statusUrl" method="POST" class="inline">
                        @csrf
                        <button type="submit" 
                                class="inline-flex w-full justify-center rounded-md px-3 py-2 text-sm font-semibold text-white shadow-sm sm:ml-3 sm:w-auto"
                                :class="statusAction === 'suspend' ? 'bg-orange-600 hover:bg-orange-500' : 'bg-green-600 hover:bg-green-500'">
                            <span x-show="statusAction === 'suspend'">Suspendre</span>
                            <span x-show="statusAction === 'activate'">Activer</span>
                        </button>
                    </form>
                    <button type="button" 
                            @click="showStatusModal = false"
                            class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>
    </div>
</x-layouts.admin>
