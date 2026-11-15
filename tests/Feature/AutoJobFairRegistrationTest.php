<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\GraduateData;
use App\Models\JobFair;
use App\Models\JobFairRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AutoJobFairRegistrationTest extends TestCase
{
    use RefreshDatabase;

    private function createFair(array $overrides = []): JobFair
    {
        $creatorId = $overrides['created_by'] ?? User::create([
            'name' => 'Fair Creator ' . uniqid(),
            'email' => 'creator_' . uniqid() . '@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_approved' => true,
            'is_active' => true,
        ])->id;

        return JobFair::create(array_merge([
            'title' => 'معرض التوظيف السنوي',
            'subtitle' => 'فرص عمل وتدريب',
            'description' => 'وصف المعرض التجريبي',
            'event_date' => now()->addDays(5)->toDateString(),
            'start_time' => '09:00',
            'end_time' => '17:00',
            'location' => 'القاعة الرئيسية',
            'status' => 'published',
            'registration_open' => true,
            'max_graduates' => null,
            'created_by' => $creatorId,
        ], $overrides));
    }

    private function createGraduateUser(array $overrides = []): User
    {
        $unique = uniqid();

        return User::create(array_merge([
            'name' => 'خريج ' . $unique,
            'email' => "grad_{$unique}@example.com",
            'password' => Hash::make('password123'),
            'role' => 'graduate',
            'is_approved' => true,
            'is_active' => true,
        ], $overrides));
    }

    public function test_existing_approved_active_graduates_are_backfilled_ignoring_max_graduates_and_filtering_ineligible(): void
    {
        $now = now();
        $creatorId = DB::table('users')->insertGetId([
            'name' => 'Admin Creator',
            'email' => 'admin_creator@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_approved' => 1,
            'is_active' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $publishedFairId = DB::table('job_fairs')->insertGetId([
            'title' => 'Published Fair With Cap 1',
            'event_date' => now()->addDays(7)->toDateString(),
            'end_time' => '18:00',
            'location' => 'Hall A',
            'status' => 'published',
            'registration_open' => 1,
            'max_graduates' => 1,
            'created_by' => $creatorId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $ongoingFairId = DB::table('job_fairs')->insertGetId([
            'title' => 'Ongoing Fair Today',
            'event_date' => now()->toDateString(),
            'end_time' => '23:59:59',
            'location' => 'Hall B',
            'status' => 'ongoing',
            'registration_open' => 1,
            'max_graduates' => 1,
            'created_by' => $creatorId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $draftFairId = DB::table('job_fairs')->insertGetId([
            'title' => 'Draft Fair',
            'event_date' => now()->addDays(10)->toDateString(),
            'location' => 'Hall C',
            'status' => 'draft',
            'registration_open' => 1,
            'created_by' => $creatorId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $completedFairId = DB::table('job_fairs')->insertGetId([
            'title' => 'Completed Fair',
            'event_date' => now()->addDays(10)->toDateString(),
            'location' => 'Hall D',
            'status' => 'completed',
            'registration_open' => 0,
            'created_by' => $creatorId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $cancelledFairId = DB::table('job_fairs')->insertGetId([
            'title' => 'Cancelled Fair',
            'event_date' => now()->addDays(10)->toDateString(),
            'location' => 'Hall E',
            'status' => 'cancelled',
            'registration_open' => 0,
            'created_by' => $creatorId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $pastFairId = DB::table('job_fairs')->insertGetId([
            'title' => 'Past Fair',
            'event_date' => now()->subDays(3)->toDateString(),
            'location' => 'Hall F',
            'status' => 'published',
            'registration_open' => 1,
            'created_by' => $creatorId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $grad1Id = DB::table('users')->insertGetId([
            'name' => 'Grad One',
            'email' => 'grad1@example.com',
            'password' => Hash::make('password'),
            'role' => 'graduate',
            'is_approved' => 1,
            'is_active' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $grad2Id = DB::table('users')->insertGetId([
            'name' => 'Grad Two Exceeds Cap',
            'email' => 'grad2@example.com',
            'password' => Hash::make('password'),
            'role' => 'graduate',
            'is_approved' => 1,
            'is_active' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $pendingGradId = DB::table('users')->insertGetId([
            'name' => 'Pending Grad',
            'email' => 'pending@example.com',
            'password' => Hash::make('password'),
            'role' => 'graduate',
            'is_approved' => 0,
            'is_active' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $inactiveGradId = DB::table('users')->insertGetId([
            'name' => 'Inactive Grad',
            'email' => 'inactive@example.com',
            'password' => Hash::make('password'),
            'role' => 'graduate',
            'is_approved' => 1,
            'is_active' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $trainerId = DB::table('users')->insertGetId([
            'name' => 'Trainer User',
            'email' => 'trainer@example.com',
            'password' => Hash::make('password'),
            'role' => 'trainer',
            'is_approved' => 1,
            'is_active' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->assertSame(0, JobFairRegistration::count());

        $this->artisan('jobfair:sync-graduates')->assertExitCode(0);

        // 2 eligible graduates x 2 eligible fairs = 4 registrations (max_graduates = 1 is bypassed)
        $this->assertSame(4, JobFairRegistration::count());

        foreach ([$publishedFairId, $ongoingFairId] as $fairId) {
            foreach ([$grad1Id, $grad2Id] as $gradId) {
                $reg = JobFairRegistration::where('job_fair_id', $fairId)
                    ->where('user_id', $gradId)
                    ->first();

                $this->assertNotNull($reg);
                $this->assertSame('registered', $reg->status);
                $this->assertNotEmpty($reg->qr_code);
                $this->assertNotEmpty($reg->registration_number);
            }
        }

        // Running again must be idempotent
        $this->artisan('jobfair:sync-graduates')->assertExitCode(0);
        $this->assertSame(4, JobFairRegistration::count());

        // Unique codes across all registrations
        $this->assertSame(4, JobFairRegistration::distinct('qr_code')->count('qr_code'));
        $this->assertSame(4, JobFairRegistration::distinct('registration_number')->count('registration_number'));

        // Excluded users and excluded fairs must have 0 registrations
        $this->assertSame(0, JobFairRegistration::whereIn('user_id', [$pendingGradId, $inactiveGradId, $trainerId])->count());
        $this->assertSame(0, JobFairRegistration::whereIn('job_fair_id', [$draftFairId, $completedFairId, $cancelledFairId, $pastFairId])->count());
    }

    public function test_new_approved_graduate_created_directly_and_via_staff_auto_registers_and_badge_workflows_work(): void
    {
        $fair = $this->createFair(['status' => 'published', 'max_graduates' => 1]);

        // Direct User::create omitting `is_active` (relies on DB default true)
        $directGraduate = User::create([
            'name' => 'أحمد الخريج المباشر',
            'email' => 'direct_grad@example.com',
            'password' => Hash::make('password123'),
            'role' => 'graduate',
            'is_approved' => true,
        ]);

        $directReg = JobFairRegistration::where('job_fair_id', $fair->id)
            ->where('user_id', $directGraduate->id)
            ->first();
        $this->assertNotNull($directReg);

        // Simulate subsequent request loading `is_active` DB default (true) from database
        $directGraduate->refresh();
        $this->assertTrue((bool) $directGraduate->is_active);

        // Staff creation via CareerGuidanceController::storeGraduate (max_graduates=1 bypassed)
        $officer = User::create([
            'name' => 'Career Officer',
            'email' => 'officer@example.com',
            'password' => Hash::make('password123'),
            'role' => 'career_guidance_officer',
            'is_approved' => true,
            'is_active' => true,
        ]);

        $this->actingAs($officer)
            ->post(route('career-guidance.graduates.store'), [
                'name' => 'سالم الخريج المضاف',
                'email' => 'staff_grad@example.com',
                'phone' => '0912345678',
                'university' => 'جامعة طرابلس',
                'sector' => 'هندسة وتقنية',
                'faculty' => 'كلية تقنية المعلومات',
                'major' => 'هندسة برمجيات',
                'graduation_year' => (int) date('Y'),
                'employment_status' => 'seeking_opportunities',
            ])
            ->assertRedirect();

        $staffGraduate = User::where('email', 'staff_grad@example.com')->firstOrFail();
        $this->assertTrue((bool) $staffGraduate->is_approved);
        $this->assertTrue((bool) $staffGraduate->is_active);

        $staffReg = JobFairRegistration::where('job_fair_id', $fair->id)
            ->where('user_id', $staffGraduate->id)
            ->first();
        $this->assertNotNull($staffReg);

        // Badge, print view, ID card, graduate fairs index, public page, catalog
        $this->actingAs($directGraduate)->get(route('job-fair.my-ticket', $directReg->id))->assertOk();
        $this->actingAs($directGraduate)->get(route('job-fair.ticket.print', $fair->id))->assertOk();
        $this->actingAs($directGraduate)->get(route('graduate.id-card'))->assertOk();
        $this->actingAs($directGraduate)->get(route('graduate.job-fairs.index'))->assertOk();
        $this->actingAs($directGraduate)->get(route('job-fair.public', $fair->id))->assertOk();
        $this->actingAs($directGraduate)->get(route('job-fair.catalog', $fair->id))->assertOk();

        // After changing initial temporary password, the staff-created graduate can also open their ticket directly
        $staffGraduate->forceFill(['must_change_password' => false])->save();
        $this->actingAs($staffGraduate)->get(route('job-fair.my-ticket', $staffReg->id))->assertOk();

        // Company booth QR scan recognizes the auto-registered badge
        $companyUser = User::create([
            'name' => 'Tech Co Rep',
            'email' => 'company_rep@example.com',
            'password' => Hash::make('password123'),
            'role' => 'company',
            'is_approved' => true,
            'is_active' => true,
        ]);

        Company::create([
            'user_id' => $companyUser->id,
            'name' => 'Tech Co',
            'email' => 'company_rep@example.com',
            'is_approved' => true,
            'partnership_status' => 'active',
        ]);

        $this->actingAs($companyUser)
            ->postJson(route('company.job-fairs.store-visit', $fair->id), [
                'graduate_id' => $staffGraduate->id,
            ])
            ->assertOk()
            ->assertJson(['success' => true]);
    }

    public function test_pending_graduate_is_not_auto_registered_until_approved(): void
    {
        $fair = $this->createFair(['status' => 'published']);

        $pendingViaRoute = $this->createGraduateUser([
            'email' => 'pending_route@example.com',
            'is_approved' => false,
        ]);

        $pendingViaUpdate = $this->createGraduateUser([
            'email' => 'pending_update@example.com',
            'is_approved' => false,
        ]);

        $inactiveGrad = $this->createGraduateUser([
            'email' => 'inactive_update@example.com',
            'is_approved' => true,
            'is_active' => false,
        ]);

        $this->assertSame(0, JobFairRegistration::where('job_fair_id', $fair->id)->count());

        $officer = User::create([
            'name' => 'Approver Officer',
            'email' => 'approver@example.com',
            'password' => Hash::make('password123'),
            'role' => 'career_guidance_officer',
            'is_approved' => true,
            'is_active' => true,
        ]);

        GraduateData::syncFromUser($pendingViaRoute, [
            'added_by' => $officer->id,
        ]);

        $this->actingAs($officer)
            ->post(route('career-guidance.approve-graduate', $pendingViaRoute->id))
            ->assertRedirect();

        $this->assertDatabaseHas('job_fair_registrations', [
            'job_fair_id' => $fair->id,
            'user_id' => $pendingViaRoute->id,
        ]);

        // Direct model approval
        $pendingViaUpdate->update(['is_approved' => true]);
        $this->assertDatabaseHas('job_fair_registrations', [
            'job_fair_id' => $fair->id,
            'user_id' => $pendingViaUpdate->id,
        ]);

        // Re-activating an inactive approved graduate also auto-registers them
        $this->assertDatabaseMissing('job_fair_registrations', [
            'job_fair_id' => $fair->id,
            'user_id' => $inactiveGrad->id,
        ]);

        $inactiveGrad->update(['is_active' => true]);
        $this->assertDatabaseHas('job_fair_registrations', [
            'job_fair_id' => $fair->id,
            'user_id' => $inactiveGrad->id,
        ]);
    }

    public function test_draft_or_past_fair_does_not_auto_register_until_fair_becomes_published_or_ongoing(): void
    {
        $graduate = $this->createGraduateUser();

        $draftFair = $this->createFair([
            'title' => 'معرض مسودة',
            'status' => 'draft',
            'event_date' => now()->addDays(4)->toDateString(),
        ]);

        $pastFair = $this->createFair([
            'title' => 'معرض منتهي',
            'status' => 'published',
            'event_date' => now()->subDays(2)->toDateString(),
        ]);

        $this->assertSame(0, JobFairRegistration::where('user_id', $graduate->id)->count());

        $draftFair->update(['status' => 'published']);

        $this->assertDatabaseHas('job_fair_registrations', [
            'job_fair_id' => $draftFair->id,
            'user_id' => $graduate->id,
        ]);

        $this->assertDatabaseMissing('job_fair_registrations', [
            'job_fair_id' => $pastFair->id,
            'user_id' => $graduate->id,
        ]);
    }

    public function test_graduate_can_cancel_registration_without_silent_re_registration_and_duplicate_post_redirects_to_ticket(): void
    {
        $fair = $this->createFair(['status' => 'published']);
        $gradA = $this->createGraduateUser(['email' => 'cancel_grad@example.com']);
        $gradB = $this->createGraduateUser(['email' => 'duplicate_grad@example.com']);

        $this->assertDatabaseHas('job_fair_registrations', [
            'job_fair_id' => $fair->id,
            'user_id' => $gradA->id,
        ]);

        // Graduate A cancels registration
        $this->actingAs($gradA)
            ->delete(route('graduate.job-fairs.cancel', $fair->id))
            ->assertRedirect();

        $this->assertDatabaseMissing('job_fair_registrations', [
            'job_fair_id' => $fair->id,
            'user_id' => $gradA->id,
        ]);

        // Profile edits & browsing pages must not silently re-register Grad A
        $gradA->update(['name' => 'اسم محدث', 'phone' => '0921112233']);
        $this->actingAs($gradA)->get(route('graduate.job-fairs.index'))->assertOk();
        $this->actingAs($gradA)->get(route('job-fair.public', $fair->id))->assertOk();
        $this->actingAs($gradA)->get(route('graduate.id-card'))->assertOk();

        $this->assertDatabaseMissing('job_fair_registrations', [
            'job_fair_id' => $fair->id,
            'user_id' => $gradA->id,
        ]);

        // Graduate B submitting manual POST /job-fair/{fair}/register goes straight to my-ticket without duplicate
        $regB = JobFairRegistration::where('job_fair_id', $fair->id)
            ->where('user_id', $gradB->id)
            ->firstOrFail();

        $this->actingAs($gradB)
            ->post(route('job-fair.register', $fair->id))
            ->assertRedirect(route('job-fair.my-ticket', $regB->id));

        $this->assertSame(
            1,
            JobFairRegistration::where('job_fair_id', $fair->id)->where('user_id', $gradB->id)->count()
        );
    }
}
