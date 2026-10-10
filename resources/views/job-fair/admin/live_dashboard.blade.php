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
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">إجمالي الحضور (Check-ins)</div>
                            <div class="h1 mb-0 font-weight-bold text-gray-800">{{ $stats['attended'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-3x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">المسجلين الإجمالي</div>
                            <div class="h1 mb-0 font-weight-bold text-gray-800">{{ $stats['total_registrations'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-3x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">الشركات المشاركة</div>
                            <div class="h1 mb-0 font-weight-bold text-gray-800">{{ $stats['total_companies'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-3x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">سير ذاتية مستلمة (Leads)</div>
                            <div class="h1 mb-0 font-weight-bold text-gray-800">{{ $stats['total_leads'] ?? \App\Models\JobFairVisit::where('job_fair_id', $fair->id)->count() }}</div>
                            <div class="mt-1 text-muted" style="font-size: 0.78rem;">
                                <span class="text-success font-weight-bold">{{ $stats['job_applications'] ?? 0 }}</span> لوظائف | 
                                <span class="text-primary font-weight-bold">{{ $stats['general_leads'] ?? 0 }}</span> عام
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-invoice fa-3x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- أحدث الحضور -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow mb-4 h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-history mr-2"></i> أحدث الخريجين الحاضرين</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0">الخريج</th>
                                    <th class="border-0">التخصص</th>
                                    <th class="border-0">وقت الحضور</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentCheckins as $checkin)
                                    <tr>
                                        <td class="align-middle">
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-3" style="width: 40px; height: 40px;">
                                                    {{ mb_substr($checkin->graduate->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="font-weight-bold">{{ $checkin->graduate->name }}</div>
                                                    <div class="small text-muted">{{ $checkin->registration_number }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle">{{ $checkin->graduate->major ?? 'غير محدد' }}</td>
                                        <td class="align-middle">
                                            <span class="badge badge-success px-2 py-1"><i class="far fa-clock mr-1"></i> {{ $checkin->updated_at->diffForHumans() }}</span>
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
        <div class="col-lg-4 mb-4">
            <div class="card shadow mb-4 h-100">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-pie mr-2"></i> التخصصات الأكثر حضوراً</h6>
                </div>
                <div class="card-body">
                    @forelse($topMajors as $major)
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="font-weight-bold">{{ $major->major }}</span>
                                <span class="text-muted">{{ $major->total }} خريج</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-info" role="progressbar" style="width: {{ ($major->total / max($stats['total_attended'], 1)) * 100 }}%" aria-valuenow="{{ $major->total }}" aria-valuemin="0" aria-valuemax="{{ $stats['total_attended'] }}"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-chart-bar fa-3x mb-3 text-gray-300"></i>
                            <p>لا توجد بيانات كافية بعد.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════
         مؤشرات التوظيف، إحصائيات الشركات والوظائف، وحالات الترشح اللحظية
         ══════════════════════════════════════════════════════════════════ --}}
    <div class="row mb-3 mt-2">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden" style="background: linear-gradient(135deg, #091f3c 0%, #03488a 55%, #045db0 100%);">
                <div class="card-body p-3 p-md-4 text-white d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 50px; height: 50px; background: rgba(238,202,62,0.2); color: #eeca3e; font-size: 1.4rem;">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1 text-white">إحصائيات التوظيف والترشيحات بالمعرض (Live Recruitment)</h5>
                            <p class="mb-0 text-white-50 small">متابعة دقيقة وفورية للسير الذاتية المستلمة، طلبات التقديم لكل شركة ولكل وظيفة، وحالات الترشح</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold" style="font-size: 0.85rem;">
                            <i class="fas fa-file-invoice me-1"></i> إجمالي السير: {{ $recruitment['nomination_stats']['total'] ?? 0 }}
                        </span>
                        <a href="{{ route('job-fair.admin.show', $fair->id) }}" class="btn btn-sm btn-outline-light rounded-pill px-3">
                            <i class="fas fa-cog me-1"></i> تفاصيل المعرض
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
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: #ffffff; border-right: 5px solid #f59e0b !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-bold mb-1">قيد المراجعة والانتظار</div>
                        <h2 class="fw-bold mb-0 text-warning">{{ $recruitment['nomination_stats']['pending'] ?? 0 }}</h2>
                        <small class="text-muted" style="font-size: 0.78rem;">سير بانتظار استكمال التقييم</small>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(245,158,11,0.12); color: #f59e0b; font-size: 1.3rem;">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- القائمة القصيرة -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: #ffffff; border-right: 5px solid #0284c7 !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-bold mb-1">القائمة القصيرة (Shortlisted)</div>
                        <h2 class="fw-bold mb-0 text-info">{{ $recruitment['nomination_stats']['shortlisted'] ?? 0 }}</h2>
                        <small class="text-muted" style="font-size: 0.78rem;">مرشحون مميزون للمقابلات</small>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(2,132,199,0.12); color: #0284c7; font-size: 1.3rem;">
                        <i class="fas fa-star"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- تم القبول والتوظيف -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: #ffffff; border-right: 5px solid #10b981 !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-bold mb-1">تم القبول والترشيح (Accepted)</div>
                        <h2 class="fw-bold mb-0 text-success">{{ $recruitment['nomination_stats']['accepted'] ?? 0 }}</h2>
                        <small class="text-muted" style="font-size: 0.78rem;">مقبولون للوظائف والشواغر</small>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(16,185,129,0.12); color: #10b981; font-size: 1.3rem;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- غير ملائم / مرفوض -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: #ffffff; border-right: 5px solid #ef4444 !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-bold mb-1">غير ملائم / مرفوض (Rejected)</div>
                        <h2 class="fw-bold mb-0 text-danger">{{ $recruitment['nomination_stats']['rejected'] ?? 0 }}</h2>
                        <small class="text-muted" style="font-size: 0.78rem;">لم يستوفوا متطلبات الشاغر</small>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(239,68,68,0.12); color: #ef4444; font-size: 1.3rem;">
                        <i class="fas fa-times-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- التبويبات الرئيسية: الشركات | الوظائف | سجل الترشيحات المباشر -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-bottom p-3">
            <ul class="nav nav-pills card-header-pills gap-2" id="fairRecruitmentTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active rounded-pill fw-bold px-3 py-2 d-inline-flex align-items-center gap-2" id="companies-tab" data-bs-toggle="tab" data-bs-target="#tab-companies" type="button" role="tab">
                        <i class="fas fa-building text-primary"></i>
                        <span>إحصائيات كل شركة</span>
                        <span class="badge bg-primary text-white rounded-pill ms-1">{{ count($recruitment['company_stats'] ?? []) }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill fw-bold px-3 py-2 d-inline-flex align-items-center gap-2" id="jobs-tab" data-bs-toggle="tab" data-bs-target="#tab-jobs" type="button" role="tab">
                        <i class="fas fa-briefcase text-success"></i>
                        <span>إحصائيات كل وظيفة</span>
                        <span class="badge bg-success text-white rounded-pill ms-1">{{ count($recruitment['job_stats'] ?? []) }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill fw-bold px-3 py-2 d-inline-flex align-items-center gap-2" id="leads-tab" data-bs-toggle="tab" data-bs-target="#tab-leads" type="button" role="tab">
                        <i class="fas fa-stream text-warning"></i>
                        <span>السجل المباشر للترشيحات</span>
                        <span class="badge bg-warning text-dark rounded-pill ms-1">{{ count($recruitment['recent_leads'] ?? []) }}</span>
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body p-3 p-md-4">
            <div class="tab-content" id="fairRecruitmentTabsContent">
                
                {{-- 1. تبويب إحصائيات كل شركة --}}
                <div class="tab-pane fade show active" id="tab-companies" role="tabpanel">
                    @if(empty($recruitment['company_stats']))
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-building fa-3x mb-3 text-gray-300"></i>
                            <p class="mb-0">لا توجد بيانات شركات مسجلة في المعرض حالياً.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light text-dark">
                                    <tr>
                                        <th class="border-0">الشركة</th>
                                        <th class="border-0 text-center">الجناح</th>
                                        <th class="border-0 text-center">إجمالي السير</th>
                                        <th class="border-0 text-center">تقديم لوظائف</th>
                                        <th class="border-0 text-center">تقديم عام</th>
                                        <th class="border-0 text-center">مقبول ✅</th>
                                        <th class="border-0 text-center">قائمة قصيرة ⭐</th>
                                        <th class="border-0 text-center">انتظار 🟡</th>
                                        <th class="border-0 text-center">مرفوض ❌</th>
                                        <th class="border-0" style="min-width: 140px;">مؤشر الحالات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recruitment['company_stats'] as $c)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-2.5">
                                                    @if($c['logo'])
                                                        <img src="{{ Storage::url($c['logo']) }}" alt="{{ $c['name'] }}" class="rounded-circle border p-0.5 bg-white shadow-xs" style="width: 40px; height: 40px; object-fit: contain;">
                                                    @else
                                                        <div class="rounded-circle bg-light text-primary border d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 40px; height: 40px; font-size: 1rem;">
                                                            <i class="fas fa-building"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="fw-bold text-dark">{{ $c['name'] }}</div>
                                                        @if(!empty($c['available_positions']))
                                                            <small class="text-muted">{{ $c['available_positions'] }} وظائف معلنة</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-dark border px-2 py-1">{{ $c['booth'] }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-primary rounded-pill px-2.5 py-1.5 fw-bold fs-6">{{ $c['total_leads'] }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="fw-bold text-success">{{ $c['job_leads'] }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="text-muted">{{ $c['general_leads'] }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-success rounded-pill px-2 py-1">{{ $c['accepted'] }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-info text-dark rounded-pill px-2 py-1">{{ $c['shortlisted'] }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-warning text-dark rounded-pill px-2 py-1">{{ $c['pending'] }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-danger rounded-pill px-2 py-1">{{ $c['rejected'] }}</span>
                                            </td>
                                            <td>
                                                @php
                                                    $cTot = max($c['total_leads'], 1);
                                                    $accPct = ($c['accepted'] / $cTot) * 100;
                                                    $shortPct = ($c['shortlisted'] / $cTot) * 100;
                                                    $pendPct = ($c['pending'] / $cTot) * 100;
                                                @endphp
                                                <div class="progress" style="height: 8px;" title="مقبول: {{ $c['accepted'] }} | قائمة قصيرة: {{ $c['shortlisted'] }} | انتظار: {{ $c['pending'] }}">
                                                    <div class="progress-bar bg-success" style="width: {{ $accPct }}%"></div>
                                                    <div class="progress-bar bg-info" style="width: {{ $shortPct }}%"></div>
                                                    <div class="progress-bar bg-warning" style="width: {{ $pendPct }}%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                {{-- 2. تبويب إحصائيات كل وظيفة --}}
                <div class="tab-pane fade" id="tab-jobs" role="tabpanel">
                    @if(empty($recruitment['job_stats']))
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-briefcase fa-3x mb-3 text-gray-300"></i>
                            <p class="mb-0">لا توجد وظائف معلنة مرتبطة بالمعرض حتى الآن.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light text-dark">
                                    <tr>
                                        <th class="border-0">مسمى الوظيفة</th>
                                        <th class="border-0">الشركة</th>
                                        <th class="border-0 text-center">المقاعد الشاغرة</th>
                                        <th class="border-0 text-center">المتقدمون بالمعرض</th>
                                        <th class="border-0 text-center">مقبول ✅</th>
                                        <th class="border-0 text-center">قائمة قصيرة ⭐</th>
                                        <th class="border-0 text-center">انتظار 🟡</th>
                                        <th class="border-0 text-center">مرفوض ❌</th>
                                        <th class="border-0 text-center">حالة الوظيفة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recruitment['job_stats'] as $j)
                                        <tr>
                                            <td>
                                                <div class="fw-bold text-dark fs-6">{{ $j['title'] }}</div>
                                                <small class="text-muted">ID: #{{ $j['id'] }}</small>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    @if($j['company_logo'])
                                                        <img src="{{ Storage::url($j['company_logo']) }}" alt="{{ $j['company_name'] }}" class="rounded-circle border bg-white" style="width: 28px; height: 28px; object-fit: contain;">
                                                    @endif
                                                    <span class="fw-semibold text-secondary">{{ $j['company_name'] }}</span>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-dark border px-2 py-1">{{ $j['seats'] }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-primary rounded-pill px-2.5 py-1.5 fw-bold fs-6">{{ $j['total_applied'] }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-success rounded-pill px-2 py-1">{{ $j['accepted'] }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-info text-dark rounded-pill px-2 py-1">{{ $j['shortlisted'] }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-warning text-dark rounded-pill px-2 py-1">{{ $j['pending'] }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-danger rounded-pill px-2 py-1">{{ $j['rejected'] }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge {{ $j['status'] === 'open' ? 'bg-success' : 'bg-secondary' }} px-2 py-1 rounded-pill" style="font-size: 0.75rem;">
                                                    {{ $j['status'] === 'open' ? 'نشطة ومفتوحة' : $j['status'] }}
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
                <div class="tab-pane fade" id="tab-leads" role="tabpanel">
                    @if(empty($recruitment['recent_leads']) || $recruitment['recent_leads']->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-id-card fa-3x mb-3 text-gray-300"></i>
                            <p class="mb-0">لم يتم مسح أي بطاقة أو استلام سير في المعرض حتى الآن.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light text-dark">
                                    <tr>
                                        <th class="border-0">الخريج</th>
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
                                                'shortlisted' => ['bg' => 'bg-info text-dark', 'icon' => 'fas fa-star', 'label' => 'قائمة قصيرة ⭐'],
                                                'accepted'    => ['bg' => 'bg-success text-white', 'icon' => 'fas fa-check-circle', 'label' => 'مقبول للتوظيف ✅'],
                                                'rejected'    => ['bg' => 'bg-danger text-white', 'icon' => 'fas fa-times-circle', 'label' => 'مرفوض ❌'],
                                                'pending'     => ['bg' => 'bg-warning text-dark', 'icon' => 'fas fa-clock', 'label' => 'قيد المراجعة 🟡'],
                                            ];
                                            $stInfo = $stMap[$lead->status] ?? $stMap['pending'];
                                            $cName = $lead->company ? ($lead->company->company ? $lead->company->company->name : $lead->company->name) : 'شركة';
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px;">
                                                        {{ mb_substr($lead->graduate->name ?? 'خ', 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark">{{ $lead->graduate->name ?? 'غير محدد' }}</div>
                                                        <small class="text-muted">{{ $lead->graduate->major ?? 'خريج' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="fw-semibold text-secondary">{{ $cName }}</span>
                                            </td>
                                            <td>
                                                @if($lead->jobOpportunity)
                                                    <span class="badge bg-light text-dark border px-2.5 py-1.5 fw-bold">
                                                        <i class="fas fa-briefcase text-primary me-1"></i> {{ $lead->jobOpportunity->title }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-light text-muted border px-2.5 py-1.5">
                                                        <i class="fas fa-user-plus me-1"></i> تقديم واهتمام عام
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge {{ $stInfo['bg'] }} px-2.5 py-1.5 rounded-pill shadow-xs">
                                                    <i class="{{ $stInfo['icon'] }} me-1"></i> {{ $stInfo['label'] }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-muted small">{{ Str::limit($lead->notes ?: 'لا توجد ملاحظات مسجلة', 40) }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-muted border px-2 py-1 small">
                                                    <i class="far fa-clock me-1"></i> {{ $lead->updated_at ? $lead->updated_at->diffForHumans() : $lead->created_at->diffForHumans() }}
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
    // Tab switching support
    document.addEventListener('DOMContentLoaded', function() {
        const tabButtons = document.querySelectorAll('#fairRecruitmentTabs button[data-bs-toggle="tab"]');
        tabButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                tabButtons.forEach(b => b.classList.remove('active'));
                document.querySelectorAll('#fairRecruitmentTabsContent .tab-pane').forEach(p => p.classList.remove('show', 'active'));
                this.classList.add('active');
                const targetSelector = this.getAttribute('data-bs-target');
                const targetPane = document.querySelector(targetSelector);
                if (targetPane) {
                    targetPane.classList.add('show', 'active');
                }
            });
        });
    });

    // Refresh page every 30 seconds for live updates
    setTimeout(function() {
        window.location.reload();
    }, 30000);
</script>
@endsection
