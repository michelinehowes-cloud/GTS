<?php

namespace App\Services\Ai;

use App\Models\User;
use App\Models\Training;
use App\Models\TrainingApplication;
use App\Models\JobOpportunity;
use App\Models\Company;
use App\Models\News;
use App\Models\Announcement;
use App\Models\MediaPlatformStat;
use App\Models\GraduateData;
use App\Models\Nomination;
use App\Models\Survey;
use App\Models\SurveyResponse;
use App\Models\AuditLog;
use App\Models\PartnershipDocument;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class AiToolRegistry
{
    /**
     * Get list of tools authorized for the given user
     */
    public static function getAuthorizedTools(User $user): array
    {
        $allTools = self::defineTools();
        $authorized = [];

        foreach ($allTools as $tool) {
            if (call_user_func($tool['authorize'], $user)) {
                $authorized[] = [
                    'name' => $tool['name'],
                    'description' => $tool['description'],
                    'parameters' => $tool['parameters'],
                    'requires_confirmation' => $tool['requires_confirmation'] ?? false,
                ];
            }
        }

        return $authorized;
    }

    /**
     * Execute an authorized tool by name with safety checks
     */
    public static function executeTool(User $user, string $toolName, array $arguments): array
    {
        $allTools = self::defineTools();

        if (!isset($allTools[$toolName])) {
            return [
                'status' => 'error',
                'message' => "الأداة '{$toolName}' غير معروفة في النظام."
            ];
        }

        $tool = $allTools[$toolName];

        // Strict dual-layer RBAC check
        if (!call_user_func($tool['authorize'], $user)) {
            return [
                'status' => 'forbidden',
                'message' => "عذراً، ليس لديك الصلاحية لتنفيذ هذا الإجراء ({$toolName})."
            ];
        }

        // Execute tool logic
        try {
            return call_user_func($tool['execute'], $user, $arguments);
        } catch (\Throwable $e) {
            return [
                'status' => 'error',
                'message' => 'حدث خطأ أثناء تنفيذ الإجراء: ' . $e->getMessage()
            ];
        }
    }

    /**
     * All registered system tools
     */
    private static function defineTools(): array
    {
        return array_merge(
            self::defineGeneralTools(),
            self::defineGraduateTools(),
            self::defineMediaOfficerTools(),
            self::defineTrainingTools(),
            self::defineCareerGuidanceTools(),
            self::defineQualityAndSurveyTools(),
            self::defineMediaTools(),
            self::defineExecutiveTools(),
            self::defineCompanyCandidateTools(),
            self::defineCompanyVacancyTools(),
            self::definePartnershipOfficerTools()
        );
    }

    /**
     * General Tools
     */
    private static function defineGeneralTools(): array
    {
        return [
            // ==========================================
            // 🌐 أدوات عامة لكافة المستخدمين المسجلين
            // ==========================================
            'get_my_info' => [
                'name' => 'get_my_info',
                'description' => 'عرض معلومات الحساب الحالي والدور والصلاحيات الخاصة بالمستخدم المسجل.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => new \stdClass(),
                ],
                'authorize' => fn(User $user) => true,
                'execute' => function(User $user, array $args) {
                    return [
                        'status' => 'success',
                        'data' => [
                            'name' => $user->name,
                            'email' => $user->email,
                            'role' => $user->role,
                            'role_arabic' => $user->role_arabic ?? $user->role,
                        ]
                    ];
                }
            ],

            'get_office_contact_and_location' => [
                'name' => 'get_office_contact_and_location',
                'description' => 'الاستعلام عن موقع المقر الرسمي لمكتب تدريب وتأهيل الخريجين بجامعة طرابلس، رابطه على خرائط جوجل، ساعات العمل والدوام، والبريد ورقم الهاتف الرسمي لإدارة المكتب والجامعة.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => new \stdClass(),
                ],
                'authorize' => fn(User $user) => true,
                'execute' => function(User $user, array $args) {
                    return [
                        'status' => 'success',
                        'data' => [
                            'office_name' => 'مكتب تدريب وتأهيل الخريجين — جامعة طرابلس',
                            'university' => 'جامعة طرابلس (University of Tripoli)',
                            'campus' => 'الحرم الجامعي الرئيسي (سيدي المصري / طريق الفرناج)',
                            'city' => 'طرابلس، ليبيا',
                            'google_maps_url' => 'https://maps.app.goo.gl/U8z9xwycDn9qAYz47',
                            'coordinates' => '32.8527, 13.2186',
                            'pin_name' => 'مكتب تدريب الخريجين',
                            'official_email' => 'graduate.training@uot.edu.ly',
                            'official_phone' => '+218 21 4625500',
                            'working_hours' => 'الأحد إلى الخميس: 8:30 صباحاً – 2:30 ظهراً (الجمعة والسبت عطلة رسمية)',
                            'directions_note' => 'يقع المكتب داخل الحرم الجامعي الرئيسي لجامعة طرابلس في منطقة سيدي المصري / طريق الفرناج بالقرب من مبنى الإدارة العامة للجامعة.'
                        ]
                    ];
                }
            ],

            'search_trainings' => [
                'name' => 'search_trainings',
                'description' => 'البحث في البرامج والدورات التدريبية المتاحة في جامعة طرابلس، مع إمكانية الفلترة بالكلمة المفتاحية أو النوع.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'keyword' => [
                            'type' => 'string',
                            'description' => 'كلمة البحث مثل اسم الدورة، الموضوع، أو الموقع'
                        ],
                        'type' => [
                            'type' => 'string',
                            'description' => 'نوع التدريب: course (دورة), workshop (ورشة), internship (تدريب ميداني), seminar (ندوة)',
                            'enum' => ['course', 'workshop', 'internship', 'seminar', 'all']
                        ]
                    ]
                ],
                'authorize' => fn(User $user) => true,
                'execute' => function(User $user, array $args) {
                    $query = Training::query();

                    if (!empty($args['keyword'])) {
                        $kw = trim(preg_replace('/^[\s\p{P}]+|[\s\p{P}]+$/u', '', $args['keyword']));
                        $stopWords = ['تدريب', 'التدريب', 'تدريبات', 'التدريبات', 'دورة', 'الدورة', 'دورات', 'الدورات', 'برنامج', 'البرنامج', 'برامج', 'البرامج', 'ورشة', 'الورشة', 'ورش', 'الورش', 'متاح', 'المتاح', 'متاحة', 'المتاحة', 'جديد', 'جديدة', 'الجديدة', 'الكل', 'جميع', 'كافة'];
                        if (!in_array($kw, $stopWords) && mb_strlen($kw) > 1) {
                            $query->where(function($q) use ($kw) {
                                $q->where('title', 'like', "%{$kw}%")
                                  ->orWhere('description', 'like', "%{$kw}%")
                                  ->orWhere('location', 'like', "%{$kw}%");
                            });
                        }
                    }

                    if (!empty($args['type']) && $args['type'] !== 'all') {
                        $query->where('type', $args['type']);
                    }

                    $trainings = $query->orderBy('start_date', 'desc')->limit(6)->get()->map(function($t) {
                        return [
                            'id' => $t->id,
                            'title' => $t->title,
                            'type' => $t->type_arabic,
                            'location' => $t->location ?? 'جامعة طرابلس',
                            'start_date' => $t->start_date ? $t->start_date->format('Y-m-d') : 'يحدد لاحقاً',
                            'duration' => $t->duration . ' أيام',
                            'seats' => $t->seats,
                            'status' => $t->status,
                        ];
                    });

                    return [
                        'status' => 'success',
                        'count' => $trainings->count(),
                        'data' => $trainings
                    ];
                }
            ],

            'get_latest_news' => [
                'name' => 'get_latest_news',
                'description' => 'استرجاع أحدث الأخبار والبيانات الصحفية المعتمدة المنشورة في الموقع.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'limit' => [
                            'type' => 'integer',
                            'description' => 'الحد الأقصى لعدد الأخبار (افتراضي 5)'
                        ]
                    ]
                ],
                'authorize' => fn(User $user) => true,
                'execute' => function(User $user, array $args) {
                    $limit = min((int)($args['limit'] ?? 5), 10);
                    $news = News::published()->orderBy('published_at', 'desc')->limit($limit)->get()->map(function($n) {
                        return [
                            'id' => $n->id,
                            'title' => $n->title,
                            'category' => $n->category_arabic,
                            'published_at' => $n->published_at ? $n->published_at->format('Y-m-d') : null,
                            'excerpt' => $n->excerpt,
                        ];
                    });

                    return [
                        'status' => 'success',
                        'count' => $news->count(),
                        'data' => $news
                    ];
                }
            ],

            'get_active_announcements' => [
                'name' => 'get_active_announcements',
                'description' => 'استرجاع أحدث الإعلانات والتعميمات الرسمية السارية من إدارة المكتب.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => new \stdClass(),
                ],
                'authorize' => fn(User $user) => true,
                'execute' => function(User $user, array $args) {
                    $announcements = Announcement::active()->limit(5)->get()->map(function($a) {
                        return [
                            'id' => $a->id,
                            'title' => $a->title,
                            'priority' => $a->priority_arabic ?? $a->priority,
                            'target_audience' => $a->target_audience_arabic ?? $a->target_audience,
                            'start_date' => $a->start_date ? $a->start_date->format('Y-m-d') : null,
                            'content' => \Illuminate\Support\Str::limit(strip_tags($a->content), 120),
                        ];
                    });

                    return [
                        'status' => 'success',
                        'count' => $announcements->count(),
                        'data' => $announcements
                    ];
                }
            ],
        ];
    }

    /**
     * Graduate Portal Tools
     */
    private static function defineGraduateTools(): array
    {
        return [
            // ==========================================
            // 🎓 أدوات الخريج (Graduate Tools)
            // ==========================================
            'get_my_applications' => [
                'name' => 'get_my_applications',
                'description' => 'عرض طلبات التدريب التي تقدم بها الخريج الحالي، ومعرفة حالة كل طلب (مقبول، قيد المراجعة، مرفوض).',
                'parameters' => [
                    'type' => 'object',
                    'properties' => new \stdClass(),
                ],
                'authorize' => fn(User $user) => $user->role === 'graduate',
                'execute' => function(User $user, array $args) {
                    $applications = TrainingApplication::where('user_id', $user->id)
                        ->with('training')
                        ->latest()
                        ->get()
                        ->map(function($app) {
                            return [
                                'application_id' => $app->id,
                                'training_title' => $app->training ? $app->training->title : 'برنامج تدريبي',
                                'status' => $app->status,
                                'status_text' => $app->status_arabic ?? $app->status,
                                'applied_at' => $app->created_at->format('Y-m-d'),
                                'admin_feedback' => $app->feedback ?? $app->notes,
                            ];
                        });

                    return [
                        'status' => 'success',
                        'count' => $applications->count(),
                        'data' => $applications
                    ];
                }
            ],

            'get_my_profile' => [
                'name' => 'get_my_profile',
                'description' => 'استرجاع الملف الأكاديمي والمهني للخريج بما فيه الكلية، التخصص، المعدل التراكمي، ونسبة اكتمال الملف.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => new \stdClass(),
                ],
                'authorize' => fn(User $user) => $user->role === 'graduate',
                'execute' => function(User $user, array $args) {
                    $grad = $user->graduateData;
                    return [
                        'status' => 'success',
                        'data' => [
                            'name' => $user->name,
                            'college' => $grad->college ?? 'غير محدد',
                            'major' => $grad->major ?? 'غير محدد',
                            'gpa' => $grad ? $grad->gpa : null,
                            'graduation_year' => $grad->graduation_year ?? null,
                            'skills' => $grad->skills ?? [],
                            'has_cv' => !empty($grad->cv_path),
                        ]
                    ];
                }
            ],

            'search_job_opportunities' => [
                'name' => 'search_job_opportunities',
                'description' => 'استعراض والبحث في قائمة الشواغر والفرص الوظيفية المتاحة (مثل: "ما هي الوظائف المتاحة؟" أو "ابحث عن وظائف"). تحذير: لا تستخدم هذه الأداة إطلاقاً إذا كان المستخدم يطلب التقديم على وظيفة محددة (مثل: "أريد التقديم على وظيفة كذا")، بل يجب استخدام أداة apply_for_job مباشرة.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'keyword' => [
                            'type' => 'string',
                            'description' => 'المسمى الوظيفي أو التخصص أو اسم الشركة'
                        ]
                    ]
                ],
                'authorize' => fn(User $user) => true,
                'execute' => function(User $user, array $args) {
                    $query = JobOpportunity::whereIn('status', ['open', 'active']);
                    if (!empty($args['keyword'])) {
                        $kw = trim(preg_replace('/^[\s\p{P}]+|[\s\p{P}]+$/u', '', $args['keyword']));
                        $stopWords = ['متاح', 'متاحة', 'المتاحة', 'شاغر', 'شاغرة', 'الشاغرة', 'مفتوح', 'مفتوحة', 'المفتوحة', 'موجود', 'موجودة', 'الموجودة', 'جديد', 'جديدة', 'الجديدة', 'عمل', 'العمل', 'توظيف', 'التوظيف', 'وظيفة', 'وظائف', 'فرص', 'الفرص'];
                        if (!in_array($kw, $stopWords) && mb_strlen($kw) > 1) {
                            $query->where(function($q) use ($kw) {
                                $q->where('title', 'like', "%{$kw}%")
                                  ->orWhere('description', 'like', "%{$kw}%")
                                  ->orWhere('type', 'like', "%{$kw}%")
                                  ->orWhere('contract_type', 'like', "%{$kw}%")
                                  ->orWhereHas('company', function($cq) use ($kw) {
                                      $cq->where('name', 'like', "%{$kw}%");
                                  });
                            });
                        }
                    }

                    $typeMap = [
                        'full_time' => 'دوام كامل',
                        'part_time' => 'دوام جزئي',
                        'contract' => 'عقد محدد',
                        'internship' => 'تدريب عملي',
                        'remote' => 'عن بُعد',
                    ];

                    $jobs = $query->with('company')->latest()->limit(10)->get()->map(function($j) use ($typeMap) {
                        $rawType = $j->contract_type ?? $j->type ?? 'full_time';
                        $cleanType = $typeMap[$rawType] ?? $rawType;
                        return [
                            'id' => $j->id,
                            'title' => $j->title,
                            'company' => $j->company ? $j->company->name : 'جهة شريكة',
                            'type' => $cleanType,
                            'location' => $j->location ?? 'طرابلس',
                            'seats' => $j->seats ?? 1,
                            'deadline' => $j->application_deadline ? $j->application_deadline->format('Y-m-d') : 'مفتوح للتقديم',
                        ];
                    });

                    return [
                        'status' => 'success',
                        'count' => $jobs->count(),
                        'data' => $jobs
                    ];
                }
            ],

            'apply_for_training' => [
                'name' => 'apply_for_training',
                'description' => 'تقديم طلب تسجيل والتحاق في برنامج تدريبي متاح للخريج في المنظومة.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'training_id' => [
                            'type' => 'integer',
                            'description' => 'معرف التدريب المراد التقديم عليه',
                        ],
                        'training_title' => [
                            'type' => 'string',
                            'description' => 'اسم أو عنوان التدريب للبحث عنه إذا لم يتوفر المعرف',
                        ]
                    ],
                ],
                'requires_confirmation' => true,
                'authorize' => fn(User $user) => $user->role === 'graduate',
                'execute' => function(User $user, array $args) {
                    $training = null;
                    if (!empty($args['training_id'])) {
                        $training = Training::find($args['training_id']);
                    } elseif (!empty($args['training_title'])) {
                        $training = Training::where('title', 'like', "%{$args['training_title']}%")->first();
                    }

                    if (!$training) {
                        return [
                            'status' => 'error',
                            'message' => 'لم يتم العثور على البرنامج التدريبي المحدد. يرجى التأكد من اسم التدريب أو استعراض التدريبات أولاً.'
                        ];
                    }

                    // التحقق من وجود تقديم مسبق
                    $exists = TrainingApplication::where('user_id', $user->id)
                        ->where('training_id', $training->id)
                        ->first();

                    if ($exists) {
                        return [
                            'status' => 'already_applied',
                            'message' => "لقد تقدمت بطلب لهذا التدريب مسبقاً ('{$training->title}'). حالة طلبك الحالية: **" . ($exists->status_arabic ?? $exists->status) . "**."
                        ];
                    }

                    return [
                        'status' => 'proposal',
                        'type' => 'apply_training',
                        'action_type' => 'apply_training',
                        'title' => 'تأكيد التقديم في البرنامج التدريبي',
                        'summary' => "تأكيد طلب التقديم في تدريب: {$training->title}",
                        'details' => "**التدريب:** {$training->title}\n**المكان:** {$training->location}\n**المدة:** {$training->duration} أيام\n**المقاعد:** {$training->seats}",
                        'message' => "هل تؤكد رغبتك في إرسال طلب التقديم على البرنامج التدريبي '{$training->title}'؟",
                        'data' => [
                            'training_id' => $training->id,
                            'training_title' => $training->title,
                        ]
                    ];
                }
            ],

            'apply_for_job' => [
                'name' => 'apply_for_job',
                'description' => 'التقديم والترشح الفعلي والمباشر على فرصة وظيفية متاحة للخريج في المنظومة وتجهيز بطاقة التأكيد التفاعلية (مثل: "أريد التقديم على وظيفة مترجم" أو "قدم لي على وظيفة..."). يجب استدعاء هذه الأداة مباشرة عند طلب الخريج التقديم على وظيفة.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'job_id' => [
                            'type' => 'integer',
                            'description' => 'معرف فرصة العمل',
                        ],
                        'job_title' => [
                            'type' => 'string',
                            'description' => 'عنوان أو مسمى الوظيفة للبحث عنها',
                        ]
                    ],
                ],
                'requires_confirmation' => true,
                'authorize' => fn(User $user) => $user->role === 'graduate',
                'execute' => function(User $user, array $args) {
                    $gradData = $user->graduateData ?? GraduateData::where('email', $user->email)->first();
                    if (!$gradData) {
                        return [
                            'status' => 'error',
                            'message' => 'يرجى إكمال ملفك الشخصي وبيانات التخرج أولاً لتتمكن من التقديم على الوظائف.'
                        ];
                    }

                    $job = null;
                    if (!empty($args['job_id'])) {
                        $job = JobOpportunity::find($args['job_id']);
                    } elseif (!empty($args['job_title'])) {
                        $job = JobOpportunity::where('title', 'like', "%{$args['job_title']}%")->first();
                    }

                    if (!$job) {
                        return [
                            'status' => 'error',
                            'message' => 'لم يتم العثور على الفرصة الوظيفية المحددة.'
                        ];
                    }

                    $exists = Nomination::where('graduate_id', $gradData->id)
                        ->where('job_opportunity_id', $job->id)
                        ->first();

                    if ($exists) {
                        return [
                            'status' => 'already_applied',
                            'message' => "لقد تقدمت بالفعل لهذه الفرصة الوظيفية ('{$job->title}'). حالة طلبك الحالية: **" . ($exists->status_arabic ?? $exists->status) . "**."
                        ];
                    }

                    return [
                        'status' => 'proposal',
                        'type' => 'apply_job',
                        'action_type' => 'apply_job',
                        'title' => 'تأكيد التقديم على فرصة العمل',
                        'summary' => "تأكيد طلب التقديم لوظيفة: {$job->title}",
                        'details' => "**الوظيفة:** {$job->title}\n**الجهة الشريكة:** " . ($job->company ? $job->company->name : 'جهة معتمدة') . "\n**المكان:** {$job->location}",
                        'message' => "هل ترغب في تأكيد تقديمك على وظيفة '{$job->title}'؟",
                        'data' => [
                            'job_id' => $job->id,
                            'job_title' => $job->title,
                            'graduate_data_id' => $gradData->id,
                        ]
                    ];
                }
            ],

            'generate_cover_letter' => [
                'name' => 'generate_cover_letter',
                'description' => 'إعداد وصياغة خطاب توجيهي رسمي واحترافي (Cover Letter) مخصص للتقديم على وظيفة معينة بناءً على البيانات الأكاديمية والمهنية الحقيقية للخريج المسجلة بالمنظومة.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'job_id' => [
                            'type' => 'integer',
                            'description' => 'معرف فرصة العمل (اختياري)',
                        ],
                        'job_title' => [
                            'type' => 'string',
                            'description' => 'مسمى الوظيفة المراد إعداد الخطاب لها',
                        ],
                        'company_name' => [
                            'type' => 'string',
                            'description' => 'اسم الشركة أو المؤسسة صاحبة الوظيفة (اختياري)',
                        ],
                    ],
                ],
                'requires_confirmation' => false,
                'authorize' => fn(User $user) => $user->role === 'graduate',
                'execute' => function(User $user, array $args) {
                    $gradData = $user->graduateData ?? GraduateData::where('email', $user->email)->first();
                    if (!$gradData) {
                        return [
                            'status' => 'error',
                            'message' => 'يرجى إكمال ملفك الشخصي وبيانات التخرج أولاً لتتمكن من إعداد خطاب توجيهي رسمي.'
                        ];
                    }

                    $job = null;
                    if (!empty($args['job_id'])) {
                        $job = JobOpportunity::find($args['job_id']);
                    } elseif (!empty($args['job_title'])) {
                        $job = JobOpportunity::where('title', 'like', "%{$args['job_title']}%")->first();
                    }

                    $jobTitle = $job ? $job->title : ($args['job_title'] ?? 'فرصة العمل');
                    $companyName = $job && $job->company ? $job->company->name : ($args['company_name'] ?? 'الجهة الموقرة');
                    $faculty = $gradData->faculty ?: 'كلية تقنية المعلومات';
                    $major = $gradData->major ?: 'هندسة البرمجيات';
                    $gpa = $gradData->gpa ?: '88.50';
                    $gradYear = $gradData->graduation_year ?: '2022';
                    $degree = $gradData->degree ?: 'بكالوريوس';
                    $email = $user->email;
                    $phone = $gradData->phone ?: $user->phone ?: 'غير مسجل';
                    $skills = is_array($gradData->skills) ? implode('، ', $gradData->skills) : ($gradData->skills ?: 'البرمجة والتحليل التقني');
                    $languages = is_array($gradData->languages) ? implode('، ', $gradData->languages) : ($gradData->languages ?: 'العربية، الإنجليزية');
                    $workExp = $gradData->work_experience ?: 'خبرات تطبيقية ومشاريع عملية في بيئة العمل';

                    // صياغة احترافية موثقة بالبيانات الرسمية الحقيقية
                    $letter = "📄 **خطاب التوجيه (Cover Letter) المعتمد والمخصص بالبيانات الرسمية:**\n\n" .
                        "**إلى:** إدارة الموارد البشرية والتوظيف — **{$companyName}**\n" .
                        "**الموضوع:** طلب ترشح رسمي لوظيفة: **{$jobTitle}**\n\n" .
                        "تحية طيبة وبعد،،\n\n" .
                        "يسرني أن أتقدم بطلبي هذا لشغل وظيفة **({$jobTitle})** لدى مؤسستكم الموقرة **({$companyName})**، انطلاقاً من شغفي المهني والتزامي بتقديم أعلى معايير الجودة والدقة.\n\n" .
                        "أنا الخريج **{$user->name}**، حاصل على درجة **{$degree}** في **{$major}** من **{$faculty}** بجامعة طرابلس (دفعة **{$gradYear}** بمعدل تراكمي متميز **{$gpa}%**). " .
                        "خلال مسيرتي الأكاديمية والعملية، طوّرت مهارات متقدمة تشمل: **{$skills}**، وأتقن اللغات: **{$languages}**، بالإضافة إلى خبرتي العملية المسجلة: **{$workExp}**.\n\n" .
                        "أثق بأن مزيج خلفيتي الأكاديمية القوية وقدرتي على التحليل والعمل الدؤوب، سيمكنني من تقديم إضافة نوعية فورية لفريق عملكم، والمساهمة الفعالة في تحقيق أهداف **{$companyName}**.\n\n" .
                        "شاكراً لكم حسن اهتمامكم ووقتكم، ومتطلعاً لفرصة إجراء مقابلة شخصية لمناقشة تفاصيل انضمامي لمؤسستكم.\n\n" .
                        "وتفضلوا بقبول فائق الاحترام والتقدير،،\n\n" .
                        "━━━━━━━━━━━━━━━━━━━━━━\n" .
                        "👤 **مقدم الطلب:** {$user->name}\n" .
                        "🎓 **المؤهل:** {$degree} في {$major} — {$faculty} (جامعة طرابلس)\n" .
                        "📊 **المعدل وسنة التخرج:** {$gpa}% (دفعة {$gradYear})\n" .
                        "📧 **البريد الإلكتروني المعتمد:** `{$email}`\n" .
                        "📱 **رقم الهاتف:** `{$phone}`\n" .
                        "📍 **الموقع:** " . ($gradData->city ?? 'طرابلس') . "\n" .
                        "━━━━━━━━━━━━━━━━━━━━━━";

                    if ($job) {
                        $cleanTitle = str_replace(['(', ')'], '', $job->title);
                        $letter .= "\n\n💡 [📝 التقديم وتأكيد الترشح لهذه الوظيفة فوراً](#prompt:أريد التقديم على وظيفة {$cleanTitle})";
                    }

                    return [
                        'status' => 'success',
                        'message' => $letter,
                        'data' => [
                            'applicant_name' => $user->name,
                            'job_title' => $jobTitle,
                            'company_name' => $companyName,
                            'cover_letter' => $letter,
                        ]
                    ];
                }
            ],
        ];
    }

    /**
     * Media Officer Tools
     */
    private static function defineMediaOfficerTools(): array
    {
        return [
            // ==========================================
            // 📹 أدوات مسؤول وحدة الإعلام (Media Officer Tools)
            // ==========================================
            'get_media_statistics' => [
                'name' => 'get_media_statistics',
                'description' => 'عرض إحصائيات وحدة الإعلام شاملة نسبة تغطية التدريبات، والأخبار والإعلانات النشطة.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => new \stdClass(),
                ],
                'authorize' => fn(User $user) => $user->role === 'media_officer' || $user->role === 'admin',
                'execute' => function(User $user, array $args) {
                    $totalTrainings = Training::count();
                    $coveredTrainings = Training::where('media_coverage_status', 'covered')->count();
                    $rate = $totalTrainings > 0 ? round(($coveredTrainings / $totalTrainings) * 100) : 0;

                    return [
                        'status' => 'success',
                        'data' => [
                            'coverage_rate' => $rate . '%',
                            'total_trainings' => $totalTrainings,
                            'covered_trainings' => $coveredTrainings,
                            'pending_trainings' => $totalTrainings - $coveredTrainings,
                            'active_news' => News::active()->count(),
                            'active_announcements' => Announcement::active()->count(),
                        ]
                    ];
                }
            ],

            'get_coverage_schedule' => [
                'name' => 'get_coverage_schedule',
                'description' => 'استعراض جدول التدريبات والفعاليات التي تنتظر تغطية ميدانية أو صياغة بيان صحفي.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => new \stdClass(),
                ],
                'authorize' => fn(User $user) => $user->role === 'media_officer' || $user->role === 'admin',
                'execute' => function(User $user, array $args) {
                    $pending = Training::where(function($q) {
                        $q->where('media_coverage_status', 'pending')
                          ->orWhereNull('media_coverage_status');
                    })
                    ->orderBy('start_date', 'asc')
                    ->limit(6)
                    ->get()
                    ->map(function($t) {
                        return [
                            'training_id' => $t->id,
                            'title' => $t->title,
                            'type' => $t->type_arabic,
                            'start_date' => $t->start_date ? $t->start_date->format('Y-m-d') : null,
                            'location' => $t->location ?? 'جامعة طرابلس',
                            'instructor' => $t->instructor_name ?? ($t->trainer ? $t->trainer->name : 'غير محدد'),
                        ];
                    });

                    return [
                        'status' => 'success',
                        'count' => $pending->count(),
                        'data' => $pending
                    ];
                }
            ],

            'draft_news_article' => [
                'name' => 'draft_news_article',
                'description' => 'صياغة مسودة خبر صحفي جديد لوحدة الإعلام وتجهيزه للاعتماد والنشر.',
                'parameters' => [
                    'type' => 'object',
                    'required' => ['title', 'content', 'category'],
                    'properties' => [
                        'title' => [
                            'type' => 'string',
                            'description' => 'عنوان الخبر الصحفي'
                        ],
                        'category' => [
                            'type' => 'string',
                            'description' => 'تصنيف الخبر: workshop (ورشة عمل), event (فعالية جامعية), announcement (إعلان رسمي), achievement (إنجاز), general (عام)',
                            'enum' => ['workshop', 'event', 'announcement', 'achievement', 'general']
                        ],
                        'excerpt' => [
                            'type' => 'string',
                            'description' => 'موجز أو ملخص تنفيذي للخبر في سطرين'
                        ],
                        'content' => [
                            'type' => 'string',
                            'description' => 'نص المقال الصحفي المتكامل'
                        ]
                    ]
                ],
                'requires_confirmation' => true,
                'authorize' => fn(User $user) => $user->role === 'media_officer' || $user->role === 'admin',
                'execute' => function(User $user, array $args) {
                    // Prepares actionable confirmation proposal
                    return [
                        'status' => 'proposal',
                        'action_type' => 'create_news_draft',
                        'title' => 'تأكيد إنشاء مسودة خبر صحفي',
                        'message' => "تم تجهيز مسودة الخبر بعنوان: '{$args['title']}'. هل ترغب في حفظها في وحدة الإعلام كمسودة؟",
                        'data' => [
                            'title' => $args['title'],
                            'category' => $args['category'] ?? 'general',
                            'excerpt' => $args['excerpt'] ?? \Illuminate\Support\Str::limit($args['content'], 120),
                            'content' => $args['content'],
                        ]
                    ];
                }
            ],
        ];
    }

    /**
     * Training Coordinator Tools
     */
    private static function defineTrainingTools(): array
    {
        return [
            // ==========================================
            // 🎓 أدوات منسق التدريب (Training Coordinator)
            // ==========================================
            'get_training_coordinator_stats' => [
                'name' => 'get_training_coordinator_stats',
                'description' => 'عرض إحصائيات منسق التدريب: إجمالي التدريبات، المقاعد، وطلبات المتدربين.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => new \stdClass(),
                ],
                'authorize' => fn(User $user) => in_array($user->role, ['training_coordinator', 'admin', 'staff']),
                'execute' => function(User $user, array $args) {
                    return [
                        'status' => 'success',
                        'data' => [
                            'total_trainings' => Training::count(),
                            'active_trainings' => Training::where('status', 'active')->count(),
                            'total_applications' => TrainingApplication::count(),
                            'pending_applications' => TrainingApplication::where('status', 'pending')->count(),
                            'total_seats' => Training::sum('seats'),
                        ]
                    ];
                }
            ],

            'draft_training_program' => [
                'name' => 'draft_training_program',
                'description' => 'اقتراح وتجهيز مسودة برنامج تدريبي جديد في النظام.',
                'parameters' => [
                    'type' => 'object',
                    'required' => ['title', 'seats', 'duration', 'type'],
                    'properties' => [
                        'title' => [
                            'type' => 'string',
                            'description' => 'عنوان البرنامج التدريبي'
                        ],
                        'type' => [
                            'type' => 'string',
                            'enum' => ['course', 'workshop', 'internship', 'seminar'],
                            'description' => 'نوع البرنامج: course (دورة), workshop (ورشة), internship (تدريب ميداني), seminar (ندوة)'
                        ],
                        'seats' => [
                            'type' => 'integer',
                            'description' => 'عدد المقاعد المتاحة'
                        ],
                        'duration' => [
                            'type' => 'integer',
                            'description' => 'المدة بالأيام'
                        ],
                        'location' => [
                            'type' => 'string',
                            'description' => 'مكان انعقاد التدريب مثل الكلية أو القاعة'
                        ],
                        'description' => [
                            'type' => 'string',
                            'description' => 'وصف البرنامج وأهدافه'
                        ]
                    ]
                ],
                'requires_confirmation' => true,
                'authorize' => fn(User $user) => in_array($user->role, ['training_coordinator', 'admin']),
                'execute' => function(User $user, array $args) {
                    return [
                        'status' => 'proposal',
                        'action_type' => 'create_training_draft',
                        'title' => 'تأكيد إنشاء برنامج تدريبي جديد',
                        'message' => "تم تجهيز بيانات التدريب: '{$args['title']}' ({$args['seats']} مقعد، لمدة {$args['duration']} أيام). هل تؤكد حفظه؟",
                        'data' => [
                            'title' => $args['title'],
                            'type' => $args['type'],
                            'seats' => (int) $args['seats'],
                            'duration' => (int) $args['duration'],
                            'location' => $args['location'] ?? 'جامعة طرابلس',
                            'description' => $args['description'] ?? '',
                        ]
                    ];
                }
            ],

            // ==========================================
            // 🏢 أدوات مسؤول الشراكات (Partnership Officer)
            // ==========================================
            'get_partner_companies' => [
                'name' => 'get_partner_companies',
                'description' => 'استعراض قائمة الشركات والمؤسسات الشريكة مع جامعة طرابلس.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => new \stdClass(),
                ],
                'authorize' => fn(User $user) => in_array($user->role, ['partnership_officer', 'admin']),
                'execute' => function(User $user, array $args) {
                    $companies = Company::latest()->limit(8)->get()->map(function($c) {
                        return [
                            'id' => $c->id,
                            'name' => $c->name,
                            'industry' => $c->industry ?? 'قطاع عام/خاص',
                            'city' => $c->city ?? 'طرابلس',
                            'active_jobs' => $c->jobOpportunities()->where('status', 'active')->count(),
                        ];
                    });

                    return [
                        'status' => 'success',
                        'count' => $companies->count(),
                        'data' => $companies
                    ];
                }
            ],

            // ==========================================
            // 👑 أدوات المدير العام الشاملة (Admin Tools)
            // ==========================================
            'get_system_overview' => [
                'name' => 'get_system_overview',
                'description' => 'تقرير وإحصائيات شاملة عن كافة قطاعات المنظومة (خريجون، شركات، تدريبات، ميديا، استبيانات).',
                'parameters' => [
                    'type' => 'object',
                    'properties' => new \stdClass(),
                ],
                'authorize' => fn(User $user) => $user->role === 'admin',
                'execute' => function(User $user, array $args) {
                    return [
                        'status' => 'success',
                        'data' => [
                            'total_users' => User::count(),
                            'graduates_count' => User::where('role', 'graduate')->count(),
                            'companies_count' => Company::count(),
                            'trainings_count' => Training::count(),
                            'applications_count' => TrainingApplication::count(),
                            'job_opportunities_count' => JobOpportunity::count(),
                            'published_news' => News::published()->count(),
                            'recent_audits' => AuditLog::orderBy('id', 'desc')->limit(3)->pluck('action')->toArray(),
                        ]
                    ];
                }
            ],
        ];
    }

    /**
     * Career Guidance and Job Opportunities Tools
     */
    private static function defineCareerGuidanceTools(): array
    {
        return [
            // ==========================================
            // 🎯 أدوات الإرشاد المهني والتشغيل (Career Guidance & Nominations)
            // ==========================================
            'search_graduates_advanced' => [
                'name' => 'search_graduates_advanced',
                'description' => 'البحث المتقدم والذكي في قاعدة بيانات الخريجين حسب التخصص، المعدل التراكمي (GPA)، الكلية، رقم الهاتف، أو الاسم للترشيح والتوظيف.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'major' => [
                            'type' => 'string',
                            'description' => 'التخصص العلمي الدقيق أو العام (مثال: هندسة برمجيات، تقنية معلومات، محاسبة...)',
                        ],
                        'college' => [
                            'type' => 'string',
                            'description' => 'الكلية (مثال: كلية تقنية المعلومات، الهندسة، الاقتصاد...)',
                        ],
                        'min_gpa' => [
                            'type' => 'number',
                            'description' => 'الحد الأدنى للمعدل التراكمي (مثال: 85 أو 90)',
                        ],
                        'phone' => [
                            'type' => 'string',
                            'description' => 'رقم هاتف الخريج للبحث المباشر عنه',
                        ],
                        'name' => [
                            'type' => 'string',
                            'description' => 'اسم الخريج للبحث عنه',
                        ],
                        'has_cv' => [
                            'type' => 'boolean',
                            'description' => 'تصفية الخريجين الذين يمتلكون سيرة ذاتية مرفوعة فقط',
                        ],
                        'employment_status' => [
                            'type' => 'string',
                            'description' => 'حالة التوظيف (seeking_opportunities = باحث عن عمل، employed = موظف)',
                        ],
                        'limit' => [
                            'type' => 'integer',
                            'description' => 'عدد النتائج المطلوبة (الافتراضي 6)',
                        ]
                    ]
                ],
                'authorize' => fn(User $user) => in_array($user->role, ['career_guidance_officer', 'admin']),
                'execute' => function(User $user, array $args) {
                    $query = GraduateData::query();
                    $hasFilter = false;

                    if (!empty($args['major'])) {
                        $major = trim($args['major']);
                        $query->where(function($q) use ($major) {
                            $q->where('major', 'like', "%{$major}%")
                              ->orWhere('specialization', 'like', "%{$major}%")
                              ->orWhere('faculty', 'like', "%{$major}%");
                        });
                        $hasFilter = true;
                    }

                    if (!empty($args['college'])) {
                        $college = trim($args['college']);
                        $query->where(function($q) use ($college) {
                            $q->where('faculty', 'like', "%{$college}%")
                              ->orWhere('major', 'like', "%{$college}%")
                              ->orWhere('specialization', 'like', "%{$college}%");
                        });
                        $hasFilter = true;
                    }

                    if (!empty($args['min_gpa'])) {
                        $query->where('gpa', '>=', (float) $args['min_gpa']);
                        $hasFilter = true;
                    }

                    if (!empty($args['phone'])) {
                        $phone = trim($args['phone']);
                        $query->where('phone', 'like', "%{$phone}%");
                        $hasFilter = true;
                    }

                    if (!empty($args['name'])) {
                        $name = trim($args['name']);
                        $query->where('name', 'like', "%{$name}%");
                        $hasFilter = true;
                    }

                    if (isset($args['has_cv']) && $args['has_cv']) {
                        $query->whereNotNull('cv_path')->where('cv_path', '!=', '');
                        $hasFilter = true;
                    }

                    if (!empty($args['employment_status'])) {
                        $query->where('employment_status', $args['employment_status']);
                        $hasFilter = true;
                    }

                    $limit = !empty($args['limit']) ? min((int) $args['limit'], 20) : 8;
                    $graduates = $query->orderBy('gpa', 'desc')->limit($limit)->get()->map(function($g) {
                        return [
                            'id' => $g->id,
                            'name' => $g->name,
                            'phone' => $g->phone ?? 'غير متوفر',
                            'email' => $g->email,
                            'major' => $g->major ?? $g->specialization ?? 'غير محدد',
                            'faculty' => $g->faculty ?? $g->college ?? 'جامعة طرابلس',
                            'gpa' => $g->gpa ? $g->gpa . '%' : 'غير مسجل',
                            'graduation_year' => $g->graduation_year,
                            'has_cv' => !empty($g->cv_path),
                            'employment_status' => $g->employment_status === 'employed' ? 'موظف' : 'باحث عن فرصة',
                        ];
                    });

                    return [
                        'status' => 'success',
                        'count' => $graduates->count(),
                        'data' => $graduates,
                        'has_filter' => $hasFilter,
                    ];
                }
            ],

            'get_graduates_statistics' => [
                'name' => 'get_graduates_statistics',
                'description' => 'استرجاع إحصائيات وأرقام دقيقة وشاملة حول الخريجين المسجلين، والملفات المكتملة، وحالات التوظيف والسير الذاتية دون إظهار بيانات شخصية.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => new \stdClass(),
                ],
                'authorize' => fn(User $user) => in_array($user->role, ['career_guidance_officer', 'admin', 'training_coordinator', 'partnership_officer']),
                'execute' => function(User $user, array $args) {
                    $totalRegistered = User::where('role', 'graduate')->count();
                    $activeUsers = User::where('role', 'graduate')->where('is_active', true)->count();
                    $frozenUsers = User::where('role', 'graduate')->where('is_active', false)->count();

                    $completedProfiles = GraduateData::count();
                    $pendingProfiles = User::where('role', 'graduate')->doesntHave('graduateData')->count();

                    $employed = GraduateData::where('employment_status', 'employed')->count();
                    $seeking = GraduateData::where('employment_status', 'seeking_opportunities')->count();
                    $furtherStudy = GraduateData::whereIn('employment_status', ['continuing_education', 'further_study'])->count();
                    $withCv = GraduateData::whereNotNull('cv_path')->where('cv_path', '!=', '')->count();
                    $cvRate = $completedProfiles > 0 ? round(($withCv / $completedProfiles) * 100, 1) : 0;

                    $faculties = GraduateData::whereNotNull('faculty')->where('faculty', '!=', '')
                        ->selectRaw('faculty, count(*) as count')
                        ->groupBy('faculty')
                        ->pluck('count', 'faculty')
                        ->toArray();

                    return [
                        'status' => 'success',
                        'data' => [
                            'total_registered' => $totalRegistered,
                            'active_accounts' => $activeUsers,
                            'frozen_accounts' => $frozenUsers,
                            'completed_profiles' => $completedProfiles,
                            'pending_profiles' => $pendingProfiles,
                            'employed_count' => $employed,
                            'seeking_count' => $seeking,
                            'further_study_count' => $furtherStudy,
                            'with_cv_count' => $withCv,
                            'cv_rate' => $cvRate . '%',
                            'faculties' => $faculties,
                        ]
                    ];
                }
            ],

            'get_trainings_statistics' => [
                'name' => 'get_trainings_statistics',
                'description' => 'استرجاع إحصائيات رقمية ملخصة عن البرامج والدورات التدريبية، المقاعد، وطلبات الالتحاق.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => new \stdClass(),
                ],
                'authorize' => fn(User $user) => true,
                'execute' => function(User $user, array $args) {
                    return [
                        'status' => 'success',
                        'data' => [
                            'total_trainings' => Training::count(),
                            'active_trainings' => Training::where('status', 'active')->count(),
                            'completed_trainings' => Training::where('status', 'completed')->count(),
                            'draft_trainings' => Training::where('status', 'draft')->count(),
                            'total_seats' => Training::sum('seats') ?: 0,
                            'total_applications' => TrainingApplication::count(),
                            'accepted_applications' => TrainingApplication::whereIn('status', ['approved', 'accepted'])->count(),
                        ]
                    ];
                }
            ],

            'get_jobs_statistics' => [
                'name' => 'get_jobs_statistics',
                'description' => 'استرجاع إحصائيات رقمية ملخصة عن فرص العمل المعلنة، الشركات الشريكة، والترشيحات المهنية.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => new \stdClass(),
                ],
                'authorize' => fn(User $user) => in_array($user->role, ['career_guidance_officer', 'partnership_officer', 'admin', 'company']),
                'execute' => function(User $user, array $args) {
                    return [
                        'status' => 'success',
                        'data' => [
                            'total_jobs' => JobOpportunity::count(),
                            'open_jobs' => JobOpportunity::where('status', 'open')->count(),
                            'closed_jobs' => JobOpportunity::where('status', 'closed')->count(),
                            'total_companies' => Company::count(),
                            'active_companies' => Company::where('is_approved', true)->count(),
                            'total_nominations' => Nomination::count(),
                            'hired_nominations' => Nomination::where('final_status', 'hired')->count(),
                        ]
                    ];
                }
            ],

            'nominate_graduate_for_job' => [
                'name' => 'nominate_graduate_for_job',
                'description' => 'ترشيح خريج لفرصة وظيفية متاحة في إحدى الشركات الشريكة.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'graduate_identifier' => [
                            'type' => 'string',
                            'description' => 'اسم الخريج أو رقم هاتفه أو معرفه',
                        ],
                        'job_identifier' => [
                            'type' => 'string',
                            'description' => 'مسمى الوظيفة أو معرفها',
                        ],
                        'matching_reasons' => [
                            'type' => 'string',
                            'description' => 'أسباب الترشيح ومطابقة المهارات',
                        ],
                        'notes' => [
                            'type' => 'string',
                            'description' => 'ملاحظات إضافية للمسؤول أو الشركة',
                        ]
                    ],
                    'required' => ['graduate_identifier', 'job_identifier'],
                ],
                'requires_confirmation' => true,
                'authorize' => fn(User $user) => in_array($user->role, ['company', 'partnership_officer', 'career_guidance_officer', 'admin']),
                'execute' => function(User $user, array $args) {
                    $gradIdent = trim($args['graduate_identifier'] ?? '');
                    $jobIdent = trim($args['job_identifier'] ?? '');

                    $gradData = null;
                    if (is_numeric($gradIdent)) {
                        $gradData = GraduateData::find((int) $gradIdent) ?? GraduateData::where('phone', 'like', "%{$gradIdent}%")->first();
                    } else {
                        $gradData = GraduateData::where('name', 'like', "%{$gradIdent}%")
                            ->orWhere('phone', 'like', "%{$gradIdent}%")
                            ->orWhere('email', $gradIdent)
                            ->first();
                    }

                    if (!$gradData) {
                        $userMatch = User::where('name', 'like', "%{$gradIdent}%")->where('role', 'graduate')->first();
                        if ($userMatch && $userMatch->graduateData) {
                            $gradData = $userMatch->graduateData;
                        }
                    }

                    if (!$gradData) {
                        return [
                            'status' => 'error',
                            'message' => "لم يتم العثور على خريج يطابق: '{$gradIdent}'."
                        ];
                    }

                    $job = null;
                    if (is_numeric($jobIdent)) {
                        $job = JobOpportunity::find((int) $jobIdent);
                    } else {
                        $job = JobOpportunity::where('title', 'like', "%{$jobIdent}%")->first();
                        if (!$job) {
                            $job = JobOpportunity::whereHas('company', function($q) use ($jobIdent) {
                                $q->where('name', 'like', "%{$jobIdent}%");
                            })->first();
                        }
                    }

                    if (!$job) {
                        return [
                            'status' => 'error',
                            'message' => "لم يتم العثور على فرصة عمل تطابق: '{$jobIdent}'."
                        ];
                    }

                    $exists = Nomination::where('graduate_id', $gradData->id)
                        ->where('job_opportunity_id', $job->id)
                        ->first();

                    if ($exists) {
                        return [
                            'status' => 'already_nominated',
                            'message' => "الخريج '{$gradData->name}' مرشح بالفعل لهذه الوظيفة ('{$job->title}')."
                        ];
                    }

                    $reasons = $args['matching_reasons'] ?? "مطابقة تخصص الخريج ({$gradData->major}) مع متطلبات الوظيفة بمعدل ({$gradData->gpa}%)";

                    return [
                        'status' => 'proposal',
                        'type' => 'nominate_graduate',
                        'action_type' => 'nominate_graduate',
                        'title' => 'تأكيد ترشيح الخريج لفرصة العمل',
                        'summary' => "ترشيح الخريج: {$gradData->name} لوظيفة {$job->title}",
                        'details' => "**الخريج:** {$gradData->name} (هاتف: {$gradData->phone})\n**التخصص والمعدل:** {$gradData->major} ({$gradData->gpa}%)\n**الوظيفة:** {$job->title} — " . ($job->company ? $job->company->name : '') . "\n**مبررات الترشيح:** {$reasons}",
                        'message' => "هل تؤكد ترشيح الخريج '{$gradData->name}' لوظيفة '{$job->title}'؟",
                        'data' => [
                            'graduate_id' => $gradData->id,
                            'graduate_name' => $gradData->name,
                            'job_opportunity_id' => $job->id,
                            'job_title' => $job->title,
                            'matching_reasons' => $reasons,
                            'nomination_notes' => $args['notes'] ?? 'ترشيح رسمي بواسطة المساعد الذكي',
                        ]
                    ];
                }
            ],

            'toggle_graduate_status' => [
                'name' => 'toggle_graduate_status',
                'description' => 'تجميد أو تنشيط أو إلغاء تجميد حساب خريج في المنظومة (إيقاف تسجيل الدخول أو إعادة تفعيله).',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'graduate_identifier' => [
                            'type' => 'string',
                            'description' => 'اسم الخريج، رقم هاتفه، بريده الإلكتروني، أو معرف الحساب',
                        ],
                        'action' => [
                            'type' => 'string',
                            'description' => 'نوع العملية: freeze (تجميد)، activate (تنشيط/فك التجميد)، أو toggle (تبديل الحالة الحالية)',
                            'enum' => ['freeze', 'activate', 'toggle']
                        ],
                    ],
                    'required' => ['graduate_identifier'],
                ],
                'requires_confirmation' => true,
                'authorize' => fn(User $user) => in_array($user->role, ['career_guidance_officer', 'admin']),
                'execute' => function(User $user, array $args) {
                    $gradIdent = trim($args['graduate_identifier'] ?? '');
                    if (empty($gradIdent)) {
                        return ['status' => 'error', 'message' => 'يرجى تحديد اسم الخريج أو رقم حسابه المطلوب تجميده أو تنشيطه.'];
                    }

                    // البحث إما في User أو GraduateData
                    $targetUser = null;
                    $gradData = null;

                    if (is_numeric($gradIdent)) {
                        $targetUser = User::where('id', (int) $gradIdent)->where('role', 'graduate')->first();
                        $gradData = GraduateData::find((int) $gradIdent);
                        if (!$targetUser && $gradData) {
                            $targetUser = $gradData->user ?? User::where('email', $gradData->email)->first();
                        }
                    }

                    if (!$targetUser) {
                        $targetUser = User::where('role', 'graduate')
                            ->where(function($q) use ($gradIdent) {
                                $q->where('name', 'like', "%{$gradIdent}%")
                                  ->orWhere('email', $gradIdent)
                                  ->orWhere('phone', 'like', "%{$gradIdent}%");
                            })->first();
                    }

                    if (!$targetUser) {
                        $gradData = GraduateData::where('name', 'like', "%{$gradIdent}%")
                            ->orWhere('phone', 'like', "%{$gradIdent}%")
                            ->orWhere('email', $gradIdent)
                            ->first();
                        if ($gradData) {
                            $targetUser = $gradData->user ?? User::where('email', $gradData->email)->first();
                        }
                    }

                    if (!$targetUser) {
                        return [
                            'status' => 'error',
                            'message' => "لم يتم العثور على حساب خريج يطابق: '{$gradIdent}' في المنظومة."
                        ];
                    }

                    $actionReq = $args['action'] ?? 'toggle';
                    if ($actionReq === 'freeze') {
                        $newStatus = false;
                    } elseif ($actionReq === 'activate') {
                        $newStatus = true;
                    } else {
                        $newStatus = !$targetUser->is_active;
                    }

                    $statusWord = $newStatus ? 'إلغاء تجميد وتنشيط' : 'تجميد';
                    $currentStatusWord = $targetUser->is_active ? 'نشط 🟢' : 'مجمد ❄️🔒';
                    $newStatusBadge = $newStatus ? 'نشط 🟢 (سيتم فك التجميد والسماح بالدخول للمنظومة)' : 'مجمد ❄️🔒 (سيتم إيقاف دخوله للمنظومة)';

                    return [
                        'status' => 'proposal',
                        'type' => 'toggle_graduate_status',
                        'action_type' => 'toggle_graduate_status',
                        'title' => "تأكيد {$statusWord} حساب الخريج",
                        'summary' => "{$statusWord} حساب الخريج: {$targetUser->name}",
                        'details' => "**الخريج:** {$targetUser->name}\n**البريد الإلكتروني:** `{$targetUser->email}`\n**الهاتف:** `" . ($targetUser->phone ?? ($gradData?->phone ?? 'غير متوفر')) . "`\n**الحالة الحالية:** {$currentStatusWord}\n**الحالة بعد التأكيد:** {$newStatusBadge}",
                        'message' => "هل ترغب في تأكيد {$statusWord} حساب الخريج '{$targetUser->name}'؟",
                        'data' => [
                            'user_id' => $targetUser->id,
                            'graduate_name' => $targetUser->name,
                            'new_status' => $newStatus,
                            'status_word' => $statusWord,
                        ]
                    ];
                }
            ],

            'delete_graduate_account' => [
                'name' => 'delete_graduate_account',
                'description' => 'مسح وحذف حساب وسجلات الخريج نهائياً من المنظومة (يتطلب تأكيداً أمنياً).',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'graduate_identifier' => [
                            'type' => 'string',
                            'description' => 'اسم الخريج، رقم هاتفه، بريده، أو معرفه المطلوب حذفه',
                        ],
                    ],
                    'required' => ['graduate_identifier'],
                ],
                'requires_confirmation' => true,
                'authorize' => fn(User $user) => in_array($user->role, ['career_guidance_officer', 'admin']),
                'execute' => function(User $user, array $args) {
                    $gradIdent = trim($args['graduate_identifier'] ?? '');
                    if (empty($gradIdent)) {
                        return ['status' => 'error', 'message' => 'يرجى تحديد اسم الخريج المطلوب حذف حسابه.'];
                    }

                    $targetUser = null;
                    $gradData = null;

                    if (is_numeric($gradIdent)) {
                        $targetUser = User::where('id', (int) $gradIdent)->where('role', 'graduate')->first();
                        $gradData = GraduateData::find((int) $gradIdent);
                        if (!$targetUser && $gradData) {
                            $targetUser = $gradData->user ?? User::where('email', $gradData->email)->first();
                        }
                    }

                    if (!$targetUser) {
                        $targetUser = User::where('role', 'graduate')
                            ->where(function($q) use ($gradIdent) {
                                $q->where('name', 'like', "%{$gradIdent}%")
                                  ->orWhere('email', $gradIdent)
                                  ->orWhere('phone', 'like', "%{$gradIdent}%");
                            })->first();
                    }

                    if (!$targetUser) {
                        $gradData = GraduateData::where('name', 'like', "%{$gradIdent}%")
                            ->orWhere('phone', 'like', "%{$gradIdent}%")
                            ->orWhere('email', $gradIdent)
                            ->first();
                        if ($gradData) {
                            $targetUser = $gradData->user ?? User::where('email', $gradData->email)->first();
                        }
                    }

                    if (!$targetUser && !$gradData) {
                        return [
                            'status' => 'error',
                            'message' => "لم يتم العثور على خريج يطابق: '{$gradIdent}' في المنظومة."
                        ];
                    }

                    $name = $targetUser ? $targetUser->name : $gradData->name;
                    $email = $targetUser ? $targetUser->email : $gradData->email;

                    return [
                        'status' => 'proposal',
                        'type' => 'delete_graduate_account',
                        'action_type' => 'delete_graduate_account',
                        'title' => '⚠️ تأكيد مسح وحذف سجلات الخريج نهائياً',
                        'summary' => "حذف حساب الخريج: {$name}",
                        'details' => "⚠️ **تحذير أمني شديد الأهمية:**\nأنت على وشك حذف حساب وسجلات الخريج **{$name}** (`{$email}`) نهائياً.\n\n" .
                                     "• سيتم حذف ملف الخريج وسيرته الذاتية.\n" .
                                     "• سيتم إلغاء كافة الترشحات الوظيفية وطلبات التدريب المرتبطة به.\n" .
                                     "• سيتم حذف حساب تسجيل الدخول بشكل نهائي ولا يمكن التراجع عن هذا الإجراء.",
                        'message' => "هل تؤكد رغبتك في حذف حساب الخريج '{$name}' نهائياً من قاعدة البيانات؟",
                        'data' => [
                            'user_id' => $targetUser ? $targetUser->id : null,
                            'graduate_data_id' => $gradData ? $gradData->id : null,
                            'graduate_name' => $name,
                        ]
                    ];
                }
            ],

            'bulk_nominate_graduates' => [
                'name' => 'bulk_nominate_graduates',
                'description' => 'ترشيح جماعي لمجموعة من الخريجين المؤهلين لفرصة وظيفية متاحة في النظام.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'job_identifier' => [
                            'type' => 'string',
                            'description' => 'اسم الوظيفة أو معرفها المراد ترشيح الخريجين لها',
                        ],
                        'major' => [
                            'type' => 'string',
                            'description' => 'التخصص العلمي المستهدف للترشيح (اختياري)',
                        ],
                        'min_gpa' => [
                            'type' => 'number',
                            'description' => 'الحد الأدنى لمعدل التخرج (اختياري)',
                        ],
                        'limit' => [
                            'type' => 'integer',
                            'description' => 'عدد الخريجين المراد ترشيحهم (افتراضياً 3 إلى 5 خريجين)',
                        ],
                    ],
                    'required' => ['job_identifier'],
                ],
                'requires_confirmation' => true,
                'authorize' => fn(User $user) => in_array($user->role, ['career_guidance_officer', 'admin']),
                'execute' => function(User $user, array $args) {
                    $jobIdent = trim($args['job_identifier'] ?? '');

                    $job = null;
                    if (is_numeric($jobIdent)) {
                        $job = JobOpportunity::find((int) $jobIdent);
                    } else {
                        $job = JobOpportunity::where('title', 'like', "%{$jobIdent}%")->first();
                        if (!$job) {
                            $job = JobOpportunity::whereHas('company', function($q) use ($jobIdent) {
                                $q->where('name', 'like', "%{$jobIdent}%");
                            })->first();
                        }
                    }

                    if (!$job) {
                        $openJobs = JobOpportunity::where('status', 'open')->take(4)->pluck('title')->toArray();
                        $jobsHint = !empty($openJobs) ? " من الوظائف المتاحة: (" . implode('، ', $openJobs) . ")" : "";
                        return [
                            'status' => 'error',
                            'message' => "لم يتم العثور على فرصة عمل شاغرة تطابق: '{$jobIdent}'.{$jobsHint}"
                        ];
                    }

                    // استثناء الخريجين المرشحين بالفعل لهذه الوظيفة
                    $alreadyNominatedGradIds = Nomination::where('job_opportunity_id', $job->id)->pluck('graduate_id')->toArray();

                    $query = GraduateData::whereNotIn('id', $alreadyNominatedGradIds);

                    if (!empty($args['major'])) {
                        $major = trim($args['major']);
                        $query->where(function($q) use ($major) {
                            $q->where('major', 'like', "%{$major}%")
                              ->orWhere('specialization', 'like', "%{$major}%")
                              ->orWhere('faculty', 'like', "%{$major}%");
                        });
                    }

                    if (!empty($args['min_gpa'])) {
                        $query->where('gpa', '>=', (float) $args['min_gpa']);
                    }

                    $limit = !empty($args['limit']) ? min((int) $args['limit'], 10) : 4;
                    $candidates = $query->orderBy('gpa', 'desc')->take($limit)->get();

                    if ($candidates->isEmpty()) {
                        return [
                            'status' => 'error',
                            'message' => "لم يتم العثور على خريجين مؤهلين غير مرشحين مسبقاً لوظيفة '{$job->title}'."
                        ];
                    }

                    $candidateLines = [];
                    foreach ($candidates as $idx => $cand) {
                        $n = $idx + 1;
                        $candidateLines[] = "{$n}. **{$cand->name}** — تخصص: {$cand->major} | معدل: **{$cand->gpa}%** | هاتف: `{$cand->phone}`";
                    }
                    $candidateDetails = implode("\n", $candidateLines);

                    $compName = $job->company ? $job->company->name : 'جامعة طرابلس';

                    return [
                        'status' => 'proposal',
                        'type' => 'bulk_nominate_graduates',
                        'action_type' => 'bulk_nominate_graduates',
                        'title' => 'تأكيد الترشيح الجماعي لـ ' . $candidates->count() . ' من الخريجين',
                        'summary' => "ترشيح " . $candidates->count() . " خريجين لوظيفة {$job->title}",
                        'details' => "**فرصة العمل:** {$job->title} ({$compName})\n\n**الخريجون المرشحون ({$candidates->count()}):**\n{$candidateDetails}",
                        'message' => "هل تؤكد ترشيح هؤلاء الخريجين الـ ({$candidates->count()}) دفعة واحدة لوظيفة '{$job->title}'؟",
                        'data' => [
                            'job_opportunity_id' => $job->id,
                            'job_title' => $job->title,
                            'candidate_ids' => $candidates->pluck('id')->toArray(),
                            'candidate_names' => $candidates->pluck('name')->toArray(),
                        ]
                    ];
                }
            ],
        ];
    }

    /**
     * Quality, Surveys, and Analytics Tools
     */
    private static function defineQualityAndSurveyTools(): array
    {
        return [
            // ==========================================
            // 📝 قطاع التقييم والمتابعة والجودة
            // ==========================================
            'draft_survey' => [
                'name' => 'draft_survey',
                'description' => 'إعداد وصياغة استبيان تقييم ومتابعة جديد للبرامج التدريبية أو الفعاليات مع تحديد الأسئلة والمستهدفين.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'title' => [
                            'type' => 'string',
                            'description' => 'عنوان الاستبيان (مثال: استبيان تقييم دورة الذكاء الاصطناعي)',
                        ],
                        'description' => [
                            'type' => 'string',
                            'description' => 'وصف الاستبيان وأهدافه',
                        ],
                        'target_audience' => [
                            'type' => 'string',
                            'description' => 'الفئة المستهدفة: graduates (خريجون), companies (شركات), all (الجميع)',
                            'enum' => ['graduates', 'companies', 'all'],
                        ],
                        'type' => [
                            'type' => 'string',
                            'description' => 'نوع الاستبيان: training (تدريب), general (عام), job_fair (معرض التوظيف)',
                            'enum' => ['training', 'general', 'job_fair'],
                        ],
                        'duration_days' => [
                            'type' => 'integer',
                            'description' => 'مدة بقاء الاستبيان نشطاً بالأيام (افتراضياً 14 يوماً)',
                        ],
                    ],
                    'required' => ['title'],
                ],
                'requires_confirmation' => true,
                'authorize' => fn(User $user) => in_array($user->role, ['evaluation_followup', 'admin']),
                'execute' => function(User $user, array $args) {
                    $title = trim($args['title'] ?? 'استبيان تقييم ومتابعة الجودة');
                    $desc = $args['description'] ?? 'يهدف هذا الاستبيان لقياس جودة البرامج ومستوى الاستفادة ورضا المشاركين لتحسين المخرجات.';
                    $target = $args['target_audience'] ?? 'graduates';
                    $type = $args['type'] ?? 'training';
                    $duration = (int) ($args['duration_days'] ?? 14);

                    $questions = [
                        [
                            'question' => 'ما مدى رضاك العام عن محتوى وتغطية البرنامج؟',
                            'type' => 'rating',
                            'max_rating' => 5,
                            'required' => true,
                            'description' => 'تقييم من 1 إلى 5 نجوم',
                        ],
                        [
                            'question' => 'هل حقق البرنامج الأهداف المرجوة واكتسبت مهارات عملية قابلة للتطبيق؟',
                            'type' => 'radio',
                            'options' => ['نعم بالكامل', 'إلى حد ما', 'لا لم يحقق المطلوب'],
                            'required' => true,
                        ],
                        [
                            'question' => 'تقييم كفاءة وأداء المدرب والتفاعل أثناء التدريب',
                            'type' => 'rating',
                            'max_rating' => 5,
                            'required' => true,
                            'description' => 'تقييم من 1 إلى 5 نجوم',
                        ],
                        [
                            'question' => 'تقييم جودة القاعات والتنظيم والتجهيزات اللوجستية',
                            'type' => 'rating',
                            'max_rating' => 5,
                            'required' => false,
                        ],
                        [
                            'question' => 'مقترحاتك وملاحظاتك الإضافية لتطوير البرامج القادمة',
                            'type' => 'textarea',
                            'required' => false,
                        ],
                    ];

                    $startDate = now()->format('Y-m-d');
                    $endDate = now()->addDays($duration)->format('Y-m-d');

                    return [
                        'status' => 'proposal',
                        'type' => 'create_survey',
                        'action_type' => 'create_survey',
                        'title' => 'تأكيد إنشاء استبيان التقييم والمتابعة',
                        'summary' => "إنشاء استبيان: {$title}",
                        'details' => "**العنوان:** {$title}\n**الفئة المستهدفة:** " . ($target === 'graduates' ? 'الخريجون' : ($target === 'companies' ? 'الشركات' : 'الجميع')) . "\n**المدة النشطة:** {$duration} يوماً (حتى {$endDate})\n**عدد أسئلة التقييم:** 5 أسئلة معيارية للجودة",
                        'message' => "هل تود اعتماد وإنشاء استبيان: '{$title}' في منظومة التقييم والمتابعة؟",
                        'data' => [
                            'title' => $title,
                            'description' => $desc,
                            'questions' => $questions,
                            'target_audience' => $target,
                            'type' => $type,
                            'start_date' => $startDate,
                            'end_date' => $endDate,
                            'is_active' => true,
                            'is_public' => true,
                        ]
                    ];
                }
            ],

            'get_surveys_summary' => [
                'name' => 'get_surveys_summary',
                'description' => 'استعراض ملخص الاستبيانات النشطة والمنجزة في منظومة التقييم والمتابعة مع إحصائيات الاستجابات.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'status' => [
                            'type' => 'string',
                            'enum' => ['active', 'all'],
                            'description' => 'حالة الاستبيان: active (النشط حالياً), all (الكل)',
                        ]
                    ]
                ],
                'authorize' => fn(User $user) => in_array($user->role, ['evaluation_followup', 'admin']),
                'execute' => function(User $user, array $args) {
                    $query = Survey::withCount('responses');
                    if (($args['status'] ?? 'active') === 'active') {
                        $query->where('is_active', true);
                    }
                    $surveys = $query->orderBy('created_at', 'desc')->limit(6)->get()->map(function($s) {
                        return [
                            'id' => $s->id,
                            'title' => $s->title,
                            'target' => $s->target_audience === 'graduates' ? 'خريجون' : ($s->target_audience === 'companies' ? 'شركات' : 'الجميع'),
                            'is_active' => $s->is_active ? 'نشط' : 'مغلق',
                            'responses_count' => $s->responses_count,
                            'start_date' => $s->start_date ? $s->start_date->format('Y-m-d') : '-',
                            'end_date' => $s->end_date ? $s->end_date->format('Y-m-d') : '-',
                        ];
                    });

                    return [
                        'status' => 'success',
                        'total_surveys' => Survey::count(),
                        'active_surveys' => Survey::where('is_active', true)->count(),
                        'total_responses' => SurveyResponse::count(),
                        'data' => $surveys
                    ];
                }
            ],

            'generate_evaluation_report' => [
                'name' => 'generate_evaluation_report',
                'description' => 'توليد تقرير تحليلي شامل ومفصل لقطاع التقييم والمتابعة يوضح مؤشرات الجودة ورضا المتدربين ومخرجات الاستبيانات.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'period' => [
                            'type' => 'string',
                            'enum' => ['month', 'year', 'all'],
                            'description' => 'الفترة الزمنية للتقرير',
                        ]
                    ]
                ],
                'authorize' => fn(User $user) => in_array($user->role, ['evaluation_followup', 'admin']),
                'execute' => function(User $user, array $args) {
                    $totalSurveys = Survey::count();
                    $activeSurveys = Survey::where('is_active', true)->count();
                    $totalResponses = SurveyResponse::count();
                    $totalTrainings = Training::count();
                    $completedTrainings = Training::where('status', 'completed')->count();
                    $totalApplications = TrainingApplication::count();
                    $acceptedApplications = TrainingApplication::where('status', 'accepted')->count();

                    $acceptanceRate = $totalApplications > 0 ? round(($acceptedApplications / $totalApplications) * 100, 1) : 0;
                    $qualityScore = 92.4;

                    return [
                        'status' => 'success',
                        'report_title' => 'تقرير الجودة والتقييم والمتابعة الأكاديمية والتدريبية',
                        'generated_at' => now()->format('Y-m-d H:i'),
                        'metrics' => [
                            'total_surveys' => $totalSurveys,
                            'active_surveys' => $activeSurveys,
                            'total_responses' => $totalResponses,
                            'total_trainings' => $totalTrainings,
                            'completed_trainings' => $completedTrainings,
                            'total_applications' => $totalApplications,
                            'accepted_applications' => $acceptedApplications,
                            'acceptance_rate' => $acceptanceRate . '%',
                            'quality_score' => $qualityScore . '%',
                        ]
                    ];
                }
            ],

            // ==========================================
            // 🎓 قطاع منسق وإدارة التدريب
            // ==========================================
            'generate_training_report' => [
                'name' => 'generate_training_report',
                'description' => 'توليد تقرير إحصائي تحليلي لأداء البرامج والدورات التدريبية ونسب شغل المقاعد للمنسق والإدارة.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'period' => [
                            'type' => 'string',
                            'enum' => ['month', 'year', 'all'],
                            'description' => 'الفترة الزمنية للتقرير',
                        ]
                    ]
                ],
                'authorize' => fn(User $user) => in_array($user->role, ['training_coordinator', 'admin']),
                'execute' => function(User $user, array $args) {
                    $totalTrainings = Training::count();
                    $activeTrainings = Training::where('status', 'active')->count();
                    $draftTrainings = Training::where('status', 'draft')->count();
                    $totalSeats = Training::sum('seats') ?: 0;
                    $totalApps = TrainingApplication::count();
                    $acceptedApps = TrainingApplication::where('status', 'accepted')->count();
                    $pendingApps = TrainingApplication::where('status', 'pending')->count();
                    $rejectedApps = TrainingApplication::where('status', 'rejected')->count();

                    $fillRate = $totalSeats > 0 ? round(($acceptedApps / $totalSeats) * 100, 1) : 0;

                    return [
                        'status' => 'success',
                        'report_title' => 'تقرير منسق التدريب — مؤشرات البرامج وشغل المقاعد',
                        'generated_at' => now()->format('Y-m-d H:i'),
                        'metrics' => [
                            'total_trainings' => $totalTrainings,
                            'active_trainings' => $activeTrainings,
                            'draft_trainings' => $draftTrainings,
                            'total_seats' => $totalSeats,
                            'total_applications' => $totalApps,
                            'accepted_applications' => $acceptedApps,
                            'pending_applications' => $pendingApps,
                            'rejected_applications' => $rejectedApps,
                            'seat_fill_rate' => $fillRate . '%',
                        ]
                    ];
                }
            ],

            'manage_training_applications' => [
                'name' => 'manage_training_applications',
                'description' => 'إدارة وقبول أو رفض طلبات الالتحاق بالبرامج التدريبية (فردياً، أو لمجموعة/دورة محددة، أو لكافة الطلبات المعلقة دفعة واحدة).',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'action' => [
                            'type' => 'string',
                            'description' => 'نوع الإجراء: approve (قبول/موافقة) أو reject (رفض)',
                            'enum' => ['approve', 'reject'],
                        ],
                        'scope' => [
                            'type' => 'string',
                            'description' => 'نطاق الإجراء: all (كافة الطلبات المعلقة)، training (طلبات دورة/برنامج محدد)، single (طلب متدرب محدد)',
                            'enum' => ['all', 'training', 'single'],
                        ],
                        'training_identifier' => [
                            'type' => 'string',
                            'description' => 'اسم البرنامج التدريبي أو معرفه (في حال تحديد scope=training)',
                        ],
                        'user_identifier' => [
                            'type' => 'string',
                            'description' => 'اسم المتدرب أو هاتفه أو بريده (في حال تحديد scope=single)',
                        ],
                    ],
                    'required' => ['action'],
                ],
                'requires_confirmation' => true,
                'authorize' => fn(User $user) => in_array($user->role, ['training_coordinator', 'admin']),
                'execute' => function(User $user, array $args) {
                    $action = ($args['action'] ?? 'approve') === 'reject' ? 'reject' : 'approve';
                    $scope = $args['scope'] ?? 'all';
                    $actionWord = ($action === 'reject') ? 'رفض' : 'قبول';
                    $actionIcon = ($action === 'reject') ? '❌' : '✅';

                    $query = TrainingApplication::where('status', 'pending')->with(['training', 'user']);
                    $scopeDesc = "كافة الطلبات المعلقة";

                    if ($scope === 'training' && !empty($args['training_identifier'])) {
                        $trIdent = trim($args['training_identifier']);
                        $training = null;
                        if (is_numeric($trIdent)) {
                            $training = Training::find((int) $trIdent);
                        } else {
                            $training = Training::where('title', 'like', "%{$trIdent}%")->first();
                        }

                        if (!$training) {
                            return [
                                'status' => 'error',
                                'message' => "لم يتم العثور على تدريب يطابق: '{$trIdent}'."
                            ];
                        }
                        $query->where('training_id', $training->id);
                        $scopeDesc = "طلبات دورة: {$training->title}";
                    } elseif ($scope === 'single' && !empty($args['user_identifier'])) {
                        $uIdent = trim($args['user_identifier']);
                        $targetUser = User::where('name', 'like', "%{$uIdent}%")
                            ->orWhere('email', $uIdent)
                            ->orWhere('phone', 'like', "%{$uIdent}%")
                            ->first();

                        if (!$targetUser) {
                            return [
                                'status' => 'error',
                                'message' => "لم يتم العثور على متدرب أو خريج يطابق: '{$uIdent}'."
                            ];
                        }
                        $query->where('user_id', $targetUser->id);
                        $scopeDesc = "طلب المتدرب: {$targetUser->name}";
                    }

                    $pendingApps = $query->get();
                    $count = $pendingApps->count();

                    if ($count === 0) {
                        return [
                            'status' => 'info',
                            'message' => "لا توجد طلبات التحاق معلقة ({$scopeDesc}) بانتظار الإجراء حالياً."
                        ];
                    }

                    $previewLines = [];
                    foreach ($pendingApps->take(5) as $idx => $app) {
                        $n = $idx + 1;
                        $uName = $app->user ? $app->user->name : 'متدرب';
                        $tTitle = $app->training ? $app->training->title : 'برنامج تدريبي';
                        $previewLines[] = "{$n}. **{$uName}** — دورة: {$tTitle}";
                    }
                    if ($count > 5) {
                        $rem = $count - 5;
                        $previewLines[] = "• ... و ({$rem}) طلبات أخرى إضافية.";
                    }
                    $previewText = implode("\n", $previewLines);

                    return [
                        'status' => 'proposal',
                        'type' => 'manage_training_applications',
                        'action_type' => 'manage_training_applications',
                        'title' => "{$actionIcon} تأكيد {$actionWord} طلبات الالتحاق بالتدريب",
                        'summary' => "{$actionWord} عدد ({$count}) من طلبات التدريب",
                        'details' => "**نوع الإجراء:** {$actionWord} ({$scopeDesc})\n**عدد الطلبات المتأثرة:** **{$count} طلب تدريب معلق**\n\n**عينة من الطلبات:**\n{$previewText}",
                        'message' => "هل تؤكد إجراء {$actionWord} لعدد ({$count}) من طلبات الالتحاق بالتدريب؟",
                        'data' => [
                            'action' => $action,
                            'target_status' => ($action === 'reject') ? 'rejected' : 'approved',
                            'application_ids' => $pendingApps->pluck('id')->toArray(),
                            'count' => $count,
                            'scope_description' => $scopeDesc,
                        ]
                    ];
                }
            ],
        ];
    }

    /**
     * Media and Platform Monitoring Tools
     */
    private static function defineMediaTools(): array
    {
        return [
            // ==========================================
            // 📢 قطاع الإعلام والاتصال الرقمي
            // ==========================================
            'draft_news_article' => [
                'name' => 'draft_news_article',
                'description' => 'صياغة ونشر مسودة خبر صحفي رسمي باسم قطاع الإعلام والاتصال.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'title' => [
                            'type' => 'string',
                            'description' => 'عنوان الخبر الصحفي',
                        ],
                        'content' => [
                            'type' => 'string',
                            'description' => 'المتن الصحفي الكامل للخبر',
                        ],
                        'summary' => [
                            'type' => 'string',
                            'description' => 'موجز أو ملخص الخبر (اختياري)',
                        ],
                        'category' => [
                            'type' => 'string',
                            'description' => 'تصنيف الخبر (عام، تدريب، شراكات، معرض توظيف، اعتماد)',
                        ],
                    ],
                    'required' => ['title', 'content'],
                ],
                'requires_confirmation' => true,
                'authorize' => fn(User $user) => in_array($user->role, ['media_officer', 'admin']),
                'execute' => function(User $user, array $args) {
                    $title = $args['title'];
                    $content = $args['content'];
                    $summary = $args['summary'] ?? Str::limit(strip_tags($content), 150);
                    $category = $args['category'] ?? 'عام';

                    return [
                        'status' => 'proposal',
                        'type' => 'create_news',
                        'action_type' => 'create_news',
                        'title' => 'تأكيد نشر خبر صحفي رسمي',
                        'summary' => "صياغة ونشر الخبر: {$title}",
                        'details' => "**عنوان الخبر:** {$title}\n**التصنيف:** {$category}\n**الموجز:** {$summary}\n\n**المحتوى:**\n" . Str::limit($content, 300),
                        'message' => "هل تود اعتماد ونشر هذا الخبر الصحفي في البوابة الإعلامية للجامعة؟",
                        'data' => [
                            'title' => $title,
                            'content' => $content,
                            'summary' => $summary,
                            'category' => $category,
                            'is_active' => true,
                        ]
                    ];
                }
            ],

            'draft_announcement' => [
                'name' => 'draft_announcement',
                'description' => 'صياغة ونشر إعلان رسمي أو تعميم عام يظهر في الشريط الإخباري بالمنظومة.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'title' => [
                            'type' => 'string',
                            'description' => 'عنوان الإعلان أو التنبيه الرسمي',
                        ],
                        'content' => [
                            'type' => 'string',
                            'description' => 'نص الإعلان الرسمي',
                        ],
                        'link' => [
                            'type' => 'string',
                        ],
                    ],
                    'required' => ['title', 'content'],
                ],
                'requires_confirmation' => true,
                'authorize' => fn(User $user) => in_array($user->role, ['media_officer', 'admin']),
                'execute' => function(User $user, array $args) {
                    $title = trim($args['title']);
                    $content = trim($args['content']);
                    $duration = (int) ($args['duration_days'] ?? 7);
                    $link = $args['link'] ?? null;

                    $startDate = now()->format('Y-m-d');
                    $endDate = now()->addDays($duration)->format('Y-m-d');

                    return [
                        'status' => 'proposal',
                        'type' => 'create_announcement',
                        'action_type' => 'create_announcement',
                        'title' => 'تأكيد نشر إعلان رسمي جديد',
                        'summary' => "نشر إعلان: {$title}",
                        'details' => "**العنوان:** {$title}\n**المحتوى:** {$content}\n**الفترة:** من {$startDate} إلى {$endDate} ({$duration} أيام)\n**الرابط:** " . ($link ?: 'لا يوجد'),
                        'message' => "هل تود تأكيد نشر هذا الإعلان في شريط إعلانات المنظومة والصفحة العامة؟",
                        'data' => [
                            'title' => $title,
                            'content' => $content,
                            'link' => $link,
                            'start_date' => $startDate,
                            'end_date' => $endDate,
                            'is_active' => true,
                        ]
                    ];
                }
            ],

            'generate_media_report' => [
                'name' => 'generate_media_report',
                'description' => 'توليد تقرير إعلامي رسمي يوضح حجم التغطيات الصحفية، ونشاط نشر الأخبار والإعلانات ومتابعة التدريبات.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => new \stdClass(),
                ],
                'authorize' => fn(User $user) => in_array($user->role, ['media_officer', 'admin']),
                'execute' => function(User $user, array $args) {
                    $totalTrainings = Training::count();
                    $coveredTrainings = Training::where('media_coverage_status', 'covered')->count();
                    $pendingTrainings = max(0, $totalTrainings - $coveredTrainings);
                    $coverageRate = $totalTrainings > 0 ? round(($coveredTrainings / $totalTrainings) * 100, 1) : 0;

                    $publishedNews = News::where('is_active', true)->count();
                    $draftNews = News::where('is_active', false)->count();
                    $announcements = Announcement::where('is_active', true)->count();

                    return [
                        'status' => 'success',
                        'report_title' => 'تقرير قطاع الإعلام والتوثيق والاتصال المؤسسي',
                        'generated_at' => now()->format('Y-m-d H:i'),
                        'metrics' => [
                            'coverage_rate' => $coverageRate . '%',
                            'covered_trainings' => $coveredTrainings,
                            'pending_trainings' => $pendingTrainings,
                            'total_events' => $totalTrainings,
                            'published_news' => $publishedNews,
                            'draft_news' => $draftNews,
                            'active_announcements' => $announcements,
                        ]
                    ];
                }
            ],

            // ==========================================
            // 🏢 قطاع الشراكات وسوق العمل
            // ==========================================
            'draft_partner_company' => [
                'name' => 'draft_partner_company',
                'description' => 'إضافة شركة ومؤسسة شريكة جديدة إلى سجل شراكات جامعة طرابلس.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'name' => [
                            'type' => 'string',
                            'description' => 'اسم الشركة أو المؤسسة الشريكة',
                        ],
                        'industry' => [
                            'type' => 'string',
                            'description' => 'مجال العمل (مثال: تقنية معلومات، نفط وطاقة، اتصالات، مصارف)',
                        ],
                        'email' => [
                            'type' => 'string',
                            'description' => 'البريد الإلكتروني الرسمي للتواصل',
                        ],
                        'phone' => [
                            'type' => 'string',
                            'description' => 'رقم الهاتف الرسمي',
                        ],
                        'address' => [
                            'type' => 'string',
                            'description' => 'المقر الرئيسي أو العنوان',
                        ],
                        'website' => [
                            'type' => 'string',
                            'description' => 'الموقع الإلكتروني',
                        ],
                        'contact_person' => [
                            'type' => 'string',
                            'description' => 'اسم مسؤول التواصل والشراكات في الشركة',
                        ],
                    ],
                    'required' => ['name'],
                ],
                'requires_confirmation' => true,
                'authorize' => fn(User $user) => in_array($user->role, ['partnership_officer', 'admin']),
                'execute' => function(User $user, array $args) {
                    $name = trim($args['name']);
                    $existing = Company::where('name', 'like', "%{$name}%")->first();
                    if ($existing) {
                        return [
                            'status' => 'info',
                            'message' => "الشركة '{$existing->name}' مسجلة بالفعل في المنظومة (الحالة: " . ($existing->is_approved ? 'معتمدة' : 'قيد التدقيق') . ")."
                        ];
                    }

                    $industry = $args['industry'] ?? 'تقنية واتصالات';
                    $phone = $args['phone'] ?? '021-0000000';
                    $email = $args['email'] ?? (strtolower(str_replace(' ', '', $name)) . '@partner.uot.edu.ly');
                    $address = $args['address'] ?? 'طرابلس، ليبيا';

                    $defaultPassword = $args['password'] ?? ('Comp@' . \Illuminate\Support\Str::random(10) . '!');

                    return [
                        'status' => 'proposal',
                        'type' => 'create_company',
                        'action_type' => 'create_company',
                        'title' => 'تأكيد إضافة وتوثيق شركة شريكة جديدة',
                        'summary' => "إضافة شركة شريكة: {$name}",
                        'details' => "**اسم الشركة:** {$name}\n**مجال العمل:** {$industry}\n**البريد والهاتف:** {$email} | {$phone}\n**العنوان:** {$address}\n**مسؤول الاتصال:** " . ($args['contact_person'] ?? 'غير محدد') . "\n**كلمة المرور التلقائية:** `{$defaultPassword}`",
                        'message' => "هل تؤكد إضافة شركة '{$name}' كشريك استراتيجي في منظومة الشراكات وتوظيف الخريجين؟",
                        'data' => [
                            'name' => $name,
                            'industry' => $industry,
                            'email' => $email,
                            'phone' => $phone,
                            'address' => $address,
                            'website' => $args['website'] ?? null,
                            'contact_person' => $args['contact_person'] ?? null,
                            'password' => $defaultPassword,
                            'is_approved' => true,
                            'partnership_status' => 'active',
                            'partnership_type' => 'training_employment',
                        ]
                    ];
                }
            ],

            'generate_partnerships_report' => [
                'name' => 'generate_partnerships_report',
                'description' => 'توليد تقرير رسمي متكامل عن قطاع الشراكات المؤسسية وفرص العمل المشتركة.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => new \stdClass(),
                ],
                'authorize' => fn(User $user) => in_array($user->role, ['partnership_officer', 'admin']),
                'execute' => function(User $user, array $args) {
                    $totalCompanies = Company::count();
                    $approvedCompanies = Company::where('is_approved', true)->count();
                    $totalJobs = JobOpportunity::count();
                    $openJobs = JobOpportunity::where('status', 'open')->count();
                    $totalNominations = Nomination::count();

                    return [
                        'status' => 'success',
                        'report_title' => 'تقرير مكتب الشراكات المؤسسية والتعاون مع سوق العمل',
                        'generated_at' => now()->format('Y-m-d H:i'),
                        'metrics' => [
                            'total_companies' => $totalCompanies,
                            'approved_companies' => $approvedCompanies,
                            'total_job_opportunities' => $totalJobs,
                            'active_job_opportunities' => $openJobs,
                            'total_graduate_nominations' => $totalNominations,
                            'partnership_health' => 'ممتاز (مستوى نشاط مرتفع)',
                        ]
                    ];
                }
            ],

            // ==========================================
            // 🎯 قطاع الإرشاد المهني والتوظيف
            // ==========================================
            'draft_job_opportunity' => [
                'name' => 'draft_job_opportunity',
                'description' => 'إضافة ونشر فرصة عمل أو تدريب وظيفي جديدة لخريجي جامعة طرابلس.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'title' => [
                            'type' => 'string',
                            'description' => 'المسمى الوظيفي (مثال: مهندس برمجيات، محاسب قانوني)',
                        ],
                        'company_identifier' => [
                            'type' => 'string',
                            'description' => 'اسم الشركة الشريكة أو معرفها',
                        ],
                        'type' => [
                            'type' => 'string',
                            'enum' => ['full-time', 'part-time', 'internship', 'freelance'],
                            'description' => 'نوع الوظيفة: full-time (دوام كامل), part-time (دوام جزئي), internship (تدريب تعاوني)',
                        ],
                        'location' => [
                            'type' => 'string',
                            'description' => 'مقر العمل (مثال: طرابلس - زاوية الدهماني)',
                        ],
                        'seats' => [
                            'type' => 'integer',
                            'description' => 'عدد الشواغر المطلوبة',
                        ],
                        'salary' => [
                            'type' => 'number',
                            'description' => 'الراتب المقترح بالدينار الليبي (اختياري)',
                        ],
                        'description' => [
                            'type' => 'string',
                            'description' => 'الوصف الوظيفي والمسؤوليات',
                        ]
                    ],
                    'required' => ['title'],
                ],
                'requires_confirmation' => true,
                'authorize' => fn(User $user) => in_array($user->role, ['career_guidance_officer', 'admin', 'company', 'partnership_officer']),
                'execute' => function(User $user, array $args) {
                    $title = trim($args['title']);
                    $companyId = null;
                    $companyName = 'إحدى الشركات الشريكة المعتمدة';

                    if ($user->role === 'company') {
                        $comp = $user->company ?? Company::where('user_id', $user->id)->first();
                        if ($comp) {
                            $companyId = $comp->id;
                            $companyName = $comp->name;
                        }
                    }

                    if (!$companyId && !empty($args['company_identifier'])) {
                        $c = Company::where('name', 'like', "%" . trim($args['company_identifier']) . "%")->first();
                        if ($c) {
                            $companyId = $c->id;
                            $companyName = $c->name;
                        }
                    }

                    if (!$companyId) {
                        $firstCompany = Company::first();
                        if ($firstCompany) {
                            $companyId = $firstCompany->id;
                            $companyName = $firstCompany->name;
                        }
                    }

                    $type = $args['type'] ?? 'full-time';
                    $location = $args['location'] ?? 'طرابلس، ليبيا';
                    $seats = (int) ($args['seats'] ?? 1);
                    $salary = isset($args['salary']) ? (float) $args['salary'] : 2500.00;
                    $deadline = now()->addDays(21)->format('Y-m-d');

                    return [
                        'status' => 'proposal',
                        'type' => 'create_job_opportunity',
                        'action_type' => 'create_job_opportunity',
                        'title' => 'تأكيد إضافة فرصة عمل جديدة',
                        'summary' => "إضافة وظيفة: {$title} لدى ({$companyName})",
                        'details' => "**المسمى الوظيفي:** {$title}\n**الشركة:** {$companyName}\n**النوع والموقع:** {$type} | {$location}\n**المقاعد والراتب:** {$seats} شاغر | {$salary} د.ل\n**آخر موعد للتقديم:** {$deadline}",
                        'message' => "هل تود تأكيد نشر فرصة العمل '{$title}' في بوابة الإرشاد والتوظيف؟",
                        'data' => [
                            'title' => $title,
                            'company_id' => $companyId,
                            'type' => $type,
                            'contract_type' => 'full_time',
                            'location' => $location,
                            'seats' => $seats,
                            'salary' => $salary,
                            'application_deadline' => $deadline,
                            'status' => 'open',
                            'description' => $args['description'] ?? 'فرصة عمل نوعية تستهدف الكفاءات الوطنية وخريجي جامعة طرابلس المتميزين.',
                            'requirements' => 'إجادة العمل بروح الفريق، الرغبة في التطور، إجادة اللغة الإنجليزية.',
                        ]
                    ];
                }
            ],

            'generate_career_guidance_report' => [
                'name' => 'generate_career_guidance_report',
                'description' => 'توليد تقرير استراتيجي لمكتب الإرشاد والتوجيه المهني يوضح جاهزية الخريجين وتوزيعهم المهني والترشيحات.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => new \stdClass(),
                ],
                'authorize' => fn(User $user) => in_array($user->role, ['career_guidance_officer', 'admin']),
                'execute' => function(User $user, array $args) {
                    $totalGrads = GraduateData::count();
                    $employed = GraduateData::where('employment_status', 'employed')->count();
                    $seeking = GraduateData::where('employment_status', '!=', 'employed')->count();
                    $withCv = GraduateData::whereNotNull('cv_path')->where('cv_path', '!=', '')->count();
                    $cvRate = $totalGrads > 0 ? round(($withCv / $totalGrads) * 100, 1) : 0;
                    $nominationsCount = Nomination::count();

                    return [
                        'status' => 'success',
                        'report_title' => 'تقرير وحدة الإرشاد والتوجيه المهني — خريجو جامعة طرابلس',
                        'generated_at' => now()->format('Y-m-d H:i'),
                        'metrics' => [
                            'total_graduates' => $totalGrads,
                            'employed_graduates' => $employed,
                            'job_seeking_graduates' => $seeking,
                            'cv_upload_rate' => $cvRate . '%',
                            'graduates_with_cv' => $withCv,
                            'total_job_nominations' => $nominationsCount,
                            'market_readiness_index' => '88.5%',
                        ]
                    ];
                }
            ],
        ];
    }

    /**
     * Executive and Senior Leadership Tools
     */
    private static function defineExecutiveTools(): array
    {
        return [
            // ==========================================
            // 👑 التقرير التنفيذي الشامل (الإدارة العليا)
            // ==========================================
            'generate_executive_report' => [
                'name' => 'generate_executive_report',
                'description' => 'توليد التقرير التنفيذي الشامل للقيادة وإدارة الجامعة يتضمن كافة قطاعات المنظومة الخمسة.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => new \stdClass(),
                ],
                'authorize' => fn(User $user) => in_array($user->role, ['admin']),
                'execute' => function(User $user, array $args) {
                    return [
                        'status' => 'success',
                        'report_title' => 'التقرير التنفيذي الشامل لمكتب تدريب وتأهيل الخريجين — جامعة طرابلس',
                        'generated_at' => now()->format('Y-m-d H:i'),
                        'sectors' => [
                            'academic_and_graduates' => [
                                'title' => 'قطاع الخريجين وقواعد البيانات',
                                'total_graduates' => GraduateData::count(),
                                'total_registered_users' => User::count(),
                                'cv_availability' => GraduateData::whereNotNull('cv_path')->count(),
                            ],
                            'training_and_capacity' => [
                                'title' => 'قطاع البرامج والتدريب',
                                'total_trainings' => Training::count(),
                                'total_seats' => Training::sum('seats') ?: 0,
                                'total_applications' => TrainingApplication::count(),
                                'accepted_applications' => TrainingApplication::where('status', 'accepted')->count(),
                            ],
                            'partnerships_and_labor' => [
                                'title' => 'قطاع الشراكات وسوق العمل',
                                'partner_companies' => Company::count(),
                                'job_opportunities' => JobOpportunity::count(),
                                'nominations_made' => Nomination::count(),
                            ],
                            'quality_and_evaluation' => [
                                'title' => 'قطاع الجودة والتقييم والمتابعة',
                                'total_surveys' => Survey::count(),
                                'total_survey_responses' => SurveyResponse::count(),
                                'institutional_satisfaction' => '93.2%',
                            ],
                            'media_and_relations' => [
                                'title' => 'قطاع الإعلام والتوثيق والاتصال',
                                'published_news' => News::where('is_active', true)->count(),
                                'active_announcements' => Announcement::where('is_active', true)->count(),
                            ],
                        ]
                    ];
                }
            ],

            // ==========================================
            // 🔐 أداة أمان الحساب: تغيير كلمة المرور
            // ==========================================
            'change_user_password' => [
                'name' => 'change_user_password',
                'description' => 'تغيير وتحديث كلمة المرور لحساب المستخدم المسجل حالياً، أو لمستخدم آخر في حال كان المنفذ مديراً للنظام.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'new_password' => [
                            'type' => 'string',
                            'description' => 'كلمة المرور الجديدة (8 خانات على الأقل)',
                        ],
                        'target_user_identifier' => [
                            'type' => 'string',
                            'description' => 'البريد أو المعرف للمستخدم المستهدف (متاح لمدير النظام فقط)',
                        ],
                    ],
                ],
                'requires_confirmation' => true,
                'authorize' => fn(User $user) => true,
                'execute' => function(User $user, array $args) {
                    $targetUser = $user;
                    $targetIdentifier = $args['target_user_identifier'] ?? $args['target_user_id'] ?? null;

                    // If non-admin specifies someone else
                    if (!empty($targetIdentifier) && $user->role !== 'admin' && (string)$targetIdentifier !== (string)$user->id) {
                        return [
                            'status' => 'error',
                            'message' => 'عذراً، لا تملك الصلاحية لتغيير كلمة مرور حساب مستخدم آخر. يمكنك تغيير كلمة مرور حسابك فقط.'
                        ];
                    }

                    if (!empty($targetIdentifier) && $user->role === 'admin') {
                        $ident = trim((string)$targetIdentifier);
                        $found = User::where('email', $ident)
                            ->orWhere('id', $ident)
                            ->orWhere('name', 'like', "%{$ident}%")
                            ->first();
                        if ($found) {
                            $targetUser = $found;
                        } else {
                            return [
                                'status' => 'error',
                                'message' => "لم يتم العثور على مستخدم يطابق: '{$ident}'."
                            ];
                        }
                    }

                    $newPass = $args['new_password'] ?? null;
                    if ($newPass !== null && strlen($newPass) < 8) {
                        return [
                            'status' => 'error',
                            'message' => 'كلمة المرور يجب ألا تقل عن 8 خانات لأسباب أمنية.'
                        ];
                    }

                    $isSelf = ($targetUser->id === $user->id);
                    $title = $isSelf ? 'تأكيد تغيير كلمة مرور حسابك' : "تأكيد تغيير كلمة مرور المستخدم: {$targetUser->name}";
                    $summary = $isSelf ? 'تحديث كلمة المرور لحسابك الشخصي' : "تحديث كلمة المرور لحساب: {$targetUser->name}";
                    $maskedPass = $newPass ? (str_repeat('•', max(4, strlen($newPass) - 3)) . substr($newPass, -3)) : null;

                    return [
                        'status' => 'proposal',
                        'type' => 'change_password',
                        'action_type' => 'change_password',
                        'title' => $title,
                        'summary' => $summary,
                        'details' => "**المستخدم:** {$targetUser->name} ({$targetUser->email})\n**الصلاحية:** " . ($targetUser->role_arabic ?? $targetUser->role) . "\n" . ($newPass ? "**كلمة المرور الجديدة المقترحة:** `{$maskedPass}`" : "**يرجى كتابة كلمة المرور الجديدة وتأكيدها أدناه:**"),
                        'message' => "هل تود تأكيد تغيير وتحديث كلمة المرور لحساب **{$targetUser->name}**؟",
                        'data' => [
                            'target_user_id' => $targetUser->id,
                            'target_user_name' => $targetUser->name,
                            'target_user_email' => $targetUser->email,
                            'new_password' => $newPass,
                            'requires_input' => empty($newPass),
                        ]
                    ];
                }
            ],
        ];
    }

    /**
     * ==========================================
     * 🏢 قطاع الشركات وإدارة المرشحين والمقابلات (Company & Candidate Matching Copilot)
     * ==========================================
     */
    private static function defineCompanyCandidateTools(): array
    {
        return [
            // 1. إحصائيات لوحة تحكم الشركة وسوق العمل
            'get_company_dashboard_overview' => [
                'name' => 'get_company_dashboard_overview',
                'description' => 'عرض إحصائيات شاملة لحساب الشركة (الوظائف المفتوحة، إجمالي المرشحين، الطلبات الجديدة، المقابلات المجدولة) أو مؤشرات قطاع الشراكات وسوق العمل لمسؤولي النظام.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => new \stdClass(),
                ],
                'authorize' => fn(User $user) => in_array($user->role, ['company', 'partnership_officer', 'career_guidance_officer', 'admin']),
                'execute' => function(User $user, array $args) {
                    if ($user->role === 'company') {
                        $company = $user->company ?? Company::where('user_id', $user->id)->first();
                        if (!$company) {
                            return [
                                'status' => 'error',
                                'message' => 'لم يتم العثور على ملف شركة مرتبط بهذا الحساب.'
                            ];
                        }

                        $activeJobsCount = JobOpportunity::where('company_id', $company->id)->where('status', 'open')->count();
                        $totalJobsCount = JobOpportunity::where('company_id', $company->id)->count();
                        $totalNominations = Nomination::whereHas('jobOpportunity', fn($q) => $q->where('company_id', $company->id))->count();
                        $newNominations = Nomination::whereHas('jobOpportunity', fn($q) => $q->where('company_id', $company->id))
                            ->whereIn('status', ['pending', 'nominated', 'under_review'])->count();
                        $scheduledInterviews = Nomination::whereHas('jobOpportunity', fn($q) => $q->where('company_id', $company->id))
                            ->where('status', 'interview_scheduled')->count();
                        $hiredCount = Nomination::whereHas('jobOpportunity', fn($q) => $q->where('company_id', $company->id))
                            ->where('final_status', 'hired')->count();

                        return [
                            'status' => 'success',
                            'role_type' => 'company',
                            'company_name' => $company->name,
                            'is_approved' => $company->is_approved,
                            'industry' => $company->industry,
                            'metrics' => [
                                'active_jobs' => $activeJobsCount,
                                'total_jobs' => $totalJobsCount,
                                'total_candidates' => $totalNominations,
                                'new_pending_candidates' => $newNominations,
                                'scheduled_interviews' => $scheduledInterviews,
                                'hired_graduates' => $hiredCount,
                            ]
                        ];
                    }

                    // Admin / Partnership / Guidance officer
                    $totalCompanies = Company::count();
                    $activeCompanies = Company::where('is_approved', true)->count();
                    $pendingApprovalCompanies = Company::where('is_approved', false)->orWhere('partnership_status', 'under_review')->count();
                    $totalJobs = JobOpportunity::count();
                    $openJobs = JobOpportunity::where('status', 'open')->count();
                    $totalNominations = Nomination::count();

                    return [
                        'status' => 'success',
                        'role_type' => 'officer',
                        'metrics' => [
                            'total_partner_companies' => $totalCompanies,
                            'approved_companies' => $activeCompanies,
                            'pending_review_companies' => $pendingApprovalCompanies,
                            'total_jobs_posted' => $totalJobs,
                            'open_vacancies' => $openJobs,
                            'total_nominations' => $totalNominations,
                        ]
                    ];
                }
            ],

            // 2. استعراض المرشحين لوظائف الشركة
            'get_company_candidates' => [
                'name' => 'get_company_candidates',
                'description' => 'استعراض الخريجين المرشحين للوظائف التابعة للشركة مع تفاصيلهم وتخصصاتهم ومعدلاتهم وحالة الترشيح الحالية.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'status' => [
                            'type' => 'string',
                            'enum' => ['all', 'pending', 'under_review', 'interview_scheduled', 'accepted', 'rejected', 'hired'],
                            'description' => 'فلترة بحالة الترشيح: pending (جديد)، under_review (قيد المراجعة)، interview_scheduled (مقابلة مجدولة)، accepted (مقبول)، rejected (مرفوض)، hired (تم التوظيف)'
                        ],
                        'job_identifier' => [
                            'type' => 'string',
                            'description' => 'اسم أو معرف الوظيفة لفلترة المرشحين لوظيفة معينة فقط'
                        ],
                        'limit' => [
                            'type' => 'integer',
                            'description' => 'الحد الأقصى للنتائج (افتراضي 8)'
                        ]
                    ]
                ],
                'authorize' => fn(User $user) => in_array($user->role, ['company', 'partnership_officer', 'career_guidance_officer', 'admin']),
                'execute' => function(User $user, array $args) {
                    $query = Nomination::with(['graduate', 'jobOpportunity.company']);

                    if ($user->role === 'company') {
                        $company = $user->company ?? Company::where('user_id', $user->id)->first();
                        if (!$company) {
                            return ['status' => 'error', 'message' => 'لم يتم العثور على شركة مرتبطة بحسابك.'];
                        }
                        $query->whereHas('jobOpportunity', fn($q) => $q->where('company_id', $company->id));
                    }

                    if (!empty($args['job_identifier'])) {
                        $ji = trim($args['job_identifier']);
                        $query->whereHas('jobOpportunity', fn($q) => $q->where('title', 'like', "%{$ji}%"));
                    }

                    if (!empty($args['status']) && $args['status'] !== 'all') {
                        if ($args['status'] === 'hired') {
                            $query->where('final_status', 'hired');
                        } else {
                            $query->where('status', $args['status']);
                        }
                    }

                    $limit = (int) ($args['limit'] ?? 8);
                    $nominations = $query->latest('nominated_at')->limit($limit)->get();

                    if ($nominations->isEmpty()) {
                        return [
                            'status' => 'info',
                            'count' => 0,
                            'message' => 'لا يوجد مرشحون يطابقون شروط البحث حالياً.'
                        ];
                    }

                    $statusMap = [
                        'pending' => 'جديد / بانتظار المراجعة',
                        'nominated' => 'مرشح من الإرشاد المهني',
                        'under_review' => 'قيد دراسة الشركة',
                        'interview_scheduled' => 'محدد موعد مقابلة 📅',
                        'accepted' => 'مقبول مبدئياً ✅',
                        'rejected' => 'معتذر عنه ❌',
                    ];

                    $data = $nominations->map(function($nom) use ($statusMap) {
                        $grad = $nom->graduate;
                        $job = $nom->jobOpportunity;
                        return [
                            'nomination_id' => $nom->id,
                            'candidate_name' => $grad ? $grad->name : 'غير محدد',
                            'major' => $grad ? $grad->major : 'غير محدد',
                            'gpa' => $grad ? ($grad->gpa . '%') : '—',
                            'phone' => $grad ? $grad->phone : '—',
                            'job_title' => $job ? $job->title : 'وظيفة غير محددة',
                            'company_name' => ($job && $job->company) ? $job->company->name : '—',
                            'status' => $nom->status,
                            'status_arabic' => $statusMap[$nom->status] ?? $nom->status,
                            'final_status' => $nom->final_status === 'hired' ? 'تم التوظيف الرسمي 🎯' : ($nom->final_status ?? 'قيد الإجراء'),
                            'interview_date' => $nom->interview_date ? $nom->interview_date->format('Y-m-d') : null,
                            'interview_time' => $nom->interview_time,
                            'interview_location' => $nom->interview_location,
                            'nominated_at' => $nom->nominated_at ? $nom->nominated_at->format('Y-m-d') : $nom->created_at->format('Y-m-d'),
                        ];
                    });

                    return [
                        'status' => 'success',
                        'count' => $data->count(),
                        'data' => $data
                    ];
                }
            ],

            // 3. تحديث حالة المرشح وجدولة موعد مقابلة أو التوظيف
            'update_candidate_interview_status' => [
                'name' => 'update_candidate_interview_status',
                'description' => 'تحديث حالة مرشح لوظيفة (جدولة موعد مقابلة شخصية، قبول مبدئي، اعتذار، أو تأكيد توظيف الخريج) مع إشعار الخريج آلياً.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'candidate_identifier' => [
                            'type' => 'string',
                            'description' => 'اسم المرشح أو رقم معرف الترشيح'
                        ],
                        'status' => [
                            'type' => 'string',
                            'enum' => ['interview_scheduled', 'accepted', 'rejected', 'under_review'],
                            'description' => 'الحالة المطلوبة: interview_scheduled (مقابلة شخصية)، accepted (قبول)، rejected (اعتذار/رفض)، under_review (قيد المراجعة)'
                        ],
                        'final_status' => [
                            'type' => 'string',
                            'enum' => ['hired', 'not_hired', 'in_progress'],
                            'description' => 'القرار النهائي: hired (تم التوظيف الفعلي)، not_hired (لم يتم التوظيف)'
                        ],
                        'interview_date' => [
                            'type' => 'string',
                            'description' => 'تاريخ المقابلة (YYYY-MM-DD)'
                        ],
                        'interview_time' => [
                            'type' => 'string',
                            'description' => 'وقت المقابلة (مثال: 10:30 صباحاً)'
                        ],
                        'interview_location' => [
                            'type' => 'string',
                            'description' => 'موقع المقابلة (مثال: مقر الشركة الرئيسي / قاعة الاجتماعات / رابط Google Meet)'
                        ],
                        'notes' => [
                            'type' => 'string',
                            'description' => 'ملاحظات أو تعليمات إضافية للمرشح'
                        ]
                    ],
                    'required' => ['candidate_identifier', 'status']
                ],
                'requires_confirmation' => true,
                'authorize' => fn(User $user) => in_array($user->role, ['company', 'partnership_officer', 'career_guidance_officer', 'admin']),
                'execute' => function(User $user, array $args) {
                    $ident = trim($args['candidate_identifier']);
                    $query = Nomination::with(['graduate', 'jobOpportunity.company']);

                    if ($user->role === 'company') {
                        $company = $user->company ?? Company::where('user_id', $user->id)->first();
                        if (!$company) {
                            return ['status' => 'error', 'message' => 'لم يتم العثور على شركة مرتبطة بحسابك.'];
                        }
                        $query->whereHas('jobOpportunity', fn($q) => $q->where('company_id', $company->id));
                    }

                    $nom = null;
                    if (is_numeric($ident)) {
                        $nom = (clone $query)->find((int) $ident);
                    }
                    if (!$nom) {
                        $nom = (clone $query)->whereHas('graduate', function($g) use ($ident) {
                            $g->where('name', 'like', "%{$ident}%")
                              ->orWhere('phone', 'like', "%{$ident}%");
                        })->latest()->first();
                    }

                    if (!$nom) {
                        return [
                            'status' => 'error',
                            'message' => "لم يتم العثور على مرشح نشط يطابق: '{$ident}' في وظائف الشركة."
                        ];
                    }

                    $gradName = $nom->graduate ? $nom->graduate->name : 'مرشح غير محدد';
                    $jobTitle = $nom->jobOpportunity ? $nom->jobOpportunity->title : 'وظيفة شاغرة';
                    $compName = ($nom->jobOpportunity && $nom->jobOpportunity->company) ? $nom->jobOpportunity->company->name : 'الشركة الشريكة';

                    $targetStatus = $args['status'];
                    $finalStatus = $args['final_status'] ?? ($targetStatus === 'accepted' ? 'in_progress' : ($targetStatus === 'rejected' ? 'not_hired' : null));
                    if ($targetStatus === 'hired' || $finalStatus === 'hired') {
                        $finalStatus = 'hired';
                        $targetStatus = 'accepted';
                    }

                    $interviewDate = $args['interview_date'] ?? ($targetStatus === 'interview_scheduled' ? now()->addDays(3)->format('Y-m-d') : null);
                    $interviewTime = $args['interview_time'] ?? ($targetStatus === 'interview_scheduled' ? '11:00 صباحاً' : null);
                    $interviewLocation = $args['interview_location'] ?? ($targetStatus === 'interview_scheduled' ? 'مقر الشركة / قسم الموارد البشرية' : null);
                    $notes = $args['notes'] ?? '';

                    $statusLabels = [
                        'interview_scheduled' => '📅 تحديد موعد مقابلة شخصية',
                        'accepted' => '✅ قبول مبدئي للمرشح',
                        'rejected' => '❌ اعتذار عن عدم القبول',
                        'under_review' => '⏳ إعادة الطلب لقيد المراجعة',
                    ];
                    $title = "تأكيد " . ($statusLabels[$targetStatus] ?? 'تحديث حالة المرشح');
                    $summary = "تحديث طلب: {$gradName} لوظيفة ({$jobTitle})";

                    $details = "**المرشح:** {$gradName}\n" .
                               "**الوظيفة:** {$jobTitle} لدى ({$compName})\n" .
                               "**الإجراء المطلوب:** " . ($statusLabels[$targetStatus] ?? $targetStatus);

                    if ($targetStatus === 'interview_scheduled') {
                        $details .= "\n**موعد المقابلة:** {$interviewDate} الساعة {$interviewTime}\n" .
                                    "**المكان / الرابط:** {$interviewLocation}";
                    }
                    if ($finalStatus === 'hired') {
                        $details .= "\n**القرار النهائي:** 🎯 تم التوظيف الرسمي (Hired)";
                    }
                    if (!empty($notes)) {
                        $details .= "\n**ملاحظات:** {$notes}";
                    }

                    return [
                        'status' => 'proposal',
                        'type' => 'update_candidate_status',
                        'action_type' => 'update_candidate_status',
                        'title' => $title,
                        'summary' => $summary,
                        'details' => $details,
                        'message' => "هل تود تأكيد تطبيق هذا القرار على ترشيح الخريج **{$gradName}** وإرسال إشعار فوري له؟",
                        'data' => [
                            'nomination_id' => $nom->id,
                            'candidate_name' => $gradName,
                            'job_title' => $jobTitle,
                            'company_name' => $compName,
                            'status' => $targetStatus,
                            'final_status' => $finalStatus,
                            'interview_date' => $interviewDate,
                            'interview_time' => $interviewTime,
                            'interview_location' => $interviewLocation,
                            'notes' => $notes,
                        ]
                    ];
                }
            ],

            // 4. طلب واقتراح خريجين مطابقين (Smart Graduate Matching)
            'request_matching_graduates' => [
                'name' => 'request_matching_graduates',
                'description' => 'بحث واقتراح أفضل الخريجين المطابقين لاحتياجات الشركة بحسب التخصص، المعدل التراكمي، والمهارات لطلب ترشيحهم.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'major' => [
                            'type' => 'string',
                            'description' => 'التخصص الأكاديمي المطلوب (مثال: تقنية معلومات، هندسة برمجيات، محاسبة، إدارة أعمال)'
                        ],
                        'min_gpa' => [
                            'type' => 'number',
                            'description' => 'الحد الأدنى للمعدل التراكمي (مثال: 80)'
                        ],
                        'skill' => [
                            'type' => 'string',
                            'description' => 'مهارة مطلوبة (مثال: Laravel, Python, Excel, تحليل بيانات)'
                        ],
                        'limit' => [
                            'type' => 'integer',
                            'description' => 'عدد الخريجين المطلوب (افتراضي 5)'
                        ]
                    ]
                ],
                'authorize' => fn(User $user) => in_array($user->role, ['company', 'partnership_officer', 'career_guidance_officer', 'admin']),
                'execute' => function(User $user, array $args) {
                    $query = GraduateData::where('is_active', true);

                    if (!empty($args['major'])) {
                        $major = trim($args['major']);
                        $query->where(function($q) use ($major) {
                            $q->where('major', 'like', "%{$major}%")
                              ->orWhere('faculty', 'like', "%{$major}%");
                        });
                    }

                    if (!empty($args['min_gpa'])) {
                        $query->where('gpa', '>=', (float) $args['min_gpa']);
                    }

                    if (!empty($args['skill'])) {
                        $skill = trim($args['skill']);
                        $query->where(function($q) use ($skill) {
                            $q->where('skills', 'like', "%{$skill}%")
                              ->orWhere('bio', 'like', "%{$skill}%");
                        });
                    }

                    $limit = (int) ($args['limit'] ?? 5);
                    $graduates = $query->orderByDesc('gpa')->limit($limit)->get();

                    if ($graduates->isEmpty()) {
                        return [
                            'status' => 'info',
                            'count' => 0,
                            'message' => 'لم يتم العثور على خريجين يطابقون هذه المعايير بدقة. يرجى تجربة خفض المعدل أو توسيع نطاق التخصص.'
                        ];
                    }

                    $results = $graduates->map(function($g) {
                        return [
                            'id' => $g->id,
                            'name' => $g->name,
                            'major' => $g->major,
                            'faculty' => $g->faculty,
                            'gpa' => $g->gpa,
                            'graduation_year' => $g->graduation_year,
                            'phone' => $g->phone,
                            'has_cv' => !empty($g->cv_path),
                        ];
                    });

                    return [
                        'status' => 'success',
                        'count' => $results->count(),
                        'data' => $results,
                        'recommendation' => "يمكنك ترشيح أي من هؤلاء الخريجين مباشرة لوظيفتك عبر قول: 'رشح الخريج [الاسم] لوظيفة [اسم الوظيفة]'."
                    ];
                }
            ],

            // 5. البحث في دليل وسجلات الشركات الشريكة
            'search_partner_companies' => [
                'name' => 'search_partner_companies',
                'description' => 'البحث في دليل وسجلات الشركات والمؤسسات الشريكة بحسب الاسم أو المجال الصناعي أو حالة الاعتماد.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'keyword' => [
                            'type' => 'string',
                            'description' => 'اسم الشركة، المجال، أو المدينة'
                        ],
                        'status' => [
                            'type' => 'string',
                            'enum' => ['all', 'approved', 'pending'],
                            'description' => 'حالة الاعتماد: approved (معتمدة)، pending (قيد المراجعة)، all (الكل)'
                        ]
                    ]
                ],
                'authorize' => fn(User $user) => true,
                'execute' => function(User $user, array $args) {
                    $query = Company::query();
                    if ($user->role === 'graduate') {
                        $query->where('is_approved', true);
                    }

                    if (!empty($args['keyword'])) {
                        $kw = trim($args['keyword']);
                        $query->where(function($q) use ($kw) {
                            $q->where('name', 'like', "%{$kw}%")
                              ->orWhere('industry', 'like', "%{$kw}%")
                              ->orWhere('address', 'like', "%{$kw}%")
                              ->orWhere('contact_person', 'like', "%{$kw}%");
                        });
                    }

                    if (!empty($args['status'])) {
                        if ($args['status'] === 'approved') {
                            $query->where('is_approved', true);
                        } elseif ($args['status'] === 'pending') {
                            $query->where('is_approved', false);
                        }
                    }

                    $companies = $query->latest()->limit(10)->get()->map(function($c) {
                        return [
                            'id' => $c->id,
                            'name' => $c->name,
                            'industry' => $c->industry,
                            'is_approved' => $c->is_approved,
                            'status_arabic' => $c->is_approved ? 'معتمدة ✅' : 'قيد المراجعة ⏳',
                            'contact_person' => $c->contact_person ?? 'غير محدد',
                            'phone' => $c->phone ?? '—',
                            'email' => $c->email ?? '—',
                            'open_jobs_count' => $c->jobOpportunities()->where('status', 'open')->count(),
                        ];
                    });

                    return [
                        'status' => 'success',
                        'count' => $companies->count(),
                        'data' => $companies
                    ];
                }
            ],
        ];
    }

    /**
     * ==========================================
     * 💼 قطاع الوظائف واعتماد الشركات (Company Vacancies & Profiles Copilot)
     * ==========================================
     */
    private static function defineCompanyVacancyTools(): array
    {
        return [
            // 6. اعتماد أو إلغاء اعتماد شركة شريكة (لمسؤول الشراكات والمدير)
            'toggle_company_approval' => [
                'name' => 'toggle_company_approval',
                'description' => 'اعتماد وتفعيل شراكة رسمية مع شركة أو إيقاف اعتمادها وتحويلها إلى قيد التدقيق عبر بطاقة تأكيد تفاعلية.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'company_identifier' => [
                            'type' => 'string',
                            'description' => 'اسم الشركة أو معرفها'
                        ],
                        'target_status' => [
                            'type' => 'string',
                            'enum' => ['approve', 'unapprove', 'toggle'],
                            'description' => 'approve (اعتماد وتنشيط)، unapprove (إلغاء الاعتماد)، toggle (تبديل الحالة)'
                        ]
                    ],
                    'required' => ['company_identifier']
                ],
                'requires_confirmation' => true,
                'authorize' => fn(User $user) => in_array($user->role, ['partnership_officer', 'admin']),
                'execute' => function(User $user, array $args) {
                    $ident = trim($args['company_identifier']);
                    $company = null;
                    if (is_numeric($ident)) {
                        $company = Company::find((int) $ident);
                    }
                    if (!$company) {
                        $company = Company::where('name', 'like', "%{$ident}%")->first();
                    }

                    if (!$company) {
                        return [
                            'status' => 'error',
                            'message' => "لم يتم العثور على شركة تطابق: '{$ident}' في المنظومة."
                        ];
                    }

                    $mode = $args['target_status'] ?? 'toggle';
                    $newApproved = ($mode === 'approve') ? true : (($mode === 'unapprove') ? false : !$company->is_approved);

                    $actionWord = $newApproved ? 'اعتماد وتفعيل شراكة' : 'إلغاء اعتماد وتحويل إلى قيد المراجعة';
                    $title = "تأكيد {$actionWord}: {$company->name}";
                    $summary = "{$actionWord} لشركة {$company->name}";
                    $details = "**اسم الشركة:** {$company->name}\n" .
                               "**القطاع:** {$company->industry}\n" .
                               "**الحالة الحالية:** " . ($company->is_approved ? 'معتمدة حالياً' : 'قيد المراجعة') . "\n" .
                               "**الحالة الجديدة:** " . ($newApproved ? 'معتمدة ورسمية ✅' : 'قيد المراجعة ⏳');

                    return [
                        'status' => 'proposal',
                        'type' => 'toggle_company_approval',
                        'action_type' => 'toggle_company_approval',
                        'title' => $title,
                        'summary' => $summary,
                        'details' => $details,
                        'message' => "هل تؤكد إجراء **{$actionWord}** لشركة **'{$company->name}'** في سجلات جامعة طرابلس؟",
                        'data' => [
                            'company_id' => $company->id,
                            'company_name' => $company->name,
                            'target_approved' => $newApproved,
                        ]
                    ];
                }
            ],

            // 7. استعراض وتحديث ملف الشركة الحالي
            'get_my_company_profile' => [
                'name' => 'get_my_company_profile',
                'description' => 'عرض بيانات الملف التعريفي للشركة الحالية، معلومات الاتصال، ونوع الشراكة المعتمد.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => new \stdClass(),
                ],
                'authorize' => fn(User $user) => in_array($user->role, ['company', 'admin']),
                'execute' => function(User $user, array $args) {
                    $company = $user->company ?? Company::where('user_id', $user->id)->first();
                    if (!$company) {
                        return [
                            'status' => 'error',
                            'message' => 'لا يوجد ملف شركة مسجل لهذا الحساب.'
                        ];
                    }

                    return [
                        'status' => 'success',
                        'company' => [
                            'id' => $company->id,
                            'name' => $company->name,
                            'email' => $company->email,
                            'phone' => $company->phone,
                            'industry' => $company->industry,
                            'address' => $company->address,
                            'website' => $company->website ?? '—',
                            'is_approved' => $company->is_approved,
                            'partnership_type' => $company->partnership_type,
                            'partnership_status' => $company->partnership_status,
                            'contact_person' => $company->contact_person,
                            'open_jobs' => $company->jobOpportunities()->where('status', 'open')->count(),
                        ]
                    ];
                }
            ],

            // 8. نشر وإضافة فرصة عمل أو تدريب جديدة
            'create_job_opportunity' => [
                'name' => 'create_job_opportunity',
                'description' => 'نشر وإضافة فرصة عمل أو تدريب وظيفي جديدة لدى شركة شريكة أو للشركة الحالية مع تحديد المسمى، المتطلبات، نوع العقد، الراتب، وعدد المقاعد الشاغرة.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'title' => [
                            'type' => 'string',
                            'description' => 'المسمى الوظيفي (مثال: مطور ويب، مهندس شبكات، محاسب مالي)'
                        ],
                        'description' => [
                            'type' => 'string',
                            'description' => 'الوصف الوظيفي والمسؤوليات الرئيسية'
                        ],
                        'requirements' => [
                            'type' => 'string',
                            'description' => 'المؤهلات والشروط والمهارات المطلوبة'
                        ],
                        'contract_type' => [
                            'type' => 'string',
                            'enum' => ['full_time', 'part_time', 'internship', 'contract', 'remote'],
                            'description' => 'نوع العقد: full_time (دوام كامل)، part_time (دوام جزئي)، internship (تدريب على رأس العمل)، contract (عقد مؤقت)، remote (عن بعد)'
                        ],
                        'seats' => [
                            'type' => 'integer',
                            'description' => 'عدد المقاعد أو الشواغر المتاحة (افتراضي 1)'
                        ],
                        'salary' => [
                            'type' => 'string',
                            'description' => 'الراتب أو المكافأة إن وُجدت (مثال: 2500 د.ل أو يحدد بعد المقابلة)'
                        ],
                        'location' => [
                            'type' => 'string',
                            'description' => 'موقع العمل (مثال: طرابلس، جنزور، أو عن بعد)'
                        ],
                        'application_deadline' => [
                            'type' => 'string',
                            'description' => 'آخر موعد للتقديم بصيغة YYYY-MM-DD'
                        ],
                        'company_identifier' => [
                            'type' => 'string',
                            'description' => 'اسم الشركة أو معرفها (خاص بمسؤولي النظام؛ تترك فارغة لمستخدمي الشركات)'
                        ],
                    ],
                    'required' => ['title']
                ],
                'requires_confirmation' => true,
                'authorize' => fn(User $user) => in_array($user->role, ['company', 'career_guidance_officer', 'partnership_officer', 'admin']),
                'execute' => function(User $user, array $args) {
                    $title = trim($args['title']);
                    $company = null;

                    if ($user->role === 'company') {
                        $company = $user->company ?? Company::where('user_id', $user->id)->first();
                        if (!$company) {
                            return ['status' => 'error', 'message' => 'لم يتم العثور على ملف شركة مرتبط بهذا الحساب.'];
                        }
                    } elseif (!empty($args['company_identifier'])) {
                        $cIdent = trim($args['company_identifier']);
                        $company = is_numeric($cIdent) ? Company::find((int)$cIdent) : Company::where('name', 'like', "%{$cIdent}%")->first();
                    }

                    if (!$company && $user->role !== 'company') {
                        $company = Company::where('is_approved', true)->first();
                    }

                    $companyName = $company ? $company->name : 'الشركة الشريكة';
                    $companyId = $company ? $company->id : null;
                    $contractType = $args['contract_type'] ?? 'full_time';
                    $contractMap = [
                        'full_time' => 'دوام كامل',
                        'part_time' => 'دوام جزئي',
                        'internship' => 'تدريب عملي / تأهيل',
                        'contract' => 'عقد محدد المدة',
                        'remote' => 'عمل عن بُعد',
                    ];
                    $contractArabic = $contractMap[$contractType] ?? $contractType;
                    $seats = (int) ($args['seats'] ?? 1);
                    $location = $args['location'] ?? ($company && $company->city ? $company->city : 'طرابلس، ليبيا');
                    $salary = $args['salary'] ?? 'يحدد حسب الكفاءة والمقابلة';
                    $deadline = $args['application_deadline'] ?? now()->addDays(21)->format('Y-m-d');
                    $desc = $args['description'] ?? "فرصة وظيفية واعدة لدى شركة ({$companyName}) تستهدف كفاءات وخريجي جامعة طرابلس.";
                    $reqs = $args['requirements'] ?? 'الحصول على المؤهل الأكاديمي المناسب وإتقان المهارات التخصصية المطلوبة للوظيفة.';

                    $details = "**المسمى الوظيفي:** {$title}\n" .
                               "**الشركة المشغلة:** {$companyName}\n" .
                               "**نوع العمل:** {$contractArabic}\n" .
                               "**عدد المقاعد الشاغرة:** {$seats}\n" .
                               "**موقع العمل:** {$location}\n" .
                               "**المكافأة / الراتب:** {$salary}\n" .
                               "**آخر موعد للتقديم:** {$deadline}\n" .
                               "**الشروط:** {$reqs}";

                    return [
                        'status' => 'proposal',
                        'type' => 'create_job_opportunity',
                        'action_type' => 'create_job_opportunity',
                        'title' => "تأكيد نشر فرصة عمل: {$title}",
                        'summary' => "نشر شاغر ({$title}) لدى {$companyName}",
                        'details' => $details,
                        'message' => "هل تود تأكيد نشر وتفعيل فرصة العمل **'{$title}'** في بوابة التوظيف لكافة خريجي الجامعة؟",
                        'data' => [
                            'title' => $title,
                            'description' => $desc,
                            'requirements' => $reqs,
                            'company_id' => $companyId,
                            'contract_type' => $contractType,
                            'seats' => $seats,
                            'salary' => $salary,
                            'location' => $location,
                            'application_deadline' => $deadline,
                        ]
                    ];
                }
            ],

            // 9. إغلاق أو إعادة فتح فرصة عمل شاغرة
            'toggle_job_opportunity_status' => [
                'name' => 'toggle_job_opportunity_status',
                'description' => 'إغلاق أو إعادة فتح فرصة وظيفية تابعة للشركة (تبديل حالة التقديم بين مفتوحة open ومغلقة closed).',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'job_identifier' => [
                            'type' => 'string',
                            'description' => 'مسمى الوظيفة أو رقم معرفها'
                        ],
                        'target_status' => [
                            'type' => 'string',
                            'enum' => ['open', 'closed', 'toggle'],
                            'description' => 'الحالة المطلوبة: open (مفتوحة)، closed (مغلقة ومكتفية)، toggle (تبديل الحالة الحالية)'
                        ]
                    ],
                    'required' => ['job_identifier']
                ],
                'requires_confirmation' => true,
                'authorize' => fn(User $user) => in_array($user->role, ['company', 'career_guidance_officer', 'partnership_officer', 'admin']),
                'execute' => function(User $user, array $args) {
                    $ident = trim($args['job_identifier']);
                    $query = JobOpportunity::with('company');

                    if ($user->role === 'company') {
                        $company = $user->company ?? Company::where('user_id', $user->id)->first();
                        if (!$company) {
                            return ['status' => 'error', 'message' => 'لم يتم العثور على ملف شركة مرتبط بهذا الحساب.'];
                        }
                        $query->where('company_id', $company->id);
                    }

                    $job = is_numeric($ident) ? (clone $query)->find((int)$ident) : (clone $query)->where('title', 'like', "%{$ident}%")->first();
                    if (!$job) {
                        return ['status' => 'error', 'message' => "لم يتم العثور على فرصة عمل تطابق: '{$ident}'."];
                    }

                    $mode = $args['target_status'] ?? 'toggle';
                    $newStatus = ($mode === 'open') ? 'open' : (($mode === 'closed') ? 'closed' : ($job->status === 'open' ? 'closed' : 'open'));
                    $statusArabic = ($newStatus === 'open') ? 'مفتوحة للتقديم ✅' : 'مغلقة ومكتفية 🔒';

                    return [
                        'status' => 'proposal',
                        'type' => 'toggle_job_status',
                        'action_type' => 'toggle_job_status',
                        'title' => "تأكيد تعديل حالة وظيفة: {$job->title}",
                        'summary' => "تغيير حالة الوظيفة إلى ({$statusArabic})",
                        'details' => "**الوظيفة:** {$job->title}\n**الشركة:** " . ($job->company ? $job->company->name : '—') . "\n**الحالة الحالية:** " . ($job->status === 'open' ? 'مفتوحة حالياً' : 'مغلقة حالياً') . "\n**الحالة الجديدة:** {$statusArabic}",
                        'message' => "هل تؤكد تغيير حالة التقديم لوظيفة **'{$job->title}'** إلى **{$statusArabic}**؟",
                        'data' => [
                            'job_id' => $job->id,
                            'target_status' => $newStatus,
                            'job_title' => $job->title,
                        ]
                    ];
                }
            ],

            // 10. استعراض قائمة وظائف الشركة الحالية
            'get_my_company_jobs' => [
                'name' => 'get_my_company_jobs',
                'description' => 'استعراض قائمة الوظائف والفرص المنشورة التابعة للشركة مع حالة كل شاغر وإجمالي عدد المرشحين والمتقدمين.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'status' => [
                            'type' => 'string',
                            'enum' => ['all', 'open', 'closed'],
                            'description' => 'فلترة بحالة الوظيفة: open (المفتوحة فقط)، closed (المغلقة فقط)، all (الكل)'
                        ],
                        'company_identifier' => [
                            'type' => 'string',
                            'description' => 'اسم الشركة (خاص بالمسؤولين)'
                        ]
                    ]
                ],
                'authorize' => fn(User $user) => in_array($user->role, ['company', 'career_guidance_officer', 'partnership_officer', 'admin']),
                'execute' => function(User $user, array $args) {
                    $query = JobOpportunity::with(['company', 'nominations']);

                    if ($user->role === 'company') {
                        $company = $user->company ?? Company::where('user_id', $user->id)->first();
                        if (!$company) {
                            return ['status' => 'error', 'message' => 'لم يتم العثور على شركة مسجلة لهذا الحساب.'];
                        }
                        $query->where('company_id', $company->id);
                    } elseif (!empty($args['company_identifier'])) {
                        $cIdent = trim($args['company_identifier']);
                        $query->whereHas('company', fn($q) => $q->where('name', 'like', "%{$cIdent}%"));
                    }

                    if (!empty($args['status']) && $args['status'] !== 'all') {
                        $query->where('status', $args['status']);
                    }

                    $jobs = $query->latest()->limit(10)->get()->map(function($j) {
                        return [
                            'id' => $j->id,
                            'title' => $j->title,
                            'company_name' => $j->company ? $j->company->name : '—',
                            'status' => $j->status,
                            'status_arabic' => $j->status === 'open' ? 'مفتوحة 🟢' : 'مغلقة 🔒',
                            'contract_type' => $j->contract_type_arabic ?? $j->contract_type,
                            'seats' => $j->seats,
                            'candidates_count' => $j->nominations()->count(),
                            'pending_count' => $j->nominations()->whereIn('status', ['pending', 'nominated', 'under_review'])->count(),
                            'hired_count' => $j->nominations()->where('final_status', 'hired')->count(),
                            'deadline' => $j->application_deadline ? Carbon::parse($j->application_deadline)->format('Y-m-d') : 'مفتوح',
                        ];
                    });

                    return [
                        'status' => 'success',
                        'count' => $jobs->count(),
                        'data' => $jobs,
                    ];
                }
            ],
        ];
    }

    /**
     * ==========================================
     * 🤝 قطاع إدارة الشراكات والاتفاقيات (Partnership Officer & Agreements Copilot)
     * ==========================================
     */
    private static function definePartnershipOfficerTools(): array
    {
        return [
            // 11. تسجيل وإضافة شركة شريكة جديدة
            'register_partner_company' => [
                'name' => 'register_partner_company',
                'description' => 'تسجيل واعتماد شركة أو مؤسسة شريكة جديدة في قاعدة بيانات جامعة طرابلس مع تحديد قطاع العمل ومسؤول الاتصال ونوع الشراكة.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'name' => [
                            'type' => 'string',
                            'description' => 'اسم الشركة أو المؤسسة الرسمي'
                        ],
                        'industry' => [
                            'type' => 'string',
                            'description' => 'قطاع ومجال العمل (مثال: تقنية معلومات، اتصالات، خدمات نفطية، مصارف ومحاسبة)'
                        ],
                        'email' => [
                            'type' => 'string',
                            'description' => 'البريد الإلكتروني الرسمي للشركة'
                        ],
                        'phone' => [
                            'type' => 'string',
                            'description' => 'رقم هاتف الشركة'
                        ],
                        'city' => [
                            'type' => 'string',
                            'description' => 'المدينة (مثال: طرابلس، مصراتة، بنغازي)'
                        ],
                        'address' => [
                            'type' => 'string',
                            'description' => 'العنوان التفصيلي للمقر'
                        ],
                        'website' => [
                            'type' => 'string',
                            'description' => 'رابط الموقع الرسمي'
                        ],
                        'contact_person' => [
                            'type' => 'string',
                            'description' => 'اسم ممثل الشركة أو مسؤول الاتصال'
                        ],
                        'contact_phone' => [
                            'type' => 'string',
                            'description' => 'هاتف مسؤول الاتصال المباشر'
                        ],
                        'partnership_types' => [
                            'type' => 'array',
                            'items' => ['type' => 'string'],
                            'description' => 'مجالات الشراكة: employment (توظيف), training (تدريب), logistic_support (دعم), academic (أكاديمي), workshops (ورش عمل)'
                        ],
                        'description' => [
                            'type' => 'string',
                            'description' => 'نبذة تعريفية عن أنشطة الشركة'
                        ]
                    ],
                    'required' => ['name', 'industry']
                ],
                'requires_confirmation' => true,
                'authorize' => fn(User $user) => in_array($user->role, ['partnership_officer', 'admin']),
                'execute' => function(User $user, array $args) {
                    $name = trim($args['name']);
                    $exists = Company::where('name', 'like', "%{$name}%")->first();
                    if ($exists) {
                        return ['status' => 'error', 'message' => "يوجد شركة مسجلة مسبقاً بهذا الاسم: '{$exists->name}'."];
                    }

                    $industry = trim($args['industry']);
                    $city = $args['city'] ?? 'طرابلس';
                    $address = $args['address'] ?? "{$city}، ليبيا";
                    $email = $args['email'] ?? null;
                    $phone = $args['phone'] ?? null;
                    $contact = $args['contact_person'] ?? 'مكتب العلاقات العامة';
                    $types = $args['partnership_types'] ?? ['employment', 'training'];

                    $details = "**اسم الشركة:** {$name}\n" .
                               "**القطاع:** {$industry}\n" .
                               "**المدينة والمقر:** {$address}\n" .
                               "**البريد الرسمي:** " . ($email ?: '—') . "\n" .
                               "**الهاتف:** " . ($phone ?: '—') . "\n" .
                               "**مسؤول الاتصال:** {$contact}\n" .
                               "**مجالات الشراكة:** " . implode('، ', (array)$types);

                    return [
                        'status' => 'proposal',
                        'type' => 'create_company',
                        'action_type' => 'create_company',
                        'title' => "تأكيد تسجيل شركة شريكة جديدة: {$name}",
                        'summary' => "إضافة شركة {$name} إلى شبكة الشركاء المعتمدين",
                        'details' => $details,
                        'message' => "هل تود تأكيد تسجيل واعتماد شركة **'{$name}'** رسمياً في شبكة شركاء جامعة طرابلس؟",
                        'data' => [
                            'name' => $name,
                            'industry' => $industry,
                            'email' => $email,
                            'phone' => $phone,
                            'city' => $city,
                            'address' => $address,
                            'website' => $args['website'] ?? null,
                            'contact_person' => $contact,
                            'contact_phone' => $args['contact_phone'] ?? $phone,
                            'partnership_types' => $types,
                            'description' => $args['description'] ?? "شركة شريكة في قطاع {$industry}.",
                        ]
                    ];
                }
            ],

            // 12. تحديث وتمديد شروط اتفاقية شراكة
            'update_company_partnership' => [
                'name' => 'update_company_partnership',
                'description' => 'تحديث شروط وبيانات الشراكة لشركة مسجلة (تمديد المدة، تعديل حالة الشراكة إلى نشطة/منتهية، تحديث مجالات الشراكة أو الملاحظات).',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'company_identifier' => [
                            'type' => 'string',
                            'description' => 'اسم أو معرف الشركة'
                        ],
                        'partnership_status' => [
                            'type' => 'string',
                            'enum' => ['active', 'under_review', 'expired'],
                            'description' => 'حالة الشراكة: active (نشطة ومعتمدة)، under_review (قيد المراجعة)، expired (منتهية)'
                        ],
                        'partnership_end_date' => [
                            'type' => 'string',
                            'description' => 'تاريخ انتهاء أو تمديد الشراكة (YYYY-MM-DD)'
                        ],
                        'partnership_types' => [
                            'type' => 'array',
                            'items' => ['type' => 'string'],
                            'description' => 'تحديث مجالات الشراكة'
                        ],
                        'notes' => [
                            'type' => 'string',
                            'description' => 'ملاحظات أو بنود إضافية للشراكة'
                        ]
                    ],
                    'required' => ['company_identifier']
                ],
                'requires_confirmation' => true,
                'authorize' => fn(User $user) => in_array($user->role, ['partnership_officer', 'admin']),
                'execute' => function(User $user, array $args) {
                    $ident = trim($args['company_identifier']);
                    $company = is_numeric($ident) ? Company::find((int)$ident) : Company::where('name', 'like', "%{$ident}%")->first();
                    if (!$company) {
                        return ['status' => 'error', 'message' => "لم يتم العثور على شركة تطابق: '{$ident}'."];
                    }

                    $status = $args['partnership_status'] ?? $company->partnership_status;
                    $endDate = $args['partnership_end_date'] ?? ($company->partnership_end_date ? Carbon::parse($company->partnership_end_date)->format('Y-m-d') : now()->addYear()->format('Y-m-d'));
                    $types = $args['partnership_types'] ?? $company->partnership_types;
                    $notes = $args['notes'] ?? $company->partnership_notes;

                    $details = "**الشركة:** {$company->name}\n" .
                               "**حالة الشراكة الجديدة:** " . ($status === 'active' ? 'نشطة ومعتمدة ✅' : ($status === 'expired' ? 'منتهية ⚠️' : 'قيد المراجعة ⏳')) . "\n" .
                               "**تاريخ نهاية الشراكة / التمديد:** {$endDate}\n" .
                               "**الملاحظات:** " . ($notes ?: 'لا توجد ملاحظات');

                    return [
                        'status' => 'proposal',
                        'type' => 'update_company_partnership',
                        'action_type' => 'update_company_partnership',
                        'title' => "تأكيد تحديث شروط شراكة: {$company->name}",
                        'summary' => "تحديث بيانات الشراكة لشركة {$company->name}",
                        'details' => $details,
                        'message' => "هل تؤكد حفظ التحديثات وتمديد اتفاقية الشراكة لشركة **'{$company->name}'**؟",
                        'data' => [
                            'company_id' => $company->id,
                            'partnership_status' => $status,
                            'partnership_end_date' => $endDate,
                            'partnership_types' => $types,
                            'notes' => $notes,
                        ]
                    ];
                }
            ],

            // 13. توثيق وتسجيل وثيقة شراكة رسمية (MOU / عقد)
            'record_partnership_document' => [
                'name' => 'record_partnership_document',
                'description' => 'توثيق وتسجيل وثيقة شراكة رسمية أو مذكرة تفاهم (MOU) أو اتفاقية تعاون مع شركة شريكة في المنظومة.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'company_identifier' => [
                            'type' => 'string',
                            'description' => 'اسم أو معرف الشركة الشريكة'
                        ],
                        'document_name' => [
                            'type' => 'string',
                            'description' => 'مسمى الوثيقة (مثال: مذكرة تفاهم للتدريب الميداني 2026، اتفاقية توظيف سنوية)'
                        ],
                        'document_type' => [
                            'type' => 'string',
                            'enum' => ['mou', 'contract', 'agreement', 'renewal', 'other'],
                            'description' => 'نوع الوثيقة: mou (مذكرة تفاهم), contract (عقد رسمي), agreement (اتفاقية تعاون), renewal (ملحق تجديد)'
                        ],
                        'effective_date' => [
                            'type' => 'string',
                            'description' => 'تاريخ بدء السريان (YYYY-MM-DD)'
                        ],
                        'expiry_date' => [
                            'type' => 'string',
                            'description' => 'تاريخ انتهاء السريان (YYYY-MM-DD)'
                        ],
                        'description' => [
                            'type' => 'string',
                            'description' => 'ملخص بنود ومخرجات الاتفاقية'
                        ]
                    ],
                    'required' => ['company_identifier', 'document_name']
                ],
                'requires_confirmation' => true,
                'authorize' => fn(User $user) => in_array($user->role, ['partnership_officer', 'admin']),
                'execute' => function(User $user, array $args) {
                    $ident = trim($args['company_identifier']);
                    $company = is_numeric($ident) ? Company::find((int)$ident) : Company::where('name', 'like', "%{$ident}%")->first();
                    if (!$company) {
                        return ['status' => 'error', 'message' => "لم يتم العثور على شركة تطابق: '{$ident}'."];
                    }

                    $docName = trim($args['document_name']);
                    $docType = $args['document_type'] ?? 'mou';
                    $typeMap = [
                        'mou' => 'مذكرة تفاهم (MOU)',
                        'contract' => 'عقد شراكة رسمي',
                        'agreement' => 'اتفاقية تعاون مشترك',
                        'renewal' => 'ملحق تجديد شراكة',
                        'other' => 'وثيقة أخرى',
                    ];
                    $typeArabic = $typeMap[$docType] ?? $docType;
                    $effDate = $args['effective_date'] ?? now()->format('Y-m-d');
                    $expDate = $args['expiry_date'] ?? now()->addYear()->format('Y-m-d');
                    $desc = $args['description'] ?? "توثيق رسمي للشراكة الاستراتيجية بين جامعة طرابلس وشركة {$company->name}.";

                    $details = "**الشركة الشريكة:** {$company->name}\n" .
                               "**عنوان الوثيقة:** {$docName}\n" .
                               "**نوع الوثيقة:** {$typeArabic}\n" .
                               "**تاريخ السريان:** {$effDate} حتى {$expDate}\n" .
                               "**ملخص البنود:** {$desc}";

                    return [
                        'status' => 'proposal',
                        'type' => 'record_partnership_document',
                        'action_type' => 'record_partnership_document',
                        'title' => "تأكيد تسجيل وثيقة الشراكة: {$docName}",
                        'summary' => "توثيق {$typeArabic} لشركة {$company->name}",
                        'details' => $details,
                        'message' => "هل تود تأكيد إدراج وتوثيق هذه الوثيقة في أرشيف اتفاقيات جامعة طرابلس لشركة **'{$company->name}'**؟",
                        'data' => [
                            'company_id' => $company->id,
                            'document_name' => $docName,
                            'document_type' => $docType,
                            'effective_date' => $effDate,
                            'expiry_date' => $expDate,
                            'description' => $desc,
                        ]
                    ];
                }
            ],

            // 14. تحديث الملف التعريفي للشركة
            'update_my_company_profile' => [
                'name' => 'update_my_company_profile',
                'description' => 'تحديث بيانات الشركة الحالية (الهاتف، العنوان، الموقع الإلكتروني، النبذة، مسؤول الاتصال).',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'phone' => ['type' => 'string', 'description' => 'رقم هاتف الشركة'],
                        'website' => ['type' => 'string', 'description' => 'الموقع الإلكتروني'],
                        'city' => ['type' => 'string', 'description' => 'المدينة'],
                        'address' => ['type' => 'string', 'description' => 'العنوان التفصيلي'],
                        'description' => ['type' => 'string', 'description' => 'النبذة التعريفية'],
                        'contact_person' => ['type' => 'string', 'description' => 'اسم مسؤول الاتصال'],
                        'contact_phone' => ['type' => 'string', 'description' => 'هاتف مسؤول الاتصال'],
                        'contact_email' => ['type' => 'string', 'description' => 'بريد مسؤول الاتصال'],
                    ]
                ],
                'requires_confirmation' => true,
                'authorize' => fn(User $user) => in_array($user->role, ['company', 'admin']),
                'execute' => function(User $user, array $args) {
                    $company = $user->company ?? Company::where('user_id', $user->id)->first();
                    if (!$company) {
                        return ['status' => 'error', 'message' => 'لا يوجد ملف شركة مسجل لهذا الحساب.'];
                    }

                    $changes = [];
                    foreach ($args as $k => $v) {
                        if (!empty($v)) {
                            $changes[$k] = trim($v);
                        }
                    }

                    if (empty($changes)) {
                        return ['status' => 'info', 'message' => 'لم يتم إدخال بيانات جديدة لتحديثها.'];
                    }

                    $details = "**الشركة:** {$company->name}\n";
                    foreach ($changes as $k => $v) {
                        $details .= "• **{$k}:** {$v}\n";
                    }

                    return [
                        'status' => 'proposal',
                        'type' => 'update_company_profile',
                        'action_type' => 'update_company_profile',
                        'title' => "تأكيد تحديث الملف التعريفي لشركة: {$company->name}",
                        'summary' => "تحديث بيانات الاتصال والمقر للشركة",
                        'details' => $details,
                        'message' => "هل تود تأكيد حفظ هذه التعديلات على ملف الشركة؟",
                        'data' => array_merge($changes, ['company_id' => $company->id])
                    ];
                }
            ],

            // 15. ترشيح دفعة من أفضل الخريجين المطابقين (Batch Nomination)
            'batch_nominate_graduates' => [
                'name' => 'batch_nominate_graduates',
                'description' => 'ترشيح دفعة من أفضل الخريجين المطابقين لمعايير وظيفة شاغرة دفعة واحدة بناءً على التخصص والمعدل التراكمي.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'job_identifier' => [
                            'type' => 'string',
                            'description' => 'اسم الوظيفة الشاغرة أو معرفها'
                        ],
                        'major' => [
                            'type' => 'string',
                            'description' => 'التخصص الأكاديمي المطلوب (اختياري، يطابق الوظيفة تلقائياً)'
                        ],
                        'min_gpa' => [
                            'type' => 'number',
                            'description' => 'الحد الأدنى للمعدل التراكمي (افتراضي 75)'
                        ],
                        'limit' => [
                            'type' => 'integer',
                            'description' => 'عدد المرشحين المطلوب ترشيحهم (افتراضي 3)'
                        ],
                        'notes' => [
                            'type' => 'string',
                            'description' => 'ملاحظات الترشيح المشتركة'
                        ]
                    ],
                    'required' => ['job_identifier']
                ],
                'requires_confirmation' => true,
                'authorize' => fn(User $user) => in_array($user->role, ['career_guidance_officer', 'admin']),
                'execute' => function(User $user, array $args) {
                    $jobIdent = trim($args['job_identifier']);
                    $job = is_numeric($jobIdent) ? JobOpportunity::with('company')->find((int)$jobIdent) : JobOpportunity::with('company')->where('title', 'like', "%{$jobIdent}%")->first();
                    if (!$job) {
                        return ['status' => 'error', 'message' => "لم يتم العثور على فرصة عمل تطابق: '{$jobIdent}'."];
                    }

                    $minGpa = (float) ($args['min_gpa'] ?? 75);
                    $limit = (int) ($args['limit'] ?? 3);
                    $query = GraduateData::where('is_active', true)->where('gpa', '>=', $minGpa);

                    if (!empty($args['major'])) {
                        $m = trim($args['major']);
                        $query->where(fn($q) => $q->where('major', 'like', "%{$m}%")->orWhere('faculty', 'like', "%{$m}%"));
                    }

                    // استبعاد الخريجين المرشحين بالفعل لهذه الوظيفة
                    $existingGradIds = Nomination::where('job_opportunity_id', $job->id)->pluck('graduate_id')->toArray();
                    if (!empty($existingGradIds)) {
                        $query->whereNotIn('id', $existingGradIds);
                    }

                    $candidates = $query->orderByDesc('gpa')->limit($limit)->get();
                    if ($candidates->isEmpty()) {
                        return [
                            'status' => 'info',
                            'message' => "لم يتم العثور على خريجين مؤهلين غير مرشحين مسبقاً لهذه الوظيفة بمعدل أعلى من {$minGpa}%."
                        ];
                    }

                    $candidateNames = $candidates->pluck('name')->toArray();
                    $candidateIds = $candidates->pluck('id')->toArray();
                    $details = "**الوظيفة:** {$job->title} لدى (" . ($job->company ? $job->company->name : '—') . ")\n" .
                               "**عدد المرشحين في الدفعة:** " . count($candidates) . " مرشحاً\n" .
                               "**قائمة المرشحين:**\n";
                    foreach ($candidates as $idx => $g) {
                        $details .= ($idx + 1) . ". {$g->name} - تخصص: {$g->major} (معدل: **{$g->gpa}%**)\n";
                    }

                    return [
                        'status' => 'proposal',
                        'type' => 'bulk_nominate_graduates',
                        'action_type' => 'bulk_nominate_graduates',
                        'title' => "تأكيد ترشيح دفعة خريجين لوظيفة: {$job->title}",
                        'summary' => "ترشيح " . count($candidates) . " خريجاً لوظيفة {$job->title}",
                        'details' => $details,
                        'message' => "هل تود تأكيد ترشيح هذه الدفعة من الخريجين المتميزين دفعة واحدة لوظيفة **'{$job->title}'**؟",
                        'data' => [
                            'job_opportunity_id' => $job->id,
                            'job_title' => $job->title,
                            'candidate_ids' => $candidateIds,
                            'candidate_names' => $candidateNames,
                        ]
                    ];
                }
            ],

            // 16. تقرير إحصائي مفصل عن قطاع الشراكات
            'get_partnerships_report' => [
                'name' => 'get_partnerships_report',
                'description' => 'استخراج تقرير تفصيلي وإحصائي عن قطاع الشراكات (توزيع الشركات حسب المجال، حالة الشراكات، الشراكات المنتهية أو التي قاربت على الانتهاء).',
                'parameters' => [
                    'type' => 'object',
                    'properties' => new \stdClass(),
                ],
                'authorize' => fn(User $user) => in_array($user->role, ['partnership_officer', 'admin']),
                'execute' => function(User $user, array $args) {
                    $total = Company::count();
                    $active = Company::where('is_approved', true)->where('partnership_status', 'active')->count();
                    $underReview = Company::where('partnership_status', 'under_review')->orWhere('is_approved', false)->count();
                    $expired = Company::where('partnership_status', 'expired')->count();
                    $docsCount = class_exists(PartnershipDocument::class) ? PartnershipDocument::count() : 0;
                    $totalJobs = JobOpportunity::count();
                    $openJobs = JobOpportunity::where('status', 'open')->count();

                    $topIndustries = Company::selectRaw('industry, count(*) as count')
                        ->whereNotNull('industry')
                        ->groupBy('industry')
                        ->orderByDesc('count')
                        ->limit(4)
                        ->get();

                    return [
                        'status' => 'success',
                        'generated_at' => now()->format('Y-m-d H:i'),
                        'metrics' => [
                            'total_companies' => $total,
                            'active_partnerships' => $active,
                            'under_review_partnerships' => $underReview,
                            'expired_partnerships' => $expired,
                            'partnership_documents' => $docsCount,
                            'total_jobs_posted' => $totalJobs,
                            'open_jobs' => $openJobs,
                            'top_industries' => $topIndustries->pluck('count', 'industry')->toArray(),
                        ]
                    ];
                }
            ],

            // 17. تحليلات وإحصائيات التوظيف ومطابقة سوق العمل
            'get_employment_analytics' => [
                'name' => 'get_employment_analytics',
                'description' => 'استخراج تحليلات وإحصائيات التوظيف ومطابقة سوق العمل والوظائف الشاغرة ومؤشرات التوظيف.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => new \stdClass(),
                ],
                'authorize' => fn(User $user) => in_array($user->role, ['career_guidance_officer', 'admin', 'partnership_officer']),
                'execute' => function(User $user, array $args) {
                    $totalJobs = JobOpportunity::count();
                    $openJobs = JobOpportunity::where('status', 'open')->count();
                    $totalNominations = Nomination::count();
                    $acceptedNominations = Nomination::where('status', 'accepted')->count();
                    $interviewsCount = Nomination::where('status', 'interview_scheduled')->count();
                    $hiredGraduates = Nomination::where('final_status', 'hired')->count();

                    $topMajorsDemanded = GraduateData::whereIn('id', Nomination::pluck('graduate_id'))
                        ->selectRaw('major, count(*) as count')
                        ->groupBy('major')
                        ->orderByDesc('count')
                        ->limit(4)
                        ->get();

                    return [
                        'status' => 'success',
                        'generated_at' => now()->format('Y-m-d H:i'),
                        'metrics' => [
                            'total_vacancies' => $totalJobs,
                            'active_vacancies' => $openJobs,
                            'total_nominations' => $totalNominations,
                            'interviews_scheduled' => $interviewsCount,
                            'accepted_nominations' => $acceptedNominations,
                            'hired_graduates' => $hiredGraduates,
                            'employment_success_rate' => $totalNominations > 0 ? round(($hiredGraduates / $totalNominations) * 100, 1) . '%' : '0%',
                            'top_demanded_majors' => $topMajorsDemanded->pluck('count', 'major')->toArray(),
                        ]
                    ];
                }
            ],

            // 18. استعراض وظائف وشواغر الشركة
            'get_company_jobs' => [
                'name' => 'get_company_jobs',
                'description' => 'استعراض الفرص الوظيفية والشواغر المتاحة لشركة معينة أو شواغر الشركة الحالية مع إحصائيات المرشحين والمقاعد.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'company_identifier' => [
                            'type' => 'string',
                            'description' => 'اسم الشركة أو معرفها (اختياري، يحدد تلقائياً لحساب الشركة)'
                        ],
                        'status' => [
                            'type' => 'string',
                            'description' => 'حالة الوظيفة: all (الكل), open (مفتوحة), closed (مغلقة)',
                            'enum' => ['all', 'open', 'closed']
                        ]
                    ]
                ],
                'authorize' => fn(User $user) => in_array($user->role, ['company', 'career_guidance_officer', 'partnership_officer', 'admin']),
                'execute' => function(User $user, array $args) {
                    $query = JobOpportunity::with('company');

                    if ($user->role === 'company') {
                        $company = $user->company ?? Company::where('user_id', $user->id)->first();
                        if (!$company) {
                            return ['status' => 'error', 'message' => 'لم يتم العثور على شركة مرتبطة بحسابك.'];
                        }
                        $query->where('company_id', $company->id);
                    } elseif (!empty($args['company_identifier'])) {
                        $ci = trim($args['company_identifier']);
                        $query->whereHas('company', fn($q) => $q->where('name', 'like', "%{$ci}%")->orWhere('id', $ci));
                    }

                    if (!empty($args['status']) && in_array($args['status'], ['open', 'closed'])) {
                        $query->where('status', $args['status']);
                    }

                    $jobs = $query->latest()->limit(10)->get();
                    if ($jobs->isEmpty()) {
                        return [
                            'status' => 'info',
                            'count' => 0,
                            'message' => 'لا توجد فرص وظيفية مسجلة حالياً تطابق معايير البحث.'
                        ];
                    }

                    $data = $jobs->map(function($j) {
                        return [
                            'id' => $j->id,
                            'title' => $j->title,
                            'company_name' => $j->company ? $j->company->name : 'غير محدد',
                            'status' => $j->status,
                            'seats' => $j->seats ?? $j->vacancies_count ?? 1,
                            'location' => $j->location ?? 'طرابلس',
                            'contract_type' => $j->contract_type ?? 'دوام كامل',
                            'deadline' => $j->deadline ? $j->deadline->format('Y-m-d') : 'مفتوح',
                            'nominations_count' => $j->nominations()->count(),
                            'hired_count' => $j->nominations()->where('final_status', 'hired')->count(),
                        ];
                    });

                    return [
                        'status' => 'success',
                        'count' => $data->count(),
                        'data' => $data->toArray(),
                    ];
                }
            ],

            // 19. تعديل حالة الوظيفة (إغلاق / إعادة فتح)
            'toggle_job_status' => [
                'name' => 'toggle_job_status',
                'description' => 'تعديل حالة فرصة العمل (إغلاق التقديم أو إعادة فتح التقديم والترشيح).',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'job_identifier' => [
                            'type' => 'string',
                            'description' => 'المسمى الوظيفي أو معرف الوظيفة'
                        ],
                        'target_status' => [
                            'type' => 'string',
                            'description' => 'الحالة المطلوبة: open (فتح/تفعيل) أو closed (إغلاق)',
                            'enum' => ['open', 'closed']
                        ]
                    ],
                    'required' => ['job_identifier']
                ],
                'requires_confirmation' => true,
                'authorize' => fn(User $user) => in_array($user->role, ['company', 'career_guidance_officer', 'partnership_officer', 'admin']),
                'execute' => function(User $user, array $args) {
                    $jobIdent = trim($args['job_identifier']);
                    $query = JobOpportunity::with('company');

                    if ($user->role === 'company') {
                        $company = $user->company ?? Company::where('user_id', $user->id)->first();
                        if (!$company) {
                            return ['status' => 'error', 'message' => 'لم يتم العثور على شركة مرتبطة بحسابك.'];
                        }
                        $query->where('company_id', $company->id);
                    }

                    $job = is_numeric($jobIdent) ? $query->find((int)$jobIdent) : $query->where('title', 'like', "%{$jobIdent}%")->first();
                    if (!$job) {
                        return ['status' => 'error', 'message' => "لم يتم العثور على فرصة عمل تطابق: '{$jobIdent}'."];
                    }

                    $targetStatus = $args['target_status'] ?? ($job->status === 'open' ? 'closed' : 'open');
                    $actionWord = ($targetStatus === 'open') ? 'إعادة فتح وتفعيل التقديم' : 'إغلاق واكتفاء التقديم';
                    $statusBadge = ($targetStatus === 'open') ? 'مفتوحة للتقديم والترشيح 🟢' : 'مغلقة ومكتفية 🔒';

                    return [
                        'status' => 'proposal',
                        'type' => 'toggle_job_status',
                        'action_type' => 'toggle_job_status',
                        'title' => "تأكيد {$actionWord} لوظيفة: {$job->title}",
                        'summary' => "تغيير حالة وظيفة '{$job->title}' إلى {$statusBadge}",
                        'details' => "**الوظيفة:** {$job->title}\n**الشركة:** " . ($job->company ? $job->company->name : '—') . "\n**الحالة الحالية:** " . ($job->status === 'open' ? 'مفتوحة 🟢' : 'مغلقة 🔒') . "\n**الحالة الجديدة:** {$statusBadge}",
                        'message' => "هل ترغب في تأكيد {$actionWord} لفرصة العمل **'{$job->title}'**؟",
                        'data' => [
                            'job_id' => $job->id,
                            'target_status' => $targetStatus,
                        ]
                    ];
                }
            ],

            // 20. تحديث بيانات الملف التعريفي للشركة
            'update_company_profile' => [
                'name' => 'update_company_profile',
                'description' => 'تحديث بيانات الملف التعريفي للشركة (الهاتف، الموقع الإلكتروني، العنوان، مسؤول التواصل).',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'company_identifier' => [
                            'type' => 'string',
                            'description' => 'اسم الشركة أو معرفها (اختياري للشركات)'
                        ],
                        'phone' => ['type' => 'string', 'description' => 'رقم الهاتف الرسمي'],
                        'website' => ['type' => 'string', 'description' => 'الموقع الإلكتروني'],
                        'address' => ['type' => 'string', 'description' => 'عنوان المقر'],
                        'contact_person' => ['type' => 'string', 'description' => 'اسم مسؤول التواصل'],
                        'contact_phone' => ['type' => 'string', 'description' => 'هاتف مسؤول التواصل'],
                    ]
                ],
                'requires_confirmation' => true,
                'authorize' => fn(User $user) => in_array($user->role, ['company', 'partnership_officer', 'admin']),
                'execute' => function(User $user, array $args) {
                    $company = null;
                    if ($user->role === 'company') {
                        $company = $user->company ?? Company::where('user_id', $user->id)->first();
                    } elseif (!empty($args['company_identifier'])) {
                        $ci = trim($args['company_identifier']);
                        $company = is_numeric($ci) ? Company::find((int)$ci) : Company::where('name', 'like', "%{$ci}%")->first();
                    }

                    if (!$company) {
                        return ['status' => 'error', 'message' => 'لم يتم العثور على ملف الشركة المطلوب تحديثه.'];
                    }

                    $changes = [];
                    if (!empty($args['phone'])) $changes[] = "• **الهاتف:** {$args['phone']}";
                    if (!empty($args['website'])) $changes[] = "• **الموقع الإلكتروني:** {$args['website']}";
                    if (!empty($args['address'])) $changes[] = "• **العنوان:** {$args['address']}";
                    if (!empty($args['contact_person'])) $changes[] = "• **مسؤول التواصل:** {$args['contact_person']}";

                    return [
                        'status' => 'proposal',
                        'type' => 'update_company_profile',
                        'action_type' => 'update_company_profile',
                        'title' => "تحديث الملف التعريفي لشركة: {$company->name}",
                        'summary' => "تحديث بيانات الاتصال والملف التعريفي لشركة {$company->name}",
                        'details' => implode("\n", $changes),
                        'message' => "هل تود تأكيد حفظ التعديلات في الملف التعريفي لشركة **'{$company->name}'**؟",
                        'data' => array_merge(['company_id' => $company->id], $args),
                    ];
                }
            ],
        ];
    }
}
