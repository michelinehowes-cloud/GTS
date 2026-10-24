<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Training;
use App\Models\TrainingApplication;
use App\Models\JobFair;
use App\Models\JobFairEvent;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Notification;

class TrainingTimingAndNotificationTest extends TestCase
{
    /**
     * اختبار دوال تحويل الوقت والصيغة العربية في نموذج التدريب
     */
    public function test_training_model_timing_accessors_format_arabic_correctly()
    {
        $training = new Training([
            'title' => 'ورشة الذكاء الاصطناعي وتطبيقاته',
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-05',
            'start_time' => '09:30',
            'end_time' => '13:00',
        ]);

        $this->assertEquals('09:30 ص', $training->formatted_start_time);
        $this->assertEquals('01:00 م', $training->formatted_end_time);
        $this->assertEquals('من 09:30 ص إلى 01:00 م', $training->timing_text);
        $this->assertStringContainsString('من 2026/11/01 إلى 2026/11/05', $training->full_schedule_text);
        $this->assertStringContainsString('(من 09:30 ص إلى 01:00 م)', $training->full_schedule_text);
    }

    /**
     * اختبار إنشاء منسق التدريب لدورة تدريبية مع تحديد وقت البداية والنهاية
     */
    public function test_coordinator_can_create_training_with_timing()
    {
        $coordinator = User::factory()->create([
            'role' => 'training_coordinator',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $data = [
            'title' => 'دورة إدارة المشاريع الاحترافية PMP',
            'type' => 'course',
            'category' => 'business',
            'duration' => 'أسبوعان',
            'field' => 'ادارة اعمال',
            'description' => 'دورة تدريبية مكثفة تؤهل للحصول على شهادة إدارة المشاريع الاحترافية.',
            'seats' => 25,
            'location' => 'مدرج كلية الهندسة - القاعة الكبرى',
            'start_date' => '2026-11-10',
            'end_date' => '2026-11-20',
            'start_time' => '10:00',
            'end_time' => '14:00',
            'instructor_name' => 'د. أحمد المهدي',
            'status' => 'active',
        ];

        $response = $this->actingAs($coordinator)->post('/coordinator/trainings', $data);

        $response->assertRedirect(route('training-coordinator.trainings'));
        $training = Training::where('title', 'دورة إدارة المشاريع الاحترافية PMP')->first();
        $this->assertNotNull($training);
        $this->assertStringStartsWith('10:00', (string) $training->start_time);
        $this->assertStringStartsWith('14:00', (string) $training->end_time);
    }

    /**
     * اختبار إرسال إشعار القبول في التدريب ومطابقة تفاصيل التوقيت والتواريخ
     */
    public function test_training_approval_notification_includes_timing_details()
    {
        $graduate = User::factory()->create([
            'role' => 'graduate',
            'email' => 'graduate_test@uot.edu.ly',
            'name' => 'محمد الطاهر',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $coordinator = User::factory()->create([
            'role' => 'training_coordinator',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $training = Training::create([
            'title' => 'ورشة كتابة السيرة الذاتية واجتياز المقابلات',
            'type' => 'workshop',
            'category' => 'career_skills',
            'duration' => 'يومان',
            'field' => 'تطوير مهني',
            'description' => 'ورشة متخصصة للخريجين.',
            'seats' => 30,
            'location' => 'قاعة التطوير والتدريب - الإدارة العامة',
            'start_date' => '2026-11-15',
            'end_date' => '2026-11-16',
            'start_time' => '10:00',
            'end_time' => '13:00',
            'instructor_name' => 'أ. فاطمة الورفلي',
            'coordinator_id' => $coordinator->id,
            'status' => 'active',
        ]);

        $application = TrainingApplication::create([
            'training_id' => $training->id,
            'user_id' => $graduate->id,
            'status' => 'approved',
        ]);

        $notificationService = app(NotificationService::class);
        $notificationService->notifyTrainingApplicationApproved($application);

        $notification = \App\Models\Notification::where('user_id', $graduate->id)->latest()->first();

        $this->assertNotNull($notification);
        $this->assertEquals('success', $notification->type);

        $details = $notification->data['details'] ?? [];
        $this->assertArrayHasKey('توقيت الجلسات', $details);
        $this->assertEquals('من 10:00 ص إلى 01:00 م', $details['توقيت الجلسات']);
        $this->assertArrayHasKey('تاريخ الانعقاد', $details);
        $this->assertArrayHasKey('المقر / القاعة', $details);
        $this->assertEquals('قاعة التطوير والتدريب - الإدارة العامة', $details['المقر / القاعة']);
    }

    /**
     * اختبار دوال تحويل الوقت في فعاليات وورش المعرض الوظيفي JobFairEvent
     */
    public function test_job_fair_event_timing_accessors()
    {
        $event = new JobFairEvent([
            'title' => 'ماستر كلاس: القيادة والابتكار في بيئة العمل',
            'type' => 'masterclass',
            'start_time' => '2026-11-10 11:30:00',
            'end_time' => '2026-11-10 13:00:00',
            'location' => 'المدرج الرئيسي',
        ]);

        $this->assertEquals('11:30 ص', $event->formatted_start_time);
        $this->assertEquals('01:00 م', $event->formatted_end_time);
        $this->assertEquals('من 11:30 ص إلى 01:00 م', $event->timing_text);
        $this->assertStringContainsString('من 11:30 ص إلى 01:00 م', $event->full_schedule_text);
    }

    /**
     * اختبار نقطة نهاية موافقة المنسق على طلب التدريب وتأكيد إرسال التوقيت والتاريخ المنسق
     */
    public function test_coordinator_approve_application_endpoint_sends_email_with_timing()
    {
        $coordinator = User::factory()->create([
            'role' => 'training_coordinator',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $graduate = User::factory()->create([
            'role' => 'graduate',
            'name' => 'المنيب محمد الشريف',
            'email' => 'almonib@uot.edu.ly',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $training = Training::create([
            'title' => 'السيرة الذاتية CV',
            'type' => 'workshop',
            'category' => 'career_skills',
            'description' => 'ورشة متخصصة في كتابة السيرة الذاتية واجتياز المقابلات الشخصية.',
            'duration' => '1 يوم',
            'location' => 'جامعة طرابلس',
            'start_date' => '2026-10-28',
            'end_date' => '2026-10-28',
            'start_time' => '10:00',
            'end_time' => '13:00',
            'coordinator_id' => $coordinator->id,
            'status' => 'active',
            'seats' => 20,
        ]);

        $application = TrainingApplication::create([
            'training_id' => $training->id,
            'user_id' => $graduate->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($coordinator)->post('/coordinator/applications/' . $application->id . '/approve');

        $response->assertSessionHas('success');
        $this->assertEquals('approved', $application->fresh()->status);

        $notification = \App\Models\Notification::where('user_id', $graduate->id)->latest()->first();
        $this->assertNotNull($notification);
        $this->assertArrayHasKey('توقيت الجلسات', $notification->data['details']);
        $this->assertEquals('من 10:00 ص إلى 01:00 م', $notification->data['details']['توقيت الجلسات']);
        $this->assertEquals('2026-10-28', $notification->data['details']['تاريخ الانعقاد']);
    }
}
