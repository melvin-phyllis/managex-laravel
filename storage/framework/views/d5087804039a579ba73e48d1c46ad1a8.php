<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'events' => [],
    'maxItems' => 5,
    'emptyMessage' => 'Aucun événement à venir'
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
    'events' => [],
    'maxItems' => 5,
    'emptyMessage' => 'Aucun événement à venir'
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php
$eventTypes = [
    'leave' => [
        'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>',
        'bg' => 'bg-green-100',
        'text' => 'text-green-600',
        'border' => 'border-green-200',
        'label' => 'Congé'
    ],
    'task' => [
        'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>',
        'bg' => 'bg-blue-100',
        'text' => 'text-blue-600',
        'border' => 'border-blue-200',
        'label' => 'Tâche'
    ],
    'survey' => [
        'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>',
        'bg' => 'bg-indigo-100',
        'text' => 'text-indigo-600',
        'border' => 'border-indigo-200',
        'label' => 'Sondage'
    ],
    'deadline' => [
        'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
        'bg' => 'bg-yellow-100',
        'text' => 'text-yellow-600',
        'border' => 'border-yellow-200',
        'label' => 'Échéance'
    ],
    'payroll' => [
        'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
        'bg' => 'bg-emerald-100',
        'text' => 'text-emerald-600',
        'border' => 'border-emerald-200',
        'label' => 'Paie'
    ]
];
?>

<div <?php echo e($attributes->merge(['class' => 'bg-white rounded-xl shadow-sm border border-gray-100 animate-fade-in-up'])); ?>>
    
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <div class="flex items-center space-x-2">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <h3 class="font-semibold text-gray-900">Événements à venir</h3>
        </div>
        <?php if(count($events) > $maxItems): ?>
            <span class="text-xs text-gray-400"><?php echo e(count($events)); ?> total</span>
        <?php endif; ?>
    </div>

    
    <div class="divide-y divide-gray-50">
        <?php $__empty_1 = true; $__currentLoopData = array_slice($events, 0, $maxItems); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $type = $event['type'] ?? 'task';
                $typeConfig = $eventTypes[$type] ?? $eventTypes['task'];
                $daysUntil = isset($event['date']) ? \Carbon\Carbon::parse($event['date'])->diffInDays(now()) : 0;
                $isToday = $daysUntil === 0;
                $isTomorrow = $daysUntil === 1;
                $isUrgent = $daysUntil <= 2;
            ?>

            <a href="<?php echo e($event['link'] ?? '#'); ?>"
               class="block px-6 py-4 hover:bg-gray-50 transition-colors group"
               style="animation-delay: <?php echo e($index * 50); ?>ms">
                <div class="flex items-start space-x-4">
                    
                    <div class="flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full <?php echo e($typeConfig['bg']); ?> <?php echo e($typeConfig['text']); ?> flex items-center justify-center">
                            <?php echo $typeConfig['icon']; ?>

                        </div>
                        <?php if($index < min(count($events), $maxItems) - 1): ?>
                            <div class="w-0.5 h-full bg-gray-100 mt-2"></div>
                        <?php endif; ?>
                    </div>

                    
                    <div class="flex-1 min-w-0 pb-4">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-medium <?php echo e($typeConfig['text']); ?> <?php echo e($typeConfig['bg']); ?> px-2 py-0.5 rounded-full">
                                <?php echo e($typeConfig['label']); ?>

                            </span>
                            <span class="text-xs <?php echo e($isUrgent ? 'text-red-600 font-medium' : 'text-gray-400'); ?>">
                                <?php if($isToday): ?>
                                    Aujourd'hui
                                <?php elseif($isTomorrow): ?>
                                    Demain
                                <?php else: ?>
                                    Dans <?php echo e($daysUntil); ?> jours
                                <?php endif; ?>
                            </span>
                        </div>

                        <h4 class="text-sm font-medium text-gray-900 group-hover:text-blue-600 transition-colors truncate">
                            <?php echo e($event['title']); ?>

                        </h4>

                        <?php if(isset($event['subtitle'])): ?>
                            <p class="text-xs text-gray-500 mt-0.5"><?php echo e($event['subtitle']); ?></p>
                        <?php endif; ?>

                        <?php if(isset($event['date'])): ?>
                            <p class="text-xs text-gray-400 mt-1 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <?php echo e(\Carbon\Carbon::parse($event['date'])->format('d/m/Y')); ?>

                            </p>
                        <?php endif; ?>
                    </div>

                    
                    <svg class="w-5 h-5 text-gray-300 group-hover:text-gray-500 transition-colors flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="px-6 py-12 text-center">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-gray-500 text-sm"><?php echo e($emptyMessage); ?></p>
                <p class="text-gray-400 text-xs mt-1">Profitez de votre temps libre !</p>
            </div>
        <?php endif; ?>
    </div>

    
    <?php if(count($events) > $maxItems): ?>
        <div class="px-6 py-3 border-t border-gray-100 text-center">
            <a href="#" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                Voir tous les événements (<?php echo e(count($events)); ?>)
            </a>
        </div>
    <?php endif; ?>
</div>
<?php /**PATH D:\ManageX\resources\views\components\upcoming-events.blade.php ENDPATH**/ ?>