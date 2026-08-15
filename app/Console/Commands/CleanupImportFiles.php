<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupImportFiles extends Command
{
    protected $signature = 'imports:cleanup {--days=30 : Hapus file import lebih lama dari N hari}';

    protected $description = 'Hapus file import di storage yang sudah melebihi umur tertentu';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoff = now()->subDays($days)->timestamp;

        $files = Storage::disk('local')->files('imports');
        $deleted = 0;

        foreach ($files as $file) {
            $lastModified = Storage::disk('local')->lastModified($file);

            if ($lastModified < $cutoff) {
                Storage::disk('local')->delete($file);
                $deleted++;
            }
        }

        $this->info("Import cleanup selesai: {$deleted} file dihapus dari {$days} hari terakhir.");

        return self::SUCCESS;
    }
}