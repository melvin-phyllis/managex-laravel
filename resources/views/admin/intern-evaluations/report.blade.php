<x-layouts.admin>
    <div class="space-y-6">
        <!-- Header -->
        <x-table-header title="Rapport des Évaluations" subtitle="Historique complet avec filtres">
            <x-slot:icon>
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/20">
                    <x-icon name="bar-chart-2" class="w-6 h-6 text-white" />
                </div>
            </x-slot:icon>
            <x-slot:actions>
                <a href="{{ route('admin.intern-evaluations.index') }}" class="inline-flex items-center px-4 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 transition-all text-sm">
                    <x-icon name="arrow-left" class="w-4 h-4 mr-2" />
                    Retour
                </a>
            </x-slot:actions>
        </x-table-header>

        <!-- Filters -->
        <x-filter-bar :hasActiveFilters="request()->hasAny(['department_id', 'tutor_id', 'date_from', 'date_to'])">
            <x-slot:filters>
                <div class="flex-1 min-w-[180px]">
                    <select name="department_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-violet-500">
                        <option value="">Tous les départements</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 min-w-[180px]">
                    <select name="tutor_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-violet-500">
                        <option value="">Tous les tuteurs</option>
                        @foreach($tutors as $tutor)
                            <option value="{{ $tutor->id }}" {{ request('tutor_id') == $tutor->id ? 'selected' : '' }}>{{ $tutor->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-violet-500" placeholder="Du">
                    <span class="text-gray-400">→</span>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-violet-500" placeholder="Au">
                </div>
            </x-slot:filters>
        </x-filter-bar>

        <!-- Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Stagiaire</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Semaine</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Disc.</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Comp.</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Tech.</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Com.</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Total</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Tuteur</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($evaluations as $evaluation)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-violet-100 text-violet-600 rounded-full flex items-center justify-center font-semibold text-sm">
                                            {{ strtoupper(substr($evaluation->intern->name ?? 'S', 0, 1)) }}
                                        </div>
                                        <span class="font-medium">{{ $evaluation->intern->name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $evaluation->week_label }}</td>
                                <td class="px-6 py-4 text-center text-sm">{{ $evaluation->discipline_score }}</td>
                                <td class="px-6 py-4 text-center text-sm">{{ $evaluation->behavior_score }}</td>
                                <td class="px-6 py-4 text-center text-sm">{{ $evaluation->skills_score }}</td>
                                <td class="px-6 py-4 text-center text-sm">{{ $evaluation->communication_score }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-sm font-semibold
                                        @if($evaluation->grade_letter === 'A') bg-green-100 text-green-700
                                        @elseif($evaluation->grade_letter === 'B') bg-blue-100 text-blue-700
                                        @elseif($evaluation->grade_letter === 'C') bg-yellow-100 text-yellow-700
                                        @elseif($evaluation->grade_letter === 'D') bg-orange-100 text-orange-700
                                        @else bg-red-100 text-red-700
                                        @endif">
                                        {{ $evaluation->total_score }}/10
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $evaluation->tutor->name ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                    Aucune évaluation trouvée
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($evaluations->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $evaluations->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
