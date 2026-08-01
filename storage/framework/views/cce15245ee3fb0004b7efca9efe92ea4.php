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
    <?php if(session('success')): ?>
        <?php if (isset($component)) { $__componentOriginal5194778a3a7b899dcee5619d0610f5cf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5194778a3a7b899dcee5619d0610f5cf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.alert','data' => ['type' => 'success']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'success']); ?><?php echo e(session('success')); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5194778a3a7b899dcee5619d0610f5cf)): ?>
<?php $attributes = $__attributesOriginal5194778a3a7b899dcee5619d0610f5cf; ?>
<?php unset($__attributesOriginal5194778a3a7b899dcee5619d0610f5cf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5194778a3a7b899dcee5619d0610f5cf)): ?>
<?php $component = $__componentOriginal5194778a3a7b899dcee5619d0610f5cf; ?>
<?php unset($__componentOriginal5194778a3a7b899dcee5619d0610f5cf); ?>
<?php endif; ?>
    <?php endif; ?>

    <main class="flex-grow px-container-margin pt-6 pb-32 flex flex-col items-center">
        
        <div class="w-full max-w-sm bg-white rounded-2xl shadow-card p-8 flex flex-col items-center animate-pop">
            
            <div class="flex flex-col items-center mb-6">
                <h2 class="font-headline-sm text-headline-sm text-on-surface max-w-full min-w-0 break-words text-center leading-snug"><?php echo e($student->name); ?></h2>
                <p class="font-body-md text-body-md text-secondary/60 mt-0.5"><?php echo e($student->nim); ?></p>
            </div>

            
            <div class="w-full aspect-square bg-surface-container-low rounded-xl flex items-center justify-center relative mb-6 p-6 border border-black/[0.04]">
                <div class="absolute top-4 left-4 w-6 h-6 border-t-2 border-l-2 border-primary"></div>
                <div class="absolute top-4 right-4 w-6 h-6 border-t-2 border-r-2 border-primary"></div>
                <div class="absolute bottom-4 left-4 w-6 h-6 border-b-2 border-l-2 border-primary"></div>
                <div class="absolute bottom-4 right-4 w-6 h-6 border-b-2 border-r-2 border-primary"></div>
                <div class="w-full h-full bg-white rounded-xl flex items-center justify-center shadow-sm">
                    <img src="<?php echo e($qrDataUrl); ?>" alt="QR <?php echo e($student->nim); ?>" class="w-full h-full object-contain p-2">
                </div>
            </div>

            
            <a href="<?php echo e($qrDataUrl); ?>"
               download="qr-<?php echo e($student->nim); ?>.png"
               class="w-full h-14 bg-primary text-white rounded-full font-headline-sm text-headline-sm flex items-center justify-center gap-2 shadow-button active:scale-[0.98] transition-transform duration-200">
                <span class="material-symbols-outlined">download</span>
                Download QR PNG
            </a>

            
            <p class="mt-6 font-body-md text-body-md text-secondary/60 text-center leading-relaxed px-2">
                Tunjukkan QR ini pada petugas saat pengambilan paket untuk verifikasi identitas Anda.
            </p>
        </div>

        
        <div class="mt-6 grid grid-cols-2 gap-4 w-full max-w-sm">
            <div class="bg-white shadow-soft rounded-xl p-4">
                <span class="font-label-md text-label-md text-secondary/60 uppercase">Status</span>
                <p class="font-headline-sm text-headline-sm text-on-surface mt-1">Aktif</p>
            </div>
            <div class="bg-white shadow-soft rounded-xl p-4">
                <span class="font-label-md text-label-md text-secondary/60 uppercase">Valid Hingga</span>
                <p class="font-headline-sm text-headline-sm text-on-surface mt-1"><?php echo e(now()->year); ?></p>
            </div>
        </div>

        
        <?php if(isset($activeSchedules) && $activeSchedules->count()): ?>
        <div class="mt-6 w-full max-w-sm">
            <h3 class="font-headline-sm text-headline-sm text-on-background mb-3">Jadwal Distribusi</h3>
            <div class="bg-white shadow-card rounded-2xl overflow-hidden">
                <?php $__currentLoopData = $activeSchedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-center px-5 py-4 <?php echo e(!$loop->last ? 'divider-subtle' : ''); ?> active:bg-black/[0.02] transition-colors cursor-pointer">
                    <div class="w-12 h-12 rounded-xl bg-primary/5 flex flex-col items-center justify-center mr-4 shrink-0">
                        <span class="text-[10px] font-bold text-primary uppercase"><?php echo e(\Carbon\Carbon::parse($schedule->date)->format('M')); ?></span>
                        <span class="text-lg font-extrabold text-primary leading-tight"><?php echo e(\Carbon\Carbon::parse($schedule->date)->format('d')); ?></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-headline-sm text-[15px] text-on-background"><?php echo e($schedule->name); ?></h4>
                        <p class="font-body-md text-[13px] text-secondary/60 mt-0.5"><?php echo e($schedule->location); ?></p>
                    </div>
                    <span class="material-symbols-outlined text-primary shrink-0">chevron_right</span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>
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
<?php /**PATH C:\laragon\www\Horizon-UniStock - Copy\resources\views/student/qr-show.blade.php ENDPATH**/ ?>