<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['hasHeader' => true, 'hasFooter' => true]));

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

foreach (array_filter((['hasHeader' => true, 'hasFooter' => true]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<div <?php echo e($attributes->merge(['class' => 'bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden'])); ?>>
    <!-- Barre d'actions groupées -->
    <div x-show="selectedCount > 0"
         x-transition
         style="display: none;"
         class="bg-blue-50 border-b border-blue-100 px-6 py-3 flex items-center justify-between">
        <span class="text-sm font-medium text-blue-700">
            <span x-text="selectedCount"></span> élément(s) sélectionné(s)
        </span>
        <div class="flex items-center gap-2">
            <?php echo e($bulkActions ?? ''); ?>

        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto custom-scrollbar">
        <table class="w-full">
            <?php if($hasHeader && isset($header)): ?>
            <thead class="bg-gradient-to-r from-gray-50 to-slate-100 border-b border-gray-200">
                <?php echo e($header); ?>

            </thead>
            <?php endif; ?>
            <tbody class="divide-y divide-gray-100">
                <?php echo e($body ?? $slot); ?>

            </tbody>
        </table>
    </div>

    <!-- Footer avec pagination -->
    <?php if($hasFooter && isset($pagination)): ?>
    <div class="bg-gray-50 border-t border-gray-100 px-6 py-4">
        <?php echo e($pagination); ?>

    </div>
    <?php endif; ?>
</div>
<?php /**PATH D:\ManageX\resources\views\components\data-table.blade.php ENDPATH**/ ?>