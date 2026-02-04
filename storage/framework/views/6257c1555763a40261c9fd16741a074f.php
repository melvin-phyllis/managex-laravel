<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['hasActiveFilters' => false]));

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

foreach (array_filter((['hasActiveFilters' => false]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 mb-6">
    <form method="GET" <?php echo e($attributes); ?>>
        <!-- Ligne principale de filtres -->
        <div class="flex flex-wrap items-center gap-4">
            <?php echo e($filters ?? $slot); ?>

        </div>

        <!-- Filtres actifs (chips) -->
        <?php if($hasActiveFilters && isset($activeFilters)): ?>
        <div class="flex flex-wrap items-center gap-2 pt-3 border-t border-gray-100 mt-4">
            <span class="text-sm text-gray-500">Filtres actifs:</span>
            <?php echo e($activeFilters); ?>

        </div>
        <?php endif; ?>
    </form>
</div>
<?php /**PATH D:\ManageX\resources\views\components\filter-bar.blade.php ENDPATH**/ ?>