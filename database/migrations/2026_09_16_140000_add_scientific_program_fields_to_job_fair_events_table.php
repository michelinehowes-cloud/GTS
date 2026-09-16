<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('job_fair_events', function (Blueprint $table) {
            $table->string('type')->default('workshop')->after('title'); // masterclass, workshop, panel_discussion, keynote
            $table->string('speaker_title')->nullable()->after('speaker_name');
            $table->text('speaker_bio')->nullable()->after('speaker_title');
            $table->string('speaker_image')->nullable()->after('speaker_bio');
            $table->text('topics')->nullable()->after('description');
            $table->string('target_audience')->nullable()->after('location');
            $table->string('status')->default('open')->after('capacity'); // open, upcoming, completed, ended
            $table->boolean('is_featured')->default(false)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_fair_events', function (Blueprint $table) {
            $table->dropColumn([
                'type',
                'speaker_title',
                'speaker_bio',
                'speaker_image',
                'topics',
                'target_audience',
                'status',
                'is_featured',
            ]);
        });
    }
};
