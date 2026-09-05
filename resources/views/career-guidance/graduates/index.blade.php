@extends('layouts.app')

@section('title', 'إدارة الخريجين - الإرشاد المهني')

@php
    $prefix = request()->routeIs('admin.*') ? 'admin.career-guidance' : 'career-guidance';
@endphp

@section('content')
<div class="container-fluid">
    <!-- الشريط الأزرق الموحد المعتمد في المنظومة -->
    <x-page-hero
        title="إدارة بيانات الخريجين"
        subtitle="عرض وإدارة قاعدة بيانات الخريجين المسجلين في النظام ومتابعة الترشيحات والتوظيف"
        icon="fas fa-user-graduate"
        :breadcrumbs="[
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'لوحة الإرشاد المهني', 'url' => route($prefix . '.dashboard')],
            ['label' => 'إدارة بيانات الخريجين']
        ]"
        secondaryBadge="إجمالي الخريجين: {{ method_exists($graduates, 'total') ? $graduates->total() : $graduates->count() }}"
        secondaryBadgeIcon="fas fa-users"
    >
        <button type="button" class="btn btn-warning text-dark fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" data-bs-toggle="modal" data-bs-target="#importGraduatesModal" style="font-size: 0.88rem; transition: transform 0.2s ease;">
            <i class="fas fa-file-import fs-6"></i>
            <span>استيراد Excel</span>
        </button>
        <a href="{{ route($prefix . '.graduates.create') }}" class="btn btn-light bg-white text-primary fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem; transition: transform 0.2s ease;">
            <i class="fas fa-user-plus fs-6"></i>
            <span>إضافة خريج جديد</span>
        </a>
    </x-page-hero>

    <!-- Alert Messages -->
    @if(session('import_errors'))
        <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 shadow-sm mb-4" role="alert">
            <h6 class="fw-bold"><i class="fas fa-exclamation-triangle me-2"></i>أخطاء في الاستيراد:</h6>
            <ul class="mb-0">
                @foreach(session('import_errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 rounded-3 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 shadow-sm mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Search & Filter Card -->
    <div class="card-modern mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 text-primary fw-bold fs-6">
                <i class="fas fa-search-plus me-2"></i>تصفية وبحث الخريجين
            </h5>
            @if(request()->anyFilled(['search', 'name', 'phone', 'major', 'graduation_year', 'employment_status']))
                <a href="{{ route($prefix . '.graduates') }}" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                    <i class="fas fa-times me-1"></i> مسح جميع الفلاتر
                </a>
            @endif
        </div>
        <div class="card-body p-3 p-md-4">
            <form action="{{ route($prefix . '.graduates') }}" method="GET" class="row g-3">
                <!-- بحث بالاسم -->
                <div class="col-md-6 col-lg-3">
                    <label for="name" class="form-label-modern small fw-bold">
                        <i class="fas fa-user text-primary me-1"></i> اسم الخريج
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" name="name" id="name" class="form-control form-control-modern border-start-0"
                               value="{{ request('name') }}" placeholder="ابحث باسم الخريج...">
                    </div>
                </div>

                <!-- بحث برقم الهاتف -->
                <div class="col-md-6 col-lg-3">
                    <label for="phone" class="form-label-modern small fw-bold">
                        <i class="fas fa-phone-alt text-primary me-1"></i> رقم الهاتف
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-phone"></i></span>
                        <input type="text" name="phone" id="phone" class="form-control form-control-modern border-start-0"
                               value="{{ request('phone') }}" placeholder="مثال: 0912345678...">
                    </div>
                </div>

                <!-- تصفية حسب التخصص -->
                <div class="col-md-4 col-lg-2">
                    <label for="major" class="form-label-modern small fw-bold">
                        <i class="fas fa-graduation-cap text-primary me-1"></i> التخصص
                    </label>
                    <select name="major" id="major" class="form-select form-select-modern">
                        <option value="">جميع التخصصات</option>
                        @foreach($majors as $major)
                            <option value="{{ $major }}" {{ request('major') == $major ? 'selected' : '' }}>{{ $major }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- تصفية حسب سنة التخرج -->
                <div class="col-md-4 col-lg-2">
                    <label for="graduation_year" class="form-label-modern small fw-bold">
                        <i class="fas fa-calendar-alt text-primary me-1"></i> سنة التخرج
                    </label>
                    <select name="graduation_year" id="graduation_year" class="form-select form-select-modern">
                        <option value="">جميع السنوات</option>
                        @foreach($graduationYears as $year)
                            <option value="{{ $year }}" {{ request('graduation_year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- تصفية حسب حالة التوظيف -->
                <div class="col-md-4 col-lg-2">
                    <label for="employment_status" class="form-label-modern small fw-bold">
                        <i class="fas fa-briefcase text-primary me-1"></i> حالة التوظيف
                    </label>
                    <select name="employment_status" id="employment_status" class="form-select form-select-modern">
                        <option value="">جميع الحالات</option>
                        <option value="employed" {{ request('employment_status') == 'employed' ? 'selected' : '' }}>موظف</option>
                        <option value="unemployed" {{ request('employment_status') == 'unemployed' ? 'selected' : '' }}>عاطل عن العمل</option>
                        <option value="seeking_opportunities" {{ request('employment_status') == 'seeking_opportunities' ? 'selected' : '' }}>باحث عن عمل</option>
                        <option value="continuing_education" {{ (request('employment_status') == 'continuing_education' || request('employment_status') == 'further_study') ? 'selected' : '' }}>مستكمل للدراسة</option>
                    </select>
                </div>

                <!-- أزرار البحث -->
                <div class="col-12 d-flex justify-content-end align-items-center gap-2 pt-2 border-top">
                    <button type="submit" class="btn btn-primary-modern px-4 py-2">
                        <i class="fas fa-search me-1"></i> بحث وتصفية
                    </button>
                    <a href="{{ route($prefix . '.graduates') }}" class="btn btn-outline-secondary px-3 py-2" title="إعادة تعيين">
                        <i class="fas fa-redo me-1"></i> إعادة تعيين
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Graduates Table Card -->
    <div class="card-modern">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 text-primary fw-bold fs-6">
                <i class="fas fa-list me-2"></i>قائمة الخريجين
            </h5>
            <span class="badge bg-light text-secondary border rounded-pill px-3 py-1.5 fw-bold">
                {{ $graduates->count() }} خريج مسجل
            </span>
        </div>
        <div class="card-body p-0">
            @if($graduates->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 custom-graduates-table">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-center py-3 text-secondary small text-uppercase fw-bold" style="width: 50px;">#</th>
                                <th class="py-3 px-3 text-secondary small text-uppercase fw-bold" style="min-width: 220px;">الخريج</th>
                                <th class="py-3 px-3 text-secondary small text-uppercase fw-bold" style="min-width: 220px;">التخصص والكلية</th>
                                <th class="text-center py-3 text-secondary small text-uppercase fw-bold" style="width: 100px; white-space: nowrap;">سنة التخرج</th>
                                <th class="text-center py-3 text-secondary small text-uppercase fw-bold" style="width: 110px; white-space: nowrap;">المعدل (%)</th>
                                <th class="text-center py-3 text-secondary small text-uppercase fw-bold" style="width: 150px; white-space: nowrap;">حالة التوظيف</th>
                                <th class="text-center py-3 text-secondary small text-uppercase fw-bold" style="width: 120px; white-space: nowrap;">الترشيحات</th>
                                <th class="text-center py-3 text-secondary small text-uppercase fw-bold" style="width: 140px; white-space: nowrap;">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($graduates as $graduate)
                            <tr>
                                <td class="text-center text-muted fw-bold">{{ $loop->iteration }}</td>
                                <td class="px-3">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle fw-bold d-flex align-items-center justify-content-center me-3 flex-shrink-0 shadow-sm" style="width: 42px; height: 42px; font-size: 1.15rem; background-color: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd;">
                                            {{ mb_substr($graduate->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <a href="{{ route($prefix . '.graduates.show', $graduate->id) }}" class="fw-bold text-dark text-decoration-none d-block hover-primary" style="font-size: 0.95rem;">
                                                {{ $graduate->name }}
                                            </a>
                                            <div class="small text-muted d-flex align-items-center gap-2 flex-wrap mt-0.5">
                                                @if($graduate->phone)
                                                    <span class="text-nowrap" title="رقم الهاتف">
                                                        <i class="fas fa-phone-alt text-primary me-1" style="font-size: 0.72rem;"></i><span dir="ltr">{{ $graduate->phone }}</span>
                                                    </span>
                                                @endif
                                                @if($graduate->email)
                                                    <span class="text-truncate" style="max-width: 170px;" title="{{ $graduate->email }}">
                                                        <i class="fas fa-envelope text-muted me-1" style="font-size: 0.72rem;"></i>{{ $graduate->email }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3">
                                    <div class="fw-bold text-dark mb-1" style="font-size: 0.92rem;">
                                        {{ $graduate->major }}
                                    </div>
                                    <div class="small text-muted">
                                        <i class="fas fa-university me-1 text-secondary"></i>{{ $graduate->faculty ?? ($graduate->university ?? 'جامعة طرابلس') }}
                                    </div>
                                </td>
                                <td class="text-center fw-bold text-secondary" style="white-space: nowrap;">
                                    {{ $graduate->graduation_year }}
                                </td>
                                <td class="text-center" style="white-space: nowrap;">
                                    @if($graduate->gpa)
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-3 py-1.5 fw-bold" style="font-size: 0.85rem;">
                                            {{ $graduate->gpa }}%
                                        </span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td class="text-center" style="white-space: nowrap;">
                                    @if($graduate->employment_status == 'employed')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1.5 fw-semibold" style="font-size: 0.82rem;">
                                            <i class="fas fa-check-circle me-1"></i> موظف
                                        </span>
                                    @elseif($graduate->employment_status == 'seeking_opportunities')
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-3 py-1.5 fw-semibold" style="font-size: 0.82rem;">
                                            <i class="fas fa-search me-1"></i> باحث عن عمل
                                        </span>
                                    @elseif($graduate->employment_status == 'unemployed')
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-1.5 fw-semibold" style="font-size: 0.82rem;">
                                            <i class="fas fa-times-circle me-1"></i> عاطل عن العمل
                                        </span>
                                    @elseif($graduate->employment_status == 'continuing_education' || $graduate->employment_status == 'further_study')
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-3 py-1.5 fw-semibold" style="font-size: 0.82rem;">
                                            <i class="fas fa-graduation-cap me-1"></i> يواصل دراسته
                                        </span>
                                    @else
                                        <span class="badge bg-light text-secondary border rounded-pill px-3 py-1.5" style="font-size: 0.82rem;">غير محدد</span>
                                    @endif
                                </td>
                                <td class="text-center" style="white-space: nowrap;">
                                    <div class="d-flex flex-column align-items-center gap-1">
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.8rem;">
                                            <i class="fas fa-paper-plane me-1"></i> {{ $graduate->nominations_count }} ترشيح
                                        </span>
                                        @if($graduate->accepted_nominations_count > 0)
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2.5 py-0.5" style="font-size: 0.72rem;">
                                                <i class="fas fa-check me-1"></i> {{ $graduate->accepted_nominations_count }} مقبول
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center" style="white-space: nowrap;">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <a href="{{ route($prefix . '.graduates.show', $graduate->id) }}" class="btn btn-sm btn-outline-info rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm action-btn-hover" style="width: 34px; height: 34px;" title="عرض التفاصيل">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route($prefix . '.graduates.edit', $graduate->id) }}" class="btn btn-sm btn-outline-warning rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm action-btn-hover" style="width: 34px; height: 34px;" title="تعديل البيانات">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route($prefix . '.nominations.create', ['graduate_id' => $graduate->id]) }}" class="btn btn-sm btn-outline-primary rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm action-btn-hover" style="width: 34px; height: 34px;" title="ترشيح لفرصة عمل">
                                            <i class="fas fa-paper-plane"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-users-slash text-muted display-4 mb-3 opacity-50"></i>
                    <h5 class="text-dark fw-bold">لا توجد بيانات خريجين مطابقة</h5>
                    <p class="text-muted">يمكنك البدء بإضافة خريجين جدد أو استيرادهم من ملف Excel</p>
                    <a href="{{ route($prefix . '.graduates.create') }}" class="btn btn-primary-modern px-4 rounded-pill">
                        <i class="fas fa-plus me-2"></i>إضافة أول خريج
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal لاستيراد الخريجين -->
<div class="modal fade" id="importGraduatesModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-white py-3 border-bottom">
                <h5 class="modal-title text-primary fw-bold">
                    <i class="fas fa-file-import me-2"></i>
                    استيراد بيانات الخريجين من Excel
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route($prefix . '.import.graduates') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- تحميل الملف -->
                    <div class="mb-4">
                        <label for="excel_file" class="form-label-modern fw-bold">رفع ملف Excel</label>
                        <input type="file" class="form-control form-control-modern @error('excel_file') is-invalid @enderror" 
                               id="excel_file" name="excel_file" accept=".xlsx,.xls,.csv" required>
                        @error('excel_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text small text-muted">الملفات المسموحة: .xlsx, .xls, .csv (الحجم الأقصى: 5MB)</div>
                    </div>

                    <!-- تعليمات التنسيق -->
                    <div class="alert alert-info border-0 rounded-3 mb-4">
                        <h6 class="alert-heading fw-bold mb-2">
                            <i class="fas fa-info-circle me-2"></i>
                            تعليمات تنسيق الملف
                        </h6>
                        <hr class="my-2">
                        <div class="row">
                            <div class="col-md-6">
                                <strong class="small d-block mb-1">الأعمدة المطلوبة:</strong>
                                <ul class="mb-2 small">
                                    <li><code>name</code> - الاسم الكامل <span class="text-danger">*</span></li>
                                    <li><code>major</code> - التخصص <span class="text-danger">*</span></li>
                                    <li><code>graduation_year</code> - سنة التخرج <span class="text-danger">*</span></li>
                                    <li><code>employment_status</code> - حالة التوظيف <span class="text-danger">*</span></li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <strong class="small d-block mb-1">الأعمدة الاختيارية:</strong>
                                <ul class="mb-0 small">
                                    <li><code>email</code> - البريد الإلكتروني</li>
                                    <li><code>phone</code> - رقم الهاتف</li>
                                    <li><code>gpa</code> - المعدل التراكمي (%)</li>
                                    <li><code>degree</code> - الدرجة العلمية</li>
                                    <li><code>skills</code> - المهارات (مفصولة بفاصلة)</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- أزرار الإجراء -->
                    <div class="d-flex justify-content-between align-items-center pt-2">
                        <a href="{{ route($prefix . '.download.template') }}" class="btn btn-outline-secondary rounded-pill">
                            <i class="fas fa-download me-2"></i>
                            تحميل النموذج
                        </a>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">
                                إلغاء
                            </button>
                            <button type="submit" class="btn btn-primary-modern rounded-pill px-4">
                                <i class="fas fa-upload me-1"></i>
                                بدء الاستيراد
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .custom-graduates-table thead th {
        background-color: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
        color: #475569;
        font-size: 0.85rem;
        padding-top: 14px;
        padding-bottom: 14px;
    }
    .custom-graduates-table tbody td {
        padding-top: 14px;
        padding-bottom: 14px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .custom-graduates-table tbody tr:hover {
        background-color: #f8fafc !important;
    }
    .hover-primary:hover {
        color: #0284c7 !important;
    }
    .action-btn-hover {
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .action-btn-hover:hover {
        transform: translateY(-2px);
    }
</style>
@endpush
