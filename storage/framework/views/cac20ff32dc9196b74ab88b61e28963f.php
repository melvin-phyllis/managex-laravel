<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['title', 'subtitle', 'icon', 'actions']));

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

foreach (array_filter((['title', 'subtitle', 'icon', 'actions']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
            <?php if(isset($icon)): ?>
                <?php echo e($icon); ?>

            <?php endif; ?>
            <?php echo e($title); ?>

        </h1>
        <?php if(isset($subtitle)): ?>
            <p class="text-gray-500 mt-1"><?php echo e($subtitle); ?></p>
        <?php endif; ?>
    </div>
    <div class="flex items-center gap-3">
        <?php if(isset($actions)): ?>
            <?php echo e($actions); ?>

        <?php endif; ?>
    </div>
</div>
<?php /**PATH D:\ManageX\resources\views\components\table-header.blade.php ENDPATH**/ ?>