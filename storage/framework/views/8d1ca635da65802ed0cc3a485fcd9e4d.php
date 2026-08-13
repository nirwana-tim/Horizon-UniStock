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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Kelola Akun','subtitle' => 'Buat dan kelola akun admin (Finance) dan staff.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Kelola Akun','subtitle' => 'Buat dan kelola akun admin (Finance) dan staff.']); ?>
         <?php $__env->slot('actions', null, []); ?> 
            <a href="<?php echo e(route('admin.user.create')); ?>"
                class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary-700 text-white hover:bg-primary-800 rounded-lg text-sm font-medium focus:outline-none focus:ring-2 focus:ring-primary-300 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Akun
            </a>
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

    <div x-data="serverTable('<?php echo e(route('admin.user.index')); ?>')">

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

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center gap-3">
                <div class="flex items-center gap-2 text-xs font-medium text-gray-700">
                    <span>Filter:</span>
                    <a href="<?php echo e(route('admin.user.index')); ?>" class="px-3 py-1 rounded-full <?php echo e(!request('role') && !request('status') ? 'bg-primary-700 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'); ?>">Semua</a>
                    <a href="?role=admin" class="px-3 py-1 rounded-full <?php echo e(request('role') === 'admin' ? 'bg-primary-700 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'); ?>">Admin</a>
                    <a href="?role=staff" class="px-3 py-1 rounded-full <?php echo e(request('role') === 'staff' ? 'bg-primary-700 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'); ?>">Staff</a>
                    <a href="?status=inactive" class="px-3 py-1 rounded-full <?php echo e(request('status') === 'inactive' ? 'bg-primary-700 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'); ?>">Nonaktif</a>
                </div>
            </div>

            <div class="p-5">
                <div class="mb-4">
                    <input type="text"
                           x-model="search"
                           @input.debounce.300ms="page=1; fetchData()"
                           placeholder="Cari nama atau email..."
                           class="w-full sm:w-80 border-gray-300 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody x-html="tableHtml" class="bg-white divide-y divide-gray-200">
                            <?php echo $__env->make('system.users._table', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </tbody>
                    </table>
                    <div x-html="paginationHtml" class="mt-4">
                        <?php $__env->startComponent('components.alpine-pagination', ['paginator' => $users]); ?><?php echo $__env->renderComponent(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbf21375200b9142c0cd45acd38de22a2)): ?>
<?php $attributes = $__attributesOriginalbf21375200b9142c0cd45acd38de22a2; ?>
<?php unset($__attributesOriginalbf21375200b9142c0cd45acd38de22a2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbf21375200b9142c0cd45acd38de22a2)): ?>
<?php $component = $__componentOriginalbf21375200b9142c0cd45acd38de22a2; ?>
<?php unset($__componentOriginalbf21375200b9142c0cd45acd38de22a2); ?>
<?php endif; ?><?php /**PATH C:\laragon\www\Horizon-UniStock - Copy\resources\views/system/users/index.blade.php ENDPATH**/ ?>