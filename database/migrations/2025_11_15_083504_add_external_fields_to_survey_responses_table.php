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
        Schema::table('survey_responses', function (Blueprint $table) {
            $table->string('participant_email')->nullable()->after('user_id');
            $table->string('participant_name')->nullable()->after('participant_email');
            $table->boolean('is_external')->default(false)->after('participant_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('survey_responses', function (Blueprint $table) {
            $table->dropColumn(['participant_email', 'participant_name', 'is_external']);
        });
    }
};
