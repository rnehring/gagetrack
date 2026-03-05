@extends('layouts.app')
@section('title', 'Calibration Calendar ' . $year)
@section('content')

@php
    $today        = now();
    $currentYear  = (int) $today->format('Y');
    $currentMonth = (int) $today->format('n');
@endphp

{{-- Stat Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    {{-- Total Gages --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-5 py-4 flex items-center gap-4">
        <div class="shrink-0 w-11 h-11 rounded-lg bg-brand-50 flex items-center justify-center text-2xl">
            🔧
        </div>
        <div>
            <div class="text-2xl font-bold text-gray-800 leading-none">{{ number_format($totalGages) }}</div>
            <div class="text-xs font-medium text-gray-400 mt-1 uppercase tracking-wide">Active Gages</div>
        </div>
    </div>

    {{-- Currently Calibrated --}}
    <div class="bg-white rounded-xl border border-emerald-200 shadow-sm px-5 py-4 flex items-center gap-4">
        <div class="shrink-0 w-11 h-11 rounded-lg bg-emerald-50 flex items-center justify-center text-2xl">
            ✅
        </div>
        <div>
            <div class="text-2xl font-bold text-emerald-700 leading-none">{{ number_format($currentGages) }}</div>
            <div class="text-xs font-medium text-gray-400 mt-1 uppercase tracking-wide">In Calibration</div>
            <div class="text-xs text-gray-300 mt-0.5">Due date current</div>
        </div>
    </div>

    {{-- Overdue / Failed --}}
    <div class="bg-white rounded-xl border {{ ($overdueCount > 0 || $failedGages > 0) ? 'border-red-200' : 'border-gray-200' }} shadow-sm px-5 py-4 flex items-center gap-4">
        <div class="shrink-0 w-11 h-11 rounded-lg {{ ($overdueCount > 0 || $failedGages > 0) ? 'bg-red-50' : 'bg-gray-50' }} flex items-center justify-center text-2xl">
            ⚠️
        </div>
        <div>
            <div class="text-2xl font-bold {{ ($overdueCount > 0 || $failedGages > 0) ? 'text-red-600' : 'text-gray-400' }} leading-none">
                {{ number_format($overdueCount) }}
            </div>
            <div class="text-xs font-medium text-gray-400 mt-1 uppercase tracking-wide">Overdue</div>
            @if($failedGages > 0)
                <div class="text-xs text-red-400 mt-0.5">{{ $failedGages }} last-failed</div>
            @else
                <div class="text-xs text-gray-300 mt-0.5">All current</div>
            @endif
        </div>
    </div>

    {{-- Suppliers --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-5 py-4 flex items-center gap-4">
        <div class="shrink-0 w-11 h-11 rounded-lg bg-gray-50 flex items-center justify-center text-2xl">
            🏭
        </div>
        <div>
            <div class="text-2xl font-bold text-gray-800 leading-none">{{ number_format($activeSuppliers) }}</div>
            <div class="text-xs font-medium text-gray-400 mt-1 uppercase tracking-wide">Suppliers</div>
            <div class="text-xs text-gray-300 mt-0.5">Active</div>
        </div>
    </div>

</div>

{{-- Page Header + Year Nav --}}
<div class="flex items-center justify-between mb-5">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Calibration Calendar</h1>
        <p class="text-sm text-gray-400 mt-0.5">Gages scheduled for calibration by month</p>
    </div>
    {{-- Year Navigator --}}
    <div class="inline-flex items-center bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden divide-x divide-gray-200">
        <a href="{{ route('calendar.index', $year - 1) }}"
           class="px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 transition-colors font-medium">← {{ $year - 1 }}</a>
        <span class="px-4 py-2 text-sm font-bold text-gray-800 bg-gray-50">{{ $year }}</span>
        <a href="{{ route('calendar.index', $year + 1) }}"
           class="px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 transition-colors font-medium">{{ $year + 1 }} →</a>
    </div>
</div>

{{-- Calendar Grid --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
    @for($m = 1; $m <= 12; $m++)
        @php
            $monthGages     = $calendar[$m] ?? [];
            $total          = count($monthGages);
            $limit          = 10;
            $hasMore        = $total > $limit;
            $isThisMonth    = ($year === $currentYear && $m === $currentMonth);
            $isPast         = ($year < $currentYear) || ($year === $currentYear && $m < $currentMonth);
            $overdueInMonth = collect($monthGages)->filter(fn($g) => $g->dateDue && $g->dateDue->lt($today))->count();
            $monthId        = 'month-' . $m;

            // Card styling
            if ($isThisMonth) {
                $cardBorder  = 'border-brand-400 ring-2 ring-brand-200 shadow-md';
                $accentBar   = 'bg-brand-600';
                $headerBg    = 'bg-brand-50';
                $monthColor  = 'text-brand-800';
                $yearColor   = 'text-brand-400';
                $countBg     = 'bg-brand-100 text-brand-700';
            } elseif ($isPast && $overdueInMonth > 0) {
                $cardBorder  = 'border-red-300 shadow-sm';
                $accentBar   = 'bg-red-500';
                $headerBg    = 'bg-red-50';
                $monthColor  = 'text-red-800';
                $yearColor   = 'text-red-300';
                $countBg     = 'bg-red-100 text-red-700';
            } elseif ($isPast) {
                $cardBorder  = 'border-gray-200';
                $accentBar   = 'bg-gray-300';
                $headerBg    = 'bg-gray-50';
                $monthColor  = 'text-gray-400';
                $yearColor   = 'text-gray-300';
                $countBg     = 'bg-gray-100 text-gray-400';
            } else {
                $cardBorder  = 'border-gray-200 shadow-sm';
                $accentBar   = 'bg-brand-500';
                $headerBg    = 'bg-white';
                $monthColor  = 'text-gray-800';
                $yearColor   = 'text-gray-300';
                $countBg     = 'bg-gray-100 text-gray-600';
            }

            // Faded opacity for empty past months
            $cardOpacity = ($isPast && $total === 0) ? 'opacity-55' : '';
        @endphp

        <div class="bg-white rounded-xl border {{ $cardBorder }} {{ $cardOpacity }} overflow-hidden flex flex-col">

            {{-- Month Header --}}
            <div class="{{ $headerBg }} px-4 py-3 flex items-start justify-between border-b border-gray-100">
                <div class="flex items-center gap-3">
                    {{-- Colored left accent bar --}}
                    <div class="w-1 self-stretch rounded-full {{ $accentBar }} shrink-0"></div>
                    <div>
                        <div class="font-bold text-sm leading-tight {{ $monthColor }}">
                            {{ date('F', mktime(0,0,0,$m,1,$year)) }}
                        </div>
                        <div class="text-xs {{ $yearColor }} mt-0.5">{{ $year }}</div>
                    </div>
                </div>
                <div class="flex items-center gap-1.5">
                    @if($overdueInMonth > 0)
                        <span class="inline-flex items-center gap-1 bg-red-100 text-red-600 text-xs font-bold px-1.5 py-0.5 rounded-full">
                            ⚠️ {{ $overdueInMonth }}
                        </span>
                    @endif
                    @if($isThisMonth)
                        <span class="bg-brand-100 text-brand-700 text-xs font-semibold px-2 py-0.5 rounded-full">
                            Now
                        </span>
                    @endif
                    <span class="{{ $countBg }} text-xs font-semibold px-2 py-0.5 rounded-full min-w-[24px] text-center">
                        {{ $total }}
                    </span>
                </div>
            </div>

            {{-- Gage List --}}
            <div class="p-2 flex flex-col gap-0.5 flex-1 min-h-[60px]">
                @forelse($monthGages as $i => $gage)
                    @php $isOverdue = $gage->dateDue && $gage->dateDue->lt($today); @endphp
                    <a href="{{ route('gages.edit', $gage->id) }}"
                       data-month="{{ $monthId }}"
                       class="gage-row group flex items-center gap-2 rounded-md px-2 py-1.5 text-xs transition-colors
                              {{ $i >= $limit ? 'extra hidden' : '' }}
                              {{ $isOverdue ? 'bg-red-50 hover:bg-red-100' : 'hover:bg-blue-50' }}">
                        {{-- Status dot --}}
                        <span class="shrink-0 w-2 h-2 rounded-full {{ $isOverdue ? 'bg-red-500' : 'bg-emerald-400' }}"></span>
                        {{-- Gage number --}}
                        <span class="font-semibold truncate flex-1 {{ $isOverdue ? 'text-red-700' : 'text-brand-700' }} group-hover:underline">
                            {{ $gage->gageNumber }}
                        </span>
                        {{-- Description --}}
                        @if($gage->description)
                            <span class="text-gray-400 truncate text-xs" style="max-width:72px">
                                {{ Str::limit($gage->description, 14) }}
                            </span>
                        @endif
                        {{-- Overdue date badge --}}
                        @if($isOverdue)
                            <span class="ml-auto shrink-0 text-xs font-bold text-red-500">
                                {{ $gage->dateDue->format('M d') }}
                            </span>
                        @endif
                    </a>
                @empty
                    <div class="flex items-center justify-center py-6 text-gray-300 text-xs italic select-none">
                        No gages due
                    </div>
                @endforelse

                {{-- Expand / Collapse --}}
                @if($hasMore)
                    <button type="button"
                            onclick="toggleMonth('{{ $monthId }}', this)"
                            class="mt-1.5 text-xs text-brand-600 hover:text-brand-800 hover:underline text-left px-2 py-1 font-medium rounded transition-colors hover:bg-blue-50">
                        + {{ $total - $limit }} more…
                    </button>
                @endif
            </div>

        </div>
    @endfor
</div>

{{-- Legend --}}
<div class="mt-6 flex items-center gap-6 text-xs text-gray-400">
    <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-400 inline-block"></span> On schedule</span>
    <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-red-500 inline-block"></span> Overdue</span>
    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded border-2 border-brand-400 inline-block"></span> Current month</span>
</div>

<script>
function toggleMonth(monthId, btn) {
    const rows = document.querySelectorAll(`a.extra[data-month="${monthId}"]`);
    const isHidden = rows[0]?.classList.contains('hidden');
    rows.forEach(r => r.classList.toggle('hidden', !isHidden));
    if (!btn.dataset.moreCount) btn.dataset.moreCount = btn.textContent.match(/\d+/)?.[0];
    btn.textContent = isHidden ? '− Show less' : `+ ${btn.dataset.moreCount} more…`;
}
</script>

@endsection
