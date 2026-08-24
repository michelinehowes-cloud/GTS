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
        Schema::create('job_fairs', function (Blueprint $table) {
            $table->id();
            $table->string('title');                          // عنوان المعرض
            $table->string('subtitle')->nullable();           // عنوان فرعي
            $table->text('description')->nullable();          // وصف المعرض
            $table->date('event_date');                       // تاريخ المعرض
            $table->time('start_time')->nullable();           // وقت البداية
            $table->time('end_time')->nullable();             // وقت النهاية
            $table->string('location');                       // مكان الانعقاد
            $table->string('hall_map')->nullable();           // صورة خارطة القاعة
            $table->string('banner_image')->nullable();       // صورة البانر
            $table->integer('max_graduates')->nullable();     // أقصى عدد خريجين
            $table->integer('max_companies')->nullable();     // أقصى عدد شركات
            $table->enum('status', ['draft', 'published', 'ongoing', 'completed', 'cancelled'])
                  ->default('draft');
            $table->boolean('registration_open')->default(true);
            $table->datetime('registration_deadline')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('job_fairs');
    }
};
