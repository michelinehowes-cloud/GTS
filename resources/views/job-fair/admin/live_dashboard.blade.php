@extends('layouts.app')

@section('title', 'المتابعة اللحظية لإحصائيات المعرض - ' . $fair->title)

@section('content')
<div class="container-fluid py-4" style="background-color: #f8f9fa;">
    {{-- الشريط العلوي الموحد مع الشعارات الرسمية المتطابقة مع صفحة المعرض العامة --}}
    @include('job-fair.admin.partials.header', [
        'fair' => $fair,
        'page' => 'live',
        'title' => 'المتابعة اللحظية لإحصائيات وحضور المعرض',
        'subtitle' => $fair->title . ' — مؤشرات الأداء الحية ومعدلات الإقبال والزيارات الميدانية'
    ])

    <!-- بطاقات الإحصائيات الرئيسية -->
    <div class="row g-3 mb-4">
        <!-- إجمالي الحضور -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3.5 recruitment-kpi-card" style="background: #ffffff; border-right: 5px solid #2563eb !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-bold mb-1.5">إجمالي الحضور (Check-ins)</div>
                        <h2 class="fw-bold mb-1 text-dark" style="font-size: 2.1rem; line-height: 1;">{{ $stats['attended'] ?? 0 }}</h2>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0.5" style="font-size: 0.72rem;">
                            <i class="fas fa-id-badge me-1"></i>حضور فعلي بالقاعة
                        </span>
                    </div>
                    <div class="rounded-4 d-flex align-items-center justify-content-center shadow-xs flex-shrink-0" style="width: 52px; height: 52px; background: rgba(37,99,235,0.12); color: #2563eb; font-size: 1.4rem; border: 1px solid rgba(37,99,235,0.25);">
                        <i class="fas fa-user-check"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- المسجلين الإجمالي -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3.5 recruitment-kpi-card" style="background: #ffffff; border-right: 5px solid #10b981 !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-bold mb-1.5">المسجلين الإجمالي</div>
                        <h2 class="fw-bold mb-1 text-dark" style="font-size: 2.1rem; line-height: 1;">{{ $stats['total_registrations'] ?? 0 }}</h2>
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5" style="font-size: 0.72rem;">
                            <i class="fas fa-users me-1"></i>خريجون مسجلون بالمعرض
                        </span>
                    </div>
                    <div class="rounded-4 d-flex align-items-center justify-content-center shadow-xs flex-shrink-0" style="width: 52px; height: 52px; background: rgba(16,185,129,0.12); color: #10b981; font-size: 1.4rem; border: 1px solid rgba(16,185,129,0.25);">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- الشركات المشاركة -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3.5 recruitment-kpi-card" style="background: #ffffff; border-right: 5px solid #0284c7 !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-bold mb-1.5">الشركات المشاركة</div>
                        <h2 class="fw-bold mb-1 text-dark" style="font-size: 2.1rem; line-height: 1;">{{ $stats['total_companies'] ?? 0 }}</h2>
                        <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill px-2 py-0.5" style="font-size: 0.72rem;">
                            <i class="fas fa-building me-1"></i>أجنحة وشركات معتمدة
                        </span>
                    </div>
                    <div class="rounded-4 d-flex align-items-center justify-content-center shadow-xs flex-shrink-0" style="width: 52px; height: 52px; background: rgba(2,132,199,0.12); color: #0284c7; font-size: 1.4rem; border: 1px solid rgba(2,132,199,0.25);">
                        <i class="fas fa-building"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- سير ذاتية مستلمة -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3.5 recruitment-kpi-card" style="background: #ffffff; border-right: 5px solid #f59e0b !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-bold mb-1.5">سير ذاتية مستلمة (Leads)</div>
                        <h2 class="fw-bold mb-1 text-dark" style="font-size: 2.1rem; line-height: 1;">{{ $stats['total_leads'] ?? \App\Models\JobFairVisit::where('job_fair_id', $fair->id)->count() }}</h2>
                        <div class="d-flex align-items-center gap-1.5 mt-1 flex-wrap">
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-1.5 py-0.5" style="font-size: 0.68rem;">
                                {{ $stats['job_applications'] ?? 0 }} لوظائف
                            </span>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-1.5 py-0.5" style="font-size: 0.68rem;">
                                {{ $stats['general_leads'] ?? 0 }} عام
                            </span>
                        </div>
                    </div>
                    <div class="rounded-4 d-flex align-items-center justify-content-center shadow-xs flex-shrink-0" style="width: 52px; height: 52px; background: rgba(245,158,11,0.12); color: #f59e0b; font-size: 1.4rem; border: 1px solid rgba(245,158,11,0.25);">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <!-- أحدث الحضور -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-header py-3 px-3.5 d-flex align-items-center justify-content-between bg-white border-bottom">
                    <h6 class="m-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="fas fa-history text-primary"></i>
                        <span>أحدث الخريجين الحاضرين</span>
                    </h6>
                    <span class="badge bg-light text-muted border px-2.5 py-1">تحديث مباشر</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 live-recruitment-table">
                            <thead>
                                <tr>
                                    <th class="border-0 text-start">الخريج</th>
                                    <th class="border-0">التخصص</th>
                                    <th class="border-0 text-center">وقت الحضور</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentCheckins as $checkin)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2.5">
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold shadow-xs flex-shrink-0" style="width: 38px; height: 38px; font-size: 0.95rem;">
                                                    {{ mb_substr($checkin->graduate->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark">{{ $checkin->graduate->name }}</div>
                                                    <small class="text-muted font-monospace">{{ $checkin->registration_number }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-secondary border px-2.5 py-1">{{ $checkin->graduate->major ?? 'غير محدد' }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill small d-inline-flex align-items-center gap-1">
                                                <i class="far fa-clock"></i>
                                                <span>{{ $checkin->updated_at->diffForHumans() }}</span>
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">لم يتم تسجيل أي حضور حتى الآن.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- أكثر التخصصات حضوراً -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-header py-3 px-3.5 bg-white border-bottom">
                    <h6 class="m-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="fas fa-chart-pie text-info"></i>
                        <span>التخصصات الأكثر حضوراً</span>
                    </h6>
                </div>
                <div class="card-body p-3.5">
                    @forelse($topMajors as $major)
                        <div class="mb-3.5">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold text-dark small">{{ $major->major }}</span>
                                <span class="badge bg-light text-muted border px-2 py-0.5 font-monospace">{{ $major->total }} خريج</span>
                            </div>
                            <div class="progress rounded-pill overflow-hidden shadow-xs" style="height: 8px; background-color: #f1f5f9;">
                                <div class="progress-bar" role="progressbar" style="width: {{ ($major->total / max($stats['total_attended'], 1)) * 100 }}%; background: linear-gradient(90deg, #0284c7 0%, #38bdf8 100%);"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-chart-bar fa-3x mb-3 text-gray-300"></i>
                            <p class="mb-0">لا توجد بيانات كافية بعد.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════
         مؤشرات التوظيف، إحصائيات الشركات والوظائف، وحالات الترشح اللحظية
         ══════════════════════════════════════════════════════════════════ --}}
    <div class="row mb-4 mt-2">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden position-relative" style="background: linear-gradient(135deg, #091f3c 0%, #03488a 55%, #045db0 100%);">
                <div class="position-absolute top-0 start-0 w-100 h-100 opacity-10" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 16px 16px; pointer-events: none;"></div>
                <div class="card-body p-3.5 p-md-4 text-white d-flex flex-wrap align-items-center justify-content-between gap-3 position-relative z-1">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-4 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 54px; height: 54px; background: rgba(238,202,62,0.18); color: #eeca3e; font-size: 1.5rem; border: 1px solid rgba(238,202,62,0.3);">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                <h5 class="fw-bold mb-0 text-white" style="letter-spacing: -0.3px;">إحصائيات التوظيف والترشيحات بالمعرض (Live Recruitment)</h5>
                                <span class="badge bg-danger bg-opacity-75 text-white px-2.5 py-1 rounded-pill d-inline-flex align-items-center gap-1.5 shadow-xs" style="font-size: 0.72rem;">
                                    <span class="spinner-grow spinner-grow-sm text-white" style="width: 7px; height: 7px;" role="status"></span>
                                    <span>متابعة حية</span>
                                </span>
                            </div>
                            <p class="mb-0 text-white-50 small">متابعة دقيقة وفورية للسير الذاتية المستلمة، طلبات التقديم لكل شركة ولكل وظيفة، وحالات الترشح</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2.5">
                        <span class="badge px-3 py-2 rounded-pill fw-bold d-inline-flex align-items-center gap-2 shadow-xs" style="background: rgba(238, 202, 62, 0.22); color: #ffe685; border: 1px solid rgba(238, 202, 62, 0.4); font-size: 0.88rem;">
                            <i class="fas fa-file-invoice text-warning"></i>
                            <span>إجمالي السير: <strong>{{ $recruitment['nomination_stats']['total'] ?? 0 }}</strong></span>
                        </span>
                        <a href="{{ route('job-fair.admin.show', $fair->id) }}" class="btn btn-sm btn-outline-light rounded-pill px-3.5 py-1.5 d-inline-flex align-items-center gap-1.5 shadow-xs" style="font-size: 0.84rem;">
                            <i class="fas fa-cog"></i>
                            <span>تفاصيل المعرض</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- بطاقات تفصيل حالات الترشح -->
    <div class="row g-3 mb-4">
        <!-- قيد المراجعة والانتظار -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3.5 recruitment-kpi-card" style="background: #ffffff; border-right: 5px solid #f59e0b !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-bold mb-1.5">قيد المراجعة والانتظار</div>
                        <h2 class="fw-bold mb-1 text-dark" style="font-size: 2.1rem; line-height: 1;">{{ $recruitment['nomination_stats']['pending'] ?? 0 }}</h2>
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-0.5" style="font-size: 0.72rem;">
                            <i class="fas fa-clock me-1"></i>بانتظار استكمال التقييم
                        </span>
                    </div>
                    <div class="rounded-4 d-flex align-items-center justify-content-center shadow-xs flex-shrink-0" style="width: 52px; height: 52px; background: rgba(245,158,11,0.12); color: #f59e0b; font-size: 1.4rem; border: 1px solid rgba(245,158,11,0.25);">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- القائمة القصيرة -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3.5 recruitment-kpi-card" style="background: #ffffff; border-right: 5px solid #0284c7 !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-bold mb-1.5">القائمة القصيرة (Shortlisted)</div>
                        <h2 class="fw-bold mb-1 text-dark" style="font-size: 2.1rem; line-height: 1;">{{ $recruitment['nomination_stats']['shortlisted'] ?? 0 }}</h2>
                        <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill px-2 py-0.5" style="font-size: 0.72rem;">
                            <i class="fas fa-star me-1"></i>مرشحون مميزون للمقابلات
                        </span>
                    </div>
                    <div class="rounded-4 d-flex align-items-center justify-content-center shadow-xs flex-shrink-0" style="width: 52px; height: 52px; background: rgba(2,132,199,0.12); color: #0284c7; font-size: 1.4rem; border: 1px solid rgba(2,132,199,0.25);">
                        <i class="fas fa-star"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- تم القبول والتوظيف -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3.5 recruitment-kpi-card" style="background: #ffffff; border-right: 5px solid #10b981 !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-bold mb-1.5">تم القبول والترشيح (Accepted)</div>
                        <h2 class="fw-bold mb-1 text-dark" style="font-size: 2.1rem; line-height: 1;">{{ $recruitment['nomination_stats']['accepted'] ?? 0 }}</h2>
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5" style="font-size: 0.72rem;">
                            <i class="fas fa-check-circle me-1"></i>مقبولون للوظائف والشواغر
                        </span>
                    </div>
                    <div class="rounded-4 d-flex align-items-center justify-content-center shadow-xs flex-shrink-0" style="width: 52px; height: 52px; background: rgba(16,185,129,0.12); color: #10b981; font-size: 1.4rem; border: 1px solid rgba(16,185,129,0.25);">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- غير ملائم / مرفوض -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3.5 recruitment-kpi-card" style="background: #ffffff; border-right: 5px solid #ef4444 !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-bold mb-1.5">غير ملائم / مرفوض (Rejected)</div>
                        <h2 class="fw-bold mb-1 text-dark" style="font-size: 2.1rem; line-height: 1;">{{ $recruitment['nomination_stats']['rejected'] ?? 0 }}</h2>
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-0.5" style="font-size: 0.72rem;">
                            <i class="fas fa-times-circle me-1"></i>لم يستوفوا متطلبات الشاغر
                        </span>
                    </div>
                    <div class="rounded-4 d-flex align-items-center justify-content-center shadow-xs flex-shrink-0" style="width: 52px; height: 52px; background: rgba(239,68,68,0.12); color: #ef4444; font-size: 1.4rem; border: 1px solid rgba(239,68,68,0.25);">
                        <i class="fas fa-times-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- التبويبات الرئيسية: الشركات | الوظائف | سجل الترشيحات المباشر -->
    <div class="card shadow-sm border-0 rounded-4 mb-4 overflow-hidden">
        <div class="card-header bg-white border-bottom p-3 p-md-3.5 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="live-dashboard-tabs" id="fairRecruitmentTabs" role="tablist">
                <button class="nav-link active" id="companies-tab" data-bs-toggle="tab" data-bs-target="#tab-companies" type="button" role="tab">
                    <i class="fas fa-building text-primary"></i>
                    <span>إحصائيات كل شركة</span>
                    <span class="badge">{{ count($recruitment['company_stats'] ?? []) }}</span>
                </button>
                <button class="nav-link" id="jobs-tab" data-bs-toggle="tab" data-bs-target="#tab-jobs" type="button" role="tab">
                    <i class="fas fa-briefcase text-success"></i>
                    <span>إحصائيات كل وظيفة</span>
                    <span class="badge">{{ count($recruitment['job_stats'] ?? []) }}</span>
                </button>
                <button class="nav-link" id="leads-tab" data-bs-toggle="tab" data-bs-target="#tab-leads" type="button" role="tab">
                    <i class="fas fa-stream text-warning"></i>
                    <span>السجل المباشر للترشيحات</span>
                    <span class="badge">{{ count($recruitment['recent_leads'] ?? []) }}</span>
                </button>
            </div>

            <!-- شريط البحث السريع والفلترة -->
            <div class="d-flex align-items-center gap-2" style="min-width: 250px;">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="fas fa-search"></i></span>
                    <input type="text" id="filterRecruitmentInput" class="form-control bg-light border-start-0 py-2" placeholder="بحث سريع في الجدول...">
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="tab-content" id="fairRecruitmentTabsContent">
                
                {{-- 1. تبويب إحصائيات كل شركة --}}
                <div class="tab-pane fade show active p-3 p-md-4" id="tab-companies" role="tabpanel">
                    @if(empty($recruitment['company_stats']))
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-building fa-3x mb-3 text-gray-300"></i>
                            <p class="mb-0">لا توجد بيانات شركات مسجلة في المعرض حالياً.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 live-recruitment-table">
                                <thead>
                                    <tr>
                                        <th class="border-0 text-start" style="min-width: 240px;">الشركة</th>
                                        <th class="border-0 text-center" style="width: 80px;">الجناح</th>
                                        <th class="border-0 text-center" style="width: 100px;">إجمالي السير</th>
                                        <th class="border-0 text-center" style="width: 95px;">تقديم لوظائف</th>
                                        <th class="border-0 text-center" style="width: 85px;">تقديم عام</th>
                                        <th class="border-0 text-center" style="width: 90px;"><i class="fas fa-check-circle text-success me-1"></i>مقبول</th>
                                        <th class="border-0 text-center" style="width: 100px;"><i class="fas fa-star text-info me-1"></i>قائمة قصيرة</th>
                                        <th class="border-0 text-center" style="width: 90px;"><i class="fas fa-clock text-warning me-1"></i>انتظار</th>
                                        <th class="border-0 text-center" style="width: 90px;"><i class="fas fa-times-circle text-danger me-1"></i>مرفوض</th>
                                        <th class="border-0 text-center" style="min-width: 160px;">مؤشر الحالات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recruitment['company_stats'] as $c)
                                        <tr class="recruitment-table-row">
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="position-relative d-inline-flex flex-shrink-0" style="width: 44px; height: 44px;">
                                                        @if($c['logo'])
                                                            <img src="{{ Storage::url($c['logo']) }}" alt="{{ $c['name'] }}" 
                                                                 class="rounded-3 border p-1 bg-white shadow-xs w-100 h-100" 
                                                                 style="object-fit: contain;" 
                                                                 onerror="this.style.display='none'; this.nextElementSibling.classList.remove('d-none');">
                                                            <div class="rounded-3 bg-light text-primary border d-none align-items-center justify-content-center fw-bold w-100 h-100" style="font-size: 1.1rem;">
                                                                <i class="fas fa-building"></i>
                                                            </div>
                                                        @else
                                                            <div class="rounded-3 bg-light text-primary border d-flex align-items-center justify-content-center fw-bold w-100 h-100" style="font-size: 1.1rem;">
                                                                <i class="fas fa-building"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark fs-6 searchable-text text-truncate" style="max-width: 230px;" title="{{ $c['name'] }}">{{ $c['name'] }}</div>
                                                        <div class="d-flex align-items-center gap-2 mt-0.5">
                                                            @if(!empty($c['available_positions']))
                                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-1.5 py-0.5" style="font-size: 0.72rem;">
                                                                    <i class="fas fa-briefcase me-1"></i>{{ $c['available_positions'] }} وظائف معلنة
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-dark border px-2.5 py-1.5 fw-bold font-monospace">{{ $c['booth'] ?: '—' }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-primary rounded-pill px-3 py-1.5 fw-bold fs-6 shadow-xs">{{ $c['total_leads'] }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="fw-bold text-success fs-6">{{ $c['job_leads'] }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="fw-medium text-muted fs-6">{{ $c['general_leads'] }}</span>
                                            </td>
                                            <td class="text-center">
                                                @if($c['accepted'] > 0)
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 fw-bold fs-6 shadow-xs">{{ $c['accepted'] }}</span>
                                                @else
                                                    <span class="text-muted opacity-50 fw-normal">—</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($c['shortlisted'] > 0)
                                                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill px-2.5 py-1 fw-bold fs-6 shadow-xs">{{ $c['shortlisted'] }}</span>
                                                @else
                                                    <span class="text-muted opacity-50 fw-normal">—</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($c['pending'] > 0)
                                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1 fw-bold fs-6 shadow-xs">{{ $c['pending'] }}</span>
                                                @else
                                                    <span class="text-muted opacity-50 fw-normal">—</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($c['rejected'] > 0)
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1 fw-bold fs-6 shadow-xs">{{ $c['rejected'] }}</span>
                                                @else
                                                    <span class="text-muted opacity-50 fw-normal">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $cTot = max($c['total_leads'], 1);
                                                    $accPct = round(($c['accepted'] / $cTot) * 100);
                                                    $shortPct = round(($c['shortlisted'] / $cTot) * 100);
                                                    $pendPct = round(($c['pending'] / $cTot) * 100);
                                                    $rejPct = round(($c['rejected'] / $cTot) * 100);
                                                @endphp
                                                @if($c['total_leads'] > 0)
                                                    <div class="progress rounded-pill overflow-hidden shadow-xs" style="height: 10px; background-color: #f1f5f9;" title="مقبول: {{ $c['accepted'] }} | قائمة قصيرة: {{ $c['shortlisted'] }} | انتظار: {{ $c['pending'] }} | مرفوض: {{ $c['rejected'] }}">
                                                        <div class="progress-bar bg-success" style="width: {{ $accPct }}%"></div>
                                                        <div class="progress-bar bg-info" style="width: {{ $shortPct }}%"></div>
                                                        <div class="progress-bar bg-warning" style="width: {{ $pendPct }}%"></div>
                                                        <div class="progress-bar bg-danger" style="width: {{ $rejPct }}%"></div>
                                                    </div>
                                                    <div class="d-flex justify-content-between mt-1 text-muted" style="font-size: 0.7rem;">
                                                        <span>{{ $accPct }}% قبول</span>
                                                        <span>{{ $c['total_leads'] }} سيرة</span>
                                                    </div>
                                                @else
                                                    <div class="progress rounded-pill" style="height: 8px; background-color: #f1f5f9;">
                                                        <div class="progress-bar bg-secondary opacity-25" style="width: 100%"></div>
                                                    </div>
                                                    <small class="text-muted d-block text-center mt-0.5" style="font-size: 0.68rem;">لا توجد طلبات</small>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                {{-- 2. تبويب إحصائيات كل وظيفة --}}
                <div class="tab-pane fade p-3 p-md-4" id="tab-jobs" role="tabpanel">
                    @if(empty($recruitment['job_stats']))
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-briefcase fa-3x mb-3 text-gray-300"></i>
                            <p class="mb-0">لا توجد وظائف معلنة مرتبطة بالمعرض حتى الآن.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 live-recruitment-table">
                                <thead>
                                    <tr>
                                        <th class="border-0 text-start" style="min-width: 200px;">مسمى الوظيفة</th>
                                        <th class="border-0" style="min-width: 180px;">الشركة</th>
                                        <th class="border-0 text-center" style="width: 95px;">المقاعد الشاغرة</th>
                                        <th class="border-0 text-center" style="width: 110px;">المتقدمون بالمعرض</th>
                                        <th class="border-0 text-center" style="width: 85px;"><i class="fas fa-check-circle text-success me-1"></i>مقبول</th>
                                        <th class="border-0 text-center" style="width: 95px;"><i class="fas fa-star text-info me-1"></i>قائمة قصيرة</th>
                                        <th class="border-0 text-center" style="width: 85px;"><i class="fas fa-clock text-warning me-1"></i>انتظار</th>
                                        <th class="border-0 text-center" style="width: 85px;"><i class="fas fa-times-circle text-danger me-1"></i>مرفوض</th>
                                        <th class="border-0 text-center" style="width: 110px;">حالة الوظيفة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recruitment['job_stats'] as $j)
                                        <tr class="recruitment-table-row">
                                            <td>
                                                <div class="fw-bold text-dark fs-6 searchable-text">{{ $j['title'] }}</div>
                                                <small class="text-muted font-monospace">ID: #{{ $j['id'] }}</small>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    @if($j['company_logo'])
                                                        <img src="{{ Storage::url($j['company_logo']) }}" alt="{{ $j['company_name'] }}" 
                                                             class="rounded-2 border bg-white p-0.5 flex-shrink-0" 
                                                             style="width: 28px; height: 28px; object-fit: contain;"
                                                             onerror="this.style.display='none'; this.nextElementSibling.classList.remove('d-none');">
                                                        <div class="rounded-2 bg-light text-primary border d-none align-items-center justify-content-center flex-shrink-0" style="width: 28px; height: 28px; font-size: 0.8rem;">
                                                            <i class="fas fa-building"></i>
                                                        </div>
                                                    @else
                                                        <div class="rounded-2 bg-light text-primary border d-flex align-items-center justify-content-center flex-shrink-0" style="width: 28px; height: 28px; font-size: 0.8rem;">
                                                            <i class="fas fa-building"></i>
                                                        </div>
                                                    @endif
                                                    <span class="fw-semibold text-secondary searchable-text text-truncate" style="max-width: 180px;">{{ $j['company_name'] }}</span>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-dark border px-2.5 py-1.5 fw-bold font-monospace">{{ $j['seats'] }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-primary rounded-pill px-3 py-1.5 fw-bold fs-6 shadow-xs">{{ $j['total_applied'] }}</span>
                                            </td>
                                            <td class="text-center">
                                                @if($j['accepted'] > 0)
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 fw-bold fs-6 shadow-xs">{{ $j['accepted'] }}</span>
                                                @else
                                                    <span class="text-muted opacity-50 fw-normal">—</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($j['shortlisted'] > 0)
                                                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill px-2.5 py-1 fw-bold fs-6 shadow-xs">{{ $j['shortlisted'] }}</span>
                                                @else
                                                    <span class="text-muted opacity-50 fw-normal">—</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($j['pending'] > 0)
                                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1 fw-bold fs-6 shadow-xs">{{ $j['pending'] }}</span>
                                                @else
                                                    <span class="text-muted opacity-50 fw-normal">—</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($j['rejected'] > 0)
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1 fw-bold fs-6 shadow-xs">{{ $j['rejected'] }}</span>
                                                @else
                                                    <span class="text-muted opacity-50 fw-normal">—</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge {{ $j['status'] === 'open' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary border' }} px-2.5 py-1 rounded-pill fw-bold d-inline-flex align-items-center gap-1.5" style="font-size: 0.74rem;">
                                                    <span class="rounded-circle {{ $j['status'] === 'open' ? 'bg-success' : 'bg-secondary' }}" style="width: 6px; height: 6px;"></span>
                                                    <span>{{ $j['status'] === 'open' ? 'نشطة ومفتوحة' : $j['status'] }}</span>
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                {{-- 3. تبويب السجل المباشر للترشيحات --}}
                <div class="tab-pane fade p-3 p-md-4" id="tab-leads" role="tabpanel">
                    @if(empty($recruitment['recent_leads']) || $recruitment['recent_leads']->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-id-card fa-3x mb-3 text-gray-300"></i>
                            <p class="mb-0">لم يتم مسح أي بطاقة أو استلام سير في المعرض حتى الآن.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 live-recruitment-table">
                                <thead>
                                    <tr>
                                        <th class="border-0 text-start">الخريج</th>
                                        <th class="border-0">الشركة المستلمة</th>
                                        <th class="border-0">الوظيفة المتقدم لها</th>
                                        <th class="border-0 text-center">حالة الترشح</th>
                                        <th class="border-0">ملاحظات المقابلة</th>
                                        <th class="border-0 text-center">توقيت المسح</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recruitment['recent_leads'] as $lead)
                                        @php
                                            $stMap = [
                                                'shortlisted' => ['bg' => 'bg-info-subtle text-info-emphasis border border-info-subtle', 'icon' => 'fas fa-star', 'label' => 'قائمة قصيرة'],
                                                'accepted'    => ['bg' => 'bg-success-subtle text-success border border-success-subtle', 'icon' => 'fas fa-check-circle', 'label' => 'مقبول للتوظيف'],
                                                'rejected'    => ['bg' => 'bg-danger-subtle text-danger border border-danger-subtle', 'icon' => 'fas fa-times-circle', 'label' => 'مرفوض'],
                                                'pending'     => ['bg' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle', 'icon' => 'fas fa-clock', 'label' => 'قيد المراجعة'],
                                            ];
                                            $stInfo = $stMap[$lead->status] ?? $stMap['pending'];
                                            $cName = $lead->company ? ($lead->company->company ? $lead->company->company->name : $lead->company->name) : 'شركة';
                                        @endphp
                                        <tr class="recruitment-table-row">
                                            <td>
                                                <div class="d-flex align-items-center gap-2.5">
                                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold shadow-xs flex-shrink-0" style="width: 38px; height: 38px; font-size: 0.95rem;">
                                                        {{ mb_substr($lead->graduate->name ?? 'خ', 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark searchable-text">{{ $lead->graduate->name ?? 'غير محدد' }}</div>
                                                        <small class="text-muted">{{ $lead->graduate->major ?? 'خريج' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="fw-semibold text-secondary searchable-text">{{ $cName }}</span>
                                            </td>
                                            <td>
                                                @if($lead->jobOpportunity)
                                                    <span class="badge bg-light text-dark border px-2.5 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5">
                                                        <i class="fas fa-briefcase text-primary"></i>
                                                        <span class="searchable-text">{{ $lead->jobOpportunity->title }}</span>
                                                    </span>
                                                @else
                                                    <span class="badge bg-light text-muted border px-2.5 py-1.5 d-inline-flex align-items-center gap-1.5">
                                                        <i class="fas fa-user-plus text-secondary"></i>
                                                        <span>تقديم واهتمام عام</span>
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge {{ $stInfo['bg'] }} px-2.5 py-1.5 rounded-pill shadow-xs d-inline-flex align-items-center gap-1.5 fw-bold" style="font-size: 0.76rem;">
                                                    <i class="{{ $stInfo['icon'] }}"></i>
                                                    <span>{{ $stInfo['label'] }}</span>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-muted small">{{ Str::limit($lead->notes ?: 'لا توجد ملاحظات مسجلة', 45) }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-muted border px-2 py-1 small d-inline-flex align-items-center gap-1">
                                                    <i class="far fa-clock"></i>
                                                    <span>{{ $lead->updated_at ? $lead->updated_at->diffForHumans() : $lead->created_at->diffForHumans() }}</span>
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>

<style>
    /* Scoped styles for Live Recruitment Dashboard */
    .live-dashboard-tabs {
        background: #f1f5f9;
        padding: 5px;
        border-radius: 16px;
        display: inline-flex;
        flex-wrap: wrap;
        gap: 6px;
        border: 1px solid #e2e8f0;
    }

    .live-dashboard-tabs .nav-link {
        color: #475569 !important; /* CRITICAL: Overrides white color in app.scss */
        background: transparent !important;
        border-radius: 12px !important;
        padding: 8px 16px !important;
        font-weight: 700 !important;
        font-size: 0.9rem !important;
        margin: 0 !important;
        border: none !important;
        box-shadow: none !important;
        transform: none !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        text-decoration: none !important;
    }

    .live-dashboard-tabs .nav-link i {
        width: auto !important;
        margin: 0 !important;
        font-size: 0.95rem !important;
    }

    .live-dashboard-tabs .nav-link:hover {
        color: #0f172a !important;
        background: rgba(255, 255, 255, 0.8) !important;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04) !important;
    }

    .live-dashboard-tabs .nav-link.active {
        color: #ffffff !important;
        background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%) !important;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.28) !important;
    }

    .live-dashboard-tabs .nav-link.active i {
        color: #ffffff !important;
    }

    .live-dashboard-tabs .nav-link .badge {
        font-size: 0.76rem !important;
        padding: 3px 8px !important;
        border-radius: 9999px !important;
        font-weight: 700 !important;
    }

    .live-dashboard-tabs .nav-link:not(.active) .badge {
        background: #e2e8f0 !important;
        color: #475569 !important;
    }

    .live-dashboard-tabs .nav-link.active .badge {
        background: rgba(255, 255, 255, 0.25) !important;
        color: #ffffff !important;
    }

    /* KPI recruitment cards */
    .recruitment-kpi-card {
        transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.25s ease;
        border-radius: 16px !important;
    }
    .recruitment-kpi-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px -6px rgba(0, 0, 0, 0.08) !important;
    }

    /* Table aesthetic */
    .live-recruitment-table th {
        font-size: 0.82rem;
        text-transform: uppercase;
        letter-spacing: 0.2px;
        font-weight: 700;
        color: #475569;
        background: #f8fafc !important;
        padding: 13px 14px !important;
        border-bottom: 1px solid #e2e8f0 !important;
    }
    .live-recruitment-table td {
        padding: 14px 14px !important;
        border-bottom: 1px solid #f1f5f9 !important;
        vertical-align: middle;
    }
    .live-recruitment-table tbody tr {
        transition: background-color 0.15s ease;
    }
    .live-recruitment-table tbody tr:hover {
        background-color: #f8fafc !important;
    }

    /* Subtle colors */
    .bg-success-subtle { background-color: #ecfdf5 !important; }
    .text-success { color: #059669 !important; }
    .border-success-subtle { border-color: #a7f3d0 !important; }

    .bg-info-subtle { background-color: #f0f9ff !important; }
    .text-info-emphasis { color: #0284c7 !important; }
    .border-info-subtle { border-color: #bae6fd !important; }

    .bg-warning-subtle { background-color: #fffbeb !important; }
    .text-warning-emphasis { color: #d97706 !important; }
    .border-warning-subtle { border-color: #fde68a !important; }

    .bg-danger-subtle { background-color: #fff1f2 !important; }
    .text-danger { color: #e11d48 !important; }
    .border-danger-subtle { border-color: #fecdd3 !important; }

    .bg-primary-subtle { background-color: #eff6ff !important; }
    .text-primary { color: #2563eb !important; }
    .border-primary-subtle { border-color: #bfdbfe !important; }

    .bg-secondary-subtle { background-color: #f1f5f9 !important; }
    .text-secondary { color: #64748b !important; }

    .shadow-xs { box-shadow: 0 1px 2px rgba(0,0,0,0.05); }

    .blink {
        animation: blinker 1.5s linear infinite;
    }
    @keyframes blinker {
        50% { opacity: 0; }
    }
    .border-left-primary { border-left: 4px solid #4e73df !important; }
    .border-left-success { border-left: 4px solid #1cc88a !important; }
    .border-left-info { border-left: 4px solid #36b9cc !important; }
    .border-left-warning { border-left: 4px solid #f6c23e !important; }
    .text-gray-300 { color: #dddfeb !important; }
    .text-gray-800 { color: #5a5c69 !important; }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabButtons = document.querySelectorAll('#fairRecruitmentTabs button');
        const tabStorageKey = 'live_fair_active_tab_{{ $fair->id }}';

        // استعادة التبويب النشط بعد التحديث التلقائي للصفحة
        const savedTabId = sessionStorage.getItem(tabStorageKey);
        if (savedTabId) {
            const targetBtn = document.getElementById(savedTabId);
            if (targetBtn) {
                tabButtons.forEach(b => b.classList.remove('active'));
                document.querySelectorAll('#fairRecruitmentTabsContent .tab-pane').forEach(p => p.classList.remove('show', 'active'));
                targetBtn.classList.add('active');
                const targetSelector = targetBtn.getAttribute('data-bs-target');
                const targetPane = document.querySelector(targetSelector);
                if (targetPane) {
                    targetPane.classList.add('show', 'active');
                }
            }
        }

        tabButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                tabButtons.forEach(b => b.classList.remove('active'));
                document.querySelectorAll('#fairRecruitmentTabsContent .tab-pane').forEach(p => p.classList.remove('show', 'active'));
                this.classList.add('active');
                sessionStorage.setItem(tabStorageKey, this.id);
                const targetSelector = this.getAttribute('data-bs-target');
                const targetPane = document.querySelector(targetSelector);
                if (targetPane) {
                    targetPane.classList.add('show', 'active');
                }
            });
        });

        // فلترة سريعة للجدول الحالي
        const searchInput = document.getElementById('filterRecruitmentInput');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value.trim().toLowerCase();
                const activePane = document.querySelector('#fairRecruitmentTabsContent .tab-pane.active');
                if (!activePane) return;

                const rows = activePane.querySelectorAll('tbody tr.recruitment-table-row');
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(query) ? '' : 'none';
                });
            });
        }
    });

    // Refresh page every 30 seconds for live updates (unless user is actively searching)
    setTimeout(function() {
        const searchInput = document.getElementById('filterRecruitmentInput');
        if (!searchInput || !searchInput.value.trim()) {
            window.location.reload();
        }
    }, 30000);
</script>
@endsection
