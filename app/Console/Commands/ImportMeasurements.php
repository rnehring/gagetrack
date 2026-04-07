<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * ImportMeasurements
 *
 * Imports the Calibration_Measurement.csv file, which contains detailed
 * per-measurement rows for each calibration event.
 *
 * IMPORTANT: Run this AFTER import:calibrations so the calibrations table
 * is populated and calibrationId can be resolved.
 *
 * Usage:
 *   php artisan import:measurements
 *   php artisan import:measurements --dry-run
 *   php artisan import:measurements --path=/absolute/path/to/Calibration_Measurement.csv
 */
class ImportMeasurements extends Command
{
    protected $signature = 'import:measurements
                            {--path= : Path to Calibration_Measurement.csv}
                            {--dry-run : Preview without writing to DB}';

    protected $description = 'Import calibration measurements from legacy Calibration_Measurement.csv';

    const DELIM = '|';

    // Excel null date artifact — rows with this time value have no real time
    const EXCEL_NULL_DATE = '12/30/1899';

    public function handle(): int
    {
        $csvPath = $this->option('path')
            ?? base_path('../../gagetrack_old/import/Calibration_Measurement.csv');

        if (! file_exists($csvPath)) {
            $this->error("CSV not found: {$csvPath}");
            $this->line('Use --path=/absolute/path/to/Calibration_Measurement.csv');
            return 1;
        }

        $dryRun = $this->option('dry-run');
        if ($dryRun) {
            $this->warn('DRY RUN — nothing will be written.');
        }

        // ---------------------------------------------------------------
        // Build lookup: "gageNumber_YYYY-MM-DD" => calibration_id
        // ---------------------------------------------------------------
        $this->line('Building calibration lookup...');
        $calibrations = DB::table('calibrations')
            ->join('gages', 'calibrations.gageId', '=', 'gages.id')
            ->select('calibrations.id as calId', 'gages.gageNumber', 'calibrations.gageId',
                     DB::raw('DATE(calibrations.dateCalibrated) as calDate'))
            ->get();

        $calLookup   = [];  // "gageNumber|YYYY-MM-DD" => calibration id
        $gageIdLookup = []; // gageNumber => gageId
        foreach ($calibrations as $c) {
            $key = strtolower($c->gageNumber) . '|' . $c->calDate;
            $calLookup[$key] = (int) $c->calId;
            $gageIdLookup[strtolower($c->gageNumber)] = (int) $c->gageId;
        }

        $this->line(count($calLookup) . ' calibration records indexed.');

        // ---------------------------------------------------------------
        // Parse CSV
        // ---------------------------------------------------------------
        $fp = fopen($csvPath, 'r');
        if (! $fp) {
            $this->error("Cannot open: {$csvPath}");
            return 1;
        }

        // Skip header
        fgetcsv($fp, 10000, self::DELIM);

        $inserted      = 0;
        $noCalibration = 0;
        $errors        = [];
        $previewRows   = [];

        while (($row = fgetcsv($fp, 10000, self::DELIM)) !== false) {
            if (empty($row[0])) {
                continue;
            }

            $gageNumberRaw = trim($row[0]);
            $dateRaw       = trim($row[1] ?? '');
            $timeRaw       = trim($row[2] ?? '');

            // Parse the calibration date
            $calibrationDate = null;
            if (! empty($dateRaw) && $dateRaw !== self::EXCEL_NULL_DATE) {
                $ts = strtotime($dateRaw);
                if ($ts) {
                    $calibrationDate = date('Y-m-d', $ts);
                }
            }

            // Resolve calibration ID
            $lookupKey     = strtolower($gageNumberRaw) . '|' . $calibrationDate;
            $calibrationId = $calLookup[$lookupKey] ?? null;
            $gageId        = $gageIdLookup[strtolower($gageNumberRaw)] ?? null;

            if (! $calibrationId) {
                $noCalibration++;
                // Not an error — some measurements may predate the calibration data
            }

            // Helper to parse numeric fields safely
            $toDecimal = function (string $val): ?float {
                $val = trim($val);
                if ($val === '' || $val === null) {
                    return null;
                }
                return is_numeric($val) ? (float) $val : null;
            };

            $record = [
                'calibrationId'         => $calibrationId,
                'gageId'                => $gageId,
                'calibrationDate'       => $calibrationDate,
                'standardUsed'          => substr(trim($row[3] ?? ''), 0, 100),
                'measurementBefore'     => $toDecimal($row[4] ?? ''),
                'measurementAfter'      => $toDecimal($row[5] ?? ''),
                'limitMin'              => $toDecimal($row[6] ?? ''),
                'nominal'               => $toDecimal($row[7] ?? ''),
                'limitMax'              => $toDecimal($row[8] ?? ''),
                'uncertainty'           => $toDecimal($row[9] ?? ''),
                'calibrationStandardGage' => substr(trim($row[10] ?? ''), 0, 50),
                // [11] LimitUse - skipped (mostly 0)
                'units'                 => substr(trim($row[12] ?? ''), 0, 30),
                'measurementType'       => substr(trim($row[13] ?? ''), 0, 20),
                'gageType'              => substr(trim($row[14] ?? ''), 0, 100),
                'comments'              => trim($row[15] ?? ''),
                'format'                => substr(trim($row[16] ?? ''), 0, 50),
            ];

            if ($dryRun) {
                $previewRows[] = [
                    $gageNumberRaw,
                    $calibrationDate ?? '(no date)',
                    $calibrationId ? "Cal #{$calibrationId}" : '(no match)',
                    $record['standardUsed'],
                    $record['measurementBefore'] ?? '-',
                    $record['measurementAfter'] ?? '-',
                ];
                $inserted++;
                continue;
            }

            try {
                DB::table('calibration_measurements')->insert($record);
                $inserted++;
            } catch (\Exception $e) {
                $errors[] = "  [{$gageNumberRaw} @ {$calibrationDate}] " . $e->getMessage();
            }
        }

        fclose($fp);

        // ---------------------------------------------------------------
        // Output
        // ---------------------------------------------------------------
        if ($dryRun && ! empty($previewRows)) {
            $this->table(
                ['Gage', 'Date', 'Calibration', 'Standard Used', 'Before', 'After'],
                array_slice($previewRows, 0, 30)
            );
            if (count($previewRows) > 30) {
                $this->line('... (showing first 30 of ' . count($previewRows) . ' rows)');
            }
        }

        $this->newLine();
        if ($dryRun) {
            $this->info("Would insert: {$inserted} measurement rows (dry run).");
            $this->line("Rows without matching calibration: {$noCalibration} (still inserted with null calibrationId)");
        } else {
            $this->info("Inserted: {$inserted}");
            $this->line("Without matching calibration (null calibrationId): {$noCalibration}");
        }

        if (! empty($errors)) {
            $this->newLine();
            $this->error('Errors:');
            foreach (array_slice($errors, 0, 20) as $err) {
                $this->line($err);
            }
        }

        return 0;
    }
}
