
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['type' => 'neutral']));

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

foreach (array_filter((['type' => 'neutral']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
$styles = [
    'success' => 'bg-green-100 text-green-800',
    'warning' => 'bg-amber-100 text-amber-800',
    'danger'  => 'bg-red-100 text-red-800',
    'info'    => 'bg-blue-100 text-blue-800',
    'neutral' => 'bg-gray-100 text-gray-700',
    'primary' => 'bg-primary-100 text-primary-800',
];
$style = $styles[$type] ?? $styles['neutral'];
?>

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($style); ?>">
    <?php echo e($slot); ?>

</span>
<?php /**PATH C:\laragon\www\Horizon-UniStock - Copy\resources\views/components/badge.blade.php ENDPATH**/ ?>