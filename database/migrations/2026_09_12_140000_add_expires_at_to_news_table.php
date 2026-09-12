<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * إضافة تاريخ انتهاء صلاحية/عرض الخبر لضبط دورة حياة الأخبار
     */
    public function up()
    {
        Schema::table('news', function (Blueprint $table) {
            if (!Schema::hasColumn('news', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->after('published_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('news', function (Blueprint $table) {
            if (Schema::hasColumn('news', 'expires_at')) {
                $table->dropColumn('expires_at');
            }
        });
    }
};
