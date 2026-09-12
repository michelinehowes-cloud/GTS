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
        Schema::table('trainings', function (Blueprint $table) {
            $table->text('media_coverage_summary')->nullable()->after('media_coverage_status')->comment('ملخص التغطية الصحفية والإعلامية');
            $table->text('media_press_release')->nullable()->after('media_coverage_summary')->comment('البيان الصحفي المعتمد');
            $table->text('media_coverage_notes')->nullable()->after('media_press_release')->comment('ملاحظات وتوصيات الفريق الإعلامي');
            $table->string('media_team_members')->nullable()->after('media_coverage_notes')->comment('فريق التغطية والمصورين');
            $table->text('media_coverage_links')->nullable()->after('media_team_members')->comment('روابط النشر على المنصات والسوشيال ميديا');
            $table->date('media_coverage_date')->nullable()->after('media_coverage_links')->comment('تاريخ إنجاز التغطية الصحفية');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trainings', function (Blueprint $table) {
            $table->dropColumn([
                'media_coverage_summary',
                'media_press_release',
                'media_coverage_notes',
                'media_team_members',
                'media_coverage_links',
                'media_coverage_date',
            ]);
        });
    }
};
