<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->enum('type', ['internship', 'full_time', 'part_time', 'contract']);
            $table->text('description');
            $table->json('requirements');
            $table->string('location');
            $table->integer('seats');
            $table->date('deadline');
            $table->decimal('salary', 10, 2)->nullable();
            $table->json('benefits')->nullable();
            $table->enum('status', ['draft', 'active', 'closed', 'cancelled'])->default('draft');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('jobs');
    }
};