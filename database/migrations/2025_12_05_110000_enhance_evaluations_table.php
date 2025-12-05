<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('evaluations', function (Blueprint $table) {
            // إضافة الحقول الجديدة فقط (التحقق من عدم وجودها)
            if (!Schema::hasColumn('evaluations', 'facilities_evaluation')) {
                $table->json('facilities_evaluation')->nullable();
            }
            if (!Schema::hasColumn('evaluations', 'content_evaluation')) {
                $table->json('content_evaluation')->nullable();
            }
            if (!Schema::hasColumn('evaluations', 'trainer_evaluation')) {
                $table->json('trainer_evaluation')->nullable();
            }
            if (!Schema::hasColumn('evaluations', 'organization_evaluation')) {
                $table->json('organization_evaluation')->nullable();
            }
            if (!Schema::hasColumn('evaluations', 'impact_evaluation')) {
                $table->json('impact_evaluation')->nullable();
            }
            if (!Schema::hasColumn('evaluations', 'employment_evaluation')) {
                $table->json('employment_evaluation')->nullable();
            }
            if (!Schema::hasColumn('evaluations', 'survey_response_id')) {
                $table->foreignId('survey_response_id')->nullable()->constrained('survey_responses')->nullOnDelete();
            }
            if (!Schema::hasColumn('evaluations', 'strengths')) {
                $table->text('strengths')->nullable();
            }
            if (!Schema::hasColumn('evaluations', 'weaknesses')) {
                $table->text('weaknesses')->nullable();
            }
            if (!Schema::hasColumn('evaluations', 'overall_rating')) {
                $table->decimal('overall_rating', 3, 2)->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('evaluations', function (Blueprint $table) {
            $columns = [
                'facilities_evaluation',
                'content_evaluation',
                'trainer_evaluation',
                'organization_evaluation',
                'impact_evaluation',
                'employment_evaluation',
                'strengths',
                'weaknesses',
                'overall_rating'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('evaluations', $column)) {
                    $table->dropColumn($column);
                }
            }

            if (Schema::hasColumn('evaluations', 'survey_response_id')) {
                $table->dropForeign(['survey_response_id']);
                $table->dropColumn('survey_response_id');
            }
        });
    }
};
