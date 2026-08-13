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
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Sales & Stock Analytics','subtitle' => 'Dashboard analitik penjualan dan ketersediaan stok barang']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Sales & Stock Analytics','subtitle' => 'Dashboard analitik penjualan dan ketersediaan stok barang']); ?>
         <?php $__env->slot('breadcrumb', null, []); ?> 
            <a href="<?php echo e(route('report.index')); ?>" class="text-gray-500 hover:text-gray-700 transition-colors">Reports</a>
            <span class="text-gray-300 mx-2">/</span>
            <span class="text-gray-800 font-medium">Sales Dashboard</span>
         <?php $__env->endSlot(); ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e)): ?>
<?php $attributes = $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e; ?>
<?php unset($__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e)): ?>
<?php $component = $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e; ?>
<?php unset($__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e); ?>
<?php endif; ?>

    <div x-data="salesDashboard" class="space-y-6">
        
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-primary-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                Filter Analitik
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Start Date</label>
                    <input type="date" x-model="startDate" class="w-full rounded-lg border-gray-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">End Date</label>
                    <input type="date" x-model="endDate" class="w-full rounded-lg border-gray-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Item Group</label>
                    <select x-model="categoryId" class="w-full rounded-lg border-gray-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                        <option value="">Semua Kategori</option>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($category->id); ?>"><?php echo e($category->label); ?> (<?php echo e($category->code); ?>)</option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Item Selected</label>
                    <select x-model="itemId" :disabled="!categoryId" class="w-full rounded-lg border-gray-200 text-sm focus:border-primary-500 focus:ring-primary-500 disabled:bg-gray-50 disabled:text-gray-400">
                        <option value="">Semua Barang</option>
                        <template x-for="item in items" :key="item.id">
                            <option :value="item.id" x-text="item.name"></option>
                        </template>
                    </select>
                    <span x-show="!categoryId" class="text-[10px] text-gray-400 mt-1 block italic">*choose the item group first</span>
                </div>
            </div>
        </div>

        
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-4 py-2.5 border-b border-gray-100 flex items-center gap-2">
                <svg class="w-4 h-4 text-primary-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z" />
                </svg>
                <h3 class="text-xs font-semibold text-gray-800 uppercase tracking-wider">Ringkasan Kategori</h3>
            </div>
            <div class="flex overflow-x-auto divide-x divide-gray-100">
                <div class="sticky left-0 z-10 flex-shrink-0 px-4 py-3 min-w-[110px] bg-primary-50 border-r border-gray-200 shadow-[4px_0_10px_-4px_rgba(0,0,0,0.08)]">
                    <div class="text-[11px] font-semibold text-primary-800 uppercase tracking-wide mb-1">Grand Total</div>
                    <div class="text-lg font-bold text-primary-900 leading-tight" x-text="kpis.grand_total ? kpis.grand_total.sold.toLocaleString('id-ID') : 0">0</div>
                    <div class="text-[10px] text-primary-700">
                        Stok: <span class="font-medium" x-text="kpis.grand_total ? kpis.grand_total.stock.toLocaleString('id-ID') : 0">0</span>
                    </div>
                </div>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $code = strtolower($cat->code); ?>
                    <div class="flex-shrink-0 px-4 py-3 min-w-[96px] hover:bg-gray-50 transition-colors">
                        <div class="text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1"><?php echo e($cat->code); ?></div>
                        <div class="text-lg font-bold text-primary-700 leading-tight" x-text="kpis['<?php echo e($code); ?>'] ? kpis['<?php echo e($code); ?>'].sold.toLocaleString('id-ID') : 0">0</div>
                        <div class="text-[10px] text-gray-400">
                            Stok: <span class="font-medium text-gray-500" x-text="kpis['<?php echo e($code); ?>'] ? kpis['<?php echo e($code); ?>'].stock.toLocaleString('id-ID') : 0">0</span>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        
        <?php if($lowStockItems->isNotEmpty() || $outOfStockItems->isNotEmpty()): ?>
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-4 py-2.5 border-b border-gray-100 flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <h3 class="text-xs font-semibold text-gray-800 uppercase tracking-wider">Peringatan Stok</h3>
                    <?php if (isset($component)) { $__componentOriginal2ddbc40e602c342e508ac696e52f8719 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ddbc40e602c342e508ac696e52f8719 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['type' => 'warning']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'warning']); ?><?php echo e($lowStockItems->count()); ?> stok rendah <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $attributes = $__attributesOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $component = $__componentOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__componentOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginal2ddbc40e602c342e508ac696e52f8719 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ddbc40e602c342e508ac696e52f8719 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['type' => 'danger']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'danger']); ?><?php echo e($outOfStockItems->count()); ?> habis <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $attributes = $__attributesOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $component = $__componentOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__componentOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Varian</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Min. Stok</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            <?php $__currentLoopData = $outOfStockItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $balance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-800"><?php echo e($balance->item->name); ?></td>
                                    <td class="px-4 py-2 text-sm text-gray-500"><?php echo e($balance->item->category?->label ?? '-'); ?></td>
                                    <td class="px-4 py-2 text-sm text-gray-500"><?php echo e($balance->variant?->size_label ?? '-'); ?></td>
                                    <td class="px-4 py-2 text-sm font-semibold text-red-600"><?php echo e($balance->quantity); ?></td>
                                    <td class="px-4 py-2 text-sm text-gray-500"><?php echo e($balance->item->min_stock ?? '-'); ?></td>
                                    <td class="px-4 py-2"><?php if (isset($component)) { $__componentOriginal2ddbc40e602c342e508ac696e52f8719 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ddbc40e602c342e508ac696e52f8719 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['type' => 'danger']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'danger']); ?>Habis <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $attributes = $__attributesOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $component = $__componentOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__componentOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php $__currentLoopData = $lowStockItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $balance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-800"><?php echo e($balance->item->name); ?></td>
                                    <td class="px-4 py-2 text-sm text-gray-500"><?php echo e($balance->item->category?->label ?? '-'); ?></td>
                                    <td class="px-4 py-2 text-sm text-gray-500"><?php echo e($balance->variant?->size_label ?? '-'); ?></td>
                                    <td class="px-4 py-2 text-sm font-semibold text-amber-600"><?php echo e($balance->quantity); ?></td>
                                    <td class="px-4 py-2 text-sm text-gray-500"><?php echo e($balance->item->min_stock ?? 5); ?></td>
                                    <td class="px-4 py-2"><?php if (isset($component)) { $__componentOriginal2ddbc40e602c342e508ac696e52f8719 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ddbc40e602c342e508ac696e52f8719 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['type' => 'warning']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'warning']); ?>Stok Rendah <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $attributes = $__attributesOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $component = $__componentOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__componentOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Chart 1: Unit Sold by Items -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex flex-col justify-between">
                <div class="mb-4">
                    <h4 class="text-sm font-semibold text-gray-800">Unit Sold by Items</h4>
                    <span class="text-[10px] text-gray-400">Total unit barang yang terdistribusikan</span>
                </div>
                <div class="h-64 w-full relative">
                    <canvas id="c1Chart"></canvas>
                </div>
            </div>

            <!-- Chart 2: Revenue by Items -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex flex-col justify-between">
                <div class="mb-4">
                    <h4 class="text-sm font-semibold text-gray-800">Revenue by Items</h4>
                    <span class="text-[10px] text-gray-400">Total nominal penjualan dalam Rupiah</span>
                </div>
                <div class="h-64 w-full relative">
                    <canvas id="c2Chart"></canvas>
                </div>
            </div>

            <!-- Chart 3: Total Revenue and Unit Sold by Month -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex flex-col justify-between">
                <div class="mb-4">
                    <h4 class="text-sm font-semibold text-gray-800">Total Revenue and Unit Sold by Month</h4>
                    <span class="text-[10px] text-gray-400">Tren penjualan bulanan (Combo Chart)</span>
                </div>
                <div class="h-64 w-full relative">
                    <canvas id="c3Chart"></canvas>
                </div>
            </div>

            <!-- Chart 4: Available Stock -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex flex-col justify-between">
                <div class="mb-4">
                    <h4 class="text-sm font-semibold text-gray-800">Available Stock</h4>
                    <span class="text-[10px] text-gray-400">Jumlah fisik stok yang tersedia saat ini</span>
                </div>
                <div class="h-64 w-full relative">
                    <canvas id="c4Chart"></canvas>
                </div>
            </div>

            <!-- Chart 5: Value Stock -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex flex-col justify-between">
                <div class="mb-4">
                    <h4 class="text-sm font-semibold text-gray-800">Value Stock</h4>
                    <span class="text-[10px] text-gray-400">Nilai valuasi stok (Stok x HPP)</span>
                </div>
                <div class="h-64 w-full relative">
                    <canvas id="c5Chart"></canvas>
                </div>
            </div>

            <!-- Chart 6: % Unit Sold -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex flex-col justify-between">
                <div class="mb-4">
                    <h4 class="text-sm font-semibold text-gray-800">% Unit Sold</h4>
                    <span class="text-[10px] text-gray-400">Kontribusi penjualan per item (%)</span>
                </div>
                <div class="h-64 w-full relative">
                    <canvas id="c6Chart"></canvas>
                </div>
            </div>
        </div>
    </div>

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
<?php /**PATH C:\laragon\www\Horizon-UniStock - Copy\resources\views/report/sales-dashboard.blade.php ENDPATH**/ ?>