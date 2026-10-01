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
        Schema::create('job_fair_visitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_fair_id')->constrained('job_fairs')->onDelete('cascade');
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('visitor_type')->default('general'); // student, job_seeker, parent, company_rep, academic, general
            $table->string('education_level')->nullable();      // high_school, diploma, bachelor, master, phd, other
            $table->string('specialization')->nullable();
            $table->string('organization')->nullable();         // الجامعة، الكلية، أو جهة العمل
            $table->string('city')->nullable();
            $table->string('visit_purpose')->nullable();        // explore_jobs, visit_booths, attend_workshops, support_graduate, general
            $table->string('ticket_number')->unique();          // مثل VIS-2026-XXXXX
            $table->string('qr_code')->nullable();
            $table->boolean('attended')->default(false);
            $table->timestamp('check_in_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['job_fair_id', 'visitor_type']);
            $table->index(['job_fair_id', 'attended']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_fair_visitors');
    }
};
