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
     <?php $__env->slot('header', null, []); ?> 
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight"><?php echo e(__('Detail Entitlement')); ?></h2>
            <a href="<?php echo e(route('distribution.entitlement.index')); ?>" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150"><?php echo e(__('← Back')); ?></a>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="mb-6">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <dt class="text-sm font-medium text-gray-500"><?php echo e(__('Entitlement Code')); ?></dt>
                                <dd class="mt-1 text-sm text-gray-900 font-semibold font-mono"><?php echo e($entitlement->code); ?></dd>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <dt class="text-sm font-medium text-gray-500"><?php echo e(__('Student Level')); ?></dt>
                                <dd class="mt-1 text-sm text-gray-900"><?php echo e($entitlement->student_level_label); ?></dd>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <dt class="text-sm font-medium text-gray-500"><?php echo e(__('Status')); ?></dt>
                                <dd class="mt-1"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo e($entitlement->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'); ?>"><?php echo e($entitlement->is_active ? 'Active' : 'Inactive'); ?></span></dd>
                            </div>
                            <?php if($entitlement->description): ?>
                            <div class="bg-gray-50 p-4 rounded-lg md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500"><?php echo e(__('Description')); ?></dt>
                                <dd class="mt-1 text-sm text-gray-900"><?php echo e($entitlement->description); ?></dd>
                            </div>
                            <?php endif; ?>
                        </dl>
                    </div>

                    <div class="mb-6 flex items-center gap-3">
                        <a href="<?php echo e(route('distribution.entitlement.edit', $entitlement)); ?>" class="inline-flex items-center px-4 py-2 bg-amber-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-600 focus:bg-amber-600 active:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition ease-in-out duration-150"><?php echo e(__('Edit')); ?></a>
                        <?php if (isset($component)) { $__componentOriginalb7eac87efb73c0c2c26fe03ec80faafd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb7eac87efb73c0c2c26fe03ec80faafd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.delete-modal','data' => ['route' => route('distribution.entitlement.destroy', $entitlement),'label' => 'Delete Entitlement','description' => 'Are you sure you want to delete entitlement '.e($entitlement->code).'? This data cannot be restored.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('delete-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('distribution.entitlement.destroy', $entitlement)),'label' => 'Delete Entitlement','description' => 'Are you sure you want to delete entitlement '.e($entitlement->code).'? This data cannot be restored.']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb7eac87efb73c0c2c26fe03ec80faafd)): ?>
<?php $attributes = $__attributesOriginalb7eac87efb73c0c2c26fe03ec80faafd; ?>
<?php unset($__attributesOriginalb7eac87efb73c0c2c26fe03ec80faafd); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb7eac87efb73c0c2c26fe03ec80faafd)): ?>
<?php $component = $__componentOriginalb7eac87efb73c0c2c26fe03ec80faafd; ?>
<?php unset($__componentOriginalb7eac87efb73c0c2c26fe03ec80faafd); ?>
<?php endif; ?>
                    </div>

                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4"><?php echo e(__('Item List')); ?></h3>
                        <?php if($entitlement->items->count()): ?>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item Name</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Code</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Available Sizes</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <?php $__currentLoopData = $entitlement->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ei): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $item = $ei->item;
                                                $baseCode = $item->base_code ?? $item->code;
                                                $availableSizes = App\Models\Item::where('base_code', $baseCode)
                                                    ->with('variants')
                                                    ->get()
                                                    ->pluck('variants.0.size_label')
                                                    ->filter()
                                                    ->implode(', ');
                                            ?>
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e($loop->iteration); ?></td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e($item?->name ?? '-'); ?></td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-500"><?php echo e($baseCode); ?></td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e($availableSizes ?: '-'); ?></td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold"><?php echo e($ei->quantity); ?></td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-sm text-gray-500"><?php echo e(__('No items yet.')); ?></p>
                        <?php endif; ?>
                    </div>

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
<?php /**PATH C:\laragon\www\Horizon-UniStock - Copy\resources\views/distribution/entitlement/show.blade.php ENDPATH**/ ?>