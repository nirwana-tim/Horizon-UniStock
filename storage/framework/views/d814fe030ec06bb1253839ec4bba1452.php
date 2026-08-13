
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['title', 'subtitle' => null]));

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

foreach (array_filter((['title', 'subtitle' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="mb-6">
    <div class="flex items-start justify-between gap-4">
        <div class="min-w-0">
            <?php if(isset($breadcrumb)): ?>
                <nav class="flex items-center gap-1.5 text-xs text-gray-400 mb-1">
                    <?php echo e($breadcrumb); ?>

                </nav>
            <?php endif; ?>

            <h1 class="text-2xl font-bold text-gray-800 leading-tight"><?php echo e($title); ?></h1>

            <?php if($subtitle): ?>
                <p class="mt-1 text-sm text-gray-500"><?php echo e($subtitle); ?></p>
            <?php endif; ?>
        </div>

        <?php if(isset($actions)): ?>
            <div class="flex items-center gap-2 flex-shrink-0 mt-1">
                <?php echo e($actions); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\laragon\www\Horizon-UniStock - Copy\resources\views/components/page-header.blade.php ENDPATH**/ ?>