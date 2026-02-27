<?php $__env->startSection('title', $gage ? 'Edit Gage: ' . $gage->gageNumber : 'Add Gage'); ?>
<?php $__env->startSection('content'); ?>

<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold text-gray-800"><?php echo e($gage ? 'Edit Gage: ' . $gage->gageNumber : 'Add Gage'); ?></h1>
    <a href="<?php echo e(route('gages.index')); ?>" class="btn-secondary">← Back to Gages</a>
</div>

<form method="POST" action="<?php echo e($gage ? route('gages.update', $gage->id) : route('gages.store')); ?>" class="space-y-4">
    <?php echo csrf_field(); ?>
    <?php if($gage): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        
        <div class="bg-white rounded-xl shadow border border-gray-100 p-5 space-y-4">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider border-b pb-2">Identification</h2>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="form-label">Gage Number <span class="text-red-500">*</span></label>
                    <input type="text" name="gageNumber" value="<?php echo e(old('gageNumber', $gageNumber)); ?>" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Status</label>
                    <select name="statusId" class="form-select">
                        <option value="">-- Select --</option>
                        <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($s->id); ?>" <?php if(old('statusId', $gage?->statusId) == $s->id): echo 'selected'; endif; ?>><?php echo e($s->value); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>

            <div>
                <label class="form-label">Description</label>
                <input type="text" name="description" value="<?php echo e(old('description', $gage?->description)); ?>" class="form-input">
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="form-label">Serial Number</label>
                    <input type="text" name="serialNumber" value="<?php echo e(old('serialNumber', $gage?->serialNumber)); ?>" class="form-input">
                </div>
                <div>
                    <label class="form-label">Model Number</label>
                    <input type="text" name="modelNumber" value="<?php echo e(old('modelNumber', $gage?->modelNumber)); ?>" class="form-input">
                </div>
                <div>
                    <label class="form-label">NIST Number</label>
                    <input type="text" name="nistNumber" value="<?php echo e(old('nistNumber', $gage?->nistNumber)); ?>" class="form-input">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="form-label">Type</label>
                    <select name="typeId" class="form-select">
                        <option value="">-- Select --</option>
                        <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($t->id); ?>" <?php if(old('typeId', $gage?->typeId) == $t->id): echo 'selected'; endif; ?>><?php echo e($t->value); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="form-label">Unit of Measure</label>
                    <select name="unitMeasureId" class="form-select">
                        <option value="">-- Select --</option>
                        <?php $__currentLoopData = $unitMeasures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($u->id); ?>" <?php if(old('unitMeasureId', $gage?->unitMeasureId) == $u->id): echo 'selected'; endif; ?>><?php echo e($u->value); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="form-label">Manufacturer</label>
                    <select name="manufacturerId" class="form-select">
                        <option value="">-- Select --</option>
                        <?php $__currentLoopData = $manufacturers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($m->id); ?>" <?php if(old('manufacturerId', $gage?->manufacturerId) == $m->id): echo 'selected'; endif; ?>><?php echo e($m->value); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="form-label">Owner</label>
                    <select name="ownerId" class="form-select">
                        <option value="">-- Select --</option>
                        <?php $__currentLoopData = $owners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($o->id); ?>" <?php if(old('ownerId', $gage?->ownerId) == $o->id): echo 'selected'; endif; ?>><?php echo e($o->value); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>

            <div>
                <label class="form-label">Supplier</label>
                <select name="supplierId" class="form-select">
                    <option value="">-- Select --</option>
                    <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($sup->id); ?>" <?php if(old('supplierId', $gage?->supplierId) == $sup->id): echo 'selected'; endif; ?>><?php echo e($sup->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div>
                <label class="form-label">Location</label>
                <select name="locationId" class="form-select">
                    <option value="">-- Select --</option>
                    <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($loc->id); ?>" <?php if(old('locationId', $gage?->locationId) == $loc->id): echo 'selected'; endif; ?>><?php echo e($loc->value); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>

        
        <div class="space-y-4">
            <div class="bg-white rounded-xl shadow border border-gray-100 p-5 space-y-4">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider border-b pb-2">Calibration Schedule</h2>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="form-label">Frequency</label>
                        <input type="number" name="frequency" value="<?php echo e(old('frequency', $gage?->frequency)); ?>" class="form-input" min="1">
                    </div>
                    <div>
                        <label class="form-label">Frequency Unit</label>
                        <select name="frequencyUnitId" class="form-select">
                            <option value="">-- Select --</option>
                            <?php $__currentLoopData = $frequencyUnits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($fu->id); ?>" <?php if(old('frequencyUnitId', $gage?->frequencyUnitId) == $fu->id): echo 'selected'; endif; ?>><?php echo e($fu->value); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="form-label">Next Due Date</label>
                    <input type="date" name="dateDue" value="<?php echo e(old('dateDue', $gage?->dateDue?->format('Y-m-d'))); ?>" class="form-input">
                </div>
            </div>

            <div class="bg-white rounded-xl shadow border border-gray-100 p-5 space-y-4">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider border-b pb-2">Notes</h2>
                <textarea name="notes" rows="5" class="form-textarea"><?php echo e(old('notes', $gage?->notes)); ?></textarea>
            </div>

            <?php if($gage): ?>
            <div class="bg-white rounded-xl shadow border border-gray-100 p-5 space-y-3">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider border-b pb-2">Actions</h2>
                <div class="flex flex-wrap gap-2">
                    <a href="<?php echo e(route('calibrations.create', ['gageId' => $gage->id])); ?>" class="btn-success text-xs">+ Calibrate</a>
                    <a href="<?php echo e(route('calibrations.index', ['gageId' => $gage->id])); ?>" class="btn-secondary text-xs">View Calibrations</a>
                    <?php if(isset($latestCalibration)): ?>
                        <a href="<?php echo e(route('reports.certifications', $gage->id)); ?>" class="btn-secondary text-xs">Certificate</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="flex gap-3 items-center">
        <button type="submit" class="btn-primary">Save Gage</button>
        <a href="<?php echo e(route('gages.index')); ?>" class="btn-secondary">Cancel</a>
        <?php if($gage): ?>
            <form method="POST" action="<?php echo e(route('gages.destroy', $gage->id)); ?>" class="inline ml-auto">
                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                <button type="submit" class="btn-danger text-xs"
                    onclick="return confirm('Delete this gage? This cannot be undone.')">Delete Gage</button>
            </form>
        <?php endif; ?>
    </div>
</form>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\nehri\Herd\gagetrack_new\resources\views/gages/form.blade.php ENDPATH**/ ?>