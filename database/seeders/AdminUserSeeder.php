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
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // إنشاء مستخدم منسق تدريب
        User::create([
            'name' => 'منسق التدريب',
            'email' => 'training@tripoliuniversity.edu.ly',
            'password' => Hash::make('password123'),
            'role' => 'training_coordinator',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // إنشاء مستخدم مسؤول الشراكات
        User::create([
            'name' => 'مسؤول الشراكات',
            'email' => 'partnership@tripoliuniversity.edu.ly',
            'password' => Hash::make('password123'),
            'role' => 'partnership_officer',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // إنشاء مستخدم مسؤول الإرشاد المهني
        User::create([
            'name' => 'مسؤول الإرشاد المهني',
            'email' => 'guidance@tripoliuniversity.edu.ly',
            'password' => Hash::make('password123'),
            'role' => 'career_guidance_officer',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // إنشاء مستخدم مسؤول التقييم والمتابعة
        User::create([
            'name' => 'مسؤول التقييم والمتابعة',
            'email' => 'evaluation@tripoliuniversity.edu.ly',
            'password' => Hash::make('password123'),
            'role' => 'evaluation_followup',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // إنشاء مستخدم خريج
        User::create([
            'name' => 'خريج تجريبي',
            'email' => 'graduate@tripoliuniversity.edu.ly',
            'password' => Hash::make('password123'),
            'role' => 'graduate',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // إنشاء مستخدم شركة
        User::create([
            'name' => 'ممثل شركة تجريبية',
            'email' => 'company@tripoliuniversity.edu.ly',
            'password' => Hash::make('password123'),
            'role' => 'company',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        echo "تم إنشاء المستخدمين بنجاح!\n";
    }
}