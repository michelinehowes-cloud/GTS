<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\JobFair;
use App\Models\Permission;

class CustomStaffAndJobFairPermissionsTest extends TestCase
{
    private function ensurePermissionsExist(): void
    {
        $perms = [
            'job_fair.view' => 'استعراض معرض التوظيف',
            'job_fair.create' => 'إنشاء معرض توظيف',
            'job_fair.edit' => 'تعديل بيانات وإعدادات المعرض',
            'job_fair.delete' => 'حذف معرض التوظيف',
            'job_fair.manage' => 'إدارة أجنحة وشركات المعرض',
            'job_fair.events' => 'إدارة الفعاليات وورش العمل',
            'job_fair.projects' => 'إدارة مشاريع التخرج بالمعرض',
            'job_fair.sponsors' => 'إدارة الرعاة والداعمين',
            'job_fair.attendance' => 'مسح التذاكر وتسجيل الدخول',
            'training.view' => 'استعراض برامج التدريب',
        ];

        foreach ($perms as $name => $displayName) {
            Permission::firstOrCreate(
                ['name' => $name],
                ['display_name' => $displayName, 'module' => 'job_fair']
            );
        }
    }

    /** @test */
    public function custom_staff_user_has_correct_role_name_and_dashboard_route()
    {
        $this->ensurePermissionsExist();

        $staff = User::factory()->create([
            'role' => 'staff',
            'is_active' => true,
            'is_approved' => true,
        ]);
        $staff->givePermission('training.view');

        $this->assertEquals('موظف مخصص الصلاحيات', $staff->role_arabic);
        $this->assertEquals(route('staff.dashboard'), $staff->dashboard_route);

        $response = $this->actingAs($staff)->get('/dashboard');
        $response->assertRedirect(route('staff.dashboard'));
    }

    /** @test */
    public function custom_staff_dashboard_displays_proper_title_and_badge()
    {
        $this->ensurePermissionsExist();

        $staff = User::factory()->create([
            'role' => 'staff',
            'is_active' => true,
            'is_approved' => true,
        ]);
        $staff->givePermission('training.view');

        $response = $this->actingAs($staff)->get(route('staff.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('لوحة تحكم الموظف (صلاحيات مخصصة)');
        $response->assertSee('موظف مخصص الصلاحيات');
    }

    /** @test */
    public function user_with_only_job_fair_view_can_access_index_and_show()
    {
        $this->ensurePermissionsExist();

        $viewer = User::factory()->create([
            'role' => 'staff',
            'is_active' => true,
            'is_approved' => true,
        ]);
        $viewer->givePermission('job_fair.view');

        $fair = JobFair::create([
            'title' => 'معرض التوظيف الاختباري',
            'status' => 'published',
            'event_date' => now()->addDays(5)->toDateString(),
            'location' => 'طرابلس',
            'created_by' => $viewer->id,
        ]);

        $this->assertTrue($viewer->canViewJobFair());
        $this->assertFalse($viewer->canManageJobFair());
        $this->assertFalse($viewer->canCreateJobFair());
        $this->assertFalse($viewer->canEditJobFair());
        $this->assertFalse($viewer->canDeleteJobFair());
        $this->assertFalse($viewer->canManageJobFairEvents());
        $this->assertFalse($viewer->canManageJobFairProjects());
        $this->assertFalse($viewer->canManageJobFairCompanies());
        $this->assertFalse($viewer->canManageJobFairSponsors());
        $this->assertFalse($viewer->canManageJobFairAttendance());

        // Index page is accessible
        $indexResponse = $this->actingAs($viewer)->get('/admin/job-fair');
        $indexResponse->assertStatus(200);

        // Show page is accessible
        $showResponse = $this->actingAs($viewer)->get("/admin/job-fair/{$fair->id}");
        $showResponse->assertStatus(200);
    }

    /** @test */
    public function user_with_only_job_fair_view_is_forbidden_from_all_mutations()
    {
        $this->ensurePermissionsExist();

        $viewer = User::factory()->create([
            'role' => 'staff',
            'is_active' => true,
            'is_approved' => true,
        ]);
        $viewer->givePermission('job_fair.view');

        $fair = JobFair::create([
            'title' => 'معرض الحماية الاختباري',
            'status' => 'published',
            'event_date' => now()->addDays(5)->toDateString(),
            'location' => 'طرابلس',
            'created_by' => $viewer->id,
        ]);

        // Cannot access create page or store fair
        $this->actingAs($viewer)->get('/admin/job-fair/create')->assertStatus(403);
        $this->actingAs($viewer)->post('/admin/job-fair', [
            'title' => 'معرض غير مصرح',
            'event_date' => now()->toDateString(),
            'location' => 'طرابلس',
        ])->assertStatus(403);

        // Cannot access edit page or update fair
        $this->actingAs($viewer)->get("/admin/job-fair/{$fair->id}/edit")->assertStatus(403);
        $this->actingAs($viewer)->put("/admin/job-fair/{$fair->id}", [
            'title' => 'تعديل غير مصرح',
        ])->assertStatus(403);

        // Cannot update fair status
        $this->actingAs($viewer)->post("/admin/job-fair/{$fair->id}/status", [
            'status' => 'draft',
        ])->assertStatus(403);

        // Cannot reset attendance
        $this->actingAs($viewer)->post("/admin/job-fair/{$fair->id}/reset-attendance")->assertStatus(403);

        // Cannot add company
        $this->actingAs($viewer)->post("/admin/job-fair/{$fair->id}/companies", [
            'company_id' => 999,
        ])->assertStatus(403);

        // Cannot add event
        $this->actingAs($viewer)->post("/admin/job-fair/{$fair->id}/events", [
            'title' => 'حدث غير مصرح',
            'type' => 'workshop',
        ])->assertStatus(403);

        // Cannot add project
        $this->actingAs($viewer)->post("/admin/job-fair/{$fair->id}/projects", [
            'title' => 'مشروع غير مصرح',
            'college' => 'الهندسة',
            'major' => 'الحاسوب',
            'supervisor_name' => 'مشرف',
            'team_members_text' => 'عضو',
            'abstract' => 'نبذة',
            'academic_year' => '2026',
        ])->assertStatus(403);

        // Cannot remove company
        $company = \App\Models\Company::create([
            'name' => 'شركة تجريبية',
            'email' => 'test_jf_' . uniqid() . '@company.ly',
        ]);
        $this->actingAs($viewer)->delete("/admin/job-fair/{$fair->id}/companies/{$company->id}")->assertStatus(403);
    }

    /** @test */
    public function user_with_job_fair_edit_can_access_edit_form()
    {
        $this->ensurePermissionsExist();

        $editor = User::factory()->create([
            'role' => 'staff',
            'is_active' => true,
            'is_approved' => true,
        ]);
        $editor->syncPermissions(['job_fair.view', 'job_fair.edit']);

        $fair = JobFair::create([
            'title' => 'معرض التعديل المصرح',
            'status' => 'published',
            'event_date' => now()->addDays(5)->toDateString(),
            'location' => 'طرابلس',
            'created_by' => $editor->id,
        ]);

        $this->assertTrue($editor->canEditJobFair());
        $response = $this->actingAs($editor)->get("/admin/job-fair/{$fair->id}/edit");
        $response->assertStatus(200);
    }
}
