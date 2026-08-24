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
        Schema::create('job_fair_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_fair_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('graduate_id')->constrained('users')->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Prevent duplicate visits in the same fair by the same company for the same graduate
            $table->unique(['job_fair_id', 'company_id', 'graduate_id'], 'visit_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('job_fair_visits');
    }
};
