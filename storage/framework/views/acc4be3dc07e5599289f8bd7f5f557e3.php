<?php $__env->startSection('title', 'Users'); ?>
<?php $__env->startSection('content'); ?>

<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold text-gray-800">Users</h1>
    <a href="<?php echo e(route('users.create')); ?>" class="btn-primary">+ Add User</a>
</div>

<div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="table-th w-8"></th>
                <th class="table-th">Name</th>
                <th class="table-th">Username</th>
                <th class="table-th">Email</th>
                <th class="table-th">Active</th>
                <th class="table-th w-8"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50">
                    <td class="table-td"><a href="<?php echo e(route('users.edit', $user->id)); ?>" class="text-brand-600">✏️</a></td>
                    <td class="table-td font-medium"><?php echo e($user->nameLast); ?>, <?php echo e($user->nameFirst); ?></td>
                    <td class="table-td text-gray-500"><?php echo e($user->username); ?></td>
                    <td class="table-td text-gray-500"><?php echo e($user->emailAddress); ?></td>
                    <td class="table-td">
                        <?php if($user->isActive): ?>
                            <span class="text-green-600">✓</span>
                        <?php else: ?>
                            <span class="text-gray-300">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="table-td">
                        <form method="POST" action="<?php echo e(route('users.destroy', $user->id)); ?>" class="inline">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="text-red-400 hover:text-red-600 text-xs"
                                onclick="return confirm('Delete this user?')">✕</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="6" class="table-td text-center text-gray-400 py-8">No users found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\nehri\Herd\gagetrack_new\resources\views/users/index.blade.php ENDPATH**/ ?>