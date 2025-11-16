<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE companies MODIFY COLUMN partnership_type ENUM('employment', 'training', 'logistic_support', 'academic', 'training_employment') NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert the enum column to its original state
        DB::statement("ALTER TABLE companies MODIFY COLUMN partnership_type ENUM('employment', 'training', 'logistic_support', 'academic') NULL");
    }
};
