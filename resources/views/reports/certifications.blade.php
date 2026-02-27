@extends('layouts.app')
@section('title', 'Calibration Certificate')
@section('content')

<div class="flex items-center justify-between mb-4 print:hidden">
    <h1 class="text-2xl font-bold text-gray-800">Calibration Certificate</h1>
    <div class="flex gap-2">
        @if($gage && $calibration)
            <button onclick="window.print()" class="btn-primary">🖨️ Print Certificate</button>
        @endif
        <a href="{{ route('reports.index') }}" class="btn-secondary">← Reports</a>
    </div>
</div>

{{-- Gage selector --}}
<div class="bg-white rounded-xl shadow border border-gray-100 p-4 mb-6 print:hidden">
    <form method="POST" action="{{ route('reports.certifications.search') }}" class="flex gap-3 items-end">
        @csrf
        <div class="flex-1 max-w-xs">
            <label class="form-label">Select Gage</label>
            <select name="search_gageId" class="form-select" onchange="this.form.submit()">
                <option value="">— Select a gage —</option>
                @foreach($gages as $g)
                    <option value="{{ $g->id }}" @selected($selectedGageId == $g->id)>
                        {{ $g->gageNumber }}@if($g->description) — {{ Str::limit($g->description, 40) }}@endif
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-primary">View</button>
    </form>
</div>

@if($gage && $calibration)

{{-- Certificate --}}
<div class="bg-white rounded-xl shadow border border-gray-200 p-8 max-w-3xl mx-auto print:shadow-none print:border-0 print:p-0" id="certificate">

    <div class="flex justify-between items-start mb-6 pb-4 border-b border-gray-200">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Calibration Certificate</h2>
            <p class="text-sm text-gray-500 mt-1">Certificate of Calibration</p>
        </div>
        <div class="text-right text-sm text-gray-500">
            <div><span class="font-semibold text-gray-700">Print Date:</span> {{ now()->format('Y-m-d') }}</div>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-6 mb-6">
        {{-- Left column --}}
        <div class="space-y-2 text-sm">
            <div class="grid grid-cols-2">
                <span class="font-semibold text-gray-600">Gage ID</span>
                <span class="text-gray-800">{{ $gage->gageNumber }}</span>
            </div>
            <div class="grid grid-cols-2">
                <span class="font-semibold text-gray-600">Serial #</span>
                <span class="text-gray-800">{{ $gage->serialNumber ?: '—' }}</span>
            </div>
            <div class="grid grid-cols-2">
                <span class="font-semibold text-gray-600">Description</span>
                <span class="text-gray-800">{{ $gage->description ?: '—' }}</span>
            </div>
            <div class="grid grid-cols-2">
                <span class="font-semibold text-gray-600">Unit of Measure</span>
                <span class="text-gray-800">{{ $gage->unitMeasure?->value ?: '—' }}</span>
            </div>
            <div class="grid grid-cols-2">
                <span class="font-semibold text-gray-600">Manufacturer</span>
                <span class="text-gray-800">{{ $gage->manufacturer?->value ?: '—' }}</span>
            </div>
            <div class="grid grid-cols-2">
                <span class="font-semibold text-gray-600">Location</span>
                <span class="text-gray-800">{{ $gage->location?->value ?: '—' }}</span>
            </div>
        </div>

        {{-- Right column --}}
        <div class="space-y-2 text-sm">
            <div class="grid grid-cols-2">
                <span class="font-semibold text-gray-600">Cal. Date</span>
                <span class="text-gray-800">{{ $calibration->dateCalibrated ? \Carbon\Carbon::parse($calibration->dateCalibrated)->format('Y-m-d') : '—' }}</span>
            </div>
            <div class="grid grid-cols-2">
                <span class="font-semibold text-gray-600">Next Due</span>
                <span class="text-gray-800">
                    @if($gage->dateDue && $gage->dateDue->year >= 2000){{ $gage->dateDue->format('Y-m-d') }}@else —@endif
                </span>
            </div>
            <div class="grid grid-cols-2">
                <span class="font-semibold text-gray-600">Cal. Frequency</span>
                <span class="text-gray-800">{{ $gage->frequencyDisplay }}</span>
            </div>
            <div class="grid grid-cols-2">
                <span class="font-semibold text-gray-600">Approved</span>
                <span class="{{ $calibration->isPassed ? 'text-green-700 font-semibold' : 'text-red-600 font-semibold' }}">
                    {{ $calibration->isPassed ? 'Yes' : 'No' }}
                </span>
            </div>
            <div class="grid grid-cols-2">
                <span class="font-semibold text-gray-600">As Found Condition</span>
                <span class="text-gray-800">{{ $calibration->foundCondition?->value ?: '—' }}</span>
            </div>
            <div class="grid grid-cols-2">
                <span class="font-semibold text-gray-600">NIST #</span>
                <span class="text-gray-800">{{ $gage->nistNumber ?: '—' }}</span>
            </div>
        </div>
    </div>

    <hr class="border-gray-200 my-4">

    {{-- Certification statement --}}
    <div class="mb-4">
        <div class="font-semibold text-gray-700 mb-2 text-sm">Certification Statement</div>
        <textarea class="w-full text-sm text-gray-700 border border-gray-300 rounded-lg p-3 print:border-0 print:p-0 print:resize-none"
                  rows="4" id="certStatement">{{ $defaultStatement }}</textarea>
        <div class="hidden print:block text-sm text-gray-700 mt-1" id="certStatementPrint">{{ $defaultStatement }}</div>
    </div>

    @if($calibration->findings)
    <div class="mb-4">
        <div class="font-semibold text-gray-700 mb-2 text-sm">Findings</div>
        <div class="text-sm text-gray-700 bg-gray-50 rounded-lg p-3 print:bg-transparent print:p-0">{{ $calibration->findings }}</div>
    </div>
    @endif

    @if($calibration->temperature || $calibration->humidity)
    <div class="grid grid-cols-2 gap-4 mb-4 text-sm">
        @if($calibration->temperature)
        <div>
            <span class="font-semibold text-gray-600">Temperature:</span>
            <span class="text-gray-800">{{ $calibration->temperature }}</span>
        </div>
        @endif
        @if($calibration->humidity)
        <div>
            <span class="font-semibold text-gray-600">Humidity:</span>
            <span class="text-gray-800">{{ $calibration->humidity }}</span>
        </div>
        @endif
    </div>
    @endif

    <hr class="border-gray-200 my-4">

    <div class="grid grid-cols-2 gap-4 text-sm">
        <div>
            <span class="font-semibold text-gray-600">Calibrated By:</span>
            <span class="text-gray-800 ml-2">{{ $calibration->calibrationBy?->value ?: '—' }}</span>
        </div>
        <div>
            <span class="font-semibold text-gray-600">Date:</span>
            <span class="text-gray-800 ml-2">{{ $calibration->dateCalibrated ? \Carbon\Carbon::parse($calibration->dateCalibrated)->format('Y-m-d') : '—' }}</span>
        </div>
    </div>

    <div class="mt-6 pt-4 border-t border-gray-200 text-center text-xs text-gray-400">
        Calibration Certificate Report — Generated {{ now()->format('Y-m-d H:i') }}
    </div>

