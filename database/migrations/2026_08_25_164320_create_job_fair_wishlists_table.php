<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('job_fair_wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('graduate_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('job_fair_company_id')->constrained('job_fair_companies')->cascadeOnDelete();
            $table->timestamps();
            
            $table->unique(['graduate_id', 'job_fair_company_id'], 'jfw_g_jfc_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('job_fair_wishlists');
    }
};
