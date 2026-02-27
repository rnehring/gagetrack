<?php $__env->startSection('title', 'Procedures'); ?>
<?php $__env->startSection('content'); ?>

<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold text-gray-800">Procedures</h1>
    <a href="<?php echo e(route('procedures.create')); ?>" class="btn-primary">+ Add Procedure</a>
</div>

<div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="table-th w-8"></th>
                <th class="table-th">Name</th>
                <th class="table-th">Description</th>
                <th class="table-th w-8"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php $__empty_1 = true; $__currentLoopData = $procedures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $procedure): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50">
                    <td class="table-td"><a href="<?php echo e(route('procedures.edit', $procedure->id)); ?>" class="text-brand-600">✏️</a></td>
                    <td class="table-td font-medium"><?php echo e($procedure->name); ?></td>
                    <td class="table-td text-gray-500 text-xs"><?php echo e(Str::limit($procedure->description, 80)); ?></td>
                    <td class="table-td">
                        <form method="POST" action="<?php echo e(route('procedures.destroy', $procedure->id)); ?>" class="inline">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="text-red-400 hover:text-red-600 text-xs"
                                onclick="return confirm('Delete this procedure?')">✕</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="4" class="table-td text-center text-gray-400 py-8">No procedures found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\nehri\Herd\gagetrack_new\resources\views/procedures/index.blade.php ENDPATH**/ ?>