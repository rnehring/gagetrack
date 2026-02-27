<?php

namespace App\Http\Controllers;

use App\Models\Calibration;
use App\Models\Gage;
use App\Models\Metadata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CalibrationController extends Controller
{
    const TIME_UNIT_IDS = [716, 718, 720];

    private function getFilterDefaults(): array
    {
        return [
            'search_gageId' => '',
            'search_dateCalibrated_start' => date('Y-m-d', strtotime('-1 year')),
            'search_dateCalibrated_stop' => date('Y-m-d'),
        ];
    }

    public function index(Request $request)
    {
        // If coming from a direct gage link
        $gageId = $request->query('gageId');

        if ($request->isMethod('post')) {
            if ($request->has('reset')) {
                session(['calibration_filters' => $this->getFilterDefaults()]);
            } else {
                session(['calibration_filters' => [
                    'search_gageId' => $request->input('search_gageId', ''),
                    'search_dateCalibrated_start' => $request->input('search_dateCalibrated_start', ''),
                    'search_dateCalibrated_stop' => $request->input('search_dateCalibrated_stop', ''),
                ]]);
            }
            return redirect()->route('calibrations.index');
        }

        $filters = session('calibration_filters', $this->getFilterDefaults());

        if ($gageId) {
            $filters['search_gageId'] = $gageId;
            $filters['search_dateCalibrated_start'] = '';
            $filters['search_dateCalibrated_stop'] = '';
            session(['calibration_filters' => $filters]);
        }

        $query = Calibration::with(['gage', 'calibrationBy', 'calibrationStatus']);

        if (!empty($filters['search_gageId'])) {
            $query->where('gageId', $filters['search_gageId']);
        }
        if (!empty($filters['search_dateCalibrated_start'])) {
            $query->whereDate('dateCalibrated', '>=', $filters['search_dateCalibrated_start']);
        }
        if (!empty($filters['search_dateCalibrated_stop'])) {
            $query->whereDate('dateCalibrated', '<=', $filters['search_dateCalibrated_stop']);
        }

        $calibrations = $query->orderByDesc('dateCalibrated')->paginate(25)->withQueryString();

        return view('calibrations.index', [
            'calibrations' => $calibrations,
            'filters' => $filters,
            'gages' => Gage::orderBy('gageNumber')->get(),
        ]);
    }

    public function create(Request $request)
    {
        $gage = null;
        $gageId = $request->query('gageId');
        if ($gageId) {
            $gage = Gage::find($gageId);
        }

        return view('calibrations.form', [
            'calibration' => null,
            'gage' => $gage,
            'gages' => Gage::orderBy('gageNumber')->get(),
            'calibrationBys' => Metadata::byCategory('calibrationBys'),
            'calibrationStatuses' => Metadata::byCategory('calibrationStatuses'),
            'calibrationTypes' => Metadata::byCategory('calibrationTypes'),
            'foundConditions' => Metadata::byCategory('foundConditions'),
            'frequencyUnits' => Metadata::byCategory('frequencyUnits'),
        ]);
    }

    public function edit(Calibration $calibration)
    {
        return view('calibrations.form', [
            'calibration' => $calibration,
            'gage' => $calibration->gage,
            'gages' => Gage::orderBy('gageNumber')->get(),
            'calibrationBys' => Metadata::byCategory('calibrationBys'),
            'calibrationStatuses' => Metadata::byCategory('calibrationStatuses'),
            'calibrationTypes' => Metadata::byCategory('calibrationTypes'),
            'foundConditions' => Metadata::byCategory('foundConditions'),
            'frequencyUnits' => Metadata::byCategory('frequencyUnits'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateCalibration($request);

        $calibration = Calibration::create($validated);

        // Handle file upload
        $this->handleCertificateUpload($request, $calibration);

        // Update the gage's due date
        $this->updateGageDueDate($calibration);

        return redirect()->route('calibrations.index')->with('success', 'Calibration saved successfully.');
    }

    public function update(Request $request, Calibration $calibration)
    {
        $validated = $this->validateCalibration($request);
        $calibration->update($validated);
        $calibration->refresh();
        $this->handleCertificateUpload($request, $calibration);

        // Recalculate due date if this is the most recent calibration for the gage
        $latestCalibration = Calibration::where('gageId', $calibration->gageId)
            ->orderByDesc('dateCalibrated')
            ->first();

        if ($latestCalibration && $latestCalibration->id === $calibration->id) {
            $this->updateGageDueDate($calibration);
        }

        return redirect()->route('calibrations.index')->with('success', 'Calibration updated successfully.');
    }

    public function downloadCertificate(string $filename)
    {
        $path = storage_path('app/certificates/' . $filename);
        if (!file_exists($path)) {
            abort(404);
        }
        return response()->download($path);
    }

    private function validateCalibration(Request $request): array
    {
        return $request->validate([
            'gageId' => 'required|integer',
            'dateCalibrated' => 'required|date',
            'calibrationById' => 'nullable|integer',
            'calibrationTypeId' => 'nullable|integer',
            'calibrationStatusId' => 'nullable|integer',
            'foundConditionId' => 'nullable|integer',
            'results' => 'nullable|string|max:255',
            'actionRequired' => 'nullable|string|max:255',
            'findings' => 'nullable|string',
            'temperature' => 'nullable|string|max:50',
            'humidity' => 'nullable|string|max:50',
            'frequency' => 'nullable|integer',
            'frequencyUnitId' => 'nullable|integer',
            'certificateNumber' => 'nullable|string|max:100',
            'isPassed' => 'nullable|boolean',
            'timeRequired' => 'nullable|string|max:10',
        ]);
    }

    private function handleCertificateUpload(Request $request, Calibration $calibration): void
    {
        if ($request->hasFile('certificateFile') && $request->file('certificateFile')->isValid()) {
            $gage = Gage::find($calibration->gageId);
            $filename = urlencode($gage->gageNumber) . '.pdf';
            $request->file('certificateFile')->storeAs('certificates', $filename);
            $calibration->update(['certificateFilename' => $filename]);
        }
    }

    private function updateGageDueDate(Calibration $calibration): void
    {
        if (!in_array($calibration->frequencyUnitId, self::TIME_UNIT_IDS)) {
            return;
        }

        $unit = match ((int) $calibration->frequencyUnitId) {
            716 => 'days',
            718 => 'months',
            720 => 'years',
        };

        $dateDue = date('Y-m-d', strtotime("+{$calibration->frequency} {$unit}", strtotime($calibration->dateCalibrated)));
        Gage::where('id', $calibration->gageId)->update(['dateDue' => $dateDue]);
    }
}
