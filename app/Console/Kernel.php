<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Schedule auto-logout setiap menit untuk cek jam operasional.
     * Pastikan cron berjalan di server: * * * * * php artisan schedule:run
     */
    protected function schedule(Schedule $schedule): void
    {
        // Cek dan logout kasir di luar jam operasional, setiap menit
        $schedule->command('cyberprotect:auto-logout')->everyMinute();
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
