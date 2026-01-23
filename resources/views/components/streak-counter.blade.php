@props([
    'currentStreak' => 0,
    'bestStreak' => 0,
    'lastPresenceDate' => null,
    'milestones' => [7, 14, 30, 60, 90]
])

@php
$nextMilestone = collect($milestones)->first(fn($m) => $m > $currentStreak) ?? $milestones[count($milestones) - 1];
$progressToMilestone = $nextMilestone > 0 ? min(100, ($currentStreak / $nextMilestone) * 100) : 0;
$isAtMilestone = in_array($currentStreak, $milestones);
@endphp

<div {{ $attributes->merge(['class' => ' w-full bg-gradient-to-r from-orange-400 via-orange-500 to-red-500 rounded-xl shadow-lg p-6 text-white animate-fade-in-up']) }}
     x-data="streakCounter({{ $currentStreak }}, {{ $isAtMilestone ? 'true' : 'false' }})"
     x-init="init()">

    <div class="flex  items-center justify-between">
        {{-- Left side: Fire icon and streak count --}}
        <div class="flex items-center space-x-4">
            <div class="relative">
                {{-- Fire emoji with animation --}}
                <span class="text-5xl streak-fire" :class="{ 'animate-bounce-in': celebrating }">
                    @if($currentStreak >= 30)
                        🔥
                    @elseif($currentStreak >= 7)
                        🔥
                    @elseif($currentStreak > 0)
                        🔥
                    @else
                        💤
                    @endif
                </span>
                {{-- Milestone badge --}}
                @if($isAtMilestone)
                    <span class="absolute -top-1 -right-1 w-6 h-6 bg-yellow-300 text-yellow-800 rounded-full flex items-center justify-center text-xs font-bold animate-bounce">
                        ⭐
                    </span>
                @endif
            </div>

            <div>
                <p class="text-white/80 text-sm font-medium">Série en cours</p>
                <div class="flex items-baseline space-x-1">
                    <span class="text-4xl font-bold" x-text="displayCount">{{ $currentStreak }}</span>
                    <span class="text-white/80 text-lg">jour{{ $currentStreak > 1 ? 's' : '' }}</span>
                </div>
            </div>
        </div>

        {{-- Right side: Best streak and next milestone --}}
        <div class="text-right">
            <div class="mb-2">
                <p class="text-white/60 text-xs">Meilleure série</p>
                <p class="text-lg font-semibold">
                    <span>🏆</span> {{ $bestStreak }} jours
                </p>
            </div>
            @if($currentStreak < $nextMilestone)
                <div>
                    <p class="text-white/60 text-xs">Prochain palier</p>
                    <p class="text-sm font-medium">{{ $nextMilestone - $currentStreak }} jours restants</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Progress bar to next milestone --}}
    @if($currentStreak < $nextMilestone)
        <div class="mt-4">
            <div class="flex items-center justify-between text-xs text-white/70 mb-1">
                <span>Objectif: {{ $nextMilestone }} jours</span>
                <span>{{ round($progressToMilestone) }}%</span>
            </div>
            <div class="h-2 bg-white/20 rounded-full overflow-hidden">
                <div class="h-full bg-white rounded-full transition-all duration-1000 ease-out"
                     x-bind:style="'width: ' + progressWidth + '%'"
                     x-data="{ progressWidth: 0 }"
                     x-init="setTimeout(() => progressWidth = {{ $progressToMilestone }}, 300)">
                </div>
            </div>
        </div>
    @endif

    {{-- Milestone celebration overlay --}}
    <template x-if="celebrating">
        <div class="fixed inset-0 flex items-center justify-center z-50 pointer-events-none">
            <div class="text-center animate-bounce-in">
                <div class="text-8xl mb-4">🎉</div>
                <p class="text-3xl font-bold text-yellow-400 drop-shadow-lg">Félicitations!</p>
                <p class="text-xl text-white drop-shadow-lg">{{ $currentStreak }} jours consécutifs!</p>
            </div>
        </div>
    </template>

    {{-- Motivational messages --}}
    @if($currentStreak == 0)
        <p class="mt-4 text-sm text-white/70 text-center">
            Pointez aujourd'hui pour commencer une nouvelle série !
        </p>
    @elseif($currentStreak >= 30)
        <p class="mt-4 text-sm text-white/90 text-center font-medium">
            🌟 Incroyable ! Vous êtes un exemple pour l'équipe !
        </p>
    @elseif($currentStreak >= 14)
        <p class="mt-4 text-sm text-white/80 text-center">
            💪 Excellent travail ! Continuez sur cette lancée !
        </p>
    @elseif($currentStreak >= 7)
        <p class="mt-4 text-sm text-white/80 text-center">
            👏 Super ! Une semaine complète, bravo !
        </p>
    @endif
</div>

<script>
function streakCounter(streak, isAtMilestone) {
    return {
        displayCount: 0,
        celebrating: false,

        init() {
            // Animate count up
            this.animateCount(streak);

            // Show celebration if at milestone
            if (isAtMilestone && streak > 0) {
                setTimeout(() => {
                    this.celebrating = true;
                    setTimeout(() => this.celebrating = false, 3000);
                }, 500);
            }
        },

        animateCount(target) {
            const duration = 1000;
            const steps = 20;
            const increment = target / steps;
            let current = 0;
            const interval = setInterval(() => {
                current += increment;
                if (current >= target) {
                    this.displayCount = target;
                    clearInterval(interval);
                } else {
                    this.displayCount = Math.floor(current);
                }
            }, duration / steps);
        }
    }
}
</script>
