@extends('layouts.app')

@section('title', 'إدارة مشاريع التخرج والأرشيف - ' . $fair->title)

@push('styles')
@include('job-fair.admin.partials.modal-styles')
<style>

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

    .projects-table th {
        background-color: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 0.83rem;
        border-bottom: 2px solid #e2e8f0;
        padding: 0.9rem 1rem;
        white-space: nowrap;
    }
    .projects-table td {
        padding: 0.95rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }
    .projects-table tbody tr:hover {
        background-color: #f8fafc;
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

    {{-- الشريط العلوي الموحد مع الشعارات الرسمية المتطابقة مع صفحة المعرض العامة --}}
    @include('job-fair.admin.partials.header', [
        'fair' => $fair,
        'page' => 'projects',
        'title' => 'إدارة مشاريع التخرج والأرشيف السنوي',
        'subtitle' => $fair->title . ' — مراجعة واعتماد مشاريع الطلبة المتميزة، تنظيم الأجنحة، ونشر الابتكارات في المعرض الرقمي'
    ])

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
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-layer-group text-primary fs-5"></i>
                <h5 class="fw-bold mb-0 text-dark">مشاريع التخرج والأرشيف</h5>
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-1 rounded-pill small">
                    {{ $projects->count() }} مشروع
                </span>
            </div>
            <div class="text-muted small">
                انقر على اسم المشروع أو زر <strong class="text-primary">«مراجعة»</strong> لاستعراض التدقيق الكامل والبيانات السرية للمشروع
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 projects-table">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">#</th>
                        <th style="min-width: 280px;">المشروع</th>
                        <th style="min-width: 200px;">الكلية والتخصص</th>
                        <th style="min-width: 190px;">فريق العمل</th>
                        <th style="min-width: 170px;">المشرف الأكاديمي</th>
                        <th class="text-center" style="width: 100px;">الجناح</th>
                        <th class="text-center" style="width: 120px;">الحالة</th>
                        <th class="text-center" style="width: 80px;">المشاهدات</th>
                        <th class="text-center" style="width: 180px;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $index => $proj)
                        <tr class="{{ $proj->status === 'pending' ? 'table-warning bg-opacity-25' : '' }}">
                            <td class="text-center text-muted small fw-bold">{{ $loop->iteration }}</td>

                            <!-- المشروع والصورة -->
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    @if($proj->cover_url)
                                        <img src="{{ $proj->cover_url }}" alt="{{ $proj->title }}" class="project-avatar shadow-sm">
                                    @else
                                        <div class="project-avatar shadow-sm">
                                            <i class="{{ $proj->faculty_icon }}"></i>
                                        </div>
                                    @endif

                                    <div>
                                        <a href="javascript:void(0)" onclick="showModalSafe('reviewModal{{ $proj->id }}')" data-bs-toggle="modal" data-bs-target="#reviewModal{{ $proj->id }}" class="fw-bold text-dark text-decoration-none d-block mb-1 hover-primary fs-6" title="انقر لمعاينة ملف المشروع الكامل">
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
                                                <span class="badge bg-warning text-dark fw-bold" style="font-size: 0.72rem;"><i class="fas fa-star text-warning"></i> مميز</span>
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
                                <div class="text-muted small mt-0.5">
                                    {{ $proj->department }} &bull; <span class="badge bg-light text-dark border">{{ $proj->graduation_year }}</span>
                                </div>
                            </td>

                            <!-- فريق العمل -->
                            <td>
                                @php
                                    $names = collect($proj->team_list)->pluck('name')->filter()->implode(' • ');
                                @endphp
                                <div class="text-dark small fw-semibold text-truncate" style="max-width: 200px;" title="{{ $names }}">
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
                                    <small class="text-muted d-block">{{ $proj->supervisor_title }}</small>
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
                                <span class="badge {{ $proj->status_badge_class }} rounded-pill px-3 py-1.5 fw-semibold" style="font-size: 0.78rem;">
                                    {{ $proj->status_label }}
                                </span>
                            </td>

                            <!-- المشاهدات -->
                            <td class="text-center">
                                <span class="badge bg-light text-muted border px-2 py-1">
                                    <i class="fas fa-eye me-1 text-primary"></i>{{ $proj->views_count }}
                                </span>
                            </td>

                            <!-- الإجراءات -->
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <!-- ملف المراجعة الكامل -->
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1 shadow-sm" title="معاينة ملف المشروع الكامل وحقول المراجعة" onclick="showModalSafe('reviewModal{{ $proj->id }}')" data-bs-toggle="modal" data-bs-target="#reviewModal{{ $proj->id }}">
                                        <i class="fas fa-file-invoice"></i>
                                        <span class="small fw-bold">مراجعة</span>
                                    </button>

                                    <!-- المعاينة العامة -->
                                    <a href="{{ route('job-fair.public.projects.show', $proj->id) }}" target="_blank" class="action-circle-btn text-secondary" title="معاينة الصفحة العامة">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>

                                    <!-- تعديل -->
                                    <button type="button" class="action-circle-btn text-primary" title="تعديل بيانات المشروع" onclick="editProjectById({{ $proj->id }})" data-bs-toggle="modal" data-bs-target="#editProjectModal">
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
                                    <div class="d-flex align-items-center justify-content-center gap-1 mt-2">
                                        <form action="{{ route('job-fair.admin.projects.update-status', $proj->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="published">
                                            <button type="submit" class="btn btn-xs btn-success py-1 px-2.5 rounded-pill fw-bold shadow-sm" style="font-size: 0.72rem;" title="موافقة ونشر فوري">
                                                <i class="fas fa-check me-1"></i> اعتماد ونشر
                                            </button>
                                        </form>

                                        <button type="button" class="btn btn-xs btn-outline-danger py-1 px-2 rounded-pill fw-bold" style="font-size: 0.72rem;" onclick="showModalSafe('rejectModal{{ $proj->id }}')" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $proj->id }}" title="رفض المشروع">
                                            <i class="fas fa-times me-1"></i> رفض
                                        </button>
                                    </div>
                                @endif
                            </td>
                        </tr>
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

    {{-- ══════════════════════════════════
         نوافذ المراجعة والاعتماد (Modals)
    ══════════════════════════════════ --}}
    @foreach($projects as $proj)
        <!-- Modal: Review Project Details -->
        <div class="modal fade text-start" id="reviewModal{{ $proj->id }}" tabindex="-1" aria-labelledby="reviewModalLabel{{ $proj->id }}" aria-hidden="true" dir="rtl">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content uot-modal-content">
                    <div class="modal-header uot-modal-header uot-modal-header-review">
                        <div class="d-flex align-items-center gap-3">
                            <div class="uot-modal-icon-badge" style="border-color:#34d399; background:rgba(52,211,153,0.18); color:#34d399;">
                                <i class="fas fa-microscope"></i>
                            </div>
                            <div>
                                <h5 class="uot-modal-title" id="reviewModalLabel{{ $proj->id }}">
                                    ملف مراجعة واعتماد المشروع: {{ $proj->title }}
                                </h5>
                                <p class="uot-modal-subtitle">{{ $proj->faculty }} &bull; {{ $proj->department }} &bull; دفعة {{ $proj->graduation_year }}</p>
                            </div>
                        </div>
                        <button type="button" class="uot-btn-close" data-bs-dismiss="modal" aria-label="Close" title="إغلاق">
                            <i class="fas fa-times"></i>
                        </button>
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
                    <div class="modal-content uot-modal-content">
                        <div class="modal-header" style="background: linear-gradient(135deg, #7f1d1d 0%, #991b1b 60%, #b91c1c 100%) !important; border-bottom: 3.5px solid #eeca3e !important; padding: 1.25rem 1.75rem !important; color:#fff !important;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="uot-modal-icon-badge" style="border-color:#fca5a5; background:rgba(252,165,165,0.18); color:#fca5a5;">
                                    <i class="fas fa-times-circle"></i>
                                </div>
                                <div>
                                    <h5 class="uot-modal-title" id="rejectModalLabel{{ $proj->id }}">
                                        رفض مشروع التخرج
                                    </h5>
                                    <p class="uot-modal-subtitle">إجراء إداري رسمي &bull; توثيق سبب الرفض</p>
                                </div>
                            </div>
                            <button type="button" class="uot-btn-close" data-bs-dismiss="modal" aria-label="Close" title="إغلاق">
                                <i class="fas fa-times"></i>
                            </button>
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
    @endforeach


