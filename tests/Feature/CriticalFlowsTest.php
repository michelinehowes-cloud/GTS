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

    /**
     * التحقق من تحميل النموذج المعتمد لمشاريع التخرج بنجاح
     */
    public function test_graduation_project_submission_form_loads_successfully()
    {
        $response = $this->get(route('job-fair.public.projects.submit'));

        $response->assertStatus(200);
        $response->assertSee('النموذج المعتمد');
        $response->assertSee('استمارة تقديم وتسجيل مشروع التخرج');
    }

    /**
     * التحقق من نجاح إرسال مشروع التخرج وتخزينه وعرض صفحة التأكيد
     */
    public function test_graduation_project_submission_stores_and_redirects_successfully()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $fair = \App\Models\JobFair::create([
            'title' => 'معرض التوظيف السنوي جامعة طرابلس',
            'location' => 'جامعة طرابلس - القاطع ب',
            'academic_year' => '2026',
            'event_date' => now()->addDays(10),
            'status' => 'published',
            'created_by' => $admin->id,
        ]);

        $postData = [
            'job_fair_id' => $fair->id,
            'title' => 'نظام إدارة مشاريع التخرج المعتمد',
            'faculty' => 'كلية تقنية المعلومات',
            'department' => 'هندسة البرمجيات',
            'graduation_year' => 2026,
            'project_type' => 'تطبيق ويب وسحابي (Web Application)',
            'main_category' => 'تقنية المعلومات والبرمجيات',
            'supervisor_name' => 'د. أحمد الفيتوري',
            'supervisor_title' => 'أستاذ مشارك',
            'summary' => 'نبذة ملخصة ومختصرة عن النظام المعتمد لمشاريع التخرج بجامعة طرابلس.',
            'problem_statement' => 'صعوبة أرشفة وتوثيق ابتكارات ومشاريع التخرج ورقياً.',
            'solution_statement' => 'بناء منصة رقمية موحدة تعتمد المعايير المؤسسية.',
            'objectives' => 'توثيق المشاريع وأرشفتها.',
            'description' => 'وصف تفصيلي شامل لكافة عناصر المشروع ومنهجية بنائه واختباره.',
            'technical_specifications' => 'Laravel, MySQL, Bootstrap',
            'key_outcomes' => 'أرشفة فورية وسهولة وصول',
            'market_viability' => 'جاهز للتطبيق والتوسع',
            'team_members_raw' => "طارق محمد\nسهيل علي",
            'contact_email' => 'graduation-team@uot.edu.ly',
            'student_university_id' => 'UOT-2026-999',
            'whatsapp_phone' => '0912345678',
            'prototype_status' => 'منتج كامل قابل للتشغيل والإنتاج (Production Ready / MVP)',
        ];

        $response = $this->post(route('job-fair.public.projects.store-submission'), $postData);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        // التأكد من حفظ المشروع بحالة pending
        $this->assertDatabaseHas('job_fair_projects', [
            'title' => 'نظام إدارة مشاريع التخرج المعتمد',
            'status' => 'pending',
            'student_university_id' => 'UOT-2026-999',
        ]);

        // متابعة التوجيه والتأكد من ظهور صفحة التأكيد المعتمدة
        $followResponse = $this->get($response->headers->get('Location'));
        $followResponse->assertStatus(200);
        $followResponse->assertSee('تم استلام طلب المشروع بنجاح');
        $followResponse->assertSee('نظام إدارة مشاريع التخرج المعتمد');
    }

    /**
     * التحقق من تحميل صفحة مركز الإشعارات للمستخدم المسجل
     */
    public function test_notification_index_loads_successfully()
    {
        $user = User::factory()->create([
            'role' => 'graduate',
            'is_active' => true,
            'is_approved' => true,
        ]);

        \App\Models\Notification::create([
            'user_id' => $user->id,
            'title' => 'إشعار اختباري تجريبي',
            'message' => 'محتوى الرسالة الخاصة بالإشعار التجريبي.',
            'type' => 'info',
            'is_read' => false,
        ]);

        $response = $this->actingAs($user)->get(route('notifications.index'));

        $response->assertStatus(200);
        $response->assertSee('مركز الإشعارات');
        $response->assertSee('إشعار اختباري تجريبي');
    }

    /**
     * التحقق من تحديد إشعار فردي كمقروء وإرجاع unread_count المحدث
     */
    public function test_marking_notification_as_read_via_patch_returns_success_and_unread_count()
    {
        $user = User::factory()->create([
            'role' => 'graduate',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $notif1 = \App\Models\Notification::create([
            'user_id' => $user->id,
            'title' => 'إشعار 1',
            'message' => 'رسالة 1',
            'type' => 'info',
            'is_read' => false,
        ]);

        $notif2 = \App\Models\Notification::create([
            'user_id' => $user->id,
            'title' => 'إشعار 2',
            'message' => 'رسالة 2',
            'type' => 'warning',
            'is_read' => false,
        ]);

        $response = $this->actingAs($user)->patchJson("/notifications/{$notif1->id}/read");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'id' => $notif1->id,
            'unread_count' => 1,
        ]);

        $this->assertDatabaseHas('notifications', [
            'id' => $notif1->id,
            'is_read' => 1,
        ]);
    }

    /**
     * التحقق من تحديد كافة الإشعارات كمقروءة وإرجاع unread_count مساوياً لصفر
     */
    public function test_marking_all_notifications_as_read_returns_success_and_zero_unread_count()
    {
        $user = User::factory()->create([
            'role' => 'graduate',
            'is_active' => true,
            'is_approved' => true,
        ]);

        \App\Models\Notification::create([
            'user_id' => $user->id,
            'title' => 'إشعار غير مقروء أ',
            'message' => 'نص أ',
            'type' => 'info',
            'is_read' => false,
        ]);

        \App\Models\Notification::create([
            'user_id' => $user->id,
            'title' => 'إشعار غير مقروء ب',
            'message' => 'نص ب',
            'type' => 'success',
            'is_read' => false,
        ]);

        $response = $this->actingAs($user)->patchJson('/notifications/mark-all-read');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'unread_count' => 0,
        ]);

        $this->assertEquals(0, \App\Models\Notification::forUser($user->id)->unread()->count());
    }

    /**
     * التحقق من تحميل صفحة إدارة مشاريع المعرض للأدمن وظهور التصميم المعتمد والمودال المتناسق
     */
    public function test_admin_job_fair_projects_loads_with_approved_theme_and_modals()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $fair = \App\Models\JobFair::create([
            'title' => 'معرض التوظيف السنوي 2026',
            'location' => 'جامعة طرابلس',
            'academic_year' => '2026',
            'event_date' => now()->addDays(5),
            'status' => 'published',
            'created_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->get(route('job-fair.admin.projects.index', $fair));

        $response->assertStatus(200);
        $response->assertSee('إضافة مشروع تخرج جديد');
        $response->assertSee('uot-modal-content');
        $response->assertSee('addProjectModal');
        $response->assertSee('editProjectModal');
    }

    /**
     * التحقق من تحميل صفحة إدارة فعاليات المعرض للأدمن وظهور التصميم المعتمد والمودال المتناسق
     */
    public function test_admin_job_fair_events_loads_with_approved_theme_and_modals()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $fair = \App\Models\JobFair::create([
            'title' => 'معرض التوظيف السنوي 2026',
            'location' => 'جامعة طرابلس',
            'academic_year' => '2026',
            'event_date' => now()->addDays(5),
            'status' => 'published',
            'created_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->get(route('job-fair.admin.events.index', $fair));

        $response->assertStatus(200);
        $response->assertSee('إضافة فعالية علمية جديدة');
        $response->assertSee('uot-modal-content');
        $response->assertSee('addEventModal');
        $response->assertSee('editEventModal');
    }
}

