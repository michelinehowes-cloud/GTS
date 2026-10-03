@extends('layouts.app')

@section('title', 'إدارة مشاريع التخرج والأرشيف - ' . $fair->title)

@section('content')
<div class="container-fluid py-4" dir="rtl">
    <!-- Header Row -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 font-weight-bold">
                <i class="fas fa-folder-open text-primary me-2"></i>
                إدارة مشاريع التخرج والأرشيف السنوي
            </h1>
            <p class="text-muted mb-0">
                {{ $fair->title }} — منصة مراجعة، اعتماد، ونشر ابتكارات وبحوث تخرج طلبة كليات جامعة طرابلس
            </p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('job-fair.public.projects.submit-fair', $fair->id) }}" target="_blank" class="btn btn-warning text-dark fw-bold shadow-sm">
                <i class="fas fa-external-link-alt"></i> نموذج تقديم الخريجين
            </a>
            <form action="{{ route('job-fair.admin.toggle-feature', $fair->id) }}" method="POST" class="d-inline m-0">
                @csrf
                <input type="hidden" name="feature" value="projects">
                <button type="submit" class="btn {{ $fair->is_projects_published ? 'btn-success' : 'btn-outline-warning text-dark' }} shadow-sm fw-bold d-inline-flex align-items-center gap-1.5" title="انقر للتبديل بين إظهار المشاريع للجمهور أو إخفائها كـ Coming Soon">
                    <i class="fas {{ $fair->is_projects_published ? 'fa-eye' : 'fa-clock' }}"></i>
                    <span>{{ $fair->is_projects_published ? 'المشاريع منشورة للجمهور' : 'المشاريع قيد التحضير (Coming Soon)' }}</span>
                </button>
            </form>
            <a href="{{ route('job-fair.public.projects', $fair->id) }}" target="_blank" class="btn btn-outline-primary shadow-sm">
                <i class="fas fa-desktop"></i> معاينة المعرض الرقمي
            </a>
            <a href="{{ route('job-fair.admin.projects.export', $fair->id) }}" class="btn btn-outline-success shadow-sm">
                <i class="fas fa-file-excel"></i> تصدير البيانات (CSV)
            </a>
            <a href="{{ route('job-fair.admin.show', $fair->id) }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fas fa-arrow-right"></i> لوحة المعرض
            </a>
            <button class="btn btn-primary shadow-sm" onclick="showModalSafe('addProjectModal')" data-bs-toggle="modal" data-bs-target="#addProjectModal">
                <i class="fas fa-plus-circle"></i> إضافة مشروع جديد
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle me-1"></i> يرجى مراجعة الأخطاء التالية:
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- KPI Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-lg bg-white h-100 border-left-primary">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small font-weight-bold text-uppercase mb-1">إجمالي المشاريع المسجلة</div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] ?? 0 }}</div>
                    </div>
                    <div class="text-primary display-6"><i class="fas fa-folder"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-lg bg-white h-100 border-left-warning" style="{{ ($stats['pending'] ?? 0) > 0 ? 'background: #fffdf5;' : '' }}">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-warning small font-weight-bold text-uppercase mb-1">بانتظار المراجعة والاعتماد</div>
                        <div class="h4 mb-0 font-weight-bold text-warning d-flex align-items-center gap-2">
                            <span>{{ $stats['pending'] ?? 0 }}</span>
                            @if(($stats['pending'] ?? 0) > 0)
                                <span class="badge badge-danger text-white small" style="font-size: 0.7rem; animation: pulse 2s infinite;">بحاجة لاتخاذ إجراء</span>
                            @endif
                        </div>
                    </div>
                    <div class="text-warning display-6"><i class="fas fa-hourglass-half"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-lg bg-white h-100 border-left-success">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small font-weight-bold text-uppercase mb-1">المشاريع المعتمدة والمنشورة</div>
                        <div class="h4 mb-0 font-weight-bold text-success">{{ $stats['published'] ?? 0 }}</div>
                    </div>
                    <div class="text-success display-6"><i class="fas fa-check-circle"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-lg bg-white h-100 border-left-info">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small font-weight-bold text-uppercase mb-1">الكليات المشاركة</div>
                        <div class="h4 mb-0 font-weight-bold text-info">{{ $stats['faculties'] ?? 0 }}</div>
                    </div>
                    <div class="text-info display-6"><i class="fas fa-university"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Tabs Nav -->
    <div class="card shadow-sm border-0 rounded-lg mb-4">
        <div class="card-body p-2">
            <ul class="nav nav-pills gap-2">
                <li class="nav-item">
                    <a class="nav-link {{ $statusFilter === 'all' ? 'active' : '' }}" href="{{ route('job-fair.admin.projects.index', ['fair' => $fair->id, 'status' => 'all']) }}">
                        <i class="fas fa-list-ul me-1"></i> كافة المشاريع
                        <span class="badge bg-light text-dark ms-1">{{ $stats['total'] }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $statusFilter === 'pending' ? 'active bg-warning text-dark' : 'text-warning' }}" href="{{ route('job-fair.admin.projects.index', ['fair' => $fair->id, 'status' => 'pending']) }}">
                        <i class="fas fa-hourglass-half me-1"></i> بانتظار الاعتماد والمراجعة
                        <span class="badge {{ ($stats['pending'] ?? 0) > 0 ? 'bg-danger text-white' : 'bg-light text-dark' }} ms-1">{{ $stats['pending'] }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $statusFilter === 'published' ? 'active bg-success' : 'text-success' }}" href="{{ route('job-fair.admin.projects.index', ['fair' => $fair->id, 'status' => 'published']) }}">
                        <i class="fas fa-check-double me-1"></i> المعتمدة والمنشورة
                        <span class="badge bg-light text-dark ms-1">{{ $stats['published'] }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $statusFilter === 'rejected' ? 'active bg-danger' : 'text-danger' }}" href="{{ route('job-fair.admin.projects.index', ['fair' => $fair->id, 'status' => 'rejected']) }}">
                        <i class="fas fa-times-circle me-1"></i> المرفوضة
                        <span class="badge bg-light text-dark ms-1">{{ $stats['rejected'] }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $statusFilter === 'draft' ? 'active bg-secondary' : 'text-secondary' }}" href="{{ route('job-fair.admin.projects.index', ['fair' => $fair->id, 'status' => 'draft']) }}">
                        <i class="fas fa-pencil-alt me-1"></i> المسودات
                        <span class="badge bg-light text-dark ms-1">{{ $allProjects->where('status', 'draft')->count() }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Projects Table Card -->
    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-table me-1"></i>
                @if($statusFilter === 'pending')
                    المشاريع التي بانتظار المراجعة والاعتماد
                @elseif($statusFilter === 'published')
                    المشاريع المعتمدة والمنشورة في المعرض الرقمي
                @elseif($statusFilter === 'rejected')
                    المشاريع المرفوضة
                @else
                    قائمة مشاريع التخرج المسجلة ({{ $projects->count() }})
                @endif
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th class="border-0" style="width: 40px;">#</th>
                            <th class="border-0">عنوان المشروع والتصنيف</th>
                            <th class="border-0">الكلية والتخصص</th>
                            <th class="border-0">فريق العمل والتواصل</th>
                            <th class="border-0">بيانات المسؤول السرية</th>
                            <th class="border-0 text-center">الجناح</th>
                            <th class="border-0 text-center">الحالة</th>
                            <th class="border-0 text-center" style="width: 220px;">الإجراءات والاعتماد</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects as $proj)
                            <tr class="{{ $proj->status === 'pending' ? 'table-warning bg-opacity-10' : '' }}">
                                <td class="align-middle text-muted">{{ $loop->iteration }}</td>
                                <td class="align-middle">
                                    <h6 class="mb-1 font-weight-bold text-dark">{{ $proj->title }}</h6>
                                    <div class="d-flex align-items-center gap-1 flex-wrap">
                                        @if($proj->project_type)
                                            <span class="badge bg-light text-primary border"><i class="fas fa-cube me-1"></i>{{ $proj->project_type }}</span>
                                        @endif
                                        @if($proj->main_category)
                                            <span class="badge bg-light text-dark border">{{ $proj->main_category }}</span>
                                        @endif
                                        @if($proj->is_featured)
                                            <span class="badge badge-warning text-dark"><i class="fas fa-star"></i> مميز</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <div class="font-weight-bold text-primary">
                                        <i class="{{ $proj->faculty_icon }} me-1"></i> {{ $proj->faculty }}
                                    </div>
                                    <small class="text-muted">{{ $proj->department }} — {{ $proj->graduation_year }}</small>
                                </td>
                                <td class="align-middle">
                                    <small class="text-dark d-block">
                                        @php
                                            $names = collect($proj->team_list)->pluck('name')->filter()->implode(' • ');
                                        @endphp
                                        {{ Str::limit($names ?: 'غير محدد', 45) }}
                                    </small>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        <span class="badge bg-secondary text-white">{{ count($proj->team_list) }} طلاب</span>
                                        @if($proj->contact_email)
                                            <a href="mailto:{{ $proj->contact_email }}" class="text-muted small" title="{{ $proj->contact_email }}"><i class="fas fa-envelope text-primary"></i></a>
                                        @endif
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <div class="small">
                                        @if($proj->student_university_id)
                                            <div><i class="fas fa-id-card text-muted me-1"></i>قيد: <strong>{{ $proj->student_university_id }}</strong></div>
                                        @endif
                                        @if($proj->whatsapp_phone)
                                            <div>
                                                <i class="fab fa-whatsapp text-success me-1"></i>
                                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $proj->whatsapp_phone) }}" target="_blank" class="text-success text-decoration-none">
                                                    {{ $proj->whatsapp_phone }}
                                                </a>
                                            </div>
                                        @endif
                                        @if($proj->needs_special_equipment)
                                            <span class="badge bg-danger text-white small" title="{{ $proj->special_equipment_details }}"><i class="fas fa-plug me-1"></i>معدات خاصة</span>
                                        @endif
                                        @if($proj->prototype_status)
                                            <span class="badge bg-info text-white small">{{ Str::limit($proj->prototype_status, 20) }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="align-middle text-center">
                                    @if($proj->booth_number)
                                        <span class="badge badge-success px-2 py-1">{{ $proj->booth_number }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="align-middle text-center">
                                    <span class="badge {{ $proj->status_badge_class }} px-2 py-1">
                                        {{ $proj->status_label }}
                                    </span>
                                </td>
                                <td class="align-middle text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <!-- زر معاينة ملف المراجعة الشامل (خاص بالمسؤول) -->
                                        <button type="button" class="btn btn-outline-info" title="معاينة كافة التفاصيل وحقول المراجعة والاعتماد" data-bs-toggle="modal" data-bs-target="#reviewModal{{ $proj->id }}">
                                            <i class="fas fa-file-invoice"></i> ملف المشروع
                                        </button>

                                        <!-- المعاينة العامة -->
                                        <a href="{{ route('job-fair.public.projects.show', $proj->id) }}" target="_blank" class="btn btn-outline-secondary" title="معاينة الصفحة العامة">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>

                                        <!-- تعديل -->
                                        <button type="button" class="btn btn-outline-primary" title="تعديل" onclick="editProjectById({{ $proj->id }})">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <!-- حذف -->
                                        <form action="{{ route('job-fair.admin.projects.destroy', $proj->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المشروع نهائياً؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="حذف">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>

                                    <!-- أزرار سريعة للمشاريع بانتظار الاعتماد -->
                                    @if($proj->status === 'pending')
                                        <div class="mt-2 d-flex justify-content-center gap-1">
                                            <form action="{{ route('job-fair.admin.projects.update-status', $proj->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="published">
                                                <button type="submit" class="btn btn-sm btn-success py-0 px-2 fw-bold" title="موافقة فورية ونشر في المعرض">
                                                    <i class="fas fa-check"></i> موافقة ونشر
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2 fw-bold" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $proj->id }}" title="رفض المشروع مع ذكر السبب">
                                                <i class="fas fa-times"></i> رفض
                                            </button>
                                        </div>
                                    @endif
                                </td>
                            </tr>

                            <!-- Modal: Review Full Details & Confidential Admin Fields -->
                            <div class="modal fade text-start" id="reviewModal{{ $proj->id }}" tabindex="-1" aria-labelledby="reviewModalLabel{{ $proj->id }}" aria-hidden="true" dir="rtl">
                                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <div class="modal-header bg-dark text-white">
                                            <h5 class="modal-title font-weight-bold" id="reviewModalLabel{{ $proj->id }}">
                                                <i class="fas fa-microscope text-warning me-2"></i>
                                                ملف مراجعة المشروع واعتماده: {{ $proj->title }}
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <!-- شريط الحالة والإجراءات الإدارية السريعة -->
                                            <div class="card bg-light border-0 p-3 mb-4 rounded-3">
                                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                                    <div>
                                                        <span class="text-muted small">الحالة الحالية:</span>
                                                        <span class="badge {{ $proj->status_badge_class }} fs-6 ms-1">{{ $proj->status_label }}</span>
                                                        @if($proj->booth_number)
                                                            <span class="badge bg-success ms-2">جناح رقم: {{ $proj->booth_number }}</span>
                                                        @endif
                                                    </div>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <!-- نموذج الاعتماد والنشر وتخصيص الجناح -->
                                                        <form action="{{ route('job-fair.admin.projects.update-status', $proj->id) }}" method="POST" class="d-flex align-items-center gap-2">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="published">
                                                            <input type="text" name="booth_number" value="{{ $proj->booth_number }}" class="form-control form-control-sm" placeholder="رقم الجناح" style="width: 110px;">
                                                            <div class="form-check form-check-inline m-0">
                                                                <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="featCheck{{ $proj->id }}" {{ $proj->is_featured ? 'checked' : '' }}>
                                                                <label class="form-check-label small" for="featCheck{{ $proj->id }}">مميز</label>
                                                            </div>
                                                            <button type="submit" class="btn btn-sm btn-success fw-bold">
                                                                <i class="fas fa-check-circle me-1"></i> اعتماد ونشر
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row g-4">
                                                <!-- العمود الأيمن: المعلومات العامة (Public Fields) -->
                                                <div class="col-lg-7">
                                                    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                                                        <i class="fas fa-globe text-info me-1"></i> البيانات المعروضة للجمهور والزوار (22 حقلاً)
                                                    </h6>

                                                    <table class="table table-bordered table-sm mb-3">
                                                        <tbody>
                                                            <tr>
                                                                <th style="width: 30%;" class="bg-light">1. عنوان المشروع</th>
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
                                                                <th class="bg-light">8-9. المشرف واللقب</th>
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
                                                    <div class="card border-danger h-100 shadow-sm">
                                                        <div class="card-header bg-danger text-white py-2">
                                                            <i class="fas fa-lock me-1"></i> معلومات خاصة بالمسؤول فقط (لا تظهر للعامة)
                                                        </div>
                                                        <div class="card-body p-3">
                                                            <table class="table table-sm table-striped">
                                                                <tbody>
                                                                    <tr>
                                                                        <th class="text-danger" style="width: 45%;">1. الرقم الجامعي (رقم القيد):</th>
                                                                        <td><strong class="fs-6 text-dark">{{ $proj->student_university_id ?? 'غير مسجل' }}</strong></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th class="text-danger">2. رقم الواتساب المعتمد:</th>
                                                                        <td>
                                                                            @if($proj->whatsapp_phone)
                                                                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $proj->whatsapp_phone) }}" target="_blank" class="fw-bold text-success">
                                                                                    <i class="fab fa-whatsapp me-1"></i>{{ $proj->whatsapp_phone }}
                                                                                </a>
                                                                            @else
                                                                                <span class="text-muted">-</span>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th class="text-danger">3. المتطلبات التي يحتاجها المشروع:</th>
                                                                        <td>{{ $proj->project_requirements ?: 'لا توجد متطلبات خاصة' }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th class="text-danger">4. هل يحتاج معدات خاصة أثناء العرض:</th>
                                                                        <td>
                                                                            @if($proj->needs_special_equipment)
                                                                                <span class="badge bg-danger text-white">نعم يحتاج معدات</span>
                                                                                <div class="small mt-1 text-danger fw-bold">{{ $proj->special_equipment_details }}</div>
                                                                            @else
                                                                                <span class="badge bg-success text-white">لا يحتاج</span>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th class="text-danger">5. المتطلبات الإضافية:</th>
                                                                        <td>{{ $proj->additional_requirements ?: 'لا توجد' }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th class="text-danger">6. الملخص التنفيذي للمراجعة:</th>
                                                                        <td><div class="small bg-light p-2 rounded border">{!! nl2br(e($proj->executive_summary ?: 'لم يدرج ملخص تنفيذي')) !!}</div></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th class="text-danger">7. حالة النموذج الأولي:</th>
                                                                        <td><span class="badge bg-primary">{{ $proj->prototype_status ?: 'غير محدد' }}</span></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th class="text-danger">8. حالة النشر والمراجعة:</th>
                                                                        <td><span class="badge {{ $proj->status_badge_class }}">{{ $proj->status_label }}</span></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th class="text-danger">9. رقم الجناح بالمعرض:</th>
                                                                        <td><strong>{{ $proj->booth_number ?: 'لم يُحدد بعد' }}</strong></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th class="text-danger">10. تمييز بالرئيسية:</th>
                                                                        <td>{{ $proj->is_featured ? 'نعم (مميز)' : 'لا' }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th class="text-danger">11. ملاحظات إدارية:</th>
                                                                        <td>{{ $proj->admin_notes ?: 'لا توجد ملاحظات إدارية' }}</td>
                                                                    </tr>
                                                                    @if($proj->rejection_reason)
                                                                    <tr class="table-danger">
                                                                        <th class="text-danger">سبب الرفض:</th>
                                                                        <td class="text-danger fw-bold">{{ $proj->rejection_reason }}</td>
                                                                    </tr>
                                                                    @endif
                                                                </tbody>
                                                            </table>

                                                            <!-- نموذج تعديل الملاحظات الإدارية السريع -->
                                                            <form action="{{ route('job-fair.admin.projects.update-status', $proj->id) }}" method="POST" class="mt-3 border-top pt-3">
                                                                @csrf
                                                                @method('PATCH')
                                                                <input type="hidden" name="status" value="{{ $proj->status }}">
                                                                <label class="small font-weight-bold">تحديث ملاحظات الإدارة الخاصة بالمشروع:</label>
                                                                <textarea name="admin_notes" class="form-control form-control-sm mb-2" rows="2" placeholder="أدخل أي ملاحظات تنظيمية للمشروع...">{{ $proj->admin_notes }}</textarea>
                                                                <button type="submit" class="btn btn-sm btn-outline-dark w-100">
                                                                    <i class="fas fa-save me-1"></i> حفظ الملاحظات الإدارية
                                                                </button>
                                                            </form>
                                                        </div>
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
                                <div class="modal-dialog">
                                    <form action="{{ route('job-fair.admin.projects.update-status', $proj->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="rejected">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title font-weight-bold" id="rejectModalLabel{{ $proj->id }}">
                                                    <i class="fas fa-times-circle me-1"></i> رفض مشروع التخرج
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <p>أنت على وشك رفض مشروع: <strong>«{{ $proj->title }}»</strong>.</p>
                                                <div class="form-group mb-3">
                                                    <label class="font-weight-bold">سبب الرفض (إجراء إداري) <span class="text-danger">*</span></label>
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
                                <td colspan="8" class="text-center py-5 text-muted">
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
</div>

<!-- Modal: Add Project -->
<div class="modal fade" id="addProjectModal" tabindex="-1" role="dialog" aria-labelledby="addProjectModalLabel" aria-hidden="true" dir="rtl">
    <div class="modal-dialog modal-lg" role="document">
        <form id="addProjectForm" action="{{ route('job-fair.admin.projects.store', $fair->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
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
    <div class="modal-dialog modal-lg" role="document">
        <form id="editProjectForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
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
