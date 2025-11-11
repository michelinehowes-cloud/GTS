<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('graduates_data', function (Blueprint $table) {
            $table->id();
            
            // المعلومات الشخصية
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('national_id')->unique()->nullable(); // الرقم الوطني
            
            // المعلومات الأكاديمية
            $table->string('major'); // التخصص
            $table->string('university')->default('جامعة طرابلس');
            $table->integer('graduation_year');
            $table->decimal('gpa', 3, 2)->nullable(); // المعدل التراكمي
            $table->string('degree')->default('بكالوريوس'); // الدرجة العلمية
            
            // المهارات والقدرات
            $table->json('skills')->nullable(); // المهارات التقنية والشخصية
            $table->json('languages')->nullable(); // اللغات
            $table->text('certifications')->nullable(); // الشهادات الإضافية
            
            // معلومات التوظيف
            $table->enum('employment_status', ['employed', 'unemployed', 'seeking_opportunities', 'continuing_education'])->default('seeking_opportunities');
            $table->text('work_experience')->nullable(); // الخبرات العملية
            $table->string('cv_path')->nullable(); // مسار السيرة الذاتية
            $table->string('portfolio_url')->nullable(); // رابط Portfolio
            
            // معلومات الاتصال
            $table->text('address')->nullable();
            $table->string('linkedin_url')->nullable();
            
            // تتبع البيانات
            $table->foreignId('added_by')->constrained('users')->onDelete('cascade'); // مسؤول الإرشاد المهني الذي أضاف البيانات
            $table->enum('data_source', ['manual', 'excel_import', 'system_sync'])->default('manual');
            
            // حالة البيانات
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable(); // ملاحظات إضافية
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('graduates_data');
    }
};