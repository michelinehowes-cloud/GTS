@extends('layouts.app')

@section('title', 'إدارة المعارض والفعاليات')
@section('page-title', 'إدارة المعارض والفعاليات')

@push('styles')
<style>
    .job-fair-hero {
        background: linear-gradient(135deg, #045db0 0%, #1e40af 100%);
        border-radius: 16px;
        color: #ffffff;
        padding: 1.5rem;
        box-shadow: 0 4px 20px rgba(4, 93, 176, 0.15);
    }

    .job-fair-hero h2 {
        color: #fef08a !important;
        font-weight: 800;
    }

    .job-fair-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 1.5rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
        transition: all 0.25s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .job-fair-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
    }

    .stat-pill-box {
        text-align: center;
        padding: 0.75rem 0.5rem;
        border-radius: 12px;
        height: 100%;
    }

    .stat-pill-val {
        font-size: 1.45rem;
        font-weight: 800;
        line-height: 1.1;
        margin-bottom: 0.2rem;
    }

    .stat-pill-lbl {
        font-size: 0.75rem;
        font-weight: 600;
    }

    .fair-actions-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.5rem;
        margin-top: 1rem;
    }

    .fair-actions-grid .btn {
        font-size: 0.8rem;
        font-weight: 600;
        padding: 0.5rem 0.6rem;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap;
    }

    @media (max-width: 767.98px) {
        .job-fair-hero {
            padding: 1.25rem 1rem !important;
            border-radius: 14px !important;
        }

        .job-fair-hero h2 {
            font-size: 1.3rem !important;
        }

        .job-fair-card {
            padding: 1.1rem 0.9rem !important;
            border-radius: 14px !important;
        }

        .fair-actions-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 0.4rem !important;
        }

        .fair-actions-grid .btn {
            font-size: 0.78rem !important;
            padding: 0.45rem 0.5rem !important;
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
    <div class="job-fair-hero mb-4" style="background: linear-gradient(135deg, #091f3c 0%, #03488a 55%, #045db0 100%) !important; border-bottom: 3.5px solid #eeca3e; border-radius: 20px; padding: 1.25rem 1.6rem; box-shadow: 0 10px 30px rgba(3, 72, 138, 0.22);">
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

                <div class="d-none d-sm-flex align-items-center gap-2">
                    <img src="{{ asset('images/wahaexpo_horizontal_white.png') }}" alt="شركة الواحة لتنظيم المعارض والمؤتمرات" style="height: 34px; width: auto; max-width: 130px; object-fit: contain; filter: drop-shadow(0 2px 6px rgba(0, 0, 0, 0.35));" title="شركة الواحة لتنظيم المعارض والمؤتمرات — الراعي الاستراتيجي" onerror="this.src='{{ asset('images/wahaexpo_logo_white.png') }}';">
                    <span style="background: linear-gradient(135deg, rgba(245, 158, 11, 0.2), rgba(217, 119, 6, 0.3)); border: 1px solid rgba(245, 158, 11, 0.55); color: #fef08a; padding: 4px 10px; border-radius: 50px; font-size: 0.72rem; font-weight: 700;" class="d-none d-md-inline-flex align-items-center gap-1" title="الشريك والراعي الاستراتيجي لمعرض التوظيف السنوي">
                        <i class="fas fa-crown text-warning"></i> الراعي الاستراتيجي
                    </span>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('job-fair.public') }}" class="btn btn-sm btn-outline-light rounded-pill px-3 py-1" style="background: rgba(255,255,255,0.08); border-color: rgba(255,255,255,0.22); font-size: 0.8rem; font-weight: 600;" target="_blank">
                    <i class="fas fa-external-link-alt text-warning me-1"></i> الصفحة العامة
                </a>
                <a href="{{ route('job-fair.admin.create') }}" class="btn btn-sm btn-warning rounded-pill fw-bold text-dark px-3 py-1" style="background: #eeca3e; border: none; font-size: 0.8rem;">
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
                <p class="mb-0 text-white-50 small">إدارة وتنظيم المعارض والملتقيات والفعاليات ومتابعة حضور الخريجين والشركات</p>
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
        <div class="col-12 col-lg-6">
            <div class="job-fair-card" style="border-right: 5px solid
                @if($fair->status === 'published') #3b82f6
                @elseif($fair->status === 'ongoing') #10b981
                @elseif($fair->status === 'completed') #64748b
                @else #f59e0b
                @endif !important;">
                
                <div>
                    {{-- رأس بطاقة المعرض --}}
                    <div class="d-flex justify-content-between align-items-start mb-3 gap-2">
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h5 class="fw-bold text-dark mb-1 fs-6">{{ $fair->title }}</h5>
                                <a href="{{ route('job-fair.public', $fair->id) }}" target="_blank" class="text-primary small" title="عرض الصفحة العامة للفعالية">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            </div>
                            <div class="text-muted small d-flex flex-wrap gap-2">
                                <span><i class="fas fa-calendar-alt me-1 text-primary"></i>{{ $fair->event_date->format('d/m/Y') }}</span>
                                @if($fair->location)
                                <span><i class="fas fa-map-marker-alt me-1 text-danger"></i>{{ $fair->location }}</span>
                                @endif
                            </div>
                        </div>
                        <span class="badge rounded-pill px-3 py-1 text-nowrap
                            @if($fair->status === 'published') bg-primary text-white
                            @elseif($fair->status === 'ongoing') bg-success text-white
                            @elseif($fair->status === 'completed') bg-secondary text-white
                            @else bg-warning text-dark
                            @endif">
                            @php
                                $statusLabels = ['draft'=>'مسودة','published'=>'منشور','ongoing'=>'جارٍ الآن','completed'=>'منتهي','cancelled'=>'ملغي'];
                            @endphp
                            {{ $statusLabels[$fair->status] ?? $fair->status }}
                        </span>
                    </div>

                    {{-- إحصائيات المعرض الثلاثية --}}
                    <div class="row g-2 mb-3">
                        <div class="col-4">
                            <div class="stat-pill-box" style="background: #eff6ff;">
                                <div class="stat-pill-val" style="color: #1d4ed8;">{{ $fair->registrations_count }}</div>
                                <div class="stat-pill-lbl" style="color: #3b82f6;">خريج مسجل</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-pill-box" style="background: #ecfdf5;">
                                <div class="stat-pill-val" style="color: #047857;">{{ $fair->companies_count }}</div>
                                <div class="stat-pill-lbl" style="color: #10b981;">شركة مشاركة</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-pill-box" style="background: #fefce8;">
                                <div class="stat-pill-val" style="color: #b45309;">{{ $fair->days_remaining }}</div>
                                <div class="stat-pill-lbl" style="color: #d97706;">يوم متبقي</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- شبكة أزرار الإجراءات --}}
                <div class="fair-actions-grid">
                    <a href="{{ route('job-fair.admin.show', $fair->id) }}" class="btn btn-primary-modern">
                        <i class="fas fa-eye me-1"></i> التفاصيل
                    </a>
                    <a href="{{ route('job-fair.admin.attendance', $fair->id) }}" class="btn btn-success text-white">
                        <i class="fas fa-qrcode me-1"></i> الحضور
                    </a>
                    <a href="{{ route('job-fair.admin.edit', $fair->id) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-edit me-1"></i> تعديل
                    </a>
                    <a href="{{ route('job-fair.admin.export', $fair->id) }}" class="btn btn-info text-white">
                        <i class="fas fa-file-excel me-1"></i> تصدير
                    </a>
                </div>

            </div>
        </div>
        @endforeach
    </div>
    @endif

</div>
@endsection
