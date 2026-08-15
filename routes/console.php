<?php

use App\Console\Commands\AutoPromoteStudents;
use App\Console\Commands\CalculateStudentSummaries;
use App\Console\Commands\CleanupImportFiles;
use App\Console\Commands\CleanupOldLogs;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(CleanupOldLogs::class, ['--days=30'])->dailyAt('02:00');

Schedule::command(CleanupImportFiles::class, ['--days=30'])->dailyAt('02:05');

Schedule::command('queue:prune-failed', ['--hours=168'])->dailyAt('02:10');

Schedule::command(CalculateStudentSummaries::class)->dailyAt('02:30');

Schedule::command(AutoPromoteStudents::class, ['--semester=Y1S2'])->weeklyOn(6, '03:00');
