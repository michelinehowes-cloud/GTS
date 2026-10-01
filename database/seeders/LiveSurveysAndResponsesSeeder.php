<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Survey;
use App\Models\SurveyTemplate;
use App\Models\SurveyResponse;
use App\Models\Company;
use App\Models\Training;
use App\Models\JobFair;
use App\Models\JobFairTask;
use App\Models\User;

class LiveSurveysAndResponsesSeeder extends Seeder
{
    public function run(): void
    {
        $jobFair = JobFair::first();
        $training = Training::first();

        // =========================================================================
        // 1. استبيان تقييم الشركات المشاركة في معرض توظيف 2025
        // =========================================================================
        $companyTemplate = SurveyTemplate::where('title', 'نموذج تقييم الشركات المشاركة في معرض توظيف 2025')->first();
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
        // 3. نموذج تقييم الشركاء (قسم التقييم والمتابعة)
        // =========================================================================
        $partnerTemplate = SurveyTemplate::where('title', 'نموذج تقييم الشركاء (قسم التقييم والمتابعة)')->first();
        $partnerSurvey = Survey::updateOrCreate(
            ['slug' => 'partner-evaluation-official'],
            [
                'title' => 'نموذج تقييم الشركاء (قسم التقييم والمتابعة)',
                'description' => 'نموذج رسمي لتقييم أداء والتزام الشركاء والرعاة والمؤسسات الداعمة لأنشطة وفعاليات مكتب تدريب الخريجين بجامعة طرابلس.',
                'questions' => $partnerTemplate ? $partnerTemplate->questions : [],
                'target_audience' => 'all',
                'type' => 'job_fair',
                'related_id' => $jobFair ? $jobFair->id : 1,
                'is_active' => true,
                'is_public' => true,
                'start_date' => now()->subMonths(2),
                'end_date' => now()->addMonths(10),
            ]
        );

        // =========================================================================
        // 4. نموذج تقييم الفعالية (قسم التقييم والمتابعة)
        // =========================================================================
        $eventTemplate = SurveyTemplate::where('title', 'نموذج تقييم الفعالية (قسم التقييم والمتابعة)')->first();
        $eventSurvey = Survey::updateOrCreate(
            ['slug' => 'event-evaluation-official'],
            [
                'title' => 'نموذج تقييم الفعالية (قسم التقييم والمتابعة)',
                'description' => 'نموذج رسمي لتقييم جودة الفعاليات والأنشطة، ونسبة الحضور والتفاعل، وتحقيق الأهداف المحددة بجامعة طرابلس.',
                'questions' => $eventTemplate ? $eventTemplate->questions : [],
                'target_audience' => 'all',
                'type' => 'job_fair',
                'related_id' => $jobFair ? $jobFair->id : 1,
                'is_active' => true,
                'is_public' => true,
                'start_date' => now()->subMonths(2),
                'end_date' => now()->addMonths(10),
            ]
        );

        // =========================================================================
        // 5. نموذج تقييم التنظيم والتنسيق للفريق الداخلي
        // =========================================================================
        $internalTemplate = SurveyTemplate::where('title', 'نموذج تقييم التنظيم والتنسيق للفريق الداخلي')->first();
        $internalSurvey = Survey::updateOrCreate(
            ['slug' => 'internal-team-evaluation-official'],
            [
                'title' => 'نموذج تقييم التنظيم والتنسيق للفريق الداخلي',
                'description' => 'نموذج تقييم داخلي لقياس وضوح الأدوار، جودة التنسيق بين الفرق، فعالية التسويق والترويج، وإدارة الطوارئ والتواصل مع الإدارة.',
                'questions' => $internalTemplate ? $internalTemplate->questions : [],
                'target_audience' => 'all',
                'type' => 'job_fair',
                'related_id' => $jobFair ? $jobFair->id : 1,
                'is_active' => true,
                'is_public' => true,
                'start_date' => now()->subMonths(2),
                'end_date' => now()->addMonths(10),
            ]
        );

        // =========================================================================
        // 6. استبيان آراء الزوار للفعاليات ومعرض التوظيف
        // =========================================================================
        $visitorTemplate = SurveyTemplate::where('title', 'استبيان آراء الزوار للفعاليات ومعرض التوظيف')->first();
        $visitorSurvey = Survey::updateOrCreate(
            ['slug' => 'visitor-feedback-evaluation-official'],
            [
                'title' => 'استبيان آراء الزوار للفعاليات ومعرض التوظيف',
                'description' => 'استطلاع رأي رسمي لقياس انطباعات ورضا زوار فعاليات ومعرض التوظيف بجامعة طرابلس حول الخدمات والتنظيم والتفاعل.',
                'questions' => $visitorTemplate ? $visitorTemplate->questions : [],
                'target_audience' => 'all',
                'type' => 'job_fair',
                'related_id' => $jobFair ? $jobFair->id : 1,
                'is_active' => true,
                'is_public' => true,
                'start_date' => now()->subMonths(2),
                'end_date' => now()->addMonths(10),
            ]
        );

        // =========================================================================
        // 7. نموذج تقييم المتدربين للبرامج التدريبية
        // =========================================================================
        $traineeTemplate = SurveyTemplate::where('title', 'نموذج تقييم المتدربين للبرامج التدريبية (قسم التقييم والمتابعة)')->first();
        $traineeSurvey = Survey::updateOrCreate(
            ['slug' => 'trainee-program-evaluation-official'],
            [
                'title' => 'نموذج تقييم المتدربين للبرامج التدريبية (قسم التقييم والمتابعة)',
                'description' => 'نموذج تقييم المتدربين المعتمد وفق الهيكلية الرسمية لتقييم المدرب، البيئة التدريبية، المحتوى العلمي، والتجربة العامة.',
                'questions' => $traineeTemplate ? $traineeTemplate->questions : [],
                'target_audience' => 'graduates',
                'type' => 'training',
                'related_id' => $training ? $training->id : 1,
                'is_active' => true,
                'is_public' => true,
                'start_date' => now()->subMonths(2),
                'end_date' => now()->addMonths(10),
            ]
        );

        // =========================================================================
        // 8. ردود واقعية لاستبيان تقييم الشركات
        // =========================================================================
        $sampleCompanies = [
            ['name' => 'شركة المدار الجديد للاتصالات', 'email' => 'hr@almadar.ly', 'satisfaction' => 'راضي جداً', 'matching' => 'متوافقة تماماً 90_100%', 'hired' => 'تم تعيين 8 خريجين'],
            ['name' => 'شركة ليبيانا للهاتف المحمول', 'email' => 'careers@libyana.ly', 'satisfaction' => 'راضي جداً', 'matching' => 'متوافقة بشكل كبير 70_80%', 'hired' => 'تم توظيف 6 مهندسين'],
            ['name' => 'مصرف التجارة والتنمية', 'email' => 'jobs@bcd.ly', 'satisfaction' => 'راضي', 'matching' => 'متوافقة بشكل كبير 70_80%', 'hired' => 'تم توظيف 4 خريجي محاسبة'],
            ['name' => 'شركة الواحة للنفط والغاز', 'email' => 'info@wahaoil.net', 'satisfaction' => 'راضي', 'matching' => 'متوافقة إلى حد ما 50_60%', 'hired' => 'تم ترشيح 3 متدربين'],
            ['name' => 'شركة تداول للحلول المالية والتقنية', 'email' => 'contact@tadawul.ly', 'satisfaction' => 'راضي جداً', 'matching' => 'متوافقة تماماً 90_100%', 'hired' => 'تم تعيين 4 مطوري برمجيات'],
            ['name' => 'مستشفى الفردوس الدولي', 'email' => 'hr@firdous-hospital.ly', 'satisfaction' => 'راضي', 'matching' => 'متوافقة بشكل كبير 70_80%', 'hired' => 'تم استقطاب 5 ممرضين وتقنيين'],
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
                'question_10' => ($idx % 2 == 0) ? 'نعم' : 'لا',
                'question_11' => 'نعم',
                'question_12' => ['تنظيم ورش عمل تدريبية', 'تطوير برامج التدريب الداخلي'],
                'question_13' => 'نعم',
                'question_14' => ['نقص المهارات المطلوبة', 'صعوبة الوصول إلى المرشحين المؤهلين'],
                'question_15' => ['تحسين جودة المرشحين', 'توفير الوقت والجهد في عملية التوظيف'],
                'question_16' => ['تقنية معلومات IT', 'الهندسة', 'محاسبة'],
                'question_17' => 'نعم',
                'question_18' => 'معرض ممتاز ومثمر، نتطلع للمشاركة في دورة 2026 القادمة.',
            ];

            SurveyResponse::updateOrCreate(
                ['survey_id' => $companySurvey->id, 'participant_email' => $sc['email']],
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
        // 9. ردود واقعية لاستبيان الشركاء (Partner Evaluation)
        // =========================================================================
        $partnerEvaluators = [
            ['name' => 'لجنة تقييم الشركاء - رعاية مصرف الجمهورية', 'email' => 'eval.jumhouria@uot.edu.ly', 'scores' => [5, 5, 5, 5, 5], 'notes' => 'شريك استراتيجي ملتزم وقدم دعماً مادياً ولوجستياً رفيع المستوى.'],
            ['name' => 'لجنة تقييم الشركاء - شركة بريد ليبيا', 'email' => 'eval.libyapost@uot.edu.ly', 'scores' => [4, 5, 4, 5, 4], 'notes' => 'تعاون ممتاز في الجوانب التنظيمية والخدمات البريدية المقدمة.'],
            ['name' => 'لجنة تقييم الشركاء - شركة هاتف ليبيا', 'email' => 'eval.hl@uot.edu.ly', 'scores' => [5, 4, 5, 4, 5], 'notes' => 'حضور مميز وجناح تفاعلي جذب العديد من الخريجين والباحثين عن تدريب.'],
            ['name' => 'لجنة تقييم الشركاء - مصرف التجارة والتنمية', 'email' => 'eval.bcd@uot.edu.ly', 'scores' => [5, 5, 4, 5, 5], 'notes' => 'دعم فعال لورش العمل المالية وإجراء مقابلات مباشرة في المعرض.'],
        ];

        foreach ($partnerEvaluators as $pidx => $pe) {
            $pAnswers = [
                'question_0' => $pe['scores'][0],
                'question_1' => $pe['scores'][1],
                'question_2' => $pe['scores'][2],
                'question_3' => $pe['scores'][3],
                'question_4' => $pe['scores'][4],
                'question_5' => $pe['notes'],
            ];

            SurveyResponse::updateOrCreate(
                ['survey_id' => $partnerSurvey->id, 'participant_email' => $pe['email']],
                [
                    'answers' => $pAnswers,
                    'participant_name' => $pe['name'],
                    'is_external' => false,
                    'submitted_at' => now()->subDays(7 - $pidx),
                ]
            );
        }

        // =========================================================================
        // 10. ردود واقعية لاستبيان تقييم الفعالية (Event Evaluation)
        // =========================================================================
        $eventEvaluators = [
            ['name' => 'د. خالد المحمودي (مقيم أكاديمي)', 'email' => 'k.mahmoudi@uot.edu.ly', 'scores' => [5, 5, 5, 4, 5, 5, 5], 'notes' => 'فعالية منظمة بشكل رائع وإقبال طلابي غير مسبوق.'],
            ['name' => 'أ. نادية الفيتوري (متابعة ميدانية)', 'email' => 'n.fitouri@uot.edu.ly', 'scores' => [4, 5, 4, 5, 4, 5, 4], 'notes' => 'المحتوى متميز والقاعة كانت مجهزة بكافة الوسائل التقنية.'],
            ['name' => 'م. عادل القرقني (منسق التدريب)', 'email' => 'a.qerqeni@uot.edu.ly', 'scores' => [5, 4, 5, 4, 5, 4, 5], 'notes' => 'التزام بالوقت وتفاعل ملحوظ مع المتحدثين والضيوف.'],
        ];

        foreach ($eventEvaluators as $eidx => $ee) {
            $eAnswers = [
                'question_0' => $ee['scores'][0],
                'question_1' => $ee['scores'][1],
                'question_2' => $ee['scores'][2],
                'question_3' => $ee['scores'][3],
                'question_4' => $ee['scores'][4],
                'question_5' => $ee['scores'][5],
                'question_6' => $ee['scores'][6],
                'question_7' => $ee['notes'],
            ];

            SurveyResponse::updateOrCreate(
                ['survey_id' => $eventSurvey->id, 'participant_email' => $ee['email']],
                [
                    'answers' => $eAnswers,
                    'participant_name' => $ee['name'],
                    'is_external' => false,
                    'submitted_at' => now()->subDays(6 - $eidx),
                ]
            );
        }

        // =========================================================================
        // 11. ردود واقعية لتقييم الفريق الداخلي (Internal Team Evaluation)
        // =========================================================================
        $internalEvaluators = [
            ['name' => 'رئيس اللجنة التنظيمية', 'email' => 'org.head@uot.edu.ly', 'scores' => [5, 4, 5, 5, 4, 5], 'notes' => 'التنسيق بين اللجان كان على أعلى مستوى والاستجابة للطوارئ سريعة.'],
            ['name' => 'منسق العلاقات والإعلام', 'email' => 'media.coord@uot.edu.ly', 'scores' => [4, 5, 5, 4, 5, 4], 'notes' => 'التسويق الميداني والرقمي أثمر عن تغطية حضور تفوق المستهدف.'],
            ['name' => 'مشرف الخدمات اللوجستية', 'email' => 'logistics@uot.edu.ly', 'scores' => [5, 5, 4, 5, 4, 5], 'notes' => 'جاهزية عالية للموقع وقاعات الندوات ومتابعة مستمرة مع الإدارة.'],
        ];

        foreach ($internalEvaluators as $iidx => $ie) {
            $iAnswers = [
                'question_0' => $ie['scores'][0],
                'question_1' => $ie['scores'][1],
                'question_2' => $ie['scores'][2],
                'question_3' => $ie['scores'][3],
                'question_4' => $ie['scores'][4],
                'question_5' => $ie['scores'][5],
                'question_6' => $ie['notes'],
            ];

            SurveyResponse::updateOrCreate(
                ['survey_id' => $internalSurvey->id, 'participant_email' => $ie['email']],
                [
                    'answers' => $iAnswers,
                    'participant_name' => $ie['name'],
                    'is_external' => false,
                    'submitted_at' => now()->subDays(5 - $iidx),
                ]
            );
        }

        // =========================================================================
        // 12. ردود واقعية لاستبيان آراء الزوار (Visitor Feedback)
        // =========================================================================
        $visitors = [
            ['name' => 'م. وليد البوسيفي', 'email' => 'w.busefi@example.com', 'ratings' => ['ممتاز', 'ممتاز', 'جيد جداً', 'ممتاز', 'ممتاز', 'ممتاز'], 'fav' => 'ورشة مهارات المقابلات الشخصية', 'issue' => 'لا توجد أي مشكلة، التنظيم كان ممتازاً', 'sugg' => 'زيادة عدد الشركات التقنية المتخصصة بالذكاء الاصطناعي.'],
            ['name' => 'سناء كمال النعاس', 'email' => 's.naas@example.com', 'ratings' => ['جيد جداً', 'ممتاز', 'ممتاز', 'جيد جداً', 'جيد جداً', 'ممتاز'], 'fav' => 'جناح شركات الاتصالات والفرص التدريبية', 'issue' => 'ازدحام طفيف عند مدخل القاعة الأولى ظهراً', 'sugg' => 'تخصيص مسار سريع لحاملي بطاقات الباركود الرقمية.'],
            ['name' => 'عبد السلام الطاهر', 'email' => 'a.taher@example.com', 'ratings' => ['ممتاز', 'جيد جداً', 'ممتاز', 'ممتاز', 'جيد جداً', 'ممتاز'], 'fav' => 'معرض مشاريع التخرج المتميزة للمهندسين', 'issue' => 'كل شيء كان مرتباً ومريحاً', 'sugg' => 'تمديد فترة المعرض إلى ثلاثة أيام بدلاً من يومين.'],
            ['name' => 'ريم بشير المقريف', 'email' => 'r.megrife@example.com', 'ratings' => ['ممتاز', 'ممتاز', 'ممتاز', 'ممتاز', 'ممتاز', 'ممتاز'], 'fav' => 'جلسات التوجيه المهني وإرشاد السيرة الذاتية', 'issue' => 'لا توجد، تجربة ممتازة ومفيدة جداً', 'sugg' => 'تنظيم معارض توظيف متخصصة لكل كلية على حدة.'],
        ];

        foreach ($visitors as $vidx => $v) {
            $vAnswers = [
                'question_0' => $v['ratings'][0],
                'question_1' => $v['ratings'][1],
                'question_2' => $v['ratings'][2],
                'question_3' => $v['ratings'][3],
                'question_4' => $v['ratings'][4],
                'question_5' => $v['ratings'][5],
                'question_6' => $v['fav'],
                'question_7' => $v['issue'],
                'question_8' => $v['sugg'],
            ];

            SurveyResponse::updateOrCreate(
                ['survey_id' => $visitorSurvey->id, 'participant_email' => $v['email']],
                [
                    'answers' => $vAnswers,
                    'participant_name' => $v['name'],
                    'is_external' => true,
                    'submitted_at' => now()->subDays(4 - $vidx),
                ]
            );
        }

        // =========================================================================
        // 13. ردود واقعية لنموذج تقييم المتدربين (Trainee Evaluation)
        // =========================================================================
        $trainees = [
            ['name' => 'أسامة جمال التومي', 'email' => 'o.toumi@example.com', 'trainer_rating' => 'ممتاز'],
            ['name' => 'خديجة مصطفى العكاري', 'email' => 'kh.akkari@example.com', 'trainer_rating' => 'ممتاز'],
            ['name' => 'حسام نوري الدوكالي', 'email' => 'h.doukali@example.com', 'trainer_rating' => 'جيد جداً'],
            ['name' => 'زينب فرج بن عثمان', 'email' => 'z.othman@example.com', 'trainer_rating' => 'ممتاز'],
        ];

        foreach ($trainees as $tidx => $tr) {
            $trAnswers = [
                'question_0' => $tr['trainer_rating'],
                'question_1' => 'ممتاز',
                'question_2' => 'ممتاز',
                'question_3' => 'ممتاز',
                'question_4' => 'ممتاز',
                'question_5' => 'ممتاز',
                'question_6' => 'جيد جداً',
                'question_7' => 'ممتاز',
                'question_8' => 'ممتاز',
                'question_9' => 'ممتاز',
                'question_10' => 'جيد جداً',
                'question_11' => 'ممتاز',
                'question_12' => 'ممتاز',
                'question_13' => 'ورش متقدمة في تحليل البيانات وإدارة المشاريع الاحترافية.',
                'question_14' => 'شكر خاص للمدرب ولفريق مكتب تدريب الخريجين بجامعة طرابلس على هذا التنظيم المتميز.',
            ];

            SurveyResponse::updateOrCreate(
                ['survey_id' => $traineeSurvey->id, 'participant_email' => $tr['email']],
                [
                    'answers' => $trAnswers,
                    'participant_name' => $tr['name'],
                    'is_external' => false,
                    'submitted_at' => now()->subDays(3 - $tidx),
                ]
            );
        }

        // =========================================================================
        // 14. مهام مشروع سنة 2026 لمعرض التوظيف (Job Fair 2026 Tasks)
        // =========================================================================
        $leadAdmin = User::where('role', 'admin')->first() ?? User::first();
        $coordinators = User::whereIn('role', ['training_coordinator', 'evaluation_followup', 'partner_coordinator', 'admin'])->get();

        if ($leadAdmin && $coordinators->count() > 0) {
            $defaultTasks = [
                [
                    'user_id' => $coordinators[0]->id,
                    'team_name' => 'لجنة التجهيز والدعم اللوجستي',
                    'task_description' => 'إعداد المخطط المكاني وتجهيز أجنحة الشركات والقاعات الرئيسية وتوفير شبكات الإنترنت والمعدات الصوتية.',
                    'start_date' => now()->subDays(15)->format('Y-m-d'),
                    'due_date' => now()->addDays(10)->format('Y-m-d'),
                    'status' => 'in_progress',
                    'evaluation_score' => '4.8 / 5',
                    'notes' => 'تم إنهاء 80% من أعمال التجهيز الهندسي بالقاعة الكبرى.',
                ],
                [
                    'user_id' => $coordinators[min(1, $coordinators->count() - 1)]->id,
                    'team_name' => 'لجنة العلاقات والشراكات',
                    'task_description' => 'التواصل مع البنوك والشركات الاستراتيجية الراعية وتأكيد مشاركة 40 شركة وتوقيع مذكرات التفاهم للتوظيف.',
                    'start_date' => now()->subDays(20)->format('Y-m-d'),
                    'due_date' => now()->subDays(2)->format('Y-m-d'),
                    'status' => 'completed',
                    'evaluation_score' => '5.0 / 5',
                    'notes' => 'تم تأكيد مشاركة 45 شركة رائدة وراعي رئيسي بنجاح تام.',
                ],
                [
                    'user_id' => $coordinators[min(2, $coordinators->count() - 1)]->id,
                    'team_name' => 'اللجنة الإعلامية والتوثيق',
                    'task_description' => 'إطلاق الحملة الترويجية الرقمية وتجهيز مقاطع الفيديو التوضيحية وتغطية ورش العمل التحضيرية للمعرض.',
                    'start_date' => now()->subDays(10)->format('Y-m-d'),
                    'due_date' => now()->addDays(14)->format('Y-m-d'),
                    'status' => 'in_progress',
                    'evaluation_score' => '4.9 / 5',
                    'notes' => 'تفاعل عالي على المنصات وموقع الجامعة وتزايد تسجيل الخريجين.',
                ],
                [
                    'user_id' => $coordinators[0]->id,
                    'team_name' => 'فريق العمليات والتنظيم الميداني',
                    'task_description' => 'تدريب فريق الاستقبال والتنظيم على مسح بطاقات الباركود الرقمية وإرشاد الزوار وتنظيم الدخول.',
                    'start_date' => now()->subDays(5)->format('Y-m-d'),
                    'due_date' => now()->addDays(5)->format('Y-m-d'),
                    'status' => 'in_progress',
                    'evaluation_score' => '4.7 / 5',
                    'notes' => 'تم إجراء المحاكاة الميدانية الأولى بنجاح مع 25 منظماً متطوعاً.',
                ],
                [
                    'user_id' => $coordinators[min(1, $coordinators->count() - 1)]->id,
                    'team_name' => 'اللجنة التنظيمية الرئيسية',
                    'task_description' => 'إعداد وتجهيز نماذج التقييم الرقمية لآراء الشركات والزوار وربطها بنظام الإحصائيات الفوري.',
                    'start_date' => now()->subDays(12)->format('Y-m-d'),
                    'due_date' => now()->subDays(1)->format('Y-m-d'),
                    'status' => 'completed',
                    'evaluation_score' => '5.0 / 5',
                    'notes' => 'تم اعتماد ونشر جميع النماذج بنجاح على منصة تدريب الخريجين.',
                ],
            ];

            foreach ($defaultTasks as $dt) {
                JobFairTask::updateOrCreate(
                    [
                        'user_id' => $dt['user_id'],
                        'task_description' => $dt['task_description'],
                    ],
                    array_merge($dt, [
                        'job_fair_id' => $jobFair?->id,
                        'supervisor_id' => $leadAdmin->id,
                        'signature_name' => $leadAdmin->name,
                        'signed_at' => now(),
                    ])
                );
            }
        }
    }
}
