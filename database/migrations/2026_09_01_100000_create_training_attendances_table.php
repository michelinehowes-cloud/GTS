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
        Schema::create('training_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_id')->constrained('trainings')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('training_application_id')->nullable()->constrained('training_applications')->onDelete('cascade');
            $table->date('date');
            $table->timestamp('attended_at')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('status', 20)->default('present'); // present, late, excused, absent
            $table->text('notes')->nullable();
            $table->timestamps();

            // الفهرس الفريد لمنع تسجيل نفس الخريج مرتين في نفس اليوم للدورة
            $table->unique(['training_id', 'user_id', 'date'], 'training_user_date_unique');
            $table->index(['training_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_attendances');
    }
};
