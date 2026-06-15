<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database';
    protected $description = 'Backup database file to backups directory with day-based naming';

    public function handle()
    {
        $dbPath = database_path('database.sqlite');
        $backupDir = storage_path('backups');

        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $dayName = strtolower(Carbon::now()->format('l'));
        $backupFile = "{$backupDir}/database-{$dayName}.sqlite";

        if (file_exists($dbPath)) {
            copy($dbPath, $backupFile);
            $this->info("✓ Database backed up to: database-{$dayName}.sqlite");
        } else {
            $this->error("✗ Database file not found at: {$dbPath}");
        }
    }
}
