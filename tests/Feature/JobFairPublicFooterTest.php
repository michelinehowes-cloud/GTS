<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\JobFair;
use App\Models\JobFairCompany;
use App\Models\JobFairEvent;
use App\Models\JobFairProject;
use App\Models\JobFairSponsor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobFairPublicFooterTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_public_pages_render_unified_footer_strip_with_sponsors_only_and_not_participating_companies()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $compUser = User::factory()->create(['role' => 'company']);

        $fair = JobFair::create([
            'title'       => 'معرض التوظيف السنوي 2026',
            'event_date'  => now()->addDays(15),
            'location'    => 'طرابلس',
            'status'      => 'published',
            'created_by'  => $admin->id,
            'is_program_published' => true,
            'is_projects_published' => true,
        ]);

        // Participating company (NOT a sponsor)
        $participatingCompany = Company::create([
            'user_id' => $compUser->id,
            'name' => 'شركة مشهد المشاركة للاختبار فقط',
            'email' => $compUser->email,
            'phone' => '0912223344',
            'industry' => 'إعلام',
            'address' => 'طرابلس',
            'partnership_type' => 'employment',
            'partnership_status' => 'active',
            'is_approved' => true,
        ]);

        JobFairCompany::create([
            'job_fair_id' => $fair->id,
            'company_id' => $participatingCompany->id,
            'booth_number' => 'B4',
            'available_positions' => 2,
            'status' => 'confirmed',
        ]);

        // Official sponsor
        JobFairSponsor::create([
            'job_fair_id' => $fair->id,
            'name' => 'المؤسسة الراعية الذهبية الرسمية',
            'tier' => 'gold',
            'display_order' => 1,
            'is_active' => true,
        ]);

        $project = JobFairProject::create([
            'job_fair_id' => $fair->id,
            'title'       => 'مشروع تخرج الذكاء الاصطناعي',
            'status'      => 'published',
            'faculty'     => 'كلية تقنية المعلومات',
            'department'  => 'علوم الحاسوب',
            'graduation_year' => 2026,
            'summary'     => 'ملخص المشروع التقني',
        ]);

        $event = JobFairEvent::create([
            'job_fair_id'  => $fair->id,
            'title'        => 'ورشة عمل مسار التوظيف',
            'type'         => 'workshop',
            'start_time'   => now()->addDays(15)->setTime(10, 0),
            'end_time'     => now()->addDays(15)->setTime(12, 0),
            'speaker_name' => 'د. أحمد',
            'capacity'     => 100,
        ]);

        $urls = [
            route('job-fair.public', $fair->id),
            route('job-fair.public.program', $fair->id),
            route('job-fair.public.companies', $fair->id),
            route('job-fair.public.projects', $fair->id),
            route('job-fair.public.projects.show', $project->id),
            route('job-fair.public.events.show', $event->id),
        ];

        foreach ($urls as $url) {
            $response = $this->get($url);
            $response->assertStatus(200);

            $html = $response->getContent();
            $this->assertStringContainsString('lux-glass-strip', $html);
            $this->assertStringContainsString('رعاة المعرض', $html);

            // Extract ONLY the bottom footer strip HTML to assert sponsors vs participating companies
            $stripStart = strpos($html, '<div class="lux-glass-strip"');
            $this->assertNotFalse($stripStart, "Footer strip missing on {$url}");
            $stripHtml = substr($html, $stripStart);

            $this->assertStringContainsString('المؤسسة الراعية الذهبية الرسمية', $stripHtml);
            $this->assertStringNotContainsString('شركة مشهد المشاركة للاختبار فقط', $stripHtml);

            // Verify none of the old hardcoded demo pills exist
            $response->assertDontSee('sponsor-mini-pill');
            $response->assertDontSee('مصرف التجارة والتنمية');
            $response->assertDontSee('شركة الواحة للنفط');
        }
    }
}
