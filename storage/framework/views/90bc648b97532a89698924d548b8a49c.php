<?php $__env->startSection('title', 'Backlog Report'); ?>
<?php $__env->startSection('content'); ?>

<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold text-gray-800">Overdue Calibrations (Backlog)</h1>
    <div class="flex gap-2">
        <button onclick="window.print()" class="btn-secondary">🖨️ Print</button>
        <a href="<?php echo e(route('reports.index')); ?>" class="btn-secondary">← Reports</a>
    </div>
</div>

<div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="table-th">Gage #</th>
                <th class="table-th">Serial #</th>
                <th class="table-th">Description</th>
                <th class="table-th">Location</th>
                <th class="table-th">Due Date</th>
                <th class="table-th">Days Overdue</th>
                <th class="table-th">Frequency</th>
                <th class="table-th">Status</th>
                <th class="table-th print:hidden">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php $__empty_1 = true; $__currentLoopData = $gages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $daysOverdue = $gage->dateDue ? now()->diffInDays($gage->dateDue, false) * -1 : null;
                ?>
                <tr class="hover:bg-red-50 bg-red-50/50">
                    <td class="table-td font-medium text-red-700"><?php echo e($gage->gageNumber); ?></td>
                    <td class="table-td text-gray-500"><?php echo e($gage->serialNumber); ?></td>
                    <td class="table-td text-gray-700"><?php echo e($gage->description); ?></td>
                    <td class="table-td text-gray-500"><?php echo e($gage->location?->value); ?></td>
                    <td class="table-td text-red-600 font-semibold">
                        <?php if($gage->dateDue && $gage->dateDue->year >= 2000): ?>
                            <?php echo e($gage->dateDue->format('Y-m-d')); ?>

                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                    <td class="table-td text-red-600 font-semibold">
                        <?php if($daysOverdue !== null && $daysOverdue > 0): ?>
                            <?php echo e($daysOverdue); ?> days
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                    <td class="table-td text-gray-500"><?php echo e($gage->frequencyDisplay); ?></td>
                    <td class="table-td text-gray-500"><?php echo e($gage->status?->value); ?></td>
                    <td class="table-td print:hidden">
                        <a href="<?php echo e(route('gages.edit', $gage->id)); ?>" class="text-brand-700 hover:underline text-xs">Edit Gage</a>
                        &nbsp;|&nbsp;
                        <a href="<?php echo e(route('calibrations.create')); ?>?gageId=<?php echo e($gage->id); ?>" class="text-green-700 hover:underline text-xs">Calibrate</a>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="9" class="table-td text-center text-gray-400 py-10">
                        ✅ No overdue calibrations found.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <div class="px-4 py-2 text-xs text-gray-400 border-t border-gray-100">
        <?php echo e($gages->count()); ?> overdue gage(s) as of <?php echo e(now()->format('Y-m-d')); ?>

    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\nehri\Herd\gagetrack_new\resources\views/reports/backlog.blade.php ENDPATH**/ ?>