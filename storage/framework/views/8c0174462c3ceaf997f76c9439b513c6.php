<?php $__env->startSection('title', 'Suppliers'); ?>
<?php $__env->startSection('content'); ?>

<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold text-gray-800">Suppliers</h1>
    <a href="<?php echo e(route('suppliers.create')); ?>" class="btn-primary">+ Add Supplier</a>
</div>

<div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="table-th w-8"></th>
                <th class="table-th">Name</th>
                <th class="table-th">Code</th>
                <th class="table-th">Contact</th>
                <th class="table-th">Email</th>
                <th class="table-th">Phone</th>
                <th class="table-th">Active</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php $__empty_1 = true; $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50">
                    <td class="table-td"><a href="<?php echo e(route('suppliers.edit', $supplier->id)); ?>" class="text-brand-600 hover:text-brand-800">✏️</a></td>
                    <td class="table-td font-medium">
                        <a href="<?php echo e(route('suppliers.edit', $supplier->id)); ?>" class="text-brand-700 hover:underline"><?php echo e($supplier->name); ?></a>
                    </td>
                    <td class="table-td text-gray-500"><?php echo e($supplier->code); ?></td>
                    <td class="table-td text-gray-500"><?php echo e($supplier->contact); ?></td>
                    <td class="table-td text-gray-500"><?php echo e($supplier->email); ?></td>
                    <td class="table-td text-gray-500"><?php echo e($supplier->phone); ?></td>
                    <td class="table-td">
                        <?php if($supplier->isActive): ?>
                            <span class="text-green-600">✓</span>
                        <?php else: ?>
                            <span class="text-gray-300">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="7" class="table-td text-center text-gray-400 py-8">No suppliers found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\nehri\Herd\gagetrack_new\resources\views/suppliers/index.blade.php ENDPATH**/ ?>