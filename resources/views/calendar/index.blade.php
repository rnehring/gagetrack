@extends('layouts.app')
@section('title', 'Calibration Calendar ' . $year)
@section('content')

@php $today = now(); $currentYear = (int) $today->format('Y'); $currentMonth = (int) $today->format('n'); @endphp

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Calibration Calendar</h1>
    <div class="flex items-center gap-3">
        @if($overdueCount > 0)
            <a href="{{ route('reports.backlog') }}" class="inline-flex items-center gap-1 bg-red-100 text-red-700 text-sm font-medium px-3 py-1 rounded-full hover:bg-red-200 transition-colors">
                ⚠️ {{ $overdueCount }} Overdue
            </a>
        @endif
        <div class="flex items-center gap-2">
            <a href="{{ route('calendar.index', $year - 1) }}" class="btn-secondary px-3">← {{ $year - 1 }}</a>
            <span class="text-lg font-semibold text-gray-700 px-2">{{ $year }}</span>
            <a href="{{ route('calendar.index', $year + 1) }}" class="btn-secondary px-3">{{ $year + 1 }} →</a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
    @for($m = 1; $m <= 12; $m++)
        @php
            $monthGages  = $calendar[$m] ?? [];
            $total       = count($monthGages);
            $limit       = 10;
            $hasMore     = $total > $limit;
            $isThisMonth = ($year === $currentYear && $m === $currentMonth);
            $monthId     = 'month-' . $m;
        @endphp
        <div class="bg-white rounded-xl shadow border {{ $isThisMonth ? 'border-brand-400 ring-1 ring-brand-300' : 'border-gray-100' }} overflow-hidden flex flex-col">

            {{-- Month header --}}
            <div class="px-4 py-2 flex items-center justify-between {{ $isThisMonth ? 'bg-brand-800' : 'bg-brand-700' }} text-white shrink-0">
                <span class="font-semibold text-sm">
                    {{ date('F', mktime(0,0,0,$m,1,$year)) }}
                    @if($isThisMonth)<span class="text-xs font-normal opacity-75 ml-1">← now</span>@endif
                </span>
                <span class="text-xs bg-brand-600 rounded-full px-2 py-0.5">{{ $total }}</span>
            </div>

            {{-- Gage list --}}
            <div class="p-3 flex flex-col gap-0.5">
                @forelse($monthGages as $i => $gage)
                    @php $isOverdue = $gage->dateDue && $gage->dateDue->lt($today); @endphp
                    <a href="{{ route('gages.edit', $gage->id) }}"
                       data-month="{{ $monthId }}"
                       class="gage-row flex items-center gap-2 text-sm rounded px-1 py-0.5 group hover:bg-blue-50 {{ $i >= $limit ? 'extra hidden' : '' }}">
                        <span class="shrink-0 w-1.5 h-1.5 rounded-full mt-px {{ $isOverdue ? 'bg-red-500' : 'bg-green-400' }}"></span>
                        <span class="font-medium {{ $isOverdue ? 'text-red-700' : 'text-brand-700' }} group-hover:underline truncate">{{ $gage->gageNumber }}</span>
                        @if($gage->description)
                            <span class="text-gray-400 truncate text-xs">{{ Str::limit($gage->description, 22) }}</span>
                        @endif
                        @if($isOverdue)
                            <span class="ml-auto text-xs text-red-400 shrink-0">{{ $gage->dateDue->format('m/y') }}</span>
                        @endif
                    </a>
                @empty
                    <span class="text-gray-300 text-sm italic px-1">No gages due</span>
                @endforelse

                {{-- Expand / collapse toggle --}}
                @if($hasMore)
                    <button
                        type="button"
                        onclick="toggleMonth('{{ $monthId }}', this)"
                        class="mt-1 text-xs text-brand-600 hover:text-brand-800 hover:underline text-left px-1 py-0.5 font-medium">
                        + {{ $total - $limit }} more…
                    </button>
                @endif
            </div>

        </div>
    @endfor
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

@endsection
