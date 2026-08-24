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
        Schema::create('job_fair_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_fair_id')->constrained('job_fairs')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');   // الخريج
            $table->string('qr_code')->unique();                    // رمز QR الفريد
            $table->string('registration_number')->unique();        // رقم التسجيل
            $table->boolean('attended')->default(false);            // هل حضر؟
            $table->datetime('check_in_at')->nullable();            // وقت تسجيل الحضور
            $table->text('interests')->nullable();                  // اهتمامات الخريج (قطاعات)
            $table->text('notes')->nullable();
            $table->enum('status', ['registered', 'confirmed', 'cancelled', 'attended'])
                  ->default('registered');
            $table->timestamps();
            $table->unique(['job_fair_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('job_fair_registrations');
    }
};
