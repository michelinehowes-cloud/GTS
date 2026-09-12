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
        ];
    }
}
