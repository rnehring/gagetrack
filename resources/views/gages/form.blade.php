@extends('layouts.app')
@section('title', $gage ? 'Edit Gage: ' . $gage->gageNumber : 'Add Gage')
@section('content')

<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold text-gray-800">{{ $gage ? 'Edit Gage: ' . $gage->gageNumber : 'Add Gage' }}</h1>
    <a href="{{ route('gages.index') }}" class="btn-secondary">← Back to Gages</a>
</div>

<form method="POST" action="{{ $gage ? route('gages.update', $gage->id) : route('gages.store') }}" class="space-y-4">
    @csrf
    @if($gage) @method('PUT') @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        {{-- Left Column --}}
        <div class="bg-white rounded-xl shadow border border-gray-100 p-5 space-y-4">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider border-b pb-2">Identification</h2>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="form-label">Gage Number <span class="text-red-500">*</span></label>
                    <input type="text" name="gageNumber" value="{{ old('gageNumber', $gageNumber) }}" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Status</label>
                    <select name="statusId" class="form-select">
                        <option value="">-- Select --</option>
                        @foreach($statuses as $s)
                            <option value="{{ $s->id }}" @selected(old('statusId', $gage?->statusId) == $s->id)>{{ $s->value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="form-label">Description</label>
                <input type="text" name="description" value="{{ old('description', $gage?->description) }}" class="form-input">
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="form-label">Serial Number</label>
                    <input type="text" name="serialNumber" value="{{ old('serialNumber', $gage?->serialNumber) }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Model Number</label>
                    <input type="text" name="modelNumber" value="{{ old('modelNumber', $gage?->modelNumber) }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">NIST Number</label>
                    <input type="text" name="nistNumber" value="{{ old('nistNumber', $gage?->nistNumber) }}" class="form-input">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="form-label">Type</label>
                    <select name="typeId" class="form-select">
                        <option value="">-- Select --</option>
                        @foreach($types as $t)
                            <option value="{{ $t->id }}" @selected(old('typeId', $gage?->typeId) == $t->id)>{{ $t->value }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Unit of Measure</label>
                    <select name="unitMeasureId" class="form-select">
                        <option value="">-- Select --</option>
                        @foreach($unitMeasures as $u)
                            <option value="{{ $u->id }}" @selected(old('unitMeasureId', $gage?->unitMeasureId) == $u->id)>{{ $u->value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="form-label">Manufacturer</label>
                    <select name="manufacturerId" class="form-select">
                        <option value="">-- Select --</option>
                        @foreach($manufacturers as $m)
                            <option value="{{ $m->id }}" @selected(old('manufacturerId', $gage?->manufacturerId) == $m->id)>{{ $m->value }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Owner</label>
                    <select name="ownerId" class="form-select">
                        <option value="">-- Select --</option>
                        @foreach($owners as $o)
                            <option value="{{ $o->id }}" @selected(old('ownerId', $gage?->ownerId) == $o->id)>{{ $o->value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="form-label">Supplier</label>
                <select name="supplierId" class="form-select">
                    <option value="">-- Select --</option>
                    @foreach($suppliers as $sup)
                        <option value="{{ $sup->id }}" @selected(old('supplierId', $gage?->supplierId) == $sup->id)>{{ $sup->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">Location</label>
                <select name="locationId" class="form-select">
                    <option value="">-- Select --</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" @selected(old('locationId', $gage?->locationId) == $loc->id)>{{ $loc->value }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="space-y-4">
            <div class="bg-white rounded-xl shadow border border-gray-100 p-5 space-y-4">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider border-b pb-2">Calibration Schedule</h2>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="form-label">Frequency</label>
                        <input type="number" name="frequency" value="{{ old('frequency', $gage?->frequency) }}" class="form-input" min="1">
                    </div>
                    <div>
                        <label class="form-label">Frequency Unit</label>
                        <select name="frequencyUnitId" class="form-select">
                            <option value="">-- Select --</option>
                            @foreach($frequencyUnits as $fu)
                                <option value="{{ $fu->id }}" @selected(old('frequencyUnitId', $gage?->frequencyUnitId) == $fu->id)>{{ $fu->value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="form-label">Next Due Date</label>
                    <input type="date" name="dateDue" value="{{ old('dateDue', $gage?->dateDue?->format('Y-m-d')) }}" class="form-input">
                </div>
            </div>

            <div class="bg-white rounded-xl shadow border border-gray-100 p-5 space-y-4">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider border-b pb-2">Notes</h2>
                <textarea name="notes" rows="5" class="form-textarea">{{ old('notes', $gage?->notes) }}</textarea>
            </div>

            @if($gage)
            <div class="bg-white rounded-xl shadow border border-gray-100 p-5 space-y-3">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider border-b pb-2">Actions</h2>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('calibrations.create', ['gageId' => $gage->id]) }}" class="btn-success text-xs">+ Calibrate</a>
                    <a href="{{ route('calibrations.index', ['gageId' => $gage->id]) }}" class="btn-secondary text-xs">View Calibrations</a>
                    @if(isset($latestCalibration))
                        <a href="{{ route('reports.certifications', $gage->id) }}" class="btn-secondary text-xs">Certificate</a>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Buttons --}}
    <div class="flex gap-3 items-center">
        <button type="submit" class="btn-primary">Save Gage</button>
        <a href="{{ route('gages.index') }}" class="btn-secondary">Cancel</a>
        @if($gage)
            <form method="POST" action="{{ route('gages.destroy', $gage->id) }}" class="inline ml-auto">
                @csrf @method('DELETE')
                <button type="submit" class="btn-danger text-xs"
                    onclick="return confirm('Delete this gage? This cannot be undone.')">Delete Gage</button>
            </form>
        @endif
    </div>
</form>

@endsection
