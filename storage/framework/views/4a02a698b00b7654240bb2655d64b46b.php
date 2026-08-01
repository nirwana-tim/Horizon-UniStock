<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php
        $user = auth()->user();
        $student = $user->student ?? null;
    ?>

    <main class="flex-grow px-container-margin pt-8 pb-32">
        
        <section class="flex flex-col items-center mb-8 text-center">
            <div class="w-24 h-24 rounded-full bg-primary flex items-center justify-center text-white text-3xl font-bold mb-5 shadow-xl border-4 border-white ring-1 ring-black/[0.04]">
                <?php echo e(substr($user->name, 0, 2)); ?>

            </div>
            <h1 class="font-headline-lg-mobile text-headline-lg-mobile text-on-surface mb-1 max-w-full min-w-0 break-words"><?php echo e($user->name); ?></h1>
            <p class="font-body-lg text-body-lg text-secondary/60">NIM: <?php echo e($student->nim ?? '-'); ?></p>
        </section>

        
        <section class="bg-white rounded-2xl p-5 mb-6 shadow-card">
            <div class="space-y-4">
                <div class="flex justify-between items-start gap-3 pb-3 divider-subtle">
                    <span class="font-label-md text-label-md text-secondary/60 uppercase tracking-wider shrink-0">Program Studi</span>
                    <span class="font-body-md text-body-md text-on-surface font-semibold text-right min-w-0 break-words"><?php echo e($student->studyProgram?->name ?? '-'); ?></span>
                </div>
                <div class="flex justify-between items-start gap-3 pb-3 divider-subtle">
                    <span class="font-label-md text-label-md text-secondary/60 uppercase tracking-wider shrink-0">Fakultas</span>
                    <span class="font-body-md text-body-md text-on-surface font-semibold text-right min-w-0 break-words"><?php echo e($student->studyProgram?->faculty?->name ?? '-'); ?></span>
                </div>
                <div class="flex justify-between items-start gap-3">
                    <span class="font-label-md text-label-md text-secondary/60 uppercase tracking-wider shrink-0">Email</span>
                    <span class="font-body-md text-body-md text-on-surface font-semibold text-right min-w-0 break-all"><?php echo e($user->email); ?></span>
                </div>
            </div>
        </section>

        
        <section class="flex flex-col gap-4">
            <button type="button"
                    class="w-full h-14 rounded-full border-2 border-primary text-primary font-semibold text-body-lg flex items-center justify-center gap-2 active:scale-[0.98] transition-colors duration-200 hover:bg-primary/5"
                    data-modal="change-password">
                <span class="material-symbols-outlined">lock_reset</span>
                Ubah Password
            </button>
            <button type="button"
                    class="w-full h-14 rounded-full bg-primary text-white font-semibold text-body-lg flex items-center justify-center gap-2 shadow-button active:scale-[0.98] transition-transform duration-200"
                    data-modal="verify-email">
                <span class="material-symbols-outlined">verified_user</span>
                Kelola Email
            </button>
        </section>

        
        <div id="modal-password" class="hidden fixed inset-0 bg-black/30 z-[100] items-end justify-center" onclick="if(event.target===this)this.style.display='none'">
            <div class="bg-white rounded-t-2xl w-full max-w-lg p-6 pt-3 pb-8 max-h-[85vh] overflow-y-auto">
                <div class="w-10 h-1 bg-black/20 rounded-full mx-auto mb-4"></div>
                <h3 class="font-headline-sm text-headline-sm text-on-surface mb-5">Ubah Password</h3>
                <?php echo $__env->make('profile.partials.update-password-form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <button type="button" onclick="document.getElementById('modal-password').style.display='none'"
                        class="mt-4 w-full h-12 rounded-full border-2 border-secondary/20 text-secondary/60 font-headline-sm text-headline-sm active:scale-[0.98] transition-transform duration-200">
                    Tutup
                </button>
            </div>
        </div>

        <div id="modal-profile" class="hidden fixed inset-0 bg-black/30 z-[100] items-end justify-center" onclick="if(event.target===this)this.style.display='none'">
            <div class="bg-white rounded-t-2xl w-full max-w-lg p-6 pt-3 pb-8 max-h-[85vh] overflow-y-auto">
                <div class="w-10 h-1 bg-black/20 rounded-full mx-auto mb-4"></div>
                <h3 class="font-headline-sm text-headline-sm text-on-surface mb-5">Edit Profil</h3>
                <?php echo $__env->make('profile.partials.update-profile-information-form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <button type="button" onclick="document.getElementById('modal-profile').style.display='none'"
                        class="mt-4 w-full h-12 rounded-full border-2 border-secondary/20 text-secondary/60 font-headline-sm text-headline-sm active:scale-[0.98] transition-transform duration-200">
                    Tutup
                </button>
            </div>
        </div>
    </main>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\Horizon-UniStock - Copy\resources\views/profile/edit.blade.php ENDPATH**/ ?>