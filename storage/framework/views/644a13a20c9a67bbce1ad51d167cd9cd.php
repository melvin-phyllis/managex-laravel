<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'value' => 0,
    'max' => 100,
    'size' => 'md',
    'color' => 'blue',
    'label' => null,
    'sublabel' => null,
    'showPercentage' => true,
    'animated' => true
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
    'value' => 0,
    'max' => 100,
    'size' => 'md',
    'color' => 'blue',
    'label' => null,
    'sublabel' => null,
    'showPercentage' => true,
    'animated' => true
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php
$percentage = $max > 0 ? min(100, round(($value / $max) * 100)) : 0;

$sizes = [
    'sm' => ['container' => 'w-16 h-16', 'stroke' => 4, 'text' => 'text-sm', 'subtext' => 'text-xs'],
    'md' => ['container' => 'w-24 h-24', 'stroke' => 6, 'text' => 'text-xl', 'subtext' => 'text-xs'],
    'lg' => ['container' => 'w-32 h-32', 'stroke' => 8, 'text' => 'text-2xl', 'subtext' => 'text-sm'],
    'xl' => ['container' => 'w-40 h-40', 'stroke' => 10, 'text' => 'text-3xl', 'subtext' => 'text-base'],
];

$colorClasses = [
    'blue' => ['stroke' => 'stroke-blue-500', 'text' => 'text-blue-600'],
    'green' => ['stroke' => 'stroke-green-500', 'text' => 'text-green-600'],
    'yellow' => ['stroke' => 'stroke-yellow-500', 'text' => 'text-yellow-600'],
    'red' => ['stroke' => 'stroke-red-500', 'text' => 'text-red-600'],
    'purple' => ['stroke' => 'stroke-purple-500', 'text' => 'text-purple-600'],
    'indigo' => ['stroke' => 'stroke-indigo-500', 'text' => 'text-indigo-600'],
];

// Auto color based on percentage
if ($color === 'auto') {
    if ($percentage >= 80) {
        $colorClasses['auto'] = ['stroke' => 'stroke-green-500', 'text' => 'text-green-600'];
    } elseif ($percentage >= 50) {
        $colorClasses['auto'] = ['stroke' => 'stroke-blue-500', 'text' => 'text-blue-600'];
    } elseif ($percentage >= 25) {
        $colorClasses['auto'] = ['stroke' => 'stroke-yellow-500', 'text' => 'text-yellow-600'];
    } else {
        $colorClasses['auto'] = ['stroke' => 'stroke-red-500', 'text' => 'text-red-600'];
    }
}

$sizeConfig = $sizes[$size] ?? $sizes['md'];
$colorConfig = $colorClasses[$color] ?? $colorClasses['blue'];

// SVG calculations
$radius = 45;
$circumference = 2 * pi() * $radius;
$strokeDashoffset = $circumference - ($percentage / 100) * $circumference;
?>

<div <?php echo e($attributes->merge(['class' => 'circular-progress ' . $sizeConfig['container']])); ?>

     <?php if($animated): ?>
     x-data="{ shown: false, animatedPercentage: 0 }"
     x-init="setTimeout(() => { shown = true; animatedPercentage = <?php echo e($percentage); ?>; }, 100)"
     <?php endif; ?>>

    <svg class="w-full h-full" viewBox="0 0 100 100">
        
        <circle
            class="circular-progress-bg stroke-gray-200 fill-none"
            cx="50"
            cy="50"
            r="<?php echo e($radius); ?>"
            stroke-width="<?php echo e($sizeConfig['stroke']); ?>"
        />

        
        <circle
            class="circular-progress-bar <?php echo e($colorConfig['stroke']); ?> fill-none"
            cx="50"
            cy="50"
            r="<?php echo e($radius); ?>"
            stroke-width="<?php echo e($sizeConfig['stroke']); ?>"
            stroke-linecap="round"
            stroke-dasharray="<?php echo e($circumference); ?>"
            <?php if($animated): ?>
            :stroke-dashoffset="shown ? <?php echo e($strokeDashoffset); ?> : <?php echo e($circumference); ?>"
            <?php else: ?>
            stroke-dashoffset="<?php echo e($strokeDashoffset); ?>"
            <?php endif; ?>
            style="transform: rotate(-90deg); transform-origin: center; transition: stroke-dashoffset 1s ease-out;"
        />

        
        <?php if($showPercentage): ?>
            <text
                x="50"
                y="50"
                class="fill-current <?php echo e($colorConfig['text']); ?> <?php echo e($sizeConfig['text']); ?> font-bold"
                text-anchor="middle"
                dominant-baseline="middle"
                <?php if($animated): ?>
                x-text="Math.round(animatedPercentage) + '%'"
                <?php endif; ?>
            >
                <?php if(!$animated): ?>
                    <?php echo e($percentage); ?>%
                <?php endif; ?>
            </text>
        <?php endif; ?>
    </svg>

    
    <?php if($label || $sublabel): ?>
        <div class="text-center mt-2">
            <?php if($label): ?>
                <p class="<?php echo e($sizeConfig['text']); ?> font-semibold text-gray-900"><?php echo e($label); ?></p>
            <?php endif; ?>
            <?php if($sublabel): ?>
                <p class="<?php echo e($sizeConfig['subtext']); ?> text-gray-500"><?php echo e($sublabel); ?></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
<?php /**PATH D:\ManageX\resources\views/components/circular-progress.blade.php ENDPATH**/ ?>