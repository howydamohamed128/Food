<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('app:reminder-customer-that-reservation-time-is-closest-command')->hourly();
        $schedule->command('app:generate-monthly-salaries')->monthlyOn(1, '01:00');
        $schedule->command('app:cancel-unpaid-order-after-allowed-time')->hourly();
    }

    /**
     * Register the commands for the application.
     */

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
