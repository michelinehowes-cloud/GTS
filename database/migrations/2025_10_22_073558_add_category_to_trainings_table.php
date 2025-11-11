<?php
// ملف: database/migrations/xxxx_xx_xx_xxxxxx_add_category_to_trainings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('trainings', function (Blueprint $table) {
            $table->string('category')->after('description');
            $table->text('requirements')->nullable()->after('location');
            $table->text('objectives')->nullable()->after('requirements');
            $table->string('instructor_name')->after('objectives');
            $table->string('instructor_qualifications')->nullable()->after('instructor_name');
        });
    }

    public function down()
    {
        Schema::table('trainings', function (Blueprint $table) {
            $table->dropColumn([
                'category',
                'requirements', 
                'objectives',
                'instructor_name',
                'instructor_qualifications'
            ]);
        });
    }
};