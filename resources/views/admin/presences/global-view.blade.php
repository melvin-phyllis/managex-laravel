<x-layouts.admin>
    <div class="space-y-6" x-data="globalViewPage()">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/20">
                        <x-icon name="users" class="w-5 h-5 text-white" />
                    </div>
                    Suivi Global des Présences
                </h1>
                <p class="text-gray-500 mt-1 ml-13">Performance de présence par employé</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500">
                    Période : {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}
                    ({{ $workingDays }} jours ouvrés)
                </span>
            </div>
        </div>

        {{-- Filtres --}}
        <form method="GET" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Période</label>
                <select name="period" class="rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" onchange="this.form.submit()">
                    <option value="week" {{ $period === 'week' ? 'selected' : '' }}>Cette semaine</option>
                    <option value="month" {{ $period === 'month' ? 'selected' : '' }}>Ce mois</option>
                    <option value="quarter" {{ $period === 'quarter' ? 'selected' : '' }}>Ce trimestre</option>
                    <option value="year" {{ $period === 'year' ? 'selected' : '' }}>Cette année</option>
                    <option value="custom" {{ $period === 'custom' ? 'selected' : '' }}>Personnalisée</option>
                </select>
            </div>

            @if($period === 'custom')
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Du</label>
                <input type="date" name="start_date" value="{{ request('start_date', $startDate->format('Y-m-d')) }}" 
                    class="rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Au</label>
                <input type="date" name="end_date" value="{{ request('end_date', $endDate->format('Y-m-d')) }}"
                    class="rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            @endif

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Département</label>
                <select name="department_id" class="rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Tous</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <label class="flex items-center gap-2 cursor-pointer bg-red-50 text-red-700 px-3 py-2 rounded-lg border border-red-200 hover:bg-red-100 transition-colors">
                    <input type="checkbox" name="risk_only" value="true" {{ request('risk_only') === 'true' ? 'checked' : '' }} class="rounded border-red-300 text-red-600 focus:ring-red-500">
                    <x-icon name="alert-triangle" class="w-4 h-4"/>
                    <span class="text-sm font-medium">À risque uniquement</span>
                </label>
            </div>

            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors flex items-center shadow-sm">
                <x-icon name="filter" class="w-4 h-4 mr-2"/>
                Filtrer
            </button>
        </form>

        {{-- Stats Summary --}}
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <x-icon name="users" class="w-5 h-5 text-blue-600"/>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $employees->total() }}</p>
                        <p class="text-xs text-gray-500">Employés</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <x-icon name="check-circle" class="w-5 h-5 text-green-600"/>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $employees->where('risk_level', 'low')->count() }}</p>
                        <p class="text-xs text-gray-500">Performants</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <x-icon name="alert-circle" class="w-5 h-5 text-yellow-600"/>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $employees->where('risk_level', 'medium')->count() }}</p>
                        <p class="text-xs text-gray-500">À surveiller</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                        <x-icon name="alert-triangle" class="w-5 h-5 text-red-600"/>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $employees->where('risk_level', 'high')->count() }}</p>
                        <p class="text-xs text-gray-500">À risque</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tableau des employés --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left py-4 px-5 font-semibold text-gray-700 text-sm">Employé</th>
                            <th class="text-left py-4 px-5 font-semibold text-gray-700 text-sm">Département</th>
                            <th class="text-center py-4 px-5 font-semibold text-gray-700 text-sm">
                                <a href="?{{ http_build_query(array_merge(request()->all(), ['sort' => 'total_worked_hours', 'dir' => request('sort') === 'total_worked_hours' && request('dir') === 'desc' ? 'asc' : 'desc'])) }}" class="flex items-center justify-center gap-1 hover:text-indigo-600">
                                    Taux Présence
                                    @if(request('sort') === 'total_worked_hours')
                                        <x-icon name="{{ request('dir') === 'asc' ? 'arrow-up' : 'arrow-down' }}" class="w-3 h-3"/>
                                    @endif
                                </a>
                            </th>
                            <th class="text-center py-4 px-5 font-semibold text-gray-700 text-sm">
                                <a href="?{{ http_build_query(array_merge(request()->all(), ['sort' => 'late_count', 'dir' => request('sort') === 'late_count' && request('dir') === 'desc' ? 'asc' : 'desc'])) }}" class="flex items-center justify-center gap-1 hover:text-indigo-600">
                                    Retards
                                    @if(request('sort', 'late_count') === 'late_count')
                                        <x-icon name="{{ request('dir', 'desc') === 'asc' ? 'arrow-up' : 'arrow-down' }}" class="w-3 h-3"/>
                                    @endif
                                </a>
                            </th>
                            <th class="text-center py-4 px-5 font-semibold text-gray-700 text-sm">
                                <a href="?{{ http_build_query(array_merge(request()->all(), ['sort' => 'total_late_minutes', 'dir' => request('sort') === 'total_late_minutes' && request('dir') === 'desc' ? 'asc' : 'desc'])) }}" class="flex items-center justify-center gap-1 hover:text-indigo-600">
                                    Impact Retards
                                    @if(request('sort') === 'total_late_minutes')
                                        <x-icon name="{{ request('dir') === 'asc' ? 'arrow-up' : 'arrow-down' }}" class="w-3 h-3"/>
                                    @endif
                                </a>
                            </th>
                            <th class="text-center py-4 px-5 font-semibold text-gray-700 text-sm">Statut</th>
                            <th class="text-center py-4 px-5 font-semibold text-gray-700 text-sm">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($employees as $employee)
                        <tr class="hover:bg-gray-50 transition-colors">
                            {{-- Employé --}}
                            <td class="py-4 px-5">
                                <div class="flex items-center gap-3">
                                    @if($employee->avatar)
                                        <img src="{{ Storage::url($employee->avatar) }}" class="w-10 h-10 rounded-full object-cover">
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm">
                                            {{ strtoupper(substr($employee->name, 0, 2)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $employee->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $employee->position->name ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Département --}}
                            <td class="py-4 px-5">
                                @if($employee->department)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" style="background-color: {{ $employee->department->color }}20; color: {{ $employee->department->color }}">
                                        {{ $employee->department->name }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-sm">-</span>
                                @endif
                            </td>

                            {{-- Taux de présence (jauge) --}}
                            <td class="py-4 px-5">
                                <div class="flex flex-col items-center">
                                    <div class="relative w-14 h-14">
                                        <svg class="w-14 h-14 transform -rotate-90" viewBox="0 0 36 36">
                                            <circle cx="18" cy="18" r="16" fill="none" class="stroke-gray-200" stroke-width="3"></circle>
                                            <circle cx="18" cy="18" r="16" fill="none" 
                                                class="{{ $employee->attendance_rate >= 95 ? 'stroke-green-500' : ($employee->attendance_rate >= 80 ? 'stroke-yellow-500' : 'stroke-red-500') }}" 
                                                stroke-width="3" 
                                                stroke-dasharray="{{ $employee->attendance_rate }}, 100"
                                                stroke-linecap="round"></circle>
                                        </svg>
                                        <span class="absolute inset-0 flex items-center justify-center text-xs font-bold {{ $employee->attendance_rate >= 95 ? 'text-green-600' : ($employee->attendance_rate >= 80 ? 'text-yellow-600' : 'text-red-600') }}">
                                            {{ $employee->attendance_rate }}%
                                        </span>
                                    </div>
                                    <span class="text-[10px] text-gray-400 mt-1">{{ number_format($employee->total_worked_hours ?? 0, 1) }}h / {{ $expectedHours }}h</span>
                                </div>
                            </td>

                            {{-- Fréquence retards --}}
                            <td class="py-4 px-5 text-center">
                                @php $lateCount = $employee->late_count ?? 0; @endphp
                                <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-sm font-bold
                                    {{ $lateCount === 0 ? 'bg-green-100 text-green-700' : ($lateCount <= 5 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                    <x-icon name="clock" class="w-4 h-4"/>
                                    {{ $lateCount }}
                                </span>
                            </td>

                            {{-- Impact retards --}}
                            <td class="py-4 px-5 text-center">
                                @php $lateMinutes = $employee->total_late_minutes ?? 0; @endphp
                                <span class="font-medium {{ $lateMinutes === 0 ? 'text-green-600' : ($lateMinutes <= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ $employee->late_impact_formatted }}
                                </span>
                            </td>

                            {{-- Statut risque --}}
                            <td class="py-4 px-5 text-center">
                                @switch($employee->risk_level)
                                    @case('high')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">
                                            <x-icon name="alert-triangle" class="w-3 h-3"/>
                                            À risque
                                        </span>
                                        @break
                                    @case('medium')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700">
                                            <x-icon name="alert-circle" class="w-3 h-3"/>
                                            À surveiller
                                        </span>
                                        @break
                                    @default
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                            <x-icon name="check-circle" class="w-3 h-3"/>
                                            OK
                                        </span>
                                @endswitch
                            </td>

                            {{-- Actions --}}
                            <td class="py-4 px-5 text-center">
                                <a href="{{ route('admin.employees.show', $employee) }}" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors">
                                    <x-icon name="eye" class="w-4 h-4 mr-1"/>
                                    Détail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-gray-500">
                                <x-icon name="users" class="w-12 h-12 mx-auto text-gray-300 mb-3"/>
                                <p>Aucun employé trouvé</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($employees->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $employees->withQueryString()->links() }}
            </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        function globalViewPage() {
            return {
                // Future interactivity
            }
        }
    </script>
    @endpush
</x-layouts.admin>
