<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('partnership_documents', function (Blueprint $table) {
            $table->id();
            
            // العلاقات
            $table->foreignId('company_id')->constrained()->onDelete('cascade'); // الشركة
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade'); // مسؤول الشراكات الذي رفع الوثيقة
            
            // معلومات الوثيقة
            $table->string('document_name'); // اسم الوثيقة
            $table->string('document_type'); // نوع الوثيقة (mou, contract, agreement, etc.)
            $table->text('description')->nullable(); // وصف الوثيقة
            $table->string('file_path'); // مسار تخزين الملف
            $table->string('file_name'); // اسم الملف الأصلي
            $table->string('file_size'); // حجم الملف
            $table->string('mime_type'); // نوع الملف
            
            // تفاصيل الوثيقة
            $table->date('document_date')->nullable(); // تاريخ الوثيقة
            $table->date('effective_date')->nullable(); // تاريخ السريان
            $table->date('expiry_date')->nullable(); // تاريخ الانتهاء
            $table->enum('document_status', ['draft', 'active', 'expired', 'cancelled'])->default('draft');
            
            // إعدادات المشاركة
            $table->boolean('is_shared_with_company')->default(false); // مشاركة مع الشركة
            $table->boolean('is_confidential')->default(true); // وثيقة سرية
            
            // توقيعات وموافقات
            $table->boolean('company_signed')->default(false); // موقعة من الشركة
            $table->boolean('university_signed')->default(false); // موقعة من الجامعة
            $table->date('university_signed_date')->nullable(); // تاريخ توقيع الجامعة
            $table->date('company_signed_date')->nullable(); // تاريخ توقيع الشركة
            
            // تتبع الإصدارات
            $table->integer('version')->default(1); // إصدار الوثيقة
            $table->foreignId('previous_version_id')->nullable()->constrained('partnership_documents')->onDelete('set null'); // الوثيقة السابقة
            
            // مراجعة الوثيقة
            $table->text('review_notes')->nullable(); // ملاحظات المراجعة
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null'); // المسؤول المراجع
            $table->timestamp('reviewed_at')->nullable(); // تاريخ المراجعة
            
            // الأرشيف
            $table->boolean('is_archived')->default(false); // مؤرشف
            $table->text('archival_reason')->nullable(); // سبب الأرشفة
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('partnership_documents');
    }
};