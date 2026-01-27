<x-layouts.admin>
    <div class="space-y-6" x-data="presenceTable()">
        <!-- Header -->
        <x-table-header title="Gestion des Présences" subtitle="Suivi des pointages et heures travaillées">
            <x-slot:icon>
                <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/20">
                    <x-icon name="clock" class="w-6 h-6 text-white" />
                </div>
            </x-slot:icon>
            <x-slot:actions>
                <div class="flex gap-2">
                    <a href="{{ route('admin.presences.export.csv', request()->query()) }}" class="inline-flex items-center px-4 py-2.5 bg-white text-gray-700 font-medium rounded-xl border border-gray-200 hover:bg-gray-50 transition-all shadow-sm text-sm">
                        <x-icon name="file-spreadsheet" class="w-4 h-4 mr-2" />
                        CSV
                    </a>
                    <a href="{{ route('admin.presences.export.excel', request()->query()) }}" class="inline-flex items-center px-4 py-2.5 bg-green-50 text-green-700 font-medium rounded-xl border border-green-200 hover:bg-green-100 transition-all shadow-sm text-sm">
                        <x-icon name="file-spreadsheet" class="w-4 h-4 mr-2" />
                        Excel
                    </a>
                    <a href="{{ route('admin.presences.export.pdf', request()->query()) }}" class="inline-flex items-center px-4 py-2.5 bg-red-50 text-red-700 font-medium rounded-xl border border-red-200 hover:bg-red-100 transition-all shadow-sm text-sm">
                        <x-icon name="file-text" class="w-4 h-4 mr-2" />
                        PDF
                    </a>
                </div>
            </x-slot:actions>
        </x-table-header>

        @if(isset($stats))
        <!-- Stats Cards (4 colonnes) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <!-- Présents aujourd'hui -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Présents aujourd'hui</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['present_today'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg shadow-green-500/30">
                        <x-icon name="user-check" class="w-6 h-6 text-white" />
                    </div>
                </div>
            </div>
            
            <!-- Retards -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Retards ce mois</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['late_month'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl flex items-center justify-center shadow-lg shadow-amber-500/30">
                        <x-icon name="clock" class="w-6 h-6 text-white" />
                    </div>
                </div>
            </div>

            <!-- Absents -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Absents injustifiés</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['absent_month'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-rose-600 rounded-xl flex items-center justify-center shadow-lg shadow-red-500/30">
                        <x-icon name="user-x" class="w-6 h-6 text-white" />
                    </div>
                </div>
            </div>

            <!-- Heures moyennes -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Heures moy. / jour</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['avg_hours'] ?? '0h' }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/30">
                        <x-icon name="timer" class="w-6 h-6 text-white" />
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Filter Bar -->
        <x-filter-bar :hasActiveFilters="request()->hasAny(['employee_id', 'date_debut', 'date_fin'])">
            <x-slot:filters>
                <!-- Employee -->
                <div class="flex-1 min-w-[200px] relative">
                    <x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                    <select name="employee_id" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm bg-white appearance-none">
                        <option value="">Tous les employés</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Date Range -->
                <div class="flex items-center gap-2">
                    <div class="relative">
                        <input type="date" name="date_debut" value="{{ request('date_debut') }}" class="pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                        <x-icon name="calendar" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                    </div>
                    <span class="text-gray-400">à</span>
                    <div class="relative">
                        <input type="date" name="date_fin" value="{{ request('date_fin') }}" class="pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                        <x-icon name="calendar" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex gap-2">
                    <button type="submit" class="px-6 py-2.5 bg-emerald-600 text-white font-medium rounded-xl hover:bg-emerald-700 transition-colors shadow-lg shadow-emerald-500/25 flex items-center">
                        <x-icon name="filter" class="w-4 h-4 mr-2" />
                        Filtrer
                    </button>
                    @if(request()->hasAny(['employee_id', 'date_debut', 'date_fin']))
                        <a href="{{ route('admin.presences.index') }}" class="px-4 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 transition-colors flex items-center">
                            <x-icon name="x" class="w-5 h-5" />
                        </a>
                    @endif
                </div>
            </x-slot:filters>

            <x-slot:activeFilters>
                @if(request('employee_id'))
                    @php $emp = $employees->find(request('employee_id')); @endphp
                    @if($emp)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                            Employé: {{ $emp->name }}
                        </span>
                    @endif
                @endif
                @if(request('date_debut'))
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-50 text-gray-700 border border-gray-100">
                        Du: {{ \Carbon\Carbon::parse(request('date_debut'))->format('d/m/Y') }}
                    </span>
                @endif
                @if(request('date_fin'))
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-50 text-gray-700 border border-gray-100">
                        Au: {{ \Carbon\Carbon::parse(request('date_fin'))->format('d/m/Y') }}
                    </span>
                @endif
            </x-slot:activeFilters>
        </x-filter-bar>

        <!-- Table -->
        <x-data-table>
            <x-slot:header>
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Employé</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Horaires</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Durée</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Notes & Statut</th>
                </tr>
            </x-slot:header>

            <x-slot:body>
                @forelse($presences as $presence)
                    <tr class="hover:bg-emerald-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0 h-10 w-10">
                                    @if($presence->user->avatar)
                                        <img class="h-10 w-10 rounded-full object-cover ring-2 ring-white shadow-sm" src="{{ Storage::url($presence->user->avatar) }}" alt="{{ $presence->user->name }}">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center ring-2 ring-white shadow-sm">
                                            <span class="text-white font-bold text-xs">{{ strtoupper(substr($presence->user->name, 0, 2)) }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $presence->user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $presence->user->department->name ?? 'Aucun département' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $presence->date->format('d/m/Y') }}</div>
                            <div class="text-xs text-gray-500 capitalize">{{ $presence->date->translatedFormat('l') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center text-xs">
                                    <span class="w-16 text-gray-500">Arrivée:</span>
                                    <span class="font-medium text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded">{{ $presence->check_in->format('H:i') }}</span>
                                </div>
                                <div class="flex items-center text-xs">
                                    <span class="w-16 text-gray-500">Départ:</span>
                                    @if($presence->check_out)
                                        <span class="font-medium text-red-700 bg-red-50 px-1.5 py-0.5 rounded">{{ $presence->check_out->format('H:i') }}</span>
                                    @else
                                        <span class="text-xs text-amber-600 italic">En cours...</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($presence->check_out)
                                <span class="font-medium text-gray-900">{{ $presence->hours_worked }}</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($presence->notes)
                                <div class="text-sm text-gray-600 bg-gray-50 p-2 rounded-lg border border-gray-100 max-w-xs">
                                    {{ $presence->notes }}
                                </div>
                            @elseif(!$presence->check_out)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 animate-pulse">
                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                                    En cours
                                </span>
                            @else
                                <span class="text-gray-400 text-sm">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <x-icon name="user-x" class="w-8 h-8 text-gray-400" />
                                </div>
                                <p class="text-lg font-medium text-gray-900">Aucune présence trouvée</p>
                                <p class="text-sm text-gray-500 mt-1">Essayez de modifier vos filtres.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </x-slot:body>

            <x-slot:pagination>
                {{ $presences->links() }}
            </x-slot:pagination>
        </x-data-table>
    </div>

    <script>
        function presenceTable() {
            return {
                // Pour future implémentation
            }
        }
    </script>
</x-layouts.admin>
