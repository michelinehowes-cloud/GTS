<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('training_applications')) {
            Schema::create('training_applications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('training_id')->constrained()->onDelete('cascade');
                $table->string('status')->default('pending');
                $table->text('message')->nullable();
                $table->timestamp('applied_at')->useCurrent();
                $table->timestamps();
                $table->unique(['user_id', 'training_id']);
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('training_applications');
    }
};