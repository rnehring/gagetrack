<?php $__env->startSection('title', 'Calibrations'); ?>
<?php $__env->startSection('content'); ?>

<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold text-gray-800">Calibrations</h1>
    <a href="<?php echo e(route('calibrations.create')); ?>" class="btn-primary">+ Add Calibration</a>
</div>


<div class="bg-white rounded-xl shadow border border-gray-100 p-4 mb-4">
    <form method="POST" action="<?php echo e(route('calibrations.search')); ?>" class="grid grid-cols-2 sm:grid-cols-4 gap-3 items-end">
        <?php echo csrf_field(); ?>
        <div>
            <label class="form-label">Gage</label>
            <select name="search_gageId" class="form-select">
                <option value="">All Gages</option>
                <?php $__currentLoopData = $gages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($g->id); ?>" <?php if($filters['search_gageId'] == $g->id): echo 'selected'; endif; ?>><?php echo e($g->gageNumber); ?><?php echo e($g->description ? ' — ' . $g->description : ''); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="form-label">Date From</label>
            <input type="date" name="search_dateCalibrated_start" value="<?php echo e($filters['search_dateCalibrated_start']); ?>" class="form-input">
        </div>
        <div>
            <label class="form-label">Date To</label>
            <input type="date" name="search_dateCalibrated_stop" value="<?php echo e($filters['search_dateCalibrated_stop']); ?>" class="form-input">
        </div>
        <div class="flex gap-2">
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
                <th class="table-th">Date Calibrated</th>
                <th class="table-th">Calibrated By</th>
                <th class="table-th">Status</th>
                <th class="table-th">Passed</th>
                <th class="table-th">Certificate</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php $__empty_1 = true; $__currentLoopData = $calibrations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50">
                    <td class="table-td">
                        <a href="<?php echo e(route('calibrations.edit', $cal->id)); ?>" class="text-brand-600 hover:text-brand-800">✏️</a>
                    </td>
                    <td class="table-td font-medium">
                        <a href="<?php echo e(route('gages.edit', $cal->gageId)); ?>" class="text-brand-700 hover:underline"><?php echo e($cal->gage?->gageNumber); ?></a>
                    </td>
                    <td class="table-td"><?php echo e($cal->dateCalibrated?->format('Y-m-d')); ?></td>
                    <td class="table-td text-gray-500"><?php echo e($cal->calibrationBy?->value); ?></td>
                    <td class="table-td text-gray-500"><?php echo e($cal->calibrationStatus?->value); ?></td>
                    <td class="table-td">
                        <?php if($cal->isPassed): ?>
                            <span class="text-green-600 font-semibold">✓ Pass</span>
                        <?php else: ?>
                            <span class="text-red-500">✗ Fail</span>
                        <?php endif; ?>
                    </td>
                    <td class="table-td">
                        <?php if($cal->certificateFilename): ?>
                            <a href="<?php echo e(route('certificate.download', $cal->certificateFilename)); ?>" class="text-brand-600 hover:underline text-xs" target="_blank">📄 View</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="7" class="table-td text-center text-gray-400 py-8">No calibrations found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php if($calibrations->hasPages()): ?>
        <div class="px-4 py-3 border-t border-gray-100"><?php echo e($calibrations->links()); ?></div>
    <?php endif; ?>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\nehri\Herd\gagetrack_new\resources\views/calibrations/index.blade.php ENDPATH**/ ?>