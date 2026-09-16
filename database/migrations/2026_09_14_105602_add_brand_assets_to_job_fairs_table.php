<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('job_fairs', function (Blueprint $table) {
            $table->string('media_kit_path')->nullable()->after('notes');
            $table->string('brand_guidelines_path')->nullable()->after('media_kit_path');
            $table->string('fair_logo_path')->nullable()->after('brand_guidelines_path');
            $table->string('sponsors_logos_path')->nullable()->after('fair_logo_path');
            $table->text('media_kit_description')->nullable()->after('sponsors_logos_path');
        });
    }

    public function down()
    {
        Schema::table('job_fairs', function (Blueprint $table) {
            $table->dropColumn([
                'media_kit_path',
                'brand_guidelines_path',
                'fair_logo_path',
                'sponsors_logos_path',
                'media_kit_description',
            ]);
        });
    }
};
