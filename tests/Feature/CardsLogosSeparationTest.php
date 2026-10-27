<?php

namespace Tests\Feature;

use App\Models\JobFair;
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

        $reg = JobFairRegistration::create([
            'job_fair_id' => $fair->id,
            'user_id' => $user->id,
            'registration_number' => 1001,
            'qr_code' => 'TEST_QR_1001',
            'status' => 'registered',
        ]);

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

        $reg = JobFairRegistration::create([
            'job_fair_id' => $fair->id,
            'user_id' => $user->id,
            'registration_number' => 2002,
            'qr_code' => 'TEST_QR_2002',
            'status' => 'registered',
        ]);

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
}
