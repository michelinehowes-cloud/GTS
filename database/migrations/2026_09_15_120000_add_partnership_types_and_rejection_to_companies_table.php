<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'partnership_types')) {
                $table->json('partnership_types')->nullable()->after('partnership_type');
            }
            if (!Schema::hasColumn('companies', 'rejection_notes')) {
                $table->text('rejection_notes')->nullable()->after('partnership_notes');
            }
        });

        // ترحيل البيانات الحالية من partnership_type إلى partnership_types
        try {
            $companies = DB::table('companies')->select('id', 'partnership_type')->get();
            foreach ($companies as $company) {
                if (!empty($company->partnership_type)) {
                    // إذا كانت القيمة مجمعة سابقة مثل training_employment نحولها لنوعين
                    $types = ($company->partnership_type === 'training_employment') 
                        ? ['training', 'employment'] 
                        : [$company->partnership_type];

                    DB::table('companies')->where('id', $company->id)->update([
                        'partnership_types' => json_encode($types, JSON_UNESCAPED_UNICODE)
                    ]);
                }
            }
        } catch (\Exception $e) {
            // تجاهل الخطأ في حالة الترحيل التجريبي
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'partnership_types')) {
                $table->dropColumn('partnership_types');
            }
            if (Schema::hasColumn('companies', 'rejection_notes')) {
                $table->dropColumn('rejection_notes');
            }
        });
    }
};
