<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\SurveyTemplate;
use Database\Seeders\SurveyTemplateSeeder;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            (new SurveyTemplateSeeder())->run();
            
            // تحديث التصنيفات لضمان التوافق التام
            SurveyTemplate::where('title', 'نموذج تقييم الشركاء (قسم التقييم والمتابعة)')->update(['category' => 'employment']);
            SurveyTemplate::where('title', 'نموذج تقييم التنظيم والتنسيق للفريق الداخلي')->update(['category' => 'events']);
            SurveyTemplate::where('title', 'استبيان آراء الزوار للفعاليات ومعرض التوظيف')->update(['category' => 'events']);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Migration seed survey templates: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse template seed data
    }
};
