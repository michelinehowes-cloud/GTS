<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Notification;
use Illuminate\Support\Facades\RateLimiter;

class CompanyRegistrationTest extends TestCase
{
    /**
     * اختبار عرض صفحة تسجيل شركة بنجاح
     */
    public function test_company_registration_page_is_accessible()
    {
        $response = $this->get(route('company.register'));
        $response->assertStatus(200);
        $response->assertSee('طلب انضمام وتسجيل شركة');
    }

    /**
     * اختبار عملية تسجيل شركة جديدة وحفظ البيانات وإرسال الاستجابة فورياً
     */
    public function test_company_can_register_successfully_without_hanging()
    {
        $uniq = uniqid();
        $email = "company.test.{$uniq}@example.com";
        $companyName = "شركة التجربة للتقنية {$uniq}";

        // تأكد من تهيئة معدل المحاولات
        RateLimiter::clear('register-company|127.0.0.1');

        // إنشاء مستخدم مسؤول لاستقبال الإشعار
        User::create([
            'name' => 'مسؤول الشراكات للاختبار',
            'email' => "admin.test.{$uniq}@example.com",
            'password' => bcrypt('password'),
            'role' => 'partnership_officer',
        ]);

        $payload = [
            'company_name' => $companyName,
            'industry' => 'تقنية المعلومات والاتصالات',
            'city' => 'طرابلس',
            'address' => 'طريق الشط، برج طرابلس',
            'phone' => '0213344556',
            'website' => 'https://example-tech.ly',
            'description' => 'شركة رائدة في تقديم الحلول البرمجية والتدريب التقني.',
            'partnership_types' => ['employment', 'training'],
            'contact_name' => 'م. طارق الزنتاني',
            'contact_position' => 'مدير الموارد البشرية',
            'contact_phone' => '0912345678',
            'email' => $email,
            'password' => 'Secret1234!',
            'password_confirmation' => 'Secret1234!',
        ];

        $response = $this->post(route('company.register.store'), $payload);

        // يجب التوجيه لصفحة النجاح مباشرة
        $response->assertRedirect(route('company.register.success'));

        // التأكد من حفظ المستخدم في قاعدة البيانات
        $this->assertDatabaseHas('users', [
            'email' => $email,
            'role' => 'company',
            'is_approved' => 0,
            'is_active' => 0,
        ]);

        // التأكد من حفظ سجل الشركة بالبيانات الصحيحة
        $this->assertDatabaseHas('companies', [
            'name' => $companyName,
            'email' => $email,
            'is_approved' => 0,
            'partnership_type' => 'employment',
            'partnership_status' => 'under_review',
            'contact_person' => 'م. طارق الزنتاني',
        ]);

        // التأكد من إنشاء إشعار في قاعدة البيانات
        $this->assertDatabaseHas('notifications', [
            'title' => 'طلب تسجيل شركة جديدة',
        ]);

        // التأكد من إمكانية فتح صفحة النجاح
        $successResponse = $this->get(route('company.register.success'));
        $successResponse->assertStatus(200);
        $successResponse->assertSee('تم استلام طلب تسجيل شركتكم بنجاح');
    }
}
