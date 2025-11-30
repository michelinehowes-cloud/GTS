<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('surveys', function (Blueprint $table) {
            // إضافة نوع الاستبيان
            $table->enum('type', ['general', 'training', 'job_opportunity', 'activity'])->default('general')->after('target_audience');

            // إضافة المعرف المرتبط (للتدريب أو الوظيفة)
            $table->unsignedBigInteger('related_id')->nullable()->after('type');

            // تحسين الفهرسة للأداء
            $table->index(['type', 'related_id']);
        });
    }

    public function down()
    {
        Schema::table('surveys', function (Blueprint $table) {
            $table->dropColumn(['type', 'related_id']);
        });
    }
};
