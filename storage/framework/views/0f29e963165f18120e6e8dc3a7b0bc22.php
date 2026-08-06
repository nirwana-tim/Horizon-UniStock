
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['title' => null, 'simple' => false]));

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

foreach (array_filter((['title' => null, 'simple' => false]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<header x-data="{}"
    class="bg-white border-b border-gray-200 h-14 flex items-center px-4 gap-4 flex-shrink-0 z-10">

    <?php if(!$simple): ?>
        
        <button class="lg:hidden p-1.5 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-colors"
            @click="$dispatch('toggle-sidebar')" aria-label="Buka menu navigasi">
            <svg aria-hidden="true" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    <?php endif; ?>

    
    <?php if($simple): ?>
        <a href="<?php echo e(route('dashboard')); ?>" class="flex items-center gap-2">
            <img src="<?php echo e(asset('images/logo.webp')); ?>" alt="<?php echo e(config('app.name', 'Horizon')); ?>"
                 width="28" height="28" fetchpriority="high"
                 class="w-7 h-7 object-contain">
            <span translate="no" class="font-headline-sm text-headline-sm text-primary font-bold"><?php echo e(config('app.name', 'Horizon')); ?></span>
        </a>
    <?php endif; ?>

    
    <div class="flex-1 min-w-0">
        <?php if(isset($breadcrumb)): ?>
            <?php echo e($breadcrumb); ?>

        <?php else: ?>
            <?php if($title): ?>
                <h1 class="text-sm font-semibold text-gray-700 truncate"><?php echo e($title); ?></h1>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    
    <?php if($simple): ?>
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open"
                class="flex items-center gap-2 p-1 rounded-lg hover:bg-gray-50 transition-colors">
                <div
                    class="w-8 h-8 bg-primary-700 rounded-full flex items-center justify-center text-white text-xs font-bold uppercase">
                    <?php echo e(substr(Auth::user()->name, 0, 2)); ?>

                </div>
            </button>

            <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                class="absolute right-0 top-full mt-1 w-48 bg-white rounded-lg border border-gray-200 shadow-lg py-1 z-50">
                <div class="px-3 py-2 border-b border-gray-100">
                    <p class="text-sm font-medium text-gray-800 truncate"><?php echo e(Auth::user()->name); ?></p>
                    <p class="text-xs text-gray-400 capitalize"><?php echo e(Auth::user()->getRoleNames()->first() ?? 'user'); ?>

                    </p>
                </div>
                <a href="<?php echo e(route('profile.edit')); ?>"
                    class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                    <svg aria-hidden="true" class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Profile
                </a>
                <a href="<?php echo e(route('logout')); ?>" onclick="event.preventDefault(); document.getElementById('logout-form').submit()"
                    class="w-full flex items-center gap-2 px-3 py-3 text-sm text-red-600 hover:bg-red-50 transition-colors">
                    <svg aria-hidden="true" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Logout
                </a>
            </div>
        </div>
    <?php endif; ?>

</header>
<?php /**PATH C:\laragon\www\Horizon-UniStock - Copy\resources\views/components/topbar.blade.php ENDPATH**/ ?>