@extends('layouts.app')

@section('title', 'لوحة تحكم الشركة')
@section('page-title', 'لوحة تحكم الشركة')

@section('content')
<div class="container-fluid py-4">
    <!-- الشريط الترحيبي المعتمد -->
    <x-page-hero
        title="مرحباً بك، {{ $company->name ?? auth()->user()->name }}"
        subtitle="{{ $company ? ($company->industry ? 'قطاع ' . $company->industry . ' — إدارة فرص العمل ومتابعة المتقدمين والتوظيف' : 'لوحة إدارة فرص العمل ومتابعة المتقدمين والتوظيف') : 'لوحة إدارة فرص العمل ومتابعة المتقدمين والتوظيف' }}"
        icon="fas fa-building"
        :breadcrumbs="[
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'لوحة تحكم الشركة']
        ]"
        secondaryBadge="مؤسسة شريكة معتمدة"
        secondaryBadgeIcon="fas fa-handshake"
    >
        <a href="{{ route('job-opportunities.create') }}" class="btn btn-warning text-dark fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 text-nowrap" style="font-size: 0.88rem; transition: transform 0.2s ease;">
            <i class="fas fa-plus-circle fs-6"></i>
            <span>+ إضافة فرصة عمل</span>
        </a>
        <a href="{{ route('job-opportunities.index') }}" class="btn btn-light bg-white text-primary fw-bold py-2.5 px-3 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 text-nowrap" style="font-size: 0.88rem; transition: transform 0.2s ease;">
            <i class="fas fa-briefcase fs-6"></i>
            <span>إدارة الوظائف</span>
        </a>
        <a href="{{ route('company.nominations') }}" class="btn btn-light bg-white text-primary fw-bold py-2.5 px-3 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 text-nowrap" style="font-size: 0.88rem; transition: transform 0.2s ease;">
            <i class="fas fa-users fs-6"></i>
            <span>المرشحون للوظائف</span>
        </a>
        <a href="{{ route('company.job-fairs.index') }}" class="btn btn-light bg-white text-primary fw-bold py-2.5 px-3 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 text-nowrap" style="font-size: 0.88rem; transition: transform 0.2s ease;">
            <i class="fas fa-calendar-star fs-6 text-warning"></i>
            <span>المعارض والفعاليات</span>
        </a>
        <a href="{{ route('company.profile') }}" class="btn btn-outline-light text-white fw-bold py-2.5 px-3 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 text-nowrap" style="font-size: 0.88rem; transition: transform 0.2s ease;">
            <i class="fas fa-building fs-6"></i>
            <span>الملف التعريفي</span>
        </a>
    </x-page-hero>

    <!-- بطاقات الإحصائيات (Bento Grid Stats) -->
    <div class="bento-grid mb-4">
        <!-- فرص العمل الفعالة -->
        <div class="bento-card">
            <div class="bento-card-header d-flex align-items-center justify-content-between">
                <h3 class="bento-card-title mb-0">فرص العمل الفعالة</h3>
                <div class="bento-card-icon bento-icon-primary">
                    <i class="fas fa-briefcase"></i>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-end mt-2">
                <div class="bento-stat">{{ $stats['active_jobs'] ?? 0 }}</div>
                <div class="badge rounded-pill bg-primary bg-opacity-10 text-primary px-3 py-1.5 mb-2 fw-bold">
                    من إجمالي {{ $stats['total_jobs'] ?? 0 }} <i class="fas fa-arrow-trend-up ms-1"></i>
                </div>
            </div>
            <div class="bento-desc mt-2 text-muted small">الوظائف المفتوحة حالياً لاستقبال الخريجين</div>
            <div class="mt-3 pt-2 border-top border-light">
                <a href="{{ route('job-opportunities.index') }}" class="small fw-bold text-primary text-decoration-none d-flex align-items-center justify-content-between">
                    <span>استعراض كافة الوظائف</span>
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>
        </div>

        <!-- إجمالي الطلبات والمرشحين -->
        <div class="bento-card">
            <div class="bento-card-header d-flex align-items-center justify-content-between">
                <h3 class="bento-card-title mb-0">إجمالي المرشحين</h3>
                <div class="bento-card-icon bento-icon-success">
                    <i class="fas fa-file-alt"></i>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-end mt-2">
                <div class="bento-stat">{{ $stats['total_applications'] ?? 0 }}</div>
                <div class="badge rounded-pill bg-success bg-opacity-10 text-success px-3 py-1.5 mb-2 fw-bold">
                    نشط <i class="fas fa-bolt ms-1"></i>
                </div>
            </div>
            <div class="bento-desc mt-2 text-muted small">إجمالي السير الذاتية والطلبات المستلمة</div>
            <div class="mt-3 pt-2 border-top border-light">
                <a href="{{ route('company.nominations') }}" class="small fw-bold text-success text-decoration-none d-flex align-items-center justify-content-between">
                    <span>سجل المرشحين الكامل</span>
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>
        </div>

        <!-- طلبات جديدة قيد المراجعة -->
        <div class="bento-card">
            <div class="bento-card-header d-flex align-items-center justify-content-between">
                <h3 class="bento-card-title mb-0">طلبات جديدة</h3>
                <div class="bento-card-icon bento-icon-warning">
                    <i class="fas fa-bell"></i>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-end mt-2">
                <div class="bento-stat">{{ $stats['new_applications'] ?? 0 }}</div>
                <div class="badge rounded-pill bg-warning bg-opacity-10 text-warning px-3 py-1.5 mb-2 fw-bold" style="color: #d97706 !important;">
                    بانتظار المراجعة <i class="fas fa-clock ms-1"></i>
                </div>
            </div>
            <div class="bento-desc mt-2 text-muted small">طلبات ترشيح جديدة تتطلب اتخاذ قرار</div>
            <div class="mt-3 pt-2 border-top border-light">
                <a href="{{ route('company.nominations') }}" class="small fw-bold text-warning text-decoration-none d-flex align-items-center justify-content-between" style="color: #d97706 !important;">
                    <span>مراجعة الطلبات الجديدة</span>
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>
        </div>
        
        <!-- مسح الباركود (معرض التوظيف) -->
        <div class="bento-card">
            <div class="bento-card-header d-flex align-items-center justify-content-between">
                <h3 class="bento-card-title mb-0">زوار الفعاليات (QR)</h3>
                <div class="bento-card-icon bento-icon-gold">
                    <i class="fas fa-qrcode"></i>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-end mt-2">
                <div class="bento-stat">{{ $stats['qr_scans'] ?? 0 }}</div>
                <div class="badge rounded-pill bg-info bg-opacity-10 text-info px-3 py-1.5 mb-2 fw-bold">
                    المعارض والفعاليات <i class="fas fa-id-badge ms-1"></i>
                </div>
            </div>
            <div class="bento-desc mt-2 text-muted small">عمليات مسح كود الخريجين بأجنحة الفعاليات</div>
            <div class="mt-3 pt-2 border-top border-light">
                <a href="{{ route('company.job-fairs.index') }}" class="small fw-bold text-primary text-decoration-none d-flex align-items-center justify-content-between">
                    <span>فتح أجنحة الفعاليات والماسح</span>
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- شبكة المحتوى الرئيسية (Main Dashboard Layout) -->
    <div class="row g-4">
        <!-- العمود الأيمن: أحدث فرص العمل والمرشحين (8 أعمدة) -->
        <div class="col-lg-8">
            <!-- 1. بطاقة أحدث فرص العمل للشركة -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: rgba(30, 58, 138, 0.08); color: #1e3a8a;">
                            <i class="fas fa-briefcase fs-6"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark fs-6">أحدث فرص العمل المنشورة</h5>
                            <small class="text-muted">الوظائف والفرص التدريبية المعروضة على منصة جامعة طرابلس</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('job-opportunities.create') }}" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold" style="background: #1e3a8a;">
                            <i class="fas fa-plus me-1"></i> نشر فرصة جديدة
                        </a>
                        <a href="{{ route('job-opportunities.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                            عرض الكل
                        </a>
                    </div>
                </div>

                <div class="card-body p-0">
                    @if(isset($recentJobs) && $recentJobs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                                <thead class="table-light text-muted small">
                                    <tr>
                                        <th class="ps-4 py-3">المسمى الوظيفي</th>
                                        <th class="py-3">النوع والدوام</th>
                                        <th class="py-3 text-center">المقاعد</th>
                                        <th class="py-3 text-center">المتقدمون</th>
                                        <th class="py-3 text-center">الحالة</th>
                                        <th class="py-3">آخر موعد</th>
                                        <th class="pe-4 py-3 text-center">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentJobs as $job)
                                        <tr>
                                            <td class="ps-4 py-3">
                                                <a href="{{ route('job-opportunities.show', $job->id) }}" class="fw-bold text-dark text-decoration-none d-block">
                                                    {{ $job->title }}
                                                </a>
                                                <small class="text-muted"><i class="fas fa-map-marker-alt me-1 text-danger"></i>{{ $job->location ?? 'طرابلس' }}</small>
                                            </td>
                                            <td>
                                                @if($job->type === 'job')
                                                    <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary px-2.5 py-1">وظيفة</span>
                                                @elseif($job->type === 'training')
                                                    <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning px-2.5 py-1" style="color: #d97706 !important;">تدريب</span>
                                                @else
                                                    <span class="badge rounded-pill bg-info bg-opacity-10 text-info px-2.5 py-1">تدريب عملي</span>
                                                @endif
                                                <span class="badge bg-light text-secondary border px-2 py-1 ms-1">
                                                    {{ $job->contract_type === 'full_time' ? 'دوام كامل' : ($job->contract_type === 'part_time' ? 'دوام جزئي' : 'عقد') }}
                                                </span>
                                            </td>
                                            <td class="text-center fw-bold">{{ $job->seats }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('company.nominations', ['opportunity_id' => $job->id]) }}" class="badge rounded-pill bg-success bg-opacity-10 text-success text-decoration-none px-2.5 py-1.5 fw-bold">
                                                    <i class="fas fa-users me-1"></i>{{ $job->nominations_count }} متقدم
                                                </a>
                                            </td>
                                            <td class="text-center">
                                                @if($job->status === 'open' && (!$job->application_deadline || $job->application_deadline >= now()))
                                                    <span class="badge rounded-pill bg-success text-white px-2.5 py-1">نشطة ومفتوحة</span>
                                                @else
                                                    <span class="badge rounded-pill bg-secondary text-white px-2.5 py-1">مغلقة</span>
                                                @endif
                                            </td>
                                            <td class="small text-muted">
                                                {{ $job->application_deadline ? \Carbon\Carbon::parse($job->application_deadline)->format('Y/m/d') : 'مفتوح' }}
                                            </td>
                                            <td class="pe-4 text-center">
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('job-opportunities.show', $job->id) }}" class="btn btn-outline-light text-dark border" title="عرض التفاصيل">
                                                        <i class="fas fa-eye text-primary"></i>
                                                    </a>
                                                    <a href="{{ route('job-opportunities.edit', $job->id) }}" class="btn btn-outline-light text-dark border" title="تعديل">
                                                        <i class="fas fa-pen text-warning"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5 px-4">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 72px; height: 72px; background: rgba(30, 58, 138, 0.06); color: #1e3a8a; font-size: 1.8rem;">
                                <i class="fas fa-briefcase"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">لم تقم بنشر أي فرصة عمل حتى الآن</h6>
                            <p class="text-muted small mb-4 mx-auto" style="max-width: 480px;">
                                ابدأ بنشر شواغركم الوظيفية وبرامج التدريب للوصول المباشر إلى قاعدة بيانات تضم نخبة خريجي كليات جامعة طرابلس في كافة التخصصات.
                            </p>
                            <a href="{{ route('job-opportunities.create') }}" class="btn btn-primary rounded-pill px-4 py-2.5 fw-bold shadow-sm" style="background: #1e3a8a;">
                                <i class="fas fa-plus-circle me-1.5"></i> إنشاء ونشر أول فرصة عمل الآن
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- 2. بطاقة أحدث المرشحين والمتقدمين -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: rgba(16, 185, 129, 0.08); color: #10b981;">
                            <i class="fas fa-user-check fs-6"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark fs-6">أحدث المرشحين والمتقدمين</h5>
                            <small class="text-muted">الخريجون المرشحون لشغل الشواغر الوظيفية بشركتكم</small>
                        </div>
                    </div>
                    <a href="{{ route('company.nominations') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                        سجل المرشحين بالكامل <i class="fas fa-arrow-left ms-1"></i>
                    </a>
                </div>

                <div class="card-body p-0">
                    @if(isset($recentNominations) && $recentNominations->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                                <thead class="table-light text-muted small">
                                    <tr>
                                        <th class="ps-4 py-3">الخريج</th>
                                        <th class="py-3">الوظيفة المستهدفة</th>
                                        <th class="py-3">تاريخ الترشيح</th>
                                        <th class="py-3 text-center">الحالة</th>
                                        <th class="pe-4 py-3 text-center">الإجراء</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentNominations as $nomination)
                                        <tr>
                                            <td class="ps-4 py-3">
                                                <div class="d-flex align-items-center gap-2.5">
                                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0" style="width: 38px; height: 38px; background: linear-gradient(135deg, #1e3a8a, #3b82f6); font-size: 0.85rem;">
                                                        {{ mb_substr($nomination->graduate->full_name ?? $nomination->graduate->name ?? 'خ', 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.92rem;">
                                                            {{ $nomination->graduate->full_name ?? $nomination->graduate->name ?? 'خريج' }}
                                                        </h6>
                                                        <small class="text-muted">
                                                            {{ $nomination->graduate->specialization ?? 'جامعة طرابلس' }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="fw-semibold text-dark">{{ $nomination->jobOpportunity->title ?? 'وظيفة عامة' }}</span>
                                            </td>
                                            <td class="small text-muted">
                                                {{ $nomination->nominated_at ? \Carbon\Carbon::parse($nomination->nominated_at)->diffForHumans() : 'مؤخراً' }}
                                            </td>
                                            <td class="text-center">
                                                @php
                                                    $statusLabels = [
                                                        'pending' => ['قيد المراجعة', 'warning'],
                                                        'nominated' => ['تم الترشيح', 'info'],
                                                        'under_review' => ['تحت الدراسة', 'primary'],
                                                        'interview_scheduled' => ['مقابلة مجدولة', 'primary'],
                                                        'accepted' => ['مقبول للتوظيف', 'success'],
                                                        'rejected' => ['مرفوض', 'danger'],
                                                    ];
                                                    $st = $statusLabels[$nomination->status] ?? [$nomination->status, 'secondary'];
                                                @endphp
                                                <span class="badge rounded-pill bg-{{ $st[1] }} bg-opacity-10 text-{{ $st[1] }} px-2.5 py-1 fw-bold">
                                                    {{ $st[0] }}
                                                </span>
                                            </td>
                                            <td class="pe-4 text-center">
                                                <a href="{{ route('company.nominations.show', $nomination->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                    <i class="fas fa-id-card me-1"></i> عرض الملف
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5 px-4">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px; background: rgba(16, 185, 129, 0.06); color: #10b981; font-size: 1.6rem;">
                                <i class="fas fa-users"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">لا توجد طلبات ترشيح جديدة حالياً</h6>
                            <p class="text-muted small mb-0 mx-auto" style="max-width: 440px;">
                                ستظهر هنا ملفات وسير الخريجين فور ترشيحهم أو تقدمهم إلى الشواغر الوظيفية الفعالة لديكم.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- العمود الأيسر: جناح المعرض، هوية الشركة والإرشادات (4 أعمدة) -->
        <div class="col-lg-4">
            <!-- 1. بطاقة بوابة معرض التوظيف السنوي -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" style="background: linear-gradient(145deg, #0b1f3a, #15386a); color: white;">
                <div class="card-body p-4 position-relative">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background: rgba(245, 158, 11, 0.2); border: 1px solid rgba(245, 158, 11, 0.4); color: #f59e0b;">
                            <i class="fas fa-qrcode me-1"></i> بوابة الفعاليات الذكية
                        </span>
                        <i class="fas fa-building text-white-50 fs-4"></i>
                    </div>

                    <h5 class="fw-bold text-white mb-2 fs-6">أجنحة شركتكم في المعارض والفعاليات</h5>
                    <p class="text-white-50 small mb-4" style="line-height: 1.6;">
                        استخدم الماسح الميداني لتسجيل زيارات الخريجين فورياً، وتحميل بطاقات الـ QR الرسمية المعتمدة لأجنحتكم.
                    </p>

                    <div class="d-grid gap-2">
                        <a href="{{ route('company.job-fairs.index') }}" class="btn btn-warning text-dark fw-bold rounded-3 py-2.5 d-flex align-items-center justify-content-center gap-2 shadow-sm" style="background: #f59e0b; border: none;">
                            <i class="fas fa-camera"></i>
                            <span>فتح ماسح الباركود الميداني</span>
                        </a>
                        <a href="{{ route('company.job-fairs.index') }}" class="btn btn-outline-light rounded-3 py-2 small d-flex align-items-center justify-content-center gap-2" style="border-color: rgba(255,255,255,0.25);">
                            <i class="fas fa-id-badge"></i>
                            <span>إدارة الجناح واستعراض الزوار</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- 2. بطاقة ملخص هوية الشركة -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-dark mb-0 fs-6">
                        <i class="fas fa-id-card text-primary me-1.5"></i>ملف الشركة المسجل
                    </h6>
                    <a href="{{ route('company.profile') }}" class="small text-primary text-decoration-none fw-bold">
                        تعديل <i class="fas fa-pen small ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
                        @if($company && $company->logo_path)
                            <img src="{{ asset('storage/' . $company->logo_path) }}" alt="{{ $company->name }}" class="rounded-3 border" style="width: 52px; height: 52px; object-fit: contain; padding: 2px;">
                        @else
                            <div class="rounded-3 d-flex align-items-center justify-content-center fw-bold text-primary" style="width: 52px; height: 52px; background: rgba(30, 58, 138, 0.08); font-size: 1.4rem;">
                                <i class="fas fa-building"></i>
                            </div>
                        @endif
                        <div class="overflow-hidden">
                            <h6 class="fw-bold text-dark mb-0 text-truncate">{{ $company->name ?? auth()->user()->name }}</h6>
                            <small class="text-muted d-block text-truncate">{{ $company->email ?? auth()->user()->email }}</small>
                        </div>
                    </div>

                    <ul class="list-unstyled mb-0 small">
                        <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span class="text-muted"><i class="fas fa-industry me-1.5 text-secondary"></i>مجال العمل:</span>
                            <span class="fw-bold text-dark">{{ $company->industry ?? 'غير محدد' }}</span>
                        </li>
                        <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span class="text-muted"><i class="fas fa-map-marker-alt me-1.5 text-secondary"></i>المدينة / المقر:</span>
                            <span class="fw-bold text-dark">{{ $company->address ?? 'طرابلس، ليبيا' }}</span>
                        </li>
                        <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span class="text-muted"><i class="fas fa-phone me-1.5 text-secondary"></i>الهاتف:</span>
                            <span class="fw-semibold text-dark font-monospace" dir="ltr">{{ $company->phone ?? 'غير متوفر' }}</span>
                        </li>
                        <li class="d-flex justify-content-between align-items-center pt-2">
                            <span class="text-muted"><i class="fas fa-certificate me-1.5 text-warning"></i>حالة الاعتماد:</span>
                            <span class="badge rounded-pill bg-success bg-opacity-10 text-success px-2.5 py-1 fw-bold">
                                <i class="fas fa-check-circle me-1"></i>شراكة معتمدة ونشطة
                            </span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- 3. إرشادات سريعة لمدراء الموارد البشرية -->
            <div class="card border-0 shadow-sm rounded-4" style="background: #f8fafc;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="fas fa-lightbulb text-warning fs-5"></i>
                        <h6 class="fw-bold text-dark mb-0">نصائح توظيف ناجحة</h6>
                    </div>
                    <p class="text-muted small mb-3" style="line-height: 1.7;">
                        للحصول على أفضل الكفاءات المتوافقة مع متطلبات وظائفكم:
                    </p>
                    <ul class="text-muted small ps-3 mb-0" style="line-height: 1.8;">
                        <li>حدد المهارات التقنية والتخصص الأكاديمي المطلوب بدقة.</li>
                        <li>تابع حالة المرشحين بانتظام وحدّث حالتهم عبر نظام التوظيف (ATS).</li>
                        <li>فريق مكتب تدريب وتأهيل الخريجين بجامعة طرابلس جاهز دائماً لتسهيل المقابلات والتنسيق الأكاديمي.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection