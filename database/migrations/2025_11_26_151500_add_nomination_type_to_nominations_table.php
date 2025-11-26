<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('nominations', function (Blueprint $table) {
            if (!Schema::hasColumn('nominations', 'nomination_type')) {
                $table->string('nomination_type')->default('officer')->after('nominated_by');
            }
        });
    }

    public function down()
    {
        Schema::table('nominations', function (Blueprint $table) {
            if (Schema::hasColumn('nominations', 'nomination_type')) {
                $table->dropColumn('nomination_type');
            }
        });
    }
};
