<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('certificate_code')->unique(); // e.g. UOT-CERT-2026-08412
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('training_id')->nullable()->constrained('trainings')->nullOnDelete();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->enum('type', ['training_attendance', 'workshop_attendance', 'cooperative_attendance'])
                  ->default('training_attendance');
            $table->string('title'); // e.g. دورة الذكاء الاصطناعي وهندسة البيانات
            $table->string('recipient_name'); // اسم الخريج المستفيد
            $table->integer('hours')->nullable(); // عدد الساعات التدريبية
            $table->date('issue_date'); // تاريخ إصدار الشهادة
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('instructor_name')->nullable();
            $table->boolean('has_company_collaboration')->default(false);
            $table->string('company_name')->nullable(); // اسم الجهة الشريكة
            $table->string('company_logo')->nullable(); // مسار شعار الشركة الشريكة
            $table->string('status')->default('issued'); // issued, revoked
            $table->text('qr_code_data')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('certificates');
    }
};
