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
        Schema::create('job_fair_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_fair_id')->nullable()->constrained('job_fairs')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('supervisor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('team_name')->default('اللجنة التنظيمية الرئيسية');
            $table->text('task_description');
            $table->date('start_date');
            $table->date('due_date');
            $table->date('completed_date')->nullable();
            $table->string('evaluation_score')->nullable(); // e.g. "5/5", "ممتاز", "95%"
            $table->string('status')->default('in_progress'); // in_progress, completed, delayed, pending
            $table->text('notes')->nullable();
            $table->string('signature_name')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_fair_tasks');
    }
};
