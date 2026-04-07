<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportGages extends Command
{
    protected $signature = 'import:gages
                            {--path= : Path to Gage_Master.csv (default: gagetrack_old/import/Gage_Master.csv relative to project root)}
                            {--dry-run : Preview what would be inserted without writing to DB}
                            {--update-existing : Update gages that already exist (matched by gageNumber) instead of skipping}';

    protected $description = 'Import gages from the legacy Gage_Master.csv export file';

    // Delimiter used in the export
    const DELIM = '|';

    public function handle(): int
    {
        // ---------------------------------------------------------------
        // Resolve the CSV path
        // ---------------------------------------------------------------
        $csvPath = $this->option('path')
            ?? base_path('../../gagetrack_old/import/Gage_Master.csv');

        if (! file_exists($csvPath)) {
            $this->error("CSV file not found: {$csvPath}");
            $this->line('Use --path=/absolute/path/to/Gage_Master.csv');
            return 1;
        }

        $dryRun         = $this->option('dry-run');
        $updateExisting = $this->option('update-existing');

        if ($dryRun) {
            $this->warn('DRY RUN — nothing will be written to the database.');
        }

        // ---------------------------------------------------------------
        // Build metadata lookup tables (category → [id => lowercase_value])
        // ---------------------------------------------------------------
        $lookups = $this->buildLookups([
            'frequencyUnits',
            'locations',
            'manufacturers',
            'owners',
            'types',
            'unitMeasures',
        ]);

        $this->line('Metadata lookups loaded.');

        // ---------------------------------------------------------------
        // Parse the CSV
        // ---------------------------------------------------------------
        $fp = fopen($csvPath, 'r');
        if (! $fp) {
            $this->error("Cannot open: {$csvPath}");
            return 1;
        }

        // Skip header row
        fgetcsv($fp, 10000, self::DELIM);

        $inserted  = 0;
        $updated   = 0;
        $skipped   = 0;
        $errors    = [];
        $previewRows = [];

        while (($row = fgetcsv($fp, 10000, self::DELIM)) !== false) {
            // Skip blank rows
            if (empty($row[0])) {
                continue;
            }

            /*
             * Column mapping from Gage_Master.csv:
             * [0]  Gage_ID             → gageNumber
             * [1]  Gage_SN             → serialNumber
             * [3]  Model_No            → modelNumber
             * [4]  Manufacturer        → manufacturerId  (lookup)
             * [5]  GM_Owner            → ownerId         (lookup)
             * [6]  Description         → description
             * [7]  GM_Type             → typeId          (lookup)
             * [8]  Unit_of_Meas        → unitMeasureId   (lookup)
             * [14] Storage_Location    → locationId      (lookup; fallback to [15])
             * [15] Current_Location    → locationId      (if [14] empty)
             * [19] Calibration_Frequency → frequency
             * [20] Cal_Freq_UOM        → frequencyUnitId (lookup)
             * [34] Notes               → notes
             * [35] Status              → statusId        (direct integer)
             * [54] Nist_No             → nistNumber
             */

            $gageNumber   = trim($row[0]);
            $serialNumber = trim($row[1] ?? '');
            $modelNumber  = trim($row[3] ?? '');
            $description  = trim($row[6] ?? '');
            $frequency    = (int) trim($row[19] ?? 0);
            $notes        = trim($row[34] ?? '');
            $statusId     = (int) trim($row[35] ?? 0);
            $nistNumber   = trim($row[54] ?? '');

            // Metadata ID lookups (returns 0 if not found)
            $manufacturerId  = $this->lookupId($lookups['manufacturers'],  trim($row[4] ?? ''));
            $ownerId         = $this->lookupId($lookups['owners'],         trim($row[5] ?? ''));
            $typeId          = $this->lookupId($lookups['types'],          trim($row[7] ?? ''));
            $unitMeasureId   = $this->lookupId($lookups['unitMeasures'],   trim($row[8] ?? ''));
            $frequencyUnitId = $this->lookupId($lookups['frequencyUnits'], trim($row[20] ?? ''));

            // Location: prefer Current_Location [15]; fall back to Storage_Location [14]
            $locationRaw = ! empty(trim($row[15] ?? ''))
                ? trim($row[15])
                : trim($row[14] ?? '');
            $locationId  = $this->lookupId($lookups['locations'], $locationRaw);

            // isActive mirrors the legacy logic: statusId 1 or 7 → active
            $isActive = in_array($statusId, [1, 7]) ? 1 : 0;

            $record = [
                'gageNumber'      => $gageNumber,
                'serialNumber'    => $serialNumber,
                'modelNumber'     => $modelNumber,
                'description'     => $description,
                'frequency'       => $frequency ?: null,
                'frequencyUnitId' => $frequencyUnitId ?: null,
                'locationId'      => $locationId ?: null,
                'manufacturerId'  => $manufacturerId ?: null,
                'ownerId'         => $ownerId ?: null,
                'typeId'          => $typeId ?: null,
                'unitMeasureId'   => $unitMeasureId ?: null,
                'statusId'        => $statusId ?: null,
                'nistNumber'      => $nistNumber,
                'notes'           => $notes,
                'isActive'        => $isActive,
            ];

            if ($dryRun) {
                $previewRows[] = [
                    $gageNumber,
                    substr($description, 0, 30),
                    $locationId ?: '?',
                    $typeId ?: '?',
                    $statusId,
                ];
                $inserted++;
                continue;
            }

            // Check for existing record
            $existing = DB::table('gages')->where('gageNumber', $gageNumber)->first();

            if ($existing) {
                if ($updateExisting) {
                    DB::table('gages')->where('gageNumber', $gageNumber)->update($record);
                    $updated++;
                } else {
                    $skipped++;
                }
            } else {
                try {
                    DB::table('gages')->insert($record);
                    $inserted++;
                } catch (\Exception $e) {
                    $errors[] = "  [{$gageNumber}] " . $e->getMessage();
                }
            }
        }

        fclose($fp);

        // ---------------------------------------------------------------
        // Output results
        // ---------------------------------------------------------------
        if ($dryRun && ! empty($previewRows)) {
            $this->table(
                ['Gage Number', 'Description', 'Location ID', 'Type ID', 'Status ID'],
                $previewRows
            );
        }

        $this->newLine();
        if ($dryRun) {
            $this->info("Would insert: {$inserted} gages (dry run).");
        } else {
            $this->info("Inserted: {$inserted}");
            $this->line("Updated:  {$updated}");
            $this->line("Skipped:  {$skipped} (already exist; use --update-existing to overwrite)");
        }

        if (! empty($errors)) {
            $this->newLine();
            $this->error('Errors:');
            foreach ($errors as $err) {
                $this->line($err);
            }
        }

        $this->newLine();
        $this->info('Done! Run php artisan gages:recalculate-due-dates to set due dates from calibration history.');

        return 0;
    }

    // ---------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------

    /**
     * Build lookup arrays: category → [metadata_id => lowercase_value]
     */
    private function buildLookups(array $categories): array
    {
        $lookups = [];
        foreach ($categories as $cat) {
            $rows = DB::table('metadata')
                ->where('category', $cat)
                ->get(['id', 'value']);

            $lookups[$cat] = [];
            foreach ($rows as $row) {
                $lookups[$cat][$row->id] = strtolower($row->value);
            }
        }
        return $lookups;
    }

    /**
     * Search a lookup array for a value and return its ID (key), or 0 if not found.
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
}
