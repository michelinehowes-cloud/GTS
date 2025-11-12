<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'مدير النظام',
            'email' => 'admin@tripoliuniversity.edu.ly',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // يمكنك إضافة مستخدمين إضافيين للاختبار
        User::create([
            'name' => 'خريج تجريبي',
            'email' => 'graduate@tripoliuniversity.edu.ly',
            'password' => Hash::make('password123'),
            'role' => 'graduate',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        $this->call([
            AdminUserSeeder::class,
            CompanySeeder::class,
        ]);
    }
}
