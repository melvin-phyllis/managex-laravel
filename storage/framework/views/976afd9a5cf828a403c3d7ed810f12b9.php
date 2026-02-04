<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['status', 'type' => 'default']));

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

foreach (array_filter((['status', 'type' => 'default']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php
$colorClasses = match($status) {
    'pending' => 'bg-yellow-100 text-yellow-800',
    'approved' => 'bg-green-100 text-green-800',
    'rejected' => 'bg-red-100 text-red-800',
    'completed' => 'bg-amber-100 text-amber-800',
    'validated' => 'bg-emerald-100 text-emerald-800',
    'paid' => 'bg-green-100 text-green-800',
    'active' => 'bg-green-100 text-green-800',
    'inactive' => 'bg-gray-100 text-gray-800',
    'low' => 'bg-green-100 text-green-800',
    'medium' => 'bg-yellow-100 text-yellow-800',
    'high' => 'bg-red-100 text-red-800',
    'conge' => 'bg-blue-100 text-blue-800',
    'maladie' => 'bg-purple-100 text-purple-800',
    'autre' => 'bg-gray-100 text-gray-800',
    default => 'bg-gray-100 text-gray-800',
};

$labels = match($status) {
    'pending' => 'En attente',
    'approved' => 'En cours',
    'rejected' => 'Refusé',
    'completed' => 'Terminé (à valider)',
    'validated' => 'Validé',
    'paid' => 'Payé',
    'active' => 'Actif',
    'inactive' => 'Inactif',
    'low' => 'Basse',
    'medium' => 'Moyenne',
    'high' => 'Haute',
    'conge' => 'Congé',
    'maladie' => 'Maladie',
    'autre' => 'Autre',
    default => $status,
};
?>

<span <?php echo e($attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium $colorClasses"])); ?>>
    <?php echo e($slot->isEmpty() ? $labels : $slot); ?>

</span>
<?php /**PATH D:\ManageX\resources\views\components\status-badge.blade.php ENDPATH**/ ?>