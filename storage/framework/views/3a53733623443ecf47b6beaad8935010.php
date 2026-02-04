<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'actions' => []
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
    'actions' => []
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php
$defaultActions = [
    [
        'id' => 'leave',
        'label' => 'Demander un congé',
        'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>',
        'color' => 'purple',
        'route' => 'employee.leaves.create'
    ],
    [
        'id' => 'task',
        'label' => 'Mes tâches',
        'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>',
        'color' => 'blue',
        'route' => 'employee.tasks.index'
    ],
    [
        'id' => 'payroll',
        'label' => 'Mes fiches de paie',
        'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>',
        'color' => 'green',
        'route' => 'employee.payrolls.index'
    ],
    [
        'id' => 'survey',
        'label' => 'Sondages',
        'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>',
        'color' => 'indigo',
        'route' => 'employee.surveys.index'
    ]
];

$actionsList = !empty($actions) ? $actions : $defaultActions;

$colorClasses = [
    'blue' => ['bg' => 'bg-blue-50', 'icon' => 'text-blue-600', 'hover' => 'hover:border-blue-300 hover:bg-blue-50'],
    'green' => ['bg' => 'bg-green-50', 'icon' => 'text-green-600', 'hover' => 'hover:border-green-300 hover:bg-green-50'],
    'yellow' => ['bg' => 'bg-yellow-50', 'icon' => 'text-yellow-600', 'hover' => 'hover:border-yellow-300 hover:bg-yellow-50'],
    'red' => ['bg' => 'bg-red-50', 'icon' => 'text-red-600', 'hover' => 'hover:border-red-300 hover:bg-red-50'],
    'purple' => ['bg' => 'bg-purple-50', 'icon' => 'text-purple-600', 'hover' => 'hover:border-purple-300 hover:bg-purple-50'],
    'indigo' => ['bg' => 'bg-indigo-50', 'icon' => 'text-indigo-600', 'hover' => 'hover:border-indigo-300 hover:bg-indigo-50'],
];
?>

<div <?php echo e($attributes->merge(['class' => 'bg-white rounded-xl shadow-sm border border-gray-100 p-6 animate-fade-in-up'])); ?>>
    
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold text-gray-900">Actions rapides</h3>
        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
    </div>

    
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <?php $__currentLoopData = $actionsList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $color = $action['color'] ?? 'blue';
                $colorConfig = $colorClasses[$color] ?? $colorClasses['blue'];
                $url = isset($action['route']) ? route($action['route']) : ($action['url'] ?? '#');
            ?>

            <a href="<?php echo e($url); ?>"
               class="quick-action-btn group <?php echo e($colorConfig['hover']); ?>"
               style="animation-delay: <?php echo e($index * 50); ?>ms"
               <?php if(isset($action['external']) && $action['external']): ?>
               target="_blank"
               <?php endif; ?>>
                
                <div class="w-12 h-12 rounded-full <?php echo e($colorConfig['bg']); ?> flex items-center justify-center mb-2 transition-transform group-hover:scale-110">
                    <span class="<?php echo e($colorConfig['icon']); ?>">
                        <?php echo $action['icon']; ?>

                    </span>
                </div>

                
                <span class="text-xs font-medium text-gray-700 text-center leading-tight">
                    <?php echo e($action['label']); ?>

                </span>

                
                <?php if(isset($action['badge']) && $action['badge'] > 0): ?>
                    <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center">
                        <?php echo e($action['badge'] > 9 ? '9+' : $action['badge']); ?>

                    </span>
                <?php endif; ?>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <?php if(isset($slot) && !$slot->isEmpty()): ?>
        <div class="mt-4 pt-4 border-t border-gray-100">
            <?php echo e($slot); ?>

        </div>
    <?php endif; ?>
</div>
<?php /**PATH D:\ManageX\resources\views\components\quick-actions.blade.php ENDPATH**/ ?>