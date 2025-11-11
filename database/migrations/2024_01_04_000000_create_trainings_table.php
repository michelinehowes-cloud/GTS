<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('trainings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->enum('type', ['workshop', 'course', 'seminar', 'internship']);
            $table->string('duration');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('location');
            $table->integer('seats');
            $table->json('materials')->nullable();
            $table->json('requirements')->nullable();
            $table->foreignId('coordinator_id')->constrained('users');
            $table->foreignId('trainer_id')->nullable()->constrained('users');
            $table->enum('status', ['draft', 'active', 'completed', 'cancelled'])->default('draft');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('trainings');
    }
};