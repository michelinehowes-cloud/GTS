@extends('layouts.app')

@section('title', 'إدارة مشاريع التخرج والأرشيف - ' . $fair->title)

@push('styles')
<style>
    .job-fair-detail-hero {
        background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #2563eb 100%) !important;
        color: #ffffff !important;
        border-radius: 18px;
        padding: 1.5rem 1.75rem;
        box-shadow: 0 8px 24px rgba(30, 58, 138, 0.18);
        position: relative;
        overflow: visible !important;
        border-bottom: 3px solid #f59e0b;
    }

    .fair-logo-badge {
        width: 80px;
        height: 80px;
        border-radius: 14px;
        background: #ffffff;
        padding: 5px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.16);
        object-fit: contain;
        flex-shrink: 0;
        border: 2px solid rgba(255, 255, 255, 0.95);
    }

    .partner-mini-badge {
        height: 44px;
        padding: 4px 10px;
        border-radius: 10px;
        background: #ffffff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.12);
        display: inline-flex;
        align-items: center;
    }
    .partner-mini-badge img {
        height: 34px;
        max-width: 105px;
        object-fit: contain;
    }

    .fair-actions-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 0.55rem;
        align-items: center;
    }

    .fair-btn-action {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.55rem 0.95rem;
        font-size: 0.84rem;
        font-weight: 600;
        border-radius: 50rem;
        transition: all 0.2s ease;
        text-decoration: none;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
    }
    .fair-btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.16);
    }

    .fair-btn-glass {
        background: rgba(255, 255, 255, 0.15);
        color: #ffffff !important;
        border: 1px solid rgba(255, 255, 255, 0.3);
        backdrop-filter: blur(4px);
    }
    .fair-btn-glass:hover {
        background: rgba(255, 255, 255, 0.28);
        color: #ffffff !important;
        border-color: rgba(255, 255, 255, 0.5);
    }

    .pill-filter {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 0.4rem 1rem;
        border-radius: 50px;
        font-size: 0.84rem;
        font-weight: 600;
        color: #475569;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        text-decoration: none;
        transition: all 0.15s;
    }
    .pill-filter:hover {
        background: #f1f5f9;
        color: #1e293b;
    }
    .pill-filter.active {
        background: #2563eb;
        color: #ffffff !important;
        border-color: #2563eb;
    }
    .pill-filter.active.pill-warning {
        background: #f59e0b;
        color: #0f172a !important;
        border-color: #f59e0b;
    }
    .pill-filter.active.pill-success {
        background: #10b981;
        color: #ffffff !important;
        border-color: #10b981;
    }
    .pill-filter.active.pill-danger {
        background: #ef4444;
        color: #ffffff !important;
        border-color: #ef4444;
    }

    .project-avatar {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        object-fit: cover;
        flex-shrink: 0;
        border: 1px solid rgba(0, 0, 0, 0.08);
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #2563eb;
        font-size: 1.25rem;
    }

    .action-circle-btn {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        font-size: 0.8rem;
        color: #475569;
    }
    .action-circle-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.08);
    }

    .admin-confidential-box {
        background: #fff5f5;
        border: 1.5px solid #feb2b2;
        border-radius: 16px;
        padding: 16px;
        position: relative;
    }

    @media (max-width: 991.98px) {
        .job-fair-detail-hero {
            padding: 1.25rem 1rem;
        }
        .fair-actions-toolbar {
            width: 100%;
            margin-top: 1rem;
            justify-content: flex-start;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4" dir="rtl">

    {{-- ══════════════════════════════════
         HERO HEADER (المظهر الموحد للمعرض)
    ══════════════════════════════════ --}}
    <div class="job-fair-detail-hero mb-4">
        <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center justify-content-between gap-3">
            
            <div class="d-flex align-items-center gap-3">
                <!-- زر العودة -->
                <a href="{{ route('job-fair.admin.show', $fair->id) }}" class="btn btn-outline-light rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px; background: rgba(255, 255, 255, 0.12); border-color: rgba(255, 255, 255, 0.3);" title="العودة لتفاصيل المعرض">
                    <i class="fas fa-arrow-right"></i>
                </a>

                <!-- شعار المعرض البارز -->
                <img src="{{ $fair->logo_url }}" alt="{{ $fair->title }}" class="fair-logo-badge" onerror="this.onerror=null;this.src='{{ asset('images/job_fair_logo.png') }}';">

                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h2 class="fw-bold mb-0 fs-4 text-white">
                            إدارة مشاريع التخرج والأرشيف السنوي
                        </h2>
                        @php
                            $statusColors = [
                                'draft' => ['bg' => 'bg-secondary', 'label' => 'مسودة'],
                                'published' => ['bg' => 'bg-success', 'label' => 'منشور ومتاح'],
                                'ongoing' => ['bg' => 'bg-warning text-dark', 'label' => 'جارٍ الآن 🟢'],
                                'completed' => ['bg' => 'bg-info text-dark', 'label' => 'منتهي']
                            ];
                            $st = $statusColors[$fair->status] ?? ['bg' => 'bg-light text-dark', 'label' => $fair->status];
                        @endphp
                        <span class="badge {{ $st['bg'] }} rounded-pill px-2.5 py-1 small fw-bold">
                            {{ $st['label'] }}
                        </span>
                    </div>

                    <p class="text-white-50 small mb-1 mt-0.5">
                        {{ $fair->title }} — مراجعة واعتماد مشاريع الطلبة المتميزة، تنظيم الأجنحة، ونشر الابتكارات في المعرض الرقمي
                    </p>

                    <!-- شعارات الشركاء في الهيدر -->
                    <div class="d-flex align-items-center flex-wrap gap-2 mt-2">
                        <div class="partner-mini-badge" title="مكتب تدريب وتأهيل الخريجين — جامعة طرابلس">
                            <img src="{{ asset('images/gto_logo.jpg') }}" alt="مكتب تدريب الخريجين" onerror="this.src='{{ asset('images/logo.jpg') }}'">
                        </div>
                        <div class="partner-mini-badge" title="الراعي الاستراتيجي: شركة الواحة لتنظيم المعارض والمؤتمرات">
                            <img src="{{ asset('images/wahaexpo_horizontal_dark.png') }}" alt="شركة الواحة للمعارض" onerror="this.src='{{ asset('images/wahaexpo_logo.png') }}'">
                        </div>
                        <span class="text-white-50 small ms-2"><i class="fas fa-calendar-alt me-1 text-warning"></i>{{ $fair->event_date->format('Y-m-d') }}</span>
                        @if($fair->location)
                        <span class="text-white-50 small"><i class="fas fa-map-marker-alt me-1 text-danger"></i>{{ $fair->location }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- أزرار الإجراءات الموحدة في الهيدر -->
            <div class="fair-actions-toolbar">
                <!-- زر إضافة مشروع جديد -->
                <button type="button" class="fair-btn-action btn btn-warning text-dark shadow-sm fw-bold" onclick="showModalSafe('addProjectModal')" data-bs-toggle="modal" data-bs-target="#addProjectModal">
                    <i class="fas fa-plus-circle"></i>
                    <span>إضافة مشروع</span>
                </button>

                <!-- زر نموذج تقديم الخريجين المباشر -->
                <a href="{{ route('job-fair.public.projects.submit-fair', $fair->id) }}" target="_blank" class="fair-btn-action fair-btn-glass" title="فتح نموذج تعبئة المشاريع المخصص للطلبة">
                    <i class="fas fa-file-upload text-warning"></i>
                    <span>نموذج التقديم</span>
                </a>

                <!-- زر التبديل بين النشر و Coming Soon -->
                <form action="{{ route('job-fair.admin.toggle-feature', $fair->id) }}" method="POST" class="d-inline m-0">
                    @csrf
                    <input type="hidden" name="feature" value="projects">
                    <button type="submit" class="fair-btn-action fair-btn-glass" title="التبديل بين إظهار المشاريع للجمهور أو إخفائها كـ Coming Soon">
                        <i class="fas {{ $fair->is_projects_published ? 'fa-eye text-success' : 'fa-clock text-warning' }}"></i>
                        <span>{{ $fair->is_projects_published ? 'منشور للجمهور' : 'قيد التحضير' }}</span>
                    </button>
                </form>

                <!-- المعرض الرقمي للجمهور -->
                <a href="{{ route('job-fair.public.projects', $fair->id) }}" target="_blank" class="fair-btn-action fair-btn-glass" title="معاينة صفحة المشاريع العامة">
                    <i class="fas fa-desktop text-info"></i>
                    <span>المعرض الرقمي</span>
                </a>

                <!-- تصدير CSV -->
                <a href="{{ route('job-fair.admin.projects.export', $fair->id) }}" class="fair-btn-action fair-btn-glass" title="تصدير كود المشاريع إلى ملف CSV">
                    <i class="fas fa-file-excel text-success"></i>
                    <span>تصدير CSV</span>
                </a>

                <!-- العودة لصفحة تفاصيل المعرض -->
                <a href="{{ route('job-fair.admin.show', $fair->id) }}" class="fair-btn-action fair-btn-glass" title="لوحة المعرض الرئيسية">
                    <i class="fas fa-arrow-right"></i>
                    <span>لوحة المعرض</span>
                </a>
            </div>

        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-3 border-0 shadow-sm mb-4">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger rounded-3 border-0 shadow-sm mb-4">
            <i class="fas fa-exclamation-triangle me-2"></i><strong>يرجى مراجعة الملاحظات التالية:</strong>
            <ul class="mb-0 mt-1 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ══════════════════════════════════
         بطاقات الإحصائيات (مكون stat-card الموحد)
    ══════════════════════════════════ --}}
    <div class="row g-2 g-md-3 mb-4">
        @include('components.stat-card', [
            'col' => 'col-6 col-md-4 col-xl-2',
            'title' => 'إجمالي المشاريع',
            'value' => $stats['total'] ?? 0,
            'icon' => 'fas fa-folder-open',
            'color' => 'primary',
            'description' => 'كافة المشاريع المسجلة'
        ])
        @include('components.stat-card', [
            'col' => 'col-6 col-md-4 col-xl-2',
            'title' => 'بانتظار الاعتماد',
            'value' => $stats['pending'] ?? 0,
            'icon' => 'fas fa-hourglass-half',
            'color' => 'warning',
            'badge' => ($stats['pending'] ?? 0) > 0 ? 'مهم' : null,
            'badgeClass' => 'bg-danger text-white',
            'description' => 'تتطلب قرار واعتماد'
        ])
        @include('components.stat-card', [
            'col' => 'col-6 col-md-4 col-xl-2',
            'title' => 'معتمدة ومنشورة',
            'value' => $stats['published'] ?? 0,
            'icon' => 'fas fa-check-double',
            'color' => 'success',
            'description' => 'متاحة للجمهور والشركات'
        ])
        @include('components.stat-card', [
            'col' => 'col-6 col-md-4 col-xl-2',
            'title' => 'المرفوضة',
            'value' => $stats['rejected'] ?? 0,
            'icon' => 'fas fa-times-circle',
            'color' => 'danger',
            'description' => 'مرفوضة مع ذكر السبب'
        ])
        @include('components.stat-card', [
            'col' => 'col-6 col-md-4 col-xl-2',
            'title' => 'الكليات المشاركة',
            'value' => $stats['faculties'] ?? 0,
            'icon' => 'fas fa-university',
            'color' => 'info',
            'description' => 'كليات ممثلة بالمشاريع'
        ])
        @include('components.stat-card', [
            'col' => 'col-6 col-md-4 col-xl-2',
            'title' => 'إجمالي المشاهدات',
            'value' => $stats['views'] ?? 0,
            'icon' => 'fas fa-eye',
            'color' => 'secondary',
            'description' => 'تفاعل الزوار'
        ])
    </div>

    {{-- ══════════════════════════════════
         صندوق البحث والفلاتر الموحد
    ══════════════════════════════════ --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3 p-md-4">
            <form method="GET" action="{{ route('job-fair.admin.projects.index', $fair->id) }}" id="projectsFilterForm">
                <input type="hidden" name="status" value="{{ $statusFilter }}">

                <div class="row g-2 align-items-center">
                    <!-- حقل البحث النصي -->
                    <div class="col-lg-5 col-md-12">
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="fas fa-search"></i></span>
                            <input type="text" name="search" class="form-control border-start-0" placeholder="ابحث بعنوان المشروع، اسم الطالب، المشرف، أو رقم القيد..." value="{{ request('search') }}">
                        </div>
                    </div>

                    <!-- الكلية -->
                    <div class="col-lg-3 col-md-6">
                        <select name="faculty" class="form-select" onchange="this.form.submit()">
                            <option value="">كافة الكليات المشاركة</option>
                            @foreach($faculties as $fac)
                                <option value="{{ $fac }}" {{ request('faculty') === $fac ? 'selected' : '' }}>{{ $fac }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- القسم / التخصص -->
                    <div class="col-lg-2 col-md-4">
                        <select name="department" class="form-select" onchange="this.form.submit()">
                            <option value="">كافة الأقسام</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept }}" {{ request('department') === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- زر تصفير الفلترة -->
                    <div class="col-lg-2 col-md-2 text-start">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100 fw-bold">
                                <i class="fas fa-filter me-1"></i> تصفية
                            </button>
                            @if(request()->filled('search') || request()->filled('faculty') || request()->filled('department'))
                                <a href="{{ route('job-fair.admin.projects.index', ['fair' => $fair->id, 'status' => $statusFilter]) }}" class="btn btn-outline-secondary" title="إعادة تعيين الفلاتر">
                                    <i class="fas fa-undo"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- أزرار تبويب الحالة السريعة -->
                <div class="d-flex align-items-center gap-2 flex-wrap mt-3 pt-3 border-top">
                    <span class="text-muted small fw-bold me-1"><i class="fas fa-tags me-1"></i>حالة المشروع:</span>

                    <a href="{{ route('job-fair.admin.projects.index', ['fair' => $fair->id, 'status' => 'all'] + request()->except('page', 'status')) }}" class="pill-filter {{ $statusFilter === 'all' ? 'active' : '' }}">
                        <i class="fas fa-list-ul"></i> الكل ({{ $stats['total'] }})
                    </a>

                    <a href="{{ route('job-fair.admin.projects.index', ['fair' => $fair->id, 'status' => 'pending'] + request()->except('page', 'status')) }}" class="pill-filter pill-warning {{ $statusFilter === 'pending' ? 'active pill-warning' : '' }}" style="{{ ($stats['pending'] ?? 0) > 0 ? 'border-color: #f59e0b; color: #b45309;' : '' }}">
                        <i class="fas fa-hourglass-half"></i> بانتظار الاعتماد ({{ $stats['pending'] }})
                    </a>

                    <a href="{{ route('job-fair.admin.projects.index', ['fair' => $fair->id, 'status' => 'published'] + request()->except('page', 'status')) }}" class="pill-filter pill-success {{ $statusFilter === 'published' ? 'active pill-success' : '' }}">
                        <i class="fas fa-check-circle"></i> المعتمدة والمنشورة ({{ $stats['published'] }})
                    </a>

                    <a href="{{ route('job-fair.admin.projects.index', ['fair' => $fair->id, 'status' => 'rejected'] + request()->except('page', 'status')) }}" class="pill-filter pill-danger {{ $statusFilter === 'rejected' ? 'active pill-danger' : '' }}">
                        <i class="fas fa-times-circle"></i> المرفوضة ({{ $stats['rejected'] }})
                    </a>

                    <a href="{{ route('job-fair.admin.projects.index', ['fair' => $fair->id, 'status' => 'draft'] + request()->except('page', 'status')) }}" class="pill-filter {{ $statusFilter === 'draft' ? 'active' : '' }}">
                        <i class="fas fa-pencil-alt"></i> المسودات ({{ $stats['draft'] ?? 0 }})
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════
         جدول مشاريع التخرج
    ══════════════════════════════════ --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="text-center" style="width: 50px;">#</th>
                        <th style="min-width: 280px;">المشروع</th>
                        <th style="min-width: 200px;">الكلية والتخصص</th>
                        <th style="min-width: 180px;">فريق العمل</th>
                        <th style="min-width: 170px;">المشرف الأكاديمي</th>
                        <th class="text-center" style="width: 100px;">الجناح</th>
                        <th class="text-center" style="width: 130px;">الحالة</th>
                        <th class="text-center" style="width: 90px;">المشاهدات</th>
                        <th class="text-center" style="width: 180px;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $index => $proj)
                        <tr class="{{ $proj->status === 'pending' ? 'table-warning bg-opacity-25' : '' }}">
                            <td class="text-center text-muted small">{{ $loop->iteration }}</td>

                            <!-- المشروع والصورة -->
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    @if($proj->cover_url)
                                        <img src="{{ $proj->cover_url }}" alt="{{ $proj->title }}" class="project-avatar">
                                    @else
                                        <div class="project-avatar">
                                            <i class="{{ $proj->faculty_icon }}"></i>
                                        </div>
                                    @endif

                                    <div>
                                        <a href="javascript:void(0)" onclick="showModalSafe('reviewModal{{ $proj->id }}')" class="fw-bold text-dark text-decoration-none d-block mb-1 hover-primary" title="انقر لمعاينة ملف المشروع الكامل">
                                            {{ $proj->title }}
                                        </a>
                                        <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                            @if($proj->project_type)
                                                <span class="badge bg-light text-primary border" style="font-size: 0.72rem;">{{ $proj->project_type }}</span>
                                            @endif
                                            @if($proj->main_category)
                                                <span class="badge bg-light text-success border" style="font-size: 0.72rem;">{{ $proj->main_category }}</span>
                                            @endif
                                            @if($proj->is_featured)
                                                <span class="badge bg-warning text-dark" style="font-size: 0.72rem;"><i class="fas fa-star text-warning"></i> مميز</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- الكلية والتخصص -->
                            <td>
                                <div class="fw-semibold text-primary">
                                    <i class="{{ $proj->faculty_icon }} me-1"></i> {{ $proj->faculty }}
                                </div>
                                <div class="text-muted small">
                                    {{ $proj->department }} &bull; <span class="badge bg-light text-dark border">{{ $proj->graduation_year }}</span>
                                </div>
                            </td>

                            <!-- فريق العمل -->
                            <td>
                                @php
                                    $names = collect($proj->team_list)->pluck('name')->filter()->implode(' • ');
                                @endphp
                                <div class="text-dark small fw-semibold text-truncate" style="max-width: 220px;" title="{{ $names }}">
                                    {{ $names ?: 'غير محدد' }}
                                </div>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <span class="badge bg-light text-secondary border small">{{ count($proj->team_list) }} طلاب</span>
                                    @if($proj->contact_email)
                                        <a href="mailto:{{ $proj->contact_email }}" class="text-muted small" title="{{ $proj->contact_email }}">
                                            <i class="fas fa-envelope text-primary"></i>
                                        </a>
                                    @endif
                                    @if($proj->whatsapp_phone)
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $proj->whatsapp_phone) }}" target="_blank" class="text-success small" title="مراسلة واتساب: {{ $proj->whatsapp_phone }}">
                                            <i class="fab fa-whatsapp"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>

                            <!-- المشرف الأكاديمي -->
                            <td>
                                <div class="small fw-bold text-dark">{{ $proj->supervisor_name ?: '—' }}</div>
                                @if($proj->supervisor_title)
                                    <small class="text-muted">{{ $proj->supervisor_title }}</small>
                                @endif
                            </td>

                            <!-- رقم الجناح -->
                            <td class="text-center">
                                @if($proj->booth_number)
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1 fw-bold">
                                        {{ $proj->booth_number }}
                                    </span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>

                            <!-- الحالة -->
                            <td class="text-center">
                                <span class="badge {{ $proj->status_badge_class }} rounded-pill px-2.5 py-1">
                                    {{ $proj->status_label }}
                                </span>
                            </td>

                            <!-- المشاهدات -->
                            <td class="text-center">
                                <span class="badge bg-light text-muted border">
                                    <i class="fas fa-eye me-1"></i>{{ $proj->views_count }}
                                </span>
                            </td>

                            <!-- الإجراءات -->
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <!-- ملف المراجعة الكامل -->
                                    <button type="button" class="action-circle-btn text-info" title="معاينة ملف المشروع الكامل وحقول المراجعة" onclick="showModalSafe('reviewModal{{ $proj->id }}')">
                                        <i class="fas fa-file-invoice"></i>
                                    </button>

                                    <!-- المعاينة العامة -->
                                    <a href="{{ route('job-fair.public.projects.show', $proj->id) }}" target="_blank" class="action-circle-btn text-secondary" title="معاينة الصفحة العامة">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>

                                    <!-- تعديل -->
                                    <button type="button" class="action-circle-btn text-primary" title="تعديل بيانات المشروع" onclick="editProjectById({{ $proj->id }})">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <!-- حذف -->
                                    <form action="{{ route('job-fair.admin.projects.destroy', $proj->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المشروع نهائياً؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-circle-btn text-danger" title="حذف المشروع">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>

                                <!-- أزرار القرار السريع للمشاريع المعلقة -->
                                @if($proj->status === 'pending')
                                    <div class="d-flex align-items-center justify-content-center gap-1 mt-1.5">
                                        <form action="{{ route('job-fair.admin.projects.update-status', $proj->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="published">
                                            <button type="submit" class="btn btn-xs btn-success py-0.5 px-2 rounded-pill fw-bold" style="font-size: 0.72rem;" title="موافقة ونشر فوري">
                                                <i class="fas fa-check me-1"></i> موافقة
                                            </button>
                                        </form>

                                        <button type="button" class="btn btn-xs btn-outline-danger py-0.5 px-2 rounded-pill fw-bold" style="font-size: 0.72rem;" onclick="showModalSafe('rejectModal{{ $proj->id }}')" title="رفض المشروع">
                                            <i class="fas fa-times me-1"></i> رفض
                                        </button>
                                    </div>
                                @endif
                            </td>
                        </tr>

                        <!-- Modal: Review Project Details -->
                        <div class="modal fade text-start" id="reviewModal{{ $proj->id }}" tabindex="-1" aria-labelledby="reviewModalLabel{{ $proj->id }}" aria-hidden="true" dir="rtl">
                            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
                                    <div class="modal-header bg-primary text-white py-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fas fa-microscope text-warning fs-5"></i>
                                            <div>
                                                <h5 class="modal-title font-weight-bold mb-0" id="reviewModalLabel{{ $proj->id }}">
                                                    ملف مراجعة واعتماد المشروع: {{ $proj->title }}
                                                </h5>
                                                <small class="text-white-50">{{ $proj->faculty }} &bull; {{ $proj->department }} &bull; دفعة {{ $proj->graduation_year }}</small>
                                            </div>
                                        </div>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <!-- شريط القرار الإداري السريع -->
                                        <div class="card bg-light border-0 p-3 mb-4 rounded-3">
                                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="text-muted small fw-bold">الحالة:</span>
                                                    <span class="badge {{ $proj->status_badge_class }} fs-6">{{ $proj->status_label }}</span>
                                                    @if($proj->booth_number)
                                                        <span class="badge bg-success ms-1">جناح رقم: {{ $proj->booth_number }}</span>
                                                    @endif
                                                </div>

                                                <!-- نموذج اعتماد المشروع وتخصيص الجناح -->
                                                <form action="{{ route('job-fair.admin.projects.update-status', $proj->id) }}" method="POST" class="d-flex align-items-center gap-2">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="published">
                                                    <input type="text" name="booth_number" value="{{ $proj->booth_number }}" class="form-control form-control-sm" placeholder="رقم الجناح" style="width: 100px;">
                                                    <div class="form-check form-check-inline m-0">
                                                        <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="featCheckModal{{ $proj->id }}" {{ $proj->is_featured ? 'checked' : '' }}>
                                                        <label class="form-check-label small fw-bold" for="featCheckModal{{ $proj->id }}">مميز</label>
                                                    </div>
                                                    <button type="submit" class="btn btn-sm btn-success fw-bold px-3">
                                                        <i class="fas fa-check-circle me-1"></i> اعتماد ونشر
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                        <div class="row g-4">
                                            <!-- العمود الأيمن: البيانات العامة (Public Fields) -->
                                            <div class="col-lg-7">
                                                <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                                                    <i class="fas fa-globe text-info me-1"></i> البيانات المتاحة للجمهور (22 حقلاً)
                                                </h6>

                                                <table class="table table-bordered table-sm mb-3">
                                                    <tbody>
                                                        <tr>
                                                            <th style="width: 32%;" class="bg-light">1. عنوان المشروع</th>
                                                            <td><strong>{{ $proj->title }}</strong></td>
                                                        </tr>
                                                        <tr>
                                                            <th class="bg-light">2. الكلية</th>
                                                            <td>{{ $proj->faculty }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="bg-light">3. القسم / التخصص</th>
                                                            <td>{{ $proj->department }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="bg-light">4. سنة التخرج</th>
                                                            <td>{{ $proj->graduation_year }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="bg-light">5. نوع المشروع</th>
                                                            <td><span class="badge bg-light text-primary border">{{ $proj->project_type ?? 'غير محدد' }}</span></td>
                                                        </tr>
                                                        <tr>
                                                            <th class="bg-light">6. المجال الرئيسي</th>
                                                            <td><span class="badge bg-light text-success border">{{ $proj->main_category ?? 'غير محدد' }}</span></td>
                                                        </tr>
                                                        <tr>
                                                            <th class="bg-light">7. فريق العمل</th>
                                                            <td>
                                                                <ul class="mb-0 ps-3">
                                                                    @foreach($proj->team_list as $m)
                                                                        <li>{{ $m['name'] ?? $m }} @if(!empty($m['role'])) <span class="text-muted">({{ $m['role'] }})</span> @endif</li>
                                                                    @endforeach
                                                                </ul>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th class="bg-light">8-9. المشرف الأكاديمي</th>
                                                            <td>{{ $proj->supervisor_name ?? '-' }} ({{ $proj->supervisor_title ?? 'مشرف' }})</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="bg-light">10. وصف المشروع</th>
                                                            <td><div class="small">{!! nl2br(e($proj->description)) !!}</div></td>
                                                        </tr>
                                                        <tr>
                                                            <th class="bg-light">11. نبذة مختصرة (Abstract)</th>
                                                            <td><div class="small text-muted">{!! nl2br(e($proj->summary)) !!}</div></td>
                                                        </tr>
                                                        <tr>
                                                            <th class="bg-light">12. المشكلة المعالجة</th>
                                                            <td><div class="small">{!! nl2br(e($proj->problem_statement ?? '-')) !!}</div></td>
                                                        </tr>
                                                        <tr>
                                                            <th class="bg-light">13. الحل المقدم</th>
                                                            <td><div class="small">{!! nl2br(e($proj->solution_statement ?? '-')) !!}</div></td>
                                                        </tr>
                                                        <tr>
                                                            <th class="bg-light">14. أهداف المشروع</th>
                                                            <td><div class="small">{!! nl2br(e($proj->objectives ?? '-')) !!}</div></td>
                                                        </tr>
                                                        <tr>
                                                            <th class="bg-light">15. المواصفات الفنية</th>
                                                            <td><div class="small">{!! nl2br(e($proj->technical_specifications ?? '-')) !!}</div></td>
                                                        </tr>
                                                        <tr>
                                                            <th class="bg-light">16. أبرز النتائج</th>
                                                            <td><div class="small">{!! nl2br(e($proj->key_outcomes ?? '-')) !!}</div></td>
                                                        </tr>
                                                        <tr>
                                                            <th class="bg-light">17. قابلية التسويق</th>
                                                            <td><div class="small">{!! nl2br(e($proj->market_viability ?? '-')) !!}</div></td>
                                                        </tr>
                                                        <tr>
                                                            <th class="bg-light">18-19. البوستر والغلاف</th>
                                                            <td>
                                                                <div class="d-flex gap-2">
                                                                    @if($proj->poster_url)
                                                                        <a href="{{ $proj->poster_url }}" target="_blank" class="btn btn-xs btn-outline-primary"><i class="fas fa-image"></i> البوستر</a>
                                                                    @endif
                                                                    @if($proj->cover_image)
                                                                        <a href="{{ Storage::url($proj->cover_image) }}" target="_blank" class="btn btn-xs btn-outline-secondary"><i class="fas fa-camera"></i> الغلاف</a>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th class="bg-light">20. رابط البرمجي/GitHub</th>
                                                            <td>
                                                                @if($proj->project_url)
                                                                    <a href="{{ $proj->project_url }}" target="_blank">{{ $proj->project_url }}</a>
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th class="bg-light">21. رابط الفيديو</th>
                                                            <td>
                                                                @if($proj->video_url)
                                                                    <a href="{{ $proj->video_url }}" target="_blank">{{ $proj->video_url }}</a>
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th class="bg-light">22. بريد التواصل العام</th>
                                                            <td><a href="mailto:{{ $proj->contact_email }}">{{ $proj->contact_email ?? '-' }}</a></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <!-- العمود الأيسر: الحقول الخاصة بالمسؤول فقط (Admin Only) -->
                                            <div class="col-lg-5">
                                                <div class="admin-confidential-box h-100 shadow-sm">
                                                    <div class="d-flex align-items-center gap-2 mb-3 text-danger fw-bold fs-6">
                                                        <i class="fas fa-user-shield fs-5"></i>
                                                        <span>معلومات خاصة بالمسؤول فقط (لا تظهر للعامة)</span>
                                                    </div>

                                                    <table class="table table-sm table-borderless mb-0">
                                                        <tbody>
                                                            <tr class="border-bottom">
                                                                <th class="text-danger small" style="width: 45%;">1. الرقم الجامعي (رقم القيد):</th>
                                                                <td><strong class="fs-6 text-dark">{{ $proj->student_university_id ?? 'غير مسجل' }}</strong></td>
                                                            </tr>
                                                            <tr class="border-bottom">
                                                                <th class="text-danger small">2. هاتف الواتساب:</th>
                                                                <td>
                                                                    @if($proj->whatsapp_phone)
                                                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $proj->whatsapp_phone) }}" target="_blank" class="fw-bold text-success text-decoration-none">
                                                                            <i class="fab fa-whatsapp me-1"></i>{{ $proj->whatsapp_phone }}
                                                                        </a>
                                                                    @else
                                                                        <span class="text-muted">-</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <tr class="border-bottom">
                                                                <th class="text-danger small">3. متطلبات المشروع:</th>
                                                                <td>{{ $proj->project_requirements ?: 'لا توجد متطلبات خاصة' }}</td>
                                                            </tr>
                                                            <tr class="border-bottom">
                                                                <th class="text-danger small">4. معدات خاصة بالعرض:</th>
                                                                <td>
                                                                    @if($proj->needs_special_equipment)
                                                                        <span class="badge bg-danger text-white">نعم يحتاج معدات</span>
                                                                        <div class="small mt-1 text-danger fw-bold">{{ $proj->special_equipment_details }}</div>
                                                                    @else
                                                                        <span class="badge bg-success text-white">لا يحتاج</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <tr class="border-bottom">
                                                                <th class="text-danger small">5. المتطلبات الإضافية:</th>
                                                                <td>{{ $proj->additional_requirements ?: 'لا توجد' }}</td>
                                                            </tr>
                                                            <tr class="border-bottom">
                                                                <th class="text-danger small">6. الملخص التنفيذي الداخلي:</th>
                                                                <td><div class="small bg-white p-2 rounded border">{!! nl2br(e($proj->executive_summary ?: 'لم يدرج ملخص تنفيذي')) !!}</div></td>
                                                            </tr>
                                                            <tr class="border-bottom">
                                                                <th class="text-danger small">7. حالة النموذج الأولي:</th>
                                                                <td><span class="badge bg-primary">{{ $proj->prototype_status ?: 'غير محدد' }}</span></td>
                                                            </tr>
                                                            <tr class="border-bottom">
                                                                <th class="text-danger small">8. حالة النشر والمراجعة:</th>
                                                                <td><span class="badge {{ $proj->status_badge_class }}">{{ $proj->status_label }}</span></td>
                                                            </tr>
                                                            <tr class="border-bottom">
                                                                <th class="text-danger small">9. رقم الجناح:</th>
                                                                <td><strong>{{ $proj->booth_number ?: 'لم يُحدد بعد' }}</strong></td>
                                                            </tr>
                                                            <tr class="border-bottom">
                                                                <th class="text-danger small">10. تمييز بالرئيسية:</th>
                                                                <td>{{ $proj->is_featured ? 'نعم (مميز)' : 'لا' }}</td>
                                                            </tr>
                                                            <tr class="border-bottom">
                                                                <th class="text-danger small">11. ملاحظات إدارية:</th>
                                                                <td>{{ $proj->admin_notes ?: 'لا توجد ملاحظات' }}</td>
                                                            </tr>
                                                            @if($proj->rejection_reason)
                                                            <tr class="table-danger">
                                                                <th class="text-danger small">سبب الرفض:</th>
                                                                <td class="text-danger fw-bold">{{ $proj->rejection_reason }}</td>
                                                            </tr>
                                                            @endif
                                                        </tbody>
                                                    </table>

                                                    <!-- نموذج تحديث الملاحظات الإدارية السريع -->
                                                    <form action="{{ route('job-fair.admin.projects.update-status', $proj->id) }}" method="POST" class="mt-3 border-top pt-3">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="{{ $proj->status }}">
                                                        <label class="small font-weight-bold text-dark">ملاحظات الإدارة الداخلية:</label>
                                                        <textarea name="admin_notes" class="form-control form-control-sm mb-2" rows="2" placeholder="أدخل أي ملاحظات خاصة باللجنة المنظمة...">{{ $proj->admin_notes }}</textarea>
                                                        <button type="submit" class="btn btn-sm btn-outline-dark w-100">
                                                            <i class="fas fa-save me-1"></i> حفظ الملاحظات
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                                        <button type="button" class="btn btn-primary" onclick="editProjectById({{ $proj->id }})" data-bs-dismiss="modal">
                                            <i class="fas fa-edit me-1"></i> تعديل بيانات المشروع
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal: Reject Project -->
                        <div class="modal fade" id="rejectModal{{ $proj->id }}" tabindex="-1" aria-labelledby="rejectModalLabel{{ $proj->id }}" aria-hidden="true" dir="rtl">
                            <div class="modal-dialog modal-dialog-centered">
                                <form action="{{ route('job-fair.admin.projects.update-status', $proj->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="rejected">
                                    <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
                                        <div class="modal-header bg-danger text-white py-3">
                                            <h5 class="modal-title font-weight-bold" id="rejectModalLabel{{ $proj->id }}">
                                                <i class="fas fa-times-circle me-1"></i> رفض مشروع التخرج
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <p class="mb-3">أنت على وشك رفض مشروع: <strong>«{{ $proj->title }}»</strong>.</p>
                                            <div class="form-group mb-2">
                                                <label class="font-weight-bold small mb-1">سبب الرفض (إجراء إداري رسمي) <span class="text-danger">*</span></label>
                                                <textarea name="rejection_reason" class="form-control" rows="3" required placeholder="وضح سبب رفض المشروع (عدم استيفاء الشروط، تكرار الفكرة، نقص التجهيزات...)..."></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                            <button type="submit" class="btn btn-danger fw-bold">
                                                <i class="fas fa-ban me-1"></i> تأكيد الرفض
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-3x mb-3 d-block text-gray-300"></i>
                                لا توجد مشاريع تخرج مطابقة للفلتر المحدد حالياً.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Add Project -->
