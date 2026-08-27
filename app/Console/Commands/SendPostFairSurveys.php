<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\JobFair;
use App\Models\JobFairRegistration;
use App\Models\JobFairCompany;
use App\Services\NotificationService;
use Carbon\Carbon;

class SendPostFairSurveys extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jobfair:send-surveys';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send post-fair surveys to attendees and companies after the job fair completes.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(NotificationService $notificationService)
    {
        // Find completed job fairs where surveys haven't been sent yet
        // For simplicity, we just look at fairs that ended in the past 24 hours
        $fairs = JobFair::where('status', 'completed')
            ->whereDate('updated_at', '>=', Carbon::now()->subDays(1))
            ->whereNotNull('survey_id')
            ->get();

        $count = 0;

        foreach ($fairs as $fair) {
            $this->info("Sending surveys for fair: {$fair->title}");

            // 1. Send to Graduates who attended
            $attendedGraduates = JobFairRegistration::where('job_fair_id', $fair->id)
                ->where('status', 'attended')
                ->with('graduate')
                ->get();

            foreach ($attendedGraduates as $registration) {
                if ($registration->graduate) {
                    $notificationService->sendNotification(
                        $registration->graduate,
                        'تقييم معرض التوظيف',
                        "شكراً لحضورك معرض {$fair->title}. نرجو منك تقييم المعرض لمساعدتنا على تحسين المعارض القادمة.",
                        'system',
                        route('surveys.show', $fair->survey_id)
                    );
                    $count++;
                }
            }

            // 2. Send to Companies who participated
            $participatingCompanies = JobFairCompany::where('job_fair_id', $fair->id)
                ->with('company.user') // Assuming Company model has a user relationship
                ->get();

            foreach ($participatingCompanies as $pc) {
                if ($pc->company && $pc->company->user) {
                    $notificationService->sendNotification(
                        $pc->company->user,
                        'تقييم معرض التوظيف (للشركات)',
                        "شكراً لمشاركة شركتكم في معرض {$fair->title}. نرجو منكم تقييم المعرض.",
                        'system',
                        route('surveys.show', $fair->survey_id)
                    );
                    $count++;
                }
            }
        }

        $this->info("Successfully sent {$count} survey notifications.");
        return 0;
    }
}
