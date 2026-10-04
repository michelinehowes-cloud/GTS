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
        // إرسال تذكيرات معارض التوظيف التي تبدأ غداً (يومياً الساعة 10 صباحاً)
        $schedule->command('jobfair:send-reminders')->dailyAt('10:00');

        // أخذ نسخة احتياطية آلية شاملة من قاعدة البيانات يومياً الساعة 2:00 فجراً والاحتفاظ بآخر 14 نسخة
        $schedule->command('backup:database --keep=14')->dailyAt('02:00');
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
