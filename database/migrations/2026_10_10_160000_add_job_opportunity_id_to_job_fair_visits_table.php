<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('job_fair_visits') && !Schema::hasColumn('job_fair_visits', 'job_opportunity_id')) {
            Schema::table('job_fair_visits', function (Blueprint $table) {
                $table->unsignedBigInteger('job_opportunity_id')->nullable()->after('graduate_id');
            });
            try {
                Schema::table('job_fair_visits', function (Blueprint $table) {
                    $table->foreign('job_opportunity_id')->references('id')->on('job_opportunities')->nullOnDelete();
                });
            } catch (\Throwable $e) {}
        }

        if (Schema::hasTable('job_opportunities') && !Schema::hasColumn('job_opportunities', 'job_fair_id')) {
            Schema::table('job_opportunities', function (Blueprint $table) {
                $table->unsignedBigInteger('job_fair_id')->nullable()->after('company_id');
            });
            try {
                Schema::table('job_opportunities', function (Blueprint $table) {
                    $table->foreign('job_fair_id')->references('id')->on('job_fairs')->nullOnDelete();
                });
            } catch (\Throwable $e) {}
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('job_fair_visits') && Schema::hasColumn('job_fair_visits', 'job_opportunity_id')) {
            Schema::table('job_fair_visits', function (Blueprint $table) {
                $table->dropForeign(['job_opportunity_id']);
                $table->dropColumn('job_opportunity_id');
            });
        }

        if (Schema::hasTable('job_opportunities') && Schema::hasColumn('job_opportunities', 'job_fair_id')) {
            Schema::table('job_opportunities', function (Blueprint $table) {
                $table->dropForeign(['job_fair_id']);
                $table->dropColumn('job_fair_id');
            });
        }
    }
};
