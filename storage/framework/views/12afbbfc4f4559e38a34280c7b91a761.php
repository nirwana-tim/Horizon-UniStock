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

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-800">Distribution Schedules</h3>
                        <a href="<?php echo e(route('distribution.distribution-schedule.create')); ?>" class="inline-flex items-center px-4 py-2 bg-primary-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-800 focus:bg-primary-800 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150"><?php echo e(__('+ Add Schedule')); ?></a>
                    </div>

                    <div x-data="serverTable('<?php echo e(route('distribution.distribution-schedule.index')); ?>')">

                        <div class="mb-4 space-y-3">
                            <div>
                                <input type="text"
                                       x-model="search"
                                       @input.debounce.300ms="page=1; fetchData()"
                                       placeholder="Search..."
                                       class="w-72 border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                            </div>
                            <div class="flex items-center gap-3">
                                <select x-model="period" @change="page=1; fetchData()"
                                    class="w-48 border-gray-300 rounded-md shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500">
                                    <option value="">All Periods</option>
                                    <?php $__currentLoopData = $periods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($p); ?>"><?php echo e($p); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <select x-model="facultyId" @change="page=1; fetchData()"
                                    class="w-56 border-gray-300 rounded-md shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500">
                                    <option value="">All Faculties</option>
                                    <?php $__currentLoopData = $faculties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($f->id); ?>"><?php echo e($f->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <select x-model="studyProgramId" @change="page=1; fetchData()"
                                    class="w-56 border-gray-300 rounded-md shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500">
                                    <option value="">All Study Programs</option>
                                    <?php $__currentLoopData = $studyPrograms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($sp->id); ?>"><?php echo e($sp->faculty->code); ?> - <?php echo e($sp->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Schedule Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Faculty / Study Program</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student Level</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Active</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody x-html="tableHtml" class="bg-white divide-y divide-gray-200">
                                    <?php echo $__env->make('distribution.distribution-schedule._table', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                </tbody>
                            </table>
                            <div x-html="paginationHtml" class="mt-4">
                                <?php $__env->startComponent('components.alpine-pagination', ['paginator' => $schedules]); ?><?php echo $__env->renderComponent(); ?>
                            </div>
                        </div>

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
<?php endif; ?>
<?php /**PATH C:\laragon\www\Horizon-UniStock - Copy\resources\views/distribution/distribution-schedule/index.blade.php ENDPATH**/ ?>