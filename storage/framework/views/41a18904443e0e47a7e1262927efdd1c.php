<?php $__env->startSection('title', $user ? 'Edit User' : 'Add User'); ?>
<?php $__env->startSection('content'); ?>

<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold text-gray-800"><?php echo e($user ? 'Edit User: ' . $user->username : 'Add User'); ?></h1>
    <a href="<?php echo e(route('users.index')); ?>" class="btn-secondary">← Back</a>
</div>

<div class="max-w-lg">
    <form method="POST" action="<?php echo e($user ? route('users.update', $user->id) : route('users.store')); ?>" class="bg-white rounded-xl shadow border border-gray-100 p-5 space-y-4">
        <?php echo csrf_field(); ?>
        <?php if($user): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="form-label">First Name <span class="text-red-500">*</span></label>
                <input type="text" name="nameFirst" value="<?php echo e(old('nameFirst', $user?->nameFirst)); ?>" class="form-input" required>
            </div>
            <div>
                <label class="form-label">Last Name <span class="text-red-500">*</span></label>
                <input type="text" name="nameLast" value="<?php echo e(old('nameLast', $user?->nameLast)); ?>" class="form-input" required>
            </div>
        </div>

        <div>
            <label class="form-label">Username <span class="text-red-500">*</span></label>
            <input type="text" name="username" value="<?php echo e(old('username', $user?->username)); ?>" class="form-input" required>
        </div>

        <div>
            <label class="form-label">Email</label>
            <input type="email" name="emailAddress" value="<?php echo e(old('emailAddress', $user?->emailAddress)); ?>" class="form-input">
        </div>

        <div>
            <label class="form-label"><?php echo e($user ? 'New Password (leave blank to keep current)' : 'Password'); ?> <?php echo e(!$user ? '*' : ''); ?></label>
            <input type="password" name="password" class="form-input" <?php echo e(!$user ? 'required' : ''); ?>>
        </div>

        <div class="flex gap-6">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="isActive" value="1" <?php if(old('isActive', $user?->isActive ?? true)): echo 'checked'; endif; ?> class="rounded">
                <span class="text-sm font-medium text-gray-700">Active</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="isHidden" value="1" <?php if(old('isHidden', $user?->isHidden)): echo 'checked'; endif; ?> class="rounded">
                <span class="text-sm font-medium text-gray-700">Hidden</span>
            </label>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="btn-primary">Save User</button>
            <a href="<?php echo e(route('users.index')); ?>" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\nehri\Herd\gagetrack_new\resources\views/users/form.blade.php ENDPATH**/ ?>