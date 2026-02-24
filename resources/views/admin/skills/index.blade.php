<x-layouts.admin>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Matrice de Compétences</h1>
            <p class="text-sm text-gray-500 mt-1">Vue d'ensemble des niveaux de compétences par employé.</p>
        </div>
        <a href="{{ route('admin.skills.manage') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white rounded-xl" style="background: #1B3C35;">
            Gérer les compétences
        </a>
    </div>

    @if($skills->count() && $employees->count())
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left px-4 py-3 font-semibold text-gray-900 sticky left-0 bg-white z-10 min-w-[180px]">Employé</th>
                        @foreach($skills as $skill)
                            <th class="text-center px-2 py-3 font-medium text-gray-600 text-xs min-w-[80px]" title="{{ $skill->description }}">
                                <span class="block truncate">{{ $skill->name }}</span>
                                <span class="block text-gray-400 text-[10px]">{{ $skill->category_label }}</span>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($employees as $employee)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900 sticky left-0 bg-white">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600">
                                        {{ strtoupper(substr($employee->name, 0, 2)) }}
                                    </div>
                                    <span class="truncate">{{ $employee->name }}</span>
                                </div>
                            </td>
                            @foreach($skills as $skill)
                                @php
                                    $us = $employee->userSkills->firstWhere('skill_id', $skill->id);
                                    $level = $us ? $us->level : 0;
                                    $colors = ['bg-gray-100 text-gray-400', 'bg-red-100 text-red-700', 'bg-orange-100 text-orange-700', 'bg-yellow-100 text-yellow-700', 'bg-emerald-100 text-emerald-700', 'bg-[#1B3C35]/10 text-[#1B3C35]'];
                                @endphp
                                <td class="text-center px-2 py-3">
                                    @if($level > 0)
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-bold {{ $colors[$level] }}
                                            {{ $us && $us->validated_at ? 'ring-2 ring-emerald-400 ring-offset-1' : '' }}"
                                            title="{{ \App\Models\UserSkill::LEVELS[$level] }}{{ $us && $us->validated_at ? ' (Validé)' : '' }}">
                                            {{ $level }}
                                        </span>
                                    @else
                                        <span class="text-gray-300">-</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-12 text-center">
            <p class="text-gray-500">
                @if($skills->isEmpty())
                    Aucune compétence définie. <a href="{{ route('admin.skills.manage') }}" class="font-medium" style="color: #1B3C35;">Ajouter des compétences</a>.
                @else
                    Aucun employé avec des compétences renseignées.
                @endif
            </p>
        </div>
    @endif
</div>
</x-layouts.admin>
