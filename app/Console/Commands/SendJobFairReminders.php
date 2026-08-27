<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\JobFair;
use App\Models\JobFairRegistration;
use Illuminate\Support\Facades\Mail;
use App\Mail\JobFairReminderMail;
use Carbon\Carbon;

class SendJobFairReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jobfair:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'إرسال تذكيرات للخريجين المسجلين في المعارض التي تبدأ غداً';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $tomorrow = Carbon::tomorrow()->toDateString();
        
        $fairs = JobFair::where('status', 'published')
            ->whereDate('event_date', $tomorrow)
            ->get();

        if ($fairs->isEmpty()) {
            $this->info('لا توجد معارض توظيف غداً.');
            return Command::SUCCESS;
        }

        foreach ($fairs as $fair) {
            $registrations = JobFairRegistration::where('job_fair_id', $fair->id)
                ->with('graduate')
                ->get();

            $count = 0;
            foreach ($registrations as $reg) {
                if ($reg->graduate && $reg->graduate->email) {
                    Mail::to($reg->graduate->email)->send(new JobFairReminderMail($fair, $reg));
                    $count++;
                }
            }

            $this->info("تم إرسال {$count} رسالة تذكير لمعرض: {$fair->title}");
        }

        return Command::SUCCESS;
    }
}
