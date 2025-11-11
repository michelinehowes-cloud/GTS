<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->string('role')->default('graduate');
                $table->string('phone')->nullable();
                $table->text('address')->nullable();
                $table->string('university')->nullable();
                $table->string('major')->nullable();
                $table->string('degree')->nullable();
                $table->integer('graduation_year')->nullable();
                $table->decimal('gpa', 3, 2)->nullable();
                $table->text('skills')->nullable();
                $table->text('experiences')->nullable();
                $table->text('education')->nullable();
                $table->string('resume_path')->nullable();
                $table->boolean('is_active')->default(true);
                $table->rememberToken();
                $table->timestamps();
            });
        }
    }
    
    public function down() {
        Schema::dropIfExists('users');
    }
};