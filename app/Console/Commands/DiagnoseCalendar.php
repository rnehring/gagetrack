<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DiagnoseCalendar extends Command
{
    protected $signature   = 'gages:diagnose-calendar';
    protected $description = 'Dump raw data to diagnose why gages are not showing on the calendar';

    public function handle(): int
    {
        // 1. What statusId values exist and what do they map to?
        $this->info('=== STATUS METADATA ===');
        $statuses = DB::select("SELECT id, category, value FROM metadata WHERE category LIKE '%status%' ORDER BY id");
        $this->table(['id', 'category', 'value'], array_map(fn($r) => [$r->id, $r->category, $r->value], $statuses));

        // 2. Distribution of statusId values in gages table
        $this->info('=== GAGE statusId DISTRIBUTION ===');
        $dist = DB::select("SELECT statusId, COUNT(*) as cnt FROM gages WHERE isActive=1 GROUP BY statusId ORDER BY cnt DESC");
        $this->table(['statusId', 'count'], array_map(fn($r) => [$r->statusId, $r->cnt], $dist));

        // 3. Sample of dateDue values
        $this->info('=== SAMPLE dateDue VALUES (first 10 active gages) ===');
        $sample = DB::select("SELECT id, gageNumber, statusId, dateDue FROM gages WHERE isActive=1 ORDER BY dateDue DESC LIMIT 10");
        $this->table(['id', 'gageNumber', 'statusId', 'dateDue'], array_map(fn($r) => [$r->id, $r->gageNumber, $r->statusId, $r->dateDue], $sample));

        // 4. Run the exact overdue query from CalendarController
        $this->info('=== OVERDUE QUERY (statusId=1, dateDue < today, not 0000-00-00) ===');
        $overdue = DB::select("SELECT COUNT(*) as cnt FROM gages WHERE isActive=1 AND statusId=1 AND dateDue < CURDATE() AND dateDue != '0000-00-00'");
        $this->line('Count with statusId=1: ' . $overdue[0]->cnt);

        // 5. Try without statusId filter
        $overdueAny = DB::select("SELECT COUNT(*) as cnt FROM gages WHERE isActive=1 AND dateDue < CURDATE() AND dateDue != '0000-00-00'");
        $this->line('Count WITHOUT statusId filter: ' . $overdueAny[0]->cnt);

        // 6. dateDue range check
        $this->info('=== dateDue RANGE FOR ACTIVE GAGES ===');
        $range = DB::selectOne("SELECT MIN(dateDue) as min_due, MAX(dateDue) as max_due FROM gages WHERE isActive=1 AND dateDue != '0000-00-00'");
        $this->line('Min dateDue: ' . $range->min_due);
        $this->line('Max dateDue: ' . $range->max_due);

        // 7. How many fall in current year?
        $this->info('=== GAGES DUE IN ' . date('Y') . ' ===');
        $thisYear = DB::selectOne("SELECT COUNT(*) as cnt FROM gages WHERE isActive=1 AND YEAR(dateDue) = YEAR(CURDATE())");
        $this->line('Count: ' . $thisYear->cnt);

        return 0;
    }
}
