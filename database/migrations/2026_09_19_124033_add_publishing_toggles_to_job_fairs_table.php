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
        Schema::table('job_fairs', function (Blueprint $table) {
            $table->boolean('is_program_published')->default(false)->after('registration_open');
            $table->boolean('is_projects_published')->default(true)->after('is_program_published');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('job_fairs', function (Blueprint $table) {
            $table->dropColumn(['is_program_published', 'is_projects_published']);
        });
    }
};
