<?php

namespace App\Http\Controllers;

use App\Models\Calibration;
use App\Models\Gage;
use App\Models\Metadata;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function gages(Request $request)
    {
        $filters = session('report_gage_filters', [
            'search_gageId'       => '',
            'search_locationId'   => 0,
            'search_statusId'     => 1,
            'search_dateDue_start' => '',
            'search_dateDue_stop'  => '',
        ]);

        if ($request->isMethod('post')) {
            if ($request->has('reset')) {
                $filters = [
                    'search_gageId'       => '',
                    'search_locationId'   => 0,
                    'search_statusId'     => 1,
                    'search_dateDue_start' => '',
                    'search_dateDue_stop'  => '',
                ];
            } else {
                $filters = [
                    'search_gageId'       => $request->input('search_gageId', ''),
                    'search_locationId'   => (int) $request->input('search_locationId', 0),
                    'search_statusId'     => (int) $request->input('search_statusId', 0),
                    'search_dateDue_start' => $request->input('search_dateDue_start', ''),
                    'search_dateDue_stop'  => $request->input('search_dateDue_stop', ''),
                ];
            }
            session(['report_gage_filters' => $filters]);
            return redirect()->route('reports.gages');
        }

        $query = Gage::with(['status', 'type', 'location', 'frequencyUnit']);

        if (! empty($filters['search_gageId'])) {
            $query->where('id', $filters['search_gageId']);
        }
        if (! empty($filters['search_locationId'])) {
            $query->where('locationId', $filters['search_locationId']);
        }
        if (! empty($filters['search_statusId'])) {
            $query->where('statusId', $filters['search_statusId']);
        }
        if (! empty($filters['search_dateDue_start'])) {
            $query->whereDate('dateDue', '>=', $filters['search_dateDue_start']);
        }
        if (! empty($filters['search_dateDue_stop'])) {
            $query->whereDate('dateDue', '<=', $filters['search_dateDue_stop']);
        }

        $gages = $query->orderBy('gageNumber')->get();

        return view('reports.gages', [
            'gages'     => $gages,
            'filters'   => $filters,
            'allGages'  => Gage::orderBy('gageNumber')->get(),
            'locations' => Metadata::byCategory('locations'),
            'statuses'  => Metadata::byCategory('statuses'),
        ]);
    }

    public function backlog()
    {
        $gages = Gage::with(['status', 'type', 'location', 'frequencyUnit'])
            ->where('isActive', 1)
            ->where('statusId', 1)
            ->where('dateDue', '<', date('Y-m-d'))
            ->where('dateDue', '!=', '0000-00-00')
            ->orderBy('dateDue')
            ->get();

        return view('reports.backlog', compact('gages'));
    }

    public function certifications(Request $request, int $gageId = null)
    {
        if ($request->isMethod('post')) {
            $gageId = (int) $request->input('search_gageId', 0);
            return redirect()->route('reports.certifications', $gageId ?: null);
        }

        $calibration = null;
        $gage = null;

        if ($gageId) {
            $gage = Gage::with(['status', 'type', 'location', 'unitMeasure', 'frequencyUnit', 'manufacturer'])->find($gageId);
            if ($gage) {
                $calibration = Calibration::with(['calibrationBy', 'foundCondition'])
                    ->where('gageId', $gageId)
                    ->where('isPassed', 1)
                    ->orderByDesc('dateCalibrated')
                    ->first();
            }
        }

        $defaultStatement = "The instrument identified above has been calibrated using standards traceable to the National Institute of Standards and Technology (NIST). This calibration was performed in accordance with established procedures and the instrument meets the specifications listed.";

        return view('reports.certifications', [
            'gage'             => $gage,
            'calibration'      => $calibration,
            'gages'            => Gage::orderBy('gageNumber')->get(),
            'selectedGageId'   => $gageId,
            'defaultStatement' => $defaultStatement,
        ]);
    }
}
