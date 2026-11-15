<?php

namespace App\Console\Commands;

use App\Services\JobFairAutoRegistrationService;
use Illuminate\Console\Command;

class SyncJobFairGraduates extends Command
{
    protected $signature = 'jobfair:sync-graduates {--fair= : Sync graduates only for a specific job fair ID}';

    protected $description = 'Automatically register all approved and active graduates in active career fairs and issue fair badges';

    public function handle(): int
    {
        $fairOption = $this->option('fair');
        $fairId = ($fairOption !== null && $fairOption !== '') ? (int) $fairOption : null;

        $stats = JobFairAutoRegistrationService::syncAllActiveFairs($fairId);

        $this->info("تمت مزامنة {$stats['fairs_processed']} معرض(ات) وإنشاء {$stats['registrations_created']} تسجيل/بطاقة جديدة.");

        return self::SUCCESS;
    }
}
