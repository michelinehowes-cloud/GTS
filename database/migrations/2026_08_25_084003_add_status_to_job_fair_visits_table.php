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
        Schema::table('job_fair_visits', function (Blueprint $table) {
            $table->enum('status', ['pending', 'shortlisted', 'accepted', 'rejected'])
                  ->default('pending')
                  ->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('job_fair_visits', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
