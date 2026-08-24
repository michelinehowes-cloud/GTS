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
        Schema::create('job_fair_companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_fair_id')->constrained('job_fairs')->onDelete('cascade');
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->string('booth_number')->nullable();            // رقم الجناح
            $table->string('booth_location')->nullable();          // موقع الجناح في القاعة
            $table->text('participating_sectors')->nullable();     // القطاعات المشاركة بها
            $table->integer('available_positions')->nullable();    // عدد الوظائف المتاحة
            $table->text('requirements')->nullable();              // متطلبات التوظيف
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('confirmed');
            $table->timestamps();
            $table->unique(['job_fair_id', 'company_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('job_fair_companies');
    }
};
