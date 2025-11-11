<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            // نوع الشراكة
            $table->enum('partnership_type', ['employment', 'training', 'logistic_support', 'academic'])->nullable()->after('is_approved');
            
            // حالة الشراكة
            $table->enum('partnership_status', ['active', 'expired', 'under_review'])->default('under_review')->after('partnership_type');
            
            // ملاحظات الشراكة
            $table->text('partnership_notes')->nullable()->after('partnership_status');
            
            // تاريخ بداية الشراكة
            $table->date('partnership_start_date')->nullable()->after('partnership_notes');
            
            // تاريخ نهاية الشراكة
            $table->date('partnership_end_date')->nullable()->after('partnership_start_date');
            
            // شخص الاتصال في الشركة
            $table->string('contact_person')->nullable()->after('partnership_end_date');
            $table->string('contact_position')->nullable()->after('contact_person');
            $table->string('contact_phone')->nullable()->after('contact_position');
            $table->string('contact_email')->nullable()->after('contact_phone');
        });
    }

    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'partnership_type',
                'partnership_status',
                'partnership_notes',
                'partnership_start_date',
                'partnership_end_date',
                'contact_person',
                'contact_position',
                'contact_phone',
                'contact_email'
            ]);
        });
    }
};