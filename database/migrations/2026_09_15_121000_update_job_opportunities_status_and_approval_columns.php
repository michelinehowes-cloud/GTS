<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // تعديل عمود status ليدعم الحالات المتعددة كـ string
        try {
            DB::statement("ALTER TABLE job_opportunities MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'pending'");
        } catch (\Exception $e) {
            // في حالة كان SQLite أو بيئة مختلفة
        }

        Schema::table('job_opportunities', function (Blueprint $table) {
            if (!Schema::hasColumn('job_opportunities', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('status');
            }
            if (!Schema::hasColumn('job_opportunities', 'reviewed_by')) {
                $table->foreignId('reviewed_by')->nullable()->after('rejection_reason')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('job_opportunities', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_opportunities', function (Blueprint $table) {
            if (Schema::hasColumn('job_opportunities', 'reviewed_by')) {
                $table->dropForeign(['reviewed_by']);
                $table->dropColumn('reviewed_by');
            }
            if (Schema::hasColumn('job_opportunities', 'rejection_reason')) {
                $table->dropColumn('rejection_reason');
            }
            if (Schema::hasColumn('job_opportunities', 'reviewed_at')) {
                $table->dropColumn('reviewed_at');
            }
        });
    }
};
