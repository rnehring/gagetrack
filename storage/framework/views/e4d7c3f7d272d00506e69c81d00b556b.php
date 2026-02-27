<?php $__env->startSection('title', 'Configurations'); ?>
<?php $__env->startSection('content'); ?>

<h1 class="text-2xl font-bold text-gray-800 mb-4">Configurations</h1>

<div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="table-th w-8"></th>
                <th class="table-th">Name</th>
                <th class="table-th">Value</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php $__empty_1 = true; $__currentLoopData = $configurations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $config): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50">
                    <td class="table-td"><a href="<?php echo e(route('configurations.edit', $config->id)); ?>" class="text-brand-600">✏️</a></td>
                    <td class="table-td font-medium"><?php echo e($config->name); ?></td>
                    <td class="table-td text-gray-600"><?php echo e($config->value); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="3" class="table-td text-center text-gray-400 py-8">No configurations.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\nehri\Herd\gagetrack_new\resources\views/configurations/index.blade.php ENDPATH**/ ?>