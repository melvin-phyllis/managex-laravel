<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'type' => 'card',
    'count' => 1,
    'columns' => 1
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
    'type' => 'card',
    'count' => 1,
    'columns' => 1
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php
$gridClass = match($columns) {
    2 => 'grid-cols-1 sm:grid-cols-2',
    3 => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
    4 => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4',
    5 => 'grid-cols-2 sm:grid-cols-3 lg:grid-cols-5',
    default => 'grid-cols-1',
};
?>

<div class="grid <?php echo e($gridClass); ?> gap-6">
    <?php for($i = 0; $i < $count; $i++): ?>
        <?php switch($type):
            case ('stat-card'): ?>
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 animate-pulse">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="h-4 bg-gray-200 rounded w-24 mb-3"></div>
                            <div class="h-8 bg-gray-200 rounded w-16"></div>
                        </div>
                        <div class="w-12 h-12 bg-gray-200 rounded-full"></div>
                    </div>
                </div>
                <?php break; ?>

            <?php case ('chart'): ?>
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 animate-pulse">
                    <div class="h-5 bg-gray-200 rounded w-32 mb-4"></div>
                    <div class="h-48 bg-gray-200 rounded"></div>
                </div>
                <?php break; ?>

            <?php case ('activity'): ?>
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 animate-pulse">
                    <div class="h-5 bg-gray-200 rounded w-40 mb-4"></div>
                    <div class="space-y-4">
                        <?php for($j = 0; $j < 5; $j++): ?>
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gray-200 rounded-full"></div>
                                <div class="flex-1">
                                    <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                                    <div class="h-3 bg-gray-200 rounded w-1/4"></div>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
                <?php break; ?>

            <?php case ('alert'): ?>
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 animate-pulse">
                    <div class="h-5 bg-gray-200 rounded w-32 mb-4"></div>
                    <div class="space-y-3">
                        <?php for($j = 0; $j < 3; $j++): ?>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-gray-200 rounded-full"></div>
                                    <div class="h-4 bg-gray-200 rounded w-40"></div>
                                </div>
                                <div class="w-16 h-8 bg-gray-200 rounded"></div>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
                <?php break; ?>

            <?php case ('calendar'): ?>
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 animate-pulse">
                    <div class="flex items-center justify-between mb-4">
                        <div class="h-5 bg-gray-200 rounded w-24"></div>
                        <div class="flex space-x-2">
                            <div class="w-8 h-8 bg-gray-200 rounded"></div>
                            <div class="w-8 h-8 bg-gray-200 rounded"></div>
                        </div>
                    </div>
                    <div class="grid grid-cols-7 gap-2">
                        <?php for($j = 0; $j < 35; $j++): ?>
                            <div class="w-8 h-8 bg-gray-200 rounded-full"></div>
                        <?php endfor; ?>
                    </div>
                </div>
                <?php break; ?>

            <?php case ('list'): ?>
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 animate-pulse">
                    <div class="h-5 bg-gray-200 rounded w-32 mb-4"></div>
                    <div class="space-y-3">
                        <?php for($j = 0; $j < 4; $j++): ?>
                            <div class="flex items-center justify-between p-3 border-b border-gray-100">
                                <div class="flex-1">
                                    <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                                    <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                                </div>
                                <div class="w-20 h-6 bg-gray-200 rounded-full"></div>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
                <?php break; ?>

            <?php case ('text-line'): ?>
                <div class="h-4 bg-gray-200 rounded animate-pulse" style="width: <?php echo e(rand(60, 100)); ?>%"></div>
                <?php break; ?>

            <?php default: ?>
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 animate-pulse">
                    <div class="h-4 bg-gray-200 rounded w-3/4 mb-4"></div>
                    <div class="h-4 bg-gray-200 rounded w-1/2 mb-4"></div>
                    <div class="h-20 bg-gray-200 rounded"></div>
                </div>
        <?php endswitch; ?>
    <?php endfor; ?>
</div>
<?php /**PATH D:\ManageX\resources\views\components\skeleton-loader.blade.php ENDPATH**/ ?>