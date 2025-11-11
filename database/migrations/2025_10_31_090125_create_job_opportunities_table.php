<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('job_opportunities', function (Blueprint $table) {
            $table->id();
            
            // المعلومات الأساسية
            $table->string('title');
            $table->text('description');
            $table->enum('type', ['job', 'training', 'internship']); // نوع الفرصة
            $table->enum('contract_type', ['full_time', 'part_time', 'contract', 'freelance'])->nullable(); // نوع العقد
            
            // معلومات الشركة
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            
            // التفاصيل
            $table->string('location');
            $table->integer('seats'); // عدد المقاعد
            $table->date('start_date');
            $table->date('end_date');
            $table->date('application_deadline'); // آخر موعد للتقديم
            
            // المتطلبات
            $table->json('required_specializations')->nullable(); // التخصصات المطلوبة
            $table->json('required_skills')->nullable(); // المهارات المطلوبة
            $table->string('required_experience')->nullable(); // الخبرة المطلوبة
            $table->decimal('salary', 10, 2)->nullable(); // الراتب
            
            // حالة الفرصة
            $table->enum('status', ['new', 'open', 'closed', 'completed'])->default('new');
            
            // معلومات إضافية
            $table->text('benefits')->nullable(); // المزايا
            $table->text('requirements')->nullable(); // المتطلبات العامة
            
            // تتبع المستخدم
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // مسؤول الشراكات الذي أنشأ الفرصة
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('job_opportunities');
    }
};