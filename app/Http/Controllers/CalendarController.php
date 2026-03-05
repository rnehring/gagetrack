<?php

namespace App\Http\Controllers;

use App\Models\Calibration;
use App\Models\Gage;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CalendarController extends Controller
{
    public function index(Request $request, int $year = null)
    {
        $year         = $year ?? (int) date('Y');
        $currentYear  = (int) date('Y');
        $currentMonth = (int) date('n');

        // Gages with a due date falling within the requested year
        $calendar = [];
        $yearGages = Gage::with(['frequencyUnit'])
            ->where('isActive', 1)
            ->whereRaw('YEAR(dateDue) = ?', [$year])
            ->orderBy('dateDue')
            ->get();

        foreach ($yearGages as $gage) {
            $month = (int) $gage->dateDue->format('n');
            $calendar[$month][] = $gage;
        }

        // Overdue gages — active + in-service (statusId=1) with a real past date
        $overdueGages = Gage::with(['frequencyUnit'])
            ->where('isActive', 1)
            ->where('statusId', 1)
            ->whereRaw("dateDue < CURDATE()")
            ->whereRaw("dateDue != '0000-00-00'")
            ->orderBy('dateDue')
            ->get();

        $overdueCount = $overdueGages->count();

        Log::debug('CalendarController', [
            'year'          => $year,
            'currentYear'   => $currentYear,
            'currentMonth'  => $currentMonth,
            'yearGageCount' => $yearGages->count(),
            'overdueCount'  => $overdueCount,
        ]);

        // Pin overdue gages to the current month when viewing the current or a future year
        if ($overdueGages->isNotEmpty() && $year >= $currentYear) {
            $pinMonth   = ($year == $currentYear) ? $currentMonth : 1;
            $pinned     = $calendar[$pinMonth] ?? [];
            $pinnedIds  = array_flip(array_map(fn($g) => $g->id, $pinned));

            foreach ($overdueGages as $gage) {
                if (! isset($pinnedIds[$gage->id])) {
                    $calendar[$pinMonth][] = $gage;
                }
            }

            Log::debug('Pinned overdue gages', [
                'pinMonth'  => $pinMonth,
                'pinCount'  => $overdueGages->count(),
                'calSlot'   => count($calendar[$pinMonth] ?? []),
            ]);
        }

        $totalGages       = Gage::where('isActive', 1)->count();
        $currentGages     = Gage::where('isActive', 1)
                               ->whereRaw("dateDue >= CURDATE()")
                               ->whereRaw("dateDue != '0000-00-00'")
                               ->count();
        $failedGages      = Gage::where('isActive', 1)
                               ->whereHas('calibrations', fn($q) => $q->where('isPassed', 0)
                                   ->whereRaw('id = (SELECT MAX(id) FROM calibrations c2 WHERE c2.gageId = calibrations.gageId)'))
                               ->count();
        $activeSuppliers  = Supplier::where('isActive', 1)->count();

        return view('calendar.index', compact(
            'calendar', 'year', 'overdueCount',
            'totalGages', 'currentGages', 'failedGages', 'activeSuppliers'
        ));
    }
}
