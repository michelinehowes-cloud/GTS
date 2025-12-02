<?php
// ملف: database/migrations/xxxx_xx_xx_xxxxxx_add_category_to_trainings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('trainings', function (Blueprint $table) {
            if (!Schema::hasColumn('trainings', 'category')) {
                $table->string('category')->nullable();
            }
            if (!Schema::hasColumn('trainings', 'requirements')) {
                $table->text('requirements')->nullable();
            }
            if (!Schema::hasColumn('trainings', 'objectives')) {
                $table->text('objectives')->nullable();
            }
            if (!Schema::hasColumn('trainings', 'instructor_name')) {
                $table->string('instructor_name')->nullable();
            }
            if (!Schema::hasColumn('trainings', 'instructor_qualifications')) {
                $table->string('instructor_qualifications')->nullable();
            }
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