<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Training;
use App\Models\Company;
use App\Models\Certificate;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CriticalFlowsTest extends TestCase
{
    /**
     * التحقق من تحميل صفحة تسجيل الخريجين بنجاح
     */
    public function test_graduate_registration_page_loads_successfully()
    {
        $response = $this->get(route('graduate.register'));

        $response->assertRedirect('/?open_register=1');
    }

    /**
     * التحقق من منع الزائر غير المسجل من الوصول لتدريبات المنسق
     */
    public function test_unauthenticated_user_cannot_access_coordinator_trainings()
    {
        $response = $this->get('/coordinator/trainings');

        $response->assertRedirect('/login');
    }

    /**
     * التحقق من منع مستخدم بدور خريج من الوصول لشاشة منسق التدريب
     */
    public function test_graduate_cannot_access_coordinator_trainings()
    {
        $graduate = User::factory()->create([
            'role' => 'graduate',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $response = $this->actingAs($graduate)->get('/coordinator/trainings');

        // يُعاد توجيهه للوحة التحكم الرئيسية مع رسالة خطأ بالصلاحيات
        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error');
    }

    /**
     * التحقق من تحميل شاشة تدريبات المنسق بنجاح بدون خطأ 500
     */
    public function test_coordinator_can_view_trainings_without_500_error()
    {
        $coordinator = User::factory()->create([
            'role' => 'training_coordinator',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $company = Company::factory()->create();

        Training::factory()->create([
            'coordinator_id' => $coordinator->id,
            'company_id' => $company->id,
            'title' => 'دورة تدريبية اختبارية للمنسق',
        ]);

        $response = $this->actingAs($coordinator)->get('/coordinator/trainings');

        $response->assertStatus(200);
        $response->assertSee('البرامج التدريبية');
    }

    /**
     * التحقق من تحميل شاشة تدريبات الإدارة بنجاح بدون خطأ 500
     */
    public function test_admin_can_view_admin_trainings_without_500_error()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.trainings'));

        $response->assertStatus(200);
    }

    /**
     * التحقق من فحص شهادة غير موجودة لا يسبب 500
     */
    public function test_verifying_invalid_certificate_returns_graceful_response()
    {
        $response = $this->get('/certificates/verify/NON-EXISTENT-CODE-999');

        // لا يجب أن يرمي 500 تحت أي ظرف
        $this->assertNotEquals(500, $response->getStatusCode());
    }

    /**
     * التحقق من تحميل معرض المشاريع العامة بدون أخطاء
     */
    public function test_job_fair_projects_index_loads_gracefully()
    {
        $response = $this->get('/job-fair/projects');

        // صفحة المشاريع العامة تعمل بنجاح بدون خطأ
        $this->assertNotEquals(500, $response->getStatusCode());
    }
}
