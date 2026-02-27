<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Gage Tracker'); ?></title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="h-full flex flex-col">


<nav class="bg-brand-800 shadow-md">
    <div class="max-w-screen-2xl mx-auto px-4 flex items-center justify-between h-14">
        <a href="<?php echo e(route('calendar.index')); ?>" class="text-white font-bold text-lg tracking-wide hover:text-blue-200 transition-colors">
            🔧 Gage Tracker
        </a>
        <?php if(session('user')): ?>
        <div class="flex items-center gap-0.5 text-sm flex-wrap">
            <a href="<?php echo e(route('calendar.index')); ?>" class="text-blue-100 hover:text-white px-2 py-1 rounded transition-colors <?php echo e(request()->routeIs('calendar.*') ? 'bg-brand-600' : 'hover:bg-brand-700'); ?>">Calendar</a>
            <a href="<?php echo e(route('gages.index')); ?>" class="text-blue-100 hover:text-white px-2 py-1 rounded transition-colors <?php echo e(request()->routeIs('gages.*') ? 'bg-brand-600' : 'hover:bg-brand-700'); ?>">Gages</a>
            <a href="<?php echo e(route('calibrations.index')); ?>" class="text-blue-100 hover:text-white px-2 py-1 rounded transition-colors <?php echo e(request()->routeIs('calibrations.*') ? 'bg-brand-600' : 'hover:bg-brand-700'); ?>">Calibrations</a>
            <a href="<?php echo e(route('reports.index')); ?>" class="text-blue-100 hover:text-white px-2 py-1 rounded transition-colors <?php echo e(request()->routeIs('reports.*') ? 'bg-brand-600' : 'hover:bg-brand-700'); ?>">Reports</a>
            <a href="<?php echo e(route('suppliers.index')); ?>" class="text-blue-100 hover:text-white px-2 py-1 rounded transition-colors <?php echo e(request()->routeIs('suppliers.*') ? 'bg-brand-600' : 'hover:bg-brand-700'); ?>">Suppliers</a>
            <a href="<?php echo e(route('metadata.index')); ?>" class="text-blue-100 hover:text-white px-2 py-1 rounded transition-colors <?php echo e(request()->routeIs('metadata.*') ? 'bg-brand-600' : 'hover:bg-brand-700'); ?>">Metadata</a>
            <a href="<?php echo e(route('configurations.index')); ?>" class="text-blue-100 hover:text-white px-2 py-1 rounded transition-colors <?php echo e(request()->routeIs('configurations.*') ? 'bg-brand-600' : 'hover:bg-brand-700'); ?>">Config</a>
            <a href="<?php echo e(route('procedures.index')); ?>" class="text-blue-100 hover:text-white px-2 py-1 rounded transition-colors <?php echo e(request()->routeIs('procedures.*') ? 'bg-brand-600' : 'hover:bg-brand-700'); ?>">Procedures</a>
            <a href="<?php echo e(route('users.index')); ?>" class="text-blue-100 hover:text-white px-2 py-1 rounded transition-colors <?php echo e(request()->routeIs('users.*') ? 'bg-brand-600' : 'hover:bg-brand-700'); ?>">Users</a>
            <span class="text-blue-300 mx-2">|</span>
            <span class="text-blue-200 text-xs"><?php echo e(session('user.nameFirst')); ?></span>
            <a href="<?php echo e(route('change-password')); ?>" class="text-blue-100 hover:text-white px-2 py-1 rounded text-xs hover:bg-brand-700 transition-colors">Pwd</a>
            <form method="POST" action="<?php echo e(route('logout')); ?>" class="inline">
                <?php echo csrf_field(); ?>
                <button type="submit" class="text-blue-100 hover:text-white px-2 py-1 rounded text-xs hover:bg-brand-700 transition-colors">Logout</button>
            </form>
        </div>
        <?php endif; ?>
    </div>
</nav>


<div class="max-w-screen-2xl mx-auto w-full px-4 pt-4">
    <?php if(session('success')): ?>
        <div class="flex items-center gap-2 bg-green-50 border border-green-300 text-green-800 rounded-lg px-4 py-3 text-sm mb-2">
            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="flex items-center gap-2 bg-red-50 border border-red-300 text-red-800 rounded-lg px-4 py-3 text-sm mb-2">
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
        <div class="bg-red-50 border border-red-300 text-red-800 rounded-lg px-4 py-3 text-sm mb-2">
            <ul class="list-disc list-inside space-y-1">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>
</div>


<main class="flex-1 max-w-screen-2xl mx-auto w-full px-4 py-4">
    <?php echo $__env->yieldContent('content'); ?>
</main>

<footer class="text-center text-xs text-gray-400 py-3">&copy; <?php echo e(date('Y')); ?> Gage Tracker</footer>

</body>
</html>
<?php /**PATH C:\Users\nehri\Herd\gagetrack_new\resources\views/layouts/app.blade.php ENDPATH**/ ?>