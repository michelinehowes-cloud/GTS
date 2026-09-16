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
            $table->string('fair_logo_white_path')->nullable()->after('fair_logo_path');
            $table->string('fair_logo_horizontal_path')->nullable()->after('fair_logo_white_path');
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
            $table->dropColumn([
                'fair_logo_white_path',
                'fair_logo_horizontal_path',
            ]);
        });
    }
};
