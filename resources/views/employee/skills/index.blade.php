<x-layouts.employee>
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Mes Compétences</h1>
            <p class="text-sm text-gray-500 mt-1">Auto-évaluez vos compétences et suivez votre progression.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="border-l-4 p-4 rounded-r-lg animate-fade-in" style="background: rgba(27, 60, 53, 0.1); border-color: #1B3C35;">
            <p class="text-sm font-medium" style="color: #1B3C35;">{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Radar Chart --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden animate-fade-in-up">
            <div class="px-6 py-4 border-b border-gray-100" style="background: linear-gradient(90deg, rgba(27,60,53,0.1), rgba(133,144,170,0.08));">
                <h2 class="font-semibold text-gray-900">Vue d'ensemble</h2>
            </div>
            <div class="p-6">
                <canvas id="skillsRadarChart" width="300" height="300"></canvas>
            </div>
            <div class="px-6 pb-4">
                <div class="flex flex-wrap gap-2 justify-center">
                    @foreach(\App\Models\UserSkill::LEVELS as $level => $label)
                        <div class="flex items-center gap-1 text-xs text-gray-500">
                            <span class="w-4 h-1 rounded-full"
                                style="background: {{ ['#ef4444','#f97316','#eab308','#22c55e','#1B3C35'][$level - 1] }}">
                            </span>
                            {{ $level }} - {{ $label }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Skills by Category --}}
        <div class="lg:col-span-2 space-y-4">
            @foreach($categories as $category => $categorySkills)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden animate-fade-in-up">
                    <div class="px-5 py-3 border-b border-gray-100 flex items-center gap-2" style="background: linear-gradient(90deg, rgba(200,169,110,0.1), rgba(231,227,212,0.08));">
                        <h3 class="font-semibold text-gray-900 text-sm">{{ \App\Models\Skill::CATEGORIES[$category] ?? $category }}</h3>
                        <span class="text-xs text-gray-400">({{ $categorySkills->count() }})</span>
                    </div>
                    <div class="divide-y divide-gray-50">
                        @foreach($categorySkills as $skill)
                            @php $userSkill = $mySkills->get($skill->id); @endphp
                            <div class="p-4 flex items-center justify-between">
                                <div class="flex-1 min-w-0 pr-4">
                                    <p class="font-medium text-gray-900 text-sm">{{ $skill->name }}</p>
                                    @if($skill->description)
                                        <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $skill->description }}</p>
                                    @endif
                                    @if($userSkill && $userSkill->is_validated)
                                        <span class="inline-flex items-center gap-1 text-xs text-emerald-600 mt-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/></svg>
                                            Validé par {{ $userSkill->validator->name ?? 'Admin' }}
                                        </span>
                                    @endif
                                </div>
                                <form action="{{ route('employee.skills.update') }}" method="POST" class="flex items-center gap-1">
                                    @csrf
                                    <input type="hidden" name="skill_id" value="{{ $skill->id }}">
                                    <input type="hidden" name="level" value="{{ $userSkill->level ?? 0 }}" class="skill-level-input">
                                    <div class="flex gap-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <div onclick="this.closest('form').querySelector('.skill-level-input').value={{ $i }};this.closest('form').submit();"
                                                class="w-8 h-8 rounded-lg text-xs font-bold flex items-center justify-center transition-all cursor-pointer
                                                {{ $userSkill && $userSkill->level >= $i
                                                    ? 'text-white shadow-sm'
                                                    : 'bg-gray-100 text-gray-400 hover:bg-gray-200' }}"
                                                @if($userSkill && $userSkill->level >= $i)
                                                    style="background: {{ ['#ef4444','#f97316','#eab308','#22c55e','#1B3C35'][$i - 1] }}"
                                                @endif
                                                title="{{ \App\Models\UserSkill::LEVELS[$i] }}">
                                                {{ $i }}
                                            </div>
                                        @endfor
                                    </div>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            @if($categories->isEmpty())
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-12 text-center">
                    <p class="text-gray-500">Aucune compétence n'a encore été définie par l'administration.</p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Chart.js for Radar --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('skillsRadarChart');
    if (!ctx) return;

    const labels = @json($categories->map(fn($skills, $cat) => \App\Models\Skill::CATEGORIES[$cat] ?? $cat)->values());
    const data = @json($categories->map(function($skills, $cat) use ($mySkills) {
        $total = 0; $count = 0;
        foreach ($skills as $skill) {
            $us = $mySkills->get($skill->id);
            if ($us) { $total += $us->level; $count++; }
        }
        return $count > 0 ? round($total / $count, 1) : 0;
    })->values());

    new Chart(ctx, {
        type: 'radar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Mon niveau',
                data: data,
                backgroundColor: 'rgba(27, 60, 53, 0.2)',
                borderColor: '#1B3C35',
                borderWidth: 2,
                pointBackgroundColor: '#C8A96E',
                pointBorderColor: '#1B3C35',
                pointHoverRadius: 6,
            }]
        },
        options: {
            responsive: true,
            scales: {
                r: {
                    beginAtZero: true,
                    max: 5,
                    ticks: { stepSize: 1, display: true },
                    grid: { color: 'rgba(0,0,0,0.05)' },
                    pointLabels: { font: { size: 11, weight: '600' } },
                }
            },
            plugins: {
                legend: { display: false },
            }
        }
    });
});
</script>
</x-layouts.employee>
