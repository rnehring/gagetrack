<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportCalibrations extends Command
{
    protected $signature = 'import:calibrations
                            {--path= : Path to Calibration_Header.csv (default: gagetrack_old/import/Calibration_Header.csv)}
                            {--dry-run : Preview what would be inserted without writing to DB}
                            {--skip-existing : Skip calibrations whose gageId+dateCalibrated already exist (default: allow dupes)}';

    protected $description = 'Import calibrations from the legacy Calibration_Header.csv export file';

    const DELIM = '|';

    /*
     * The legacy importCalibrations.php hard-coded this mapping for foundConditionId:
     *   ResultCode 1 → metadata id 670  (Acceptable / Pass)
     *   ResultCode 2 → metadata id 671  (Marginal)
     *   ResultCode 3 → metadata id 672  (Fail)
     *
     * NOTE: These IDs must match what is actually in your metadata table!
     * Run:  SELECT id, value FROM metadata WHERE category = 'foundConditions';
     * and adjust the mapping below if your IDs differ.
     */
    const FOUND_CONDITION_MAP = [
        1 => 670,   // ResultCode 1 → foundConditions id 670
        2 => 671,
        3 => 672,
    ];

    /*
     * FrequencyUnit IDs that represent calendar-based intervals
     * (used to trigger due-date updates on the parent gage).
     */
    const TIME_UNIT_IDS = [716, 718, 720];

    public function handle(): int
    {
        // ---------------------------------------------------------------
        // Resolve path
        // ---------------------------------------------------------------
        $csvPath = $this->option('path')
            ?? base_path('../../gagetrack_old/import/Calibration_Header.csv');

        if (! file_exists($csvPath)) {
            $this->error("CSV not found: {$csvPath}");
            $this->line('Use --path=/absolute/path/to/Calibration_Header.csv');
            return 1;
        }

        $dryRun       = $this->option('dry-run');
        $skipExisting = $this->option('skip-existing');

        if ($dryRun) {
            $this->warn('DRY RUN — nothing will be written.');
        }

        // ---------------------------------------------------------------
        // Build lookup tables
        // ---------------------------------------------------------------
        $calibrationBys      = $this->buildLookup('calibrationBys');
        $calibrationStatuses = $this->buildLookup('calibrationStatuses');
        $calibrationTypes    = $this->buildLookup('calibrationTypes');
        $frequencyUnits      = $this->buildLookup('frequencyUnits');

        // Gages lookup: gageNumber → id
        $gagesRaw  = DB::table('gages')->get(['id', 'gageNumber']);
        $gagesMap  = [];
        foreach ($gagesRaw as $g) {
            $gagesMap[strtolower($g->gageNumber)] = (int) $g->id;
        }

        $this->line(sprintf(
            'Lookups ready. %d gages, %d calibrationBys, %d statuses, %d types, %d freqUnits',
            count($gagesMap),
            count($calibrationBys),
            count($calibrationStatuses),
            count($calibrationTypes),
            count($frequencyUnits)
        ));

        // ---------------------------------------------------------------
        // Parse CSV
        // ---------------------------------------------------------------
        $fp = fopen($csvPath, 'r');
        if (! $fp) {
            $this->error("Cannot open: {$csvPath}");
            return 1;
        }

        // Skip header row
        fgetcsv($fp, 10000, self::DELIM);

        $inserted    = 0;
        $skipped     = 0;
        $noGage      = 0;
        $errors      = [];
        $previewRows = [];

        while (($row = fgetcsv($fp, 10000, self::DELIM)) !== false) {
            if (empty($row[0])) {
                continue;
            }

            /*
             * Column mapping from Calibration_Header.csv:
             * [0]  Gage_ID              → gageId          (lookup by gageNumber)
             * [1]  Calibration_Date     → dateCalibrated  (date part)
             * [2]  Calibration_Time     → dateCalibrated  (time part)
             * [3]  Calibration_Type     → calibrationTypeId   (lookup)
             * [4]  Calibration_By       → calibrationById     (lookup)
             * [6]  Results              → results
             * [7]  Action_Required      → actionRequired
             * [8]  Approved             → isPassed         (-1 or 1 means passed; 0 means failed)
             * [9]  Findings             → findings
             * [13] ResultCode           → foundConditionId (mapped via FOUND_CONDITION_MAP)
             * [15] CertNo               → certificateNumber
             * [16] Time_Required        → timeRequired     (decimal hours → HH:MM)
             * [19] CalFrequency         → frequency
             * [20] CalFrequency_UOM     → frequencyUnitId  (lookup)
             * [24] Temperature          → temperature
             * [25] Humidity             → humidity
             * [28] CalibType            → calibrationStatusId (lookup; contains "Passed", "Failed", etc.)
             */

            $gageNumberRaw = trim($row[0]);
            $gageId        = $gagesMap[strtolower($gageNumberRaw)] ?? 0;

            if (! $gageId) {
                $noGage++;
                $errors[] = "  No gage found for Gage_ID: {$gageNumberRaw}";
                continue;
            }

            // Date + time combined
            $dateStr = trim($row[1] ?? '');
            $timeStr = trim($row[2] ?? '');
            $dateCalibrated = '';
            if (! empty($dateStr)) {
                $ts = strtotime($dateStr . ' ' . $timeStr);
                $dateCalibrated = date('Y-m-d H:i:s', $ts ?: strtotime($dateStr));
            }

            // Approved / isPassed: legacy stored -1 for "approved" and 0 for "not"
            $approvedRaw = trim($row[8] ?? '0');
            $isPassed    = ($approvedRaw != 0) ? 1 : 0;

            // ResultCode → foundConditionId
            $resultCode      = (int) trim($row[13] ?? 0);
            $foundConditionId = self::FOUND_CONDITION_MAP[$resultCode] ?? null;

            // Time required: legacy stored as decimal hours, convert to HH:MM
            $timeRequired = $this->decimalToTime(trim($row[16] ?? '0'));

            // Frequency
            $frequency    = (int) trim($row[19] ?? 0);
            $freqUnitRaw  = trim($row[20] ?? '');
            $frequencyUnitId = $this->lookupId($frequencyUnits, $freqUnitRaw);

            // Metadata ID lookups
            $calibrationTypeId   = $this->lookupId($calibrationTypes,    trim($row[3] ?? ''));
            $calibrationById     = $this->lookupId($calibrationBys,      trim($row[4] ?? ''));
            $calibrationStatusId = $this->lookupId($calibrationStatuses, trim($row[28] ?? ''));

            $record = [
                'gageId'              => $gageId,
                'dateCalibrated'      => $dateCalibrated ?: null,
                'calibrationById'     => $calibrationById ?: null,
                'calibrationTypeId'   => $calibrationTypeId ?: null,
                'calibrationStatusId' => $calibrationStatusId ?: null,
                'foundConditionId'    => $foundConditionId ?: null,
                'results'             => trim($row[6] ?? ''),
                'actionRequired'      => trim($row[7] ?? ''),
                'findings'            => trim($row[9] ?? ''),
                'certificateNumber'   => trim($row[15] ?? ''),
                'timeRequired'        => $timeRequired,
                'frequency'           => $frequency ?: null,
                'frequencyUnitId'     => $frequencyUnitId ?: null,
                'temperature'         => trim($row[24] ?? ''),
                'humidity'            => trim($row[25] ?? ''),
                'isPassed'            => $isPassed,
            ];

            if ($dryRun) {
                $previewRows[] = [
                    $gageNumberRaw,
                    $dateCalibrated ?: '(no date)',
                    $isPassed ? 'Pass' : 'Fail',
                    trim($row[4] ?? ''),
                    substr(trim($row[9] ?? ''), 0, 40),
                ];
                $inserted++;
                continue;
            }

            // Optional deduplication
            if ($skipExisting && $dateCalibrated) {
                $exists = DB::table('calibrations')
                    ->where('gageId', $gageId)
                    ->where('dateCalibrated', $dateCalibrated)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }
            }

            try {
                $calId = DB::table('calibrations')->insertGetId($record);
                $inserted++;

                // Update gage due date if this is a time-based frequency calibration
                if ($isPassed && $frequency && in_array($frequencyUnitId, self::TIME_UNIT_IDS)) {
                    $unit = match ((int) $frequencyUnitId) {
                        716 => 'days',
                        718 => 'months',
                        720 => 'years',
                    };
                    $dateDue = date('Y-m-d', strtotime("+{$frequency} {$unit}", strtotime($dateCalibrated)));
                    DB::table('gages')->where('id', $gageId)->update(['dateDue' => $dateDue]);
                }
            } catch (\Exception $e) {
                $errors[] = "  [{$gageNumberRaw} @ {$dateCalibrated}] " . $e->getMessage();
            }
        }

        fclose($fp);

        // ---------------------------------------------------------------
        // Output
        // ---------------------------------------------------------------
        if ($dryRun && ! empty($previewRows)) {
            $this->table(
                ['Gage Number', 'Date Calibrated', 'Pass/Fail', 'Calibrated By', 'Findings (40 chars)'],
                $previewRows
            );
        }

        $this->newLine();
        if ($dryRun) {
            $this->info("Would insert: {$inserted} calibrations (dry run).");
            $this->line("Gages not found in DB: {$noGage}");
        } else {
            $this->info("Inserted:          {$inserted}");
            $this->line("Skipped (dupes):   {$skipped}");
            $this->line("Skipped (no gage): {$noGage}");
        }

        if (! empty($errors)) {
            $this->newLine();
            $this->warn('Issues encountered (first 20):');
            foreach (array_slice($errors, 0, 20) as $err) {
                $this->line($err);
            }
            if (count($errors) > 20) {
                $this->line('... and ' . (count($errors) - 20) . ' more.');
            }
        }

        if (! $dryRun) {
            $this->newLine();
            $this->info('Tip: Run php artisan gages:recalculate-due-dates to reconcile all due dates from the full history.');
        }

        return 0;
    }

    // ---------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------

    /**
     * Build a lookup: [metadata_id => lowercase_value] for a given category.
     */
    private function buildLookup(string $category): array
    {
        $rows = DB::table('metadata')
            ->where('category', $category)
            ->get(['id', 'value']);

        $map = [];
        foreach ($rows as $row) {
            $map[$row->id] = strtolower($row->value);
        }
        return $map;
    }

    /**
     * Search a [id => lowercase_value] lookup for a value, return its ID or 0.
     */
    private function lookupId(array $lookup, string $value): int
    {
        if (empty($value)) {
            return 0;
        }
        $needle = strtolower(trim($value));
        $key    = array_search($needle, $lookup, true);
        return ($key !== false) ? (int) $key : 0;
    }

    /**
     * Convert a decimal hour value to HH:MM string.
     * Mirrors the legacy decimal_to_time() function.
     *
     * The legacy function does:
     *   $hours   = floor((int)$decimal % 60);   ← note: modulo 60, not the whole hours
     *   $minutes = round(($decimal - (int)$decimal) * 60);
     *
     * This keeps the same logic to match legacy data exactly.
     */
    private function decimalToTime(string $decimal): string
    {
        if (! is_numeric($decimal) || (float) $decimal == 0) {
            return '00:00';
        }
        $hours   = (int) $decimal % 60;
        $minutes = round(((float) $decimal - (int) $decimal) * 60);
        return str_pad((string) $hours, 2, '0', STR_PAD_LEFT)
             . ':'
             . str_pad((string) $minutes, 2, '0', STR_PAD_LEFT);
    }
}
