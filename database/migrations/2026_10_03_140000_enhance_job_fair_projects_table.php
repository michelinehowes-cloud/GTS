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
        Schema::table('job_fair_projects', function (Blueprint $table) {
            // ربط بحساب المستخدم / الخريج إن وُجد
            $table->foreignId('user_id')->nullable()->after('job_fair_id')->constrained('users')->nullOnDelete();

            // حقول عامة للزوار والشركات (Public Fields)
            $table->string('project_type')->nullable()->after('graduation_year'); // نوع المشروع (برمجي، ذكاء اصطناعي، عتادي...)
            $table->string('main_category')->nullable()->after('project_type'); // المجال الرئيسي للمشروع
            $table->text('problem_statement')->nullable()->after('summary'); // المشكلة التي يعالجها المشروع
            $table->text('solution_statement')->nullable()->after('problem_statement'); // الحل الذي يقدمه المشروع
            $table->text('technical_specifications')->nullable()->after('description'); // التفاصيل الفنية والمخرجات والمواصفات
            $table->text('key_outcomes')->nullable()->after('technical_specifications'); // أبرز النتائج والمميزات
            $table->text('market_viability')->nullable()->after('key_outcomes'); // إمكانية تطوير المشروع إلى منتج قابل للتسويق
            $table->string('contact_email')->nullable()->after('project_url'); // البريد الإلكتروني للتواصل

            // حقول خاصة بالمسؤول فقط - سرية ولا تظهر للعامة (Admin Only Fields)
            $table->string('student_university_id')->nullable()->after('contact_email'); // الرقم الجامعي / رقم القيد
            $table->string('whatsapp_phone')->nullable()->after('student_university_id'); // رقم الهاتف المسجل بالواتساب
            $table->text('project_requirements')->nullable()->after('whatsapp_phone'); // المتطلبات التي يحتاجها المشروع
            $table->boolean('needs_special_equipment')->default(false)->after('project_requirements'); // هل يحتاج معدات خاصة أثناء العرض
            $table->text('special_equipment_details')->nullable()->after('needs_special_equipment'); // تفاصيل المعدات الخاصة
            $table->text('additional_requirements')->nullable()->after('special_equipment_details'); // المتطلبات الإضافية الخاصة بالمشروع
            $table->text('executive_summary')->nullable()->after('additional_requirements'); // الملخص التنفيذي للمراجعة الداخلية
            $table->string('prototype_status')->nullable()->after('executive_summary'); // حالة النموذج الأولي
            $table->text('admin_notes')->nullable()->after('prototype_status'); // ملاحظات أو إجراءات إدارية
            $table->text('rejection_reason')->nullable()->after('admin_notes'); // سبب الرفض إن تم رفض المشروع
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('job_fair_projects', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'user_id',
                'project_type',
                'main_category',
                'problem_statement',
                'solution_statement',
                'technical_specifications',
                'key_outcomes',
                'market_viability',
                'contact_email',
                'student_university_id',
                'whatsapp_phone',
                'project_requirements',
                'needs_special_equipment',
                'special_equipment_details',
                'additional_requirements',
                'executive_summary',
                'prototype_status',
                'admin_notes',
                'rejection_reason',
            ]);
        });
    }
};
