@extends('layouts.app')

@section('title', 'إدارة المعارض والفعاليات')
@section('page-title', 'إدارة المعارض والفعاليات')

@push('styles')
<style>
    .job-fair-hero {
        background: linear-gradient(135deg, #091f3c 0%, #03488a 55%, #045db0 100%) !important;
        border-bottom: 3.5px solid #eeca3e;
        border-radius: 20px;
        padding: 1.4rem 1.8rem;
        box-shadow: 0 10px 30px rgba(3, 72, 138, 0.22);
    }

    .job-fair-hero h2 {
        color: #fef08a !important;
        font-weight: 800;
    }

    /* Premium Modern Fair Card */
    .job-fair-premium-card {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid rgba(226, 232, 240, 0.85);
        box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05), 0 2px 6px -1px rgba(15, 23, 42, 0.03);
        transition: all 0.28s cubic-bezier(0.16, 1, 0.3, 1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
        position: relative;
        overflow: hidden;
    }

    .job-fair-premium-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 18px 36px -4px rgba(3, 72, 138, 0.12), 0 6px 14px -2px rgba(15, 23, 42, 0.05);
        border-color: rgba(3, 72, 138, 0.28);
    }

    /* Status accent top hairline */
    .card-top-stripe {
        height: 5px;
        width: 100%;
    }
    .card-top-stripe.published {
        background: linear-gradient(90deg, #10b981 0%, #059669 100%);
    }
    .card-top-stripe.ongoing {
        background: linear-gradient(90deg, #3b82f6 0%, #1d4ed8 100%);
    }
    .card-top-stripe.completed {
        background: linear-gradient(90deg, #94a3b8 0%, #64748b 100%);
    }
    .card-top-stripe.draft {
        background: linear-gradient(90deg, #f59e0b 0%, #d97706 100%);
    }

    .card-inner-body {
        padding: 1.4rem;
        display: flex;
        flex-direction: column;
        gap: 1.15rem;
        flex-grow: 1;
    }

    .fair-header-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 0.85rem;
    }

    .fair-avatar-box {
        width: 62px;
        height: 62px;
        border-radius: 16px;
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        padding: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.04);
    }

    .fair-avatar-box img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .fair-title-text {
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.35;
        margin-bottom: 0.35rem;
    }

    .fair-title-text a {
        color: inherit;
        text-decoration: none;
        transition: color 0.2s;
    }
    .fair-title-text a:hover {
        color: #03488a;
    }

    .fair-chips-row {
        display: flex;
        flex-wrap: wrap;
        gap: 0.45rem;
        align-items: center;
    }

    .fair-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.77rem;
        font-weight: 600;
        padding: 3px 9px;
        border-radius: 50px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #475569;
    }

    .fair-chip.location {
        background: #fef2f2;
        border-color: #fee2e2;
        color: #991b1b;
    }

    /* Status badge with pulsing dot */
    .fair-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        font-size: 0.78rem;
        font-weight: 700;
        padding: 5px 12px;
        border-radius: 50px;
        white-space: nowrap;
    }

    .fair-status-badge.published {
        background: rgba(16, 185, 129, 0.1);
        color: #047857;
        border: 1px solid rgba(16, 185, 129, 0.25);
    }
    .fair-status-badge.ongoing {
        background: rgba(37, 99, 235, 0.1);
        color: #1d4ed8;
        border: 1px solid rgba(37, 99, 235, 0.25);
    }
    .fair-status-badge.completed {
        background: #f1f5f9;
        color: #64748b;
        border: 1px solid #e2e8f0;
    }
    .fair-status-badge.draft {
        background: #fffbeb;
        color: #b45309;
        border: 1px solid #fde68a;
    }

    .live-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }
    .live-dot.green {
        background: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.25);
        animation: pulseGlow 2s infinite;
    }
    .live-dot.blue {
        background: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25);
        animation: pulseGlow 2s infinite;
    }
    .live-dot.amber {
        background: #f59e0b;
    }
    .live-dot.gray {
        background: #94a3b8;
    }

    @keyframes pulseGlow {
        0% { transform: scale(0.95); opacity: 0.8; }
        50% { transform: scale(1.15); opacity: 1; }
        100% { transform: scale(0.95); opacity: 0.8; }
    }

    /* Bento Metrics Row */
    .bento-metrics-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.65rem;
    }

    .bento-metric-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 0.85rem 0.5rem;
        text-align: center;
        transition: all 0.2s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .bento-metric-card:hover {
        background: #ffffff;
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
    }

    .bento-metric-icon {
        width: 32px;
        height: 32px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        margin-bottom: 0.35rem;
    }
    .bento-metric-icon.blue {
        background: #eff6ff;
        color: #1d4ed8;
    }
    .bento-metric-icon.emerald {
        background: #ecfdf5;
        color: #047857;
    }
    .bento-metric-icon.amber {
        background: #fffbeb;
        color: #b45309;
    }

    .bento-metric-val {
        font-size: 1.35rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
        margin-bottom: 0.2rem;
    }

    .bento-metric-lbl {
        font-size: 0.72rem;
        font-weight: 600;
        color: #64748b;
    }

    /* Actions Footer */
    .card-actions-footer {
        background: #fcfdfe;
        border-top: 1px solid #f1f5f9;
        padding: 0.95rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    /* Primary Details Hub Button */
    .btn-fair-primary {
        background: linear-gradient(135deg, #03488a 0%, #045db0 100%);
        color: #ffffff !important;
        font-size: 0.84rem;
        font-weight: 700;
        padding: 0.58rem 1rem;
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 14px rgba(3, 72, 138, 0.22);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
        flex: 1 1 auto;
        text-decoration: none;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .btn-fair-primary:hover {
        background: linear-gradient(135deg, #02386e 0%, #03488a 100%);
        box-shadow: 0 6px 18px rgba(3, 72, 138, 0.32);
        transform: translateY(-1px);
        color: #ffffff !important;
    }

    /* Attendance Button */
    .btn-fair-attendance {
        background: #f0fdf4;
        color: #166534 !important;
        border: 1.5px solid #bbf7d0;
        font-size: 0.82rem;
        font-weight: 700;
        padding: 0.52rem 0.9rem;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        text-decoration: none;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .btn-fair-attendance:hover {
        background: #166534;
        color: #ffffff !important;
        border-color: #166534;
        box-shadow: 0 4px 12px rgba(22, 101, 52, 0.2);
    }

    /* Utility buttons */
    .btn-fair-utility {
        background: #ffffff;
        color: #334155 !important;
        border: 1px solid #e2e8f0;
        font-size: 0.82rem;
        font-weight: 600;
        padding: 0.52rem 0.8rem;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        text-decoration: none;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .btn-fair-utility:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #0f172a !important;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    }

    .btn-fair-utility.export:hover {
        border-color: #86efac;
        background: #f0fdf4;
        color: #166534 !important;
    }

    .btn-fair-icon {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        color: #64748b !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-fair-icon:hover {
        background: #f8fafc;
        color: #03488a !important;
        border-color: #cbd5e1;
    }

    @media (max-width: 767.98px) {
        .job-fair-hero {
            padding: 1.25rem 1rem !important;
            border-radius: 16px !important;
        }

        .job-fair-hero h2 {
            font-size: 1.25rem !important;
        }

        .card-inner-body {
            padding: 1.1rem !important;
            gap: 1rem !important;
        }

        .bento-metrics-grid {
            gap: 0.45rem !important;
        }

        .bento-metric-val {
            font-size: 1.15rem !important;
        }

        .card-actions-footer {
            padding: 0.8rem 1rem !important;
            gap: 0.4rem !important;
        }

        .btn-fair-primary {
            width: 100% !important;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- Breadcrumbs --}}
    @include('components.breadcrumbs', [
        'items' => [
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'لوحة تحكم المدير', 'url' => route('admin.dashboard')],
            ['label' => 'إدارة المعارض والفعاليات', 'active' => true],
        ]
    ])

    <!-- Hero Header مع الشعارات الرسمية المتطابقة مع صفحة المعرض الرئيسية -->
    <div class="job-fair-hero mb-4">
        <!-- Top Strip: Brand Logos Bar (Identical to public fair page) -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 pb-3 mb-3" style="border-bottom: 1px solid rgba(255, 255, 255, 0.14);">
            <div class="d-flex align-items-center flex-wrap gap-3">
                <a href="{{ route('home') }}" class="d-flex align-items-center gap-2 text-decoration-none" title="الصفحة الرئيسية للنظام">
                    <img src="{{ asset('images/logo.jpg') }}" alt="شعار الجامعة" style="width: 44px; height: 44px; border-radius: 50%; border: 2px solid #eeca3e; object-fit: cover; box-shadow: 0 3px 10px rgba(0,0,0,0.28);" onerror="this.src='{{ asset('images/gto_logo.jpg') }}'">
                    <div class="d-none d-sm-block text-end" style="line-height: 1.25;">
                        <div class="text-white fw-bold" style="font-size: 0.92rem;">مكتب تدريب الخريجين</div>
                        <div style="color: #eeca3e; font-size: 0.74rem; font-weight: 700;">جامعة طرابلس</div>
                    </div>
                </a>

                <div style="width: 1px; height: 32px; background: rgba(255, 255, 255, 0.25); margin: 0 4px;"></div>

                <a href="{{ route('job-fair.public') }}" target="_blank" title="شعار المعرض — الصفحة العامة">
                    <img src="{{ asset('images/job_fair_logo_white.png') }}" alt="معرض التوظيف" style="height: 38px; width: auto; max-width: 140px; object-fit: contain; filter: drop-shadow(0 2px 6px rgba(0, 0, 0, 0.35));">
                </a>

                <div style="width: 1px; height: 32px; background: rgba(255, 255, 255, 0.25); margin: 0 4px;" class="d-none d-sm-block"></div>

                <div class="d-none d-sm-flex align-items-center">
                    <img src="{{ asset('images/wahaexpo_horizontal_white.png') }}" alt="شركة الواحة لتنظيم المعارض والمؤتمرات" style="height: 34px; width: auto; max-width: 130px; object-fit: contain; filter: drop-shadow(0 2px 6px rgba(0, 0, 0, 0.35));" title="شركة الواحة لتنظيم المعارض والمؤتمرات" onerror="this.src='{{ asset('images/wahaexpo_logo_white.png') }}';">
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('job-fair.public') }}" class="btn btn-sm btn-outline-light rounded-pill px-3 py-1" style="background: rgba(255,255,255,0.08); border-color: rgba(255,255,255,0.22); font-size: 0.82rem; font-weight: 600;" target="_blank">
                    <i class="fas fa-external-link-alt text-warning me-1"></i> الصفحة العامة
                </a>
                <a href="{{ route('job-fair.admin.create') }}" class="btn btn-sm rounded-pill fw-bold px-3 py-1 shadow-sm" style="background: linear-gradient(135deg, #eeca3e 0%, #d4af37 100%); color: #0f172a; border: none; font-size: 0.82rem;">
                    <i class="fas fa-plus me-1"></i> إنشاء فعالية جديدة
                </a>
            </div>
        </div>

        <!-- Title & Subtitle Row -->
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h2 class="mb-1 fs-4 fw-bold text-white">
                    <i class="fas fa-calendar-check me-2" style="color: #eeca3e;"></i>
                    إدارة المعارض والفعاليات
                </h2>
                <p class="mb-0 text-white-50 small">إدارة وتنظيم المعارض والملتقيات ومتابعة مؤشرات حضور الخريجين والشركات المشاركة</p>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success rounded-3 border-0 shadow-sm mb-4">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
    </div>
    @endif

    @if($fairs->isEmpty())
    <div class="card-modern text-center py-5">
        <div style="font-size: 3.5rem">🎪</div>
        <h5 class="fw-bold text-dark mt-3">لا توجد معارض أو فعاليات مسجلة حتى الآن</h5>
        <p class="text-muted small mb-4">يمكنك إنشاء أول فعالية أو معرض وإتاحة تسجيل الخريجين والشركات</p>
        <a href="{{ route('job-fair.admin.create') }}" class="btn btn-primary-modern rounded-pill px-4 mx-auto">
            <i class="fas fa-plus me-2"></i> إنشاء أول فعالية
        </a>
    </div>
    @else
    <div class="row g-3 g-md-4">
        @foreach($fairs as $fair)
        <div class="col-12 {{ $fairs->count() === 1 ? 'col-lg-8 col-xl-7 mx-auto' : 'col-lg-6' }}">
            <div class="job-fair-premium-card">
                
                {{-- شريط ملون علوي يعبر عن حالة المعرض بهدوء --}}
                <div class="card-top-stripe {{ $fair->status }}"></div>

                <div class="card-inner-body">
                    {{-- رأس بطاقة المعرض: الشعار، العنوان، الوسوم، والحالة --}}
                    <div class="fair-header-row">
                        <div class="d-flex align-items-center gap-3">
                            <div class="fair-avatar-box">
                                <img src="{{ $fair->logo_url ?? asset('images/job_fair_logo.png') }}" 
                                     alt="{{ $fair->title }}" 
                                     onerror="this.onerror=null;this.src='{{ asset('images/job_fair_logo.png') }}';">
                            </div>
                            <div>
                                <h4 class="fair-title-text">
                                    <a href="{{ route('job-fair.admin.show', $fair->id) }}">{{ $fair->title }}</a>
                                </h4>
                                <div class="fair-chips-row">
                                    <span class="fair-chip">
                                        <i class="far fa-calendar-alt text-primary"></i>
                                        {{ $fair->event_date ? $fair->event_date->format('d/m/Y') : 'غير محدد' }}
                                    </span>
                                    @if($fair->location)
                                    <span class="fair-chip location">
                                        <i class="fas fa-map-marker-alt text-danger"></i>
                                        {{ $fair->location }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-column align-items-end gap-2">
                            <span class="fair-status-badge {{ $fair->status }}">
                                @if($fair->status === 'published')
                                    <span class="live-dot green"></span> منشور
                                @elseif($fair->status === 'ongoing')
                                    <span class="live-dot blue"></span> جارٍ الآن
                                @elseif($fair->status === 'completed')
                                    <span class="live-dot gray"></span> منتهي
                                @else
                                    <span class="live-dot amber"></span> مسودة
                                @endif
                            </span>
                            <a href="{{ route('job-fair.public', $fair->id) }}" 
                               target="_blank" 
                               class="btn-fair-icon" 
                               title="عرض الصفحة العامة للفعالية">
                                <i class="fas fa-external-link-alt" style="font-size: 0.8rem;"></i>
                            </a>
                        </div>
                    </div>

                    {{-- إحصائيات المعرض الثلاثية بنمط Bento الراقي --}}
                    <div class="bento-metrics-grid">
                        <div class="bento-metric-card">
                            <div class="bento-metric-icon blue">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <div class="bento-metric-val">{{ number_format($fair->registrations_count) }}</div>
                            <div class="bento-metric-lbl">خريج مسجل</div>
                        </div>

                        <div class="bento-metric-card">
                            <div class="bento-metric-icon emerald">
                                <i class="fas fa-building"></i>
                            </div>
                            <div class="bento-metric-val">{{ number_format($fair->companies_count) }}</div>
                            <div class="bento-metric-lbl">شركة مشاركة</div>
                        </div>

                        <div class="bento-metric-card">
                            <div class="bento-metric-icon amber">
                                <i class="fas fa-hourglass-half"></i>
                            </div>
                            <div class="bento-metric-val">{{ $fair->days_remaining }}</div>
                            <div class="bento-metric-lbl">يوم متبقي</div>
                        </div>
                    </div>

                    {{-- شريط حالة التسجيل والفعاليات --}}
                    <div class="d-flex align-items-center justify-content-between pt-2 text-muted" style="font-size: 0.78rem; border-top: 1px dashed #e2e8f0;">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-door-open text-primary"></i>
                            <span>التسجيل:</span>
                            <span class="fw-bold {{ $fair->registration_open ? 'text-success' : 'text-danger' }}">
                                {{ $fair->registration_open ? 'مفتوح للتقديم' : 'مغلق حالياً' }}
                            </span>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <i class="fas fa-calendar-day text-secondary"></i>
                            <span>الفعاليات: {{ $fair->events()->count() }}</span>
                        </div>
                    </div>

                </div>

                {{-- شريط أزرار الإجراءات المتناسقة --}}
                <div class="card-actions-footer">
                    <a href="{{ route('job-fair.admin.show', $fair->id) }}" class="btn-fair-primary">
                        <i class="fas fa-sliders-h"></i>
                        <span>لوحة التحكم والتفاصيل</span>
                    </a>
                    <a href="{{ route('job-fair.admin.attendance', $fair->id) }}" class="btn-fair-attendance" title="شاشة تسجيل وقراءة الحضور عبر QR">
                        <i class="fas fa-qrcode"></i>
                        <span>الحضور</span>
                    </a>
                    <a href="{{ route('job-fair.admin.edit', $fair->id) }}" class="btn-fair-utility" title="تعديل بيانات المعرض">
                        <i class="fas fa-edit text-primary"></i>
                        <span>تعديل</span>
                    </a>
                    <a href="{{ route('job-fair.admin.export', $fair->id) }}" class="btn-fair-utility export" title="تصدير كشف البيانات إلى Excel">
                        <i class="fas fa-file-excel text-success"></i>
                        <span>تصدير</span>
                    </a>
                </div>

            </div>
        </div>
        @endforeach
    </div>
    @endif

</div>
@endsection
