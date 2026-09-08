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
    <div class="card border-0 shadow-sm rounded-4 mb-4 filter-card-premium">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2.5">
                <div class="filter-header-icon">
                    <i class="fas fa-filter text-primary"></i>
                </div>
                <div>
                    <h5 class="card-title mb-0 text-dark fw-bold" style="font-size: 1.05rem;">تصفية وبحث الخريجين</h5>
                    <span class="text-muted small">ابحث بالاسم أو رقم الهاتف وخصص النتائج حسب التخصص وسنة التخرج</span>
                </div>
            </div>
            @if(request()->anyFilled(['search', 'name', 'phone', 'major', 'graduation_year', 'employment_status']))
                <a href="{{ route($prefix . '.graduates') }}" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-none hover-shadow">
                    <i class="fas fa-times-circle"></i>
                    <span>إلغاء الفلاتر</span>
                </a>
            @endif
        </div>
        <div class="card-body p-3 p-md-4">
            <form action="{{ route($prefix . '.graduates') }}" method="GET">
                <div class="row g-3">
                    <!-- بحث باسم الخريج -->
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-bold text-secondary small mb-1.5 d-flex align-items-center gap-1.5">
                            <i class="fas fa-user text-primary" style="font-size: 0.82rem;"></i>
                            <span>اسم الخريج</span>
                        </label>
                        <div class="search-input-wrapper">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" name="name" id="name" class="form-control premium-search-input"
                                   value="{{ request('name') }}" placeholder="اكتب اسم الخريج للبحث..." autocomplete="off">
                            @if(request('name'))
                                <a href="{{ request()->fullUrlWithQuery(['name' => null]) }}" class="clear-input-btn" title="مسح الاسم">&times;</a>
                            @endif
                        </div>
                    </div>

                    <!-- بحث برقم الهاتف -->
                    <div class="col-md-6">
                        <label for="phone" class="form-label fw-bold text-secondary small mb-1.5 d-flex align-items-center gap-1.5">
                            <i class="fas fa-phone-alt text-primary" style="font-size: 0.82rem;"></i>
                            <span>رقم الهاتف</span>
                        </label>
                        <div class="search-input-wrapper">
                            <i class="fas fa-phone text-muted search-icon"></i>
                            <input type="text" name="phone" id="phone" class="form-control premium-search-input text-start" dir="ltr"
                                   value="{{ request('phone') }}" placeholder="09XXXXXXXX" autocomplete="off">
                            @if(request('phone'))
                                <a href="{{ request()->fullUrlWithQuery(['phone' => null]) }}" class="clear-input-btn" title="مسح رقم الهاتف">&times;</a>
                            @endif
                        </div>
                    </div>

                    <!-- تصفية حسب التخصص -->
                    <div class="col-md-4 col-lg-3">
                        <label for="major" class="form-label fw-bold text-secondary small mb-1.5 d-flex align-items-center gap-1.5">
                            <i class="fas fa-graduation-cap text-primary" style="font-size: 0.82rem;"></i>
                            <span>التخصص</span>
                        </label>
                        <select name="major" id="major" class="form-select premium-select">
                            <option value="">جميع التخصصات</option>
                            @foreach($majors as $major)
                                <option value="{{ $major }}" {{ request('major') == $major ? 'selected' : '' }}>{{ $major }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- تصفية حسب سنة التخرج -->
                    <div class="col-md-4 col-lg-3">
                        <label for="graduation_year" class="form-label fw-bold text-secondary small mb-1.5 d-flex align-items-center gap-1.5">
                            <i class="fas fa-calendar-alt text-primary" style="font-size: 0.82rem;"></i>
                            <span>سنة التخرج</span>
                        </label>
                        <select name="graduation_year" id="graduation_year" class="form-select premium-select">
                            <option value="">جميع السنوات</option>
                            @foreach($graduationYears as $year)
                                <option value="{{ $year }}" {{ request('graduation_year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- تصفية حسب حالة التوظيف -->
                    <div class="col-md-4 col-lg-3">
                        <label for="employment_status" class="form-label fw-bold text-secondary small mb-1.5 d-flex align-items-center gap-1.5">
                            <i class="fas fa-briefcase text-primary" style="font-size: 0.82rem;"></i>
                            <span>حالة التوظيف</span>
                        </label>
                        <select name="employment_status" id="employment_status" class="form-select premium-select">
                            <option value="">جميع الحالات</option>
                            <option value="employed" {{ request('employment_status') == 'employed' ? 'selected' : '' }}>موظف</option>
                            <option value="unemployed" {{ request('employment_status') == 'unemployed' ? 'selected' : '' }}>عاطل عن العمل</option>
                            <option value="seeking_opportunities" {{ request('employment_status') == 'seeking_opportunities' ? 'selected' : '' }}>باحث عن عمل</option>
                            <option value="continuing_education" {{ (request('employment_status') == 'continuing_education' || request('employment_status') == 'further_study') ? 'selected' : '' }}>مستكمل للدراسة</option>
                        </select>
                    </div>

                    <!-- أزرار البحث -->
                    <div class="col-md-12 col-lg-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary-gradient flex-grow-1 py-2.5 px-3 rounded-3 fw-bold d-inline-flex align-items-center justify-content-center gap-2 shadow-sm">
                            <i class="fas fa-search"></i>
                            <span>بحث وتصفية</span>
                        </button>
                        <a href="{{ route($prefix . '.graduates') }}" class="btn btn-light bg-white border text-secondary py-2.5 px-3 rounded-3 fw-semibold shadow-sm hover-shadow" title="إعادة تعيين الفلاتر">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>

                <!-- شريط الفلاتر النشطة إن وجدت -->
                @if(request()->anyFilled(['name', 'phone', 'major', 'graduation_year', 'employment_status']))
                    <div class="active-filters-bar mt-3 pt-3 border-top d-flex align-items-center flex-wrap gap-2">
                        <span class="small fw-bold text-muted d-inline-flex align-items-center gap-1">
                            <i class="fas fa-filter text-primary"></i> الفلاتر المطبقة:
                        </span>
                        @if(request('name'))
                            <span class="active-filter-tag">
                                <span>الاسم: <strong>{{ request('name') }}</strong></span>
                                <a href="{{ request()->fullUrlWithQuery(['name' => null]) }}" class="remove-tag" title="إزالة فلتر الاسم">&times;</a>
                            </span>
                        @endif
                        @if(request('phone'))
                            <span class="active-filter-tag">
                                <span>الهاتف: <strong dir="ltr">{{ request('phone') }}</strong></span>
                                <a href="{{ request()->fullUrlWithQuery(['phone' => null]) }}" class="remove-tag" title="إزالة فلتر الهاتف">&times;</a>
                            </span>
                        @endif
                        @if(request('major'))
                            <span class="active-filter-tag">
                                <span>التخصص: <strong>{{ request('major') }}</strong></span>
                                <a href="{{ request()->fullUrlWithQuery(['major' => null]) }}" class="remove-tag" title="إزالة فلتر التخصص">&times;</a>
                            </span>
                        @endif
                        @if(request('graduation_year'))
                            <span class="active-filter-tag">
                                <span>دفعة: <strong>{{ request('graduation_year') }}</strong></span>
                                <a href="{{ request()->fullUrlWithQuery(['graduation_year' => null]) }}" class="remove-tag" title="إزالة فلتر سنة التخرج">&times;</a>
                            </span>
                        @endif
                        @if(request('employment_status'))
                            @php
                                $statusLabels = [
                                    'employed' => 'موظف',
                                    'unemployed' => 'عاطل عن العمل',
                                    'seeking_opportunities' => 'باحث عن عمل',
                                    'continuing_education' => 'مستكمل للدراسة',
                                    'further_study' => 'مستكمل للدراسة',
                                ];
                            @endphp
                            <span class="active-filter-tag">
                                <span>الحالة: <strong>{{ $statusLabels[request('employment_status')] ?? request('employment_status') }}</strong></span>
                                <a href="{{ request()->fullUrlWithQuery(['employment_status' => null]) }}" class="remove-tag" title="إزالة فلتر الحالة">&times;</a>
                            </span>
                        @endif
                        <a href="{{ route($prefix . '.graduates') }}" class="btn btn-link btn-sm text-danger text-decoration-none ms-auto p-0 fw-bold">
                            <i class="fas fa-trash-alt me-1"></i> مسح الكل
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Graduates Table Card -->
    <div class="card border-0 shadow-sm rounded-4 table-card-premium">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2.5">
                <div class="filter-header-icon bg-blue-subtle">
                    <i class="fas fa-list-ul text-primary"></i>
                </div>
                <div>
                    <h5 class="card-title mb-0 text-dark fw-bold" style="font-size: 1.05rem;">قائمة الخريجين</h5>
                    <span class="text-muted small">عرض وتحديث السجلات والترشيحات المهنية</span>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2 fw-bold" style="font-size: 0.85rem;">
                    <i class="fas fa-user-check me-1"></i> {{ $graduates->count() }} خريج مسجل
                </span>
            </div>
        </div>
        <div class="card-body p-0">
            @if($graduates->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 premium-graduates-table">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 45px;">#</th>
                                <th style="min-width: 200px;">الخريج</th>
                                <th style="min-width: 170px;">التخصص والكلية</th>
                                <th class="text-center" style="width: 110px;">التخرج والمعدل</th>
                                <th class="text-center" style="width: 130px;">حالة التوظيف</th>
                                <th class="text-center" style="width: 110px;">الترشيحات</th>
                                <th class="text-center" style="width: 130px;">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($graduates as $graduate)
                            <tr>
                                <td class="text-center text-muted fw-bold">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div class="graduate-avatar flex-shrink-0">
                                            {{ mb_substr($graduate->name, 0, 1) }}
                                        </div>
                                        <div class="graduate-details">
                                            <a href="{{ route($prefix . '.graduates.show', $graduate->id) }}" class="graduate-name" title="{{ $graduate->name }}">
                                                {{ $graduate->name }}
                                            </a>
                                            <div class="graduate-meta d-flex align-items-center gap-2 flex-wrap mt-0.5">
                                                @if($graduate->phone)
                                                    <a href="tel:{{ $graduate->phone }}" class="graduate-phone" dir="ltr" title="الاتصال بالخريج">
                                                        <i class="fas fa-phone-alt me-1"></i>{{ $graduate->phone }}
                                                    </a>
                                                @endif
                                                @if($graduate->email)
                                                    <span class="graduate-email" title="{{ $graduate->email }}">
                                                        <i class="fas fa-envelope me-1"></i>{{ $graduate->email }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark" style="font-size: 0.92rem;">
                                        {{ $graduate->major }}
                                    </div>
                                    <div class="small text-muted d-flex align-items-center gap-1 mt-0.5">
                                        <i class="fas fa-university text-secondary" style="font-size: 0.78rem;"></i>
                                        <span>{{ $graduate->faculty ?? ($graduate->university ?? 'جامعة طرابلس') }}</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="fw-bold text-dark" style="font-size: 0.95rem;">
                                        {{ $graduate->graduation_year }}
                                    </div>
                                    @if($graduate->gpa)
                                        <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2.5 py-0.5 fw-bold mt-1" style="font-size: 0.75rem;">
                                            {{ $graduate->gpa }}%
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($graduate->employment_status == 'employed')
                                        <span class="status-badge status-employed">
                                            <i class="fas fa-check-circle"></i> موظف
                                        </span>
                                    @elseif($graduate->employment_status == 'seeking_opportunities')
                                        <span class="status-badge status-seeking">
                                            <i class="fas fa-search"></i> باحث عن عمل
                                        </span>
                                    @elseif($graduate->employment_status == 'unemployed')
                                        <span class="status-badge status-unemployed">
                                            <i class="fas fa-times-circle"></i> عاطل عن العمل
                                        </span>
                                    @elseif($graduate->employment_status == 'continuing_education' || $graduate->employment_status == 'further_study')
                                        <span class="status-badge status-student">
                                            <i class="fas fa-graduation-cap"></i> يواصل دراسته
                                        </span>
                                    @else
                                        <span class="badge bg-light text-secondary border rounded-pill px-3 py-1">غير محدد</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="nomination-pill {{ $graduate->accepted_nominations_count > 0 ? 'has-accepted' : '' }}">
                                        <i class="fas fa-paper-plane me-1"></i>
                                        <span>{{ $graduate->nominations_count }}</span>
                                        @if($graduate->accepted_nominations_count > 0)
                                            <span class="accepted-tag" title="مقبول في الوظيفة">✓ {{ $graduate->accepted_nominations_count }}</span>
                                        @endif
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-inline-flex align-items-center gap-1.5">
                                        <a href="{{ route($prefix . '.graduates.show', $graduate->id) }}" class="action-btn action-btn-info" title="عرض الملف الكامل">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route($prefix . '.graduates.edit', $graduate->id) }}" class="action-btn action-btn-warning" title="تعديل البيانات">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route($prefix . '.nominations.create', ['graduate_id' => $graduate->id]) }}" class="action-btn action-btn-primary" title="ترشيح لفرصة عمل">
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
                    <div class="empty-state-icon mb-3">
                        <i class="fas fa-users-slash fa-3x text-muted opacity-50"></i>
                    </div>
                    <h5 class="text-dark fw-bold mb-1">لا توجد نتائج مطابقة لبحثك</h5>
                    <p class="text-muted small mb-3">جرّب تغيير كلمات البحث أو مسح بعض الفلاتر لعرض مزيد من الخريجين</p>
                    <a href="{{ route($prefix . '.graduates') }}" class="btn btn-outline-primary rounded-pill px-4 py-2">
                        <i class="fas fa-redo me-1"></i> مسح الفلاتر والعودة
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
    /* Filter Card Styling */
    .filter-card-premium {
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05) !important;
        background: #ffffff;
    }
    .filter-header-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background-color: #eff6ff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }
    .bg-blue-subtle {
        background-color: #e0f2fe !important;
    }
    .search-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }
    .search-input-wrapper .search-icon {
        position: absolute;
        right: 14px;
        color: #94a3b8;
        font-size: 0.9rem;
        pointer-events: none;
        z-index: 2;
    }
    .premium-search-input {
        height: 44px;
        border-radius: 10px !important;
        border: 1.5px solid #e2e8f0;
        padding-right: 40px !important;
        padding-left: 14px !important;
        font-size: 0.92rem;
        color: #1e293b;
        background-color: #f8fafc;
        transition: all 0.2s ease;
    }
    .premium-search-input:focus {
        background-color: #ffffff;
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12);
        color: #0f172a;
    }
    .search-input-wrapper .clear-input-btn {
        position: absolute;
        left: 12px;
        color: #94a3b8;
        text-decoration: none;
        font-size: 1.2rem;
        line-height: 1;
        transition: color 0.15s;
    }
    .search-input-wrapper .clear-input-btn:hover {
        color: #ef4444;
    }
    .premium-select {
        height: 44px;
        border-radius: 10px !important;
        border: 1.5px solid #e2e8f0;
        font-size: 0.9rem;
        color: #1e293b;
        background-color: #f8fafc;
        transition: all 0.2s ease;
        padding-right: 12px;
        padding-left: 32px;
    }
    .premium-select:focus {
        background-color: #ffffff;
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12);
    }
    .btn-primary-gradient {
        background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%) !important;
        border: none !important;
        color: #ffffff !important;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .btn-primary-gradient:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3) !important;
        color: #ffffff !important;
    }
    .active-filter-tag {
        background-color: #eff6ff;
        color: #1e40af;
        border: 1px solid #bfdbfe;
        border-radius: 50rem;
        padding: 4px 12px;
        font-size: 0.82rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .active-filter-tag .remove-tag {
        color: #3b82f6;
        text-decoration: none;
        font-weight: bold;
        font-size: 1.1rem;
        line-height: 1;
    }
    .active-filter-tag .remove-tag:hover {
        color: #dc2626;
    }

    /* Table Styling */
    .table-card-premium {
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05) !important;
    }
    .premium-graduates-table {
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }
    .premium-graduates-table thead th {
        background-color: #f8fafc;
        color: #475569;
        font-size: 0.84rem;
        font-weight: 700;
        padding: 14px 16px;
        border-bottom: 2px solid #e2e8f0;
        white-space: nowrap;
    }
    .premium-graduates-table tbody td {
        padding: 13px 16px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        background-color: #ffffff;
        transition: background-color 0.15s ease;
    }
    .premium-graduates-table tbody tr:hover td {
        background-color: #f8fafc;
    }
    .graduate-avatar {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
        color: #ffffff;
        font-weight: 800;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 6px rgba(2, 132, 199, 0.25);
    }
    .graduate-name {
        font-weight: 700;
        font-size: 0.95rem;
        color: #0f172a;
        text-decoration: none;
        display: block;
        transition: color 0.15s;
    }
    .graduate-name:hover {
        color: #2563eb;
    }
    .graduate-phone {
        font-size: 0.78rem;
        color: #15803d;
        background-color: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 6px;
        padding: 1px 7px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.15s;
    }
    .graduate-phone:hover {
        background-color: #dcfce7;
        color: #166534;
    }
    .graduate-email {
        font-size: 0.78rem;
        color: #64748b;
        max-width: 170px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: inline-block;
    }
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 50rem;
        font-size: 0.8rem;
        font-weight: 600;
        white-space: nowrap;
    }
    .status-employed {
        background-color: #f0fdf4;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }
    .status-seeking {
        background-color: #fffbeb;
        color: #b45309;
        border: 1px solid #fde68a;
    }
    .status-unemployed {
        background-color: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }
    .status-student {
        background-color: #f0f9ff;
        color: #0369a1;
        border: 1px solid #bae6fd;
    }
    .nomination-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #475569;
        padding: 4px 10px;
        border-radius: 50rem;
        font-size: 0.8rem;
        font-weight: 600;
    }
    .nomination-pill.has-accepted {
        background-color: #f0fdf4;
        border-color: #bbf7d0;
        color: #166534;
    }
    .nomination-pill .accepted-tag {
        background-color: #16a34a;
        color: #ffffff;
        border-radius: 50rem;
        padding: 1px 6px;
        font-size: 0.72rem;
        font-weight: 700;
    }
    .action-btn {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.88rem;
        transition: all 0.15s ease;
        border: 1px solid transparent;
        text-decoration: none;
    }
    .action-btn:hover {
        transform: translateY(-2px);
    }
    .action-btn-info {
        background-color: #f0f9ff;
        color: #0284c7;
        border-color: #bae6fd;
    }
    .action-btn-info:hover {
        background-color: #0284c7;
        color: #ffffff;
        box-shadow: 0 4px 10px rgba(2, 132, 199, 0.25);
    }
    .action-btn-warning {
        background-color: #fffbeb;
        color: #d97706;
        border-color: #fde68a;
    }
    .action-btn-warning:hover {
        background-color: #d97706;
        color: #ffffff;
        box-shadow: 0 4px 10px rgba(217, 119, 6, 0.25);
    }
    .action-btn-primary {
        background-color: #eff6ff;
        color: #2563eb;
        border-color: #bfdbfe;
    }
    .action-btn-primary:hover {
        background-color: #2563eb;
        color: #ffffff;
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.25);
    }
</style>
@endpush
