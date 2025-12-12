<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();

        // TAMBAHKAN BARIS INI UNTUK MENJALANKAN PERINTAH NOTIFIKASI SETIAP HARI
        $schedule->command('project:send-due-date-reminders')->dailyAt('08:00');
        
        // Auto-cleanup notifikasi yang lebih dari 7 hari
        $schedule->command('notifications:cleanup')->dailyAt('02:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
