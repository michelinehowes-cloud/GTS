<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // إرسال تذكيرات معارض التوظيف التي تبدأ غداً (يومياً الساعة 09:30 صباحاً بتوقيت طرابلس)
        $schedule->command('jobfair:send-reminders')->dailyAt('09:30')->timezone('Africa/Tripoli');

        // أخذ نسخة احتياطية آلية مرتين يومياً: الساعة 10:00 صباحاً و 2:30 ظهراً (14:30) بتوقيت طرابلس
        $schedule->command('backup:database --keep=20')->dailyAt('10:00')->timezone('Africa/Tripoli');
        $schedule->command('backup:database --keep=20')->dailyAt('14:30')->timezone('Africa/Tripoli');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
