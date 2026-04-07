<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CopyCertificatePdfs extends Command
{
    protected $signature = 'import:certificates
                            {--source= : Source directory containing the legacy PDFs (default: gagetrack_old/files)}
                            {--dry-run : List files that would be copied without actually copying}';

    protected $description = 'Copy legacy certificate PDFs from the old app\'s files/ directory into storage/app/certificates/';

    public function handle(): int
    {
        $sourceDir = $this->option('source')
            ?? base_path('../../gagetrack_old/files');

        $destDir = storage_path('app/certificates');

        if (! is_dir($sourceDir)) {
            $this->error("Source directory not found: {$sourceDir}");
            $this->line('Use --source=/absolute/path/to/files');
            return 1;
        }

        $dryRun = $this->option('dry-run');

        if (! $dryRun && ! is_dir($destDir)) {
            mkdir($destDir, 0755, true);
            $this->line("Created: {$destDir}");
        }

        $pdfs = glob($sourceDir . '/*.pdf');

        if (empty($pdfs)) {
            $this->warn('No PDF files found in source directory.');
            return 0;
        }

        $copied   = 0;
        $skipped  = 0;
        $overwrite = 0;

        foreach ($pdfs as $src) {
            $filename = basename($src);
            $dest     = $destDir . '/' . $filename;

            if ($dryRun) {
                $status = file_exists($dest) ? '[overwrite]' : '[new]';
                $this->line("{$status} {$filename}");
                $copied++;
                continue;
            }

            if (file_exists($dest)) {
                // Overwrite if source is newer
                if (filemtime($src) > filemtime($dest)) {
                    copy($src, $dest);
                    $overwrite++;
                } else {
                    $skipped++;
                }
            } else {
                copy($src, $dest);
                $copied++;
            }
        }

        $this->newLine();
        if ($dryRun) {
            $this->info("Would process: {$copied} PDF files (dry run).");
        } else {
            $this->info("Copied (new):      {$copied}");
            $this->line("Overwritten:       {$overwrite}");
            $this->line("Skipped (current): {$skipped}");
            $this->line("Destination:       {$destDir}");
        }

        return 0;
    }
}
