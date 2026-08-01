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
        $pendingItems = $entitlementItems->filter(fn($item) => !in_array($item->id, $receivedItemIds));
        $receivedCount = $receivedTransactions->sum(fn($tx) => $tx->items->count());
    ?>

    <main class="min-h-screen pb-8">
        
        <nav class="flex w-full bg-white/90 backdrop-blur-sm sticky top-16 z-40 shadow-[0_1px_0_rgba(0,0,0,0.04)]">
            <button type="button"
                    class="flex-1 relative py-4 text-center font-label-md text-label-md transition-colors duration-150 active:scale-95 <?php echo e(request()->input('tab', 'pending') === 'pending' ? 'text-primary' : 'text-black/30'); ?>"
                    onclick="document.getElementById('tab-pending').style.display='';document.getElementById('tab-received').style.display='none';this.classList.add('text-primary');this.classList.remove('text-black/30');this.nextElementSibling.classList.add('text-black/30');this.nextElementSibling.classList.remove('text-primary');">
                Pending
                <?php if(request()->input('tab', 'pending') === 'pending'): ?>
                    <div class="active-tab-indicator"></div>
                <?php endif; ?>
            </button>
            <button type="button"
                    class="flex-1 relative py-4 text-center font-label-md text-label-md transition-colors duration-150 active:scale-95 <?php echo e(request()->input('tab') === 'received' ? 'text-primary' : 'text-black/30'); ?>"
                    onclick="document.getElementById('tab-received').style.display='';document.getElementById('tab-pending').style.display='none';this.classList.add('text-primary');this.classList.remove('text-black/30');this.previousElementSibling.classList.add('text-black/30');this.previousElementSibling.classList.remove('text-primary');">
                Received
                <?php if(request()->input('tab') === 'received'): ?>
                    <div class="active-tab-indicator"></div>
                <?php endif; ?>
            </button>
        </nav>

        
        <div class="px-container-margin pt-4 flex justify-between items-center">
            <span class="text-secondary/50 font-label-md text-label-md" id="items-counter"><?php echo e($pendingItems->count()); ?> ITEMS PENDING</span>
        </div>

        
        <div id="tab-pending" class="px-container-margin mt-4 space-y-4" style="<?php echo e(request()->input('tab') !== 'received' ? '' : 'display:none'); ?>">
            <?php if($pendingItems->isNotEmpty()): ?>
                <?php $__currentLoopData = $pendingItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="bg-white rounded-2xl p-5 shadow-card flex items-center justify-between active:scale-[0.99] transition-transform duration-200 cursor-pointer"
                         data-modal="barang:<?php echo e($item->id); ?>"
                         data-item-id="<?php echo e($item->id); ?>"
                         data-item-name="<?php echo e($item->name); ?>"
                         data-item-size="<?php echo e(is_array($selectedSizes[$item->id] ?? null) ? ($selectedSizes[$item->id]['size'] ?? '') : ($selectedSizes[$item->id] ?? '')); ?>"
                         data-item-status="received">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-xl bg-primary/5 flex items-center justify-center">
                            <span class="material-symbols-outlined text-primary text-2xl" style="font-variation-settings: 'FILL' 1;">checkroom</span>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <h3 class="font-headline-sm text-[15px] text-on-surface"><?php echo e($item->name); ?></h3>
                            <?php if(isset($selectedSizes[$item->id]) && !empty($selectedSizes[$item->id])): ?>
                                <div class="inline-flex items-center px-2.5 py-1 rounded-full bg-surface-variant/40 w-fit">
                                    <span class="text-[10px] font-semibold text-secondary uppercase tracking-wider">Ukuran <?php echo e(is_array($selectedSizes[$item->id]) ? ($selectedSizes[$item->id]['size'] ?? '-') : $selectedSizes[$item->id]); ?></span>
                                </div>
                            <?php else: ?>
                                <span class="text-[10px] font-semibold text-amber-600 uppercase tracking-wider">Ukuran belum dipilih</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="flex flex-col items-end gap-1">
                        <span class="font-semibold font-label-md text-label-md text-primary">Pending</span>
                        <span class="text-[10px] text-secondary/50">ID: #<?php echo e($item->id); ?></span>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <div class="bg-white rounded-2xl p-8 text-center">
                    <span class="material-symbols-outlined text-4xl text-secondary/30 mb-3">inventory_2</span>
                    <p class="font-body-md text-body-md text-secondary/50">Tidak ada barang pending</p>
                </div>
            <?php endif; ?>
        </div>

        
        <div id="tab-received" class="px-container-margin mt-4 space-y-4" style="<?php echo e(request()->input('tab') === 'received' ? '' : 'display:none'); ?>">
            <?php if($receivedTransactions->isNotEmpty()): ?>
                <?php $__currentLoopData = $receivedTransactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $__currentLoopData = $tx->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-white rounded-2xl p-5 shadow-card flex items-center justify-between active:scale-[0.99] transition-transform duration-200 cursor-pointer"
                     data-modal="barang:<?php echo e($item->id); ?>"
                     data-item-id="<?php echo e($item->id); ?>"
                     data-item-name="<?php echo e($item->name); ?>"
                     data-item-size="<?php echo e(is_array($selectedSizes[$item->id] ?? null) ? ($selectedSizes[$item->id]['size'] ?? '') : ($selectedSizes[$item->id] ?? '')); ?>"
                     data-item-status="pending">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-xl bg-emerald-50 flex items-center justify-center">
                                <span class="material-symbols-outlined text-emerald-600 text-2xl" style="font-variation-settings: 'FILL' 1;">checkroom</span>
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <h3 class="font-headline-sm text-[15px] text-on-surface"><?php echo e($item->item?->name ?? 'Item'); ?></h3>
                                <?php if($item->actual_size): ?>
                                    <div class="inline-flex items-center px-2.5 py-1 rounded-full bg-surface-variant/40 w-fit">
                                        <span class="text-[10px] font-semibold text-secondary uppercase tracking-wider">Ukuran <?php echo e($item->actual_size); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-1">
                            <span class="font-semibold font-label-md text-label-md text-emerald-600">Received</span>
                            <span class="text-[10px] text-secondary/50"><?php echo e($item->quantity); ?> pcs</span>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <div class="bg-white rounded-2xl p-8 text-center">
                    <span class="material-symbols-outlined text-4xl text-secondary/30 mb-3">inventory_2</span>
                    <p class="font-body-md text-body-md text-secondary/50">Tidak ada barang yang sudah diterima</p>
                </div>
            <?php endif; ?>
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
<?php /**PATH C:\laragon\www\Horizon-UniStock - Copy\resources\views/student/items.blade.php ENDPATH**/ ?>