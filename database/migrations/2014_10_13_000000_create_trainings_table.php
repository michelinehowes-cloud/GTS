<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        if (!Schema::hasTable('trainings')) {
            Schema::create('trainings', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description');
                $table->string('type');
                $table->string('duration');
                $table->date('start_date');
                $table->date('end_date');
                $table->string('location');
                $table->integer('seats');
                $table->string('status')->default('active');
                $table->foreignId('company_id')->nullable()->constrained()->onDelete('set null');
                $table->foreignId('coordinator_id')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
            });
        }
    }
    
    public function down() {
        Schema::dropIfExists('trainings');
    }
};