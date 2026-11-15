<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\JobFair;
use App\Models\JobFairCompany;
use App\Models\JobFairRegistration;
use App\Models\User;
use App\Models\GraduateData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CardsLogosSeparationTest extends TestCase
{
    use RefreshDatabase;

    public function test_graduate_id_card_contains_only_office_logo_and_not_job_fair_logo_inside_card()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create([
            'role' => 'graduate',
            'name' => 'محمد أحمد طرابلسي',
            'national_id' => '119900112233',
        ]);

        GraduateData::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'national_id' => '119900112233',
            'faculty' => 'كلية تقنية المعلومات',
            'major' => 'هندسة برمجيات',
            'gpa' => 3.85,
            'graduation_year' => 2025,
            'added_by' => $admin->id,
        ]);

        $fair = JobFair::create([
            'title' => 'معرض التوظيف السنوي 2026',
            'status' => 'published',
            'event_date' => now()->addDays(5),
            'location' => 'مجمع الكليات الطبية',
            'created_by' => $admin->id,
        ]);

        $reg = JobFairRegistration::updateOrCreate(
            [
                'job_fair_id' => $fair->id,
                'user_id' => $user->id,
            ],
            [
                'registration_number' => 1001,
                'qr_code' => 'TEST_QR_1001',
                'status' => 'registered',
            ]
        );

        $response = $this->actingAs($user)->get(route('graduate.id-card'));

        $response->assertStatus(200);

        // Graduate ID Card page has the graduate's details
        $response->assertSee('بطاقة الخريج الرقمية');
        $response->assertSee('محمد أحمد طرابلسي');
        $response->assertSee('119900112233');
        $response->assertSee('هوية رقمية معتمدة — جامعة طرابلس');

        // It provides an external link to the Job Fair ticket for registered fair
        $response->assertSee(route('job-fair.my-ticket', $reg->id));
        $response->assertSee('عرض بطاقة معرض التوظيف السنوي 2026');

        // The card itself should NOT contain ticket number #1001 inside the ID card container header
        $content = $response->getContent();
        
        // Find id-card-container
        $this->assertStringContainsString('id="id-card-container"', $content);
        
        // Inside id-card-container, check that office logo is present
        $cardContainer = substr($content, strpos($content, 'id="id-card-container"'));
        $cardContainer = substr($cardContainer, 0, strpos($cardContainer, '</style>'));
        
        $this->assertStringContainsString('logo.jpg', $cardContainer);
        // It must NOT have Job Fair logo or Waha logo inside the graduate card container
        $this->assertStringNotContainsString('job_fair_logo_white.png', $cardContainer);
        $this->assertStringNotContainsString('wahaexpo', $cardContainer);
    }

    public function test_job_fair_ticket_contains_office_logo_fair_logo_and_waha_logo()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create([
            'role' => 'graduate',
            'name' => 'سارة علي بن عثمان',
        ]);

        $fair = JobFair::create([
            'title' => 'معرض التوظيف السنوي 2026',
            'status' => 'published',
            'event_date' => now()->addDays(5),
            'location' => 'مجمع الكليات الطبية',
            'created_by' => $admin->id,
        ]);

        $reg = JobFairRegistration::updateOrCreate(
            [
                'job_fair_id' => $fair->id,
                'user_id' => $user->id,
            ],
            [
                'registration_number' => 2002,
                'qr_code' => 'TEST_QR_2002',
                'status' => 'registered',
            ]
        );

        $response = $this->actingAs($user)->get(route('job-fair.my-ticket', $reg->id));

        $response->assertStatus(200);

        // Header must have Office Logo
        $response->assertSee('office_logo_white.png');

        // Header must have Job Fair Logo
        $response->assertSee('job_fair_logo_white.png');

        // Header must have Waha Company Logo
        $response->assertSee('wahaexpo_horizontal_white.png');
        $response->assertSee('شركة الواحة لتنظيم المعارض والمؤتمرات');
        $response->assertSee('شركة الواحة للمعارض');
    }

    public function test_company_job_fairs_index_and_qr_scanner_render_with_responsive_rtl_layout()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $companyUser = User::factory()->create([
            'role' => 'company',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $company = Company::create([
            'user_id' => $companyUser->id,
            'name' => 'شركة التقنية الليبية',
            'email' => $companyUser->email,
            'is_approved' => true,
            'partnership_status' => 'active',
        ]);

        $fair = JobFair::create([
            'title' => 'معرض التوظيف 2026 - جامعة طرابلس',
            'status' => 'published',
            'event_date' => now()->addDays(5),
            'location' => 'موقف سيارات كلية الهندسة',
            'created_by' => $admin->id,
        ]);

        JobFairCompany::create([
            'job_fair_id' => $fair->id,
            'company_id' => $company->id,
            'booth_number' => 'A-12',
            'status' => 'confirmed',
        ]);

        $indexResponse = $this->actingAs($companyUser)->get(route('company.job-fairs.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('sub-actions-grid', false);
        $indexResponse->assertSee('موقف سيارات كلية الهندسة');
        $indexResponse->assertSee('div.uiverse-train-card .card-content', false);

        $scannerResponse = $this->actingAs($companyUser)->get(route('company.job-fairs.scanner', $fair->id));
        $scannerResponse->assertStatus(200);
        $scannerResponse->assertSee('padding-inline-end: 2.5rem;', false);
        $scannerResponse->assertSee('d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2', false);
        $scannerResponse->assertSee('id="result-success" class="result-card result-success d-none px-3 px-sm-4"', false);
    }
}
