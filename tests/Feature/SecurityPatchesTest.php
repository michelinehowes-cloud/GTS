<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\GraduateData;
use App\Models\Training;
use App\Models\TrainingApplication;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class SecurityPatchesTest extends TestCase
{
    protected function fakePassword(): string
    {
        return 'Sec_' . \Illuminate\Support\Str::random(10) . '1!';
    }

    /** @test */
    public function career_guidance_cannot_reset_admin_password_via_graduate_password_reset()
    {
        $officer = User::factory()->create([
            'role' => 'career_guidance_officer',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $adminSecret = $this->fakePassword();
        $adminInitialPass = Hash::make($adminSecret);
        $admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin_victim_' . uniqid() . '@tripoli.edu.ly',
            'password' => $adminInitialPass,
            'is_active' => true,
            'is_approved' => true,
        ]);

        // Graduate record malicious pointer to admin user ID
        $gradData = GraduateData::create([
            'user_id' => $admin->id,
            'name' => 'Fake Grad Attacker',
            'email' => $admin->email,
            'major' => 'Computer Science',
            'graduation_year' => 2024,
            'added_by' => $officer->id,
            'is_active' => true,
        ]);

        $attemptPassword = $this->fakePassword();
        $response = $this->actingAs($officer)
            ->patch("/career-guidance/graduates/{$gradData->id}/reset-password", [
                'new_password' => $attemptPassword,
                'new_password_confirmation' => $attemptPassword,
            ]);

        $response->assertSessionHasErrors(['password_error']);
        $this->assertTrue(Hash::check($adminSecret, $admin->fresh()->password));
        $this->assertFalse(Hash::check($attemptPassword, $admin->fresh()->password));
    }

    /** @test */
    public function career_guidance_cannot_overwrite_admin_account_via_create_account()
    {
        $officer = User::factory()->create([
            'role' => 'career_guidance_officer',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $adminSecret = $this->fakePassword();
        $adminInitialPass = Hash::make($adminSecret);
        $admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin_target_' . uniqid() . '@tripoli.edu.ly',
            'password' => $adminInitialPass,
            'is_active' => true,
            'is_approved' => true,
        ]);

        $gradData = GraduateData::create([
            'user_id' => null,
            'name' => 'Target Admin Pointer',
            'email' => $admin->email,
            'major' => 'Computer Science',
            'graduation_year' => 2024,
            'added_by' => $officer->id,
            'is_active' => true,
        ]);

        $takeoverPassword = $this->fakePassword();
        $response = $this->actingAs($officer)
            ->post("/career-guidance/graduates/{$gradData->id}/create-user", [
                'new_password' => $takeoverPassword,
                'new_password_confirmation' => $takeoverPassword,
            ]);

        $response->assertSessionHasErrors(['error']);
        $this->assertTrue(Hash::check($adminSecret, $admin->fresh()->password));
    }

    /** @test */
    public function career_guidance_update_cannot_hijack_admin_account()
    {
        $officer = User::factory()->create([
            'role' => 'career_guidance_officer',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $adminSecret = $this->fakePassword();
        $adminInitialPass = Hash::make($adminSecret);
        $admin = User::factory()->create([
            'role' => 'admin',
            'name' => 'Real Administrator',
            'email' => 'admin_hijack_' . uniqid() . '@tripoli.edu.ly',
            'password' => $adminInitialPass,
            'is_active' => true,
            'is_approved' => true,
        ]);

        // A malicious graduate record pointing to admin
        $gradData = GraduateData::create([
            'user_id' => $admin->id,
            'name' => 'Fake Graduate',
            'email' => 'fake_grad_' . uniqid() . '@example.com',
            'major' => 'Computer Science',
            'graduation_year' => 2024,
            'added_by' => $officer->id,
            'is_active' => true,
        ]);

        $hijackPassword = $this->fakePassword();
        $response = $this->actingAs($officer)->put("/career-guidance/graduates/{$gradData->id}", [
            'name' => 'Tampered Admin Name',
            'email' => $gradData->email,
            'university' => 'Tripoli',
            'sector' => 'IT',
            'faculty' => 'Science',
            'major' => 'Computer Science',
            'degree' => 'BSc',
            'graduation_year' => 2024,
            'employment_status' => 'seeking_opportunities',
            'password' => $hijackPassword,
            'password_confirmation' => $hijackPassword,
        ]);

        // Admin must remain untouched!
        $this->assertEquals('Real Administrator', $admin->fresh()->name);
        $this->assertTrue(Hash::check($adminSecret, $admin->fresh()->password));
        // And the graduate's user_id must NOT point to the admin
        $this->assertNotEquals($admin->id, $gradData->fresh()->user_id);
    }

    /** @test */
    public function distributed_brute_force_via_rotating_proxies_is_blocked()
    {
        $user = User::factory()->create([
            'role' => 'graduate',
            'email' => 'otp_distributed_' . uniqid() . '@example.com',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $validCode = '987654';
        DB::table('password_reset_codes')->insert([
            'email' => $user->email,
            'code' => $validCode,
            'created_at' => now(),
        ]);

        $emailThrottleKey = 'verify-otp-email|' . strtolower($user->email);
        RateLimiter::clear($emailThrottleKey);

        $attemptPass = $this->fakePassword();
        // Attacker rotates IPs for each attempt: 1.1.1.1, 1.1.1.2, 1.1.1.3, etc.
        for ($i = 1; $i <= 5; $i++) {
            $this->withServerVariables(['REMOTE_ADDR' => "10.0.0.{$i}"])
                ->post('/verify-code', [
                    'email' => $user->email,
                    'code' => '000000',
                    'password' => $attemptPass,
                    'password_confirmation' => $attemptPass,
                ]);
        }

        // Even across 5 different IPs, the OTP must be PURGED from the DB!
        $codeInDb = DB::table('password_reset_codes')->where('email', $user->email)->first();
        $this->assertNull($codeInDb, 'Distributed brute-force across rotating proxies must purge OTP.');
    }

    /** @test */
    public function training_store_blocks_mass_assignment_injection()
    {
        $coordA = User::factory()->create([
            'role' => 'training_coordinator',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $coordB = User::factory()->create([
            'role' => 'training_coordinator',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $uniqueTitle = 'Coord A Program ' . uniqid();
        // Coord A attempts to inject coordinator_id = Coord B and media_coverage_status = published
        $response = $this->actingAs($coordA)->post('/coordinator/trainings', [
            'title' => $uniqueTitle,
            'description' => 'Test description',
            'type' => 'workshop',
            'duration' => '30 hours',
            'start_date' => now()->addDays(5)->format('Y-m-d'),
            'end_date' => now()->addDays(35)->format('Y-m-d'),
            'location' => 'Tripoli',
            'seats' => 25,
            'status' => 'active',
            'category' => 'Engineering',
            'instructor_name' => 'Dr. Smith',
            'coordinator_id' => $coordB->id, // Malicious injection
            'media_coverage_status' => 'published', // Malicious injection
        ]);

        $createdTraining = Training::where('title', $uniqueTitle)->first();
        $this->assertNotNull($createdTraining);
        // coordinator_id must be Coord A, NOT the injected Coord B!
        $this->assertEquals($coordA->id, $createdTraining->coordinator_id);
        // media_coverage_status must NOT be the injected 'published' (remains default 'pending')
        $this->assertNotEquals('published', $createdTraining->media_coverage_status);
        $this->assertEquals('pending', $createdTraining->media_coverage_status);
    }

    /** @test */
    public function non_admin_cannot_assign_arbitrary_permissions_on_user_store()
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $permManage = Permission::firstOrCreate(['name' => 'users.manage'], ['display_name' => 'إدارة المستخدمين', 'module' => 'users']);
        $permAdminOnly = Permission::firstOrCreate(['name' => 'system.settings'], ['display_name' => 'إعدادات النظام', 'module' => 'system']);
        $staff->givePermission('users.manage');

        // Staff attempts to create another user and grant system.settings
        $newEmail = 'new_staff_' . uniqid() . '@example.com';
        $newStaffPass = $this->fakePassword();
        $response = $this->actingAs($staff)->post('/admin/users', [
            'name' => 'New Staff User',
            'email' => $newEmail,
            'password' => $newStaffPass,
            'password_confirmation' => $newStaffPass,
            'role' => 'staff',
            'permissions' => [$permAdminOnly->id],
        ]);

        $newUser = User::where('email', $newEmail)->first();
        $this->assertNotNull($newUser);
        // Permissions must NOT be assigned by non-admin!
        $this->assertFalse($newUser->hasPermission('system.settings'));
    }

    /** @test */
    public function training_coordinator_cannot_access_scanner_or_attendance_of_other_trainings()
    {
        $coordA = User::factory()->create([
            'role' => 'training_coordinator',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $coordB = User::factory()->create([
            'role' => 'training_coordinator',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $training = Training::create([
            'title' => 'Coord B Private Training',
            'description' => 'Description here',
            'type' => 'workshop',
            'duration' => '30 hours',
            'start_date' => now()->addDays(5),
            'end_date' => now()->addDays(35),
            'location' => 'Tripoli',
            'seats' => 20,
            'status' => 'active',
            'category' => 'Technology',
            'coordinator_id' => $coordB->id,
        ]);

        // Coord A attempts to access scanner
        $responseScanner = $this->actingAs($coordA)->get("/coordinator/trainings/{$training->id}/scanner");
        $responseScanner->assertStatus(403);

        // Coord A attempts to access attendance sheet
        $responseAttendance = $this->actingAs($coordA)->get("/coordinator/trainings/{$training->id}/attendance");
        $responseAttendance->assertStatus(403);

        // Coord A attempts to access edit view
        $responseEdit = $this->actingAs($coordA)->get("/coordinator/trainings/{$training->id}/edit");
        $responseEdit->assertStatus(403);
    }

    /** @test */
    public function bulk_application_operations_are_scoped_to_coordinator()
    {
        $coordA = User::factory()->create([
            'role' => 'training_coordinator',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $coordB = User::factory()->create([
            'role' => 'training_coordinator',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $trainingB = Training::create([
            'title' => 'Coord B Training',
            'description' => 'Desc',
            'type' => 'course',
            'duration' => '20 hours',
            'start_date' => now()->addDays(5),
            'end_date' => now()->addDays(25),
            'location' => 'Tripoli',
            'seats' => 20,
            'status' => 'active',
            'category' => 'Business',
            'coordinator_id' => $coordB->id,
        ]);

        $student = User::factory()->create([
            'role' => 'graduate',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $appB = TrainingApplication::create([
            'user_id' => $student->id,
            'training_id' => $trainingB->id,
            'status' => 'pending',
            'applied_at' => now(),
        ]);

        // Coord A tries to bulk-approve application belonging to Training B
        $response = $this->actingAs($coordA)->post('/coordinator/applications/bulk-approve', [
            'application_ids' => [$appB->id],
        ]);

        // The application belonging to Coord B must NOT be approved!
        $this->assertEquals('pending', $appB->fresh()->status);
    }

    /** @test */
    public function debug_routes_are_not_registered_in_non_local_environments()
    {
        $response = $this->get('/test-graduate-create');
        $response->assertStatus(404);
    }

    /** @test */
    public function non_training_coordinator_cannot_approve_or_delete_applications()
    {
        $student = User::factory()->create([
            'role' => 'graduate',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $training = Training::create([
            'title' => 'Sample Training ' . uniqid(),
            'description' => 'Desc',
            'type' => 'course',
            'duration' => '20 hours',
            'start_date' => now()->addDays(5),
            'end_date' => now()->addDays(25),
            'location' => 'Tripoli',
            'seats' => 20,
            'status' => 'active',
            'category' => 'Technology',
        ]);

        $app = TrainingApplication::create([
            'user_id' => $student->id,
            'training_id' => $training->id,
            'status' => 'pending',
            'applied_at' => now(),
        ]);

        // Malicious graduate tries to approve application via patch
        $response = $this->actingAs($student)->patch("/applications/{$app->id}/approve");
        $response->assertStatus(403);
        $this->assertEquals('pending', $app->fresh()->status);
    }

    /** @test */
    public function ai_confirm_action_cannot_demote_admin_via_company_creation()
    {
        $officer = User::factory()->create([
            'role' => 'partnership_officer',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $admin = User::factory()->create([
            'email' => 'admin_victim_' . uniqid() . '@uot.edu.ly',
            'role' => 'admin',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $aiService = app(\App\Services\Ai\AiAssistantService::class);
        $res = $aiService->confirmAction($officer, 'session_test', 'create_company', [
            'name' => 'Hostile Takeover LLC',
            'email' => $admin->email,
            'password' => $this->fakePassword(),
        ]);

        $this->assertEquals('forbidden', $res['status']);
        $this->assertEquals('admin', $admin->fresh()->role);
    }

    /** @test */
    public function ai_confirm_action_cannot_freeze_or_delete_super_admin()
    {
        $officer = User::factory()->create([
            'role' => 'career_guidance_officer',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@uot.edu.ly'],
            [
                'name' => 'Super Administrator',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'is_active' => true,
                'is_approved' => true,
            ]
        );

        $aiService = app(\App\Services\Ai\AiAssistantService::class);

        // Attempt to freeze super admin
        $resFreeze = $aiService->confirmAction($officer, 'session_test', 'toggle_graduate_status', [
            'user_id' => $superAdmin->id,
            'new_status' => false,
        ]);
        $this->assertEquals('forbidden', $resFreeze['status']);
        $this->assertTrue($superAdmin->fresh()->is_active);

        // Attempt to delete super admin
        $resDelete = $aiService->confirmAction($officer, 'session_test', 'delete_graduate_account', [
            'user_id' => $superAdmin->id,
        ]);
        $this->assertEquals('forbidden', $resDelete['status']);
        $this->assertNotNull(User::find($superAdmin->id));
    }

    /** @test */
    public function ai_manage_training_applications_is_strictly_scoped_to_coordinator()
    {
        $coordA = User::factory()->create([
            'role' => 'training_coordinator',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $coordB = User::factory()->create([
            'role' => 'training_coordinator',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $trainingB = Training::create([
            'title' => 'Coordinator B Program ' . uniqid(),
            'description' => 'Desc',
            'type' => 'course',
            'duration' => '15 hours',
            'start_date' => now()->addDays(5),
            'end_date' => now()->addDays(20),
            'location' => 'Tripoli',
            'seats' => 20,
            'status' => 'active',
            'category' => 'Science',
            'coordinator_id' => $coordB->id,
        ]);

        $student = User::factory()->create([
            'role' => 'graduate',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $appB = TrainingApplication::create([
            'user_id' => $student->id,
            'training_id' => $trainingB->id,
            'status' => 'pending',
            'applied_at' => now(),
        ]);

        $aiService = app(\App\Services\Ai\AiAssistantService::class);
        // Coordinator A tries to approve application of Coordinator B
        $res = $aiService->confirmAction($coordA, 'session_test', 'manage_training_applications', [
            'application_ids' => [$appB->id],
            'target_status' => 'approved',
        ]);

        $this->assertEquals('success', $res['status'] ?? 'success');
        // App B must STILL be pending because Coord A does not own Training B!
        $this->assertEquals('pending', $appB->fresh()->status);
    }
}

