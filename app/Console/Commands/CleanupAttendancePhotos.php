<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupAttendancePhotos extends Command
{
    protected $signature = 'attendance:cleanup-photos {--days=180}';
    protected $description = 'Delete attendance selfie photos older than --days days (default 180)';

    public function handle(): int
    {
        $cutoff = now()->subDays((int) $this->option('days'));
        $disk = Storage::disk('local');
        $deleted = 0;

        foreach ($disk->directories('attendance') as $dir) {
            $name = basename($dir);
            try {
                $dirDate = \Carbon\Carbon::parse($name);
            } catch (\Throwable $e) {
                continue;
            }
            if ($dirDate->lt($cutoff)) {
                $disk->deleteDirectory($dir);
                $deleted++;
                $this->info("Deleted {$dir}");
            }
        }
        $this->info("Done. {$deleted} day folders removed.");
        return Command::SUCCESS;
    }
}
