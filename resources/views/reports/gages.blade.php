@extends('layouts.app')
@section('title', 'Gage Report')
@section('content')

<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold text-gray-800">Gage Report</h1>
    <a href="{{ route('reports.index') }}" class="btn-secondary">← Reports</a>
</div>

{{-- Filters --}}
<div class="bg-white rounded-xl shadow border border-gray-100 p-4 mb-4">
    <form method="POST" action="{{ route('reports.gages.search') }}" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 items-end">
        @csrf
        <div>
            <label class="form-label">Gage</label>
            <select name="search_gageId" class="form-select">
                <option value="">All</option>
                @foreach($allGages as $g)
                    <option value="{{ $g->id }}" @selected($filters['search_gageId'] == $g->id)>{{ $g->gageNumber }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Location</label>
            <select name="search_locationId" class="form-select">
                <option value="0">All</option>
                @foreach($locations as $loc)
                    <option value="{{ $loc->id }}" @selected($filters['search_locationId'] == $loc->id)>{{ $loc->value }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Status</label>
            <select name="search_statusId" class="form-select">
                <option value="0">All</option>
                @foreach($statuses as $s)
                    <option value="{{ $s->id }}" @selected($filters['search_statusId'] == $s->id)>{{ $s->value }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Due From</label>
            <input type="date" name="search_dateDue_start" value="{{ $filters['search_dateDue_start'] }}" class="form-input">
        </div>
        <div>
            <label class="form-label">Due To</label>
            <input type="date" name="search_dateDue_stop" value="{{ $filters['search_dateDue_stop'] }}" class="form-input">
        </div>
        <div class="flex gap-2 col-span-2 sm:col-span-3 lg:col-span-5">
            <button type="submit" class="btn-primary">Search</button>
            <button type="submit" name="reset" value="1" class="btn-secondary">Reset</button>
            <button type="button" onclick="window.print()" class="btn-secondary ml-auto">🖨️ Print</button>
        </div>
    </form>
</div>

<div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="table-th">Gage #</th>
                <th class="table-th">Serial #</th>
                <th class="table-th">Description</th>
                <th class="table-th">Type</th>
                <th class="table-th">Location</th>
                <th class="table-th">Due Date</th>
                <th class="table-th">Frequency</th>
                <th class="table-th">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($gages as $gage)
                <tr class="hover:bg-gray-50 {{ $gage->isOverdue ? 'bg-red-50' : '' }}">
                    <td class="table-td font-medium">
                        <a href="{{ route('gages.edit', $gage->id) }}" class="text-brand-700 hover:underline">{{ $gage->gageNumber }}</a>
                    </td>
                    <td class="table-td text-gray-500">{{ $gage->serialNumber }}</td>
                    <td class="table-td text-gray-600">{{ $gage->description }}</td>
                    <td class="table-td text-gray-500">{{ $gage->type?->value }}</td>
                    <td class="table-td text-gray-500">{{ $gage->location?->value }}</td>
                    <td class="table-td {{ $gage->isOverdue ? 'text-red-600 font-semibold' : '' }}">
                        @if($gage->dateDue && $gage->dateDue->year >= 2000){{ $gage->dateDue->format('Y-m') }}@endif
                    </td>
                    <td class="table-td text-gray-500">{{ $gage->frequencyDisplay }}</td>
                    <td class="table-td text-gray-500">{{ $gage->status?->value }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="table-td text-center text-gray-400 py-8">No gages found.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-2 text-xs text-gray-400 border-t border-gray-100">{{ $gages->count() }} result(s)</div>
</div>

@endsection
