<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Gage Tracker</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="h-full flex items-center justify-center">
<div class="w-full max-w-sm">
    <div class="text-center mb-6">
        <h1 class="text-3xl font-bold text-brand-800">🔧 Gage Tracker</h1>
    </div>
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-6">Sign in</h2>

        <?php if($errors->any()): ?>
            <div class="bg-red-50 border border-red-300 text-red-700 rounded-lg px-4 py-3 text-sm mb-4">
                <?php echo e($errors->first()); ?>

            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('login.post')); ?>" class="space-y-4">
            <?php echo csrf_field(); ?>
            <div>
                <label class="form-label">Username</label>
                <input type="text" name="username" value="<?php echo e(old('username')); ?>" class="form-input" required autofocus>
            </div>
            <div>
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-input" required>
            </div>
            <button type="submit" class="btn-primary w-full justify-center">Sign In</button>
        </form>
    </div>
</div>
</body>
</html>
<?php /**PATH C:\Users\nehri\Herd\gagetrack_new\resources\views/auth/login.blade.php ENDPATH**/ ?>