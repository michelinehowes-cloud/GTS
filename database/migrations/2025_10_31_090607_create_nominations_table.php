<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('nominations', function (Blueprint $table) {
            $table->id();
            
            // العلاقات
            $table->foreignId('job_opportunity_id')->constrained()->onDelete('cascade'); // الفرصة
            $table->foreignId('graduate_id')->constrained('graduates_data')->onDelete('cascade'); // الخريج
            $table->foreignId('nominated_by')->constrained('users')->onDelete('cascade'); // مسؤول الإرشاد المهني الذي رشح
            
            // معلومات الترشيح
            $table->enum('status', ['pending', 'sent_to_company', 'under_review', 'interview_scheduled', 'accepted', 'rejected', 'withdrawn'])->default('pending');
            $table->text('nomination_notes')->nullable(); // ملاحظات الترشيح
            $table->text('matching_reasons')->nullable(); // أسباب التطابق بين الخريج والفرصة
            
            // معلومات المقابلة
            $table->date('interview_date')->nullable();
            $table->time('interview_time')->nullable();
            $table->string('interview_location')->nullable();
            $table->text('interview_notes')->nullable();
            
            // نتائج الترشيح
            $table->enum('final_status', ['hired', 'not_hired', 'in_progress'])->nullable();
            $table->text('company_feedback')->nullable(); // ملاحظات الشركة
            $table->text('graduate_feedback')->nullable(); // ملاحظات الخريج
            
            // التواريخ المهمة
            $table->timestamp('nominated_at')->useCurrent(); // تاريخ الترشيح
            $table->timestamp('sent_to_company_at')->nullable(); // تاريخ الإرسال للشركة
            $table->timestamp('company_response_at')->nullable(); // تاريخ رد الشركة
            $table->timestamp('interview_at')->nullable(); // تاريخ المقابلة الفعلي
            $table->timestamp('final_decision_at')->nullable(); // تاريخ القرار النهائي
            
            // تتبع الإشعارات
            $table->boolean('notified_graduate')->default(false); // تم إشعار الخريج
            $table->boolean('notified_company')->default(false); // تم إشعار الشركة
            
            $table->timestamps();

            // مفتاح فريد لمنع التكرار
            $table->unique(['job_opportunity_id', 'graduate_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('nominations');
    }
};