<?php

namespace Tests\Feature;

use App\Models\JobFair;
use App\Models\JobFairEvent;
use App\Models\JobFairProject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobFairPublicFooterTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_public_pages_render_unified_footer_strip_without_hardcoded_demo_sponsors()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $fair = JobFair::create([
            'title'       => 'معرض التوظيف السنوي 2026',
            'event_date'  => now()->addDays(15),
            'location'    => 'طرابلس',
            'status'      => 'published',
            'created_by'  => $admin->id,
            'is_program_published' => true,
            'is_projects_published' => true,
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

            // Verify the unified dynamic strip is present
            $response->assertSee('lux-glass-strip');
            $response->assertSee('رعاة المعرض');

            // Verify none of the old hardcoded demo pills exist
            $response->assertDontSee('sponsor-mini-pill');
            $response->assertDontSee('مصرف التجارة والتنمية');
            $response->assertDontSee('شركة الواحة للنفط');
        }
    }
}
