@extends('layouts.app')
@section('title', $calibration ? 'Edit Calibration' : 'Add Calibration')
@section('content')

<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold text-gray-800">{{ $calibration ? 'Edit Calibration' : 'Add Calibration' }}</h1>
    <a href="{{ route('calibrations.index') }}" class="btn-secondary">← Back</a>
</div>

<form method="POST"
    action="{{ $calibration ? route('calibrations.update', $calibration->id) : route('calibrations.store') }}"
    enctype="multipart/form-data" class="space-y-4">
    @csrf
    @if($calibration) @method('PUT') @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        {{-- Left --}}
        <div class="bg-white rounded-xl shadow border border-gray-100 p-5 space-y-4">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider border-b pb-2">Calibration Details</h2>

            <div>
                <label class="form-label">Gage <span class="text-red-500">*</span></label>
                <select name="gageId" class="form-select" required>
                    <option value="">-- Select Gage --</option>
                    @foreach($gages as $g)
                        <option value="{{ $g->id }}" @selected(old('gageId', $gage?->id ?? $calibration?->gageId) == $g->id)>
                            {{ $g->gageNumber }}{{ $g->description ? ' — ' . $g->description : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="form-label">Date Calibrated <span class="text-red-500">*</span></label>
                    <input type="date" name="dateCalibrated" value="{{ old('dateCalibrated', $calibration?->dateCalibrated?->format('Y-m-d') ?? date('Y-m-d')) }}" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Calibrated By</label>
                    <select name="calibrationById" class="form-select">
                        <option value="">-- Select --</option>
                        @foreach($calibrationBys as $cb)
                            <option value="{{ $cb->id }}" @selected(old('calibrationById', $calibration?->calibrationById) == $cb->id)>{{ $cb->value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="form-label">Calibration Type</label>
                    <select name="calibrationTypeId" class="form-select">
                        <option value="">-- Select --</option>
                        @foreach($calibrationTypes as $ct)
                            <option value="{{ $ct->id }}" @selected(old('calibrationTypeId', $calibration?->calibrationTypeId) == $ct->id)>{{ $ct->value }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Calibration Status</label>
                    <select name="calibrationStatusId" class="form-select">
                        <option value="">-- Select --</option>
                        @foreach($calibrationStatuses as $cs)
                            <option value="{{ $cs->id }}" @selected(old('calibrationStatusId', $calibration?->calibrationStatusId) == $cs->id)>{{ $cs->value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="form-label">As Found Condition</label>
                <select name="foundConditionId" class="form-select">
                    <option value="">-- Select --</option>
                    @foreach($foundConditions as $fc)
                        <option value="{{ $fc->id }}" @selected(old('foundConditionId', $calibration?->foundConditionId) == $fc->id)>{{ $fc->value }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">
                    <input type="checkbox" name="isPassed" value="1" @checked(old('isPassed', $calibration?->isPassed)) class="mr-1 rounded"> Passed
                </label>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="form-label">Results</label>
                    <input type="text" name="results" value="{{ old('results', $calibration?->results) }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Action Required</label>
                    <input type="text" name="actionRequired" value="{{ old('actionRequired', $calibration?->actionRequired) }}" class="form-input">
                </div>
            </div>

            <div>
                <label class="form-label">Findings</label>
                <textarea name="findings" rows="3" class="form-textarea">{{ old('findings', $calibration?->findings) }}</textarea>
            </div>
        </div>

        {{-- Right --}}
        <div class="space-y-4">
            <div class="bg-white rounded-xl shadow border border-gray-100 p-5 space-y-4">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider border-b pb-2">Environment & Frequency</h2>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="form-label">Temperature</label>
                        <input type="text" name="temperature" value="{{ old('temperature', $calibration?->temperature) }}" class="form-input" placeholder="e.g. 72°F">
                    </div>
                    <div>
                        <label class="form-label">Humidity</label>
                        <input type="text" name="humidity" value="{{ old('humidity', $calibration?->humidity) }}" class="form-input" placeholder="e.g. 45%">
                    </div>
                </div>

                <div>
                    <label class="form-label">Time Required</label>
                    <input type="text" name="timeRequired" value="{{ old('timeRequired', $calibration?->timeRequired) }}" class="form-input" placeholder="e.g. 1:30">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="form-label">Next Frequency</label>
                        <input type="number" name="frequency" value="{{ old('frequency', $calibration?->frequency ?? $gage?->frequency) }}" class="form-input" min="1">
                    </div>
                    <div>
                        <label class="form-label">Frequency Unit</label>
                        <select name="frequencyUnitId" class="form-select">
                            <option value="">-- Select --</option>
                            @foreach($frequencyUnits as $fu)
                                <option value="{{ $fu->id }}" @selected(old('frequencyUnitId', $calibration?->frequencyUnitId ?? $gage?->frequencyUnitId) == $fu->id)>{{ $fu->value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow border border-gray-100 p-5 space-y-4">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider border-b pb-2">Certificate</h2>

                <div>
                    <label class="form-label">Certificate Number</label>
                    <input type="text" name="certificateNumber" value="{{ old('certificateNumber', $calibration?->certificateNumber) }}" class="form-input">
                </div>

                <div>
                    <label class="form-label">Certificate File (PDF)</label>
                    <input type="file" name="certificateFile" accept=".pdf" class="form-input">
                    @if($calibration?->certificateFilename)
                        <p class="text-xs text-gray-500 mt-1">
                            Current: <a href="{{ route('certificate.download', $calibration->certificateFilename) }}" class="text-brand-600 hover:underline" target="_blank">{{ $calibration->certificateFilename }}</a>
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="flex gap-3">
        <button type="submit" class="btn-primary">Save Calibration</button>
        <a href="{{ route('calibrations.index') }}" class="btn-secondary">Cancel</a>
    </div>
</form>

@endsection
