@extends('layouts.app')

@section('title', $fair->title . ' - تفاصيل المعرض')

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
        font-size: 0.78rem;
    }
    .action-circle-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.08);
    }

    .reg-mobile-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid rgba(0, 0, 0, 0.06);
        padding: 0.85rem;
        margin-bottom: 0.6rem;
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
<div class="container-fluid">

    {{-- Breadcrumbs --}}
    @include('components.breadcrumbs', [
        'items' => [
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'إدارة المعارض والفعاليات', 'url' => route('job-fair.admin.index')],
            ['label' => $fair->title, 'active' => true],
        ]
    ])

    <!-- Hero Header مع إبراز الشعار والأزرار الموحدة -->
    <div class="job-fair-detail-hero mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('job-fair.admin.index') }}" class="btn btn-outline-light rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px; background: rgba(255, 255, 255, 0.12); border-color: rgba(255, 255, 255, 0.3);" title="العودة لقائمة المعارض والفعاليات">
                    <i class="fas fa-arrow-right"></i>
                </a>

                <!-- شعار المعرض البارز -->
                <img src="{{ $fair->logo_url }}" alt="{{ $fair->title }}" class="fair-logo-badge" onerror="this.onerror=null;this.src='{{ asset('images/job_fair_logo.png') }}';">

                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h2 class="fw-bold mb-0 fs-4 text-white">
                            {{ $fair->title }}
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

                    @if($fair->subtitle)
                        <p class="text-white-50 small mb-1 mt-0.5">{{ $fair->subtitle }}</p>
                    @endif

                    <div class="d-flex align-items-center flex-wrap gap-3 text-white-50 small mt-1">
                        <span><i class="fas fa-calendar-alt me-1 text-warning"></i>{{ $fair->event_date->format('Y-m-d') }}</span>
                        @if($fair->location)
                        <span><i class="fas fa-map-marker-alt me-1 text-danger"></i>{{ $fair->location }}</span>
                        @endif
                        @if($fair->start_time)
                        <span><i class="fas fa-clock me-1 text-info"></i>{{ substr($fair->start_time, 0, 5) }} @if($fair->end_time) - {{ substr($fair->end_time, 0, 5) }} @endif</span>
                        @endif
                    </div>

                    <!-- شعارات الشركاء في الهيدر -->
                    <div class="d-flex align-items-center flex-wrap gap-2 mt-2">
                        <div class="partner-mini-badge" title="مكتب تدريب وتأهيل الخريجين — جامعة طرابلس">
                            <img src="{{ asset('images/gto_logo.jpg') }}" alt="مكتب تدريب الخريجين" onerror="this.src='{{ asset('images/logo.jpg') }}'">
                        </div>
                        <div class="partner-mini-badge" title="الراعي الاستراتيجي: شركة الواحة لتنظيم المعارض والمؤتمرات">
                            <img src="{{ asset('images/wahaexpo_logo.png') }}" alt="شركة الواحة للمعارض">
                        </div>
                        <span class="d-inline-flex align-items-center gap-1 text-warning small fw-bold ms-1" style="font-size: 0.76rem;">
                            <i class="fas fa-crown"></i> الراعي الاستراتيجي
                        </span>
                    </div>
                </div>
            </div>

            <!-- أزرار الإجراءات الموحدة والأنيقة -->
            <div class="fair-actions-toolbar">
                <!-- ماسح QR (ذهبي بارز) -->
                <a href="{{ route('job-fair.admin.attendance', $fair->id) }}" class="fair-btn-action btn btn-warning text-dark shadow-sm">
                    <i class="fas fa-qrcode"></i>
                    <span>ماسح QR</span>
                </a>

                <!-- المتابعة اللحظية للحضور -->
                <a href="{{ route('job-fair.admin.live', $fair->id) }}" class="fair-btn-action btn btn-info text-white shadow-sm" target="_blank" style="background: linear-gradient(135deg, #0284c7, #0369a1); border: none;" title="لوحة المتابعة اللحظية للحضور والإحصائيات">
                    <i class="fas fa-chart-line"></i>
                    <span>المتابعة اللحظية</span>
                </a>

                <!-- تعديل بيانات المعرض -->
                <a href="{{ route('job-fair.admin.edit', $fair->id) }}" class="fair-btn-action btn btn-light bg-white text-primary shadow-sm">
                    <i class="fas fa-edit"></i>
                    <span>تعديل</span>
                </a>

                <!-- تصدير Excel -->
                <a href="{{ route('job-fair.admin.export', $fair->id) }}" class="fair-btn-action fair-btn-glass">
                    <i class="fas fa-file-excel text-success"></i>
                    <span>تصدير</span>
                </a>

                <!-- صفحة المعرض العامة -->
                <a href="{{ route('job-fair.public', $fair->id) }}" class="fair-btn-action fair-btn-glass" target="_blank" title="الصفحة العامة للفعالية">
                    <i class="fas fa-globe text-info"></i>
                    <span>صفحة عامة</span>
                </a>

                <!-- الرعاة -->
                <a href="#sponsorsSection" class="fair-btn-action fair-btn-glass">
                    <i class="fas fa-crown text-warning"></i>
                    <span>الرعاة ({{ $fair->sponsors->count() }})</span>
                </a>

                <!-- البرنامج العلمي والفعاليات -->
                <a href="{{ route('job-fair.admin.events.index', $fair->id) }}" class="fair-btn-action btn btn-warning text-dark shadow-sm fw-bold">
                    <i class="fas fa-graduation-cap"></i>
                    <span>البرنامج العلمي ({{ $fair->events->count() }})</span>
                </a>

                <!-- مشاريع التخرج -->
                <a href="{{ route('job-fair.admin.projects.index', $fair->id) }}" class="fair-btn-action btn btn-light bg-white text-dark shadow-sm fw-bold">
                    <i class="fas fa-lightbulb text-warning"></i>
                    <span>مشاريع التخرج ({{ $fair->projects->count() }})</span>
                </a>

                <!-- الهوية البصرية -->
                <a href="#brandIdentitySection" class="fair-btn-action fair-btn-glass">
                    <i class="fas fa-palette text-warning"></i>
                    <span>الهوية البصرية</span>
                </a>

                <!-- إدارة زوار المعرض -->
                <a href="{{ route('job-fair.admin.visitors.index', $fair->id) }}" class="fair-btn-action btn btn-success text-white shadow-sm fw-bold">
                    <i class="fas fa-id-badge"></i>
                    <span>إدارة الزوار ({{ $stats['total_visitors'] ?? $fair->visitors->count() }})</span>
                </a>

                <!-- قائمة تغيير الحالة منسدلة أنيقة -->
                <div class="dropdown d-inline-block position-relative">
                    <button class="fair-btn-action fair-btn-glass dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-sliders-h"></i>
                        <span>الحالة</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 p-1.5" style="min-width: 190px; z-index: 1060;">
                        @php
                            $statusIcons = [
                                'draft' => ['color' => 'text-secondary', 'icon' => 'fas fa-file-alt'],
                                'published' => ['color' => 'text-success', 'icon' => 'fas fa-check-circle'],
                                'ongoing' => ['color' => 'text-warning', 'icon' => 'fas fa-play-circle'],
                                'completed' => ['color' => 'text-primary', 'icon' => 'fas fa-flag-checkered']
                            ];
                        @endphp
                        @foreach(['draft'=>'مسودة','published'=>'منشور ومتاح','ongoing'=>'جارٍ الآن','completed'=>'منتهي'] as $val => $label)
                        <li>
                            <form action="{{ route('job-fair.admin.status', $fair->id) }}" method="POST" class="m-0">
                                @csrf
                                <input type="hidden" name="status" value="{{ $val }}">
                                <button type="submit" class="dropdown-item rounded-2 py-2 px-3 small d-flex align-items-center justify-content-between {{ $fair->status == $val ? 'active fw-bold' : '' }}">
                                    <span class="d-flex align-items-center gap-2">
                                        <i class="{{ $statusIcons[$val]['icon'] ?? 'fas fa-circle' }} {{ $fair->status == $val ? 'text-white' : ($statusIcons[$val]['color'] ?? '') }}"></i>
                                        <span>{{ $label }}</span>
                                    </span>
                                    @if($fair->status == $val)
                                        <i class="fas fa-check text-white ms-2"></i>
                                    @endif
                                </button>
                            </form>
                        </li>
                        @endforeach
                    </ul>
                </div>

                <!-- تصفير الحضور -->
                <form action="{{ route('job-fair.admin.reset-attendance', $fair->id) }}" method="POST" class="d-inline m-0">
                    @csrf
                    <button type="submit" class="fair-btn-action fair-btn-glass text-white-50" onclick="return confirm('تحذير: هل أنت متأكد من رغبتك في إعادة تهيئة سجلات الحضور بالكامل؟')" title="إعادة تعيين حضور المعرض">
                        <i class="fas fa-undo"></i>
                        <span>تصفير</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success rounded-3 border-0 shadow-sm mb-4">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
    </div>
    @endif

    <!-- بطاقات الإحصائيات -->
    <div class="row g-2 g-md-3 mb-4">
        @include('components.stat-card', [
            'col' => 'col-6 col-md-4 col-xl',
            'title' => 'خريج مسجل',
            'value' => $stats['total_registered'] ?? 0,
            'icon' => 'fas fa-user-graduate',
            'color' => 'primary'
        ])
        @include('components.stat-card', [
            'col' => 'col-6 col-md-4 col-xl',
            'title' => 'زائر ومستضاف',
            'value' => $stats['total_visitors'] ?? $fair->visitors->count(),
            'icon' => 'fas fa-id-badge',
            'color' => 'success',
            'link' => route('job-fair.admin.visitors.index', $fair->id),
            'description' => 'انقر لإدارة الزوار'
        ])
        @include('components.stat-card', [
            'col' => 'col-6 col-md-4 col-xl',
            'title' => 'حضروا المعرض',
            'value' => $stats['total_attended'] ?? 0,
            'icon' => 'fas fa-user-check',
            'color' => 'success'
        ])
        @include('components.stat-card', [
            'col' => 'col-6 col-md-4 col-xl',
            'title' => 'شركة مشاركة',
            'value' => $stats['total_companies'] ?? 0,
            'icon' => 'fas fa-building',
            'color' => 'warning'
        ])
        @include('components.stat-card', [
            'col' => 'col-6 col-md-4 col-xl',
            'title' => 'يوم متبقي',
            'value' => $stats['days_remaining'] ?? 0,
            'icon' => 'fas fa-clock',
            'color' => 'info'
        ])
    </div>

    <!-- لوحة الوصول السريع وإعدادات نشر البرامج العلمية ومشاريع التخرج -->
    <div class="row g-3 mb-4">
        <!-- البرنامج العلمي والتدريبي -->
        <div class="col-md-6">
            <div class="card-modern p-3 p-md-4 d-flex flex-column justify-content-between h-100 gap-3" style="background: linear-gradient(135deg, rgba(238,202,62,0.1), rgba(245,158,11,0.03)); border: 1.5px solid rgba(245,158,11,0.25);">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 50px; height: 50px; background: rgba(245,158,11,0.15); color: #b45309; font-size: 1.35rem;">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h6 class="fw-bold mb-0 text-dark">البرنامج العلمي والتدريبي</h6>
                                <span id="badge-program-status" class="badge {{ $fair->is_program_published ? 'bg-success text-white' : 'bg-warning text-dark' }} px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">
                                    <i class="fas {{ $fair->is_program_published ? 'fa-eye' : 'fa-clock' }} me-1"></i>
                                    <span>{{ $fair->is_program_published ? 'منشور للزوار' : 'Coming Soon (قريباً)' }}</span>
                                </span>
                            </div>
                            <p class="text-muted small mb-0 mt-1">{{ $fair->events->count() }} فعالية وورشة عمل &bull; إشراف وإدارة المتحدثين والحضور</p>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between pt-2 border-top border-warning border-opacity-25 flex-wrap gap-2">
                    <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                        <input class="form-check-input feature-toggle-switch" type="checkbox" role="switch" id="toggleProgramSwitch" 
                               data-feature="program" 
                               data-url="{{ route('job-fair.admin.toggle-feature', $fair->id) }}"
                               {{ $fair->is_program_published ? 'checked' : '' }}
                               style="width: 2.8rem; height: 1.4rem; cursor: pointer;">
                        <label class="form-check-label fw-bold small text-dark" for="toggleProgramSwitch" style="cursor: pointer;">
                            عرض الفعاليات للجمهور
                        </label>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('job-fair.public.program', $fair->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-sm" title="معاينة الصفحة العامة للبرنامج">
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                        <a href="{{ route('job-fair.admin.events.index', $fair->id) }}" class="btn btn-sm btn-warning text-dark fw-bold rounded-pill px-3 shadow-sm">
                            <span>إدارة الفعاليات</span>
                            <i class="fas fa-arrow-left ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- معرض وأرشيف مشاريع التخرج -->
        <div class="col-md-6">
            <div class="card-modern p-3 p-md-4 d-flex flex-column justify-content-between h-100 gap-3" style="background: linear-gradient(135deg, rgba(4,93,176,0.1), rgba(3,105,161,0.03)); border: 1.5px solid rgba(4,93,176,0.25);">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 50px; height: 50px; background: rgba(4,93,176,0.15); color: #045db0; font-size: 1.35rem;">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h6 class="fw-bold mb-0 text-dark">معرض وأرشيف مشاريع التخرج</h6>
                                <span id="badge-projects-status" class="badge {{ $fair->is_projects_published ? 'bg-success text-white' : 'bg-warning text-dark' }} px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">
                                    <i class="fas {{ $fair->is_projects_published ? 'fa-eye' : 'fa-clock' }} me-1"></i>
                                    <span>{{ $fair->is_projects_published ? 'منشور للزوار' : 'Coming Soon (قريباً)' }}</span>
                                </span>
                            </div>
                            <p class="text-muted small mb-0 mt-1">{{ $fair->projects->count() }} مشروع تخرج مسجّل &bull; توليد QR Code، بوسترات وملفات</p>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between pt-2 border-top border-primary border-opacity-25 flex-wrap gap-2">
                    <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                        <input class="form-check-input feature-toggle-switch" type="checkbox" role="switch" id="toggleProjectsSwitch" 
                               data-feature="projects" 
                               data-url="{{ route('job-fair.admin.toggle-feature', $fair->id) }}"
                               {{ $fair->is_projects_published ? 'checked' : '' }}
                               style="width: 2.8rem; height: 1.4rem; cursor: pointer;">
                        <label class="form-check-label fw-bold small text-dark" for="toggleProjectsSwitch" style="cursor: pointer;">
                            عرض المشاريع للجمهور
                        </label>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('job-fair.public.projects', $fair->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-sm" title="معاينة الصفحة العامة للمشاريع">
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                        <a href="{{ route('job-fair.admin.projects.index', $fair->id) }}" class="btn btn-sm text-white fw-bold rounded-pill px-3 shadow-sm" style="background: #045db0;">
                            <span>إدارة المشاريع</span>
                            <i class="fas fa-arrow-left ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 g-md-4">
        <!-- الشركات المشاركة -->
        <div class="col-12 col-lg-5">
            <div class="card-modern h-100 p-3 p-md-4">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                    <h5 class="fw-bold mb-0 text-dark fs-6">
                        <i class="fas fa-building me-2 text-primary"></i>الشركات المشاركة
                    </h5>
                    <button class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm d-inline-flex align-items-center gap-1" data-bs-toggle="collapse" data-bs-target="#addCompanyForm">
                        <i class="fas fa-plus"></i>
                        <span>إضافة شركة</span>
                    </button>
                </div>

                <!-- Add Company Form Collapse -->
                <div class="collapse mb-3" id="addCompanyForm">
                    <form action="{{ route('job-fair.admin.add-company', $fair->id) }}" method="POST"
                          class="p-3 rounded-3" style="background: #f8fafc; border: 1px dashed #cbd5e1">
                        @csrf
                        <div class="row g-2">
                            <div class="col-12">
                                <label class="form-label-modern small fw-bold">اختر الشركة</label>
                                <select name="company_id" class="form-select form-select-sm" required>
                                    <option value="">-- اختر شركة --</option>
                                    @php $existingIds = $fair->companies->pluck('company_id')->toArray(); @endphp
                                    @foreach(App\Models\Company::whereNotIn('id', $existingIds)->get() as $co)
                                    <option value="{{ $co->id }}">{{ $co->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <input type="text" name="booth_number" class="form-control form-control-sm" placeholder="رقم الجناح (A1)">
                            </div>
                            <div class="col-6">
                                <input type="number" name="available_positions" class="form-control form-control-sm" placeholder="عدد الوظائف" min="0">
                            </div>
                            <div class="col-12">
                                <textarea name="requirements" class="form-control form-control-sm" rows="2" placeholder="متطلبات التوظيف..."></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-sm btn-primary-modern w-100 rounded-pill">
                                    <i class="fas fa-check me-1"></i>تأكيد إضافة الشركة
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Companies List -->
                @if($fair->companies->isEmpty())
                <p class="text-muted text-center py-4 mb-0 small">لا توجد شركات مضافة في هذا المعرض حتى الآن</p>
                @else
                <div class="d-flex flex-column gap-2">
                    @foreach($fair->companies as $fc)
                    <div class="d-flex align-items-center justify-content-between p-2.5 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                        <div class="d-flex align-items-center gap-2.5">
                            @if($fc->company && $fc->company->logo)
                                <img src="{{ Storage::url($fc->company->logo) }}" alt="{{ $fc->company->name }}" class="rounded-circle border p-0.5 bg-white shadow-xs" style="width: 38px; height: 38px; object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-light text-primary border d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 38px; height: 38px; font-size: 0.95rem;">
                                    <i class="fas fa-building"></i>
                                </div>
                            @endif
                            <div>
                                <h6 class="fw-bold text-dark mb-0 fs-6">{{ $fc->company->name }}</h6>
                                <div class="d-flex gap-1 flex-wrap mt-1">
                                    @if($fc->booth_number)
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25" style="font-size: 0.72rem;">جناح {{ $fc->booth_number }}</span>
                                    @endif
                                    @if($fc->available_positions)
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25" style="font-size: 0.72rem;">{{ $fc->available_positions }} وظائف</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <form action="{{ route('job-fair.admin.remove-company', [$fair->id, $fc->company_id]) }}" method="POST" class="m-0">
                            @csrf @method('DELETE')
                            <button type="submit" class="action-circle-btn text-danger"
                                    onclick="return confirm('هل أنت متأكد من حذف الشركة من المعرض؟')" title="حذف الشركة">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        <!-- قائمة الخريجين المسجلين -->
        <div class="col-12 col-lg-7">
            <div class="card-modern h-100 p-3 p-md-4">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                    <h5 class="fw-bold mb-0 text-dark fs-6">
                        <i class="fas fa-user-graduate me-2 text-primary"></i>الخريجون المسجلون
                    </h5>
                    <span class="badge bg-primary rounded-pill px-2 py-1">{{ $stats['total_registered'] ?? 0 }}</span>
                </div>

                @if($registrations->isEmpty())
                <p class="text-muted text-center py-4 mb-0 small">لا توجد تسجيلات حتى الآن</p>
                @else
                {{-- 🖥️ عرض سطح المكتب --}}
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-2 px-3 border-0 small fw-bold">الرقم</th>
                                <th class="py-2 border-0 small fw-bold">الاسم</th>
                                <th class="py-2 border-0 small fw-bold">التخصص</th>
                                <th class="py-2 border-0 small fw-bold">الحضور</th>
                                <th class="py-2 border-0 text-center small fw-bold">البطاقة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($registrations as $reg)
                            <tr>
                                <td class="px-3"><span class="badge bg-light text-primary border" style="font-size: 0.72rem;">{{ $reg->registration_number }}</span></td>
                                <td class="fw-bold text-dark">{{ $reg->graduate->name }}</td>
                                <td><span class="text-muted small">{{ $reg->graduate->major ?? '—' }}</span></td>
                                <td>
                                    @if($reg->attended)
                                    <span class="badge bg-success text-white rounded-pill px-2 py-1" style="font-size: 0.7rem;"><i class="fas fa-check me-1"></i>حضر</span>
                                    @else
                                    <span class="badge bg-secondary text-white rounded-pill px-2 py-1" style="font-size: 0.7rem;">غائب</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('job-fair.my-ticket', $reg->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1" target="_blank" style="font-size: 0.75rem;">
                                        <i class="fas fa-ticket-alt me-1"></i>عرض
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- 📱 عرض الموبايل --}}
                <div class="d-md-none">
                    @foreach($registrations as $reg)
                    <div class="reg-mobile-card">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="fw-bold text-dark mb-0 fs-6">{{ $reg->graduate->name }}</h6>
                            @if($reg->attended)
                            <span class="badge bg-success text-white rounded-pill" style="font-size: 0.68rem;">حضر</span>
                            @else
                            <span class="badge bg-secondary text-white rounded-pill" style="font-size: 0.68rem;">غائب</span>
                            @endif
                        </div>
                        <div class="d-flex justify-content-between align-items-center text-muted small mt-2">
                            <span><i class="fas fa-graduation-cap me-1"></i>{{ $reg->graduate->major ?? '—' }}</span>
                            <a href="{{ route('job-fair.my-ticket', $reg->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-0" target="_blank" style="font-size: 0.75rem;">
                                <i class="fas fa-ticket-alt me-1"></i>التذكرة
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>

                @if(method_exists($registrations, 'links'))
                    <div class="mt-3 d-flex justify-content-center">
                        {{ $registrations->links() }}
                    </div>
                @endif
                @endif
            </div>
        </div>
    </div>

    <!-- قسم البرنامج العلمي والفعاليات التدريبية المصاحبة -->
    <div class="row mt-4 mb-4">
        <div class="col-12">
            <div class="card-modern p-3 p-md-4" id="scientificProgramSection">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3 flex-wrap gap-2">
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <h5 class="fw-bold mb-0 text-dark fs-6">
                                <i class="fas fa-graduation-cap text-primary me-2"></i>البرنامج العلمي والفعاليات التدريبية ({{ $fair->events->count() }} فعالية)
                            </h5>
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-2.5 py-1 small fw-bold">
                                رحلة الجاهزية المهنية
                            </span>
                        </div>
                        <p class="text-muted small mb-0 mt-1">إدارة فعاليات الماستر كلاس، ورش العمل التطبيقية، والجلسات الحوارية، وتعديل المتحدثين والمقاعد والمحاور.</p>
                    </div>

                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <a href="{{ route('job-fair.public.program', $fair->id) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-xs">
                            <i class="fas fa-external-link-alt"></i>
                            <span>معاينة صفحة البرنامج</span>
                        </a>
                        <a href="{{ route('job-fair.admin.events.index', $fair->id) }}" class="btn btn-sm btn-primary rounded-pill px-3.5 fw-bold d-inline-flex align-items-center gap-1.5 shadow-xs">
                            <i class="fas fa-cog"></i>
                            <span>إدارة وتعديل الفعاليات بالكامل</span>
                        </a>
                    </div>
                </div>

                @if($fair->events->isEmpty())
                    <p class="text-muted text-center py-4 mb-0 small">لا توجد فعاليات علمية مضافة في هذا المعرض حتى الآن</p>
                @else
                    <div class="row g-3">
                        @foreach($fair->events as $ev)
                        <div class="col-12 col-md-6 col-xl-4">
                            <div class="card border rounded-3 p-3 h-100 bg-white shadow-xs d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge {{ $ev->type === 'masterclass' ? 'bg-warning text-dark' : ($ev->type === 'workshop' ? 'bg-primary text-white' : 'bg-info text-dark') }} rounded-pill px-2 py-0.5" style="font-size: 0.72rem;">
                                            <i class="{{ $ev->type_icon }} me-1"></i>{{ $ev->type_short_label }}
                                        </span>
                                        <span class="badge bg-light text-muted border" style="font-size: 0.72rem;">
                                            {{ $ev->attendees()->count() }} {{ $ev->capacity ? '/ ' . $ev->capacity : '' }} مقعد
                                        </span>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1 fs-6" style="line-height: 1.4;">
                                        {{ $ev->title }}
                                    </h6>
                                    <div class="text-muted small mb-2 d-flex align-items-center gap-1.5">
                                        <i class="fas fa-user-tie text-primary"></i>
                                        <span>{{ $ev->speaker_name ?? 'لم يحدد' }}</span>
                                    </div>
                                    @if($ev->location)
                                    <div class="text-muted small mb-3">
                                        <i class="fas fa-map-marker-alt text-danger me-1"></i>{{ $ev->location }}
                                    </div>
                                    @endif
                                </div>
                                <div class="d-flex gap-2 pt-2 border-top mt-auto">
                                    <a href="{{ route('job-fair.public.events.show', $ev->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill flex-grow-1" style="font-size: 0.76rem;">
                                        <i class="fas fa-qrcode me-1"></i>الصفحة وQR
                                    </a>
                                    <a href="{{ route('job-fair.admin.events.index', $fair->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3" style="font-size: 0.76rem;">
                                        <i class="fas fa-edit me-1"></i>تعديل
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- قسم إدارة الهوية البصرية والأصول الإعلامية للمعرض (Brand Identity & Media Kit) -->
    <div class="row mt-4 mb-4">
        <div class="col-12">
            <div class="card-modern p-3 p-md-4" id="brandIdentitySection">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3 flex-wrap gap-2">
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <h5 class="fw-bold mb-0 text-dark fs-6">
                                <i class="fas fa-palette text-warning me-2"></i>الهوية البصرية والأصول الإعلامية للمعرض (Brand Identity & Media Kit)
                            </h5>
                            @php
                                $hasCustomIdentity = $fair->fair_logo_path || $fair->fair_logo_white_path || $fair->fair_logo_horizontal_path || $fair->brand_guidelines_path || $fair->media_kit_path;
                            @endphp
                            @if($hasCustomIdentity)
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2.5 py-1 small fw-bold">
                                    <i class="fas fa-sparkles me-1"></i>هوية مخصصة لهذا الحدث
                                </span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-2.5 py-1 small fw-bold">
                                    <i class="fas fa-info-circle me-1"></i>الأصول الافتراضية للنظام
                                </span>
                            @endif
                        </div>
                        <p class="text-muted small mb-0 mt-1">تخصيص وإدارة الشعارات، دليل الهوية، والحقيبة الإعلامية الخاصة بهذا المعرض والتي تظهر لممثلي الشركات المشاركة.</p>
                    </div>

                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <!-- معاينة كما يراها ممثلو الشركات -->
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-xs" data-bs-toggle="modal" data-bs-target="#previewCompanyMediaKitModal">
                            <i class="fas fa-eye text-primary"></i>
                            <span>معاينة كما تظهر للشركات</span>
                        </button>

                        <!-- زر تعديل ورفع الأصول -->
                        <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold d-inline-flex align-items-center gap-1.5 shadow-sm" data-bs-toggle="modal" data-bs-target="#editBrandIdentityModal">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span>رفع وتعديل الهوية</span>
                        </button>
                    </div>
                </div>

                <!-- بطاقات الأصول الخمسة Bento Grid -->
                <div class="row g-3 mb-4">
                    <!-- 1. الشعار الأساسي الملون -->
                    <div class="col-12 col-sm-6 col-lg">
                        <div class="card border rounded-3 p-3 h-100 bg-white shadow-xs d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-light text-dark border small fw-bold">الشعار الأساسي</span>
                                @if($fair->fair_logo_path)
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">مخصص</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">افتراضي</span>
                                @endif
                            </div>
                            <div class="bg-light rounded-3 p-2.5 d-flex align-items-center justify-content-center mb-3 border" style="height: 100px;">
                                <img src="{{ $fair->logo_url }}" alt="شعار المعرض" style="max-height: 85px; max-width: 100%; object-fit: contain;" onerror="this.onerror=null;this.src='{{ asset('images/job_fair_logo.png') }}';">
                            </div>
                            <h6 class="fw-bold text-dark mb-1 fs-6 text-truncate" title="شعار المعرض الأساسي (PNG/SVG)">الشعار الملون الأساسي</h6>
                            <small class="text-muted mb-3 d-block" style="font-size: 0.73rem;">للإعلانات والمطبوعات الملونة</small>
                            <div class="d-flex gap-1.5 mt-auto">
                                <a href="{{ route('job-fair.admin.brand-identity.download', [$fair->id, 'fair-logo']) }}" class="btn btn-sm btn-outline-primary rounded-pill flex-grow-1 fw-semibold" style="font-size: 0.76rem;">
                                    <i class="fas fa-download me-1"></i>تحميل
                                </a>
                                @if($fair->fair_logo_path)
                                <form action="{{ route('job-fair.admin.brand-identity.delete', [$fair->id, 'fair_logo']) }}" method="POST" class="m-0">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="action-circle-btn text-danger" onclick="return confirm('استعادة الشعار الافتراضي؟')" title="حذف الشعار المخصص">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- 2. الشعار بالنسخة البيضاء الشفافة -->
                    <div class="col-12 col-sm-6 col-lg">
                        <div class="card border rounded-3 p-3 h-100 bg-white shadow-xs d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-light text-dark border small fw-bold">النسخة البيضاء</span>
                                @if($fair->fair_logo_white_path)
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">مخصص</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">افتراضي</span>
                                @endif
                            </div>
                            <div class="rounded-3 p-2.5 d-flex align-items-center justify-content-center mb-3 border" style="height: 100px; background: #0f172a;">
                                <img src="{{ $fair->white_logo_url }}" alt="الشعار الأبيض" style="max-height: 85px; max-width: 100%; object-fit: contain;" onerror="this.onerror=null;this.src='{{ asset('images/job_fair_logo_white.png') }}';">
                            </div>
                            <h6 class="fw-bold text-dark mb-1 fs-6 text-truncate" title="الشعار الأبيض الشفاف">شعار أبيض شفاف</h6>
                            <small class="text-muted mb-3 d-block" style="font-size: 0.73rem;">للخلفيات الداكنة وشريط الهيدر</small>
                            <div class="d-flex gap-1.5 mt-auto">
                                <a href="{{ route('job-fair.admin.brand-identity.download', [$fair->id, 'fair-logo-white']) }}" class="btn btn-sm btn-outline-primary rounded-pill flex-grow-1 fw-semibold" style="font-size: 0.76rem;">
                                    <i class="fas fa-download me-1"></i>تحميل
                                </a>
                                @if($fair->fair_logo_white_path)
                                <form action="{{ route('job-fair.admin.brand-identity.delete', [$fair->id, 'fair_logo_white']) }}" method="POST" class="m-0">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="action-circle-btn text-danger" onclick="return confirm('استعادة الشعار الأبيض الافتراضي؟')" title="حذف الشعار الأبيض المخصص">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- 3. الشعار بالنسخة الأفقية -->
                    <div class="col-12 col-sm-6 col-lg">
                        <div class="card border rounded-3 p-3 h-100 bg-white shadow-xs d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-light text-dark border small fw-bold">النسخة الأفقية</span>
                                @if($fair->fair_logo_horizontal_path)
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">مخصص</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">افتراضي</span>
                                @endif
                            </div>
                            <div class="bg-light rounded-3 p-2.5 d-flex align-items-center justify-content-center mb-3 border" style="height: 100px;">
                                <img src="{{ $fair->horizontal_logo_url }}" alt="الشعار الأفقي" style="max-height: 60px; max-width: 100%; object-fit: contain;" onerror="this.onerror=null;this.src='{{ asset('images/job_fair_logo_horizontal.png') }}';">
                            </div>
                            <h6 class="fw-bold text-dark mb-1 fs-6 text-truncate" title="شعار المعرض الأفقي">شعار المعرض (أفقي)</h6>
                            <small class="text-muted mb-3 d-block" style="font-size: 0.73rem;">للافتات الطولية والمواقع الإلكترونية</small>
                            <div class="d-flex gap-1.5 mt-auto">
                                <a href="{{ route('job-fair.admin.brand-identity.download', [$fair->id, 'fair-logo-horizontal']) }}" class="btn btn-sm btn-outline-primary rounded-pill flex-grow-1 fw-semibold" style="font-size: 0.76rem;">
                                    <i class="fas fa-download me-1"></i>تحميل
                                </a>
                                @if($fair->fair_logo_horizontal_path)
                                <form action="{{ route('job-fair.admin.brand-identity.delete', [$fair->id, 'fair_logo_horizontal']) }}" method="POST" class="m-0">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="action-circle-btn text-danger" onclick="return confirm('استعادة الشعار الأفقي الافتراضي؟')" title="حذف الشعار الأفقي">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- 4. دليل استخدام الهوية البصرية (PDF) -->
                    <div class="col-12 col-sm-6 col-lg">
                        <div class="card border rounded-3 p-3 h-100 bg-white shadow-xs d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-light text-dark border small fw-bold">دليل الهوية</span>
                                @if($fair->brand_guidelines_path)
                                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">PDF متوفر</span>
                                @else
                                    <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">غير مرفوع</span>
                                @endif
                            </div>
                            <div class="bg-danger bg-opacity-10 rounded-3 p-2.5 d-flex flex-column align-items-center justify-content-center mb-3 border border-danger border-opacity-25" style="height: 100px;">
                                <i class="fas fa-file-pdf text-danger fs-1 mb-1"></i>
                                <span class="small fw-semibold text-danger" style="font-size: 0.72rem;">Brand Guidelines</span>
                            </div>
                            <h6 class="fw-bold text-dark mb-1 fs-6 text-truncate" title="دليل استخدام الهوية البصرية">دليل الهوية (PDF)</h6>
                            <small class="text-muted mb-3 d-block" style="font-size: 0.73rem;">إرشادات الألوان والخطوط والأبعاد</small>
                            <div class="d-flex gap-1.5 mt-auto">
                                @if($fair->brand_guidelines_path)
                                    <a href="{{ route('job-fair.admin.brand-identity.download', [$fair->id, 'brand-guidelines']) }}" class="btn btn-sm btn-outline-danger rounded-pill flex-grow-1 fw-semibold" style="font-size: 0.76rem;" target="_blank">
                                        <i class="fas fa-download me-1"></i>تحميل
                                    </a>
                                    <form action="{{ route('job-fair.admin.brand-identity.delete', [$fair->id, 'brand_guidelines']) }}" method="POST" class="m-0">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="action-circle-btn text-danger" onclick="return confirm('حذف ملف دليل الهوية البصرية؟')" title="حذف الملف">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                @else
                                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill w-100 fw-semibold" style="font-size: 0.76rem;" data-bs-toggle="modal" data-bs-target="#editBrandIdentityModal">
                                        <i class="fas fa-plus me-1"></i>رفع PDF
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- 5. الحقيبة الإعلامية الشاملة (ZIP) -->
                    <div class="col-12 col-sm-6 col-lg">
                        <div class="card border rounded-3 p-3 h-100 bg-white shadow-xs d-flex flex-column" style="background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%);">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 small fw-bold">الحزمة الشاملة</span>
                                @if($fair->media_kit_path)
                                    <span class="badge bg-primary rounded-pill px-2 py-0.5 text-white" style="font-size: 0.7rem;">حزمة مخصصة</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">الحزمة العامة</span>
                                @endif
                            </div>
                            <div class="bg-primary bg-opacity-10 rounded-3 p-2.5 d-flex flex-column align-items-center justify-content-center mb-3 border border-primary border-opacity-25" style="height: 100px;">
                                <i class="fas fa-file-archive text-primary fs-1 mb-1"></i>
                                <span class="small fw-semibold text-primary" style="font-size: 0.72rem;">All-in-One ZIP</span>
                            </div>
                            <h6 class="fw-bold text-dark mb-1 fs-6 text-truncate" title="الحقيبة الإعلامية الكاملة (ZIP)">الحقيبة المجمعة (ZIP)</h6>
                            <small class="text-muted mb-3 d-block" style="font-size: 0.73rem;">تحميل فوري لكافة الأصول بنقرة واحدة</small>
                            <div class="d-flex gap-1.5 mt-auto">
                                <a href="{{ route('job-fair.admin.brand-identity.download', [$fair->id, 'media-kit']) }}" class="btn btn-sm btn-primary rounded-pill flex-grow-1 fw-semibold shadow-xs" style="font-size: 0.76rem;">
                                    <i class="fas fa-download me-1"></i>تحميل
                                </a>
                                @if($fair->media_kit_path)
                                <form action="{{ route('job-fair.admin.brand-identity.delete', [$fair->id, 'media_kit_zip']) }}" method="POST" class="m-0">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="action-circle-btn text-danger" onclick="return confirm('استعادة الحقيبة العامة للنظام؟')" title="حذف الحزمة المخصصة">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- توجيهات وإرشادات الهوية للشركات المشاركة -->
                <div class="p-3 rounded-3 border bg-light">
                    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                        <h6 class="fw-bold text-dark mb-0 fs-6">
                            <i class="fas fa-comment-alt text-primary me-1.5"></i>توجيهات وإرشادات الهوية الموجهة للشركات المشاركة:
                        </h6>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-0.5" style="font-size: 0.76rem;" data-bs-toggle="modal" data-bs-target="#editBrandIdentityModal">
                            <i class="fas fa-pen me-1"></i>تعديل الإرشادات
                        </button>
                    </div>
                    @if($fair->media_kit_description)
                        <div class="bg-white p-3 rounded-2 border text-dark small" style="white-space: pre-line; line-height: 1.6;">
                            {{ $fair->media_kit_description }}
                        </div>
                    @else
                        <div class="text-muted small py-2 d-flex align-items-center gap-2">
                            <i class="fas fa-info-circle text-secondary fs-5"></i>
                            <span>لم تُكتب أي إرشادات خاصة بهذا الحدث حتى الآن. يمكنك إضافة شروط استخدام الشعار، الأبعاد المسموحة للافتات الأجنحة، أو أكواد الألوان المعتمدة لتظهر داخل نافذة الأصول لكل شركة مشاركة.</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal 1: رفع وتحديث الهوية البصرية والأصول الإعلامية -->
    <div class="modal fade" id="editBrandIdentityModal" tabindex="-1" aria-labelledby="editBrandIdentityModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
                <div class="modal-header text-white p-3.5 border-0" style="background: linear-gradient(135deg, #0b192e 0%, #1e3a8a 100%);">
                    <div>
                        <div class="badge bg-warning text-dark px-3 py-0.5 rounded-pill fw-bold mb-1.5" style="font-size: 0.75rem;">
                            <i class="fas fa-palette me-1"></i> هوية الأيفنت الرسمية
                        </div>
                        <h5 class="modal-title fw-bold text-white mb-0" id="editBrandIdentityModalLabel">
                            إدارة وتحديث الهوية البصرية والأصول الإعلامية — {{ $fair->title }}
                        </h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('job-fair.admin.brand-identity.update', $fair->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4 bg-light">
                        <div class="alert alert-primary bg-primary bg-opacity-10 border-primary border-opacity-25 rounded-3 py-2 px-3 mb-3 small text-dark">
                            <i class="fas fa-shield-alt text-primary me-1"></i>
                            <strong>ملاحظة:</strong> يمكنك رفع الأصول الخاصة بهذا المعرض تحديداً. أي أصل لم يتم رفعه سيستخدم تلقائياً الهوية الافتراضية للنظام حتى لا تتأثر الشركات.
                        </div>

                        <div class="row g-3">
                            <!-- الشعار الأساسي -->
                            <div class="col-md-6">
                                <label class="form-label-modern small fw-bold">
                                    <i class="fas fa-image text-primary me-1"></i>شعار المعرض الأساسي (ملون)
                                </label>
                                <input type="file" name="fair_logo" class="form-control form-control-sm bg-white" accept="image/png,image/jpeg,image/svg+xml,image/webp">
                                <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">PNG, SVG أو JPG (الحد الأقصى 4MB). يُفضل خلفية شفافة.</small>
                                @if($fair->fair_logo_path)
                                    <small class="text-success fw-semibold mt-0.5 d-block" style="font-size: 0.72rem;"><i class="fas fa-check-circle me-1"></i>يوجد ملف مخصص محفوظ حالياً.</small>
                                @endif
                            </div>

                            <!-- الشعار الأبيض الشفاف -->
                            <div class="col-md-6">
                                <label class="form-label-modern small fw-bold">
                                    <i class="fas fa-adjust text-secondary me-1"></i>الشعار بالنسخة البيضاء الشفافة
                                </label>
                                <input type="file" name="fair_logo_white" class="form-control form-control-sm bg-white" accept="image/png,image/svg+xml,image/webp">
                                <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">PNG أو SVG بخطوط بيضاء للخلفيات الداكنة (الحد الأقصى 4MB).</small>
                                @if($fair->fair_logo_white_path)
                                    <small class="text-success fw-semibold mt-0.5 d-block" style="font-size: 0.72rem;"><i class="fas fa-check-circle me-1"></i>يوجد ملف مخصص محفوظ حالياً.</small>
                                @endif
                            </div>

                            <!-- الشعار الأفقي -->
                            <div class="col-md-6">
                                <label class="form-label-modern small fw-bold">
                                    <i class="fas fa-arrows-alt-h text-info me-1"></i>شعار المعرض بالنسخة الأفقية
                                </label>
                                <input type="file" name="fair_logo_horizontal" class="form-control form-control-sm bg-white" accept="image/png,image/jpeg,image/svg+xml,image/webp">
                                <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">للافتات الطولية والويب (الحد الأقصى 4MB).</small>
                                @if($fair->fair_logo_horizontal_path)
                                    <small class="text-success fw-semibold mt-0.5 d-block" style="font-size: 0.72rem;"><i class="fas fa-check-circle me-1"></i>يوجد ملف مخصص محفوظ حالياً.</small>
                                @endif
                            </div>

                            <!-- دليل الهوية البصرية PDF -->
                            <div class="col-md-6">
                                <label class="form-label-modern small fw-bold">
                                    <i class="fas fa-file-pdf text-danger me-1"></i>دليل استخدام الهوية البصرية (PDF)
                                </label>
                                <input type="file" name="brand_guidelines" class="form-control form-control-sm bg-white" accept="application/pdf">
                                <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">كتيب إرشادات الخطوط والألوان (الحد الأقصى 25MB).</small>
                                @if($fair->brand_guidelines_path)
                                    <small class="text-success fw-semibold mt-0.5 d-block" style="font-size: 0.72rem;"><i class="fas fa-check-circle me-1"></i>يوجد كتيب PDF محفوظ حالياً.</small>
                                @endif
                            </div>

                            <!-- الحقيبة الإعلامية الكاملة ZIP -->
                            <div class="col-12">
                                <label class="form-label-modern small fw-bold">
                                    <i class="fas fa-file-archive text-warning me-1"></i>الحقيبة الإعلامية الكاملة المجمعة (ZIP / RAR)
                                </label>
                                <input type="file" name="media_kit_zip" class="form-control form-control-sm bg-white" accept=".zip,.rar,.7z">
                                <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">ملف أرشيف مضغوط يحتوي على كافة الشعارات ودليل الاستخدام للتحميل بنقرة واحدة (الحد الأقصى 60MB).</small>
                                @if($fair->media_kit_path)
                                    <small class="text-success fw-semibold mt-0.5 d-block" style="font-size: 0.72rem;"><i class="fas fa-check-circle me-1"></i>يوجد حزمة ZIP مخصصة لهذا المعرض.</small>
                                @endif
                            </div>

                            <!-- إرشادات وتوجيهات الهوية للشركات -->
                            <div class="col-12">
                                <label class="form-label-modern small fw-bold">
                                    <i class="fas fa-bullhorn text-primary me-1"></i>إرشادات وتوجيهات الهوية البصرية للشركات المشاركة
                                </label>
                                <textarea name="media_kit_description" class="form-control form-control-sm bg-white" rows="3" placeholder="اكتب هنا أي توجيهات خاصة بالمعرض: مثل أبعاد طباعة البنرات، قيود تعديل الشعار، كود الألوان الرسمية، أو متطلبات لافتات الأجنحة...">{{ old('media_kit_description', $fair->media_kit_description) }}</textarea>
                                <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">سيظهر هذا النص لممثلي الشركات المشاركة في الجزء العلوي من نافذة الأصول الإعلامية الخاصة بهم.</small>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-white border-0 p-3 d-flex justify-content-between">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                            <i class="fas fa-check me-1"></i> حفظ وتحديث الهوية
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal 2: معاينة حزمة الأصول كما يراها ممثلو الشركات -->
    <div class="modal fade" id="previewCompanyMediaKitModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
                <div class="bg-dark text-warning p-2 px-3 text-center small fw-bold border-bottom">
                    <i class="fas fa-eye me-1"></i> وضع المعاينة التجريبية: هكذا تظهر نافذة الأصول لممثلي الشركات المشاركة في {{ $fair->title }}
                </div>
                <div class="modal-header text-white p-4 border-0" style="background: linear-gradient(135deg, #0b192e 0%, #1e3a8a 100%);">
                    <div>
                        <div class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold mb-2">
                            <i class="fas fa-palette me-1"></i> المواد الإعلامية الرسمية
                        </div>
                        <h4 class="modal-title fw-bold text-white mb-1">
                            الأصول الإعلامية والهوية البصرية — {{ $fair->title }}
                        </h4>
                        <p class="text-white-50 small mb-0">كافة الأصول والشعارات معتمدة وجاهزة للتحميل لاستخدامها في إعلاناتكم ومطبوعات الجناح</p>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4 bg-light">
                    {{-- التوجيهات --}}
                    @if($fair->media_kit_description)
                        <div class="alert alert-info border-0 rounded-3 shadow-sm mb-4" style="background: rgba(30, 58, 138, 0.06); border-right: 4px solid #1e3a8a !important;">
                            <h6 class="fw-bold text-primary mb-1">
                                <i class="fas fa-info-circle me-1"></i> إرشادات وتوجيهات الهوية البصرية للمعرض:
                            </h6>
                            <p class="small text-dark mb-0" style="white-space: pre-line;">{{ $fair->media_kit_description }}</p>
                        </div>
                    @endif

                    {{-- حزمة الـ ZIP الرئيسية --}}
                    <div class="card border-0 rounded-3 shadow-sm mb-4" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border: 1.5px dashed #3b82f6 !important;">
                        <div class="card-body p-3 d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-primary text-white rounded-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px; font-size: 1.6rem;">
                                    <i class="fas fa-file-archive"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-primary mb-1">الحقيبة الإعلامية الكاملة (All-in-One Media Kit ZIP)</h6>
                                    <p class="text-muted small mb-0">تحتوي على كافة الشعارات الرسمية (ملونة، أفقية، بيضاء شفافة)، ودليل استخدام الهوية البصرية.</p>
                                </div>
                            </div>
                            <a href="{{ route('job-fair.admin.brand-identity.download', [$fair->id, 'media-kit']) }}" class="btn btn-primary rounded-pill px-4 py-2 fw-bold text-nowrap shadow-sm">
                                <i class="fas fa-download me-1"></i> تحميل الحزمة (ZIP)
                            </a>
                        </div>
                    </div>

                    {{-- شبكة الملفات الفردية --}}
                    <h6 class="fw-bold text-dark mb-3">
                        <i class="fas fa-images text-warning me-1"></i> الشعارات والأصول الفردية بدقة عالية:
                    </h6>

                    <div class="row g-3 mb-4">
                        {{-- شعار المعرض الرسمي --}}
                        <div class="col-md-6">
                            <div class="card border-0 rounded-3 shadow-sm h-100 bg-white p-3">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="bg-light border rounded-3 p-2.5 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <img src="{{ $fair->logo_url }}" alt="شعار المعرض" style="max-width: 100%; max-height: 100%; object-fit: contain;" onerror="this.onerror=null;this.src='{{ asset('images/job_fair_logo.png') }}';">
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">شعار {{ $fair->title }}</div>
                                        <small class="text-muted">النسخة الأساسية بالألوان الكاملة (PNG)</small>
                                    </div>
                                </div>
                                <div class="d-flex gap-2 mt-auto">
                                    <a href="{{ route('job-fair.admin.brand-identity.download', [$fair->id, 'fair-logo']) }}" class="btn btn-sm btn-outline-primary rounded-pill flex-grow-1 fw-semibold">
                                        <i class="fas fa-download me-1"></i> تحميل النسخة
                                    </a>
                                    <a href="{{ route('job-fair.admin.brand-identity.download', [$fair->id, 'fair-logo-white']) }}" class="btn btn-sm btn-outline-secondary rounded-pill fw-semibold" title="النسخة البيضاء للخلفيات الداكنة">
                                        <i class="fas fa-adjust me-1"></i> بيضاء شفافة
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- شعار المعرض الأفقي --}}
                        <div class="col-md-6">
                            <div class="card border-0 rounded-3 shadow-sm h-100 bg-white p-3">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="bg-light border rounded-3 p-2.5 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <img src="{{ $fair->horizontal_logo_url }}" alt="شعار المعرض أفقي" style="max-width: 100%; max-height: 100%; object-fit: contain;" onerror="this.onerror=null;this.src='{{ asset('images/job_fair_logo_horizontal.png') }}';">
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">شعار المعرض (نسخة أفقية)</div>
                                        <small class="text-muted">للافتات الطولية والمواقع (PNG)</small>
                                    </div>
                                </div>
                                <a href="{{ route('job-fair.admin.brand-identity.download', [$fair->id, 'fair-logo-horizontal']) }}" class="btn btn-sm btn-outline-primary rounded-pill w-100 mt-auto fw-semibold">
                                    <i class="fas fa-download me-1"></i> تحميل الشعار الأفقي
                                </a>
                            </div>
                        </div>

                        {{-- دليل الهوية البصرية --}}
                        @if($fair->brand_guidelines_path)
                        <div class="col-md-6">
                            <div class="card border-0 rounded-3 shadow-sm h-100 bg-white p-3">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="bg-danger bg-opacity-10 text-danger rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 1.6rem;">
                                        <i class="fas fa-file-pdf"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">دليل استخدام الهوية البصرية</div>
                                        <small class="text-muted">إرشادات الألوان والخطوط والأبعاد (PDF)</small>
                                    </div>
                                </div>
                                <a href="{{ route('job-fair.admin.brand-identity.download', [$fair->id, 'brand-guidelines']) }}" class="btn btn-sm btn-outline-danger rounded-pill w-100 mt-auto fw-semibold" target="_blank">
                                    <i class="fas fa-download me-1"></i> تحميل دليل الهوية (PDF)
                                </a>
                            </div>
                        </div>
                        @endif

                        {{-- شعار مكتب تدريب الخريجين --}}
                        <div class="col-md-6">
                            <div class="card border-0 rounded-3 shadow-sm h-100 bg-white p-3">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="bg-light border rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <img src="{{ asset('images/logo.jpg') }}" alt="مكتب الخريجين" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">شعار مكتب تدريب الخريجين</div>
                                        <small class="text-muted">جامعة طرابلس (شعار رسمي عالي الدقة)</small>
                                    </div>
                                </div>
                                <a href="{{ route('job-fair.admin.brand-identity.download', [$fair->id, 'office-logo']) }}" class="btn btn-sm btn-outline-primary rounded-pill w-100 mt-auto fw-semibold">
                                    <i class="fas fa-download me-1"></i> تحميل شعار المكتب
                                </a>
                            </div>
                        </div>

                        {{-- شعار جامعة طرابلس --}}
                        <div class="col-md-6">
                            <div class="card border-0 rounded-3 shadow-sm h-100 bg-white p-3">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="bg-dark rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <img src="{{ asset('images/uni_logo_white.png') }}" alt="جامعة طرابلس" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">شعار جامعة طرابلس</div>
                                        <small class="text-muted">الجهة الأكاديمية الراعية والمنظمة</small>
                                    </div>
                                </div>
                                <a href="{{ route('job-fair.admin.brand-identity.download', [$fair->id, 'university-logo']) }}" class="btn btn-sm btn-outline-primary rounded-pill w-100 mt-auto fw-semibold">
                                    <i class="fas fa-download me-1"></i> تحميل شعار الجامعة
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- قائمة رعاة المعرض الرسميين --}}
                    @if($fair->sponsors && $fair->sponsors->count() > 0)
                        <div class="p-3 bg-white rounded-3 shadow-sm border">
                            <h6 class="fw-bold text-dark mb-2">
                                <i class="fas fa-award text-warning me-1"></i> الرعاة الرسميون للمعرض:
                            </h6>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($fair->sponsors as $sp)
                                    <span class="badge bg-light text-dark border p-2 rounded-pill d-inline-flex align-items-center gap-2">
                                        <span class="badge bg-warning text-dark rounded-pill">{{ $sp->tier_label }}</span>
                                        <strong>{{ $sp->name }}</strong>
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <div class="modal-footer bg-white border-0 p-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4 fw-semibold" data-bs-dismiss="modal">إغلاق المعاينة</button>
                </div>
            </div>
        </div>
    </div>

    <!-- قسم إدارة الجهات الراعية (Sponsors) -->
    <div class="row mt-4 mb-4">
        <div class="col-12">
            <div class="card-modern p-3 p-md-4" id="sponsorsSection">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3 flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="fw-bold mb-0 text-dark fs-6">
                            <i class="fas fa-crown text-warning me-2"></i>الجهات الراعية والداعمة (Sponsors)
                        </h5>
                        <span class="badge bg-warning bg-opacity-10 text-dark border border-warning border-opacity-25 rounded-pill px-2.5 py-1 small fw-bold">
                            {{ $fair->sponsors->count() }} جهة راعية
                        </span>
                    </div>
                    <button class="btn btn-sm btn-warning text-dark fw-bold rounded-pill px-3 shadow-sm d-inline-flex align-items-center gap-1" data-bs-toggle="collapse" data-bs-target="#addSponsorForm">
                        <i class="fas fa-plus"></i>
                        <span>إضافة جهة راعية</span>
                    </button>
                </div>

                <!-- نموذج إضافة جهة راعية (Collapse) -->
                <div class="collapse mb-4" id="addSponsorForm">
                    <form action="{{ route('job-fair.admin.sponsors.store', $fair->id) }}" method="POST" enctype="multipart/form-data"
                          class="p-4 rounded-3" style="background: #f8fafc; border: 1.5px dashed #cbd5e1">
                        @csrf
                        <h6 class="fw-bold text-primary mb-3">
                            <i class="fas fa-plus-circle me-1"></i> بيانات جهة الرعاية الجديدة:
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label-modern small fw-bold">اسم الجهة أو الشركة الراعية <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control form-control-sm" placeholder="مثال: شركة المدار الجديد" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label-modern small fw-bold">مستوى الرعاية <span class="text-danger">*</span></label>
                                <select name="tier" class="form-select form-select-sm" required>
                                    <option value="diamond">الراعي الماسي 💎 (Diamond)</option>
                                    <option value="platinum">الراعي البلاتيني ⭐ (Platinum)</option>
                                    <option value="gold" selected>الراعي الذهبي 🏆 (Gold)</option>
                                    <option value="silver">الراعي الفضي 🥈 (Silver)</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label-modern small fw-bold">الموقع الإلكتروني</label>
                                <input type="url" name="website" class="form-control form-control-sm" placeholder="https://example.ly">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label-modern small fw-bold">ترتيب الظهور</label>
                                <input type="number" name="display_order" class="form-control form-control-sm" value="0" min="0">
                            </div>

                            <div class="col-md-8">
                                <label class="form-label-modern small fw-bold">نبذة عن الرعاية أو الشركة</label>
                                <textarea name="description" class="form-control form-control-sm" rows="2" placeholder="وصف موجز يظهر في بطاقة الراعي بصفحة المعرض..."></textarea>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label-modern small fw-bold">شعار الراعي (صورة)</label>
                                <input type="file" name="logo" class="form-control form-control-sm" accept="image/*">
                                <small class="text-muted" style="font-size: 0.72rem;">يفضل خلفية شفافة PNG بدقة مناسبة</small>
                            </div>

                            <div class="col-12 d-flex justify-content-between align-items-center pt-2">
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="isActiveNew" value="1" checked>
                                    <label class="form-check-label small fw-semibold text-dark" for="isActiveNew">تفعيل وظهور الراعي في صفحة المعرض</label>
                                </div>
                                <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 fw-bold">
                                    <i class="fas fa-check me-1"></i> حفظ وإضافة الراعي
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- قائمة الرعاة الحالية -->
                @if($fair->sponsors->isEmpty())
                    <p class="text-muted text-center py-4 mb-0 small">لا توجد جهات راعية مضافة في هذا المعرض حتى الآن</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="py-2.5 px-3 border-0 small fw-bold">الشعار</th>
                                    <th class="py-2.5 border-0 small fw-bold">الجهة الراعية</th>
                                    <th class="py-2.5 border-0 small fw-bold">مستوى الرعاية</th>
                                    <th class="py-2.5 border-0 small fw-bold">النبذة التعريفية</th>
                                    <th class="py-2.5 border-0 text-center small fw-bold">الترتيب</th>
                                    <th class="py-2.5 border-0 text-center small fw-bold">الحالة</th>
                                    <th class="py-2.5 border-0 text-center small fw-bold">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($fair->sponsors->sortBy('display_order') as $sp)
                                <tr>
                                    <td class="px-3">
                                        <div class="rounded-3 border p-1 bg-white d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                            @if($sp->logo_path)
                                                <img src="{{ Storage::url($sp->logo_path) }}" alt="{{ $sp->name }}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                            @else
                                                <i class="fas fa-award text-warning fs-5"></i>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $sp->name }}</div>
                                        @if($sp->website)
                                            <a href="{{ $sp->website }}" target="_blank" class="small text-primary text-decoration-none d-inline-flex align-items-center gap-1">
                                                <span>{{ $sp->website }}</span>
                                                <i class="fas fa-external-link-alt" style="font-size: 0.65rem;"></i>
                                            </a>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $badgeStyle = match($sp->tier) {
                                                'diamond' => 'background: rgba(56, 189, 248, 0.15); color: #0284c7; border: 1px solid rgba(56, 189, 248, 0.3);',
                                                'platinum' => 'background: rgba(148, 163, 184, 0.15); color: #475569; border: 1px solid rgba(148, 163, 184, 0.3);',
                                                'silver' => 'background: rgba(156, 163, 175, 0.15); color: #4b5563; border: 1px solid rgba(156, 163, 175, 0.3);',
                                                default => 'background: rgba(245, 158, 11, 0.15); color: #b45309; border: 1px solid rgba(245, 158, 11, 0.3);',
                                            };
                                        @endphp
                                        <span class="badge rounded-pill px-2.5 py-1 fw-bold" style="{{ $badgeStyle }}">
                                            {{ $sp->tier_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="small text-muted text-truncate" style="max-width: 250px;" title="{{ $sp->description }}">
                                            {{ $sp->description ?: '—' }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border">{{ $sp->display_order }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($sp->is_active)
                                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2.5 py-1">مفعل</span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-2.5 py-1">معطل</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <!-- زر تعديل -->
                                            <button type="button" class="action-circle-btn text-primary" data-bs-toggle="modal" data-bs-target="#editSponsorModal{{ $sp->id }}" title="تعديل جهة الرعاية">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            <!-- زر حذف -->
                                            <form action="{{ route('job-fair.admin.sponsors.destroy', $sp->id) }}" method="POST" class="d-inline m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="action-circle-btn text-danger" onclick="return confirm('هل أنت متأكد من رغبتك في حذف جهة الرعاية؟')" title="حذف">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal تعديل الراعي -->
                                <div class="modal fade" id="editSponsorModal{{ $sp->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
                                            <div class="modal-header text-white p-3.5 border-0" style="background: linear-gradient(135deg, #1e3a8a, #2563eb);">
                                                <h5 class="modal-title fw-bold text-white mb-0">
                                                    <i class="fas fa-edit me-1"></i> تعديل بيانات الراعي: {{ $sp->name }}
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('job-fair.admin.sponsors.update', $sp->id) }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body p-4 bg-light">
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label-modern small fw-bold">اسم الجهة الراعية <span class="text-danger">*</span></label>
                                                            <input type="text" name="name" value="{{ $sp->name }}" class="form-control form-control-sm" required>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <label class="form-label-modern small fw-bold">مستوى الرعاية <span class="text-danger">*</span></label>
                                                            <select name="tier" class="form-select form-select-sm" required>
                                                                <option value="diamond" {{ $sp->tier == 'diamond' ? 'selected' : '' }}>الراعي الماسي 💎 (Diamond)</option>
                                                                <option value="platinum" {{ $sp->tier == 'platinum' ? 'selected' : '' }}>الراعي البلاتيني ⭐ (Platinum)</option>
                                                                <option value="gold" {{ $sp->tier == 'gold' ? 'selected' : '' }}>الراعي الذهبي 🏆 (Gold)</option>
                                                                <option value="silver" {{ $sp->tier == 'silver' ? 'selected' : '' }}>الراعي الفضي 🥈 (Silver)</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-8">
                                                            <label class="form-label-modern small fw-bold">الموقع الإلكتروني</label>
                                                            <input type="url" name="website" value="{{ $sp->website }}" class="form-control form-control-sm">
                                                        </div>

                                                        <div class="col-md-4">
                                                            <label class="form-label-modern small fw-bold">ترتيب الظهور</label>
                                                            <input type="number" name="display_order" value="{{ $sp->display_order }}" class="form-control form-control-sm" min="0">
                                                        </div>

                                                        <div class="col-12">
                                                            <label class="form-label-modern small fw-bold">نبذة عن الرعاية</label>
                                                            <textarea name="description" class="form-control form-control-sm" rows="3">{{ $sp->description }}</textarea>
                                                        </div>

                                                        <div class="col-12">
                                                            <label class="form-label-modern small fw-bold">تحديث الشعار (اختياري)</label>
                                                            <input type="file" name="logo" class="form-control form-control-sm" accept="image/*">
                                                            @if($sp->logo_path)
                                                                <small class="text-success mt-1 d-block">يوجد شعار محفوظ حالياً: {{ basename($sp->logo_path) }}</small>
                                                            @endif
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-check form-switch m-0">
                                                                <input class="form-check-input" type="checkbox" name="is_active" id="isActiveEdit{{ $sp->id }}" value="1" {{ $sp->is_active ? 'checked' : '' }}>
                                                                <label class="form-check-label small fw-semibold text-dark" for="isActiveEdit{{ $sp->id }}">تفعيل وظهور الراعي في صفحة المعرض</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-white border-0 p-3">
                                                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                                                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">حفظ التغييرات</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.feature-toggle-switch').forEach(function(toggle) {
        toggle.addEventListener('change', function() {
            const feature = this.getAttribute('data-feature');
            const url = this.getAttribute('data-url');
            const isChecked = this.checked;
            const badge = document.getElementById('badge-' + feature + '-status');

            this.disabled = true;

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ feature: feature })
            })
            .then(res => res.json())
            .then(data => {
                this.disabled = false;
                if (data.success) {
                    if (badge) {
                        if (data.published) {
                            badge.className = 'badge bg-success text-white px-2.5 py-1 rounded-pill';
                            badge.innerHTML = '<i class="fas fa-eye me-1"></i><span>منشور للزوار</span>';
                        } else {
                            badge.className = 'badge bg-warning text-dark px-2.5 py-1 rounded-pill';
                            badge.innerHTML = '<i class="fas fa-clock me-1"></i><span>Coming Soon (قريباً)</span>';
                        }
                    }
                } else {
                    this.checked = !isChecked;
                    alert(data.message || 'حدث خطأ أثناء تعديل حالة النشر.');
                }
            })
            .catch(err => {
                this.disabled = false;
                this.checked = !isChecked;
                alert('تعذر الاتصال بالخادم.');
            });
        });
    });
});
</script>
@endpush
@endsection
