<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * توسعة الأعمدة وتحويل حقول ENUM المقيدة في MySQL إلى VARCHAR/TEXT
     * لمنع أخطاء 500 (Data truncated / Data too long) في نماذج الخريجين ومعرض التوظيف.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if (!in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        $statements = [
            ['users', 'national_id', 'ALTER TABLE `users` MODIFY COLUMN `national_id` VARCHAR(50) NULL'],
            ['users', 'specialization', 'ALTER TABLE `users` MODIFY COLUMN `specialization` VARCHAR(255) NULL'],
            ['users', 'qualification', 'ALTER TABLE `users` MODIFY COLUMN `qualification` VARCHAR(255) NULL'],
            ['graduates_data', 'specialization', 'ALTER TABLE `graduates_data` MODIFY COLUMN `specialization` VARCHAR(255) NULL'],
            ['graduates_data', 'qualification', 'ALTER TABLE `graduates_data` MODIFY COLUMN `qualification` VARCHAR(255) NULL'],
            ['graduates_data', 'employment_status', "ALTER TABLE `graduates_data` MODIFY COLUMN `employment_status` VARCHAR(50) NOT NULL DEFAULT 'seeking_opportunities'"],
            ['graduates_data', 'data_source', "ALTER TABLE `graduates_data` MODIFY COLUMN `data_source` VARCHAR(50) NOT NULL DEFAULT 'manual'"],
            ['job_fair_visitors', 'visit_purpose', 'ALTER TABLE `job_fair_visitors` MODIFY COLUMN `visit_purpose` TEXT NULL'],
        ];

        foreach ($statements as [$table, $column, $sql]) {
            try {
                if (Schema::hasTable($table) && Schema::hasColumn($table, $column)) {
                    DB::statement($sql);
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("Migration column widen skipped for {$table}.{$column}: " . $e->getMessage());
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // لا حاجة لتضييق الأعمدة لتجنب فقدان البيانات في بيئة الإنتاج
    }
};

