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
        if (Schema::hasTable('users')) {
            DB::statement("ALTER TABLE users MODIFY COLUMN gpa DECIMAL(5,2) NULL;");
        }

        if (Schema::hasTable('graduates_data')) {
            DB::statement("ALTER TABLE graduates_data MODIFY COLUMN gpa DECIMAL(5,2) NULL;");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users')) {
            DB::statement("ALTER TABLE users MODIFY COLUMN gpa DECIMAL(3,2) NULL;");
        }

        if (Schema::hasTable('graduates_data')) {
            DB::statement("ALTER TABLE graduates_data MODIFY COLUMN gpa DECIMAL(3,2) NULL;");
        }
    }
};
