<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_approved')->default(true)->after('role')->comment('هل تمت الموافقة على الحساب');
            $table->timestamp('approved_at')->nullable()->after('is_approved')->comment('تاريخ الموافقة');
            $table->unsignedBigInteger('approved_by')->nullable()->after('approved_at')->comment('معرف الموافق');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_approved', 'approved_at', 'approved_by']);
        });
    }
};
