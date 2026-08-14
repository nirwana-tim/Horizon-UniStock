<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CleanupOldLogs extends Command
{
    protected $signature = 'logs:cleanup {--days=30 : Hapus backup log lebih lama dari N hari}';

    protected $description = 'Rotate dan bersihkan laravel.log';

    public function handle(): int
    {
        $days = (int) $this->option('days');

        $this->rotateLogs($days);

        return self::SUCCESS;
    }

    private function rotateLogs(int $days): void
    {
        $logPath = storage_path('logs/laravel.log');

        if (File::exists($logPath) && File::size($logPath) > 0) {
            $backupPath = $logPath . '.' . now()->format('Y-m-d-His');
            File::copy($logPath, $backupPath);
            File::put($logPath, '');
            $this->info("Rotated laravel.log to " . basename($backupPath));
        }

        $cutoff = now()->subDays($days)->timestamp;
        $oldLogs = glob(storage_path('logs/laravel.log.*'));

        foreach ($oldLogs as $log) {
            if (filemtime($log) < $cutoff) {
                File::delete($log);
                $this->info("Deleted old log: " . basename($log));
            }
        }
    }
}
