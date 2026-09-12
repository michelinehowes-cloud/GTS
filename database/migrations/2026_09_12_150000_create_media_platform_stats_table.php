<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('media_platform_stats', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_ribbon_visible')->default(true);
            $table->string('global_mode')->default('auto'); // 'auto' (real db counts) or 'manual' (media custom values)

            // Graduates Stat
            $table->string('graduates_mode')->default('auto'); // 'auto' or 'manual'
            $table->integer('graduates_custom_value')->nullable();
            $table->boolean('graduates_visible')->default(true);
            $table->string('graduates_label')->default('خريج مسجل ومعتمد');
            $table->string('graduates_prefix')->default('+');

            // Companies Stat
            $table->string('companies_mode')->default('auto');
            $table->integer('companies_custom_value')->nullable();
            $table->boolean('companies_visible')->default(true);
            $table->string('companies_label')->default('شركة ومؤسسة شريكة');
            $table->string('companies_prefix')->default('+');

            // Trainings Stat
            $table->string('trainings_mode')->default('auto');
            $table->integer('trainings_custom_value')->nullable();
            $table->boolean('trainings_visible')->default(true);
            $table->string('trainings_label')->default('برنامج تدريبي وتأهيلي');
            $table->string('trainings_prefix')->default('+');

            // Opportunities Stat
            $table->string('opportunities_mode')->default('auto');
            $table->integer('opportunities_custom_value')->nullable();
            $table->boolean('opportunities_visible')->default(true);
            $table->string('opportunities_label')->default('فرصة عمل وترشيح');
            $table->string('opportunities_prefix')->default('+');

            $table->foreignId('updated_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_platform_stats');
    }
};
