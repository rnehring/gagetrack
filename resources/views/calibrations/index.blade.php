@extends('layouts.app')
@section('title', 'Calibrations')
@section('content')

<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold text-gray-800">Calibrations</h1>
    <a href="{{ route('calibrations.create') }}" class="btn-primary">+ Add Calibration</a>
</div>

{{-- Search Form --}}
<div class="bg-white rounded-xl shadow border border-gray-100 p-4 mb-4">
    <form method="POST" action="{{ route('calibrations.search') }}" class="grid grid-cols-2 sm:grid-cols-4 gap-3 items-end">
        @csrf
        <div>
            <label class="form-label">Gage</label>
            <select name="search_gageId" class="form-select">
                <option value="">All Gages</option>
                @foreach($gages as $g)
                    <option value="{{ $g->id }}" @selected($filters['search_gageId'] == $g->id)>{{ $g->gageNumber }}{{ $g->description ? ' — ' . $g->description : '' }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Date From</label>
            <input type="date" name="search_dateCalibrated_start" value="{{ $filters['search_dateCalibrated_start'] }}" class="form-input">
        </div>
        <div>
            <label class="form-label">Date To</label>
            <input type="date" name="search_dateCalibrated_stop" value="{{ $filters['search_dateCalibrated_stop'] }}" class="form-input">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="btn-primary">Search</button>
            <button type="submit" name="reset" value="1" class="btn-secondary">Reset</button>
        </div>
    </form>
</div>

{{-- Results --}}
<div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="table-th w-8"></th>
                <th class="table-th">Gage #</th>
                <th class="table-th">Date Calibrated</th>
                <th class="table-th">Calibrated By</th>
                <th class="table-th">Status</th>
                <th class="table-th">Passed</th>
                <th class="table-th">Certificate</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($calibrations as $cal)
                <tr class="hover:bg-gray-50">
                    <td class="table-td">
                        <a href="{{ route('calibrations.edit', $cal->id) }}" class="text-brand-600 hover:text-brand-800">✏️</a>
                    </td>
                    <td class="table-td font-medium">
                        <a href="{{ route('gages.edit', $cal->gageId) }}" class="text-brand-700 hover:underline">{{ $cal->gage?->gageNumber }}</a>
                    </td>
                    <td class="table-td">{{ $cal->dateCalibrated?->format('Y-m-d') }}</td>
                    <td class="table-td text-gray-500">{{ $cal->calibrationBy?->value }}</td>
                    <td class="table-td text-gray-500">{{ $cal->calibrationStatus?->value }}</td>
                    <td class="table-td">
                        @if($cal->isPassed)
                            <span class="text-green-600 font-semibold">✓ Pass</span>
                        @else
                            <span class="text-red-500">✗ Fail</span>
                        @endif
                    </td>
                    <td class="table-td">
                        @if($cal->certificateFilename)
                            <a href="{{ route('certificate.download', $cal->certificateFilename) }}" class="text-brand-600 hover:underline text-xs" target="_blank">📄 View</a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="table-td text-center text-gray-400 py-8">No calibrations found.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($calibrations->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">{{ $calibrations->links() }}</div>
    @endif
</div>

@endsection
