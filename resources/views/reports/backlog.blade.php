@extends('layouts.app')
@section('title', 'Backlog Report')
@section('content')

<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold text-gray-800">Overdue Calibrations (Backlog)</h1>
    <div class="flex gap-2">
        <button onclick="window.print()" class="btn-secondary">🖨️ Print</button>
        <a href="{{ route('reports.index') }}" class="btn-secondary">← Reports</a>
    </div>
</div>

<div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="table-th">Gage #</th>
                <th class="table-th">Serial #</th>
                <th class="table-th">Description</th>
                <th class="table-th">Location</th>
                <th class="table-th">Due Date</th>
                <th class="table-th">Days Overdue</th>
                <th class="table-th">Frequency</th>
                <th class="table-th">Status</th>
                <th class="table-th print:hidden">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($gages as $gage)
                @php
                    $daysOverdue = $gage->dateDue ? now()->diffInDays($gage->dateDue, false) * -1 : null;
                @endphp
                <tr class="hover:bg-red-50 bg-red-50/50">
                    <td class="table-td font-medium text-red-700">{{ $gage->gageNumber }}</td>
                    <td class="table-td text-gray-500">{{ $gage->serialNumber }}</td>
                    <td class="table-td text-gray-700">{{ $gage->description }}</td>
                    <td class="table-td text-gray-500">{{ $gage->location?->value }}</td>
                    <td class="table-td text-red-600 font-semibold">
                        @if($gage->dateDue && $gage->dateDue->year >= 2000)
                            {{ $gage->dateDue->format('Y-m-d') }}
                        @else
                            —
                        @endif
                    </td>
                    <td class="table-td text-red-600 font-semibold">
                        @if($daysOverdue !== null && $daysOverdue > 0)
                            {{ $daysOverdue }} days
                        @else
                            —
                        @endif
                    </td>
                    <td class="table-td text-gray-500">{{ $gage->frequencyDisplay }}</td>
                    <td class="table-td text-gray-500">{{ $gage->status?->value }}</td>
                    <td class="table-td print:hidden">
                        <a href="{{ route('gages.edit', $gage->id) }}" class="text-brand-700 hover:underline text-xs">Edit Gage</a>
                        &nbsp;|&nbsp;
                        <a href="{{ route('calibrations.create') }}?gageId={{ $gage->id }}" class="text-green-700 hover:underline text-xs">Calibrate</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="table-td text-center text-gray-400 py-10">
                        ✅ No overdue calibrations found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-2 text-xs text-gray-400 border-t border-gray-100">
        {{ $gages->count() }} overdue gage(s) as of {{ now()->format('Y-m-d') }}
    </div>
</div>

@endsection
