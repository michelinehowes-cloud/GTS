<?php

use App\Services\JobFairAutoRegistrationService;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Backfill registrations and badges for existing approved and active graduates
     * in all currently published/ongoing non-ended job fairs.
     */
    public function up(): void
    {
        JobFairAutoRegistrationService::syncAllActiveFairs();
    }

    /**
     * Do not delete registrations on rollback to preserve existing attendance and tickets.
     */
    public function down(): void
    {
        // Intentionally left empty.
    }
};
