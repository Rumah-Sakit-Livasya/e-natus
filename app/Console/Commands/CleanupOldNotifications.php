<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupOldNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:cleanup {--days=7 : Number of days to keep notifications}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete notifications older than specified days (default: 7 days)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        
        $this->info("Cleaning up notifications older than {$days} days...");

        $cutoffDate = now()->subDays($days);
        
        $deletedCount = DB::table('notifications')
            ->where('created_at', '<', $cutoffDate)
            ->delete();

        $this->info("Deleted {$deletedCount} old notification(s).");

        return Command::SUCCESS;
    }
}
