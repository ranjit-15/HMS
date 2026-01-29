<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('bookings:expire')->everyMinute();
        $schedule->command('notifications:due-today')->dailyAt('07:00');
        $schedule->command('bookings:cleanup')->everyFiveMinutes();
        $schedule->command('books:due-reminders')->dailyAt('08:00'); // Send 3-day warning and overdue reminders
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
