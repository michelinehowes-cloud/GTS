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
        Schema::table('trainings', function (Blueprint $table) {
            // إضافة الأعمدة بالترتيب الصحيح

            if (!Schema::hasColumn('trainings', 'description')) {
                // نفترض أن title موجود
                $table->text('description')->nullable()->after('title');
            }

            if (!Schema::hasColumn('trainings', 'duration')) {
                // نضعه بعد description إذا وجد، وإلا نتركه في النهاية (أو بعد title)
                if (Schema::hasColumn('trainings', 'description')) {
                    $table->string('duration')->nullable()->after('description');
                } else {
                    $table->string('duration')->nullable();
                }
            }

            if (!Schema::hasColumn('trainings', 'location')) {
                // نفترض أن end_date موجود
                if (Schema::hasColumn('trainings', 'end_date')) {
                    $table->string('location')->nullable()->after('end_date');
                } else {
                    $table->string('location')->nullable();
                }
            }

            if (!Schema::hasColumn('trainings', 'seats')) {
                if (Schema::hasColumn('trainings', 'location')) {
                    $table->integer('seats')->default(0)->after('location');
                } else {
                    $table->integer('seats')->default(0);
                }
            }

            if (!Schema::hasColumn('trainings', 'coordinator_id')) {
                if (Schema::hasColumn('trainings', 'seats')) {
                    $table->foreignId('coordinator_id')->nullable()->constrained('users')->onDelete('set null')->after('seats');
                } else {
                    $table->foreignId('coordinator_id')->nullable()->constrained('users')->onDelete('set null');
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trainings', function (Blueprint $table) {
            // الحذف بترتيب عكسي أو لا يهم
            if (Schema::hasColumn('trainings', 'coordinator_id')) {
                $table->dropForeign(['coordinator_id']);
                $table->dropColumn('coordinator_id');
            }
            if (Schema::hasColumn('trainings', 'seats')) {
                $table->dropColumn('seats');
            }
            if (Schema::hasColumn('trainings', 'location')) {
                $table->dropColumn('location');
            }
            if (Schema::hasColumn('trainings', 'duration')) {
                $table->dropColumn('duration');
            }
            if (Schema::hasColumn('trainings', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
};
