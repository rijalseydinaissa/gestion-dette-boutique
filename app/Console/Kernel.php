<?php

namespace App\Console;

use App\Jobs\TwilioJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\ArchiveJob;
use App\Jobs\SendWeeklyDebtReminder;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->job(new TwilioJob)->weekly()->fridays()->at('14:00');
        $schedule->command('sms:send-reminders')->everyFiveMinutes();
        $schedule->job(new SendWeeklyDebtReminder)->weekly();
        //  $schedule->job(new ArchiveJob())->dailyAt('23:59');
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
