<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('training_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('report_name');
            $table->enum('report_type', [
                'training_programs', 
                'training_applications', 
                'student_data', 
                'attendance', 
                'evaluation'
            ]);
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_size');
            $table->text('description')->nullable();
            $table->timestamp('uploaded_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('training_reports');
    }
};