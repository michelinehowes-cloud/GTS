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
        Schema::table('users', function (Blueprint $table) {
            // إضافة الحقول المطلوبة لنظام تسجيل الخريجين
            if (!Schema::hasColumn('users', 'national_id')) {
                $table->string('national_id', 20)->nullable()->unique()->after('email');
            }
            if (!Schema::hasColumn('users', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'gender')) {
                $table->enum('gender', ['male', 'female'])->nullable()->after('date_of_birth');
            }
            if (!Schema::hasColumn('users', 'city')) {
                $table->string('city', 100)->nullable()->after('address');
            }
            if (!Schema::hasColumn('users', 'qualification')) {
                $table->string('qualification', 100)->nullable()->after('university');
            }
            if (!Schema::hasColumn('users', 'specialization')) {
                $table->string('specialization', 100)->nullable()->after('qualification');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'national_id',
                'date_of_birth',
                'gender',
                'city',
                'qualification',
                'specialization',
            ]);
        });
    }
};
