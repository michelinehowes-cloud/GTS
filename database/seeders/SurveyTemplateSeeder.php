<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SurveyTemplate;

class SurveyTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            // =========================================================================
            // 🎓 1. محور التدريب وورش العمل (Training & Workshops)
            // =========================================================================
            [
                'title' => 'استبيان قياس أثر البرنامج التدريبي ورضا المتدربين',
                'description' => 'نموذج شامل لقياس جودة التدريب، كفاءة المدرب، ملائمة المادة العلمية، والمهارات المكتسبة لسوق العمل بعد انتهاء البرنامج.',
                'category' => 'training',
                'target_audience' => 'graduates',
                'type' => 'training',
                'is_system' => true,
                'questions' => [
                    [
                        'question' => 'ما مدى تقييمك العام لجودة البرنامج التدريبي؟',
                        'type' => 'rating',
                        'max_rating' => 5,
                        'required' => true,
                        'description' => '1 يعني ضعيف جداً، 5 يعني ممتاز',
                    ],
                    [
                        'question' => 'تمكن المدرب من المادة العلمية وقدرته على إيصال المعلومات بسلاسة',
                        'type' => 'rating',
                        'max_rating' => 5,
                        'required' => true,
                        'description' => 'تقييم أداء المدرب وتفاعله مع المتدربين',
                    ],
                    [
                        'question' => 'مدى وضوح المحتوى التدريبي وملاءمة التطبيقات العملية لاحتياجات سوق العمل',
                        'type' => 'rating',
                        'max_rating' => 5,
                        'required' => true,
                        'description' => null,
                    ],
                    [
                        'question' => 'تقييم البيئة التدريبية والتنظيم والالتزام بالجدول الزمني',
                        'type' => 'rating',
                        'max_rating' => 5,
                        'required' => true,
                        'description' => null,
                    ],
                    [
                        'question' => 'ما هي أهم المهارات العملية التي اكتسبتها خلال هذا التدريب؟',
                        'type' => 'textarea',
                        'required' => true,
                        'description' => 'أذكر أهم المفاهيم أو الأدوات التي تمكنت منها',
                    ],
                    [
                        'question' => 'هل ترى أن هذا البرنامج سيساهم بشكل مباشر في زيادة فرصك الوظيفية؟',
                        'type' => 'radio',
                        'required' => true,
                        'description' => null,
                        'options' => [
                            'نعم، بشكل كبير جداً',
                            'نعم، إلى حد ما',
                            'غير متأكد',
                            'لا، يحتاج إلى تطبيقات أكثر عمقاً',
                        ],
                    ],
                    [
                        'question' => 'مقترحاتك وملاحظاتك لتطوير النسخ القادمة من هذا البرنامج',
                        'type' => 'textarea',
                        'required' => false,
                        'description' => 'رأيك يهمنا لتحسين جودة التدريب المستمر',
                    ],
                ],
            ],

            [
                'title' => 'استبيان تقييم المدرب لأداء والتزام المتدربين',
                'description' => 'نموذج موجه للمدربين لتقييم تفاعل المتدربين، التزامهم بالحضور، وجاهزيتهم المهنية لدخول سوق العمل.',
                'category' => 'training',
                'target_audience' => 'training_coordinators',
                'type' => 'training',
                'is_system' => true,
                'questions' => [
                    [
                        'question' => 'مستوى انضباط والتزام المتدربين بالمواعيد والحضور اليومي',
                        'type' => 'rating',
                        'max_rating' => 5,
                        'required' => true,
                        'description' => null,
                    ],
                    [
                        'question' => 'مدى التفاعل والمشاركة الإيجابية وإنجاز التكليفات العملية أثناء الجلسات',
                        'type' => 'rating',
                        'max_rating' => 5,
                        'required' => true,
                        'description' => null,
                    ],
                    [
                        'question' => 'مدى استيعاب المتدربين للمفاهيم الأساسية والتطبيقات المهنية المطروحة',
                        'type' => 'rating',
                        'max_rating' => 5,
                        'required' => true,
                        'description' => null,
                    ],
                    [
                        'question' => 'ما هي نسبة المتدربين الجاهزين فوراً للعمل في هذا المجال بعد اجتياز الدورة؟',
                        'type' => 'radio',
                        'required' => true,
                        'description' => null,
                        'options' => [
                            'أكثر من 80% من المتدربين',
                            'بين 50% و 80%',
                            'بين 30% و 50%',
                            'أقل من 30% (يحتاجون تدريباً متقدماً إضافياً)',
                        ],
                    ],
                    [
                        'question' => 'توصيات المدرب للمتدربين لتطوير مهاراتهم بعد التدريب',
                        'type' => 'textarea',
                        'required' => false,
                        'description' => null,
                    ],
                ],
            ],

            [
                'title' => 'استبيان حصر ودراسة الاحتياجات التدريبية للخريجين (TNA)',
                'description' => 'استبيان دوري لمعرفة متطلبات الخريجين والمهارات الأكثر طلباً لبناء الخطة التدريبية الفصلية للمكتب.',
                'category' => 'training',
                'target_audience' => 'graduates',
                'type' => 'training',
                'is_system' => true,
                'questions' => [
                    [
                        'question' => 'الكلية أو التخصص الأكاديمي وسنة التخرج',
                        'type' => 'text',
                        'required' => true,
                        'description' => 'مثال: تقنية معلومات / محاسبة / هندسة - 2025',
                    ],
                    [
                        'question' => 'ما هي المجالات التقنية أو المهنية التي ترغب بشدة في تطوير مهاراتك بها؟',
                        'type' => 'checkbox',
                        'required' => true,
                        'description' => 'يمكنك اختيار أكثر من مجال',
                        'options' => [
                            'تحليل وإدارة البيانات (Data Analysis & Excel/PowerBI)',
                            'التسويق الرقمي وإدارة الحملات (Digital Marketing)',
                            'إدارة المشاريع الاحترافية (Project Management PMP/Agile)',
                            'الأمن السيبراني وحماية الشبكات (Cybersecurity)',
                            'البرمجة وتطوير الويب والتطبيقات (Web & App Development)',
                            'اللغة الإنجليزية المهنية للأعمال (Business English)',
                            'المحاسبة والأنظمة المالية الرقمية (Digital Accounting & ERP)',
                            'مهارات التواصل وإعداد السيرة الذاتية واجتياز المقابلات',
                        ],
                    ],
                    [
                        'question' => 'ما هو نمط التدريب الأنسب لك؟',
                        'type' => 'radio',
                        'required' => true,
                        'description' => null,
                        'options' => [
                            'حضوري بالكامل بمقر جامعة طرابلس',
                            'تدريب مدمج (حضوري وعبر الإنترنت)',
                            'عبر الإنترنت بالكامل (Online Interactive)',
                        ],
                    ],
                    [
                        'question' => 'التوقيت الأنسب لحضور الورش والدورات التدريبية',
                        'type' => 'radio',
                        'required' => true,
                        'description' => null,
                        'options' => [
                            'الفترة الصباحية (9:00 ص - 1:00 م)',
                            'الفترة المسائية (3:00 م - 6:00 م)',
                            'خلال عطلة نهاية الأسبوع (الجمعة والسبت)',
                        ],
                    ],
                    [
                        'question' => 'دورات أو مهارات محددة تقترح على المكتب إضافتها في الخطة القادمة',
                        'type' => 'textarea',
                        'required' => false,
                        'description' => null,
                    ],
                ],
            ],

            // =========================================================================
            // 💼 2. محور التوظيف والشراكات (Employment & Partnerships)
            // =========================================================================
            [
                'title' => 'استبيان رضا الشركات الشريكة وأرباب العمل عن أداء الخريجين',
                'description' => 'نموذج موجه للمؤسسات والشركات الشريكة لقياس مدى الرضا عن الخريجين المرشحين والموظفين لديهم.',
                'category' => 'employment',
                'target_audience' => 'companies',
                'type' => 'job_opportunity',
                'is_system' => true,
                'questions' => [
                    [
                        'question' => 'مدى رضاكم العام عن كفاءة وجودة خريجي جامعة طرابلس المرشحين للعمل لديكم',
                        'type' => 'rating',
                        'max_rating' => 5,
                        'required' => true,
                        'description' => 'تقييم عام للمستوى الوظيفي والجاهزية',
                    ],
                    [
                        'question' => 'التزام الخريجين بالانضباط وأخلاقيات العمل وثقافة المؤسسة',
                        'type' => 'rating',
                        'max_rating' => 5,
                        'required' => true,
                        'description' => null,
                    ],
                    [
                        'question' => 'سرعة التعلم والتكيف مع بيئة العمل والمهام المسندة إليهم',
                        'type' => 'rating',
                        'max_rating' => 5,
                        'required' => true,
                        'description' => null,
                    ],
                    [
                        'question' => 'مدى رغبة شركتكم في توظيف المزيد من خريجي الجامعة في الفرص الشاغرة مستقبلاً',
                        'type' => 'radio',
                        'required' => true,
                        'description' => null,
                        'options' => [
                            'بالتأكيد، تمثل الجامعة خيارنا الأول',
                            'نعم، نرحب بذلك',
                            'ربما حسب طبيعة الوظيفة الشاغرة',
                            'لا نفضل ذلك حالياً',
                        ],
                    ],
                    [
                        'question' => 'ما هي أهم المهارات أو المعارف التي تنصحون المكتب بتركيز التدريب عليها لسد الفجوة مع سوق العمل؟',
                        'type' => 'textarea',
                        'required' => true,
                        'description' => 'ملاحظاتكم تساهم مباشرة في تحديث خطط التأهيل الوظيفي',
                    ],
                ],
            ],

            [
                'title' => 'استبيان تتبع الخريجين بعد التوظيف (Graduate Employment Tracer)',
                'description' => 'نموذج لمتابعة مسار الخريجين الوظيفي بعد التخرج وتأثير تدريب المكتب على استقرارهم المهني.',
                'category' => 'employment',
                'target_audience' => 'graduates',
                'type' => 'job_opportunity',
                'is_system' => true,
                'questions' => [
                    [
                        'question' => 'ما هي حالتك الوظيفية الحالية؟',
                        'type' => 'radio',
                        'required' => true,
                        'description' => null,
                        'options' => [
                            'موظف بدوام كامل (Full-time)',
                            'موظف بدوام جزئي (Part-time)',
                            'أعمل لحسابي الخاص / ريادي أعمال (Freelancer / Business Owner)',
                            'في فترة تدريب عملي (Internship)',
                            'أبحث حالياً عن فرصة عمل مناسبة',
                        ],
                    ],
                    [
                        'question' => 'المسمى الوظيفي الحالي والقطاع (إن وجد)',
                        'type' => 'text',
                        'required' => false,
                        'description' => 'مثال: أخصائي شبكات - القطاع المصرفي',
                    ],
                    [
                        'question' => 'هل عملك الحالي يتطابق مع تخصصك الدراسي في الجامعة؟',
                        'type' => 'radio',
                        'required' => true,
                        'description' => null,
                        'options' => [
                            'نعم، تطابق تام',
                            'نعم، في مجال قريب أو ذي صلة',
                            'لا، مجال مختلف تماماً',
                        ],
                    ],
                    [
                        'question' => 'كم استغرقت من الوقت للحصول على وظيفتك الأولى بعد التخرج؟',
                        'type' => 'radio',
                        'required' => true,
                        'description' => null,
                        'options' => [
                            'أقل من 3 أشهر',
                            'من 3 إلى 6 أشهر',
                            'من 6 أشهر إلى سنة',
                            'أكثر من سنة',
                        ],
                    ],
                    [
                        'question' => 'مدى مساهمة ورش وخدمات مكتب التدريب والتأهيل في حصولك على الوظيفة أو اجتياز المقابلة',
                        'type' => 'rating',
                        'max_rating' => 5,
                        'required' => true,
                        'description' => null,
                    ],
                ],
            ],

            // =========================================================================
            // 🎪 3. محور المعارض والفعاليات (Job Fairs & Events)
            // =========================================================================
            [
                'title' => 'استبيان تقييم زوار ومعرض التوظيف السنوي (ملتقى الخريجين)',
                'description' => 'نموذج موجه للزوار والخريجين والطلبة لتقييم تنظيم معرض التوظيف وتنوع الفرص وأجنحة الشركات.',
                'category' => 'events',
                'target_audience' => 'graduates',
                'type' => 'job_fair',
                'is_system' => true,
                'questions' => [
                    [
                        'question' => 'التقييم العام لتنظيم معرض التوظيف واستقبال الزوار',
                        'type' => 'rating',
                        'max_rating' => 5,
                        'required' => true,
                        'description' => null,
                    ],
                    [
                        'question' => 'تنوع الشركات والمؤسسات المشاركة وملاءمة الفرص المعروضة لمختلف التخصصات',
                        'type' => 'rating',
                        'max_rating' => 5,
                        'required' => true,
                        'description' => null,
                    ],
                    [
                        'question' => 'تقييم ورش العمل والجلسات الحوارية والإرشاد المهني المصاحبة للمعرض',
                        'type' => 'rating',
                        'max_rating' => 5,
                        'required' => true,
                        'description' => null,
                    ],
                    [
                        'question' => 'كم عدد المقابلات الفورية أو طلبات التوظيف التي تقدمت لها خلال المعرض؟',
                        'type' => 'radio',
                        'required' => true,
                        'description' => null,
                        'options' => [
                            '1 إلى 2 شركات',
                            '3 إلى 5 شركات',
                            'أكثر من 5 شركات',
                            'لم أتقدم (اقتصرت على الزيارة والاستكشاف)',
                        ],
                    ],
                    [
                        'question' => 'ما الذي نال إعجابك أكثر في المعرض؟ وما هي اقتراحاتك للنسخة القادمة؟',
                        'type' => 'textarea',
                        'required' => false,
                        'description' => null,
                    ],
                ],
            ],

            [
                'title' => 'استبيان تقييم الشركات المشاركة في ملتقى التوظيف ومعرض المشاريع',
                'description' => 'نموذج موجه لمسؤولي الموارد البشرية والشركات العارضة لتقييم التسهيلات اللوجستية ونوعية الخريجين.',
                'category' => 'events',
                'target_audience' => 'companies',
                'type' => 'job_fair',
                'is_system' => true,
                'questions' => [
                    [
                        'question' => 'مستوى التنظيم والتنسيق اللوجستي والدعم الفني المقدم لجناح شركتكم',
                        'type' => 'rating',
                        'max_rating' => 5,
                        'required' => true,
                        'description' => null,
                    ],
                    [
                        'question' => 'مستوى إقبال وجاهزية الخريجين والباحثين عن العمل الذين زاروا جناحكم',
                        'type' => 'rating',
                        'max_rating' => 5,
                        'required' => true,
                        'description' => null,
                    ],
                    [
                        'question' => 'تقييم جودة وأفكار مشاريع التخرج المعروضة وقابليتها للتبني والاستثمار',
                        'type' => 'rating',
                        'max_rating' => 5,
                        'required' => true,
                        'description' => null,
                    ],
                    [
                        'question' => 'هل تنوي شركتكم المشاركة في المعارض والملتقيات القادمة التي تنظمها الجامعة؟',
                        'type' => 'radio',
                        'required' => true,
                        'description' => null,
                        'options' => [
                            'نعم، بالتأكيد',
                            'نعم، إذا توفرت فرص وشواغر لدينا',
                            'سنقرر لاحقاً',
                        ],
                    ],
                    [
                        'question' => 'ملاحظاتكم ومقترحاتكم لتحسين بيئة المعرض وتعزيز الشراكة مع الجامعة',
                        'type' => 'textarea',
                        'required' => false,
                        'description' => null,
                    ],
                ],
            ],
        ];

        foreach ($templates as $templateData) {
            SurveyTemplate::updateOrCreate(
                ['title' => $templateData['title']],
                $templateData
            );
        }
    }
}
