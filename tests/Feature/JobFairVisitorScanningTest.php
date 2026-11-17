<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\JobFair;
use App\Models\JobFairCompany;
use App\Models\JobFairRegistration;
use App\Models\JobFairVisit;
use App\Models\JobFairVisitor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobFairVisitorScanningTest extends TestCase
{
    use RefreshDatabase;

    public function test_gate_attendance_scanner_successfully_checks_in_visitor_by_ticket_number()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $fair = JobFair::create([
            'title' => 'معرض التوظيف السنوي 2026',
            'status' => 'published',
            'event_date' => now()->addDays(2),
            'location' => 'مجمع الكليات الطبية',
            'created_by' => $admin->id,
        ]);

        $visitor = JobFairVisitor::create([
            'job_fair_id' => $fair->id,
            'name' => 'طارق فرج الشريف',
            'phone' => '0912233445',
            'email' => 'tarek@example.com',
            'visitor_type' => 'job_seeker',
            'specialization' => 'هندسة مدنية',
            'organization' => 'شركة المقاولات العامة',
            'city' => 'طرابلس',
            'ticket_number' => 'VIS-2026-TK890',
            'qr_code' => route('job-fair.visitor.ticket', 'VIS-2026-TK890'),
            'attended' => false,
        ]);

        // 1. Initial Gate Scan via POST to admin/job-fair/check-in
        $response = $this->actingAs($admin)->postJson(route('job-fair.admin.check-in'), [
            'job_fair_id' => $fair->id,
            'code' => 'VIS-2026-TK890',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'is_visitor' => true,
            'already_in' => false,
            'graduate' => 'طارق فرج الشريف',
            'registration_number' => 'VIS-2026-TK890',
        ]);

        $visitor->refresh();
        $this->assertTrue($visitor->attended);
        $this->assertNotNull($visitor->check_in_at);

        // 2. Repeat Gate Scan (warning / already in)
        $repeatResponse = $this->actingAs($admin)->postJson(route('job-fair.admin.check-in'), [
            'job_fair_id' => $fair->id,
            'code' => 'VIS-2026-TK890',
        ]);

        $repeatResponse->assertStatus(200);
        $repeatResponse->assertJson([
            'success' => true,
            'is_visitor' => true,
            'already_in' => true,
            'graduate' => 'طارق فرج الشريف',
        ]);
    }

    public function test_gate_attendance_scanner_checks_in_visitor_by_full_ticket_url()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $fair = JobFair::create([
            'title' => 'معرض التوظيف السنوي 2026',
            'status' => 'published',
            'event_date' => now()->addDays(2),
            'location' => 'مجمع الكليات الطبية',
            'created_by' => $admin->id,
        ]);

        $visitor = JobFairVisitor::create([
            'job_fair_id' => $fair->id,
            'name' => 'منى سالم الزنتاني',
            'phone' => '0925566778',
            'email' => 'mona@example.com',
            'visitor_type' => 'student',
            'specialization' => 'علوم حاسوب',
            'organization' => 'جامعة طرابلس',
            'city' => 'طرابلس',
            'ticket_number' => 'VIS-2026-MN123',
            'qr_code' => route('job-fair.visitor.ticket', 'VIS-2026-MN123'),
            'attended' => false,
        ]);

        $ticketUrl = route('job-fair.visitor.ticket', 'VIS-2026-MN123');

        $response = $this->actingAs($admin)->postJson(route('job-fair.admin.check-in'), [
            'job_fair_id' => $fair->id,
            'code' => $ticketUrl,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'is_visitor' => true,
            'graduate' => 'منى سالم الزنتاني',
            'registration_number' => 'VIS-2026-MN123',
        ]);

        $visitor->refresh();
        $this->assertTrue($visitor->attended);
    }

    public function test_company_scanner_successfully_records_visitor_visit()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $companyUser = User::factory()->create([
            'role' => 'company',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $company = Company::create([
            'user_id' => $companyUser->id,
            'name' => 'شركة الواحة للبرمجيات',
            'email' => $companyUser->email,
            'is_approved' => true,
            'partnership_status' => 'active',
        ]);

        $fair = JobFair::create([
            'title' => 'معرض التوظيف السنوي 2026',
            'status' => 'published',
            'event_date' => now()->addDays(2),
            'location' => 'مجمع الكليات الطبية',
            'created_by' => $admin->id,
        ]);

        JobFairCompany::create([
            'job_fair_id' => $fair->id,
            'company_id' => $company->id,
            'booth_number' => 'B-04',
            'status' => 'confirmed',
        ]);

        $visitor = JobFairVisitor::create([
            'job_fair_id' => $fair->id,
            'name' => 'خالد عبد الله محمود',
            'phone' => '0937788990',
            'email' => 'khaled@example.com',
            'visitor_type' => 'job_seeker',
            'specialization' => 'أمن سيبراني',
            'organization' => 'خريج تقنية المعلومات',
            'city' => 'طرابلس',
            'ticket_number' => 'VIS-2026-KH777',
            'qr_code' => route('job-fair.visitor.ticket', 'VIS-2026-KH777'),
            'attended' => false,
        ]);

        // 1. Company scans visitor's card
        $response = $this->actingAs($companyUser)->postJson(route('company.job-fairs.store-visit', $fair->id), [
            'code' => 'VIS-2026-KH777',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'already_visited' => false,
            'graduate_name' => 'خالد عبد الله محمود',
        ]);

        // Verify visit record exists in JobFairVisit
        $visit = JobFairVisit::where('job_fair_id', $fair->id)
            ->where('company_id', $companyUser->id)
            ->first();
        $this->assertNotNull($visit);

        // 2. Company scans visitor again (already visited)
        $repeatResponse = $this->actingAs($companyUser)->postJson(route('company.job-fairs.store-visit', $fair->id), [
            'code' => 'VIS-2026-KH777',
        ]);

        $repeatResponse->assertStatus(200);
        $repeatResponse->assertJson([
            'success' => true,
            'already_visited' => true,
            'graduate_name' => 'خالد عبد الله محمود',
        ]);
    }

    public function test_visitor_ticket_page_renders_without_errors()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $fair = JobFair::create([
            'title' => 'معرض التوظيف السنوي 2026',
            'status' => 'published',
            'event_date' => now()->addDays(2),
            'location' => 'جامعة طرابلس',
            'created_by' => $admin->id,
        ]);

        $visitor = JobFairVisitor::create([
            'job_fair_id' => $fair->id,
            'name' => 'سهام يوسف',
            'phone' => '0941122334',
            'email' => 'seham@example.com',
            'visitor_type' => 'general',
            'specialization' => 'إدارة أعمال',
            'city' => 'طرابلس',
            'ticket_number' => 'VIS-2026-SH444',
            'qr_code' => route('job-fair.visitor.ticket', 'VIS-2026-SH444'),
            'attended' => true,
        ]);

        $response = $this->get(route('job-fair.visitor.ticket', 'VIS-2026-SH444'));

        $response->assertStatus(200);
        $response->assertSee('سهام يوسف');
        $response->assertSee('VIS-2026-SH444');
        $response->assertSee('بطاقة زائر المعرض');
    }

    public function test_fair_stats_accurately_counts_both_graduates_and_visitors_attendance()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $fair = JobFair::create([
            'title' => 'معرض التوظيف السنوي 2026',
            'status' => 'published',
            'event_date' => now()->addDays(2),
            'location' => 'جامعة طرابلس',
            'created_by' => $admin->id,
        ]);

        // 1. خريج مسجل وحاضر
        $gradUser = User::factory()->create(['role' => 'graduate']);
        JobFairRegistration::updateOrCreate(
            ['job_fair_id' => $fair->id, 'user_id' => $gradUser->id],
            [
                'registration_number' => 'JF-2026-001',
                'qr_code' => 'QR-JF-001',
                'status' => 'attended',
                'attended' => true,
            ]
        );

        // 2. خريج مسجل ولم يحضر
        $absentGrad = User::factory()->create(['role' => 'graduate']);
        JobFairRegistration::updateOrCreate(
            ['job_fair_id' => $fair->id, 'user_id' => $absentGrad->id],
            [
                'registration_number' => 'JF-2026-002',
                'qr_code' => 'QR-JF-002',
                'status' => 'registered',
                'attended' => false,
            ]
        );

        // 3. زائر حاضر
        JobFairVisitor::create([
            'job_fair_id' => $fair->id,
            'name' => 'زائر حاضر',
            'phone' => '0910000001',
            'ticket_number' => 'VIS-2026-ATT1',
            'attended' => true,
            'check_in_at' => now(),
        ]);

        // 4. زائر مسجل ولم يحضر
        JobFairVisitor::create([
            'job_fair_id' => $fair->id,
            'name' => 'زائر غائب',
            'phone' => '0910000002',
            'ticket_number' => 'VIS-2026-ABS2',
            'attended' => false,
        ]);

        $response = $this->actingAs($admin)->get(route('job-fair.admin.attendance', $fair->id));
        $response->assertStatus(200);

        $stats = $response->viewData('stats');
        $this->assertEquals(4, $stats['total_registered'], 'إجمالي المسجلين يجب أن يجمع الخريجين (2) والزوار (2)');
        $this->assertEquals(2, $stats['total_attended'], 'إجمالي الحضور يجب أن يجمع الخريج الحاضر (1) والزائر الحاضر (1)');
        $this->assertEquals(1, $stats['attended_graduates']);
        $this->assertEquals(1, $stats['attended_visitors']);
    }
}
