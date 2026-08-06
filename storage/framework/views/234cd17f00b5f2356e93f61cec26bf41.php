<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'route',
    'label' => 'Delete Data',
    'description' => 'Are you sure you want to delete this data? This action cannot be undone.',
    'iconOnly' => false,
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
    'route',
    'label' => 'Delete Data',
    'description' => 'Are you sure you want to delete this data? This action cannot be undone.',
    'iconOnly' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div x-data="{ open: false }">
    <?php if($iconOnly): ?>
        <button type="button" @click="open = true"
                class="inline-flex items-center justify-center p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                title="<?php echo e($label); ?>">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
        </button>
    <?php else: ?>
        <button type="button" @click="open = true"
            class="inline-flex items-center px-2.5 py-1 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <?php echo e(__('Delete')); ?>

        </button>
    <?php endif; ?>

    
    <div x-show="open" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4">

        
        <div @click="open = false" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm"></div>

        
        <div class="relative bg-white rounded-xl shadow-xl p-6 w-full max-w-md mx-4"
             @click.outside="open = false">
            
            <div class="mx-auto w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
            </div>

            
            <h3 class="text-lg font-semibold text-gray-900 text-center mb-2"><?php echo e($label); ?></h3>
            <p class="text-sm text-gray-500 text-center mb-6"><?php echo e($description); ?></p>

            
            <div class="flex gap-3 justify-center">
                <form action="<?php echo e($route); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <?php echo e(__('Yes, Delete')); ?>

                    </button>
                </form>
                <button type="button" @click="open = false"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <?php echo e(__('Cancel')); ?>

                </button>
            </div>
        </div>
    </div>
</div><?php /**PATH C:\laragon\www\Horizon-UniStock - Copy\resources\views/components/delete-modal.blade.php ENDPATH**/ ?>