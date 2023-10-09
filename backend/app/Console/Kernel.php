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
        $schedule->command('shop:reset-amazing-offer')->everyFourMinutes();
        $schedule->command('shop:amazing-product')->everyMinute();
        $schedule->command('shop:notify-exists')->at('00:00');
        $schedule->command('shop:cancel-order')->cron('*/10 * * * *');
        $schedule->command('shop:pay-to-seller')->cron('0 * * * *');
        $schedule->command('queue:work --stop-when-empty')
            ->everyMinute()
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
