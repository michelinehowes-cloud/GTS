<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('evaluations', function (Blueprint $table) {
            // Add missing columns expected by the Evaluation model
            if (!Schema::hasColumn('evaluations', 'status')) {
                $table->string('status')->default('pending')->after('id');
            }
            if (!Schema::hasColumn('evaluations', 'type')) {
                $table->string('type')->nullable()->after('status');
            }
            if (!Schema::hasColumn('evaluations', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade')->after('type');
            }
            if (!Schema::hasColumn('evaluations', 'training_id')) {
                $table->foreignId('training_id')->nullable()->constrained('trainings')->onDelete('cascade')->after('user_id');
            }
            if (!Schema::hasColumn('evaluations', 'scores')) {
                $table->json('scores')->nullable()->after('score');
            }
            if (!Schema::hasColumn('evaluations', 'recommendations')) {
                $table->text('recommendations')->nullable()->after('comments');
            }
            if (!Schema::hasColumn('evaluations', 'evaluation_date')) {
                $table->date('evaluation_date')->nullable()->after('recommendations');
            }
        });
    }

    public function down()
    {
        Schema::table('evaluations', function (Blueprint $table) {
            $table->dropColumn([
                'status',
                'type',
                'user_id',
                'training_id',
                'scores',
                'recommendations',
                'evaluation_date'
            ]);
        });
    }
};
