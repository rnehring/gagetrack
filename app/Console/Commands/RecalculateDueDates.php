<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RecalculateDueDates extends Command
{
    protected $signature   = 'gages:recalculate-due-dates {--dry-run : Show what would change without saving}';
    protected $description = 'Recalculate dateDue for all active gages based on their last calibration date + frequency';

    // Metadata IDs matching the legacy app
    const UNIT_DAYS   = 716;
    const UNIT_MONTHS = 718;
    const UNIT_YEARS  = 720;

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('DRY RUN — no changes will be saved.');
        }

        // Replicate the exact query from the old app's NextDueDates() / dateDue.php
        $gages = DB::select("
            SELECT
                gages.id,
                gages.gageNumber,
                gages.dateDue                                                AS currentDue,
                gages.frequency                                              AS gageFrequency,
                gages.frequencyUnitId                                        AS gageFrequencyUnitId,
                ( SELECT dateCalibrated
                    FROM calibrations
                   WHERE gageId = gages.id
                ORDER BY dateCalibrated DESC
                   LIMIT 1 )                                                 AS lastCalibrated,
                ( SELECT frequency
                    FROM calibrations
                   WHERE gageId = gages.id
                ORDER BY dateCalibrated DESC
                   LIMIT 1 )                                                 AS calFrequency,
                ( SELECT frequencyUnitId
                    FROM calibrations
                   WHERE gageId = gages.id
                ORDER BY dateCalibrated DESC
                   LIMIT 1 )                                                 AS calFrequencyUnitId
            FROM gages
            WHERE gages.isActive = 1
              AND gages.frequencyUnitId IN (?, ?, ?)
              AND gages.statusId = 1
            ORDER BY lastCalibrated
        ", [self::UNIT_DAYS, self::UNIT_MONTHS, self::UNIT_YEARS]);

        if (empty($gages)) {
            $this->info('No eligible gages found.');
            return 0;
        }

        $updated  = 0;
        $skipped  = 0;
        $noCalib  = 0;
        $rows     = [];

        foreach ($gages as $gage) {
            // No calibration history — skip
            if (! $gage->lastCalibrated) {
                $noCalib++;
                continue;
            }

            // Prefer the calibration's own frequency/unit; fall back to the gage's
            $frequency     = $gage->calFrequency     ?: $gage->gageFrequency;
            $frequencyUnit = $gage->calFrequencyUnitId ?: $gage->gageFrequencyUnitId;

            if (! $frequency || ! in_array((int) $frequencyUnit, [self::UNIT_DAYS, self::UNIT_MONTHS, self::UNIT_YEARS])) {
                $skipped++;
                continue;
            }

            $unit = match ((int) $frequencyUnit) {
                self::UNIT_DAYS   => 'days',
                self::UNIT_MONTHS => 'months',
                self::UNIT_YEARS  => 'years',
            };

            $newDue = date('Y-m-d', strtotime("+{$frequency} {$unit}", strtotime($gage->lastCalibrated)));

            $rows[] = [
                $gage->gageNumber,
                $gage->lastCalibrated,
                "{$frequency} {$unit}",
                $gage->currentDue ?? '—',
                $newDue,
            ];

            if (! $dryRun) {
                DB::update('UPDATE gages SET dateDue = ? WHERE id = ?', [$newDue, $gage->id]);
            }

            $updated++;
        }

        if (! empty($rows)) {
            $this->table(
                ['Gage', 'Last Calibrated', 'Frequency', 'Old Due', 'New Due'],
                $rows
            );
        }

        $this->newLine();
        $this->info("Updated : {$updated}");
        $this->line("Skipped (no frequency) : {$skipped}");
        $this->line("Skipped (no calibrations): {$noCalib}");

        if ($dryRun) {
            $this->warn('Dry run complete — run without --dry-run to apply changes.');
        }

        return 0;
    }
}
