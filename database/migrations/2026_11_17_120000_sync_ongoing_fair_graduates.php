<?php

use App\Services\JobFairAutoRegistrationService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users')) {
            DB::table('users')
                ->where('role', 'graduate')
                ->where('email', 'like', 'vis_%@visitor.gto.com.ly')
                ->update(['role' => 'visitor']);
        }

        if (Schema::hasTable('graduates_data') && Schema::hasTable('users')) {
            $visitorUserIds = DB::table('users')->where('role', 'visitor')->pluck('id');
            if ($visitorUserIds->isNotEmpty()) {
                DB::table('graduates_data')
                    ->whereIn('user_id', $visitorUserIds)
                    ->update(['is_active' => false]);
            }
        }

        JobFairAutoRegistrationService::syncAllActiveFairs();
    }

    public function down(): void
    {
        // Non-destructive data sync migration
    }
};
