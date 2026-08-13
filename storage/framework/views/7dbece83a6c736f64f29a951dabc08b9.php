
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['title' => 'No Data Yet', 'description' => null]));

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

foreach (array_filter((['title' => 'No Data Yet', 'description' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="flex flex-col items-center justify-center py-16 text-center px-4">

    
    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
        <?php if(isset($icon)): ?>
            <?php echo e($icon); ?>

        <?php else: ?>
            <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
        <?php endif; ?>
    </div>

    <h3 class="text-base font-semibold text-gray-700 mb-1"><?php echo e($title); ?></h3>

    <?php if($description): ?>
        <p class="text-sm text-gray-500 max-w-xs mb-4"><?php echo e($description); ?></p>
    <?php endif; ?>

    <?php if(isset($actions)): ?>
        <div class="flex items-center gap-2 mt-2">
            <?php echo e($actions); ?>

        </div>
    <?php endif; ?>

</div>
<?php /**PATH C:\laragon\www\Horizon-UniStock - Copy\resources\views/components/empty-state.blade.php ENDPATH**/ ?>