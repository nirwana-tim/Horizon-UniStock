
<nav class="fixed bottom-0 w-full z-50 flex items-center h-[72px] bg-white/80 backdrop-blur-xl border-t border-black/[0.04] safe-area-bottom">
    <?php if (\Illuminate\Support\Facades\Blade::check('role', 'student')): ?>
    
    <a href="<?php echo e(route('dashboard')); ?>"
       class="flex-1 flex flex-col items-center justify-center gap-0.5 active:scale-90 transition-colors duration-200 <?php echo e(request()->routeIs('dashboard') ? 'text-primary active-nav' : 'text-black/30'); ?>"
       <?php echo e(request()->routeIs('dashboard') ? 'aria-current="page"' : ''); ?>>
        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' <?php echo e(request()->routeIs('dashboard') ? '1' : '0'); ?>, 'wght' 400, 'GRAD' 0, 'opsz' 24;">home</span>
        <span class="text-[10px] font-semibold tracking-wide">Beranda</span>
    </a>

    <a href="<?php echo e(route('student.sizes.index')); ?>"
       class="flex-1 flex flex-col items-center justify-center gap-0.5 active:scale-90 transition-colors duration-200 <?php echo e(request()->routeIs('student.sizes.*') ? 'text-primary active-nav' : 'text-black/30'); ?>"
       <?php echo e(request()->routeIs('student.sizes.*') ? 'aria-current="page"' : ''); ?>>
        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' <?php echo e(request()->routeIs('student.sizes.*') ? '1' : '0'); ?>, 'wght' 400, 'GRAD' 0, 'opsz' 24;">straighten</span>
        <span class="text-[10px] font-semibold tracking-wide">Ukuran</span>
    </a>

    <a href="<?php echo e(route('student.qr')); ?>"
       class="flex-1 flex flex-col items-center justify-center gap-0.5 active:scale-90 transition-colors duration-200 <?php echo e(request()->routeIs('student.qr') ? 'text-primary active-nav' : 'text-black/30'); ?>"
       <?php echo e(request()->routeIs('student.qr') ? 'aria-current="page"' : ''); ?>>
        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' <?php echo e(request()->routeIs('student.qr') ? '1' : '0'); ?>, 'wght' 400, 'GRAD' 0, 'opsz' 24;">qr_code_scanner</span>
        <span class="text-[10px] font-semibold tracking-wide">Scan</span>
    </a>

    <a href="<?php echo e(route('student.items.index')); ?>"
       class="flex-1 flex flex-col items-center justify-center gap-0.5 active:scale-90 transition-colors duration-200 <?php echo e(request()->routeIs('student.items.*') ? 'text-primary active-nav' : 'text-black/30'); ?>"
       <?php echo e(request()->routeIs('student.items.*') ? 'aria-current="page"' : ''); ?>>
        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' <?php echo e(request()->routeIs('student.items.*') ? '1' : '0'); ?>, 'wght' 400, 'GRAD' 0, 'opsz' 24;">inventory_2</span>
        <span class="text-[10px] font-semibold tracking-wide">Barang</span>
    </a>

    <a href="<?php echo e(route('profile.edit')); ?>"
       class="flex-1 flex flex-col items-center justify-center gap-0.5 active:scale-90 transition-colors duration-200 <?php echo e(request()->routeIs('profile.*') ? 'text-primary active-nav' : 'text-black/30'); ?>"
       <?php echo e(request()->routeIs('profile.*') ? 'aria-current="page"' : ''); ?>>
        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' <?php echo e(request()->routeIs('profile.*') ? '1' : '0'); ?>, 'wght' 400, 'GRAD' 0, 'opsz' 24;">person</span>
        <span class="text-[10px] font-semibold tracking-wide">Profil</span>
    </a>
    <?php endif; ?>

    <?php if (\Illuminate\Support\Facades\Blade::check('role', 'staff')): ?>
    
    <a href="<?php echo e(route('dashboard')); ?>"
       class="flex-1 flex flex-col items-center justify-center gap-0.5 active:scale-90 transition-colors duration-200 <?php echo e(request()->routeIs('dashboard') ? 'text-primary active-nav' : 'text-black/30'); ?>"
       <?php echo e(request()->routeIs('dashboard') ? 'aria-current="page"' : ''); ?>>
        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' <?php echo e(request()->routeIs('dashboard') ? '1' : '0'); ?>, 'wght' 400, 'GRAD' 0, 'opsz' 24;">home</span>
        <span class="text-[10px] font-semibold tracking-wide">Beranda</span>
    </a>

    <a href="<?php echo e(route('distribution.scan.index')); ?>"
       class="flex-1 flex flex-col items-center justify-center gap-0.5 active:scale-90 transition-colors duration-200 <?php echo e(request()->routeIs('distribution.scan.*') ? 'text-primary active-nav' : 'text-black/30'); ?>"
       <?php echo e(request()->routeIs('distribution.scan.*') ? 'aria-current="page"' : ''); ?>>
        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' <?php echo e(request()->routeIs('distribution.scan.*') ? '1' : '0'); ?>, 'wght' 400, 'GRAD' 0, 'opsz' 24;">qr_code_scanner</span>
        <span class="text-[10px] font-semibold tracking-wide">Scan</span>
    </a>

    <a href="<?php echo e(route('profile.edit')); ?>"
       class="flex-1 flex flex-col items-center justify-center gap-0.5 active:scale-90 transition-colors duration-200 <?php echo e(request()->routeIs('profile.*') ? 'text-primary active-nav' : 'text-black/30'); ?>"
       <?php echo e(request()->routeIs('profile.*') ? 'aria-current="page"' : ''); ?>>
        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' <?php echo e(request()->routeIs('profile.*') ? '1' : '0'); ?>, 'wght' 400, 'GRAD' 0, 'opsz' 24;">person</span>
        <span class="text-[10px] font-semibold tracking-wide">Profil</span>
    </a>
    <?php endif; ?>
</nav>
<?php /**PATH C:\laragon\www\Horizon-UniStock - Copy\resources\views/components/bottom-nav.blade.php ENDPATH**/ ?>