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
        $hour = now()->format('H');
        if ($hour < 12) $greeting = 'Selamat Pagi';
        elseif ($hour < 15) $greeting = 'Selamat Siang';
        elseif ($hour < 18) $greeting = 'Selamat Sore';
        else $greeting = 'Selamat Malam';

        $currentBaju = $profile?->baju_size ?? null;
        $currentSepatu = $profile?->sepatu_size ?? null;
    ?>

    <main class="flex-1 flex flex-col">
        <div class="max-w-md mx-auto">
            
            <div class="mb-6 overflow-hidden rounded-2xl h-48 relative shadow-card">
                <div class="w-full h-full bg-cover bg-center" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCBt5P2HmtLID51h-uo7bahZP2QGNPaNUd_2L7Ek7GZEMKx0zkZ4TqbRU27lvZ2qQ4VITeTqkB_6VSZwUcm8rZUmO_OpP4Qxum37jkyH0-Kc5qWpdjnVTk5kEo6xr1pPYbpets4QQgDe8nKAMzKb10WM8zZWhZdx0O4o7z--UKhF2R2YUVm2nk25DjqqDuj4UjRB-2GUDAUZqv6tB3wppXKaQ-0EmOZ570do3Caux7jcLZH1IZqaROL')"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent flex items-end p-5">
                    <p class="text-white font-headline-sm text-headline-sm">Lengkapi Profil Distribusi</p>
                </div>
            </div>

            
            <div id="size-status" class="mb-4">
                <?php if($currentBaju || $currentSepatu): ?>
                    <div class="glass-card shadow-card rounded-2xl p-4 flex items-start gap-3">
                        <span class="material-symbols-outlined text-primary mt-0.5">checkroom</span>
                        <div>
                            <p class="font-body-md text-body-md text-on-surface font-semibold">
                                <?php if($currentBaju && $currentSepatu): ?>
                                    Ukuran: <?php echo e($currentBaju); ?> / <?php echo e($currentSepatu); ?>

                                <?php elseif($currentBaju): ?>
                                    Baju: <?php echo e($currentBaju); ?>

                                <?php else: ?>
                                    Sepatu: <?php echo e($currentSepatu); ?>

                                <?php endif; ?>
                            </p>
                            <p class="font-body-md text-[12px] text-secondary/60 mt-0.5">Ukuran baju dan sepatu kamu saat ini.</p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="glass-card shadow-card rounded-2xl p-4 flex items-start gap-3">
                        <span class="material-symbols-outlined text-primary mt-0.5">info</span>
                        <div>
                            <p class="font-body-md text-body-md text-on-surface font-semibold">Belum Ada Ukuran</p>
                            <p class="font-body-md text-[12px] text-secondary/60 mt-0.5">Pilih ukuran baju dan sepatu dari event yang tersedia.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            
            <section>
                <div class="flex justify-between items-center mb-4">
                    <h2 class="font-headline-sm text-headline-sm text-on-background">Jadwal Distribusi</h2>
                </div>

                <?php if($events->isEmpty()): ?>
                    
                    <div class="bg-white shadow-card rounded-2xl p-6 text-center">
                        <span class="material-symbols-outlined text-4xl text-secondary/30 mb-3">event_busy</span>
                        <p class="font-body-md text-body-md text-secondary/50">Tidak ada event aktif saat ini</p>
                        <p class="font-body-md text-[12px] text-secondary/40 mt-1">Silakan hubungi admin untuk informasi lebih lanjut.</p>
                    </div>
                <?php else: ?>
                    <div class="bg-white shadow-card rounded-2xl overflow-hidden">
                        <?php $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $sub = $submissions->get($event->id);
                                $subCount = $sub?->submission_count ?? 0;
                                $remaining = $event->max_changes - $subCount;
                                $isMaxed = $remaining <= 0;

                                $endDate = \Carbon\Carbon::parse($event->end_date);
                                $month = $endDate->format('M');
                                $day = $endDate->format('d');
                            ?>

                            <a href="<?php echo e(route('student.sizes.input', $event)); ?>"
                               class="flex items-center px-5 py-4 <?php echo e(!$loop->last ? 'divider-subtle' : ''); ?> active:bg-black/[0.02] transition-colors cursor-pointer group">
                                
                                <div class="w-12 h-12 rounded-xl <?php echo e($isMaxed ? 'bg-surface-variant/50' : 'bg-primary/5'); ?> flex flex-col items-center justify-center mr-4 shrink-0">
                                    <span class="text-[10px] font-bold <?php echo e($isMaxed ? 'text-secondary' : 'text-primary'); ?> uppercase"><?php echo e($month); ?></span>
                                    <span class="text-lg font-extrabold <?php echo e($isMaxed ? 'text-secondary' : 'text-primary'); ?> leading-tight"><?php echo e($day); ?></span>
                                </div>

                                
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-headline-sm text-[15px] text-on-background"><?php echo e($event->title); ?></h4>
                                    <p class="font-body-md text-[13px] text-secondary/60 mt-0.5">
                                        <?php echo e($event->start_date->format('d M')); ?> — <?php echo e($event->end_date->format('d M Y')); ?>

                                    </p>
                                    <?php if($event->max_changes > 0): ?>
                                        <p class="font-body-md text-[12px] text-secondary/50 mt-1">
                                            <?php if($isMaxed): ?>
                                                <span class="text-secondary/70">Selesai (<?php echo e($subCount); ?>/<?php echo e($event->max_changes); ?>)</span>
                                            <?php else: ?>
                                                <span class="text-primary">Sisa <?php echo e($remaining); ?>x pengisian</span>
                                            <?php endif; ?>
                                        </p>
                                    <?php endif; ?>
                                </div>

                                
                                <span class="material-symbols-outlined text-primary shrink-0 group-hover:translate-x-0.5 transition-transform">chevron_right</span>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>
            </section>
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
<?php /**PATH C:\laragon\www\Horizon-UniStock - Copy\resources\views/student/sizes-index.blade.php ENDPATH**/ ?>