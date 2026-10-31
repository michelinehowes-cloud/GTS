<?php

namespace Tests\Feature;

use App\Models\JobFair;
use App\Models\JobFairEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class JobFairMultiSpeakerEventsTest extends TestCase
{
    use RefreshDatabase;

    private function makeTinyPngUpload(string $filename): UploadedFile
    {
        $pngBytes = base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='
        );

        return UploadedFile::fake()->createWithContent($filename, $pngBytes);
    }

    public function test_admin_can_create_and_update_multi_speaker_panel_and_all_pages_render_all_panelist_photos_and_bios()
    {
        Storage::fake('public');

        $admin = User::factory()->create(['role' => 'admin']);

        $fair = JobFair::create([
            'title'                 => 'معرض التوظيف 2026 - جامعة طرابلس',
            'event_date'            => now()->addDays(15),
            'location'              => 'جامعة طرابلس',
            'status'                => 'published',
            'created_by'            => $admin->id,
            'is_program_published'  => true,
            'is_projects_published' => true,
        ]);

        $storePayload = [
            'title'       => 'مستقبل سوق العمل الليبي والتحول الرقمي',
            'type'        => 'panel_discussion',
            'description' => 'جلسة حوارية موسعة تجمع قادة القطاعين الأكاديمي والخاص.',
            'topics'      => "مهارات المستقبل\nالذكاء الاصطناعي في المؤسسات",
            'start_time'  => now()->addDays(15)->setTime(10, 0)->format('Y-m-d\TH:i'),
            'end_time'    => now()->addDays(15)->setTime(12, 0)->format('Y-m-d\TH:i'),
            'location'    => 'المدرج الرئيسي',
            'capacity'    => 200,
            'status'      => 'open',
            'is_featured' => '1',
            'speakers'    => [
                0 => [
                    'name'  => 'د. عبد الرحمن المحمودي',
                    'role'  => 'مدير الجلسة',
                    'title' => 'أستاذ الإدارة الاستراتيجية',
                    'bio'   => 'خبير أكاديمي ومستشار في تطوير السياسات التعليمية.',
                    'image' => $this->makeTinyPngUpload('mod.png'),
                ],
                1 => [
                    'name'  => 'م. سارة الفيتوري',
                    'role'  => 'محاور',
                    'title' => 'مديرة التحول الرقمي',
                    'bio'   => 'قائدة مشاريع تقنية بخبرة تزيد عن 12 عاماً.',
                    'image' => $this->makeTinyPngUpload('sp2.png'),
                ],
                2 => [
                    'name'  => 'أ. خالد الترهوني',
                    'role'  => 'محاور',
                    'title' => 'مدير الموارد البشرية',
                    'bio'   => 'متخصص في استقطاب الكفاءات وتأهيل الخريجين.',
                    'image' => $this->makeTinyPngUpload('sp3.png'),
                ],
                3 => [
                    'name'  => 'د. ليلى البشتي',
                    'role'  => 'محاور',
                    'title' => 'خبيرة ريادة الأعمال والابتكار',
                    'bio'   => 'مؤسسة حاضنة أعمال للشركات الناشئة التقنية.',
                    'image' => $this->makeTinyPngUpload('sp4.png'),
                ],
            ],
        ];

        $response = $this->actingAs($admin)->post(
            route('job-fair.admin.events.store', $fair->id),
            $storePayload
        );

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $event = JobFairEvent::firstOrFail();
        $this->assertCount(4, $event->speakers_list);
        $this->assertEquals('د. عبد الرحمن المحمودي', $event->speaker_name);

        foreach ($event->speakers_list as $sp) {
            $this->assertNotEmpty($sp['image']);
            Storage::disk('public')->assertExists($sp['image']);
        }

        // 1. Public Event Show Page (/job-fair/events/{event}) displays Wide Banner with ALL 4 panelists
        $showRes = $this->get(route('job-fair.public.events.show', $event->id));
        $showRes->assertStatus(200);
        $showRes->assertSee('speakers-showcase-banner', false);
        $showRes->assertSee('grid-count-4', false);
        foreach ($event->speakers_list as $sp) {
            $showRes->assertSee($sp['name']);
            $showRes->assertSee($sp['title']);
            $showRes->assertSee($sp['bio']);
            $showRes->assertSee($sp['image_url'], false);
        }

        // 2. Scientific Program Page (/job-fair/{fair}/program) displays ALL 4 panelists & photos
        $programRes = $this->get(route('job-fair.public.program', $fair->id));
        $programRes->assertStatus(200);
        $programRes->assertSee('program-panelists-strip', false);
        foreach ($event->speakers_list as $sp) {
            $programRes->assertSee($sp['name']);
            $programRes->assertSee($sp['image_url'], false);
        }

        // 3. Main Job Fair Public Landing Page (/job-fair/{fair}) dedicates vertical slices to each panelist
        $publicRes = $this->get(route('job-fair.public', $fair->id));
        $publicRes->assertStatus(200);
        $publicRes->assertSee('is-panelists-deck', false);
        $publicRes->assertSee('slice-panelist-item', false);
        foreach ($event->speakers_list as $sp) {
            $publicRes->assertSee($sp['name']);
            $publicRes->assertSee($sp['image_url'], false);
        }

        // 4. Admin Events Page renders stacked avatars & count
        $adminRes = $this->actingAs($admin)->get(route('job-fair.admin.events.index', $fair->id));
        $adminRes->assertStatus(200);
        $adminRes->assertSee('4 محاورين ومتحدثين');

        // 5. Update event: preserve existing 4 speaker images + add a 5th panelist
        $existingSpeakers = $event->speakers_list;
        $updateSpeakers = [];
        foreach ($existingSpeakers as $i => $sp) {
            $updateSpeakers[$i] = [
                'name'           => $sp['name'],
                'role'           => $sp['role'],
                'title'          => $sp['title'],
                'bio'            => $sp['bio'],
                'existing_image' => $sp['image'],
            ];
        }
        $updateSpeakers[4] = [
            'name'  => 'م. طارق الورفلي',
            'role'  => 'محاور',
            'title' => 'مدير هندسة البرمجيات',
            'bio'   => 'خبير في الأنظمة السحابية والأمن السيبراني.',
            'image' => $this->makeTinyPngUpload('sp5.png'),
        ];

        $updateRes = $this->actingAs($admin)->put(
            route('job-fair.admin.events.update', $event->id),
            [
                'title'       => $event->title,
                'type'        => 'panel_discussion',
                'description' => $event->description,
                'start_time'  => $event->start_time->format('Y-m-d\TH:i'),
                'end_time'    => $event->end_time->format('Y-m-d\TH:i'),
                'status'      => 'open',
                'speakers'    => $updateSpeakers,
            ]
        );

        $updateRes->assertRedirect();
        $updateRes->assertSessionHasNoErrors();

        $event->refresh();
        $this->assertCount(5, $event->speakers_list);

        $show5Res = $this->get(route('job-fair.public.events.show', $event->id));
        $show5Res->assertStatus(200);
        $show5Res->assertSee('grid-count-5', false);
        $show5Res->assertSee('5 محاورين ومتحدثين');
        foreach ($event->speakers_list as $sp) {
            $show5Res->assertSee($sp['name']);
            $show5Res->assertSee($sp['bio']);
            Storage::disk('public')->assertExists($sp['image']);
        }
    }

    public function test_legacy_single_speaker_event_remains_fully_backward_compatible()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $fair = JobFair::create([
            'title'                => 'معرض التوظيف 2026',
            'event_date'           => now()->addDays(10),
            'location'             => 'طرابلس',
            'status'               => 'published',
            'created_by'           => $admin->id,
            'is_program_published' => true,
        ]);

        $legacyEvent = JobFairEvent::create([
            'job_fair_id'   => $fair->id,
            'title'         => 'رحلة الجاهزية المهنية لمعرض التوظيف 2026',
            'type'          => 'keynote',
            'speaker_name'  => 'المنيب محمد الشريف',
            'speaker_title' => 'موظف بوحدة الدعم التقني',
            'speaker_bio'   => 'نبذة تعريفية عن مقدم الكلمة الافتتاحية.',
            'start_time'    => now()->addDays(10)->setTime(9, 30),
            'end_time'      => now()->addDays(10)->setTime(10, 30),
            'status'        => 'open',
        ]);

        $this->assertCount(1, $legacyEvent->speakers_list);
        $this->assertEquals('المنيب محمد الشريف', $legacyEvent->speakers_list[0]['name']);

        $res = $this->get(route('job-fair.public.events.show', $legacyEvent->id));
        $res->assertStatus(200);
        $res->assertSee('grid-count-1', false);
        $res->assertSee('المنيب محمد الشريف');
        $res->assertSee('موظف بوحدة الدعم التقني');
        $res->assertSee('نبذة تعريفية عن مقدم الكلمة الافتتاحية.');
    }
}

