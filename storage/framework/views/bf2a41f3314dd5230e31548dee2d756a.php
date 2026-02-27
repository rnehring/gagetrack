<?php $__env->startSection('title', 'Gages'); ?>
<?php $__env->startSection('content'); ?>

<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold text-gray-800">Gages</h1>
    <a href="<?php echo e(route('gages.create')); ?>" class="btn-primary">+ Add Gage</a>
</div>


<div class="bg-white rounded-xl shadow border border-gray-100 p-4 mb-4">
    <form method="POST" action="<?php echo e(route('gages.search')); ?>" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 items-end">
        <?php echo csrf_field(); ?>
        <div>
            <label class="form-label">Gage Number</label>
            <input type="text" name="search_gageNumber" value="<?php echo e($filters['search_gageNumber']); ?>" class="form-input" placeholder="Search...">
        </div>
        <div>
            <label class="form-label">Description</label>
            <input type="text" name="search_description" value="<?php echo e($filters['search_description']); ?>" class="form-input" placeholder="Search...">
        </div>
        <div>
            <label class="form-label">Location</label>
            <select name="search_locationId" class="form-select">
                <option value="0">All Locations</option>
                <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($loc->id); ?>" <?php if($filters['search_locationId'] == $loc->id): echo 'selected'; endif; ?>><?php echo e($loc->value); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="form-label">Type</label>
            <select name="search_typeId" class="form-select">
                <option value="0">All Types</option>
                <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($type->id); ?>" <?php if($filters['search_typeId'] == $type->id): echo 'selected'; endif; ?>><?php echo e($type->value); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="form-label">Status</label>
            <select name="search_statusId" class="form-select">
                <option value="0">All Statuses</option>
                <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($status->id); ?>" <?php if($filters['search_statusId'] == $status->id): echo 'selected'; endif; ?>><?php echo e($status->value); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="flex gap-2 col-span-2 sm:col-span-3 lg:col-span-5">
            <button type="submit" class="btn-primary">Search</button>
            <button type="submit" name="reset" value="1" class="btn-secondary">Reset</button>
        </div>
    </form>
</div>


<div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="table-th w-8"></th>
                <th class="table-th">Gage #</th>
                <th class="table-th">Description</th>
                <th class="table-th">Location</th>
                <th class="table-th">Next Due</th>
                <th class="table-th">Status</th>
                <th class="table-th"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php $__empty_1 = true; $__currentLoopData = $gages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50 <?php echo e($gage->isOverdue ? 'bg-red-50' : ''); ?>">
                    <td class="table-td">
                        <a href="<?php echo e(route('gages.edit', $gage->id)); ?>" class="text-brand-600 hover:text-brand-800" title="Edit">✏️</a>
                    </td>
                    <td class="table-td font-medium">
                        <a href="<?php echo e(route('gages.edit', $gage->id)); ?>" class="text-brand-700 hover:underline"><?php echo e($gage->gageNumber); ?></a>
                    </td>
                    <td class="table-td text-gray-600"><?php echo e($gage->description); ?></td>
                    <td class="table-td text-gray-500"><?php echo e($gage->location?->value); ?></td>
                    <td class="table-td <?php echo e($gage->isOverdue ? 'text-red-600 font-semibold' : ''); ?>">
                        <?php if($gage->dateDue && $gage->dateDue->year >= 2000): ?>
                            <?php echo e($gage->dateDue->format('Y-m')); ?>

                            <?php if($gage->isOverdue): ?> ⚠️ <?php endif; ?>
                        <?php endif; ?>
                    </td>
                    <td class="table-td text-gray-500"><?php echo e($gage->status?->value); ?></td>
                    <td class="table-td">
                        <a href="<?php echo e(route('calibrations.index', ['gageId' => $gage->id])); ?>" class="btn-secondary text-xs px-2 py-1">Calibrations</a>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="7" class="table-td text-center text-gray-400 py-8">No gages found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php if($gages->hasPages()): ?>
        <div class="px-4 py-3 border-t border-gray-100"><?php echo e($gages->links()); ?></div>
    <?php endif; ?>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\nehri\Herd\gagetrack_new\resources\views/gages/index.blade.php ENDPATH**/ ?>