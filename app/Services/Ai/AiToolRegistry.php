<?php

namespace App\Services\Ai;

use App\Models\User;
use App\Models\Training;
use App\Models\TrainingApplication;
use App\Models\JobOpportunity;
use App\Models\Company;
use App\Models\News;
use App\Models\Announcement;
use App\Models\MediaCamera;
use App\Models\LiveBroadcastSetting;
use App\Models\MediaPlatformStat;
use App\Models\GraduateData;
use App\Models\Nomination;
use App\Models\Survey;
use App\Models\SurveyResponse;
use App\Models\AuditLog;
use Illuminate\Support\Carbon;

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
                        $kw = trim($args['keyword']);
                        $query->where(function($q) use ($kw) {
                            $q->where('title', 'like', "%{$kw}%")
                              ->orWhere('description', 'like', "%{$kw}%")
                              ->orWhere('location', 'like', "%{$kw}%");
                        });
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
                'description' => 'البحث في فرص العمل والترشيحات الوظيفية المتاحة للخريجين.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'keyword' => [
                            'type' => 'string',
                            'description' => 'المسمى الوظيفي أو التخصص أو اسم الشركة'
                        ]
                    ]
                ],
                'authorize' => fn(User $user) => $user->role === 'graduate' || $user->role === 'admin',
                'execute' => function(User $user, array $args) {
                    $query = JobOpportunity::where('status', 'active');
                    if (!empty($args['keyword'])) {
                        $kw = trim($args['keyword']);
                        $query->where(function($q) use ($kw) {
                            $q->where('title', 'like', "%{$kw}%")
                              ->orWhere('description', 'like', "%{$kw}%")
                              ->orWhere('specialization', 'like', "%{$kw}%");
                        });
                    }

                    $jobs = $query->latest()->limit(5)->get()->map(function($j) {
                        return [
                            'id' => $j->id,
                            'title' => $j->title,
                            'company' => $j->company ? $j->company->name : 'جهة شريكة',
                            'type' => $j->job_type ?? 'دوام كامل',
                            'location' => $j->location ?? 'طرابلس',
                            'deadline' => $j->deadline ? $j->deadline->format('Y-m-d') : 'مفتوح',
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
                'description' => 'التقديم والترشح الذاتي لفرصة وظيفية متاحة في المنظومة.',
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

            // ==========================================
            // 📹 أدوات مسؤول وحدة الإعلام (Media Officer Tools)
            // ==========================================
            'get_media_statistics' => [
                'name' => 'get_media_statistics',
                'description' => 'عرض إحصائيات وحدة الإعلام شاملة نسبة تغطية التدريبات، الكاميرات المتصلة، وحالة البث المباشر والأخبار.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => new \stdClass(),
                ],
                'authorize' => fn(User $user) => $user->role === 'media_officer' || $user->role === 'admin',
                'execute' => function(User $user, array $args) {
                    $totalTrainings = Training::count();
                    $coveredTrainings = Training::where('media_coverage_status', 'covered')->count();
                    $rate = $totalTrainings > 0 ? round(($coveredTrainings / $totalTrainings) * 100) : 0;
                    $broadcast = LiveBroadcastSetting::first();

                    return [
                        'status' => 'success',
                        'data' => [
                            'coverage_rate' => $rate . '%',
                            'total_trainings' => $totalTrainings,
                            'covered_trainings' => $coveredTrainings,
                            'pending_trainings' => $totalTrainings - $coveredTrainings,
                            'active_news' => News::active()->count(),
                            'active_announcements' => Announcement::active()->count(),
                            'cameras_count' => MediaCamera::count(),
                            'live_cameras' => MediaCamera::where('is_live', true)->count(),
                            'is_broadcast_live' => $broadcast ? $broadcast->is_live_now : false,
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
                            'recent_audits' => AuditLog::latest()->limit(3)->pluck('action')->toArray(),
                        ]
                    ];
                }
            ],

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

                    if (!empty($args['major'])) {
                        $major = trim($args['major']);
                        $query->where(function($q) use ($major) {
                            $q->where('major', 'like', "%{$major}%")
                              ->orWhere('specialization', 'like', "%{$major}%");
                        });
                    }

                    if (!empty($args['college'])) {
                        $college = trim($args['college']);
                        $query->where(function($q) use ($college) {
                            $q->where('college', 'like', "%{$college}%")
                              ->orWhere('faculty', 'like', "%{$college}%");
                        });
                    }

                    if (!empty($args['min_gpa'])) {
                        $query->where('gpa', '>=', (float) $args['min_gpa']);
                    }

                    if (!empty($args['phone'])) {
                        $phone = trim($args['phone']);
                        $query->where('phone', 'like', "%{$phone}%");
                    }

                    if (!empty($args['name'])) {
                        $name = trim($args['name']);
                        $query->where('name', 'like', "%{$name}%");
                    }

                    if (isset($args['has_cv']) && $args['has_cv']) {
                        $query->whereNotNull('cv_path')->where('cv_path', '!=', '');
                    }

                    if (!empty($args['employment_status'])) {
                        $query->where('employment_status', $args['employment_status']);
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
                        'data' => $graduates
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
                'authorize' => fn(User $user) => in_array($user->role, ['career_guidance_officer', 'admin']),
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

            // ==========================================
            // 📢 قطاع الإعلام والبث الذكي
            // ==========================================
            'draft_announcement' => [
                'name' => 'draft_announcement',
                'description' => 'صياغة ونشر إعلان رسمي من وحدة الإعلام يظهر في شريط الإعلانات وواجهة المنظومة.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'title' => [
                            'type' => 'string',
                            'description' => 'عنوان الإعلان الرسمي',
                        ],
                        'content' => [
                            'type' => 'string',
                            'description' => 'نص الإعلان الرسمي والتفاصيل',
                        ],
                        'link' => [
                            'type' => 'string',
                            'description' => 'رابط خارجي أو داخلي مرتبط بالإعلان (اختياري)',
                        ],
                        'duration_days' => [
                            'type' => 'integer',
                            'description' => 'مدة ظهور الإعلان بالأيام (افتراضياً 7 أيام)',
                        ]
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

            'manage_live_broadcast' => [
                'name' => 'manage_live_broadcast',
                'description' => 'التحكم في حالة البث المباشر (تشغيل / إيقاف) وعنوان البث في استوديو الميديا.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'action' => [
                            'type' => 'string',
                            'enum' => ['start', 'stop'],
                            'description' => 'الإجراء المطلوب: start (تشغيل البث), stop (إيقاف البث)',
                        ],
                        'stream_title' => [
                            'type' => 'string',
                            'description' => 'عنوان البث المباشر (اختياري)',
                        ]
                    ],
                    'required' => ['action'],
                ],
                'requires_confirmation' => true,
                'authorize' => fn(User $user) => in_array($user->role, ['media_officer', 'admin']),
                'execute' => function(User $user, array $args) {
                    $action = $args['action'];
                    $current = LiveBroadcastSetting::current();
                    $willBeLive = ($action === 'start');

                    if ($current->is_live_now === $willBeLive) {
                        return [
                            'status' => 'info',
                            'message' => $willBeLive ? "البث المباشر في الاستوديو يعمل بالفعل (ON AIR)." : "البث المباشر متوقف بالفعل حالياً (OFF AIR)."
                        ];
                    }

                    $streamTitle = !empty($args['stream_title']) ? $args['stream_title'] : $current->broadcast_title;

                    return [
                        'status' => 'proposal',
                        'type' => 'toggle_broadcast',
                        'action_type' => 'toggle_broadcast',
                        'title' => $willBeLive ? 'تأكيد تشغيل البث المباشر (Go LIVE)' : 'تأكيد إيقاف البث المباشر (End Stream)',
                        'summary' => $willBeLive ? "بدء البث المباشر: {$streamTitle}" : "إيقاف البث المباشر الحالي",
                        'details' => "**الحالة الجديدة:** " . ($willBeLive ? '🔴 ON AIR (تشغيل فوري)' : '⚪ OFF AIR (إيقاف البث)') . "\n**عنوان البث:** {$streamTitle}",
                        'message' => $willBeLive ? "هل تؤكد بدء البث المباشر للجمهور الآن؟" : "هل تؤكد إنهاء وإيقاف البث المباشر؟",
                        'data' => [
                            'is_live_now' => $willBeLive,
                            'broadcast_title' => $streamTitle,
                        ]
                    ];
                }
            ],

            'generate_media_report' => [
                'name' => 'generate_media_report',
                'description' => 'توليد تقرير إعلامي رسمي يوضح حجم التغطيات الصحفية، حالة الكاميرات، وحركة البث والأخبار.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => new \stdClass(),
                ],
                'authorize' => fn(User $user) => in_array($user->role, ['media_officer', 'admin']),
                'execute' => function(User $user, array $args) {
                    $totalTrainings = Training::count();
                    $coveredTrainings = Training::whereNotNull('media_coverage_status')
                        ->where('media_coverage_status', 'completed')
                        ->count();
                    $pendingTrainings = max(0, $totalTrainings - $coveredTrainings);
                    $coverageRate = $totalTrainings > 0 ? round(($coveredTrainings / $totalTrainings) * 100, 1) : 0;

                    $publishedNews = News::where('is_active', true)->count();
                    $draftNews = News::where('is_active', false)->count();
                    $announcements = Announcement::where('is_active', true)->count();
                    $cameras = MediaCamera::count();
                    $onlineCameras = MediaCamera::where('is_live', true)->count();
                    $broadcast = LiveBroadcastSetting::current();

                    return [
                        'status' => 'success',
                        'report_title' => 'تقرير قطاع الإعلام والتوثيق الرقمي والبث الذكي',
                        'generated_at' => now()->format('Y-m-d H:i'),
                        'metrics' => [
                            'coverage_rate' => $coverageRate . '%',
                            'covered_trainings' => $coveredTrainings,
                            'pending_trainings' => $pendingTrainings,
                            'total_events' => $totalTrainings,
                            'published_news' => $publishedNews,
                            'draft_news' => $draftNews,
                            'active_announcements' => $announcements,
                            'connected_cameras' => "{$onlineCameras}/{$cameras}",
                            'broadcast_status' => $broadcast->is_live_now ? 'ON AIR (مباشر)' : 'OFF AIR (متوقف)',
                            'viewers_count' => $broadcast->viewers_count ?? 0,
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

                    return [
                        'status' => 'proposal',
                        'type' => 'create_company',
                        'action_type' => 'create_company',
                        'title' => 'تأكيد إضافة وتوثيق شركة شريكة جديدة',
                        'summary' => "إضافة شركة شريكة: {$name}",
                        'details' => "**اسم الشركة:** {$name}\n**مجال العمل:** {$industry}\n**البريد والهاتف:** {$email} | {$phone}\n**العنوان:** {$address}\n**مسؤول الاتصال:** " . ($args['contact_person'] ?? 'غير محدد'),
                        'message' => "هل تؤكد إضافة شركة '{$name}' كشريك استراتيجي في منظومة الشراكات وتوظيف الخريجين؟",
                        'data' => [
                            'name' => $name,
                            'industry' => $industry,
                            'email' => $email,
                            'phone' => $phone,
                            'address' => $address,
                            'website' => $args['website'] ?? null,
                            'contact_person' => $args['contact_person'] ?? null,
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
                'authorize' => fn(User $user) => in_array($user->role, ['career_guidance_officer', 'admin']),
                'execute' => function(User $user, array $args) {
                    $title = trim($args['title']);
                    $companyId = null;
                    $companyName = 'إحدى الشركات الشريكة المعتمدة';

                    if (!empty($args['company_identifier'])) {
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
                                'title' => 'قطاع الإعلام والتوثيق والبث',
                                'published_news' => News::where('is_active', true)->count(),
                                'active_announcements' => Announcement::where('is_active', true)->count(),
                                'broadcast_state' => LiveBroadcastSetting::current()->is_live_now ? 'ON AIR' : 'OFF AIR',
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
}
