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
        Schema::create('training_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_id')->nullable()->constrained('trainings')->onDelete('cascade');
            $table->string('file_path');
            $table->string('file_type'); // 'image', 'video'
            $table->string('caption')->nullable();
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->boolean('is_welcome_page_media')->default(false);
            $table->integer('display_order')->nullable();
            $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('training_media');
    }
};
