<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\GraduateData;
use App\Models\JobFair;
use App\Models\JobFairCompany;
use App\Models\JobFairRegistration;
use App\Models\JobFairVisit;
use App\Models\JobFairVisitor;
use App\Models\Message;
use App\Models\Permission;
use App\Models\TrainingApplication;
use Illuminate\Support\Facades\Config;

class Prevent500ErrorsRegressionTest extends TestCase
{
    private function seedPermissions(array $names): void
    {
        foreach ($names as $name) {
            Permission::firstOrCreate(
                ['name' => $name],
                ['display_name' => $name, 'module' => explode('.', $name)[0] ?? 'general']
            );
        }
    }

    public function test_staff_dashboard_renders_without_500_for_all_custom_permission_modules(): void
    {
        $perms = [
            'graduates.view',
            'graduates.create',
            'jobs.view',
            'nominations.manage',
            'surveys.manage',
            'evaluations.manage',
            'companies.view',
            'partnerships.documents',
            'job_fair.view',
            'trainings.view',
            'trainings.attendance',
            'trainings.applications',
        ];
        $this->seedPermissions($perms);

        $staff = User::factory()->create([
            'role' => 'staff',
            'email' => 'staff_all_modules_' . uniqid() . '@tripoli.edu.ly',
            'is_active' => true,
            'is_approved' => true,
        ]);
        $staff->syncPermissions($perms);

        $dashRedirect = $this->actingAs($staff)->get('/dashboard');
        $dashRedirect->assertRedirect(route('staff.dashboard'));

        $response = $this->actingAs($staff)->get(route('staff.dashboard'));
        $response->assertStatus(200);
        $response->assertSee(route('career-guidance.graduates'), false);
        $response->assertSee(route('career-guidance.nominations'), false);
        $response->assertSee(route('evaluation-followup.surveys.index'), false);

        $cgOfficer = User::factory()->create([
            'role' => 'career_guidance_officer',
            'email' => 'cg_dash_' . uniqid() . '@tripoli.edu.ly',
            'is_active' => true,
            'is_approved' => true,
        ]);
        $this->actingAs($cgOfficer)->get(route('career-guidance.dashboard'))->assertStatus(200);

        $admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin_dash_' . uniqid() . '@tripoli.edu.ly',
            'is_active' => true,
            'is_approved' => true,
        ]);
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertStatus(200);
        $this->actingAs($admin)->get(route('admin.career-guidance.graduates'))->assertStatus(200);
    }

    public function test_graduate_dashboard_renders_without_500_with_orphaned_relations(): void
    {
        $graduate = User::factory()->create([
            'role' => 'graduate',
            'email' => 'grad_dash_' . uniqid() . '@tripoli.edu.ly',
            'is_active' => true,
            'is_approved' => true,
        ]);

        // Create an application referencing a deleted training
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        TrainingApplication::create([
            'user_id' => $graduate->id,
            'training_id' => 999999,
            'status' => 'pending',
        ]);
        Message::create([
            'sender_id' => 888888,
            'receiver_id' => $graduate->id,
            'content' => 'رسالة اختبارية',
        ]);
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        $response = $this->actingAs($graduate)->get(route('graduate.dashboard'));
        $response->assertStatus(200);
    }

    public function test_store_and_update_graduate_normalize_further_study_and_prevent_duplicate_national_id_500(): void
    {
        $this->seedPermissions(['graduates.create']);

        // Staff with ONLY graduates.create (no graduates.view)
        $creatorStaff = User::factory()->create([
            'role' => 'staff',
            'email' => 'staff_creator_' . uniqid() . '@tripoli.edu.ly',
            'is_active' => true,
            'is_approved' => true,
        ]);
        $creatorStaff->givePermission('graduates.create');

        $uniqueNatId1 = '21999' . random_int(1000000, 9999999);
        $email1 = 'grad_store_1_' . uniqid() . '@tripoli.edu.ly';
        $longMajor = 'قسم الدراسات السياحية وإدارة المؤسسات الفندقية والضيافة الدولية المتقدمة بجامعة طرابلس الليبية فرع الآداب واللغات';

        $storeResp = $this->actingAs($creatorStaff)->post(route('career-guidance.graduates.store'), [
            'name' => 'سالم عبد الرحمن الورفلي',
            'email' => $email1,
            'phone' => '0915554433',
            'national_id' => $uniqueNatId1,
            'gender' => 'male',
            'city' => 'طرابلس',
            'university' => 'جامعة طرابلس',
            'sector' => 'قاطع (ب)',
            'faculty' => 'كلية الآداب واللغات',
            'specialization' => $longMajor,
            'qualification' => 'بكالوريوس',
            'graduation_year' => 2024,
            'gpa' => 86.4,
            'employment_status' => 'further_study',
            'skills' => 'التواصل، إدارة المشاريع, الحاسوب',
            'languages' => 'العربية، الإنجليزية, الروسية',
        ]);

        // Should redirect back to create page (since creatorStaff lacks graduates.view) instead of 403 or 500
        $storeResp->assertRedirect(route('career-guidance.graduates.create'));
        $storeResp->assertSessionHas('success');

        $grad1 = GraduateData::where('email', $email1)->firstOrFail();
        $this->assertEquals('continuing_education', $grad1->employment_status);
        $this->assertEquals(['العربية', 'الإنجليزية', 'الروسية'], $grad1->languages);
        $this->assertLessThanOrEqual(100, mb_strlen($grad1->getRawOriginal('specialization')));

        // Create second graduate as admin and attempt updating national_id to grad1's national_id
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin_g_' . uniqid() . '@tripoli.edu.ly']);
        $email2 = 'grad_store_2_' . uniqid() . '@tripoli.edu.ly';
        $grad2 = GraduateData::create([
            'name' => 'آية محمود الترهوني',
            'email' => $email2,
            'national_id' => '21888' . random_int(1000000, 9999999),
            'university' => 'جامعة طرابلس',
            'sector' => 'قاطع (ب)',
            'faculty' => 'كلية الآداب واللغات',
            'major' => 'قسم اللغة الروسية',
            'degree' => 'بكالوريوس',
            'graduation_year' => 2024,
            'employment_status' => 'seeking_opportunities',
            'added_by' => $admin->id,
            'is_active' => true,
        ]);

        $dupUpdateResp = $this->actingAs($admin)
            ->from(route('career-guidance.graduates.edit', $grad2->id))
            ->put(route('career-guidance.graduates.update', $grad2->id), [
                'name' => 'آية محمود الترهوني',
                'email' => $email2,
                'national_id' => $uniqueNatId1, // Duplicate!
                'university' => 'جامعة طرابلس',
                'sector' => 'قاطع (ب)',
                'faculty' => 'كلية الآداب واللغات',
                'major' => 'قسم اللغة الروسية',
                'degree' => 'بكالوريوس',
                'graduation_year' => 2024,
                'employment_status' => 'further_study',
            ]);

        $dupUpdateResp->assertStatus(302);
        $dupUpdateResp->assertSessionHasErrors(['national_id']);
    }

    public function test_self_graduate_registration_syncs_valid_data_source_and_rejects_duplicate_student_id_gracefully(): void
    {
        Config::set('services.turnstile.secret_key', null);
        Config::set('security.rate_limits.register_max_attempts', 50);

        $studentId = '2170' . random_int(10000, 99999);
        $email = 'self_reg_' . uniqid() . '@tripoli.edu.ly';

        $resp = $this->post(route('graduate.register.store'), [
            'name' => 'معاذ فتحي الزنتاني',
            'student_id' => $studentId,
            'email' => $email,
            'password' => 'Password@2026',
            'password_confirmation' => 'Password@2026',
            'phone' => '0927776655',
            'date_of_birth' => '2001-05-15',
            'gender' => 'male',
            'address' => 'حي الأندلس',
            'city' => 'طرابلس',
            'qualification' => 'بكالوريوس',
            'specialization' => 'قسم اللغة الروسية',
            'graduation_year' => 2024,
            'university' => 'جامعة طرابلس',
            'sector' => 'قاطع (ب)',
            'faculty' => 'كلية الآداب واللغات',
            'gpa' => 88.5,
            'skills' => 'الترجمة، التدريس',
            'languages' => 'العربية، الروسية',
        ]);

        $this->assertNotEquals(500, $resp->getStatusCode());
        $user = User::where('email', $email)->firstOrFail();
        $gradData = GraduateData::where('user_id', $user->id)->first();
        $this->assertNotNull($gradData, 'GraduateData should be synced without ENUM failure');
        $this->assertEquals('system_sync', $gradData->data_source);

        auth()->logout();

        // Attempt registering another user with the exact same student_id -> must return 302 validation error, NOT 500
        $dupResp = $this->from(route('graduate.register'))->post(route('graduate.register.store'), [
            'name' => 'خريج مكرر القيد',
            'student_id' => $studentId,
            'email' => 'dup_reg_' . uniqid() . '@tripoli.edu.ly',
            'password' => 'Password@2026',
            'password_confirmation' => 'Password@2026',
            'phone' => '0918889900',
            'date_of_birth' => '2001-06-20',
            'gender' => 'male',
            'address' => 'طرابلس',
            'city' => 'طرابلس',
            'qualification' => 'بكالوريوس',
            'specialization' => 'قسم الدراسات السياحية',
            'graduation_year' => 2024,
            'university' => 'جامعة طرابلس',
            'sector' => 'قاطع (ب)',
            'faculty' => 'كلية الآداب واللغات',
            'gpa' => 80,
        ]);

        $dupResp->assertStatus(302);
        $dupResp->assertSessionHasErrors(['national_id']);
    }

    public function test_job_fair_public_visitor_multi_purpose_and_qr_scan_do_not_throw_500(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'jf_admin_' . uniqid() . '@tripoli.edu.ly']);

        // Ensure companyUser.id !== company.id by creating extra users first
        User::factory()->count(3)->create();
        $companyUser = User::factory()->create([
            'role' => 'company',
            'email' => 'comp_jf_' . uniqid() . '@company.ly',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $company = Company::create([
            'user_id' => $companyUser->id,
            'name' => 'شركة المدار الجديد للاتصالات',
            'email' => $companyUser->email,
            'phone' => '0910001111',
            'industry' => 'الاتصالات وتقنية المعلومات',
            'address' => 'طرابلس',
            'partnership_type' => 'employment',
            'partnership_status' => 'active',
            'is_approved' => true,
        ]);

        $fair = JobFair::create([
            'title' => 'ملتقى ومعرض التوظيف 2026',
            'description' => 'معرض التوظيف السنوي بجامعة طرابلس',
            'event_date' => now()->addDays(10)->toDateString(),
            'location' => 'جامعة طرابلس',
            'status' => 'published',
            'created_by' => $admin->id,
        ]);

        JobFairCompany::create([
            'job_fair_id' => $fair->id,
            'company_id' => $company->id,
            'booth_number' => 'A1',
            'available_positions' => 4,
            'status' => 'confirmed',
        ]);

        // Also insert an orphaned JobFairCompany row with missing Company record
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        JobFairCompany::create([
            'job_fair_id' => $fair->id,
            'company_id' => 999999,
            'booth_number' => 'Z9',
            'available_positions' => 1,
            'status' => 'confirmed',
        ]);
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        // 1. Public show & public companies must return 200 OK without null company crash
        $this->get(route('job-fair.public', $fair->id))->assertStatus(200);
        $this->get(route('job-fair.public.companies', $fair->id))->assertStatus(200);

        // 2. Multi-purpose visitor registration (> 255 chars JSON)
        $visitorResp = $this->postJson(route('job-fair.visitor.register', $fair->id), [
            'name' => 'أحمد مفتاح القماطي',
            'phone' => '091' . random_int(1000000, 9999999),
            'visitor_type' => 'job_seeker',
            'city' => 'طرابلس',
            'visit_purpose' => [
                'البحث عن فرص عمل وتوظيف مباشر في الشركات المشاركة',
                'البحث عن فرص تدريب ميداني وتأهيل عملي للخريجين',
                'حضور ورش العمل والجلسات الحوارية والعلمية المصاحبة',
                'استعراض مشاريع التخرج والابتكارات الطلابية المتميزة',
                'التواصل المباشر وبناء شبكة علاقات مهنية مع أرباب العمل',
            ],
        ]);
        $visitorResp->assertStatus(200)->assertJson(['success' => true]);
        $ticketNumber = $visitorResp->json('ticket_number');
        $savedVisitor = JobFairVisitor::where('ticket_number', $ticketNumber)->firstOrFail();
        $this->assertCount(5, $savedVisitor->visit_purposes_array, 'Visit purposes must remain saved in database');

        $cardResp = $this->get(route('job-fair.visitor.ticket', $ticketNumber));
        $cardResp->assertStatus(200);
        $cardResp->assertDontSee('أهداف الزيارة');

        // Lower-case / padded / RTL-marked ticket from WhatsApp or Facebook in-app browsers must still open the ticket
        $ticketResp = $this->get('/job-fair/visitor-ticket/' . strtolower($ticketNumber) . '%E2%80%8F%20');
        $ticketResp->assertStatus(200);
        $ticketResp->assertSee('detectBrowserDownloadEnv', false);

        // Unknown ticket must NOT show a 404 page; redirect to registration modal with a notice
        $this->get('/job-fair/visitor-ticket/VIS-0000-NOPE1')
            ->assertRedirect()
            ->assertSessionHas('visitor_ticket_notice');

        // Re-registering with same phone (even typed in Arabic-Indic digits with spaces) returns the same existing ticket
        $asciiPhone = (string) \App\Models\JobFairVisitor::where('ticket_number', $ticketNumber)->value('phone');
        $arabicDigitsPhone = strtr($asciiPhone, ['0' => '٠', '1' => '١', '2' => '٢', '3' => '٣', '4' => '٤', '5' => '٥', '6' => '٦', '7' => '٧', '8' => '٨', '9' => '٩']);
        $again = $this->postJson(route('job-fair.visitor.register', $fair->id), [
            'name' => 'أحمد مفتاح القماطي',
            'phone' => ' ' . substr($arabicDigitsPhone, 0, 6) . ' ' . substr($arabicDigitsPhone, 6) . ' ',
        ]);
        $again->assertStatus(200)->assertJson(['success' => true, 'already_registered' => true, 'ticket_number' => $ticketNumber]);

        // 3. Graduate QR scan uses company->user_id for job_fair_visits FK
        $graduate = User::factory()->create([
            'role' => 'graduate',
            'email' => 'grad_jf_' . uniqid() . '@tripoli.edu.ly',
            'is_active' => true,
            'is_approved' => true,
        ]);
        $reg = JobFairRegistration::create([
            'job_fair_id' => $fair->id,
            'user_id' => $graduate->id,
            'registration_number' => 'JF-2026-' . random_int(10000, 99999),
            'qr_code' => 'QR-' . uniqid(),
            'attended' => false,
        ]);

        $scanResp = $this->actingAs($graduate)->get(route('graduate.job-fairs.company.scan', [$fair->id, $company->id]));
        $scanResp->assertStatus(200);

        $this->assertTrue(
            JobFairVisit::where('job_fair_id', $fair->id)
                ->where('company_id', $companyUser->id)
                ->where('graduate_id', $graduate->id)
                ->exists()
        );

        // 4. Ticket views render cleanly
        $this->actingAs($graduate)->get(route('job-fair.my-ticket', $reg->id))->assertStatus(200);
        $this->actingAs($graduate)->get(route('job-fair.ticket.print', $fair->id))->assertStatus(200);
    }
}
