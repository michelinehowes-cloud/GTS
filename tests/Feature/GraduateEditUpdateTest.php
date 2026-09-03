<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\GraduateData;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GraduateEditUpdateTest extends TestCase
{
    public function test_graduate_edit_screen_prefills_all_self_registered_fields()
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);

        // Clean up previous test run if any
        User::where('email', 'khaled.test@tripoli.edu.ly')->delete();
        GraduateData::where('email', 'khaled.test@tripoli.edu.ly')->delete();

        // Create a graduate user with self-registered personal and academic data
        $graduateUser = User::create([
            'name' => 'خالد محمد علي',
            'email' => 'khaled.test@tripoli.edu.ly',
            'password' => bcrypt('password123'),
            'role' => 'graduate',
            'phone' => '0912345678',
            'national_id' => '11998877665',
            'date_of_birth' => '1998-04-12',
            'gender' => 'male',
            'city' => 'طرابلس',
            'address' => 'تاجوراء - طريق الشط',
            'university' => 'جامعة طرابلس',
            'sector' => 'قاطع (أ)',
            'faculty' => 'كلية تقنية المعلومات',
            'specialization' => 'قسم هندسة البرمجيات',
            'major' => 'قسم هندسة البرمجيات',
            'qualification' => 'بكالوريوس',
            'degree' => 'بكالوريوس',
            'graduation_year' => 2023,
            'gpa' => 89.50,
            'skills' => ['PHP', 'Laravel', 'MySQL'],
            'languages' => ['العربية', 'الإنجليزية'],
            'experiences' => 'مطور برمجيات متدرب لمدة سنة',
            'is_approved' => true,
        ]);

        // Create the GraduateData record linked to this graduate user
        $graduateData = GraduateData::create([
            'user_id' => $graduateUser->id,
            'name' => $graduateUser->name,
            'email' => $graduateUser->email,
            'phone' => $graduateUser->phone,
            'national_id' => $graduateUser->national_id,
            'major' => $graduateUser->major,
            'university' => $graduateUser->university,
            'sector' => $graduateUser->sector,
            'faculty' => $graduateUser->faculty,
            'graduation_year' => $graduateUser->graduation_year,
            'gpa' => $graduateUser->gpa,
            'degree' => $graduateUser->degree,
            'address' => $graduateUser->address,
            'skills' => $graduateUser->skills,
            'languages' => $graduateUser->languages,
            'work_experience' => $graduateUser->experiences,
            'employment_status' => 'seeking_opportunities',
            'added_by' => $admin->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('career-guidance.graduates.edit', $graduateData->id));

        $response->assertStatus(200);
        $response->assertSee('خالد محمد علي');
        $response->assertSee('khaled.test@tripoli.edu.ly');
        $response->assertSee('0912345678');
        $response->assertSee('11998877665');
        $response->assertSee('1998-04-12');
        $response->assertSee('طرابلس');
        $response->assertSee('تاجوراء - طريق الشط');
        $response->assertSee('جامعة طرابلس');
        $response->assertSee('قاطع (أ)');
        $response->assertSee('كلية تقنية المعلومات');
        $response->assertSee('قسم هندسة البرمجيات');
        $response->assertSee('بكالوريوس');
        $response->assertSee('89.5');
        $response->assertSee('مطور برمجيات متدرب لمدة سنة');
        $response->assertSee('PHP, Laravel, MySQL');
        $response->assertSee('العربية, الإنجليزية');
    }

    public function test_updating_graduate_with_form_fields_succeeds_without_major_degree_errors()
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);

        $graduateData = GraduateData::where('email', 'khaled.test@tripoli.edu.ly')->first();
        if (!$graduateData) {
            $this->test_graduate_edit_screen_prefills_all_self_registered_fields();
            $graduateData = GraduateData::where('email', 'khaled.test@tripoli.edu.ly')->firstOrFail();
        }

        // Send payload matching the exact form inputs (specialization and qualification without explicit major and degree)
        $payload = [
            'name' => 'خالد محمد علي المعدل',
            'email' => 'khaled.test@tripoli.edu.ly',
            'phone' => '0922222222',
            'national_id' => '11998877665',
            'date_of_birth' => '1998-04-12',
            'gender' => 'male',
            'city' => 'طرابلس الغرب',
            'address' => 'طريق السكة',
            'university' => 'جامعة طرابلس',
            'sector' => 'قاطع (أ)',
            'faculty' => 'كلية تقنية المعلومات',
            'specialization' => 'قسم هندسة البرمجيات',
            'qualification' => 'بكالوريوس',
            'graduation_year' => 2023,
            'gpa' => 92.00,
            'employment_status' => 'employed',
            'experiences' => 'مهندس برمجيات بدوام كامل',
            'skills' => 'Laravel, Docker, AWS',
            'languages' => 'العربية, الإنجليزية, الفرنسية',
        ];

        $response = $this->actingAs($admin)->put(route('career-guidance.graduates.update', $graduateData->id), $payload);

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');

        // Check updated GraduateData
        $graduateData->refresh();
        $this->assertEquals('خالد محمد علي المعدل', $graduateData->name);
        $this->assertEquals('0922222222', $graduateData->phone);
        $this->assertEquals('طرابلس الغرب', $graduateData->city);
        $this->assertEquals('قسم هندسة البرمجيات', $graduateData->major);
        $this->assertEquals('بكالوريوس', $graduateData->degree);
        $this->assertEquals(92.00, (float) $graduateData->gpa);
        $this->assertEquals('employed', $graduateData->employment_status);
        $this->assertEquals('مهندس برمجيات بدوام كامل', $graduateData->work_experience);

        // Check reverse sync to User
        $user = User::where('email', 'khaled.test@tripoli.edu.ly')->first();
        $this->assertNotNull($user);
        $this->assertEquals('خالد محمد علي المعدل', $user->name);
        $this->assertEquals('0922222222', $user->phone);
        $this->assertEquals('طرابلس الغرب', $user->city);
        $this->assertEquals('قسم هندسة البرمجيات', $user->major);
        $this->assertEquals('بكالوريوس', $user->degree);
        $this->assertEquals(92.00, (float) $user->gpa);
        $this->assertEquals('مهندس برمجيات بدوام كامل', $user->experiences);
    }
}
