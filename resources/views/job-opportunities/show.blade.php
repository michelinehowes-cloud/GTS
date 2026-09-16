@extends('layouts.app')

@section('title', 'تفاصيل فرصة العمل - ' . $opportunity->title)

@section('content')
<div class="container-fluid py-4">
    <div class="row g-4">
        <div class="col-12">
            <!-- بطاقة العنوان الرئيسية -->
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-2" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
                <div class="card-body p-4 p-md-5 text-white position-relative">
                    <div class="position-absolute top-0 end-0 opacity-10 p-4" style="pointer-events: none;">
                        <i class="fas fa-briefcase fa-8x" style="transform: rotate(-15deg);"></i>
                    </div>
                    
                    <div class="row align-items-center position-relative z-index-1">
                        <div class="col-lg-8 mb-4 mb-lg-0">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-white rounded-circle d-flex align-items-center justify-content-center shadow" style="width: 80px; height: 80px;">
                                        <i class="fas fa-briefcase fa-3x text-primary"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-4 me-4">
                                    <h1 class="h2 mb-3 fw-bold text-white">{{ $opportunity->title }}</h1>
                                    <div class="d-flex flex-wrap gap-2 gap-md-3">
                                        <span class="badge rounded-pill" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); padding: 8px 16px; font-size: 0.9rem; font-weight: 500;">
                                            <i class="fas fa-building me-1"></i>
                                            {{ $opportunity->company->name }}
                                        </span>
                                        <span class="badge rounded-pill" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); padding: 8px 16px; font-size: 0.9rem; font-weight: 500;">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            {{ $opportunity->location }}
                                        </span>
                                        <span class="badge rounded-pill" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); padding: 8px 16px; font-size: 0.9rem; font-weight: 500;">
                                            <i class="fas fa-users me-1"></i>
                                            {{ $opportunity->seats }} مقاعد
                                        </span>
                                        @if($opportunity->status === 'pending')
                                            <span class="badge rounded-pill bg-warning text-dark px-3 py-2 fw-bold" style="font-size: 0.9rem;">
                                                <i class="fas fa-clock me-1"></i>بانتظار الاعتماد والمراجعة
                                            </span>
                                        @elseif($opportunity->status === 'rejected')
                                            <span class="badge rounded-pill bg-danger text-white px-3 py-2 fw-bold" style="font-size: 0.9rem;">
                                                <i class="fas fa-times-circle me-1"></i>مرفوضة
                                            </span>
                                        @elseif($opportunity->status === 'open')
                                            <span class="badge rounded-pill bg-success text-white px-3 py-2 fw-bold" style="font-size: 0.9rem;">
                                                <i class="fas fa-check-circle me-1"></i>مفتوحة للتقديم
                                            </span>
                                        @else
                                            <span class="badge rounded-pill bg-secondary text-white px-3 py-2" style="font-size: 0.9rem;">
                                                {{ $opportunity->status }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="d-flex flex-column gap-2">
                                @if(in_array(auth()->user()->role, ['admin', 'partnership_officer', 'career_guidance_officer']) && $opportunity->status === 'pending')
                                    <form action="{{ route('job-opportunities.approve', $opportunity->id) }}" method="POST" class="d-grid m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-success fw-bold rounded-pill py-2 shadow-sm" onclick="return confirm('هل أنت متأكد من اعتماد فرصة العمل ونشرها رسمياً للخريجين؟')">
                                            <i class="fas fa-check-circle me-2"></i>اعتماد ونشر الفرصة الآن
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-danger fw-bold rounded-pill py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#rejectJobModal">
                                        <i class="fas fa-times-circle me-2"></i>رفض الفرصة
                                    </button>
                                @endif

                                <a href="{{ route('job-opportunities.nominations', $opportunity->id) }}" class="btn btn-light text-primary fw-bold rounded-pill py-2 shadow-sm">
                                    <i class="fas fa-users me-2"></i>الترشيحات ({{ $nominationsCount['total'] ?? 0 }})
                                </a>
                                @if(auth()->user()->role === 'partnership_officer')
                                <a href="{{ route('partnership.nominations') }}" class="btn rounded-pill py-2 shadow-sm" style="background: rgba(255,255,255,0.15); color: white; border: 1px solid rgba(255,255,255,0.3);">
                                    <i class="fas fa-list me-2"></i>جميع الترشيحات
                                </a>
                                @endif
                                @can('update', $opportunity)
                                <a href="{{ route('job-opportunities.edit', $opportunity->id) }}" class="btn btn-warning fw-bold rounded-pill py-2 shadow-sm">
                                    <i class="fas fa-edit me-2"></i>تعديل الفرصة
                                </a>
                                @endcan
                                <a href="{{ route('job-opportunities.index') }}" class="btn rounded-pill py-2" style="color: rgba(255,255,255,0.8); border: 1px solid rgba(255,255,255,0.2);">
                                    <i class="fas fa-arrow-right me-2"></i>رجوع للقائمة
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- شريط تنبيه حالة الاعتماد والمراجعة --}}
        @if($opportunity->status === 'pending')
            <div class="col-12">
                <div class="alert alert-warning border-warning border-opacity-25 rounded-4 p-3.5 shadow-sm mb-0">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-warning bg-opacity-20 text-warning d-flex align-items-center justify-content-center flex-shrink-0" style="width: 46px; height: 46px; font-size: 1.3rem;">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold text-dark">فرصة العمل قيد المراجعة والاعتماد</h6>
                                <p class="mb-0 text-secondary small">
                                    هذه الفرصة تم إنشاؤها من قبل الشركة وهي حالياً غير منشورة للخريجين لحين مراجعتها والموافقة عليها من قبل إدارة المنظومة.
                                </p>
                            </div>
                        </div>
                        @if(in_array(auth()->user()->role, ['admin', 'partnership_officer', 'career_guidance_officer']))
                            <div class="d-flex align-items-center gap-2">
                                <form action="{{ route('job-opportunities.approve', $opportunity->id) }}" method="POST" class="d-inline m-0">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm rounded-pill px-3 py-2 fw-bold text-nowrap" onclick="return confirm('هل أنت متأكد من اعتماد الفرصة ونشرها؟')">
                                        <i class="fas fa-check me-1"></i>اعتماد ونشر
                                    </button>
                                </form>
                                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-2 fw-bold text-nowrap" data-bs-toggle="modal" data-bs-target="#rejectJobModal">
                                    <i class="fas fa-times me-1"></i>رفض
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @elseif($opportunity->status === 'rejected')
            <div class="col-12">
                <div class="alert alert-danger border-danger border-opacity-25 rounded-4 p-3.5 shadow-sm mb-0">
                    <div class="d-flex align-items-start gap-3">
                        <div class="rounded-circle bg-danger bg-opacity-20 text-danger d-flex align-items-center justify-content-center flex-shrink-0 mt-1" style="width: 44px; height: 44px; font-size: 1.25rem;">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-bold text-danger">تم رفض نشر هذه الفرصة</h6>
                            @if($opportunity->rejection_reason)
                                <div class="bg-white bg-opacity-75 p-2.5 rounded-3 border border-danger border-opacity-25 my-2 text-dark small">
                                    <strong>سبب الرفض وملاحظات الإدارة:</strong> {{ $opportunity->rejection_reason }}
                                </div>
                            @endif
                            <p class="mb-0 text-secondary small">
                                يمكن للشركة تعديل بيانات الفرصة واستيفاء الملاحظات أعلاه، وعند الحفظ سيتم إرسالها مجدداً للمراجعة والاعتماد.
                            </p>
                        </div>
                        @can('update', $opportunity)
                            <a href="{{ route('job-opportunities.edit', $opportunity->id) }}" class="btn btn-warning btn-sm rounded-pill px-3 py-2 fw-bold text-nowrap align-self-center">
                                <i class="fas fa-edit me-1"></i>تعديل الفرصة الآن
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
        @endif

        @if(session('success'))
            <div class="col-12">
                <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-4 border-0 mb-0">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle fa-2x me-3 text-success"></i>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-bold text-success">تم بنجاح!</h6>
                            <p class="mb-0 text-dark">{{ session('success') }}</p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            </div>
        @endif

        <!-- العمود الأيمن - المعلومات الرئيسية -->
        <div class="col-lg-8">
            <div class="d-flex flex-column gap-4">
                
                <!-- وصف الفرصة -->
                <div class="card shadow-sm border-0 rounded-4 h-100 transition-all hover-shadow">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                        <h5 class="fw-bold text-dark mb-0 d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3 d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            وصف الفرصة
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-secondary mb-0" style="line-height: 1.8; font-size: 1.05rem;">
                            {{ $opportunity->description }}
                        </p>
                    </div>
                </div>

                <!-- المتطلبات والمهارات -->
                <div class="card shadow-sm border-0 rounded-4 transition-all hover-shadow">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                        <h5 class="fw-bold text-dark mb-0 d-flex align-items-center">
                            <div class="bg-info bg-opacity-10 text-info rounded-circle p-2 me-3 d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-tasks"></i>
                            </div>
                            المتطلبات والمهارات
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            @if($opportunity->required_specializations && count($opportunity->required_specializations) > 0)
                            <div class="col-md-6">
                                <h6 class="fw-bold text-muted mb-3"><i class="fas fa-graduation-cap me-2"></i>التخصصات المطلوبة</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($opportunity->required_specializations as $specialization)
                                        <span class="badge rounded-pill px-3 py-2 fw-normal" style="background: #e0e7ff; color: #4338ca; border: 1px solid #c7d2fe;">
                                            {{ $specialization }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            @if($opportunity->required_skills && count($opportunity->required_skills) > 0)
                            <div class="col-md-6">
                                <h6 class="fw-bold text-muted mb-3"><i class="fas fa-cogs me-2"></i>المهارات المطلوبة</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($opportunity->required_skills as $skill)
                                        <span class="badge rounded-pill px-3 py-2 fw-normal" style="background: #dbeafe; color: #1d4ed8; border: 1px solid #bfdbfe;">
                                            {{ $skill }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            @if($opportunity->required_experience)
                            <div class="col-12 mt-4">
                                <h6 class="fw-bold text-muted mb-2"><i class="fas fa-briefcase me-2"></i>الخبرة المطلوبة</h6>
                                <p class="text-dark mb-0 bg-light p-3 rounded-3">{{ $opportunity->required_experience }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- المتطلبات العامة -->
                @if($opportunity->requirements)
                <div class="card shadow-sm border-0 rounded-4 transition-all hover-shadow">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                        <h5 class="fw-bold text-dark mb-0 d-flex align-items-center">
                            <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-2 me-3 d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                            المتطلبات العامة
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-secondary mb-0 bg-light p-3 rounded-3" style="line-height: 1.7;">{{ $opportunity->requirements }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- العمود الأيسر - التواريخ والإحصائيات -->
        <div class="col-lg-4">
            <div class="d-flex flex-column gap-4">
                
                <!-- التواريخ -->
                <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4">
                        <h5 class="fw-bold text-dark mb-0 d-flex align-items-center">
                            <div class="bg-success bg-opacity-10 text-success rounded-circle p-2 me-3 d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            التواريخ المهمة
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex align-items-center p-3 rounded-3" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
                                <div class="bg-white rounded-circle p-2 me-3 text-success shadow-sm">
                                    <i class="fas fa-play"></i>
                                </div>
                                <div>
                                    <div class="text-success fw-bold small mb-1">تاريخ البدء</div>
                                    <div class="text-dark fw-bold">{{ $opportunity->start_date ? $opportunity->start_date->format('Y-m-d') : 'غير محدد' }}</div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center p-3 rounded-3" style="background: #eff6ff; border: 1px solid #bfdbfe;">
                                <div class="bg-white rounded-circle p-2 me-3 text-primary shadow-sm">
                                    <i class="fas fa-flag-checkered"></i>
                                </div>
                                <div>
                                    <div class="text-primary fw-bold small mb-1">تاريخ الانتهاء</div>
                                    <div class="text-dark fw-bold">{{ $opportunity->end_date ? $opportunity->end_date->format('Y-m-d') : 'غير محدد' }}</div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center p-3 rounded-3" style="background: #fef2f2; border: 1px solid #fecaca;">
                                <div class="bg-white rounded-circle p-2 me-3 text-danger shadow-sm">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div>
                                    <div class="text-danger fw-bold small mb-1">آخر موعد للتقديم</div>
                                    <div class="text-dark fw-bold">{{ $opportunity->application_deadline ? $opportunity->application_deadline->format('Y-m-d') : 'غير محدد' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- الإحصائيات -->
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4">
                        <h5 class="fw-bold text-dark mb-0 d-flex align-items-center">
                            <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle p-2 me-3 d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-chart-pie"></i>
                            </div>
                            إحصائيات الترشيحات
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="bg-light rounded-3 p-3 text-center h-100 border border-light transition-all hover-shadow-sm">
                                    <h3 class="text-primary fw-bold mb-1">{{ $nominationsCount['total'] ?? 0 }}</h3>
                                    <span class="text-muted small">إجمالي المتقدمين</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="bg-light rounded-3 p-3 text-center h-100 border border-light transition-all hover-shadow-sm">
                                    <h3 class="text-success fw-bold mb-1">{{ $nominationsCount['accepted'] ?? 0 }}</h3>
                                    <span class="text-muted small">المقبولين</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="bg-light rounded-3 p-3 text-center h-100 border border-light transition-all hover-shadow-sm">
                                    <h3 class="text-warning fw-bold mb-1">{{ $nominationsCount['pending'] ?? 0 }}</h3>
                                    <span class="text-muted small">قيد المراجعة</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="bg-light rounded-3 p-3 text-center h-100 border border-light transition-all hover-shadow-sm">
                                    <h3 class="text-danger fw-bold mb-1">{{ $nominationsCount['rejected'] ?? 0 }}</h3>
                                    <span class="text-muted small">المرفوضين</span>
                                </div>
                            </div>
                        </div>
                        
                        <a href="{{ route('job-opportunities.nominations', $opportunity->id) }}" class="btn btn-outline-primary w-100 mt-4 rounded-pill fw-bold">
                            عرض قائمة المتقدمين بالكامل
                        </a>
                    </div>
                </div>

            </div>
        </div>
<!-- Modal رفض فرصة العمل -->
@if(in_array(auth()->user()->role, ['admin', 'partnership_officer', 'career_guidance_officer']) && $opportunity->status === 'pending')
<div class="modal fade" id="rejectJobModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-danger text-white rounded-top-4 py-3">
                <h5 class="modal-title fs-6 fw-bold">
                    <i class="fas fa-times-circle me-2"></i>رفض نشر فرصة العمل
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('job-opportunities.reject', $opportunity->id) }}">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-secondary small mb-3">
                        يرجى تدوين سبب الرفض أو التوجيهات المطلوبة. سيتم إشعار الشركة بذلك لتتمكن من تصحيح الفرصة وإعادة إرسالها.
                    </p>
                    <div class="alert alert-light border rounded-3 p-3 mb-3">
                        <div class="fw-bold text-dark mb-1">{{ $opportunity->title }}</div>
                        <small class="text-muted">الشركة: {{ $opportunity->company->name ?? 'غير محدد' }}</small>
                    </div>
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label fw-bold text-dark small">سبب الرفض والملاحظات <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" id="rejection_reason" rows="4" class="form-control rounded-3" placeholder="اكتب سبب الرفض والملاحظات التوجيهية للشركة..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">
                        <i class="fas fa-paper-plane me-1"></i>تأكيد الرفض وإشعار الشركة
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<style>
    .hover-shadow:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15)!important;
    }
    .hover-shadow-sm:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.25rem 0.5rem rgba(0,0,0,.05)!important;
    }
</style>
@endsection