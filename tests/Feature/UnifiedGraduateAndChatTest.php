<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\GraduateData;
use App\Models\JobOpportunity;
use App\Models\Company;
use App\Models\Message;
use Illuminate\Foundation\Testing\WithFaker;

class UnifiedGraduateAndChatTest extends TestCase
{
    /**
     * اختبار مزامنة بيانات الخريج التلقائية والشاملة دون نقص
     */
    public function test_graduate_data_sync_from_user_persists_all_fields()
    {
        $uniqueEmail = 'sync.test.' . uniqid() . '@example.com';
        $uniqueNatId = '1199' . rand(1000000, 9999999);

        $user = User::create([
            'name' => 'خريج تجريبي موحد',
            'email' => $uniqueEmail,
            'password' => bcrypt('password123'),
            'role' => 'graduate',
            'phone' => '0920001122',
            'national_id' => $uniqueNatId,
            'date_of_birth' => '1999-05-15',
            'gender' => 'male',
            'city' => 'طرابلس',
            'address' => 'جنزور - حي الأندلس',
            'university' => 'جامعة طرابلس',
            'faculty' => 'كلية الهندسة',
            'department' => 'قسم الهندسة المدنية',
            'major' => 'هندسة مدنية',
            'specialization' => 'هندسة مدنية',
            'qualification' => 'بكالوريوس',
            'degree' => 'بكالوريوس',
            'graduation_year' => 2022,
            'gpa' => 84.75,
            'skills' => ['AutoCAD', 'Structural Analysis', 'Project Management'],
            'languages' => ['العربية', 'الإنجليزية'],
            'work_experience' => 'مهندس موقع تحت التدريب لمدة 6 أشهر',
            'is_approved' => true,
        ]);

        // Auto-sync via GraduateData::syncFromUser
        $gradData = GraduateData::syncFromUser($user);

        $this->assertNotNull($gradData);
        $this->assertEquals($user->id, $gradData->user_id);
        $this->assertEquals($user->city, $gradData->city);
        $this->assertEquals($user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : null, $gradData->date_of_birth ? $gradData->date_of_birth->format('Y-m-d') : null);
        $this->assertEquals($user->gender, $gradData->gender);
        $this->assertEquals($user->major, $gradData->major);
        $this->assertEquals($user->specialization, $gradData->specialization);
        $this->assertEquals($user->qualification, $gradData->qualification);
        $this->assertEquals(84.75, $gradData->gpa);
        $this->assertEquals(['AutoCAD', 'Structural Analysis', 'Project Management'], $gradData->skills);

        // Also test User dynamic attribute accessor self-healing
        $userFresh = User::find($user->id);
        $this->assertNotNull($userFresh->graduateData);
        $this->assertEquals($gradData->id, $userFresh->graduateData->id);
    }

    /**
     * اختبار حذف المحادثة بالكامل وحذف رسالة مفردة
     */
    public function test_conversation_and_message_deletion()
    {
        $officer = User::where('role', 'career_guidance_officer')->first() 
            ?? User::factory()->create(['role' => 'career_guidance_officer']);
        
        $graduate = User::where('role', 'graduate')->first() 
            ?? User::factory()->create(['role' => 'graduate']);

        // Create messages between them
        $msg1 = Message::create([
            'sender_id' => $officer->id,
            'receiver_id' => $graduate->id,
            'content' => 'مرحباً بك، هل تحتاج لأي مساعدة في التقديم؟',
        ]);

        $msg2 = Message::create([
            'sender_id' => $graduate->id,
            'receiver_id' => $officer->id,
            'content' => 'نعم، أود الاستفسار عن متطلبات الوظيفة.',
        ]);

        // Delete single message by sender
        $response = $this->actingAs($graduate)->deleteJson(route('messages.destroy-message', $msg2->id));
        $response->assertStatus(200);
        $this->assertDatabaseMissing('messages', ['id' => $msg2->id]);
        $this->assertDatabaseHas('messages', ['id' => $msg1->id]);

        // Delete entire conversation
        $convResponse = $this->actingAs($officer)->deleteJson(route('messages.destroy-conversation', $graduate->id));
        $convResponse->assertStatus(200);
        $this->assertDatabaseMissing('messages', ['id' => $msg1->id]);
    }

    /**
     * اختبار شمول وتعديل بيانات فرصة العمل مع التخصصات والمهارات
     */
    public function test_job_opportunity_create_and_edit_preserves_specializations_and_skills()
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
        $company = Company::first();

        if (!$company) {
            $companyUser = User::factory()->create(['role' => 'company']);
            $company = Company::create([
                'user_id' => $companyUser->id,
                'name' => 'شركة التقنية المتقدمة',
                'email' => 'tech@company.ly',
                'phone' => '0919998877',
                'address' => 'طرابلس',
                'city' => 'طرابلس',
            ]);
        }

        $job = JobOpportunity::create([
            'title' => 'مطور نظم الويب',
            'description' => 'تطوير وصيانة تطبيقات الويب الحديثة',
            'type' => 'job',
            'contract_type' => 'full_time',
            'company_id' => $company->id,
            'location' => 'طرابلس',
            'seats' => 3,
            'start_date' => now()->addDays(10),
            'end_date' => now()->addDays(90),
            'application_deadline' => now()->addDays(7),
            'required_specializations' => ['هندسة برمجيات', 'علوم حاسب'],
            'required_skills' => ['برمجة (عام)', 'تطوير الويب (Backend)'],
            'required_experience' => 'سنة خبرة أو مشروع تخرج متميز',
            'salary' => 3500.00,
            'benefits' => 'تأمين صحي، بيئة عمل مرنة، تدريب دوري',
            'requirements' => 'إجادة لغة البرمجة PHP وLaravel',
            'status' => 'open',
            'created_by' => $admin->id,
        ]);

        $this->assertNotNull($job->id);
        $this->assertEquals(['هندسة برمجيات', 'علوم حاسب'], $job->required_specializations);
        $this->assertEquals(['برمجة (عام)', 'تطوير الويب (Backend)'], $job->required_skills);
        $this->assertEquals('تأمين صحي، بيئة عمل مرنة، تدريب دوري', $job->benefits);
        $this->assertEquals(3500.00, $job->salary);

        // Edit view loads successfully
        $response = $this->actingAs($admin)->get(route('job-opportunities.edit', $job->id));
        $response->assertStatus(200);
        $response->assertSee('التخصصات المطلوبة');
        $response->assertSee('المهارات المطلوبة');
    }
}
