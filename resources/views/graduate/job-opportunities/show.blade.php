@extends('layouts.app')

@section('title', $jobOpportunity->title)

@section('content')
<div class="container-fluid px-2 px-md-3">
    @php
        $contractTypeName = match($jobOpportunity->contract_type) {
            'full_time' => 'دوام كامل',
            'part_time' => 'دوام جزئي',
            'contract' => 'عقد عمل',
            default => 'عمل حر',
        };
    @endphp

    <!-- Unified Page Hero Banner -->
    <x-page-hero
        title="{{ $jobOpportunity->title }}"
        subtitle="{{ $jobOpportunity->company->name ?? 'شركة شريكة' }} • {{ $jobOpportunity->location ?? 'طرابلس' }}"
        icon="fas fa-briefcase"
        :breadcrumbs="[
            ['label' => 'منصة الخريجين', 'url' => route('graduate.dashboard')],
            ['label' => 'فرص العمل المتاحة', 'url' => route('graduate.job-opportunities.index')],
            ['label' => $jobOpportunity->title]
        ]"
        badge="{{ $contractTypeName }}"
        badgeIcon="fas fa-clock"
        :secondaryBadge="$jobOpportunity->salary ? $jobOpportunity->salary . ' د.ل' : ($jobOpportunity->status == 'open' ? 'مفتوحة للتقديم' : 'مغلقة')"
        secondaryBadgeIcon="fas fa-money-bill-wave"
    >
        <a href="{{ route('graduate.job-opportunities.index') }}" class="btn btn-warning text-dark fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem; transition: transform 0.2s ease;">
            <i class="fas fa-arrow-right fs-6"></i>
            <span>العودة للفرص</span>
        </a>
        <a href="{{ route('graduate.dashboard') }}" class="btn btn-light bg-white text-primary fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem; transition: transform 0.2s ease;">
            <i class="fas fa-home fs-6"></i>
            <span>لوحة التحكم</span>
        </a>
    </x-page-hero>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- تفاصيل الفرصة الرئيسية -->
        <div class="col-lg-8">
            <div class="card border-0 rounded-4 shadow-sm mb-4" style="background: #ffffff;">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="card-title mb-0 text-dark fw-bold fs-6">
                        <i class="fas fa-info-circle text-primary me-2"></i>تفاصيل فرصة العمل
                    </h5>
                    @if($nomination)
                        @if($nomination->status == 'accepted')
                            <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0;">مقبول</span>
                        @elseif($nomination->status == 'interview_scheduled')
                            <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe;">مقابلة مجدولة</span>
                        @elseif($nomination->status == 'rejected' || $nomination->status == 'withdrawn')
                            <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca;">مرفوض/ملغي</span>
                        @else
                            <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background: #fffbeb; color: #d97706; border: 1px solid #fde68a;">قيد المراجعة</span>
                        @endif

                        @if($nomination->final_status && $nomination->final_status !== 'in_progress')
                            @if($nomination->final_status == 'hired')
                                <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0;">
                                    <i class="fas fa-user-check me-1"></i>تم التوظيف
                                </span>
                            @elseif($nomination->final_status == 'not_hired')
                                <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca;">
                                    <i class="fas fa-user-times me-1"></i>لم يتم التوظيف
                                </span>
                            @endif
                        @endif
                    @endif
                </div>
                <div class="card-body p-4">
                    <!-- تفاصيل سريعة في شبكة بينتو أنيقة -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-4 col-sm-6">
                            <div class="p-3 border rounded-4 bg-light bg-opacity-50 h-100">
                                <div class="small text-muted mb-1 d-flex align-items-center gap-1.5"><i class="fas fa-tag text-primary"></i> نوع الفرصة</div>
                                <div class="fw-bold text-dark fs-6">{{ $jobOpportunity->type == 'job' ? 'وظيفة' : ($jobOpportunity->type == 'training' ? 'تدريب' : 'تدريب عملي') }}</div>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-6">
                            <div class="p-3 border rounded-4 bg-light bg-opacity-50 h-100">
                                <div class="small text-muted mb-1 d-flex align-items-center gap-1.5"><i class="fas fa-clock text-info"></i> نوع العقد</div>
                                <div class="fw-bold text-dark fs-6">{{ $contractTypeName }}</div>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-6">
                            <div class="p-3 border rounded-4 bg-light bg-opacity-50 h-100">
                                <div class="small text-muted mb-1 d-flex align-items-center gap-1.5"><i class="fas fa-map-marker-alt text-danger"></i> الموقع</div>
                                <div class="fw-bold text-dark fs-6">{{ $jobOpportunity->location ?? 'غير محدد' }}</div>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-6">
                            <div class="p-3 border rounded-4 bg-light bg-opacity-50 h-100">
                                <div class="small text-muted mb-1 d-flex align-items-center gap-1.5"><i class="fas fa-users text-primary"></i> المقاعد المتاحة</div>
                                <div class="fw-bold text-dark fs-6">{{ $jobOpportunity->seats ?? 1 }} مقاعد</div>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-6">
                            <div class="p-3 border rounded-4 bg-light bg-opacity-50 h-100">
                                <div class="small text-muted mb-1 d-flex align-items-center gap-1.5"><i class="fas fa-calendar-alt text-warning"></i> آخر موعد للتقديم</div>
                                <div class="fw-bold text-dark fs-6">{{ $jobOpportunity->application_deadline ? $jobOpportunity->application_deadline->format('Y-m-d') : 'مفتوح للتسجيل' }}</div>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-6">
                            <div class="p-3 border rounded-4 bg-light bg-opacity-50 h-100">
                                <div class="small text-muted mb-1 d-flex align-items-center gap-1.5"><i class="fas fa-money-bill-wave text-success"></i> الراتب المتوقع</div>
                                <div class="fw-bold text-success fs-6">{{ $jobOpportunity->salary ? $jobOpportunity->salary . ' د.ل' : 'حسب الاتفاق' }}</div>
                            </div>
                        </div>

                        @if($jobOpportunity->start_date && $jobOpportunity->end_date)
                        <div class="col-12">
                            <div class="p-3 border rounded-4 bg-light bg-opacity-50 d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="small text-muted d-flex align-items-center gap-2">
                                    <i class="fas fa-business-time text-primary"></i>
                                    <span>فترة الفرصة:</span>
                                </div>
                                <div class="fw-bold text-dark small">
                                    من <span class="badge bg-white text-dark border px-2 py-1 mx-1">{{ $jobOpportunity->start_date->format('Y-m-d') }}</span>
                                    إلى <span class="badge bg-white text-dark border px-2 py-1 mx-1">{{ $jobOpportunity->end_date->format('Y-m-d') }}</span>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- الوصف الوظيفي -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3 d-flex align-items-center gap-2">
                            <i class="fas fa-align-right text-primary"></i>الوصف والمهام
                        </h6>
                        <div class="p-3 bg-light rounded-4 text-dark lh-lg border border-light-subtle">{!! nl2br(e($jobOpportunity->description)) !!}</div>
                    </div>

                    <!-- المتطلبات والمهارات المطلوبة -->
                    @if((!empty($jobOpportunity->required_specializations) && count($jobOpportunity->required_specializations) > 0) || (!empty($jobOpportunity->required_skills) && count($jobOpportunity->required_skills) > 0) || !empty($jobOpportunity->required_experience))
                    <div class="mb-4">
                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3 d-flex align-items-center gap-2">
                            <i class="fas fa-cogs text-primary"></i>المتطلبات التخصصية والمهارات
                        </h6>
                        <div class="p-3 bg-light rounded-4 border border-light-subtle">
                            @if(!empty($jobOpportunity->required_specializations) && count($jobOpportunity->required_specializations) > 0)
                                <div class="mb-3">
                                    <div class="small text-muted fw-bold mb-2"><i class="fas fa-graduation-cap me-1.5 text-primary"></i>التخصصات المؤهلة للتقديم:</div>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($jobOpportunity->required_specializations as $spec)
                                            <span class="badge rounded-pill px-3 py-2 fw-normal" style="background: #e0e7ff; color: #4338ca; border: 1px solid #c7d2fe; font-size: 0.82rem;">
                                                {{ $spec }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if(!empty($jobOpportunity->required_skills) && count($jobOpportunity->required_skills) > 0)
                                <div class="mb-3">
                                    <div class="small text-muted fw-bold mb-2"><i class="fas fa-star me-1.5 text-warning"></i>المهارات والتقنيات المطلوبة:</div>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($jobOpportunity->required_skills as $skill)
                                            <span class="badge rounded-pill px-3 py-2 fw-normal" style="background: #dbeafe; color: #1d4ed8; border: 1px solid #bfdbfe; font-size: 0.82rem;">
                                                {{ $skill }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if(!empty($jobOpportunity->required_experience))
                                <div>
                                    <div class="small text-muted fw-bold mb-1"><i class="fas fa-briefcase me-1.5 text-info"></i>الخبرة المطلوبة:</div>
                                    <div class="text-dark fw-semibold small bg-white p-2.5 rounded-3 border border-light-subtle">{{ $jobOpportunity->required_experience }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- المتطلبات والشروط العامة -->
                    @if($jobOpportunity->requirements)
                        <div class="mb-4">
                            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3 d-flex align-items-center gap-2">
                                <i class="fas fa-list-check text-primary"></i>الشروط والمتطلبات العامة
                            </h6>
                            <div class="p-3 bg-light rounded-4 text-dark lh-lg border border-light-subtle">{!! nl2br(e($jobOpportunity->requirements)) !!}</div>
                        </div>
                    @endif

                    <!-- المزايا والحوافز -->
                    @if($jobOpportunity->benefits)
                        <div class="mb-3">
                            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3 d-flex align-items-center gap-2">
                                <i class="fas fa-gift text-success"></i>المزايا والحوافز
                            </h6>
                            <div class="p-3 bg-light rounded-4 text-dark lh-lg border border-light-subtle">{!! nl2br(e($jobOpportunity->benefits)) !!}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- الجانب الأيسر: حالة الطلب أو نموذج التقديم -->
        <div class="col-lg-4">
            @if($nomination)
                <!-- حالة الترشيح الحالية -->
                <div class="card border-0 rounded-4 shadow-sm mb-4" style="background: #ffffff;">
                    <div class="card-header bg-white py-3 px-4 border-bottom">
                        <h5 class="card-title mb-0 text-dark fw-bold fs-6">
                            <i class="fas fa-clipboard-check text-primary me-2"></i>حالة طلبك
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted small">حالة الطلب:</span>
                            @if($nomination->status == 'accepted')
                                <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0;">مقبول</span>
                            @elseif($nomination->status == 'interview_scheduled')
                                <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe;">مقابلة مجدولة</span>
                            @elseif($nomination->status == 'rejected' || $nomination->status == 'withdrawn')
                                <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca;">مرفوض/ملغي</span>
                            @else
                                <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background: #fffbeb; color: #d97706; border: 1px solid #fde68a;">قيد المراجعة</span>
                            @endif
                        </div>

                        @if($nomination->nomination_type == 'self')
                            <div class="alert alert-info bg-info bg-opacity-10 border-0 rounded-4 text-info small mb-3">
                                <i class="fas fa-user me-1"></i> لقد قمت بالتقديم على هذه الفرصة بنفسك
                            </div>
                        @else
                            <div class="alert alert-success bg-success bg-opacity-10 border-0 rounded-4 text-success small mb-3">
                                <i class="fas fa-user-check me-1"></i> تم ترشيحك من قبل مكتب الإرشاد المهني
                            </div>
                        @endif

                        @if($nomination->interview_date)
                            <div class="p-3 bg-light rounded-4 border border-info border-opacity-25 mb-3">
                                <div class="fw-bold text-info small mb-2"><i class="fas fa-calendar-check me-1"></i> موعد المقابلة الشخصية</div>
                                <div class="small text-dark mb-1"><strong>التاريخ:</strong> {{ $nomination->interview_date->format('Y-m-d') }}</div>
                                @if($nomination->interview_time)
                                    <div class="small text-dark mb-1"><strong>الوقت:</strong> {{ $nomination->interview_time }}</div>
                                @endif
                                @if($nomination->interview_location)
                                    <div class="small text-dark"><strong>المكان:</strong> {{ $nomination->interview_location }}</div>
                                @endif
                            </div>
                        @endif

                        @if($nomination->notes)
                            <div class="small text-muted p-3 bg-light rounded-4 border border-light-subtle mb-3">
                                <strong class="text-dark">ملاحظات:</strong> {{ $nomination->notes }}
                            </div>
                        @endif

                        @if($nomination->status == 'pending')
                            <form action="{{ route('graduate.my-applications.cancel', $nomination->id) }}" method="POST"
                                onsubmit="return confirm('هل أنت متأكد من إلغاء هذا الترشيح؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger rounded-3 w-100 py-2.5 mt-2 fw-bold d-flex align-items-center justify-content-center gap-2">
                                    <i class="fas fa-times"></i>
                                    <span>إلغاء التقديم</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @else
                <!-- نموذج التقديم -->
                <div class="card border-0 rounded-4 shadow-sm mb-4" style="background: #ffffff;">
                    <div class="card-header bg-white py-3 px-4 border-bottom">
                        <h5 class="card-title mb-0 text-dark fw-bold fs-6">
                            <i class="fas fa-paper-plane text-primary me-2"></i>التقديم على الفرصة
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        @if(!$graduateData)
                            <div class="alert alert-warning bg-warning bg-opacity-10 border-0 rounded-4 text-dark small mb-0">
                                <i class="fas fa-exclamation-triangle me-1 text-warning"></i>
                                يجب إكمال بياناتك الشخصية والأكاديمية أولاً قبل التقديم.
                            </div>
                        @elseif($jobOpportunity->application_deadline && $jobOpportunity->application_deadline < now())
                            <div class="alert alert-danger bg-danger bg-opacity-10 border-0 rounded-4 text-danger small mb-0">
                                <i class="fas fa-times-circle me-1"></i>
                                انتهى موعد التقديم لهذه الفرصة.
                            </div>
                        @else
                            <form action="{{ route('graduate.job-opportunities.apply', $jobOpportunity->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-secondary">ملاحظات أو رسالة تقديمية (اختياري)</label>
                                    <textarea name="notes" class="form-control rounded-3" rows="4"
                                        placeholder="اكتب نبذة موجزة عن خبراتك ومؤهلاتك المناسبة لهذه الوظيفة..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary rounded-3 w-100 py-2.5 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                                    <i class="fas fa-paper-plane"></i>
                                    <span>تأكيد التقديم الآن</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endif

            <!-- بطاقة معلومات إضافية -->
            <div class="card border-0 rounded-4 shadow-sm" style="background: #ffffff;">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h6 class="card-title mb-0 text-dark fw-bold fs-6">
                        <i class="fas fa-info-circle text-primary me-2"></i>معلومات إضافية
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-3 pb-2 border-bottom border-light-subtle">
                        <small class="text-muted">تاريخ النشر:</small>
                        <small class="fw-bold text-dark">{{ $jobOpportunity->created_at->format('Y-m-d') }}</small>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">حالة الفرصة:</small>
                        <span class="badge rounded-pill px-3 py-1 small fw-bold" style="background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0;">
                            {{ $jobOpportunity->status == 'open' ? 'مفتوحة للتسجيل' : 'مغلقة' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection