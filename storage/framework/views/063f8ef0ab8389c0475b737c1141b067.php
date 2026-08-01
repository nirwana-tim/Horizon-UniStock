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
    <main class="px-container-margin pt-6 pb-8">
        <header class="mb-7">
            <h1 id="greeting" class="font-headline-lg-mobile text-headline-lg-mobile text-on-background"></h1>
            <p class="font-body-md text-secondary/70 mt-0.5"><?php echo e($student->nim); ?> &bull; <?php echo e($student->studyProgram?->name ?? '-'); ?></p>
        </header>

        <section id="email-card" class="mb-7"></section>

        <div class="space-y-7">
            <section id="jadwal-distribusi">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="font-headline-sm text-headline-sm text-on-background">Jadwal Distribusi</h2>
                    <button type="button" class="text-primary font-label-md hover:underline transition-transform duration-150 active:scale-95" data-scroll="jadwal-distribusi">Lihat Semua</button>
                </div>

                <?php if($sizeEvents->isNotEmpty()): ?>
                    <div class="bg-white shadow-card rounded-2xl overflow-hidden">
                        <?php $__currentLoopData = $sizeEvents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $endDate = \Carbon\Carbon::parse($event->end_date);
                                $month = $endDate->format('M');
                                $day = $endDate->format('d');
                                $isOdd = $loop->iteration % 2 === 1;
                            ?>

                            <div class="flex items-center px-5 py-4 <?php echo e(!$loop->last ? 'divider-subtle' : ''); ?> active:bg-black/[0.02] transition-colors cursor-pointer group"
                                 data-modal="schedule-<?php echo e($loop->iteration); ?>"
                                 data-event-title="<?php echo e($event->title); ?>"
                                 data-event-start="<?php echo e($event->start_date->format('d M Y')); ?>"
                                 data-event-end="<?php echo e($event->end_date->format('d M Y')); ?>"
                                 data-event-location="<?php echo e($event->description ?? ''); ?>"
                                 data-event-note="">
                                <div class="w-12 h-12 rounded-xl <?php echo e($isOdd ? 'bg-primary/5' : 'bg-surface-variant/50'); ?> flex flex-col items-center justify-center mr-4 shrink-0">
                                    <span class="text-[10px] font-bold <?php echo e($isOdd ? 'text-primary' : 'text-secondary'); ?> uppercase"><?php echo e($month); ?></span>
                                    <span class="text-lg font-extrabold <?php echo e($isOdd ? 'text-primary' : 'text-secondary'); ?> leading-tight"><?php echo e($day); ?></span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-headline-sm text-[15px] text-on-background"><?php echo e($event->title); ?></h4>
                                    <p class="font-body-md text-[13px] text-secondary/60 mt-0.5"><?php echo e($event->description ?? $event->start_date->format('d M') . ' — ' . $event->end_date->format('d M Y')); ?></p>
                                </div>
                                <span class="material-symbols-outlined text-primary shrink-0 group-hover:translate-x-0.5 transition-transform">chevron_right</span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </main>

    <?php $__env->startPush('scripts'); ?>
    <script>
        (function() {
            var hour = new Date().getHours();
            var greeting;
            if (hour >= 3 && hour < 12) {
                greeting = 'Selamat Pagi';
            } else if (hour >= 12 && hour < 15) {
                greeting = 'Selamat Siang';
            } else if (hour >= 15 && hour < 21) {
                greeting = 'Selamat Sore';
            } else {
                greeting = 'Selamat Malam';
            }
            var fullName = '<?php echo e($student->name); ?>';
            var firstName = fullName.trim().split(/\s+/)[0];
            document.getElementById('greeting').textContent = greeting + ', ' + firstName + '!';
        })();
    </script>
    <?php $__env->stopPush(); ?>
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
<?php /**PATH C:\laragon\www\Horizon-UniStock - Copy\resources\views/dashboards/student.blade.php ENDPATH**/ ?>