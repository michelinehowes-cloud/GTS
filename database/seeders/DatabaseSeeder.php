<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // استدعاء ملفات التعبئة (Seeders)

        $this->call([
            AdminUserSeeder::class,
            CompanySeeder::class,
            PermissionSeeder::class,
            JobFairEventsSeeder::class,
            JobFairProjectsSeeder::class,
            MonthNineTrainingsSeeder::class,
            SurveyTemplateSeeder::class,
            SurveyWithResponsesSeeder::class,
            LiveSurveysAndResponsesSeeder::class,
            PlatformContentSeeder::class,
        ]);
    }
}
