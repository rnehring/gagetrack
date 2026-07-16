<?php

namespace App\Http\Controllers;

use App\Models\Calibration;
use App\Models\Gage;
use App\Models\Supplier;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index(Request $request, int $year = null)
    {
        $year = $year ?? (int) date('Y');

        // Gages with a due date falling within the requested year.
        //
        // A month bucket means exactly one thing: "due in this month of this year".
        // This mirrors the legacy app (controllers/calendar.php), which selects on
        // isActive=1 AND YEAR(dateDue) = $year and buckets by MONTH(dateDue).
        // Nothing else may be merged into these buckets — doing so makes the per-month
        // counts incomparable to the legacy system and meaningless on their own terms.
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

        // Overdue backlog — every active, in-service gage whose due date has passed,
        // regardless of year. This is NOT calendar data: it is a standing worklist that
        // is displayed in its own panel above the grid.
        //
        // The filter deliberately matches ReportController::backlog() exactly, so the
        // count shown here can never disagree with the backlog report.
        $overdueGages = Gage::with(['frequencyUnit', 'location'])
            ->where('isActive', 1)
            ->where('statusId', 1)
            ->whereRaw("dateDue < CURDATE()")
            ->whereRaw("dateDue != '0000-00-00'")
            ->orderBy('dateDue')
            ->get();

        $overdueCount = $overdueGages->count();

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
            'calendar', 'year', 'overdueGages', 'overdueCount',
            'totalGages', 'currentGages', 'failedGages', 'activeSuppliers'
        ));
    }
}
