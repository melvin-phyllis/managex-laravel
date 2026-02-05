<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'currentStreak' => 0,
    'bestStreak' => 0,
    'lastPresenceDate' => null,
    'milestones' => [7, 14, 30, 60, 90]
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'currentStreak' => 0,
    'bestStreak' => 0,
    'lastPresenceDate' => null,
    'milestones' => [7, 14, 30, 60, 90]
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php
$nextMilestone = collect($milestones)->first(fn($m) => $m > $currentStreak) ?? $milestones[count($milestones) - 1];
$progressToMilestone = $nextMilestone > 0 ? min(100, ($currentStreak / $nextMilestone) * 100) : 0;
$isAtMilestone = in_array($currentStreak, $milestones);
?>

<div <?php echo e($attributes->merge(['class' => 'w-full rounded-xl shadow-lg p-6 text-white animate-fade-in-up border'])); ?>

     style="background: linear-gradient(90deg, #31708E 14%, #5085A5 60%, #8FC1E3 100%); border-width: 1px;"
     x-data="streakCounter(<?php echo e($currentStreak); ?>, <?php echo e($isAtMilestone ? 'true' : 'false'); ?>)"
     x-init="init()">

    <div class="flex  items-center justify-between">
        
        <div class="flex items-center space-x-4">
            <div class="relative">
                
                <span class="text-5xl streak-fire" :class="{ 'animate-bounce-in': celebrating }">
                    <?php if($currentStreak >= 30): ?>
                        🔥
                    <?php elseif($currentStreak >= 7): ?>
                        🔥
                    <?php elseif($currentStreak > 0): ?>
                        🔥
                    <?php else: ?>
                        💤
                    <?php endif; ?>
                </span>
                
                <?php if($isAtMilestone): ?>
                    <span class="absolute -top-1 -right-1 w-6 h-6 bg-yellow-300 text-yellow-800 rounded-full flex items-center justify-center text-xs font-bold animate-bounce">
                        ⭐
                    </span>
                <?php endif; ?>
            </div>

            <div>
                <p class="text-white/80 text-sm font-medium">Série en cours</p>
                <div class="flex items-baseline space-x-1">
                    <span class="text-4xl font-bold" x-text="displayCount"><?php echo e($currentStreak); ?></span>
                    <span class="text-white/80 text-lg">jour<?php echo e($currentStreak > 1 ? 's' : ''); ?></span>
                </div>
            </div>
        </div>

        
        <div class="text-right">
            <div class="mb-2">
                <p class="text-white/60 text-xs">Meilleure série</p>
                <p class="text-lg font-semibold">
                    <span>🏆</span> <?php echo e($bestStreak); ?> jours
                </p>
            </div>
            <?php if($currentStreak < $nextMilestone): ?>
                <div>
                    <p class="text-white/60 text-xs">Prochain palier</p>
                    <p class="text-sm font-medium"><?php echo e($nextMilestone - $currentStreak); ?> jours restants</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    
    <?php if($currentStreak < $nextMilestone): ?>
        <div class="mt-4">
            <div class="flex items-center justify-between text-xs text-white/70 mb-1">
                <span>Objectif: <?php echo e($nextMilestone); ?> jours</span>
                <span><?php echo e(round($progressToMilestone)); ?>%</span>
            </div>
            <div class="h-2 bg-white/20 rounded-full overflow-hidden">
                <div class="h-full bg-white rounded-full transition-all duration-1000 ease-out"
                     x-bind:style="'width: ' + progressWidth + '%'"
                     x-data="{ progressWidth: 0 }"
                     x-init="setTimeout(() => progressWidth = <?php echo e($progressToMilestone); ?>, 300)">
                </div>
            </div>
        </div>
    <?php endif; ?>

    
    <template x-if="celebrating">
        <div class="fixed inset-0 flex items-center justify-center z-50 pointer-events-none">
            <div class="text-center animate-bounce-in">
                <div class="text-8xl mb-4">🎉</div>
                <p class="text-3xl font-bold text-yellow-400 drop-shadow-lg">Félicitations!</p>
                <p class="text-xl text-white drop-shadow-lg"><?php echo e($currentStreak); ?> jours consécutifs!</p>
            </div>
        </div>
    </template>

    
    <?php if($currentStreak == 0): ?>
        <p class="mt-4 text-sm text-white/70 text-center">
            Pointez aujourd'hui pour commencer une nouvelle série !
        </p>
    <?php elseif($currentStreak >= 30): ?>
        <p class="mt-4 text-sm text-white/90 text-center font-medium">
            🌟 Incroyable ! Vous êtes un exemple pour l'équipe !
        </p>
    <?php elseif($currentStreak >= 14): ?>
        <p class="mt-4 text-sm text-white/80 text-center">
            💪 Excellent travail ! Continuez sur cette lancée !
        </p>
    <?php elseif($currentStreak >= 7): ?>
        <p class="mt-4 text-sm text-white/80 text-center">
            👏 Super ! Une semaine complète, bravo !
        </p>
    <?php endif; ?>
</div>

<script nonce="<?php echo e($cspNonce ?? ''); ?>">
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
<?php /**PATH D:\ManageX\resources\views/components/streak-counter.blade.php ENDPATH**/ ?>