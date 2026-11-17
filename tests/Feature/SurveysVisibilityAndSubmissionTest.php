<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\JobFair;
use App\Models\JobFairCompany;
use App\Models\JobFairRegistration;
use App\Models\Survey;
use App\Models\SurveyResponse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SurveysVisibilityAndSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_graduate_can_view_targeted_and_job_fair_surveys()
    {
        $gradUser = User::factory()->create([
            'role' => 'graduate',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $fair = JobFair::create([
            'title' => 'معرض 2026',
            'status' => 'published',
            'event_date' => now()->addDays(5),
            'location' => 'طرابلس',
            'created_by' => $gradUser->id,
        ]);

        JobFairRegistration::updateOrCreate(
            ['job_fair_id' => $fair->id, 'user_id' => $gradUser->id],
            [
                'registration_number' => 'JF-2026-TEST',
                'qr_code' => 'QR-JF-TEST',
                'status' => 'attended',
                'attended' => true,
            ]
        );

        // 1. استبيان عام موجه للخريجين
        $surveyGrad = Survey::create([
            'title' => 'استبيان تقييم خدمات الخريجين',
            'description' => 'استبيان دوري للخريجين',
            'target_audience' => 'graduates',
            'type' => 'general',
            'is_active' => true,
            'start_date' => now()->subDay(),
            'end_date' => now()->addMonth(),
            'questions' => [
                ['question' => 'ما مدى رضاك؟', 'type' => 'radio', 'options' => ['ممتاز', 'جيد']],
            ],
        ]);

        // 2. استبيان معرض توظيف
        $surveyFair = Survey::create([
            'title' => 'استبيان تقييم حضور معرض التوظيف',
            'description' => 'تقييم تجربة المعرض',
            'target_audience' => 'graduates',
            'type' => 'job_fair',
            'related_id' => $fair->id,
            'is_active' => true,
            'start_date' => now()->subDay(),
            'end_date' => now()->addMonth(),
            'questions' => [
                ['question' => 'كيف كانت الفعالية؟', 'type' => 'text'],
            ],
        ]);

        // 3. استبيان موجه للشركات فقط (يجب ألا يظهر للخريج)
        $surveyCompany = Survey::create([
            'title' => 'استبيان خاص بأصحاب العمل فقط',
            'description' => 'وصف استبيان أصحاب العمل',
            'target_audience' => 'companies',
            'type' => 'general',
            'is_active' => true,
            'start_date' => now()->subDay(),
            'end_date' => now()->addMonth(),
            'questions' => [],
        ]);

        $response = $this->actingAs($gradUser)->get(route('graduate.surveys.index'));
        $response->assertStatus(200);
        $response->assertSee('استبيان تقييم خدمات الخريجين');
        $response->assertSee('استبيان تقييم حضور معرض التوظيف');
        $response->assertDontSee('استبيان خاص بأصحاب العمل فقط');

        // خريج يقوم بتعبئة الاستبيان
        $submitResponse = $this->actingAs($gradUser)->post(route('graduate.surveys.store', $surveyGrad->id), [
            'answers' => [0 => 'ممتاز'],
        ]);
        $submitResponse->assertRedirect(route('graduate.surveys.index'));

        $this->assertDatabaseHas('survey_responses', [
            'survey_id' => $surveyGrad->id,
            'user_id' => $gradUser->id,
        ]);
    }

    public function test_company_can_view_and_submit_company_surveys()
    {
        $companyUser = User::factory()->create([
            'role' => 'company',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $company = Company::create([
            'user_id' => $companyUser->id,
            'name' => 'شركة المدار الجديد',
            'email' => $companyUser->email,
            'phone' => '0910000000',
            'is_approved' => true,
            'is_active' => true,
            'partnership_status' => 'active',
        ]);

        $fair = JobFair::create([
            'title' => 'ملتقى التوظيف 2026',
            'status' => 'published',
            'event_date' => now()->addDays(3),
            'location' => 'طرابلس',
            'created_by' => $companyUser->id,
        ]);

        JobFairCompany::create([
            'job_fair_id' => $fair->id,
            'company_id' => $company->id,
            'status' => 'confirmed',
        ]);

        // 1. استبيان موجه للشركات
        $compSurvey = Survey::create([
            'title' => 'استبيان تقييم الشركات المشاركة 2026',
            'description' => 'تقييم شامل من قبل الشركات',
            'target_audience' => 'companies',
            'type' => 'job_fair',
            'related_id' => $fair->id,
            'is_active' => true,
            'start_date' => now()->subDay(),
            'end_date' => now()->addMonth(),
            'questions' => [
                ['question' => 'ما رأيكم في تنظيم الجناح؟', 'type' => 'text', 'required' => true],
            ],
        ]);

        // 2. استبيان موجه للخريجين فقط (يجب ألا يظهر للشركة)
        $gradOnlySurvey = Survey::create([
            'title' => 'استبيان مخصص للخريجين الباحثين عن عمل',
            'description' => 'وصف استبيان الخريجين',
            'target_audience' => 'graduates',
            'type' => 'general',
            'is_active' => true,
            'start_date' => now()->subDay(),
            'end_date' => now()->addMonth(),
            'questions' => [],
        ]);

        $response = $this->actingAs($companyUser)->get(route('company.surveys.index'));
        $response->assertStatus(200);
        $response->assertSee('استبيان تقييم الشركات المشاركة 2026');
        // علاقة الاستبيان بالمعرض تعمل (كانت تسبب خطأ 500 في MySQL)
        $response->assertSee('خاص بمعرض');
        $response->assertDontSee('استبيان مخصص للخريجين الباحثين عن عمل');

        // صفحة عرض الاستبيان للشركة
        $showResponse = $this->actingAs($companyUser)->get(route('company.surveys.show', $compSurvey->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('ما رأيكم في تنظيم الجناح؟');

        // إرسال رد الشركة
        $storeResponse = $this->actingAs($companyUser)->post(route('company.surveys.store', $compSurvey->id), [
            'answers' => [0 => 'التنظيم كان ممتازاً وناجحاً جداً'],
        ]);
        $storeResponse->assertRedirect(route('company.surveys.index'));

        $this->assertDatabaseHas('survey_responses', [
            'survey_id' => $compSurvey->id,
            'user_id' => $companyUser->id,
            'participant_name' => 'شركة المدار الجديد',
        ]);
    }

    public function test_company_targeted_surveys_never_reach_graduates_in_list_or_direct_link()
    {
        $gradUser = User::factory()->create(['role' => 'graduate', 'is_active' => true, 'is_approved' => true]);

        $base = [
            'description' => 'وصف',
            'is_active' => true,
            'start_date' => now()->subDay(),
            'end_date' => now()->addMonth(),
            'questions' => [['question' => 'سؤال؟', 'type' => 'text']],
        ];

        // استبيانات موجهة للشركات بأنواع مختلفة (بما فيها معرض غير مرتبط بمعرض محدد)
        $companyFair = Survey::create($base + ['title' => 'شركات - تقييم المعرض', 'target_audience' => 'companies', 'type' => 'job_fair']);
        Survey::create($base + ['title' => 'شركات - استبيان عام', 'target_audience' => 'companies', 'type' => 'general']);
        Survey::create($base + ['title' => 'شركات - تدريب', 'target_audience' => 'companies', 'type' => 'training']);
        Survey::create($base + ['title' => 'منسقين - استبيان', 'target_audience' => 'training_coordinators', 'type' => 'general']);
        // استبيان للجميع يظهر للخريج
        Survey::create($base + ['title' => 'للجميع - رضا عام', 'target_audience' => 'all', 'type' => 'general']);

        $response = $this->actingAs($gradUser)->get(route('graduate.surveys.index'));
        $response->assertStatus(200);
        $response->assertSee('للجميع - رضا عام');
        $response->assertDontSee('شركات - تقييم المعرض');
        $response->assertDontSee('شركات - استبيان عام');
        $response->assertDontSee('شركات - تدريب');
        $response->assertDontSee('منسقين - استبيان');

        // الدخول بالرابط المباشر لاستبيان الشركات يُرفض
        $this->actingAs($gradUser)->get(route('graduate.surveys.show', $companyFair->id))
            ->assertRedirect(route('graduate.surveys.index'));

        $this->actingAs($gradUser)->post(route('graduate.surveys.store', $companyFair->id), ['answers' => [0 => 'x']])
            ->assertRedirect(route('graduate.surveys.index'));

        $this->assertDatabaseMissing('survey_responses', ['survey_id' => $companyFair->id]);

        // التحقق من عدم احتواء SQL علاقة jobFair على عمود type غير الموجود في جدول job_fairs (سبب خطأ 500 في MySQL)
        $jobFairRelationSql = (new Survey())->jobFair()->toSql();
        $this->assertStringNotContainsString('type', $jobFairRelationSql);
    }
}
