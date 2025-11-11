<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->morphs('evaluatable');
            $table->foreignId('evaluator_id')->constrained('users');
            $table->decimal('score', 5, 2);
            $table->json('criteria_scores')->nullable();
            $table->text('comments');
            $table->string('evaluation_type');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('evaluations');
    }
};