<!-- Modal: Add Project -->
<div class="modal fade" id="addProjectModal" tabindex="-1" role="dialog" aria-labelledby="addProjectModalLabel" aria-hidden="true" dir="rtl">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <form id="addProjectForm" action="{{ route('job-fair.admin.projects.store', $fair->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content uot-modal-content">
                <div class="modal-header uot-modal-header">
                    <div class="d-flex align-items-center gap-3">
                        <div class="uot-modal-icon-badge">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div>
                            <h5 class="uot-modal-title" id="addProjectModalLabel">
                                إضافة مشروع تخرج جديد (لوحة الإدارة)
                            </h5>
                            <p class="uot-modal-subtitle">جامعة طرابلس &bull; منظومة أرشيف ومعرض مشاريع التخرج المعتمدة</p>
                        </div>
                    </div>
                    <button type="button" class="uot-btn-close" data-bs-dismiss="modal" aria-label="Close" title="إغلاق">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body p-4">
                    {{-- القسم 1: البيانات الأكاديمية والأساسية --}}
                    <div class="uot-section-divider">
                        <span class="divider-icon"><i class="fas fa-university"></i></span>
                        <h6 class="divider-title">البيانات الأكاديمية والأساسية للمشروع</h6>
                        <span class="divider-badge">مطلوبة للتوثيق والأرشفة</span>
                    </div>

                    <div class="row g-3 mb-2">
                        <div class="col-12">
                            <label class="uot-form-label">
                                عنوان المشروع <span class="req">*</span>
                            </label>
                            <input type="text" name="title" class="form-control uot-input" required placeholder="عنوان المشروع بالكامل كما هو معتمد في الكلية">
                        </div>

                        <div class="col-md-4">
                            <label class="uot-form-label">
                                الكلية <span class="req">*</span>
                            </label>
                            <input type="text" name="faculty" class="form-control uot-input" required placeholder="مثال: كلية تقنية المعلومات">
                        </div>
                        <div class="col-md-4">
                            <label class="uot-form-label">
                                القسم / التخصص <span class="req">*</span>
                            </label>
                            <input type="text" name="department" class="form-control uot-input" required placeholder="مثال: هندسة البرمجيات">
                        </div>
                        <div class="col-md-4">
                            <label class="uot-form-label">
                                سنة التخرج <span class="req">*</span>
                            </label>
                            <input type="number" name="graduation_year" class="form-control uot-input" required value="{{ date('Y') }}" min="2000" max="2035">
                        </div>

                        <div class="col-md-6">
                            <label class="uot-form-label">نوع المشروع</label>
                            <input type="text" name="project_type" class="form-control uot-input" placeholder="تطبيق ويب، ذكاء اصطناعي، إنترنت أشياء، نظم مدمجة...">
                        </div>
                        <div class="col-md-6">
                            <label class="uot-form-label">المجال الرئيسي للمشروع</label>
                            <input type="text" name="main_category" class="form-control uot-input" placeholder="تقنية المعلومات، الطاقة، الصحة والطب، البيئة...">
                        </div>
                    </div>

                    {{-- القسم 2: المشرف الأكاديمي وفريق العمل --}}
                    <div class="uot-section-divider">
                        <span class="divider-icon"><i class="fas fa-users-cog"></i></span>
                        <h6 class="divider-title">المشرف الأكاديمي وفريق العمل</h6>
                        <span class="divider-badge">أعضاء الفريق والاتصال</span>
                    </div>

                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label class="uot-form-label">المشرف الأكاديمي</label>
                            <input type="text" name="supervisor_name" class="form-control uot-input" placeholder="اسم الأستاذ المشرف">
                        </div>
                        <div class="col-md-6">
                            <label class="uot-form-label">اللقب والصفة الأكاديمية</label>
                            <input type="text" name="supervisor_title" class="form-control uot-input" placeholder="أستاذ دكتور / أستاذ مشارك / محاضر">
                        </div>

                        <div class="col-12">
                            <label class="uot-form-label">فريق العمل (اسم كل طالب في سطر مستقل)</label>
                            <textarea name="team_members_raw" class="form-control uot-textarea" rows="3" placeholder="محمد علي الورفلي&#10;سارة عبد الله الزنتاني"></textarea>
                            <small class="text-muted d-block mt-1">سيتم تقسيم الأسماء وعرضها كبطاقات طلاب معتمدة في المعرض.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="uot-form-label">البريد الإلكتروني العام للتواصل</label>
                            <input type="email" name="contact_email" class="form-control uot-input" placeholder="project@uot.edu.ly">
                        </div>
                        <div class="col-md-3">
                            <label class="uot-form-label">رقم الجناح بالمعرض</label>
                            <input type="text" name="booth_number" class="form-control uot-input" placeholder="مثال: IT-01">
                        </div>
                        <div class="col-md-3">
                            <label class="uot-form-label">حالة النشر والاعتماد</label>
                            <select name="status" class="form-select uot-select">
                                <option value="published">منشور ومتاح للجمهور</option>
                                <option value="pending" selected>بانتظار الاعتماد (Pending)</option>
                                <option value="draft">مسودة داخلية</option>
                                <option value="rejected">مرفوض</option>
                            </select>
                        </div>
                    </div>

                    {{-- القسم 3: المحتوى والملخص العلمي للمشروع --}}
                    <div class="uot-section-divider">
                        <span class="divider-icon"><i class="fas fa-file-alt"></i></span>
                        <h6 class="divider-title">المحتوى والملخص العلمي للمشروع</h6>
                        <span class="divider-badge">الملخص والأهداف الفنية</span>
                    </div>

                    <div class="row g-3 mb-2">
                        <div class="col-12">
                            <label class="uot-form-label">نبذة تعريفية مختصرة (Abstract)</label>
                            <textarea name="summary" class="form-control uot-textarea" rows="2" placeholder="نبذة موجزة وشاملة تلخص فكرة المشروع..."></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="uot-form-label">المشكلة التي يعالجها المشروع</label>
                            <textarea name="problem_statement" class="form-control uot-textarea" rows="2" placeholder="ما هي الفجوة أو المشكلة الواقعية التي يتصدى لها المشروع؟"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="uot-form-label">الحل المقترح ومخرجات الابتكار</label>
                            <textarea name="solution_statement" class="form-control uot-textarea" rows="2" placeholder="كيف يحل المشروع هذه المشكلة تقنياً أو عملياً؟"></textarea>
                        </div>

                        <div class="col-12">
                            <label class="uot-form-label">أهداف المشروع</label>
                            <textarea name="objectives" class="form-control uot-textarea" rows="2" placeholder="الأهداف الاستراتيجية والتنفيذية للمشروع..."></textarea>
                        </div>

                        <div class="col-12">
                            <label class="uot-form-label">التفاصيل الفنية والمواصفات (Tech Stack)</label>
                            <textarea name="technical_specifications" class="form-control uot-textarea" rows="2" placeholder="اللغات، التقنيات، أدوات التحليل، الحساسات، الأطر البرمجية المستخدمة..."></textarea>
                        </div>

                        <div class="col-12">
                            <label class="uot-form-label">وصف المشروع الكامل</label>
                            <textarea name="description" class="form-control uot-textarea" rows="3" placeholder="شرح معمق لمنهجية العمل، معمارية النظام، ومراحل الاختبار..."></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="uot-form-label">أبرز النتائج والمميزات</label>
                            <textarea name="key_outcomes" class="form-control uot-textarea" rows="2" placeholder="أهم النتائج المحققة وميزات الأداء..."></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="uot-form-label">قابلية التطوير والتسويق التجاري</label>
                            <textarea name="market_viability" class="form-control uot-textarea" rows="2" placeholder="إمكانية تحويل المشروع إلى شركة ناشئة أو منتج تجاري..."></textarea>
                        </div>
                    </div>

                    {{-- القسم 4: الوسائط والروابط الخارجية --}}
                    <div class="uot-section-divider">
                        <span class="divider-icon"><i class="fas fa-photo-video"></i></span>
                        <h6 class="divider-title">الوسائط والروابط الخارجية</h6>
                        <span class="divider-badge">البوستر والروابط</span>
                    </div>

                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label class="uot-form-label">بوستر المشروع (Poster File / Image)</label>
                            <input type="file" name="poster_image" class="form-control uot-input" accept="image/*,application/pdf">
                        </div>
                        <div class="col-md-6">
                            <label class="uot-form-label">صورة الغلاف (Cover Image)</label>
                            <input type="file" name="cover_image" class="form-control uot-input" accept="image/*">
                        </div>

                        <div class="col-md-6">
                            <label class="uot-form-label">رابط المشروع البرمجي أو GitHub</label>
                            <input type="url" name="project_url" class="form-control uot-input" placeholder="https://github.com/username/project">
                        </div>
                        <div class="col-md-6">
                            <label class="uot-form-label">رابط فيديو العرض (Demo / YouTube)</label>
                            <input type="url" name="video_url" class="form-control uot-input" placeholder="https://youtube.com/watch?v=...">
                        </div>
                    </div>

                    {{-- القسم 5: بيانات التدقيق الإداري والاتصال الداخلي (خاصة بالمسؤول) --}}
                    <div class="uot-admin-box">
                        <div class="uot-admin-box-header">
                            <i class="fas fa-user-shield fs-5 text-warning"></i>
                            <span>معلومات خاصة بالمسؤول فقط (بيانات داخلية لا تظهر للعامة)</span>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="uot-form-label text-dark">الرقم الجامعي / رقم القيد</label>
                                <input type="text" name="student_university_id" class="form-control uot-input" placeholder="رقم القيد الجامعي للطالب الممثل">
                            </div>
                            <div class="col-md-6">
                                <label class="uot-form-label text-dark">رقم الهاتف المسجل بالواتساب</label>
                                <input type="text" name="whatsapp_phone" class="form-control uot-input" placeholder="091XXXXXXX">
                            </div>

                            <div class="col-md-6">
                                <label class="uot-form-label text-dark">حالة النموذج الأولي</label>
                                <input type="text" name="prototype_status" class="form-control uot-input" placeholder="فكرة، نموذج تجريبي، منتج جاهز للإنتاج...">
                            </div>
                            <div class="col-md-6">
                                <label class="uot-form-label text-dark">المتطلبات التي يحتاجها المشروع أثناء العرض</label>
                                <input type="text" name="project_requirements" class="form-control uot-input" placeholder="طاولات، شاشات، إنترنت، توصيلات كهربائية...">
                            </div>

                            <div class="col-12">
                                <div class="form-check form-switch mb-2">
                                    <input type="checkbox" name="needs_special_equipment" value="1" class="form-check-input" id="addNeedsEq">
                                    <label class="form-check-label fw-bold text-dark" for="addNeedsEq">
                                        يحتاج المشروع معدات أو تجهيزات خاصة في قاعة المعرض
                                    </label>
                                </div>
                                <input type="text" name="special_equipment_details" class="form-control uot-input" placeholder="وضح تفاصيل المعدات الخاصة المطلوبة...">
                            </div>

                            <div class="col-md-6">
                                <label class="uot-form-label text-dark">الملخص التنفيذي للمراجعة الداخلية</label>
                                <textarea name="executive_summary" class="form-control uot-textarea" rows="2" placeholder="ملاحظات المحكمين أو تقييم اللجنة الأكاديمية..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="uot-form-label text-dark">ملاحظات أو قرارات إدارية</label>
                                <textarea name="admin_notes" class="form-control uot-textarea" rows="2" placeholder="ملاحظات سرية خاصة بالإدارة..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-check form-switch mt-4 p-3 rounded-3 bg-light border d-flex align-items-center gap-3">
                        <input type="checkbox" name="is_featured" value="1" class="form-check-input m-0" id="addProjFeatured">
                        <label class="form-check-label fw-bold text-dark m-0" for="addProjFeatured">
                            <i class="fas fa-star text-warning me-1"></i> تمييز المشروع في الواجهة الرئيسية وأبرز مشاريع المعرض
                        </label>
                    </div>
                </div>

                <div class="modal-footer uot-modal-footer">
                    <button type="button" class="uot-btn-cancel" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> إلغاء
                    </button>
                    <button type="submit" class="uot-btn-submit">
                        <i class="fas fa-save me-1"></i> حفظ وتثبيت المشروع
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Project -->
<div class="modal fade" id="editProjectModal" tabindex="-1" role="dialog" aria-labelledby="editProjectModalLabel" aria-hidden="true" dir="rtl">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <form id="editProjectForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="modal-content uot-modal-content">
                <div class="modal-header uot-modal-header uot-modal-header-edit">
                    <div class="d-flex align-items-center gap-3">
                        <div class="uot-modal-icon-badge">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div>
                            <h5 class="uot-modal-title" id="editProjectModalLabel">
                                تعديل بيانات مشروع التخرج
                            </h5>
                            <p class="uot-modal-subtitle">جامعة طرابلس &bull; تحديث بيانات المشروع والأرشيف الأكاديمي</p>
                        </div>
                    </div>
                    <button type="button" class="uot-btn-close" data-bs-dismiss="modal" aria-label="Close" title="إغلاق">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body p-4">
                    {{-- القسم 1: البيانات الأكاديمية والأساسية --}}
                    <div class="uot-section-divider">
                        <span class="divider-icon"><i class="fas fa-university"></i></span>
                        <h6 class="divider-title">البيانات الأكاديمية والأساسية للمشروع</h6>
                        <span class="divider-badge">مطلوبة للتوثيق والأرشفة</span>
                    </div>

                    <div class="row g-3 mb-2">
                        <div class="col-12">
                            <label class="uot-form-label">
                                عنوان المشروع <span class="req">*</span>
                            </label>
                            <input type="text" name="title" id="edit_title" class="form-control uot-input" required>
                        </div>

                        <div class="col-md-4">
                            <label class="uot-form-label">
                                الكلية <span class="req">*</span>
                            </label>
                            <input type="text" name="faculty" id="edit_faculty" class="form-control uot-input" required>
                        </div>
                        <div class="col-md-4">
                            <label class="uot-form-label">
                                القسم / التخصص <span class="req">*</span>
                            </label>
                            <input type="text" name="department" id="edit_department" class="form-control uot-input" required>
                        </div>
                        <div class="col-md-4">
                            <label class="uot-form-label">
                                سنة التخرج <span class="req">*</span>
                            </label>
                            <input type="number" name="graduation_year" id="edit_graduation_year" class="form-control uot-input" required min="2000" max="2035">
                        </div>

                        <div class="col-md-6">
                            <label class="uot-form-label">نوع المشروع</label>
                            <input type="text" name="project_type" id="edit_project_type" class="form-control uot-input">
                        </div>
                        <div class="col-md-6">
                            <label class="uot-form-label">المجال الرئيسي للمشروع</label>
                            <input type="text" name="main_category" id="edit_main_category" class="form-control uot-input">
                        </div>
                    </div>

                    {{-- القسم 2: المشرف الأكاديمي وفريق العمل --}}
                    <div class="uot-section-divider">
                        <span class="divider-icon"><i class="fas fa-users-cog"></i></span>
                        <h6 class="divider-title">المشرف الأكاديمي وفريق العمل</h6>
                        <span class="divider-badge">أعضاء الفريق والاتصال</span>
                    </div>

                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label class="uot-form-label">المشرف الأكاديمي</label>
                            <input type="text" name="supervisor_name" id="edit_supervisor_name" class="form-control uot-input">
                        </div>
                        <div class="col-md-6">
                            <label class="uot-form-label">اللقب والصفة الأكاديمية</label>
                            <input type="text" name="supervisor_title" id="edit_supervisor_title" class="form-control uot-input">
                        </div>

                        <div class="col-12">
                            <label class="uot-form-label">فريق العمل (اسم كل طالب في سطر مستقل)</label>
                            <textarea name="team_members_raw" id="edit_team_members_raw" class="form-control uot-textarea" rows="3"></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="uot-form-label">البريد الإلكتروني العام للتواصل</label>
                            <input type="email" name="contact_email" id="edit_contact_email" class="form-control uot-input">
                        </div>
                        <div class="col-md-3">
                            <label class="uot-form-label">رقم الجناح بالمعرض</label>
                            <input type="text" name="booth_number" id="edit_booth_number" class="form-control uot-input">
                        </div>
                        <div class="col-md-3">
                            <label class="uot-form-label">حالة النشر والاعتماد</label>
                            <select name="status" id="edit_status" class="form-select uot-select">
                                <option value="published">منشور ومتاح للجمهور</option>
                                <option value="pending">بانتظار الاعتماد (Pending)</option>
                                <option value="draft">مسودة داخلية</option>
                                <option value="rejected">مرفوض</option>
                            </select>
                        </div>
                    </div>

                    {{-- القسم 3: المحتوى والملخص العلمي للمشروع --}}
                    <div class="uot-section-divider">
                        <span class="divider-icon"><i class="fas fa-file-alt"></i></span>
                        <h6 class="divider-title">المحتوى والملخص العلمي للمشروع</h6>
                        <span class="divider-badge">الملخص والأهداف الفنية</span>
                    </div>

                    <div class="row g-3 mb-2">
                        <div class="col-12">
                            <label class="uot-form-label">نبذة تعريفية مختصرة (Abstract)</label>
                            <textarea name="summary" id="edit_summary" class="form-control uot-textarea" rows="2"></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="uot-form-label">المشكلة التي يعالجها المشروع</label>
                            <textarea name="problem_statement" id="edit_problem_statement" class="form-control uot-textarea" rows="2"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="uot-form-label">الحل المقترح ومخرجات الابتكار</label>
                            <textarea name="solution_statement" id="edit_solution_statement" class="form-control uot-textarea" rows="2"></textarea>
                        </div>

                        <div class="col-12">
                            <label class="uot-form-label">أهداف المشروع</label>
                            <textarea name="objectives" id="edit_objectives" class="form-control uot-textarea" rows="2"></textarea>
                        </div>

                        <div class="col-12">
                            <label class="uot-form-label">التفاصيل الفنية والمواصفات (Tech Stack)</label>
                            <textarea name="technical_specifications" id="edit_technical_specifications" class="form-control uot-textarea" rows="2"></textarea>
                        </div>

                        <div class="col-12">
                            <label class="uot-form-label">وصف المشروع الكامل</label>
                            <textarea name="description" id="edit_description" class="form-control uot-textarea" rows="3"></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="uot-form-label">أبرز النتائج والمميزات</label>
                            <textarea name="key_outcomes" id="edit_key_outcomes" class="form-control uot-textarea" rows="2"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="uot-form-label">قابلية التطوير والتسويق التجاري</label>
                            <textarea name="market_viability" id="edit_market_viability" class="form-control uot-textarea" rows="2"></textarea>
                        </div>
                    </div>

                    {{-- القسم 4: الوسائط والروابط الخارجية --}}
                    <div class="uot-section-divider">
                        <span class="divider-icon"><i class="fas fa-photo-video"></i></span>
                        <h6 class="divider-title">الوسائط والروابط الخارجية</h6>
                        <span class="divider-badge">البوستر والروابط</span>
                    </div>

                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label class="uot-form-label">تحديث بوستر المشروع (Poster File / Image)</label>
                            <input type="file" name="poster_image" class="form-control uot-input" accept="image/*,application/pdf">
                        </div>
                        <div class="col-md-6">
                            <label class="uot-form-label">تحديث صورة الغلاف (Cover Image)</label>
                            <input type="file" name="cover_image" class="form-control uot-input" accept="image/*">
                        </div>

                        <div class="col-md-6">
                            <label class="uot-form-label">رابط المشروع البرمجي أو GitHub</label>
                            <input type="url" name="project_url" id="edit_project_url" class="form-control uot-input">
                        </div>
                        <div class="col-md-6">
                            <label class="uot-form-label">رابط فيديو العرض (Demo / YouTube)</label>
                            <input type="url" name="video_url" id="edit_video_url" class="form-control uot-input">
                        </div>
                    </div>

                    {{-- القسم 5: بيانات التدقيق الإداري والاتصال الداخلي (خاصة بالمسؤول) --}}
                    <div class="uot-admin-box">
                        <div class="uot-admin-box-header">
                            <i class="fas fa-user-shield fs-5 text-warning"></i>
                            <span>معلومات خاصة بالمسؤول فقط (بيانات داخلية لا تظهر للعامة)</span>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="uot-form-label text-dark">الرقم الجامعي / رقم القيد</label>
                                <input type="text" name="student_university_id" id="edit_student_university_id" class="form-control uot-input">
                            </div>
                            <div class="col-md-6">
                                <label class="uot-form-label text-dark">رقم الهاتف المسجل بالواتساب</label>
                                <input type="text" name="whatsapp_phone" id="edit_whatsapp_phone" class="form-control uot-input">
                            </div>

                            <div class="col-md-6">
                                <label class="uot-form-label text-dark">حالة النموذج الأولي</label>
                                <input type="text" name="prototype_status" id="edit_prototype_status" class="form-control uot-input">
                            </div>
                            <div class="col-md-6">
                                <label class="uot-form-label text-dark">المتطلبات التي يحتاجها المشروع أثناء العرض</label>
                                <input type="text" name="project_requirements" id="edit_project_requirements" class="form-control uot-input">
                            </div>

                            <div class="col-12">
                                <div class="form-check form-switch mb-2">
                                    <input type="checkbox" name="needs_special_equipment" value="1" class="form-check-input" id="edit_needs_special_equipment">
                                    <label class="form-check-label fw-bold text-dark" for="edit_needs_special_equipment">
                                        يحتاج المشروع معدات أو تجهيزات خاصة في قاعة المعرض
                                    </label>
                                </div>
                                <input type="text" name="special_equipment_details" id="edit_special_equipment_details" class="form-control uot-input">
                            </div>

                            <div class="col-md-6">
                                <label class="uot-form-label text-dark">الملخص التنفيذي للمراجعة الداخلية</label>
                                <textarea name="executive_summary" id="edit_executive_summary" class="form-control uot-textarea" rows="2"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="uot-form-label text-dark">ملاحظات أو قرارات إدارية</label>
                                <textarea name="admin_notes" id="edit_admin_notes" class="form-control uot-textarea" rows="2"></textarea>
                            </div>
                            <div class="col-12">
                                <label class="uot-form-label text-dark">سبب الرفض (إن وُجد)</label>
                                <textarea name="rejection_reason" id="edit_rejection_reason" class="form-control uot-textarea" rows="2"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-check form-switch mt-4 p-3 rounded-3 bg-light border d-flex align-items-center gap-3">
                        <input type="checkbox" name="is_featured" value="1" class="form-check-input m-0" id="editProjFeatured">
                        <label class="form-check-label fw-bold text-dark m-0" for="editProjFeatured">
                            <i class="fas fa-star text-warning me-1"></i> تمييز المشروع في الواجهة الرئيسية وأبرز مشاريع المعرض
                        </label>
                    </div>
                </div>

                <div class="modal-footer uot-modal-footer">
                    <button type="button" class="uot-btn-cancel" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> إلغاء
                    </button>
                    <button type="submit" class="uot-btn-submit">
                        <i class="fas fa-save me-1"></i> حفظ التعديلات
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    window.fairProjects = @json($allProjects->keyBy('id'));

    window.showModalSafe = function(modalId) {
        const el = document.getElementById(modalId);
        if (!el) {
            console.error('Modal element not found with ID:', modalId);
            return;
        }

        // 1. استخدام مكتبة Bootstrap 5 الرسمية إن وُجدت
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            try {
                const modal = bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el);
                modal.show();
                return;
            } catch (err) {
                console.warn('Bootstrap modal show failed, falling back:', err);
            }
        }

        // 2. المحاولة عبر jQuery
        if (typeof window.jQuery !== 'undefined' && typeof window.jQuery(el).modal === 'function') {
            try {
                window.jQuery(el).modal('show');
                return;
            } catch (err) {}
        }

        // 3. الحل البديل المباشر (Native DOM Fallback)
        el.classList.add('show');
        el.style.display = 'block';
        el.removeAttribute('aria-hidden');
        el.setAttribute('aria-modal', 'true');
        document.body.classList.add('modal-open');

        let backdrop = document.getElementById('custom-modal-backdrop');
        if (!backdrop) {
            backdrop = document.createElement('div');
            backdrop.id = 'custom-modal-backdrop';
            backdrop.className = 'modal-backdrop fade show';
            document.body.appendChild(backdrop);
        }
    };

    // معالج إغلاق النوافذ المنبثقة للطوارئ
    document.addEventListener('click', function(e) {
        const dismissBtn = e.target.closest('[data-bs-dismiss="modal"]');
        if (dismissBtn) {
            const modal = dismissBtn.closest('.modal');
            if (modal) {
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const inst = bootstrap.Modal.getInstance(modal);
                    if (inst) {
                        try {
                            inst.hide();
                            return;
                        } catch(err) {}
                    }
                }
                modal.classList.remove('show');
                modal.style.display = 'none';
                modal.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('modal-open');
                const backdrop = document.getElementById('custom-modal-backdrop') || document.querySelector('.modal-backdrop');
                if (backdrop) backdrop.remove();
            }
        }
    });

    window.editProjectById = function(id) {
        const proj = (window.fairProjects || {})[id];
        if (!proj) {
            console.error('Project not found with ID:', id);
            return;
        }

        const setVal = (fieldId, val) => {
            const el = document.getElementById(fieldId);
            if (el) el.value = (val !== null && val !== undefined) ? val : '';
        };

        const setChecked = (fieldId, val) => {
            const el = document.getElementById(fieldId);
            if (el) el.checked = Boolean(val);
        };

        const editForm = document.getElementById('editProjectForm');
        if (editForm) {
            editForm.action = "/admin/job-fair/projects/" + proj.id;
        }

        setVal('edit_title', proj.title);
        setVal('edit_faculty', proj.faculty);
        setVal('edit_department', proj.department);
        setVal('edit_graduation_year', proj.graduation_year || 2026);
        setVal('edit_project_type', proj.project_type);
        setVal('edit_main_category', proj.main_category);
        setVal('edit_supervisor_name', proj.supervisor_name);
        setVal('edit_supervisor_title', proj.supervisor_title);
        setVal('edit_contact_email', proj.contact_email);
        setVal('edit_booth_number', proj.booth_number);
        setVal('edit_status', proj.status || 'published');
        setVal('edit_summary', proj.summary);
        setVal('edit_problem_statement', proj.problem_statement);
        setVal('edit_solution_statement', proj.solution_statement);
        setVal('edit_objectives', proj.objectives);
        setVal('edit_technical_specifications', proj.technical_specifications);
        setVal('edit_description', proj.description);
        setVal('edit_key_outcomes', proj.key_outcomes);
        setVal('edit_market_viability', proj.market_viability);
        setVal('edit_project_url', proj.project_url);
        setVal('edit_video_url', proj.video_url);

        // الحقول الخاصة بالمسؤول فقط
        setVal('edit_student_university_id', proj.student_university_id);
        setVal('edit_whatsapp_phone', proj.whatsapp_phone);
        setVal('edit_prototype_status', proj.prototype_status);
        setVal('edit_project_requirements', proj.project_requirements);
        setChecked('edit_needs_special_equipment', proj.needs_special_equipment);
        setVal('edit_special_equipment_details', proj.special_equipment_details);
        setVal('edit_executive_summary', proj.executive_summary);
        setVal('edit_admin_notes', proj.admin_notes);
        setVal('edit_rejection_reason', proj.rejection_reason);
        setChecked('editProjFeatured', proj.is_featured);

        // أعضاء الفريق
        let teamLines = '';
        if (Array.isArray(proj.team_members)) {
            teamLines = proj.team_members.map(m => (m && (m.name || m)) || '').filter(Boolean).join('\n');
        } else if (typeof proj.team_members === 'string') {
            try {
                const parsed = JSON.parse(proj.team_members);
                if (Array.isArray(parsed)) {
                    teamLines = parsed.map(m => (m && (m.name || m)) || '').filter(Boolean).join('\n');
                } else {
                    teamLines = proj.team_members;
                }
            } catch(e) {
                teamLines = proj.team_members;
            }
        }
        setVal('edit_team_members_raw', teamLines);

        window.showModalSafe('editProjectModal');
    };
</script>
@endpush
@endsection
