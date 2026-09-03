<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $password = Hash::make('password123');

        // 1. حساب مدير النظام الأساسي (admin@tripoliuniversity.edu.ly)
        User::updateOrCreate(
            ['email' => 'admin@tripoliuniversity.edu.ly'],
            [
                'name' => 'مدير النظام العام',
                'password' => $password,
                'role' => 'admin',
                'is_active' => true,
                'is_approved' => true,
                'email_verified_at' => now(),
            ]
        );

        // 2. حساب مدير النظام الاحتياطي/المعتاد (admin@admin.com)
        User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'مدير النظام',
                'password' => $password,
                'role' => 'admin',
                'is_active' => true,
                'is_approved' => true,
                'email_verified_at' => now(),
            ]
        );

        // 3. منسق التدريب
        User::updateOrCreate(
            ['email' => 'training@tripoliuniversity.edu.ly'],
            [
                'name' => 'منسق التدريب',
                'password' => $password,
                'role' => 'training_coordinator',
                'is_active' => true,
                'is_approved' => true,
                'email_verified_at' => now(),
            ]
        );

        // 4. مسؤول الشراكات والتوظيف
        User::updateOrCreate(
            ['email' => 'partnership@tripoliuniversity.edu.ly'],
            [
                'name' => 'مسؤول الشراكات',
                'password' => $password,
                'role' => 'partnership_officer',
                'is_active' => true,
                'is_approved' => true,
                'email_verified_at' => now(),
            ]
        );

        // 5. مسؤول الإرشاد والتوجيه المهني
        User::updateOrCreate(
            ['email' => 'guidance@tripoliuniversity.edu.ly'],
            [
                'name' => 'مسؤول الإرشاد المهني',
                'password' => $password,
                'role' => 'career_guidance_officer',
                'is_active' => true,
                'is_approved' => true,
                'email_verified_at' => now(),
            ]
        );

        // 6. مسؤول التقييم والمتابعة
        User::updateOrCreate(
            ['email' => 'evaluation@tripoliuniversity.edu.ly'],
            [
                'name' => 'مسؤول التقييم والمتابعة',
                'password' => $password,
                'role' => 'evaluation_followup',
                'is_active' => true,
                'is_approved' => true,
                'email_verified_at' => now(),
            ]
        );

        // 7. مسؤول الميديا والإعلام
        User::updateOrCreate(
            ['email' => 'media@tripoliuniversity.edu.ly'],
            [
                'name' => 'مسؤول الميديا والإعلام',
                'password' => $password,
                'role' => 'media_officer',
                'is_active' => true,
                'is_approved' => true,
                'email_verified_at' => now(),
            ]
        );

        // 8. حساب خريج معتمد ونشط
        User::updateOrCreate(
            ['email' => 'graduate@tripoliuniversity.edu.ly'],
            [
                'name' => 'خريج تجريبي (معتمد)',
                'password' => $password,
                'role' => 'graduate',
                'is_active' => true,
                'is_approved' => true,
                'graduation_year' => 2025,
                'major' => 'تقنية معلومات',
                'faculty' => 'كلية تقنية المعلومات',
                'email_verified_at' => now(),
            ]
        );

        // 9. حساب شركة معتمدة ونشطة
        $companyUser = User::updateOrCreate(
            ['email' => 'company@tripoliuniversity.edu.ly'],
            [
                'name' => 'شركة طرابلس للتقنية (حساب تجريبي)',
                'password' => $password,
                'role' => 'company',
                'is_active' => true,
                'is_approved' => true,
                'email_verified_at' => now(),
            ]
        );

        // التأكد من ربط حساب الشركة بسجل شركة مفعلة
        Company::updateOrCreate(
            ['email' => 'company@tripoliuniversity.edu.ly'],
            [
                'user_id' => $companyUser->id,
                'name' => 'شركة طرابلس للتقنية المتقدمة',
                'phone' => '0912345678',
                'address' => 'طرابلس، ليبيا',
                'website' => 'https://tripoli-tech.ly',
                'description' => 'شركة رائدة معتمدة في حلول التحول الرقمي.',
                'is_approved' => true,
                'partnership_status' => 'active',
                'partnership_start_date' => now(),
                'partnership_end_date' => now()->addYears(3),
                'contact_person' => 'م. طارق المحمودي',
                'contact_email' => 'company@tripoliuniversity.edu.ly',
            ]
        );

        echo "تم إنشاء وتحديث المستخدمين الافتراضيين بنجاح مع تفعيل وتعيين كلمة المرور: password123\n";
    }
}