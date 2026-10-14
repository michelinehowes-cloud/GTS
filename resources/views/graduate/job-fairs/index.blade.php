@extends('layouts.app')

@section('title', 'الفعاليات و المعارض')

@section('content')
<div class="container-fluid px-2 px-md-3">

    <!-- Unified Page Hero Banner -->
    <x-page-hero
        title="الفعاليات و المعارض"
        subtitle="استكشف المعارض والملتقيات المهنية والفعاليات والورش العلمية وسجّل حضورك فيها مباشرة"
        icon="fas fa-calendar-star"
        :breadcrumbs="[
            ['label' => 'منصة الخريجين', 'url' => route('graduate.dashboard')],
            ['label' => 'الفعاليات و المعارض']
        ]"
        :badge="$fairs->count() > 0 ? $fairs->count() . ' معارض متاحة' : null"
        badgeIcon="fas fa-calendar-check"
    >
        <a href="{{ route('graduate.dashboard') }}" class="btn btn-light bg-white text-primary fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem; transition: transform 0.2s ease;">
            <i class="fas fa-home fs-6"></i>
            <span>لوحة التحكم</span>
        </a>
    </x-page-hero>

    <!-- Quick Stats Overview -->
    <div class="row g-3 g-md-4 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card stat-card-widget border-0 rounded-4 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-bold small">المعارض المهنية</span>
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: rgba(37, 99, 235, 0.1); color: #2563eb;">
                        <i class="fas fa-building"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bolder text-primary mb-1">{{ $fairs->count() }}</div>
                <div class="text-muted small" style="font-size: 0.72rem;">معارض وفعاليات ملتقيات</div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card stat-card-widget border-0 rounded-4 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-bold small">ورش العمل والجلسات</span>
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: rgba(14, 165, 233, 0.1); color: #0ea5e9;">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bolder text-info mb-1">{{ $events->count() }}</div>
                <div class="text-muted small" style="font-size: 0.72rem;">برامج وورش علمية</div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card stat-card-widget border-0 rounded-4 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-bold small">المعارض المسجل بها</span>
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: rgba(16, 185, 129, 0.1); color: #10b981;">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bolder text-success mb-1">{{ $myRegistrations->count() }}</div>
                <div class="text-muted small" style="font-size: 0.72rem;">تذاكر رقمية معتمدة</div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card stat-card-widget border-0 rounded-4 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-bold small">فعالياتي المسجلة</span>
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bolder text-warning mb-1">{{ count($registeredEventIds) }}</div>
                <div class="text-muted small" style="font-size: 0.72rem;">مقاعد مؤكدة</div>
            </div>
        </div>
    </div>

    <!-- Navigation Pills for Sections -->
    <ul class="nav nav-pills mb-4 gap-2 flex-wrap" id="fairsTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold px-4 py-2.5 rounded-pill shadow-sm" id="fairs-list-tab" data-bs-toggle="pill" data-bs-target="#fairs-list" type="button" role="tab" aria-controls="fairs-list" aria-selected="true">
                <i class="fas fa-calendar-star me-1.5 text-warning"></i>
                المعارض المهنية المتاحة ({{ $fairs->count() }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold px-4 py-2.5 rounded-pill shadow-sm" id="events-list-tab" data-bs-toggle="pill" data-bs-target="#events-list" type="button" role="tab" aria-controls="events-list" aria-selected="false">
                <i class="fas fa-chalkboard-teacher me-1.5 text-info"></i>
                الفعاليات وورش العمل ({{ $events->count() }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold px-4 py-2.5 rounded-pill shadow-sm" id="my-tickets-tab" data-bs-toggle="pill" data-bs-target="#my-tickets" type="button" role="tab" aria-controls="my-tickets" aria-selected="false">
                <i class="fas fa-ticket-alt me-1.5 text-success"></i>
                تذاكري ومشاركاتي ({{ $myRegistrations->count() + count($registeredEventIds) }})
            </button>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="fairsTabsContent">

        <!-- ==================== TAB 1: المعارض المهنية ==================== -->
        <div class="tab-pane fade show active" id="fairs-list" role="tabpanel" aria-labelledby="fairs-list-tab">
            @if($fairs->isEmpty())
                <div class="card border-0 rounded-4 shadow-sm p-5 text-center bg-white">
                    <div class="mb-3 text-muted opacity-50">
                        <i class="fas fa-calendar-times fa-4x"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">لا توجد معارض توظيف معلنة حالياً</h5>
                    <p class="text-muted small mb-0">تابع هذه الصفحة باستمرار حيث يتم نشر المعارض والملتقيات المهنية السنوية تباعاً.</p>
                </div>
            @else
                <div class="row g-4">
                    @foreach($fairs as $fair)
                        @php
                            $isFairReg = $myRegistrations->has($fair->id);
                            $userReg = $isFairReg ? $myRegistrations[$fair->id] : null;
                        @endphp
                        <div class="col-12 col-xl-6">
                            <div class="card border-0 rounded-4 shadow-sm h-100 overflow-hidden d-flex flex-column" style="border: 1px solid rgba(0,0,0,0.06) !important;">
                                
                                <!-- Fair Header Ribbon -->
                                <div class="p-4 text-white position-relative" style="background: linear-gradient(135deg, #0d3882 0%, #1565c0 60%, #1e88e5 100%);">
                                    <div class="d-flex align-items-start justify-content-between gap-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-circle bg-white text-primary d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 58px; height: 58px; font-size: 1.5rem;">
                                                <i class="fas fa-building-columns"></i>
                                            </div>
                                            <div>
                                                <h4 class="fw-bold mb-1 text-white fs-5">{{ $fair->title }}</h4>
                                                @if($fair->subtitle)
                                                    <p class="text-white-50 small mb-0">{{ $fair->subtitle }}</p>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Badge status -->
                                        <div>
                                            @if($isFairReg)
                                                <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background: #10b981; color: #ffffff; font-size: 0.78rem;">
                                                    <i class="fas fa-check-circle me-1"></i> مسجل بالمعرض
                                                </span>
                                            @elseif($fair->is_upcoming)
                                                <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background: rgba(255,255,255,0.22); color: #ffffff; font-size: 0.78rem; border: 1px solid rgba(255,255,255,0.35);">
                                                    <i class="fas fa-clock me-1 text-warning"></i> قادم
                                                </span>
                                            @else
                                                <span class="badge rounded-pill px-3 py-1.5 fw-bold bg-success text-white" style="font-size: 0.78rem;">
                                                    <i class="fas fa-circle me-1" style="font-size: 0.55rem;"></i> جاري الآن
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Fair Details Body -->
                                <div class="card-body p-4 flex-grow-1 d-flex flex-column justify-content-between bg-white">
                                    <div>
                                        <!-- Meta Info Row -->
                                        <div class="d-flex flex-wrap gap-3 mb-3 text-muted small p-2.5 rounded-3 bg-light">
                                            <div class="d-flex align-items-center gap-1.5">
                                                <i class="fas fa-calendar-day text-primary"></i>
                                                <span class="fw-bold text-dark">{{ $fair->event_date ? $fair->event_date->format('Y-m-d') : 'قريباً' }}</span>
                                                @if($fair->event_date)
                                                    <span>({{ $fair->event_date->locale('ar')->isoFormat('dddd') }})</span>
                                                @endif
                                            </div>

                                            @if($fair->start_time)
                                                <div class="d-flex align-items-center gap-1.5">
                                                    <i class="fas fa-clock text-info"></i>
                                                    <span>{{ \Illuminate\Support\Str::substr($fair->start_time, 0, 5) }} @if($fair->end_time) - {{ \Illuminate\Support\Str::substr($fair->end_time, 0, 5) }} @endif</span>
                                                </div>
                                            @endif

                                            <div class="d-flex align-items-center gap-1.5">
                                                <i class="fas fa-map-marker-alt text-danger"></i>
                                                <span>{{ $fair->location }}</span>
                                            </div>
                                        </div>

                                        @if($fair->description)
                                            <p class="text-secondary small mb-3" style="line-height: 1.6;">
                                                {{ \Illuminate\Support\Str::limit(strip_tags($fair->description), 160) }}
                                            </p>
                                        @endif

                                        <!-- Features badges -->
                                        <div class="d-flex align-items-center gap-2 flex-wrap mb-4">
                                            <span class="badge bg-light text-primary border border-primary-subtle rounded-pill px-2.5 py-1 small">
                                                <i class="fas fa-building me-1"></i> {{ $fair->companies_count }} شركة مشاركة
                                            </span>
                                            <span class="badge bg-light text-info border border-info-subtle rounded-pill px-2.5 py-1 small">
                                                <i class="fas fa-chalkboard-teacher me-1"></i> {{ $fair->events_count }} ورشة وفعالية
                                            </span>
                                            <span class="badge bg-light text-success border border-success-subtle rounded-pill px-2.5 py-1 small">
                                                <i class="fas fa-lightbulb me-1"></i> {{ $fair->projects_count }} مشروع تخرج
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Registration Actions & Status Box -->
                                    <div class="border-top pt-3">
                                        @if($isFairReg)
                                            <div class="p-3 rounded-3 mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2" style="background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.25);">
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="fas fa-qrcode text-success fs-5"></i>
                                                    <div>
                                                        <div class="fw-bold text-success small">أنت مسجل في هذا المعرض</div>
                                                        <div class="text-muted" style="font-size: 0.72rem;">رقم بطاقتك: <strong>#{{ $userReg->registration_number }}</strong></div>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <a href="{{ route('job-fair.my-ticket', $userReg->id) }}" class="btn btn-sm btn-success rounded-pill px-3 fw-bold shadow-sm">
                                                        <i class="fas fa-ticket-alt me-1"></i> عرض وتنزيل تذكرتي
                                                    </a>
                                                    @if(!$userReg->attended)
                                                        <form action="{{ route('graduate.job-fairs.cancel', $fair->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من رغبتك في إلغاء تسجيلك في «{{ $fair->title }}»؟ سيتم تحرير مقعدك وإلغاء ورش العمل المرتبطة بهذا المعرض.');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.78rem;" title="إلغاء التسجيل في المعرض">
                                                                <i class="fas fa-times-circle me-1"></i> إلغاء تسجيلي
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>


                                            <div class="d-flex gap-2 flex-wrap">
                                                <a href="{{ route('job-fair.catalog', $fair->id) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold flex-fill">
                                                    <i class="fas fa-book-open me-1"></i> الكتيب الرقمي للفعاليات
                                                </a>
                                                <a href="{{ route('job-fair.public', $fair->id) }}" class="btn btn-light btn-sm rounded-pill px-3 fw-bold border flex-fill">
                                                    <i class="fas fa-external-link-alt me-1"></i> زيارة البوابة العامة
                                                </a>
                                            </div>
                                        @else
                                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                                @if($fair->can_register)
                                                    <button type="button" class="btn btn-primary-modern px-4 py-2 rounded-pill fw-bold shadow-sm flex-fill" data-bs-toggle="modal" data-bs-target="#registerFairModal{{ $fair->id }}">
                                                        <i class="fas fa-user-plus me-1.5"></i> التسجيل في المعرض الآن
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-secondary px-4 py-2 rounded-pill small flex-fill" disabled>
                                                        <i class="fas fa-lock me-1.5"></i> التسجيل مغلق حالياً
                                                    </button>
                                                @endif

                                                <a href="{{ route('job-fair.public', $fair->id) }}" class="btn btn-outline-primary px-3 py-2 rounded-pill fw-bold flex-fill text-nowrap">
                                                    <i class="fas fa-eye me-1"></i> استعراض التفاصيل
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Registration Modal for this Fair -->
                        @if(!$isFairReg && $fair->can_register)
                            <div class="modal fade" id="registerFairModal{{ $fair->id }}" tabindex="-1" aria-labelledby="registerFairModalLabel{{ $fair->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
                                        <div class="modal-header text-white p-3 px-4 border-0" style="background: linear-gradient(135deg, #0d3882 0%, #1565c0 100%);">
                                            <h5 class="modal-title fw-bold fs-6" id="registerFairModalLabel{{ $fair->id }}">
                                                <i class="fas fa-user-plus text-warning me-2"></i> التسجيل في {{ $fair->title }}
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                                        </div>

                                        <form action="{{ route('job-fair.register', $fair->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-body p-4">
                                                <div class="alert border-0 rounded-3 mb-3 p-3" style="background: #eff6ff; color: #1e40af; font-size: 0.82rem;">
                                                    <i class="fas fa-info-circle me-1.5"></i>
                                                    ستحصل فوراً على تذكرة دخول رقمية مزودة برمز QR ذكي مخصص لك لتسجيل الحضور وتسهيل التواصل مع الشركات.
                                                </div>

                                                <div class="p-3 bg-light rounded-3 mb-3 small">
                                                    <div class="row g-2">
                                                        <div class="col-6">
                                                            <span class="text-muted d-block" style="font-size: 0.72rem;">اسم الخريج:</span>
                                                            <strong class="text-dark">{{ auth()->user()->name }}</strong>
                                                        </div>
                                                        <div class="col-6">
                                                            <span class="text-muted d-block" style="font-size: 0.72rem;">التخصص:</span>
                                                            <strong class="text-dark">{{ auth()->user()->major ?? '—' }}</strong>
                                                        </div>
                                                        <div class="col-6">
                                                            <span class="text-muted d-block" style="font-size: 0.72rem;">الكلية:</span>
                                                            <strong class="text-dark">{{ auth()->user()->faculty ?? '—' }}</strong>
                                                        </div>
                                                        <div class="col-6">
                                                            <span class="text-muted d-block" style="font-size: 0.72rem;">سنة التخرج:</span>
                                                            <strong class="text-dark">{{ auth()->user()->graduation_year ?? '—' }}</strong>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="mb-0">
                                                    <label class="form-label fw-bold small text-dark">
                                                        القطاعات والوظائف التي تهمك <small class="text-muted fw-normal">(اختياري)</small>
                                                    </label>
                                                    <textarea name="interests" class="form-control rounded-3" rows="2" placeholder="مثال: تكنولوجيا المعلومات، هندسة، تسويق، إدارة أعمال..."></textarea>
                                                </div>
                                            </div>

                                            <div class="modal-footer bg-light border-0 p-3 px-4 d-flex justify-content-between">
                                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                                                <button type="submit" class="btn btn-primary-modern btn-sm rounded-pill px-4 fw-bold">
                                                    <i class="fas fa-check me-1"></i> تأكيد التسجيل واستلام التذكرة
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>

        <!-- ==================== TAB 2: الفعاليات وورش العمل ==================== -->
        <div class="tab-pane fade" id="events-list" role="tabpanel" aria-labelledby="events-list-tab">
            @if($events->isEmpty())
                <div class="card border-0 rounded-4 shadow-sm p-5 text-center bg-white">
                    <div class="mb-3 text-muted opacity-50">
                        <i class="fas fa-chalkboard-teacher fa-4x"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">لا توجد فعاليات أو ورش عمل مدرجة حالياً</h5>
                    <p class="text-muted small mb-0">سيتم إضافة وتحديث جدول الفعاليات المصاحبة والماستر كلاس قريباً.</p>
                </div>
            @else
                <!-- Filter Pills for Event Types -->
                <div class="d-flex align-items-center gap-2 mb-4 flex-wrap">
                    <button type="button" class="btn btn-sm btn-outline-primary active rounded-pill px-3.5 event-filter-btn" data-filter="all">
                        <i class="fas fa-border-all me-1"></i> جميع الفعاليات ({{ $events->count() }})
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3.5 event-filter-btn" data-filter="masterclass">
                        <i class="fas fa-graduation-cap me-1"></i> ماستر كلاس
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3.5 event-filter-btn" data-filter="workshop">
                        <i class="fas fa-laptop-code me-1"></i> ورش عمل
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3.5 event-filter-btn" data-filter="panel_discussion">
                        <i class="fas fa-comments me-1"></i> جلسات حوارية
                    </button>
                </div>

                <div class="row g-4" id="eventsContainer">
                    @foreach($events as $event)
                        @php
                            $isEventReg = in_array($event->id, $registeredEventIds);
                            $attendees = $event->attendees_count ?? 0;
                            $capacity = $event->capacity ?: 0;
                            $isFull = $capacity > 0 && $attendees >= $capacity;
                        @endphp
                        <div class="col-12 col-md-6 col-xl-4 event-card-col" data-type="{{ $event->type }}">
                            <div class="card border-0 rounded-4 shadow-sm h-100 overflow-hidden d-flex flex-column bg-white" style="border: 1px solid rgba(0,0,0,0.06) !important; transition: transform 0.2s ease, box-shadow 0.2s ease;">
                                
                                <div class="card-body p-4 flex-grow-1 d-flex flex-column justify-content-between">
                                    <div>
                                        <!-- Header Badges -->
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <span class="badge rounded-pill px-3 py-1.5 small fw-bold" style="background: rgba(37, 99, 235, 0.1); color: #2563eb; border: 1px solid rgba(37, 99, 235, 0.2);">
                                                <i class="{{ $event->type_icon }} me-1"></i> {{ $event->type_short_label }}
                                            </span>

                                            @if($isEventReg)
                                                <span class="badge rounded-pill px-2.5 py-1 small fw-bold bg-success text-white">
                                                    <i class="fas fa-check-circle me-1"></i> مسجل
                                                </span>
                                            @elseif($isFull)
                                                <span class="badge rounded-pill px-2.5 py-1 small fw-bold bg-secondary text-white">
                                                    مكتمل العدد
                                                </span>
                                            @else
                                                <span class="badge rounded-pill px-2.5 py-1 small fw-bold bg-light text-success border border-success-subtle">
                                                    متاح للتسجيل
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Title -->
                                        <h5 class="fw-bold text-dark mb-2 fs-6" style="line-height: 1.4;">
                                            <a href="{{ route('job-fair.public.events.show', $event->id) }}" class="text-dark text-decoration-none hover-primary">
                                                {{ $event->title }}
                                            </a>
                                        </h5>

                                        <!-- Speaker info if present -->
                                        @if($event->speaker_name)
                                            <div class="d-flex align-items-center gap-2 mb-3 p-2 rounded-3 bg-light">
                                                @if($event->speaker_image_url)
                                                    <img src="{{ $event->speaker_image_url }}" alt="{{ $event->speaker_name }}" class="rounded-circle object-fit-cover flex-shrink-0" style="width: 38px; height: 38px; border: 1.5px solid #e2e8f0;">
                                                @else
                                                    <div class="rounded-circle bg-white text-primary d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 38px; height: 38px;">
                                                        <i class="fas fa-user-tie"></i>
                                                    </div>
                                                @endif
                                                <div class="text-truncate">
                                                    <div class="fw-bold text-dark small text-truncate">{{ $event->speaker_name }}</div>
                                                    <div class="text-muted" style="font-size: 0.72rem;">{{ $event->speaker_title ?: 'محاضر ومدرب' }}</div>
                                                </div>
                                            </div>
                                        @endif

                                        @if($event->description)
                                            <p class="text-muted small mb-3" style="font-size: 0.8rem; line-height: 1.5;">
                                                {{ \Illuminate\Support\Str::limit(strip_tags($event->description), 110) }}
                                            </p>
                                        @endif

                                        <!-- Meta info -->
                                        <div class="d-flex flex-column gap-1.5 text-muted small mb-3" style="font-size: 0.76rem;">
                                            @if($event->start_time)
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="far fa-calendar-alt text-primary" style="width: 14px;"></i>
                                                    <span>{{ $event->start_time->format('Y-m-d') }} ({{ $event->start_time->locale('ar')->isoFormat('dddd') }})</span>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="far fa-clock text-info" style="width: 14px;"></i>
                                                    <span>{{ $event->start_time->format('h:i A') }} @if($event->end_time) - {{ $event->end_time->format('h:i A') }} @endif</span>
                                                </div>
                                            @endif

                                            @if($event->location)
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="fas fa-map-marker-alt text-danger" style="width: 14px;"></i>
                                                    <span>{{ $event->location }}</span>
                                                </div>
                                            @endif

                                            @if($capacity > 0)
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="fas fa-users text-success" style="width: 14px;"></i>
                                                    <span>المقاعد: <strong>{{ $attendees }}</strong> / {{ $capacity }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Action Button Row -->
                                    <div class="border-top pt-3 d-flex align-items-center gap-2">
                                        <form action="{{ route('job-fair.event.toggle', $event->id) }}" method="POST" class="flex-grow-1">
                                            @csrf
                                            @if($isEventReg)
                                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill w-100 fw-bold" onclick="return confirm('هل تريد بالتأكيد إلغاء تسجيلك في هذه الفعالية؟');">
                                                    <i class="fas fa-times me-1"></i> إلغاء التسجيل
                                                </button>
                                            @else
                                                @if($isFull)
                                                    <button type="button" class="btn btn-secondary btn-sm rounded-pill w-100" disabled>
                                                        <i class="fas fa-ban me-1"></i> مكتمل العدد
                                                    </button>
                                                @else
                                                    <button type="submit" class="btn btn-primary-modern btn-sm rounded-pill w-100 fw-bold shadow-sm">
                                                        <i class="fas fa-plus-circle me-1"></i> التسجيل في الفعالية
                                                    </button>
                                                @endif
                                            @endif
                                        </form>

                                        <a href="{{ route('job-fair.public.events.show', $event->id) }}" class="btn btn-light btn-sm rounded-circle d-flex align-items-center justify-content-center border" style="width: 34px; height: 34px;" title="عرض التفاصيل الكاملة">
                                            <i class="fas fa-arrow-left text-primary"></i>
                                        </a>
                                    </div>
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- ==================== TAB 3: تذاكري ومشاركاتي ==================== -->
        <div class="tab-pane fade" id="my-tickets" role="tabpanel" aria-labelledby="my-tickets-tab">
            <div class="row g-4">
                
                <!-- Registered Fairs -->
                <div class="col-12 col-lg-6">
                    <div class="card border-0 rounded-4 shadow-sm bg-white h-100">
                        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                            <h5 class="card-title mb-0 fw-bold text-primary fs-6">
                                <i class="fas fa-ticket-alt me-2 text-warning"></i> تذاكر المعارض المسجل بها
                            </h5>
                            <span class="badge bg-light text-primary border border-primary-subtle rounded-pill px-2.5 py-1 small">
                                {{ $myRegistrations->count() }} تذاكر
                            </span>
                        </div>
                        <div class="card-body p-3">
                            @if($myRegistrations->isEmpty())
                                <div class="text-center py-5 text-muted">
                                    <i class="fas fa-ticket-alt fa-3x mb-3 opacity-40"></i>
                                    <p class="small mb-2">لم تقم بالتسجيل في أي معرض مهني بعد</p>
                                    <button type="button" class="btn btn-sm btn-primary-modern rounded-pill px-3" onclick="document.getElementById('fairs-list-tab').click();">
                                        تصفح المعارض المتاحة
                                    </button>
                                </div>
                            @else
                                <div class="d-flex flex-column gap-3">
                                    @foreach($myRegistrations as $reg)
                                        <div class="p-3 rounded-3 border bg-light d-flex align-items-center justify-content-between flex-wrap gap-2">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="rounded-circle bg-white text-success d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 44px; height: 44px; font-size: 1.25rem;">
                                                    <i class="fas fa-qrcode"></i>
                                                </div>
                                                <div>
                                                    <h6 class="fw-bold text-dark mb-1 fs-6">{{ $reg->jobFair->title ?? 'معرض مهني' }}</h6>
                                                    <div class="text-muted small" style="font-size: 0.74rem;">
                                                        رقم التسجيل: <span class="fw-bold text-primary font-monospace">#{{ $reg->registration_number }}</span>
                                                        @if($reg->jobFair && $reg->jobFair->event_date)
                                                            <span class="ms-2">• {{ $reg->jobFair->event_date->format('Y-m-d') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="d-flex align-items-center gap-2">
                                                <a href="{{ route('job-fair.my-ticket', $reg->id) }}" class="btn btn-sm btn-warning text-dark fw-bold rounded-pill px-3 shadow-sm">
                                                    <i class="fas fa-ticket-alt me-1"></i> تذكرتي
                                                </a>
                                                <a href="{{ route('job-fair.ticket.print', $reg->job_fair_id) }}" target="_blank" class="btn btn-sm btn-light border rounded-pill px-2.5" title="طباعة">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Registered Events / Workshops -->
                <div class="col-12 col-lg-6">
                    <div class="card border-0 rounded-4 shadow-sm bg-white h-100">
                        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                            <h5 class="card-title mb-0 fw-bold text-primary fs-6">
                                <i class="fas fa-calendar-check me-2 text-info"></i> جدول الفعاليات والورش المسجل بها
                            </h5>
                            <span class="badge bg-light text-info border border-info-subtle rounded-pill px-2.5 py-1 small">
                                {{ $myEvents->count() }} فعاليات
                            </span>
                        </div>
                        <div class="card-body p-3">
                            @if($myEvents->isEmpty())
                                <div class="text-center py-5 text-muted">
                                    <i class="fas fa-calendar-times fa-3x mb-3 opacity-40"></i>
                                    <p class="small mb-2">لم تقم بالتسجيل في أي ورشة عمل أو فعالية علمية بعد</p>
                                    <button type="button" class="btn btn-sm btn-primary-modern rounded-pill px-3" onclick="document.getElementById('events-list-tab').click();">
                                        استكشف الفعاليات المتاحة
                                    </button>
                                </div>
                            @else
                                <div class="d-flex flex-column gap-2.5">
                                    @foreach($myEvents as $myEv)
                                        <div class="p-3 rounded-3 border bg-light d-flex align-items-center justify-content-between flex-wrap gap-2">
                                            <div>
                                                <div class="d-flex align-items-center gap-2 mb-1">
                                                    <span class="badge bg-primary-subtle text-primary rounded-pill small" style="font-size: 0.7rem;">{{ $myEv->type_short_label }}</span>
                                                    <h6 class="fw-bold text-dark mb-0 fs-6">{{ $myEv->title }}</h6>
                                                </div>
                                                <div class="text-muted small d-flex align-items-center gap-3" style="font-size: 0.74rem;">
                                                    @if($myEv->start_time)
                                                        <span><i class="far fa-calendar-alt text-primary me-1"></i> {{ $myEv->start_time->format('Y-m-d') }}</span>
                                                        <span><i class="far fa-clock text-info me-1"></i> {{ $myEv->start_time->format('h:i A') }}</span>
                                                    @endif
                                                    @if($myEv->location)
                                                        <span><i class="fas fa-map-marker-alt text-danger me-1"></i> {{ $myEv->location }}</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="d-flex align-items-center gap-2">
                                                <a href="{{ route('job-fair.public.events.show', $myEv->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                    التفاصيل
                                                </a>
                                                <form action="{{ route('job-fair.event.toggle', $myEv->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="إلغاء التسجيل" onclick="return confirm('إلغاء التسجيل في هذه الفعالية؟');">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Event category filtering logic
    const filterBtns = document.querySelectorAll('.event-filter-btn');
    const eventCols = document.querySelectorAll('.event-card-col');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const filter = this.getAttribute('data-filter');

            eventCols.forEach(col => {
                if (filter === 'all' || col.getAttribute('data-type') === filter) {
                    col.style.display = 'block';
                } else {
                    col.style.display = 'none';
                }
            });
        });
    });
});
</script>
@endpush
@endsection
