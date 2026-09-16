@extends('layouts.app')

@section('title', 'ملف الشركة: ' . $company->name)

@section('page-title', 'ملف الشركة')

@section('content')
<div class="container-fluid">
    @include('components.breadcrumbs', [
        'items' => [
            ['label' => 'الرئيسية', 'url' => route('admin.dashboard')],
            ['label' => 'الشركات', 'url' => route('admin.companies')],
            ['label' => $company->name, 'active' => true],
        ]
    ])

    @php
        $isApproved = $company->is_approved && $company->partnership_status === 'active';
        $typesLabels = [
            'employment'      => ['label' => 'توظيف الخريجين',       'icon' => 'fa-user-tie',          'color' => '#0284c7', 'bg' => '#e0f2fe'],
            'training'        => ['label' => 'تدريب ميداني وتأهيل',  'icon' => 'fa-graduation-cap',     'color' => '#059669', 'bg' => '#d1fae5'],
            'logistic_support'=> ['label' => 'دعم لوجستي ورعاية',   'icon' => 'fa-award',             'color' => '#d97706', 'bg' => '#fef3c7'],
            'academic'        => ['label' => 'تعاون أكاديمي وبحثي', 'icon' => 'fa-flask',             'color' => '#7c3aed', 'bg' => '#ede9fe'],
            'workshops'       => ['label' => 'ورش عمل وندوات',       'icon' => 'fa-chalkboard-teacher', 'color' => '#db2777', 'bg' => '#fce7f3'],
        ];
        $companyTypes = $company->partnership_types ?? ($company->partnership_type ? [$company->partnership_type] : []);
        $totalJobs   = $company->jobOpportunities()->count();
        $openJobs    = $company->jobOpportunities()->where('status', 'open')->count();
    @endphp

    {{-- =================== رأس الصفحة - بطاقة الهوية =================== --}}
    {{-- =================== رأس الصفحة - بطاقة الهوية =================== --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="p-4" style="background:linear-gradient(135deg,#0ea5e9 0%,#6366f1 100%);">
            <div class="d-flex align-items-center gap-4 flex-wrap">
                {{-- شعار الشركة --}}
                <div class="flex-shrink-0">
                    @if($company->logo_path)
                        <img src="{{ Storage::url($company->logo_path) }}" alt="{{ $company->name }}"
                             style="width:84px;height:84px;border-radius:16px;object-fit:contain;background:#fff;padding:6px;border:3px solid rgba(255,255,255,.5);box-shadow:0 4px 12px rgba(0,0,0,0.08);">
                    @else
                        <div style="width:84px;height:84px;border-radius:16px;background:rgba(255,255,255,.2);border:3px solid rgba(255,255,255,.5);display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(0,0,0,0.08);">
                            <span style="font-size:2.2rem;font-weight:800;color:#fff;">{{ mb_substr($company->name, 0, 1) }}</span>
                        </div>
                    @endif
                </div>
                <div class="text-white">
                    <h2 class="fw-bold mb-1 fs-3">{{ $company->name }}</h2>
                    <div class="d-flex align-items-center gap-3 flex-wrap" style="opacity:.95;font-size:.9rem;">
                        @if($company->industry)
                            <span><i class="fas fa-industry me-1"></i>{{ $company->industry }}</span>
                        @endif
                        @if($company->address)
                            <span><i class="fas fa-map-marker-alt me-1"></i>{{ $company->city ? $company->city . ' - ' : '' }}{{ $company->address }}</span>
                        @endif
                    </div>
                </div>
                <div class="ms-auto d-flex gap-2 flex-wrap">
                    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'partnership_officer')
                    <a href="{{ route('admin.companies.edit', $company->id) }}" class="btn btn-sm btn-light text-primary fw-bold rounded-pill shadow-sm px-3">
                        <i class="fas fa-edit me-1"></i>تعديل
                    </a>
                    <form action="{{ route('admin.companies.toggle-approval', $company->id) }}" method="POST" class="m-0">
                        @csrf
                        @if($isApproved)
                            <button type="submit" class="btn btn-sm btn-warning rounded-pill fw-bold shadow-sm px-3">
                                <i class="fas fa-pause me-1"></i>تعليق الاعتماد
                            </button>
                        @else
                            <button type="submit" class="btn btn-sm btn-success rounded-pill fw-bold shadow-sm px-3">
                                <i class="fas fa-check me-1"></i>اعتماد الشركة
                            </button>
                        @endif
                    </form>
                    @endif
                </div>
            </div>
        </div>
        {{-- شريط حالة الشراكة والأنواع --}}
        <div class="bg-white px-4 py-3 border-top">
            <div class="d-flex gap-2 align-items-center flex-wrap">
                @if($isApproved)
                    <span class="badge rounded-pill px-3 py-2 fw-semibold" style="background:#dcfce7;color:#16a34a;font-size:.85rem;">
                        <i class="fas fa-check-circle me-1"></i>معتمدة ونشطة
                    </span>
                @elseif($company->partnership_status === 'expired')
                    <span class="badge rounded-pill px-3 py-2 fw-semibold" style="background:#f3f4f6;color:#6b7280;font-size:.85rem;">
                        <i class="fas fa-history me-1"></i>منتهية الشراكة
                    </span>
                @else
                    <span class="badge rounded-pill px-3 py-2 fw-semibold" style="background:#fef9c3;color:#ca8a04;font-size:.85rem;">
                        <i class="fas fa-clock me-1"></i>قيد المراجعة
                    </span>
                @endif

                @foreach((array)$companyTypes as $type)
                    @if(isset($typesLabels[$type]))
                    <span class="badge rounded-pill px-3 py-2 fw-semibold" style="background:{{ $typesLabels[$type]['bg'] }};color:{{ $typesLabels[$type]['color'] }};font-size:.82rem;">
                        <i class="fas {{ $typesLabels[$type]['icon'] }} me-1"></i>{{ $typesLabels[$type]['label'] }}
                    </span>
                    @endif
                @endforeach

                <span class="text-muted small ms-auto"><i class="fas fa-calendar me-1"></i>تاريخ التسجيل: {{ $company->created_at ? $company->created_at->format('d / m / Y') : '-' }}</span>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- العمود الأيسر: إحصائيات --}}
        <div class="col-12 col-lg-8">
            {{-- إحصائيات سريعة --}}
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="card border-0 text-center py-3 h-100" style="border-radius:12px;background:linear-gradient(135deg,#e0f2fe,#bfdbfe);">
                        <div style="font-size:1.8rem;font-weight:800;color:#0284c7;">{{ $totalJobs }}</div>
                        <div class="text-muted small">إجمالي الوظائف</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 text-center py-3 h-100" style="border-radius:12px;background:linear-gradient(135deg,#d1fae5,#a7f3d0);">
                        <div style="font-size:1.8rem;font-weight:800;color:#059669;">{{ $openJobs }}</div>
                        <div class="text-muted small">وظائف مفتوحة</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 text-center py-3 h-100" style="border-radius:12px;background:linear-gradient(135deg,#ede9fe,#ddd6fe);">
                        <div style="font-size:1.8rem;font-weight:800;color:#7c3aed;">{{ $company->trainings()->count() }}</div>
                        <div class="text-muted small">برامج التدريب</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 text-center py-3 h-100" style="border-radius:12px;background:linear-gradient(135deg,#fef3c7,#fde68a);">
                        <div style="font-size:1.8rem;font-weight:800;color:#d97706;">
                            {{ \App\Models\Nomination::whereHas('jobOpportunity', fn($q) => $q->where('company_id', $company->id))->count() }}
                        </div>
                        <div class="text-muted small">إجمالي المتقدمين</div>
                    </div>
                </div>
            </div>

            {{-- بيانات الشركة الأساسية --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <h6 class="fw-bold mb-0"><i class="fas fa-building text-primary me-2"></i>البيانات الأساسية للشركة</h6>
                </div>
                <div class="card-body px-4 py-3">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-start gap-3">
                                <span class="rounded-circle bg-light d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px;">
                                    <i class="fas fa-envelope text-primary" style="font-size:.85rem;"></i>
                                </span>
                                <div>
                                    <div class="text-muted small">البريد الإلكتروني</div>
                                    <a href="mailto:{{ $company->email }}" class="fw-semibold text-dark text-decoration-none">{{ $company->email }}</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start gap-3">
                                <span class="rounded-circle bg-light d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px;">
                                    <i class="fas fa-phone text-primary" style="font-size:.85rem;"></i>
                                </span>
                                <div>
                                    <div class="text-muted small">رقم الهاتف</div>
                                    <span class="fw-semibold text-dark">{{ $company->phone ?? '—' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start gap-3">
                                <span class="rounded-circle bg-light d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px;">
                                    <i class="fas fa-map-marker-alt text-primary" style="font-size:.85rem;"></i>
                                </span>
                                <div>
                                    <div class="text-muted small">العنوان</div>
                                    <span class="fw-semibold text-dark">{{ $company->address ?? '—' }}</span>
                                    @if($company->city)
                                        <span class="text-muted small"> ({{ $company->city }})</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if($company->website)
                        <div class="col-md-6">
                            <div class="d-flex align-items-start gap-3">
                                <span class="rounded-circle bg-light d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px;">
                                    <i class="fas fa-globe text-primary" style="font-size:.85rem;"></i>
                                </span>
                                <div>
                                    <div class="text-muted small">الموقع الإلكتروني</div>
                                    <a href="{{ $company->website }}" target="_blank" class="fw-semibold text-primary text-decoration-none">{{ $company->website }}</a>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    @if($company->description)
                    <hr class="my-3">
                    <div>
                        <div class="text-muted small mb-1"><i class="fas fa-align-right me-1"></i>نبذة تعريفية</div>
                        <p class="text-dark mb-0" style="line-height:1.8;">{{ $company->description }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- آخر الوظائف المنشورة --}}
            @if($totalJobs > 0)
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0"><i class="fas fa-briefcase text-success me-2"></i>آخر الوظائف المنشورة</h6>
                    <span class="badge bg-success rounded-pill">{{ $totalJobs }} وظيفة</span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush rounded-4">
                        @foreach($company->jobOpportunities()->latest()->take(5)->get() as $job)
                        <div class="list-group-item border-0 border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-semibold text-dark">{{ $job->title }}</div>
                                <small class="text-muted">{{ $job->location ?? '' }}
                                    @if($job->created_at)
                                        · {{ $job->created_at->diffForHumans() }}
                                    @endif
                                </small>
                            </div>
                            <span class="badge rounded-pill {{ $job->status === 'open' ? 'bg-success' : 'bg-secondary' }} bg-opacity-10 {{ $job->status === 'open' ? 'text-success' : 'text-secondary' }} px-3">
                                {{ $job->status === 'open' ? 'مفتوحة' : 'مغلقة' }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- العمود الأيمن: مسؤول الاتصال + تفاصيل الشراكة --}}
        <div class="col-12 col-lg-4">
            {{-- بيانات مسؤول الاتصال --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <h6 class="fw-bold mb-0"><i class="fas fa-id-card text-info me-2"></i>مسؤول الاتصال</h6>
                </div>
                <div class="card-body px-4 py-3">
                    @if($company->contact_person || $company->contact_phone || $company->contact_email)
                        @if($company->contact_person)
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-circle bg-info bg-opacity-10 d-flex align-items-center justify-content-center flex-shrink-0" style="width:44px;height:44px;">
                                <i class="fas fa-user text-info"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark">{{ $company->contact_person }}</div>
                                @if($company->contact_position)
                                <div class="text-muted small">{{ $company->contact_position }}</div>
                                @endif
                            </div>
                        </div>
                        @endif

                        @if($company->contact_phone)
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <span class="text-muted" style="width:20px;"><i class="fas fa-mobile-alt"></i></span>
                            <a href="tel:{{ $company->contact_phone }}" class="text-dark text-decoration-none fw-semibold">{{ $company->contact_phone }}</a>
                        </div>
                        @endif

                        @if($company->contact_email)
                        <div class="d-flex align-items-center gap-3">
                            <span class="text-muted" style="width:20px;"><i class="fas fa-at"></i></span>
                            <a href="mailto:{{ $company->contact_email }}" class="text-primary text-decoration-none">{{ $company->contact_email }}</a>
                        </div>
                        @endif
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-user-slash fs-3 mb-2 d-block opacity-30"></i>
                            <small>لم تُضف بيانات مسؤول الاتصال بعد</small>
                        </div>
                    @endif
                </div>
            </div>

            {{-- تفاصيل الشراكة --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <h6 class="fw-bold mb-0"><i class="fas fa-handshake text-purple me-2" style="color:#7c3aed;"></i>تفاصيل الشراكة</h6>
                </div>
                <div class="card-body px-4 py-3">
                    @if($company->partnership_start_date || $company->partnership_end_date)
                    <div class="mb-3">
                        <div class="text-muted small mb-1">مدة الشراكة</div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-calendar-alt text-muted" style="font-size:.8rem;"></i>
                            <span class="fw-semibold text-dark small">
                                {{ optional($company->partnership_start_date)->format('d/m/Y') ?? '—' }}
                                @if($company->partnership_end_date)
                                    — {{ $company->partnership_end_date->format('d/m/Y') }}
                                @endif
                            </span>
                        </div>
                    </div>
                    @endif

                    @if($company->partnership_notes)
                    <div class="mb-3">
                        <div class="text-muted small mb-1">ملاحظات</div>
                        <p class="small text-dark mb-0" style="line-height:1.7;">{{ $company->partnership_notes }}</p>
                    </div>
                    @endif

                    @if(!$company->partnership_start_date && !$company->partnership_notes)
                        <div class="text-center text-muted py-2">
                            <small>لا توجد تفاصيل إضافية للشراكة</small>
                        </div>
                    @endif

                    <hr class="my-3">
                    <div>
                        <div class="text-muted small mb-1">آخر تحديث</div>
                        <div class="fw-semibold text-dark small">{{ $company->updated_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
            </div>

            {{-- إجراءات سريعة --}}
            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'partnership_officer')
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <h6 class="fw-bold mb-0"><i class="fas fa-cog text-secondary me-2"></i>إجراءات</h6>
                </div>
                <div class="card-body px-4 py-3 d-grid gap-2">
                    <a href="{{ route('admin.companies.edit', $company->id) }}" class="btn btn-outline-primary btn-sm rounded-pill">
                        <i class="fas fa-edit me-2"></i>تعديل بيانات الشركة
                    </a>
                    <form action="{{ route('admin.companies.toggle-approval', $company->id) }}" method="POST">
                        @csrf
                        @if($isApproved)
                            <button type="submit" class="btn btn-outline-warning btn-sm rounded-pill w-100">
                                <i class="fas fa-pause me-2"></i>تعليق الاعتماد
                            </button>
                        @else
                            <button type="submit" class="btn btn-success btn-sm rounded-pill w-100">
                                <i class="fas fa-check me-2"></i>اعتماد وتفعيل الشركة
                            </button>
                        @endif
                    </form>
                    <a href="{{ route('admin.companies') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
