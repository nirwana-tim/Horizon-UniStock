<?php if($paginator->hasPages()): ?>
    <nav class="flex items-center gap-1" role="navigation" aria-label="Pagination">
        <?php if($paginator->onFirstPage()): ?>
            <span class="px-3 py-1.5 text-sm text-gray-400 bg-gray-50 rounded-lg border border-gray-200 cursor-not-allowed">&laquo;</span>
        <?php else: ?>
            <button data-page="<?php echo e($paginator->currentPage() - 1); ?>" @click="goToPage(<?php echo e($paginator->currentPage() - 1); ?>)" class="px-3 py-1.5 text-sm text-gray-700 bg-white rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors">&laquo;</button>
        <?php endif; ?>

        <?php $__currentLoopData = $paginator->getUrlRange(1, $paginator->lastPage()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <button data-page="<?php echo e($page); ?>" @click="goToPage(<?php echo e($page); ?>)"
                class="px-3 py-1.5 text-sm rounded-lg border transition-colors
                <?php echo e($page == $paginator->currentPage()
                    ? 'bg-primary-700 text-white border-primary-700 font-medium'
                    : 'text-gray-700 bg-white border-gray-200 hover:bg-gray-50'); ?>">
                <?php echo e($page); ?>

            </button>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <?php if($paginator->hasMorePages()): ?>
            <button data-page="<?php echo e($paginator->currentPage() + 1); ?>" @click="goToPage(<?php echo e($paginator->currentPage() + 1); ?>)" class="px-3 py-1.5 text-sm text-gray-700 bg-white rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors">&raquo;</button>
        <?php else: ?>
            <span class="px-3 py-1.5 text-sm text-gray-400 bg-gray-50 rounded-lg border border-gray-200 cursor-not-allowed">&raquo;</span>
        <?php endif; ?>
    </nav>
<?php endif; ?>
<?php /**PATH C:\laragon\www\Horizon-UniStock - Copy\resources\views/components/alpine-pagination.blade.php ENDPATH**/ ?>