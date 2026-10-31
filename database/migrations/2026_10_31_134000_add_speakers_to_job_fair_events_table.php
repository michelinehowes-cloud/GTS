<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('job_fair_events') && !Schema::hasColumn('job_fair_events', 'speakers')) {
            Schema::table('job_fair_events', function (Blueprint $table) {
                $table->longText('speakers')->nullable()->after('speaker_image');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('job_fair_events') && Schema::hasColumn('job_fair_events', 'speakers')) {
            Schema::table('job_fair_events', function (Blueprint $table) {
                $table->dropColumn('speakers');
            });
        }
    }
};
