<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Survey;
use App\Models\SurveyResponse;
use App\Models\User;

class SurveyWithResponsesSeeder extends Seeder
{
    public function run(): void
    {
        $graduates = User::where('role', 'graduate')->get();
        $companies = User::where('role', 'company')->get();

        // =========================================================================
        // 1. استبيان قياس أثر البرنامج التدريبي ورضا المتدربين (شهر 9)
        // =========================================================================
        $trainingSurvey = Survey::updateOrCreate(
            ['slug' => 'training-impact-evaluation-survey'],
            [
                'title' => 'استبيان قياس أثر البرنامج التدريبي ورضا المتدربين - سبتمبر 2026',
                'description' => 'استبيان تقييمي لقياس جودة التدريب، كفاءة المدرب، ملائمة المادة العلمية، والمهارات المكتسبة لسوق العمل.',
                'target_audience' => 'graduates',
                'type' => 'training',
                'is_active' => true,
                'is_public' => true,
                'start_date' => now()->subDays(10),
                'end_date' => now()->addDays(20),
                'questions' => [
                    [
                        'question' => 'ما مدى تقييمك العام لجودة البرنامج التدريبي وملاءمته لاحتياجاتك؟',
                        'type' => 'rating',
                        'max_rating' => 5,
                        'required' => true,
                        'description' => '1 يعني ضعيف جداً، 5 يعني ممتاز وتجاوز التوقعات',
                    ],
                    [
                        'question' => 'تقييم كفاءة المدرب وقدرته على الشرح وإيصال المعلومات وتفاعله مع المتدربين',
                        'type' => 'rating',
                        'max_rating' => 5,
                        'required' => true,
                        'description' => 'تقييم المحاضر وأسلوب التدريب التفاعلي',
                    ],
                    [
                        'question' => 'هل كانت التطبيقات العملية وورش العمل كافية لترسيخ المهارات المطلوبة؟',
                        'type' => 'radio',
                        'required' => true,
                        'description' => 'حدد مدى كفاية الجانب العملي والتطبيقي',
                        'options' => [
                            'نعم، كافية وممتازة جداً',
                            'إلى حد ما، مقبولة ولكن تحتاج ساعات إضافية',
                            'غير كافية والجانب النظري طغى على التدريب',
                        ],
                    ],
                    [
                        'question' => 'ما هي أهم المهارات العملية التي اكتسبتها خلال هذا التدريب؟',
                        'type' => 'checkbox',
                        'required' => true,
                        'description' => 'يمكنك اختيار أكثر من مهارة',
                        'options' => [
                            'تطوير النظم وبناء التطبيقات الحديثة',
                            'إدارة وتخطيط المشاريع البرمجية',
                            'مهارات العمل الجماعي وحل المشكلات المعقدة',
                            'أمن المعلومات وأفضل الممارسات البرمجية',
                            'تحليل المتطلبات وقواعد البيانات المتقدمة',
                        ],
                    ],
                    [
                        'question' => 'أذكر أهم ملاحظاتك ومقترحاتك لتطوير البرامج التدريبية القادمة',
                        'type' => 'textarea',
                        'required' => false,
                        'description' => 'رأيك يهمنا لمواصلة الارتقاء بجودة التدريب في جامعة طرابلس',
                    ],
                ],
            ]
        );

        // Delete previous responses for this survey to avoid duplicates
        SurveyResponse::where('survey_id', $trainingSurvey->id)->delete();

        // Sample feedback entries
        $feedbackPool = [
            'البرنامج التدريبي كان ممتازاً ورائعاً جداً، والمدرب ذو كفاءة عالية واستفدت عملياً من مشاريع التخرج المصغرة.',
            'تنظيم رائع من مكتب التدريب، وأتمنى تنظيم ورش عمل متقدمة في الذكاء الاصطناعي والحوسبة السحابية.',
            'التدريب كان غنياً بالمعلومات القيمة ومحاكاة بيئة العمل الواقعية في الشركات.',
            'أشكر المدرب على صبره وإتاحته الفرصة لنا للنقاش وتطبيق الأكواد خطوة بخطوة.',
            'مبادرة متميزة من الجامعة لتقليص الفجوة بين الدراسة الأكاديمية وسوق العمل الليبي.',
            'استفدت كثيراً من جانب حل المشكلات البرمجية والتعامل مع قواعد البيانات الضخمة.',
            'أتمنى تمديد فترة التدريب لأسبوع إضافي لتغطية المزيد من التطبيقات الحية.',
            'بيئة التدريب في القاعات والمختبرات كانت مريحة ومجهزة بكل المستلزمات.',
        ];

        // Seed 12 responses from graduates
        $gradList = $graduates->all();
        $sampleCount = max(8, count($gradList));

        for ($i = 0; $i < $sampleCount; $i++) {
            $user = isset($gradList[$i]) ? $gradList[$i] : null;

            // Generate realistic answers
            $q1Rating = [5, 5, 4, 5, 4, 5, 4, 3, 5, 4, 5, 5][$i % 12];
            $q2Rating = [5, 4, 5, 5, 4, 5, 5, 4, 4, 5, 5, 4][$i % 12];
            $q3Radio = [
                'نعم، كافية وممتازة جداً',
                'نعم، كافية وممتازة جداً',
                'إلى حد ما، مقبولة ولكن تحتاج ساعات إضافية',
                'نعم، كافية وممتازة جداً',
                'نعم، كافية وممتازة جداً',
                'إلى حد ما، مقبولة ولكن تحتاج ساعات إضافية',
                'غير كافية والجانب النظري طغى على التدريب',
                'نعم، كافية وممتازة جداً',
            ][$i % 8];

            $q4Checkbox = [];
            if ($i % 2 === 0) $q4Checkbox[] = 'تطوير النظم وبناء التطبيقات الحديثة';
            if ($i % 3 === 0 || $i % 2 !== 0) $q4Checkbox[] = 'مهارات العمل الجماعي وحل المشكلات المعقدة';
            if ($i % 4 === 0) $q4Checkbox[] = 'إدارة وتخطيط المشاريع البرمجية';
            if ($i % 5 === 0) $q4Checkbox[] = 'أمن المعلومات وأفضل الممارسات البرمجية';
            if (empty($q4Checkbox)) {
                $q4Checkbox = ['تطوير النظم وبناء التطبيقات الحديثة', 'مهارات العمل الجماعي وحل المشكلات المعقدة'];
            }

            $q5Text = $feedbackPool[$i % count($feedbackPool)];

            SurveyResponse::create([
                'survey_id' => $trainingSurvey->id,
                'user_id' => $user ? $user->id : 1,
                'participant_name' => $user ? $user->name : "متدرب خريج $i",
                'participant_email' => $user ? $user->email : "graduate$i@uot.edu.ly",
                'is_external' => false,
                'answers' => [
                    0 => $q1Rating,
                    1 => $q2Rating,
                    2 => $q3Radio,
                    3 => $q4Checkbox,
                    4 => $q5Text,
                ],
                'submitted_at' => now()->subDays(rand(1, 8))->subHours(rand(1, 12)),
            ]);
        }

        // =========================================================================
        // 2. استبيان قياس رضا الشركات وأرباب العمل الشريكة
        // =========================================================================
        $companySurvey = Survey::updateOrCreate(
            ['slug' => 'company-employer-satisfaction-survey'],
            [
                'title' => 'استبيان قياس رضا الشركات وأرباب العمل عن كفاءة الخريجين',
                'description' => 'استبيان سنوي موجّه للشركات والمؤسسات الشريكة لتقييم مهارات وجاهزية خريجي جامعة طرابلس في سوق العمل.',
                'target_audience' => 'companies',
                'type' => 'job_opportunity',
                'is_active' => true,
                'is_public' => true,
                'start_date' => now()->subDays(15),
                'end_date' => now()->addDays(30),
                'questions' => [
                    [
                        'question' => 'ما مدى رضاكم العام عن المستوى المعرفي والتقني لخريجي جامعة طرابلس؟',
                        'type' => 'rating',
                        'max_rating' => 5,
                        'required' => true,
                        'description' => 'تقييم عام للمستوى الأكاديمي والمهاري للمرشحين',
                    ],
                    [
                        'question' => 'تقييم التزام وانضباط الخريجين بأخلاقيات العمل والمسؤولية المهنية',
                        'type' => 'rating',
                        'max_rating' => 5,
                        'required' => true,
                        'description' => null,
                    ],
                    [
                        'question' => 'ما مدى جاهزية الخريجين للاندماج المباشر في فرق العمل البرمجية والتقنية؟',
                        'type' => 'radio',
                        'required' => true,
                        'options' => [
                            'جاهزية عالية جداً وقدرة سريعة على الإنتاج',
                            'جاهزية جيدة وتحتاج فترة تأهيل أولي بسيطة',
                            'تحتاج برامج تدريبية وتأهيلية مكثفة',
                        ],
                    ],
                    [
                        'question' => 'ما هي أبرز المهارات التي تبحث عنها شركتكم في التوظيف حالياً؟',
                        'type' => 'checkbox',
                        'required' => true,
                        'options' => [
                            'تطوير واجهات وتطبيقات الويب والموبايل',
                            'إدارة الشبكات والأمن السيبراني',
                            'تحليل البيانات وذكاء الأعمال',
                            'مهارات التواصل وإدارة الاجتماعات والعملاء',
                            'اللغة الإنجليزية التقنية المتخصصة',
                        ],
                    ],
                    [
                        'question' => 'مقترحات الشركة لتعزيز الشراكة مع مكتب التدريب والتأهيل بالجامعة',
                        'type' => 'textarea',
                        'required' => false,
                        'description' => 'توصيات لتعزيز فرص توظيف وتدريب الخريجين',
                    ],
                ],
            ]
        );

        // Delete previous responses for company survey
        SurveyResponse::where('survey_id', $companySurvey->id)->delete();

        $companyFeedbacks = [
            'خريجو جامعة طرابلس يمتلكون أساساً أكاديمياً قوياً جداً، ونرحب دائماً باستقبالهم في برامج التدريب والتوظيف لدينا.',
            'نقترح التركيز أكثر على أطر العمل الحديثة المستخدمة في الشركات والتحكم بالإصدارات (Git/CI/CD).',
            'تعاون ممتاز من مكتب التدريب والتأهيل، ومعرض التوظيف الأخير أتاح لنا مقابلة كوادر واعدة جداً.',
            'نوصي بإضافة مشاريع حقيقية بالشراكة مع الشركات المحلية ضمن متطلبات التخرج.',
        ];

        for ($j = 0; $j < 5; $j++) {
            $company = $companies->count() > $j ? $companies[$j] : null;
            $cName = $company ? $company->name : "شركة النخبة للتقنية " . ($j + 1);
            $cEmail = $company ? $company->email : "hr$j@company.ly";

            SurveyResponse::create([
                'survey_id' => $companySurvey->id,
                'user_id' => $company ? $company->id : 10,
                'participant_name' => $cName,
                'participant_email' => $cEmail,
                'is_external' => false,
                'answers' => [
                    0 => [4, 5, 4, 5, 4][$j],
                    1 => [5, 4, 5, 4, 5][$j],
                    2 => [
                        'جاهزية عالية جداً وقدرة سريعة على الإنتاج',
                        'جاهزية عالية جداً وقدرة سريعة على الإنتاج',
                        'جاهزية جيدة وتحتاج فترة تأهيل أولي بسيطة',
                        'جاهزية عالية جداً وقدرة سريعة على الإنتاج',
                        'جاهزية جيدة وتحتاج فترة تأهيل أولي بسيطة',
                    ][$j],
                    3 => [
                        'تطوير واجهات وتطبيقات الويب والموبايل',
                        'تحليل البيانات وذكاء الأعمال',
                        'مهارات التواصل وإدارة الاجتماعات والعملاء',
                    ],
                    4 => $companyFeedbacks[$j % count($companyFeedbacks)],
                ],
                'submitted_at' => now()->subDays(rand(2, 12)),
            ]);
        }
    }
}
