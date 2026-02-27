<?php

namespace App\Http\Controllers;

use App\Models\Configuration;
use App\Models\Gage;
use App\Models\Metadata;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GageController extends Controller
{
    // Frequency unit IDs that represent time-based units
    const TIME_UNIT_IDS = [716, 718, 720];

    public function index(Request $request)
    {
        $query = Gage::with(['status', 'type', 'location', 'frequencyUnit']);

        // Search filters from session
        $filters = session('gage_filters', [
            'search_gageNumber' => '',
            'search_description' => '',
            'search_locationId' => 0,
            'search_typeId' => 0,
            'search_statusId' => 1,
        ]);

        if ($request->isMethod('post')) {
            if ($request->has('reset')) {
                $filters = [
                    'search_gageNumber' => '',
                    'search_description' => '',
                    'search_locationId' => 0,
                    'search_typeId' => 0,
                    'search_statusId' => 1,
                ];
            } else {
                $filters = [
                    'search_gageNumber' => $request->input('search_gageNumber', ''),
                    'search_description' => $request->input('search_description', ''),
                    'search_locationId' => (int) $request->input('search_locationId', 0),
                    'search_typeId' => (int) $request->input('search_typeId', 0),
                    'search_statusId' => (int) $request->input('search_statusId', 0),
                ];
            }
            session(['gage_filters' => $filters]);
            return redirect()->route('gages.index');
        }

        if (! empty($filters['search_gageNumber'])) {
            $query->where('gageNumber', 'like', '%' . $filters['search_gageNumber'] . '%');
        }
        if (! empty($filters['search_description'])) {
            $query->where('description', 'like', '%' . $filters['search_description'] . '%');
        }
        if (! empty($filters['search_locationId'])) {
            $query->where('locationId', $filters['search_locationId']);
        }
        if (! empty($filters['search_typeId'])) {
            $query->where('typeId', $filters['search_typeId']);
        }
        if (! empty($filters['search_statusId'])) {
            $query->where('statusId', $filters['search_statusId']);
        }

        $gages = $query->orderBy('gageNumber')->paginate(25)->withQueryString();

        return view('gages.index', [
            'gages' => $gages,
            'filters' => $filters,
            'statuses' => Metadata::byCategory('statuses'),
            'locations' => Metadata::byCategory('locations'),
            'types' => Metadata::byCategory('types'),
        ]);
    }

    public function create()
    {
        $nextNumber = $this->getNextGageNumber();

        return view('gages.form', [
            'gage' => null,
            'gageNumber' => "T-{$nextNumber}",
            'statuses' => Metadata::byCategory('statuses'),
            'types' => Metadata::byCategory('types'),
            'locations' => Metadata::byCategory('locations'),
            'manufacturers' => Metadata::byCategory('manufacturers'),
            'owners' => Metadata::byCategory('owners'),
            'unitMeasures' => Metadata::byCategory('unitMeasures'),
            'frequencyUnits' => Metadata::byCategory('frequencyUnits'),
            'suppliers' => Supplier::where('isActive', 1)->orderBy('name')->get(),
        ]);
    }

    public function edit(Gage $gage)
    {
        // Load the most recent passed calibration for certificate link
        $latestCalibration = $gage->calibrations()
            ->where('isPassed', 1)
            ->orderByDesc('dateCalibrated')
            ->first();

        return view('gages.form', [
            'gage' => $gage,
            'gageNumber' => $gage->gageNumber,
            'latestCalibration' => $latestCalibration,
            'statuses' => Metadata::byCategory('statuses'),
            'types' => Metadata::byCategory('types'),
            'locations' => Metadata::byCategory('locations'),
            'manufacturers' => Metadata::byCategory('manufacturers'),
            'owners' => Metadata::byCategory('owners'),
            'unitMeasures' => Metadata::byCategory('unitMeasures'),
            'frequencyUnits' => Metadata::byCategory('frequencyUnits'),
            'suppliers' => Supplier::where('isActive', 1)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'gageNumber' => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
            'serialNumber' => 'nullable|string|max:100',
            'modelNumber' => 'nullable|string|max:100',
            'nistNumber' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'statusId' => 'nullable|integer',
            'typeId' => 'nullable|integer',
            'locationId' => 'nullable|integer',
            'manufacturerId' => 'nullable|integer',
            'ownerId' => 'nullable|integer',
            'unitMeasureId' => 'nullable|integer',
            'frequencyUnitId' => 'nullable|integer',
            'supplierId' => 'nullable|integer',
            'frequency' => 'nullable|integer',
            'dateDue' => 'nullable|date',
        ]);

        $validated['isActive'] = in_array($validated['statusId'] ?? 0, [1, 7]) ? 1 : 0;

        $gage = Gage::create($validated);

        // Update the next gage number if this used the auto-assigned one
        $currentNext = Configuration::getValue('nextGageNumber');
        if ($currentNext && str_contains($validated['gageNumber'], "T-{$currentNext}")) {
            $this->updateNextGageNumber((int) $currentNext);
        }

        return redirect()->route('gages.index')->with('success', 'Gage saved successfully.');
    }

    public function update(Request $request, Gage $gage)
    {
        $validated = $request->validate([
            'gageNumber' => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
            'serialNumber' => 'nullable|string|max:100',
            'modelNumber' => 'nullable|string|max:100',
            'nistNumber' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'statusId' => 'nullable|integer',
            'typeId' => 'nullable|integer',
            'locationId' => 'nullable|integer',
            'manufacturerId' => 'nullable|integer',
            'ownerId' => 'nullable|integer',
            'unitMeasureId' => 'nullable|integer',
            'frequencyUnitId' => 'nullable|integer',
            'supplierId' => 'nullable|integer',
            'frequency' => 'nullable|integer',
            'dateDue' => 'nullable|date',
        ]);

        $validated['isActive'] = in_array($validated['statusId'] ?? 0, [1, 7]) ? 1 : 0;

        // Recalculate due date if frequency changed
        $frequencyChanged = $gage->frequency != $validated['frequency'] ||
                            $gage->frequencyUnitId != $validated['frequencyUnitId'];

        if ($frequencyChanged && in_array($validated['frequencyUnitId'] ?? 0, self::TIME_UNIT_IDS)) {
            $baseDueDate = $gage->dateDue ? $gage->dateDue->toDateString() : date('Y-m-d');

            // Roll back old frequency to get the base date
            if ($gage->dateDue && in_array($gage->frequencyUnitId, self::TIME_UNIT_IDS) && $gage->frequency) {
                $oldUnit = match ($gage->frequencyUnitId) {
                    716 => 'days',
                    718 => 'months',
                    720 => 'years',
                };
                $baseDueDate = date('Y-m-d', strtotime("-{$gage->frequency} {$oldUnit}", strtotime($baseDueDate)));
            }

            $newUnit = match ((int) ($validated['frequencyUnitId'] ?? 0)) {
                716 => 'days',
                718 => 'months',
                720 => 'years',
                default => null,
            };

            if ($newUnit) {
                $validated['dateDue'] = date('Y-m-d', strtotime("+{$validated['frequency']} {$newUnit}", strtotime($baseDueDate)));
            }
        } elseif (! in_array($validated['frequencyUnitId'] ?? 0, self::TIME_UNIT_IDS)) {
            $validated['dateDue'] = null;
        }

        $gage->update($validated);

        return redirect()->route('gages.index')->with('success', 'Gage updated successfully.');
    }

    public function destroy(Gage $gage)
    {
        $gage->delete();
        return redirect()->route('gages.index')->with('success', 'Gage deleted successfully.');
    }

    private function getNextGageNumber(): int
    {
        $gageNumber = (int) Configuration::getValue('nextGageNumber') ?: 1000;

        // Find an unused number
        while (Gage::where('gageNumber', 'like', "T-{$gageNumber}%")->exists()) {
            $gageNumber++;
        }

        Configuration::setValue('nextGageNumber', (string) $gageNumber);
        return $gageNumber;
    }

    private function updateNextGageNumber(int $current): void
    {
        $next = $current + 1;
        while (Gage::where('gageNumber', 'like', "T-{$next}%")->exists()) {
            $next++;
        }
        Configuration::setValue('nextGageNumber', (string) $next);
    }
}
