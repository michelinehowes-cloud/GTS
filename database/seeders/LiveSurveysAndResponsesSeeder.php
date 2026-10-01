<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Survey;
use App\Models\SurveyTemplate;
use App\Models\SurveyResponse;
use App\Models\Company;
use App\Models\Training;
use App\Models\JobFair;
use App\Models\User;

class LiveSurveysAndResponsesSeeder extends Seeder
{
    public function run(): void
    {
        // =========================================================================
        // 1. استبيان تقييم الشركات المشاركة في معرض توظيف 2025
        // =========================================================================
        $companyTemplate = SurveyTemplate::where('title', 'نموذج تقييم الشركات المشاركة في معرض توظيف 2025')->first();
        $jobFair = JobFair::first();

        $companySurvey = Survey::updateOrCreate(
            ['slug' => 'job-fair-company-evaluation-2025'],
            [
                'title' => 'استبيان تقييم الشركات المشاركة في معرض توظيف 2025',
                'description' => 'بداية نشكر سيادتكم على مشاركتكم بمعرض التوظيف الذي عقد في جامعة طرابلس والذي تم تنظيمه من خلال مكتب تدريب الخريجين بجامعة طرابلس وبالتعاون مع شركة الواحة للمعارض، نأمل أن يكون هذا المعرض قد حقق الأهداف المرجوة من عقده ولاقى توقعاتكم على حد السواء.',
                'questions' => $companyTemplate ? $companyTemplate->questions : [],
                'target_audience' => 'companies',
                'type' => 'job_fair',
                'related_id' => $jobFair ? $jobFair->id : 1,
                'is_active' => true,
                'is_public' => true,
                'start_date' => now()->subMonths(2),
                'end_date' => now()->addMonths(10),
            ]
        );

        // =========================================================================
        // 2. نموذج تقييم التدريبات والجلسات التدريبية (قسم التقييم والمتابعة)
        // =========================================================================
        $trainingTemplate = SurveyTemplate::where('title', 'نموذج تقييم التدريبات والجلسات التدريبية (قسم التقييم والمتابعة)')->first();
        $training = Training::first();

        $trainingSurvey = Survey::updateOrCreate(
            ['slug' => 'training-session-evaluation-official'],
            [
                'title' => 'نموذج تقييم التدريبات والجلسات التدريبية (قسم التقييم والمتابعة)',
                'description' => 'النموذج الرسمي المعتمد من قسم التقييم والمتابعة بمكتب تدريب الخريجين بجامعة طرابلس لتقييم محتوى وتنفيذ الجلسة التدريبية وأداء المدرب والمحاضر وفق معايير الجودة الأكاديمية والمهنية.',
                'questions' => $trainingTemplate ? $trainingTemplate->questions : [],
                'target_audience' => 'all',
                'type' => 'training',
                'related_id' => $training ? $training->id : 1,
                'is_active' => true,
                'is_public' => true,
                'start_date' => now()->subMonths(2),
                'end_date' => now()->addMonths(10),
            ]
        );

        // =========================================================================
        // 3. إضافة عينة ردود واقعية لاستبيان الشركات (لتشغيل الإحصائيات والرسوم البيانية)
        // =========================================================================
        $sampleCompanies = [
            ['name' => 'شركة المدار الجديد للاتصالات', 'email' => 'hr@almadar.ly', 'satisfaction' => 'راضي جداً', 'matching' => 'متوافقة تماماً 90_100%', 'hired' => 'تم تعيين 8 خريجين (5 ذكور و3 إناث)'],
            ['name' => 'شركة ليبيانا للهاتف المحمول', 'email' => 'careers@libyana.ly', 'satisfaction' => 'راضي جداً', 'matching' => 'متوافقة بشكل كبير 70_80%', 'hired' => 'تم توظيف 6 مهندسين'],
            ['name' => 'مصرف التجارة والتنمية', 'email' => 'jobs@bcd.ly', 'satisfaction' => 'راضي', 'matching' => 'متوافقة بشكل كبير 70_80%', 'hired' => 'تم توظيف 4 خريجي محاسبة وإدارة'],
            ['name' => 'شركة الواحة للنفط والغاز', 'email' => 'info@wahaoil.net', 'satisfaction' => 'راضي', 'matching' => 'متوافقة إلى حد ما 50_60%', 'hired' => 'تم ترشيح 3 متدربين للتدريب التعاوني'],
            ['name' => 'شركة تداول للحلول المالية والتقنية', 'email' => 'contact@tadawul.ly', 'satisfaction' => 'راضي جداً', 'matching' => 'متوافقة تماماً 90_100%', 'hired' => 'تم تعيين 4 مطوري برمجيات'],
            ['name' => 'مستشفى الفردوس الدولي', 'email' => 'hr@firdous-hospital.ly', 'satisfaction' => 'راضي', 'matching' => 'متوافقة بشكل كبير 70_80%', 'hired' => 'تم استقطاب 5 ممرضين وتقنيين'],
            ['name' => 'شركة البريقة لتسويق النفط', 'email' => 'recruitment@brega.ly', 'satisfaction' => 'إلى حد ما راضي', 'matching' => 'متوافقة إلى حد ما 50_60%', 'hired' => 'قيد المقابلات الفنية الثانية'],
        ];

        foreach ($sampleCompanies as $idx => $sc) {
            $companyUser = User::where('email', $sc['email'])->first();

            $answers = [
                'question_0' => $sc['satisfaction'],
                'question_1' => 'مناسبين',
                'question_2' => '',
                'question_3' => 'راضي جداً',
                'question_4' => $sc['satisfaction'],
                'question_5' => 'نعم',
                'question_6' => $sc['matching'],
                'question_7' => 'نعم',
                'question_8' => $sc['hired'],
                'question_9' => 'راضي جداً',
                'question_10' => ($idx % 3 == 0) ? 'لا' : 'نعم',
                'question_11' => 'نعم',
                'question_12' => [
                    'تنظيم ورش عمل تدريبية',
                    'تطوير برامج التدريب الداخلي',
                    'تقديم استشارات حول احتياجات سوق العمل',
                    'تبادل المعلومات حول المرشحين',
                ],
                'question_13' => 'نعم',
                'question_14' => [
                    'نقص المهارات المطلوبة',
                    'صعوبة الوصول الى المرشحين المؤهلين',
                ],
                'question_15' => [
                    'تحسين جودة المرشحين',
                    'توفير الوقت والجهد في عملية التوظيف',
                    'الوصول الى قاعدة بيانات مرشحين اوسع',
                ],
                'question_16' => [
                    'تقنية معلومات it',
                    'الهندسة',
                    'ادارة اعمال',
                    'محاسبة',
                ],
                'question_17' => 'نعم',
                'question_18' => 'معرض ممتاز ومثمر ونشكر جامعة طرابلس على هذه المبادرة الرائدة ونتطلع للمزيد من الفعاليات.',
            ];

            SurveyResponse::updateOrCreate(
                [
                    'survey_id' => $companySurvey->id,
                    'participant_email' => $sc['email'],
                ],
                [
                    'user_id' => $companyUser ? $companyUser->id : null,
                    'answers' => $answers,
                    'participant_name' => $sc['name'],
                    'is_external' => $companyUser ? false : true,
                    'submitted_at' => now()->subDays(10 - $idx),
                ]
            );
        }

        // =========================================================================
        // 4. إضافة عينة ردود لنموذج تقييم التدريبات (10 متدربين ومقيمين)
        // =========================================================================
        $sampleTrainees = [
            ['name' => 'محمد أحمد الصادق', 'email' => 'm.sadiq@example.com', 'role' => 'graduate'],
            ['name' => 'فاطمة عمر الترهوني', 'email' => 'f.tarhuni@example.com', 'role' => 'graduate'],
            ['name' => 'علي عبد السلام الغرياني', 'email' => 'a.ghiryani@example.com', 'role' => 'graduate'],
            ['name' => 'سارة حسن الزنتاني', 'email' => 's.zentani@example.com', 'role' => 'graduate'],
            ['name' => 'طارق مفتاح القماطي', 'email' => 't.qamati@example.com', 'role' => 'graduate'],
            ['name' => 'منى خالد الورفلي', 'email' => 'm.werfali@example.com', 'role' => 'graduate'],
            ['name' => 'عبد المهيمن ميلاد الزليتني', 'email' => 'a.zlitni@example.com', 'role' => 'graduate'],
            ['name' => 'إيناس بشير الساعدي', 'email' => 'i.saadi@example.com', 'role' => 'graduate'],
        ];

        foreach ($sampleTrainees as $idx => $st) {
            $tUser = User::where('email', $st['email'])->first();

            $tAnswers = [
                'question_0' => 5, // وضوح أهداف البرنامج
                'question_1' => 4 + ($idx % 2), // تنظيم وتتابع المحاور
                'question_2' => 5, // جاذبية العرض
                'question_3' => 4, // تفاعل المتدربين
                'question_4' => 4 + ($idx % 2), // تنوع الوسائل
                'question_5' => 5, // تحقيق المخرجات
                'question_6' => 4, // الالتزام بالوقت
                'question_7' => 'نعم، تم تنفيذ قياس قبلي وقياس بعدي', // قياس قبلي بعدي
                'question_8' => 5, // التقييم العام للتدريب
                'question_9' => 5, // انضباط المدرب
                'question_10' => 5, // وضوح الشرح
                'question_11' => 4 + ($idx % 2), // إدارة المتدربين
                'question_12' => 5, // التفاعل مع الأسئلة
                'question_13' => 5, // الالتزام بالمحتوى
                'question_14' => 5, // المهنية
                'question_15' => 4 + ($idx % 2), // توظيف أساليب مناسبة
                'question_16' => 5, // التقييم العام للمدرب
                'question_17' => 'جلسة تدريبية ممتازة ومليئة بالتطبيقات العملية المفيدة لسوق العمل.',
            ];

            SurveyResponse::updateOrCreate(
                [
                    'survey_id' => $trainingSurvey->id,
                    'participant_email' => $st['email'],
                ],
                [
                    'user_id' => $tUser ? $tUser->id : null,
                    'answers' => $tAnswers,
                    'participant_name' => $st['name'],
                    'is_external' => $tUser ? false : true,
                    'submitted_at' => now()->subDays(8 - $idx),
                ]
            );
        }
    }
}
