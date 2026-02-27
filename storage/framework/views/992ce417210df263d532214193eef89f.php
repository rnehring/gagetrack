<?php $__env->startSection('title', 'Metadata'); ?>
<?php $__env->startSection('content'); ?>

<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold text-gray-800">Metadata</h1>
    <a href="<?php echo e(route('metadata.create')); ?>" class="btn-primary">+ Add</a>
</div>


<div class="bg-white rounded-xl shadow border border-gray-100 p-4 mb-4">
    <form method="POST" action="<?php echo e(route('metadata.search')); ?>" class="flex flex-wrap gap-3 items-end">
        <?php echo csrf_field(); ?>
        <div>
            <label class="form-label">Category</label>
            <select name="search_category" class="form-select">
                <option value="">All Categories</option>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($cat); ?>" <?php if($filters['search_category'] == $cat): echo 'selected'; endif; ?>><?php echo e($cat); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <button type="submit" class="btn-primary">Filter</button>
        <button type="submit" name="reset" value="1" class="btn-secondary">Reset</button>
    </form>
</div>

<div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="table-th w-8"></th>
                <th class="table-th">Category</th>
                <th class="table-th">Value</th>
                <th class="table-th">Description</th>
                <th class="table-th w-8"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50">
                    <td class="table-td"><a href="<?php echo e(route('metadata.edit', $record->id)); ?>" class="text-brand-600">✏️</a></td>
                    <td class="table-td font-medium text-gray-500"><?php echo e($record->category); ?></td>
                    <td class="table-td"><?php echo e($record->value); ?></td>
                    <td class="table-td text-gray-400 text-xs"><?php echo e($record->description); ?></td>
                    <td class="table-td">
                        <form method="POST" action="<?php echo e(route('metadata.destroy', $record->id)); ?>" class="inline">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="text-red-400 hover:text-red-600 text-xs"
                                onclick="return confirm('Delete this metadata?')">✕</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="5" class="table-td text-center text-gray-400 py-8">No metadata found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php if($records->hasPages()): ?>
        <div class="px-4 py-3 border-t border-gray-100"><?php echo e($records->links()); ?></div>
    <?php endif; ?>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\nehri\Herd\gagetrack_new\resources\views/metadata/index.blade.php ENDPATH**/ ?>