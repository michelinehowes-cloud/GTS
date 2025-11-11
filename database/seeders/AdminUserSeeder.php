<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // إنشاء مستخدم مدير النظام
        User::create([
            'name' => 'مدير النظام',
            'email' => 'admin@tripoliuniversity.edu.ly',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // إنشاء مستخدم منسق تدريب
        User::create([
            'name' => 'منسق التدريب',
            'email' => 'training@tripoliuniversity.edu.ly',
            'password' => Hash::make('password123'),
            'role' => 'training_coordinator',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // إنشاء مستخدم منسق توظيف
        User::create([
            'name' => 'منسق التوظيف',
            'email' => 'placement@tripoliuniversity.edu.ly',
            'password' => Hash::make('password123'),
            'role' => 'placement_coordinator',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // إنشاء مستخدم خريج
        User::create([
            'name' => 'خريج تجريبي',
            'email' => 'graduate@tripoliuniversity.edu.ly',
            'password' => Hash::make('password123'),
            'role' => 'graduate',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        echo "تم إنشاء المستخدمين بنجاح!\n";
    }
}