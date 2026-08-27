<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('job_fair_event_attendees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_fair_event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('graduate_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['registered', 'attended', 'cancelled'])->default('registered');
            $table->timestamps();
            
            $table->unique(['job_fair_event_id', 'graduate_id'], 'jfea_e_g_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('job_fair_event_attendees');
    }
};
