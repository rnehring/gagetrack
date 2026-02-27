<?php $__env->startSection('title', 'Calibration Calendar ' . $year); ?>
<?php $__env->startSection('content'); ?>

<?php $today = now(); $currentYear = (int) $today->format('Y'); $currentMonth = (int) $today->format('n'); ?>

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Calibration Calendar</h1>
    <div class="flex items-center gap-3">
        <?php if($overdueCount > 0): ?>
            <a href="<?php echo e(route('reports.backlog')); ?>" class="inline-flex items-center gap-1 bg-red-100 text-red-700 text-sm font-medium px-3 py-1 rounded-full hover:bg-red-200 transition-colors">
                ⚠️ <?php echo e($overdueCount); ?> Overdue
            </a>
        <?php endif; ?>
        <div class="flex items-center gap-2">
            <a href="<?php echo e(route('calendar.index', $year - 1)); ?>" class="btn-secondary px-3">← <?php echo e($year - 1); ?></a>
            <span class="text-lg font-semibold text-gray-700 px-2"><?php echo e($year); ?></span>
            <a href="<?php echo e(route('calendar.index', $year + 1)); ?>" class="btn-secondary px-3"><?php echo e($year + 1); ?> →</a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
    <?php for($m = 1; $m <= 12; $m++): ?>
        <?php
            $monthGages  = $calendar[$m] ?? [];
            $total       = count($monthGages);
            $limit       = 10;
            $hasMore     = $total > $limit;
            $isThisMonth = ($year === $currentYear && $m === $currentMonth);
            $monthId     = 'month-' . $m;
        ?>
        <div class="bg-white rounded-xl shadow border <?php echo e($isThisMonth ? 'border-brand-400 ring-1 ring-brand-300' : 'border-gray-100'); ?> overflow-hidden flex flex-col">

            
            <div class="px-4 py-2 flex items-center justify-between <?php echo e($isThisMonth ? 'bg-brand-800' : 'bg-brand-700'); ?> text-white shrink-0">
                <span class="font-semibold text-sm">
                    <?php echo e(date('F', mktime(0,0,0,$m,1,$year))); ?>

                    <?php if($isThisMonth): ?><span class="text-xs font-normal opacity-75 ml-1">← now</span><?php endif; ?>
                </span>
                <span class="text-xs bg-brand-600 rounded-full px-2 py-0.5"><?php echo e($total); ?></span>
            </div>

            
            <div class="p-3 flex flex-col gap-0.5">
                <?php $__empty_1 = true; $__currentLoopData = $monthGages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $gage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php $isOverdue = $gage->dateDue && $gage->dateDue->lt($today); ?>
                    <a href="<?php echo e(route('gages.edit', $gage->id)); ?>"
                       data-month="<?php echo e($monthId); ?>"
                       class="gage-row flex items-center gap-2 text-sm rounded px-1 py-0.5 group hover:bg-blue-50 <?php echo e($i >= $limit ? 'extra hidden' : ''); ?>">
                        <span class="shrink-0 w-1.5 h-1.5 rounded-full mt-px <?php echo e($isOverdue ? 'bg-red-500' : 'bg-green-400'); ?>"></span>
                        <span class="font-medium <?php echo e($isOverdue ? 'text-red-700' : 'text-brand-700'); ?> group-hover:underline truncate"><?php echo e($gage->gageNumber); ?></span>
                        <?php if($gage->description): ?>
                            <span class="text-gray-400 truncate text-xs"><?php echo e(Str::limit($gage->description, 22)); ?></span>
                        <?php endif; ?>
                        <?php if($isOverdue): ?>
                            <span class="ml-auto text-xs text-red-400 shrink-0"><?php echo e($gage->dateDue->format('m/y')); ?></span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <span class="text-gray-300 text-sm italic px-1">No gages due</span>
                <?php endif; ?>

                
                <?php if($hasMore): ?>
                    <button
                        type="button"
                        onclick="toggleMonth('<?php echo e($monthId); ?>', this)"
                        class="mt-1 text-xs text-brand-600 hover:text-brand-800 hover:underline text-left px-1 py-0.5 font-medium">
                        + <?php echo e($total - $limit); ?> more…
                    </button>
                <?php endif; ?>
            </div>

        </div>
    <?php endfor; ?>
</div>

<script>
function toggleMonth(monthId, btn) {
    const rows = document.querySelectorAll(`a.extra[data-month="${monthId}"]`);
    const isHidden = rows[0]?.classList.contains('hidden');

    rows.forEach(r => r.classList.toggle('hidden', !isHidden));

    const hiddenCount = btn.dataset.moreCount ?? btn.textContent.match(/\d+/)?.[0];
    if (!btn.dataset.moreCount) btn.dataset.moreCount = hiddenCount;

    btn.textContent = isHidden
        ? '− Show less'
        : `+ ${btn.dataset.moreCount} more…`;
}
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\nehri\Herd\gagetrack_new\resources\views/calendar/index.blade.php ENDPATH**/ ?>