</div>

@elseif($selectedGageId && !$gage)
    <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-xl p-4 max-w-xl">
        ⚠️ Gage not found. Please select a valid gage.
    </div>
@elseif($selectedGageId && $gage && !$calibration)
    <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-xl p-4 max-w-xl">
        ⚠️ No passing calibration found for <strong>{{ $gage->gageNumber }}</strong>.
        A certificate can only be generated after a passed calibration has been recorded.
    </div>
@else
    <div class="bg-gray-50 border border-gray-200 text-gray-500 rounded-xl p-8 text-center max-w-xl mx-auto">
        <div class="text-4xl mb-3">📄</div>
        <div class="font-semibold text-gray-700 mb-1">Select a Gage</div>
        <div class="text-sm">Choose a gage from the dropdown above to generate its calibration certificate.</div>
    </div>
@endif

<script>
// On print, swap textarea content to plain div so it's not cropped
window.addEventListener('beforeprint', function() {
    const ta = document.getElementById('certStatement');
    const div = document.getElementById('certStatementPrint');
    if (ta && div) {
        div.textContent = ta.value;
        ta.style.display = 'none';
        div.style.display = 'block';
    }
});
window.addEventListener('afterprint', function() {
    const ta = document.getElementById('certStatement');
    const div = document.getElementById('certStatementPrint');
    if (ta && div) {
        ta.style.display = '';
        div.style.display = 'none';
    }
});
</script>

@endsection
