<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE surveys MODIFY COLUMN type VARCHAR(50) DEFAULT 'general'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE surveys MODIFY COLUMN type ENUM('general', 'training', 'job_opportunity', 'activity') DEFAULT 'general'");
        }
    }
};
