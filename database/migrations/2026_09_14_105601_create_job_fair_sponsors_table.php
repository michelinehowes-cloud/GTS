<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('job_fair_sponsors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_fair_id')->constrained('job_fairs')->onDelete('cascade');
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->string('name');
            $table->string('tier')->default('الراعي الذهبي'); // e.g. الراعي الماسي، الراعي الذهبي، الراعي الفضي، راعي التقنية، الراعي الرسمي
            $table->string('logo_path')->nullable();
            $table->string('website')->nullable();
            $table->text('description')->nullable();
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('job_fair_sponsors');
    }
};
