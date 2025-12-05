<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trainer_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainer_id')->constrained('trainers')->onDelete('cascade');
            $table->foreignId('training_id')->constrained('trainings')->onDelete('cascade');
            $table->foreignId('evaluator_id')->constrained('users')->onDelete('cascade');
            $table->integer('rating')->comment('التقييم من 1 إلى 5');
            $table->text('strengths')->nullable()->comment('نقاط القوة');
            $table->text('weaknesses')->nullable()->comment('نقاط الضعف');
            $table->text('recommendations')->nullable()->comment('التوصيات');
            $table->text('notes')->nullable()->comment('ملاحظات إضافية');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainer_evaluations');
    }
};
