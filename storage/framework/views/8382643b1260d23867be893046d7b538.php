<?php if (isset($component)) { $__componentOriginal69dc84650370d1d4dc1b42d016d7226b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal69dc84650370d1d4dc1b42d016d7226b = $attributes; } ?>
<?php $component = App\View\Components\GuestLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('guest-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\GuestLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    
    <div class="mb-6">
        <h2 class="font-headline-lg-mobile text-headline-lg-mobile text-on-surface">Masuk</h2>
        <p class="mt-1 font-body-md text-body-md text-secondary/60">Masukkan kredensial kamu untuk melanjutkan</p>
    </div>

    <?php if (isset($component)) { $__componentOriginal7c1bf3a9346f208f66ee83b06b607fb5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c1bf3a9346f208f66ee83b06b607fb5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.auth-session-status','data' => ['class' => 'mb-4','status' => session('status')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('auth-session-status'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mb-4','status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(session('status'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7c1bf3a9346f208f66ee83b06b607fb5)): ?>
<?php $attributes = $__attributesOriginal7c1bf3a9346f208f66ee83b06b607fb5; ?>
<?php unset($__attributesOriginal7c1bf3a9346f208f66ee83b06b607fb5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7c1bf3a9346f208f66ee83b06b607fb5)): ?>
<?php $component = $__componentOriginal7c1bf3a9346f208f66ee83b06b607fb5; ?>
<?php unset($__componentOriginal7c1bf3a9346f208f66ee83b06b607fb5); ?>
<?php endif; ?>

    <form method="POST" action="<?php echo e(route('login')); ?>" class="space-y-4">
        <?php echo csrf_field(); ?>

        
        <div>
            <label for="email" class="block font-label-md text-label-md text-secondary/70 mb-1.5">
                NIM <span class="text-error">*</span>
            </label>
            <input id="email" type="text" name="email" value="<?php echo e(old('email')); ?>" required autofocus
                autocomplete="username" placeholder="Masukkan NIM"
                class="w-full h-12 px-4 rounded-xl bg-surface-container-low border border-black/[0.06] font-body-md text-on-surface placeholder-secondary/40 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-error focus:border-error focus:ring-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="mt-1 text-xs text-error flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">error</span>
                    <?php echo e($message); ?>

                </p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        
        <div x-data="{ show: false }">
            <label for="password" class="block font-label-md text-label-md text-secondary/70 mb-1.5">
                Password <span class="text-error">*</span>
            </label>
            <div class="relative">
                <input id="password" :type="show ? 'text' : 'password'" name="password" required
                    autocomplete="current-password" placeholder="Masukkan password"
                    class="w-full h-12 px-4 pr-12 rounded-xl bg-surface-container-low border border-black/[0.06] font-body-md text-on-surface placeholder-secondary/40 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-error focus:border-error focus:ring-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <button type="button" @click="show = !show"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-secondary/40 hover:text-secondary transition-colors">
                    
                    <svg x-show="!show" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    
                    <svg x-show="show" x-cloak class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.879L21 21"/>
                    </svg>
                </button>
            </div>
            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="mt-1 text-xs text-error flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">error</span>
                    <?php echo e($message); ?>

                </p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        
        <div>
            <label class="block font-label-md text-label-md text-secondary/70 mb-1.5">
                Verifikasi <span class="text-error">*</span>
            </label>
            <div class="flex items-center gap-3 mb-2">
                <img src="<?php echo e(captcha_src('math')); ?>" alt="captcha" id="captcha-img"
                     class="rounded-xl border border-black/[0.06] h-12">
                <button type="button" onclick="document.getElementById('captcha-img').src='<?php echo e(captcha_src('math')); ?>&'+Math.random()"
                        class="text-secondary/40 hover:text-secondary transition-colors flex-shrink-0"
                        title="Reload CAPTCHA">
                    <span class="material-symbols-outlined text-xl">refresh</span>
                </button>
            </div>
            <input type="text" name="captcha" placeholder="Hasil penjumlahan"
                   class="w-full h-12 px-4 rounded-xl bg-surface-container-low border border-black/[0.06] font-body-md text-on-surface placeholder-secondary/40 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors <?php $__errorArgs = ['captcha'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-error focus:border-error focus:ring-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
            <?php $__errorArgs = ['captcha'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="mt-1 text-xs text-error flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">error</span>
                    <?php echo e($message); ?>

                </p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        
        <div class="flex items-center gap-2 cursor-pointer">
            <input id="remember_me" type="checkbox" name="remember"
                class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary">
            <label for="remember_me" class="font-body-md text-body-md text-secondary/70 cursor-pointer">Ingat saya</label>
        </div>

        
        <button type="submit" id="btn-login"
            class="w-full h-12 bg-primary text-white rounded-full font-headline-sm text-headline-sm shadow-button active:scale-[0.98] transition-transform duration-200 mt-2">
            Masuk
        </button>

        
        <?php if(Route::has('password.request')): ?>
            <div class="text-center pt-2">
                <p class="font-body-md text-body-md text-secondary/50">
                    Lupa password?
                    <a href="<?php echo e(route('password.request')); ?>" class="text-primary font-semibold hover:underline">Klik di sini</a>
                </p>
            </div>
        <?php endif; ?>
    </form>

<?php $__env->startPush('scripts'); ?>
<script>
    setInterval(function () {
        document.getElementById('captcha-img').src =
            '<?php echo e(captcha_src("math")); ?>&' + Date.now();
    }, 90000);
</script>
<?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal69dc84650370d1d4dc1b42d016d7226b)): ?>
<?php $attributes = $__attributesOriginal69dc84650370d1d4dc1b42d016d7226b; ?>
<?php unset($__attributesOriginal69dc84650370d1d4dc1b42d016d7226b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal69dc84650370d1d4dc1b42d016d7226b)): ?>
<?php $component = $__componentOriginal69dc84650370d1d4dc1b42d016d7226b; ?>
<?php unset($__componentOriginal69dc84650370d1d4dc1b42d016d7226b); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\Horizon-UniStock - Copy\resources\views/auth/login.blade.php ENDPATH**/ ?>