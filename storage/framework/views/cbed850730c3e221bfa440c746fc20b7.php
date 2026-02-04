<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['value' => 0, 'max' => 100, 'showLabel' => true, 'size' => 'md']));

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

foreach (array_filter((['value' => 0, 'max' => 100, 'showLabel' => true, 'size' => 'md']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php
$percentage = min(100, max(0, ($value / $max) * 100));
$colorClass = match(true) {
    $percentage >= 100 => 'bg-green-500',
    $percentage >= 75 => 'bg-blue-500',
    $percentage >= 50 => 'bg-yellow-500',
    $percentage >= 25 => 'bg-orange-500',
    default => 'bg-red-500',
};
$heightClass = match($size) {
    'sm' => 'h-2',
    'lg' => 'h-6',
    default => 'h-4',
};
?>

<div class="w-full">
    <div class="flex items-center justify-between mb-1">
        <?php if($showLabel): ?>
            <span class="text-sm font-medium text-gray-700">Progression</span>
            <span class="text-sm font-medium text-gray-700"><?php echo e(number_format($percentage, 0)); ?>%</span>
        <?php endif; ?>
    </div>
    <div class="w-full bg-gray-200 rounded-full <?php echo e($heightClass); ?> overflow-hidden">
        <div class="<?php echo e($colorClass); ?> <?php echo e($heightClass); ?> rounded-full transition-all duration-500 ease-out"
             style="width: <?php echo e($percentage); ?>%"></div>
    </div>
</div>
<?php /**PATH D:\ManageX\resources\views/components/progress-bar.blade.php ENDPATH**/ ?>