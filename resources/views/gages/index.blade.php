@extends('layouts.app')
@section('title', 'Gages')
@section('content')

<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold text-gray-800">Gages</h1>
    <a href="{{ route('gages.create') }}" class="btn-primary">+ Add Gage</a>
</div>

{{-- Search Form --}}
<div class="bg-white rounded-xl shadow border border-gray-100 p-4 mb-4">
    <form method="POST" action="{{ route('gages.search') }}" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 items-end">
        @csrf
        <div>
            <label class="form-label">Gage Number</label>
            <input type="text" name="search_gageNumber" value="{{ $filters['search_gageNumber'] }}" class="form-input" placeholder="Search...">
        </div>
        <div>
            <label class="form-label">Description</label>
            <input type="text" name="search_description" value="{{ $filters['search_description'] }}" class="form-input" placeholder="Search...">
        </div>
        <div>
            <label class="form-label">Location</label>
            <select name="search_locationId" class="form-select">
                <option value="0">All Locations</option>
                @foreach($locations as $loc)
                    <option value="{{ $loc->id }}" @selected($filters['search_locationId'] == $loc->id)>{{ $loc->value }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Type</label>
            <select name="search_typeId" class="form-select">
                <option value="0">All Types</option>
                @foreach($types as $type)
                    <option value="{{ $type->id }}" @selected($filters['search_typeId'] == $type->id)>{{ $type->value }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Status</label>
            <select name="search_statusId" class="form-select">
                <option value="0">All Statuses</option>
                @foreach($statuses as $status)
                    <option value="{{ $status->id }}" @selected($filters['search_statusId'] == $status->id)>{{ $status->value }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2 col-span-2 sm:col-span-3 lg:col-span-5">
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
                <th class="table-th">Description</th>
                <th class="table-th">Location</th>
                <th class="table-th">Next Due</th>
                <th class="table-th">Status</th>
                <th class="table-th"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($gages as $gage)
                <tr class="hover:bg-gray-50 {{ $gage->isOverdue ? 'bg-red-50' : '' }}">
                    <td class="table-td">
                        <a href="{{ route('gages.edit', $gage->id) }}" class="text-brand-600 hover:text-brand-800" title="Edit">✏️</a>
                    </td>
                    <td class="table-td font-medium">
                        <a href="{{ route('gages.edit', $gage->id) }}" class="text-brand-700 hover:underline">{{ $gage->gageNumber }}</a>
                    </td>
                    <td class="table-td text-gray-600">{{ $gage->description }}</td>
                    <td class="table-td text-gray-500">{{ $gage->location?->value }}</td>
                    <td class="table-td {{ $gage->isOverdue ? 'text-red-600 font-semibold' : '' }}">
                        @if($gage->dateDue && $gage->dateDue->year >= 2000)
                            {{ $gage->dateDue->format('Y-m') }}
                            @if($gage->isOverdue) ⚠️ @endif
                        @endif
                    </td>
                    <td class="table-td text-gray-500">{{ $gage->status?->value }}</td>
                    <td class="table-td">
                        <a href="{{ route('calibrations.index', ['gageId' => $gage->id]) }}" class="btn-secondary text-xs px-2 py-1">Calibrations</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="table-td text-center text-gray-400 py-8">No gages found.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($gages->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">{{ $gages->links() }}</div>
    @endif
</div>

@endsection
