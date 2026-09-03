<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('graduates_data', function (Blueprint $table) {
            if (!Schema::hasColumn('graduates_data', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('graduates_data', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('graduates_data', 'gender')) {
                $table->enum('gender', ['male', 'female'])->nullable()->after('date_of_birth');
            }
            if (!Schema::hasColumn('graduates_data', 'city')) {
                $table->string('city', 100)->nullable()->after('gender');
            }
            if (!Schema::hasColumn('graduates_data', 'qualification')) {
                $table->string('qualification', 100)->nullable()->after('degree');
            }
            if (!Schema::hasColumn('graduates_data', 'specialization')) {
                $table->string('specialization', 100)->nullable()->after('major');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('graduates_data', function (Blueprint $table) {
            $columnsToDrop = [];
            foreach (['user_id', 'date_of_birth', 'gender', 'city', 'qualification', 'specialization'] as $col) {
                if (Schema::hasColumn('graduates_data', $col)) {
                    $columnsToDrop[] = $col;
                }
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
