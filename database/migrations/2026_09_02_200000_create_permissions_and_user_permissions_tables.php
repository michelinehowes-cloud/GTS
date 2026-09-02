<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. جدول الصلاحيات
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // المعرّف البرمجي للصلاحية مثل: graduates.view
            $table->string('display_name');  // الاسم المقروء بالعربية
            $table->string('module');        // اسم الوحدة التابع لها (graduates, companies, trainings, etc.)
            $table->text('description')->nullable(); // وصف مختصر للمهمة
            $table->timestamps();
        });

        // 2. جدول الربط بين المستخدمين والصلاحيات (Pivot Table)
        Schema::create('permission_user', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
            $table->primary(['user_id', 'permission_id']);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permission_user');
        Schema::dropIfExists('permissions');
    }
};
