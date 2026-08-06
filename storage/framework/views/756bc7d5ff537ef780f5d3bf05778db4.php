<?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['value' => __('Select Items & Entitlement Quantity')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Select Items & Entitlement Quantity'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $attributes = $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $component = $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
<p class="mt-1 mb-4 text-xs text-gray-500">Select items that students are entitled to and adjust the quantity.</p>

<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $existingItems = $entitlement?->items ?? collect(old('items', []));
            $oldItem = $existingItems->first(fn($i) => 
                ($i instanceof \App\Models\EntitlementItem) 
                    ? ($i->item?->base_code ?? '') === $item->code
                    : ($i['base_code'] ?? '') === $item->code
            );
            $isChecked = !empty($oldItem);
            $qty = $isChecked ? ($oldItem['quantity'] ?? $oldItem->quantity ?? 1) : 1;
        ?>
        <div class="flex items-center justify-between p-3 border rounded-lg bg-gray-50 hover:bg-gray-100 transition">
            <label class="flex items-center space-x-2 cursor-pointer flex-1 mr-2">
                <input type="checkbox" 
                       name="items[<?php echo e($idx); ?>][checked]" 
                       value="1" 
                       <?php echo e($isChecked ? 'checked' : ''); ?>

                       class="rounded border-gray-300 text-primary-700 shadow-sm focus:ring-primary-500">
                <span class="text-sm text-gray-700 font-semibold"><?php echo e($item->name); ?> (<?php echo e($item->code); ?>)</span>
            </label>
            
            <input type="hidden" name="items[<?php echo e($idx); ?>][item_id]" value="<?php echo e($item->id); ?>">
            
            <div class="flex items-center gap-1">
                <span class="text-xs text-gray-500">Qty:</span>
                <input type="number" 
                       name="items[<?php echo e($idx); ?>][quantity]" 
                       value="<?php echo e($qty); ?>" 
                       min="1" 
                       class="w-16 rounded-md border-gray-300 py-1 px-2 text-sm focus:border-primary-500 focus:ring-primary-500">
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div><?php /**PATH C:\laragon\www\Horizon-UniStock - Copy\resources\views/distribution/entitlement/_items-grid.blade.php ENDPATH**/ ?>