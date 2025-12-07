<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // إضافة الحقول لجدول users
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'university')) {
                $table->string('university')->nullable();
            }
            if (!Schema::hasColumn('users', 'sector')) {
                $table->string('sector')->nullable();
            }
            if (!Schema::hasColumn('users', 'faculty')) {
                $table->string('faculty')->nullable();
            }
        });

        // إضافة الحقول لجدول graduates_data
        Schema::table('graduates_data', function (Blueprint $table) {
            if (!Schema::hasColumn('graduates_data', 'university')) {
                $table->string('university')->nullable();
            }
            if (!Schema::hasColumn('graduates_data', 'sector')) {
                $table->string('sector')->nullable();
            }
            if (!Schema::hasColumn('graduates_data', 'faculty')) {
                $table->string('faculty')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // حذف الحقول من جدول users
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'university')) {
                $table->dropColumn('university');
            }
            if (Schema::hasColumn('users', 'sector')) {
                $table->dropColumn('sector');
            }
            if (Schema::hasColumn('users', 'faculty')) {
                $table->dropColumn('faculty');
            }
        });

        // حذف الحقول من جدول graduates_data
        Schema::table('graduates_data', function (Blueprint $table) {
            if (Schema::hasColumn('graduates_data', 'university')) {
                $table->dropColumn('university');
            }
            if (Schema::hasColumn('graduates_data', 'sector')) {
                $table->dropColumn('sector');
            }
            if (Schema::hasColumn('graduates_data', 'faculty')) {
                $table->dropColumn('faculty');
            }
        });
    }
};
