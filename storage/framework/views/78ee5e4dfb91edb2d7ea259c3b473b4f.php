<?php $__env->startSection('title', 'Reports'); ?>
<?php $__env->startSection('content'); ?>

<h1 class="text-2xl font-bold text-gray-800 mb-6">Reports</h1>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 max-w-2xl">
    <a href="<?php echo e(route('reports.gages')); ?>" class="block bg-white rounded-xl shadow border border-gray-100 p-6 hover:border-brand-400 hover:shadow-md transition-all group">
        <div class="text-3xl mb-2">📋</div>
        <div class="font-semibold text-gray-800 group-hover:text-brand-700">Gage Report</div>
        <div class="text-xs text-gray-400 mt-1">Filter and export gage data</div>
    </a>
    <a href="<?php echo e(route('reports.backlog')); ?>" class="block bg-white rounded-xl shadow border border-gray-100 p-6 hover:border-brand-400 hover:shadow-md transition-all group">
        <div class="text-3xl mb-2">⚠️</div>
        <div class="font-semibold text-gray-800 group-hover:text-brand-700">Backlog</div>
        <div class="text-xs text-gray-400 mt-1">Overdue calibrations</div>
    </a>
    <a href="<?php echo e(route('reports.certifications')); ?>" class="block bg-white rounded-xl shadow border border-gray-100 p-6 hover:border-brand-400 hover:shadow-md transition-all group">
        <div class="text-3xl mb-2">📄</div>
        <div class="font-semibold text-gray-800 group-hover:text-brand-700">Certification</div>
        <div class="text-xs text-gray-400 mt-1">Print calibration certificates</div>
    </a>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\nehri\Herd\gagetrack_new\resources\views/reports/index.blade.php ENDPATH**/ ?>