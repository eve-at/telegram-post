<?php

namespace App\Console;

use App\Console\Commands\AutoSchedule;
use App\Console\Commands\PublishMessages;
use App\Console\Commands\UnpublishMessages;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command(PublishMessages::class)->everyMinute();
        $schedule->command(UnpublishMessages::class)->everyMinute();
        $schedule->command(AutoSchedule::class)->hourlyAt(8);
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
