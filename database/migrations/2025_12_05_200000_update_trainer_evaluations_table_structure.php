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
        Schema::table('trainer_evaluations', function (Blueprint $table) {
            if (!Schema::hasColumn('trainer_evaluations', 'evaluation_id')) {
                $table->foreignId('evaluation_id')->nullable()->constrained('evaluations')->onDelete('cascade');
            }
            if (!Schema::hasColumn('trainer_evaluations', 'scores')) {
                $table->json('scores')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trainer_evaluations', function (Blueprint $table) {
            $table->dropForeign(['evaluation_id']);
            $table->dropColumn('evaluation_id');
            $table->dropColumn('scores');
        });
    }
};