<div class="modal fade" id="addProjectModal" tabindex="-1" role="dialog" aria-labelledby="addProjectModalLabel" aria-hidden="true" dir="rtl">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <form id="addProjectForm" action="{{ route('job-fair.admin.projects.store', $fair->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title font-weight-bold" id="addProjectModalLabel">
                        <i class="fas fa-plus-circle me-1"></i> إضافة مشروع تخرج جديد (لوحة الإدارة)
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-12 form-group mb-3">
                            <label class="font-weight-bold">عنوان المشروع <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" required placeholder="عنوان المشروع بالكامل">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group mb-3">
                            <label class="font-weight-bold">الكلية <span class="text-danger">*</span></label>
                            <input type="text" name="faculty" class="form-control" required placeholder="مثال: كلية تقنية المعلومات">
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label class="font-weight-bold">القسم / التخصص <span class="text-danger">*</span></label>
                            <input type="text" name="department" class="form-control" required placeholder="مثال: هندسة البرمجيات">
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label class="font-weight-bold">سنة التخرج <span class="text-danger">*</span></label>
                            <input type="number" name="graduation_year" class="form-control" required value="{{ date('Y') }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold">نوع المشروع</label>
                            <input type="text" name="project_type" class="form-control" placeholder="تطبيق ويب، ذكاء اصطناعي، إنترنت أشياء...">
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold">المجال الرئيسي للمشروع</label>
                            <input type="text" name="main_category" class="form-control" placeholder="تقنية المعلومات، الطاقة، الصحة...">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold">المشرف الأكاديمي</label>
                            <input type="text" name="supervisor_name" class="form-control" placeholder="اسم الأستاذ المشرف">
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold">اللقب والصفة الأكاديمية</label>
                            <input type="text" name="supervisor_title" class="form-control" placeholder="أستاذ دكتور / أستاذ مشارك">
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold">فريق العمل (اسم كل طالب في سطر مستقل)</label>
                        <textarea name="team_members_raw" class="form-control" rows="3" placeholder="محمد علي الورفلي&#10;سارة عبد الله الزنتاني"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold">البريد الإلكتروني العام للتواصل</label>
                            <input type="email" name="contact_email" class="form-control" placeholder="project@example.com">
                        </div>
                        <div class="col-md-3 form-group mb-3">
                            <label class="font-weight-bold">رقم الجناح</label>
                            <input type="text" name="booth_number" class="form-control" placeholder="مثال: IT-01">
                        </div>
                        <div class="col-md-3 form-group mb-3">
                            <label class="font-weight-bold">حالة النشر</label>
                            <select name="status" class="form-control">
                                <option value="published">منشور ومتاح للجمهور</option>
                                <option value="pending">بانتظار الاعتماد (Pending)</option>
                                <option value="draft">مسودة</option>
                                <option value="rejected">مرفوض</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold">نبذة تعريفية مختصرة (Abstract)</label>
                        <textarea name="summary" class="form-control" rows="2" placeholder="نبذة موجزة عن المشروع..."></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold">المشكلة التي يعالجها المشروع</label>
                            <textarea name="problem_statement" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold">الحل الذي يقدمه المشروع</label>
                            <textarea name="solution_statement" class="form-control" rows="2"></textarea>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold">أهداف المشروع</label>
                        <textarea name="objectives" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold">التفاصيل الفنية والمواصفات</label>
                        <textarea name="technical_specifications" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold">وصف المشروع الكامل</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold">أبرز النتائج والمميزات</label>
                            <textarea name="key_outcomes" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold">إمكانية التطوير والتسويق التجاري</label>
                            <textarea name="market_viability" class="form-control" rows="2"></textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold">بوستر المشروع (Poster)</label>
                            <input type="file" name="poster_image" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold">صورة الغلاف (Cover)</label>
                            <input type="file" name="cover_image" class="form-control" accept="image/*">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold">رابط المشروع البرمجي أو GitHub</label>
                            <input type="url" name="project_url" class="form-control" placeholder="https://github.com/...">
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold">رابط فيديو العرض (Demo / YouTube)</label>
                            <input type="url" name="video_url" class="form-control" placeholder="https://youtube.com/...">
                        </div>
                    </div>

                    <!-- القسم الخاص بالمسؤول فقط -->
                    <div class="card border-danger p-3 bg-light mb-3">
                        <h6 class="font-weight-bold text-danger mb-3">
                            <i class="fas fa-lock me-1"></i> معلومات خاصة بالمسؤول فقط (لا تظهر للعامة)
                        </h6>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="font-weight-bold text-danger">الرقم الجامعي / رقم القيد</label>
                                <input type="text" name="student_university_id" class="form-control" placeholder="رقم القيد الجامعي">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="font-weight-bold text-danger">رقم الهاتف المسجل بالواتساب</label>
                                <input type="text" name="whatsapp_phone" class="form-control" placeholder="091XXXXXXX">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="font-weight-bold text-danger">حالة النموذج الأولي</label>
                                <input type="text" name="prototype_status" class="form-control" placeholder="فكرة، نموذج أولي تجريبي، منتج جاهز...">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="font-weight-bold text-danger">المتطلبات التي يحتاجها المشروع</label>
                                <input type="text" name="project_requirements" class="form-control" placeholder="طاولات، توصيلات، إنترنت...">
                            </div>
                            <div class="col-md-12 form-group mb-3">
                                <div class="form-check">
                                    <input type="checkbox" name="needs_special_equipment" value="1" class="form-check-input" id="addNeedsEq">
                                    <label class="form-check-label font-weight-bold text-danger" for="addNeedsEq">
                                        يحتاج معدات خاصة أثناء العرض
                                    </label>
                                </div>
                                <input type="text" name="special_equipment_details" class="form-control mt-2" placeholder="تفاصيل المعدات الخاصة المطلوبة...">
                            </div>
                            <div class="col-md-12 form-group mb-3">
                                <label class="font-weight-bold text-danger">الملخص التنفيذي للمراجعة الداخلية</label>
                                <textarea name="executive_summary" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="col-md-12 form-group mb-3">
                                <label class="font-weight-bold text-danger">ملاحظات أو إجراءات إدارية</label>
                                <textarea name="admin_notes" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" name="is_featured" value="1" class="form-check-input" id="addProjFeatured">
                        <label class="form-check-label font-weight-bold text-warning" for="addProjFeatured">
                            <i class="fas fa-star"></i> تمييز المشروع في الصفحة الرئيسية للمعرض
                        </label>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary fw-bold">
                        <i class="fas fa-save me-1"></i> حفظ المشروع
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Project -->
<div class="modal fade" id="editProjectModal" tabindex="-1" role="dialog" aria-labelledby="editProjectModalLabel" aria-hidden="true" dir="rtl">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <form id="editProjectForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
                <div class="modal-header bg-dark text-white py-3">
                    <h5 class="modal-title font-weight-bold" id="editProjectModalLabel">
                        <i class="fas fa-edit me-1"></i> تعديل بيانات مشروع التخرج
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-12 form-group mb-3">
                            <label class="font-weight-bold">عنوان المشروع <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="edit_title" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group mb-3">
                            <label class="font-weight-bold">الكلية <span class="text-danger">*</span></label>
                            <input type="text" name="faculty" id="edit_faculty" class="form-control" required>
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label class="font-weight-bold">القسم / التخصص <span class="text-danger">*</span></label>
                            <input type="text" name="department" id="edit_department" class="form-control" required>
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label class="font-weight-bold">سنة التخرج <span class="text-danger">*</span></label>
                            <input type="number" name="graduation_year" id="edit_graduation_year" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold">نوع المشروع</label>
                            <input type="text" name="project_type" id="edit_project_type" class="form-control">
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold">المجال الرئيسي للمشروع</label>
                            <input type="text" name="main_category" id="edit_main_category" class="form-control">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold">المشرف الأكاديمي</label>
                            <input type="text" name="supervisor_name" id="edit_supervisor_name" class="form-control">
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold">اللقب والصفة الأكاديمية</label>
                            <input type="text" name="supervisor_title" id="edit_supervisor_title" class="form-control">
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold">فريق العمل (اسم كل طالب في سطر)</label>
                        <textarea name="team_members_raw" id="edit_team_members_raw" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold">البريد الإلكتروني العام للتواصل</label>
                            <input type="email" name="contact_email" id="edit_contact_email" class="form-control">
                        </div>
                        <div class="col-md-3 form-group mb-3">
                            <label class="font-weight-bold">رقم الجناح</label>
                            <input type="text" name="booth_number" id="edit_booth_number" class="form-control">
                        </div>
                        <div class="col-md-3 form-group mb-3">
                            <label class="font-weight-bold">حالة النشر</label>
                            <select name="status" id="edit_status" class="form-control">
                                <option value="published">منشور ومتاح للجمهور</option>
                                <option value="pending">بانتظار الاعتماد (Pending)</option>
                                <option value="draft">مسودة</option>
                                <option value="rejected">مرفوض</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold">نبذة تعريفية مختصرة (Abstract)</label>
                        <textarea name="summary" id="edit_summary" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold">المشكلة التي يعالجها المشروع</label>
                            <textarea name="problem_statement" id="edit_problem_statement" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold">الحل الذي يقدمه المشروع</label>
                            <textarea name="solution_statement" id="edit_solution_statement" class="form-control" rows="2"></textarea>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold">أهداف المشروع</label>
                        <textarea name="objectives" id="edit_objectives" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold">التفاصيل الفنية والمواصفات</label>
                        <textarea name="technical_specifications" id="edit_technical_specifications" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold">وصف المشروع الكامل</label>
                        <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold">أبرز النتائج والمميزات</label>
                            <textarea name="key_outcomes" id="edit_key_outcomes" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold">قابلية التطوير والتسويق التجاري</label>
                            <textarea name="market_viability" id="edit_market_viability" class="form-control" rows="2"></textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold">تحديث بوستر المشروع</label>
                            <input type="file" name="poster_image" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold">تحديث صورة الغلاف</label>
                            <input type="file" name="cover_image" class="form-control" accept="image/*">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold">رابط المشروع البرمجي أو GitHub</label>
                            <input type="url" name="project_url" id="edit_project_url" class="form-control">
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold">رابط فيديو العرض (Demo / YouTube)</label>
                            <input type="url" name="video_url" id="edit_video_url" class="form-control">
                        </div>
                    </div>

                    <!-- القسم الخاص بالمسؤول فقط في التعديل -->
                    <div class="card border-danger p-3 bg-light mb-3">
                        <h6 class="font-weight-bold text-danger mb-3">
                            <i class="fas fa-lock me-1"></i> معلومات خاصة بالمسؤول فقط (لا تظهر للعامة)
                        </h6>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="font-weight-bold text-danger">الرقم الجامعي / رقم القيد</label>
                                <input type="text" name="student_university_id" id="edit_student_university_id" class="form-control">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="font-weight-bold text-danger">رقم الهاتف المسجل بالواتساب</label>
                                <input type="text" name="whatsapp_phone" id="edit_whatsapp_phone" class="form-control">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="font-weight-bold text-danger">حالة النموذج الأولي</label>
                                <input type="text" name="prototype_status" id="edit_prototype_status" class="form-control">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="font-weight-bold text-danger">المتطلبات التي يحتاجها المشروع</label>
                                <input type="text" name="project_requirements" id="edit_project_requirements" class="form-control">
                            </div>
                            <div class="col-md-12 form-group mb-3">
                                <div class="form-check">
                                    <input type="checkbox" name="needs_special_equipment" value="1" class="form-check-input" id="edit_needs_special_equipment">
                                    <label class="form-check-label font-weight-bold text-danger" for="edit_needs_special_equipment">
                                        يحتاج معدات خاصة أثناء العرض
                                    </label>
                                </div>
                                <input type="text" name="special_equipment_details" id="edit_special_equipment_details" class="form-control mt-2" placeholder="تفاصيل المعدات الخاصة...">
                            </div>
                            <div class="col-md-12 form-group mb-3">
                                <label class="font-weight-bold text-danger">الملخص التنفيذي للمراجعة الداخلية</label>
                                <textarea name="executive_summary" id="edit_executive_summary" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="col-md-12 form-group mb-3">
                                <label class="font-weight-bold text-danger">ملاحظات أو إجراءات إدارية</label>
                                <textarea name="admin_notes" id="edit_admin_notes" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="col-md-12 form-group mb-3">
                                <label class="font-weight-bold text-danger">سبب الرفض (إن وُجد)</label>
                                <textarea name="rejection_reason" id="edit_rejection_reason" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" name="is_featured" value="1" class="form-check-input" id="editProjFeatured">
                        <label class="form-check-label font-weight-bold text-warning" for="editProjFeatured">
                            <i class="fas fa-star"></i> تمييز المشروع في الصفحة الرئيسية للمعرض
                        </label>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary fw-bold">
                        <i class="fas fa-save me-1"></i> حفظ التعديلات
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const fairProjects = @json($allProjects->keyBy('id'));

    function showModalSafe(modalId) {
        const el = document.getElementById(modalId);
        if (el) {
            const modal = bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el);
            modal.show();
        }
    }

    function editProjectById(id) {
        const proj = fairProjects[id];
        if (!proj) {
            console.error('Project not found with ID:', id);
            return;
        }

        document.getElementById('editProjectForm').action = "/admin/job-fair/projects/" + proj.id;
        document.getElementById('edit_title').value = proj.title || '';
        document.getElementById('edit_faculty').value = proj.faculty || '';
        document.getElementById('edit_department').value = proj.department || '';
        document.getElementById('edit_graduation_year').value = proj.graduation_year || 2026;
        document.getElementById('edit_project_type').value = proj.project_type || '';
        document.getElementById('edit_main_category').value = proj.main_category || '';
        document.getElementById('edit_supervisor_name').value = proj.supervisor_name || '';
        document.getElementById('edit_supervisor_title').value = proj.supervisor_title || '';
        document.getElementById('edit_contact_email').value = proj.contact_email || '';
        document.getElementById('edit_booth_number').value = proj.booth_number || '';
        document.getElementById('edit_status').value = proj.status || 'published';
        document.getElementById('edit_summary').value = proj.summary || '';
        document.getElementById('edit_problem_statement').value = proj.problem_statement || '';
        document.getElementById('edit_solution_statement').value = proj.solution_statement || '';
        document.getElementById('edit_objectives').value = proj.objectives || '';
        document.getElementById('edit_technical_specifications').value = proj.technical_specifications || '';
        document.getElementById('edit_description').value = proj.description || '';
        document.getElementById('edit_key_outcomes').value = proj.key_outcomes || '';
        document.getElementById('edit_market_viability').value = proj.market_viability || '';
        document.getElementById('edit_project_url').value = proj.project_url || '';
        document.getElementById('edit_video_url').value = proj.video_url || '';

        // الحقول الخاصة بالمسؤول فقط
        document.getElementById('edit_student_university_id').value = proj.student_university_id || '';
        document.getElementById('edit_whatsapp_phone').value = proj.whatsapp_phone || '';
        document.getElementById('edit_prototype_status').value = proj.prototype_status || '';
        document.getElementById('edit_project_requirements').value = proj.project_requirements || '';
        document.getElementById('edit_needs_special_equipment').checked = Boolean(proj.needs_special_equipment);
        document.getElementById('edit_special_equipment_details').value = proj.special_equipment_details || '';
        document.getElementById('edit_executive_summary').value = proj.executive_summary || '';
        document.getElementById('edit_admin_notes').value = proj.admin_notes || '';
        document.getElementById('edit_rejection_reason').value = proj.rejection_reason || '';
        document.getElementById('editProjFeatured').checked = Boolean(proj.is_featured);

        // أعضاء الفريق
        let teamLines = '';
        if (Array.isArray(proj.team_members)) {
            teamLines = proj.team_members.map(m => m.name || m).filter(Boolean).join('\n');
        } else if (typeof proj.team_members === 'string') {
            try {
                const parsed = JSON.parse(proj.team_members);
                if (Array.isArray(parsed)) {
                    teamLines = parsed.map(m => m.name || m).filter(Boolean).join('\n');
                } else {
                    teamLines = proj.team_members;
                }
            } catch(e) {
                teamLines = proj.team_members;
            }
        }
        document.getElementById('edit_team_members_raw').value = teamLines;

        showModalSafe('editProjectModal');
    }
</script>
@endpush
@endsection
