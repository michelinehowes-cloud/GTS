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
        Schema::create('job_fair_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_fair_id')->constrained('job_fairs')->onDelete('cascade');
            $table->string('title');
            $table->string('faculty'); // الكلية
            $table->string('department'); // القسم / التخصص
            $table->integer('graduation_year')->default(2026); // سنة التخرج
            $table->string('academic_year')->nullable()->default('2025/2026'); // العام الجامعي
            $table->string('supervisor_name')->nullable(); // المشرف الأكاديمي
            $table->string('supervisor_title')->nullable(); // اللقب العلمي (دكتور / أستاذ...)
            $table->json('team_members')->nullable(); // أسماء الطلبة وبيانات التواصل
            $table->text('summary')->nullable(); // نبذة مختصرة
            $table->text('objectives')->nullable(); // فكرة وأهداف المشروع
            $table->longText('description')->nullable(); // التفاصيل والمواصفات والنتائج
            $table->string('poster_image')->nullable(); // بوستر المشروع
            $table->string('cover_image')->nullable(); // صورة الغلاف
            $table->json('gallery_images')->nullable(); // معرض صور ونماذج أولية
            $table->string('video_url')->nullable(); // رابط فيديو تجريبي / Demo
            $table->string('project_url')->nullable(); // رابط المشروع أو GitHub
            $table->json('attachments')->nullable(); // تقارير PDF وعروض تقديمية
            $table->string('booth_number')->nullable(); // رقم جناح العرض
            $table->string('status')->default('published'); // published, draft, archived
            $table->boolean('is_featured')->default(false); // تمييز في الصفحة الرئيسية
            $table->unsignedInteger('views_count')->default(0);
            $table->timestamps();

            // Indexes for fast filtering & searching
            $table->index(['job_fair_id', 'faculty']);
            $table->index(['job_fair_id', 'graduation_year']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('job_fair_projects');
    }
};
