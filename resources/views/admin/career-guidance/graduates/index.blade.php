@extends('layouts.app')

@section('title', 'إدارة الخريجين - مسؤول الإرشاد المهني')

@push('styles')
<style>
    .action-circle-btn {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        padding: 0;
        line-height: 1;
    }
    .action-circle-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.12);
    }
    .action-circle-btn i { margin: 0 !important; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    @include('components.breadcrumbs', [
        'items' => [
            ['label' => 'الرئيسية', 'url' => route('admin.dashboard')],
            ['label' => 'إدارة الخريجين', 'active' => true],
        ]
    ])

    <!-- رسائل التنبيه -->
    @if(session('import_errors'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <h6><i class="fas fa-exclamation-triangle me-2"></i>أخطاء في الاستيراد:</h6>
            <ul class="mb-0">
                @foreach(session('import_errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- فلترة البحث -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card-modern">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 text-primary font-weight-bold">
                        <i class="fas fa-filter me-2"></i>فلاتر البحث
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.career-guidance.graduates') }}" method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label for="major" class="form-label-modern small fw-bold">التخصص</label>
                            <select name="major" id="major" class="form-select-modern form-select-sm">
                                <option value="">جميع التخصصات</option>
                                @foreach($majors as $major)
                                    <option value="{{ $major }}" {{ request('major') == $major ? 'selected' : '' }}>{{ $major }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="graduation_year" class="form-label-modern small fw-bold">سنة التخرج</label>
                            <select name="graduation_year" id="graduation_year" class="form-select-modern form-select-sm">
                                <option value="">جميع السنوات</option>
                                @foreach($graduationYears as $year)
                                    <option value="{{ $year }}" {{ request('graduation_year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="employment_status" class="form-label-modern small fw-bold">حالة التوظيف</label>
                            <select name="employment_status" id="employment_status" class="form-select-modern form-select-sm">
                                <option value="">جميع الحالات</option>
                                <option value="employed" {{ request('employment_status') == 'employed' ? 'selected' : '' }}>موظف</option>
                                <option value="unemployed" {{ request('employment_status') == 'unemployed' ? 'selected' : '' }}>غير موظف</option>
                                <option value="seeking_opportunities" {{ request('employment_status') == 'seeking_opportunities' ? 'selected' : '' }}>باحث عن عمل</option>
                                <option value="continuing_education" {{ request('employment_status') == 'continuing_education' ? 'selected' : '' }}>مستكمل للدراسة</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary-modern btn-sm me-2 w-50">
                                <i class="fas fa-search me-1"></i>بحث
                            </button>
                            <a href="{{ route('admin.career-guidance.graduates') }}" class="btn btn-secondary-modern btn-sm w-50">
                                <i class="fas fa-redo me-1"></i>إعادة تعيين
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- إحصائيات سريعة -->
    <div class="bento-grid mb-4">
        <div class="bento-card">
            <div class="bento-card-header">
                <h3 class="bento-card-title">إجمالي الخريجين</h3>
                <div class="bento-card-icon bento-icon-primary">
                    <i class="fas fa-user-graduate"></i>
                </div>
            </div>
            <div class="bento-stat">{{ $graduates->count() }}</div>
        </div>
        <div class="bento-card">
            <div class="bento-card-header">
                <h3 class="bento-card-title">خريجين موظفين</h3>
                <div class="bento-card-icon bento-icon-success">
                    <i class="fas fa-briefcase"></i>
                </div>
            </div>
            <div class="bento-stat">{{ $graduates->where('employment_status', 'employed')->count() }}</div>
        </div>
        <div class="bento-card">
            <div class="bento-card-header">
                <h3 class="bento-card-title">باحثين عن عمل</h3>
                <div class="bento-card-icon bento-icon-warning">
                    <i class="fas fa-search-dollar"></i>
                </div>
            </div>
            <div class="bento-stat">{{ $graduates->where('employment_status', 'seeking_opportunities')->count() }}</div>
        </div>
        <div class="bento-card">
            <div class="bento-card-header">
                <h3 class="bento-card-title">إجمالي الترشيحات</h3>
                <div class="bento-card-icon bento-icon-danger" style="background: rgba(15, 23, 42, 0.15); color: #0f172a;">
                    <i class="fas fa-star"></i>
                </div>
            </div>
            <div class="bento-stat">{{ $graduates->sum('nominations_count') }}</div>
        </div>
    </div>

    <!-- جدول الخريجين -->
    <div class="card-modern">
        <div class="card-header bg-white py-3 border-bottom">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="card-title mb-0 text-primary fw-bold fs-6">
                    <i class="fas fa-user-graduate me-2"></i>قائمة الخريجين المسجلين
                </h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.career-guidance.graduates.create') }}" class="btn btn-primary-modern btn-sm">
                        <i class="fas fa-user-plus me-1"></i>إضافة خريج
                    </a>
                    <button class="btn btn-sm btn-outline-success action-circle-btn px-3" style="width:auto;border-radius:8px;" data-bs-toggle="modal" data-bs-target="#importGraduatesModal">
                        <i class="fas fa-file-import me-1"></i>استيراد Excel
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            @if($graduates->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3 px-4 border-0 text-secondary small text-uppercase fw-bold" width="50">#</th>
                                <th class="py-3 border-0 text-secondary small text-uppercase fw-bold">الخريج</th>
                                <th class="py-3 border-0 text-secondary small text-uppercase fw-bold">التخصص والجامعة</th>
                                <th class="py-3 border-0 text-secondary small text-uppercase fw-bold text-center">سنة التخرج</th>
                                <th class="py-3 border-0 text-secondary small text-uppercase fw-bold text-center">المعدل</th>
                                <th class="py-3 border-0 text-secondary small text-uppercase fw-bold" width="160">حالة التوظيف</th>
                                <th class="py-3 border-0 text-secondary small text-uppercase fw-bold text-center" width="110">الترشيحات</th>
                                <th class="py-3 px-4 border-0 text-secondary small text-uppercase fw-bold text-end" width="140">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            @foreach($graduates as $graduate)
                            <tr>
                                <td class="px-4 text-muted">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-light text-primary rounded-circle d-flex align-items-center justify-content-center me-2 flex-shrink-0" style="width: 36px; height: 36px;">
                                            <span class="fw-bold">{{ mb_substr($graduate->name, 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $graduate->name }}</div>
                                            <small class="text-muted">{{ $graduate->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $graduate->major }}</div>
                                    @if($graduate->university)
                                        <small class="text-muted"><i class="fas fa-university me-1"></i>{{ $graduate->university }}</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold text-secondary">{{ $graduate->graduation_year }}</span>
                                </td>
                                <td class="text-center">
                                    @if($graduate->gpa)
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.75rem;">
                                            {{ $graduate->gpa }}%
                                        </span>
                                    @else
                                        <span class="text-muted small">--</span>
                                    @endif
                                </td>
                                <td>
                                    @if($graduate->employment_status == 'employed')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.75rem;">
                                            <i class="fas fa-briefcase me-1"></i>موظف
                                        </span>
                                    @elseif($graduate->employment_status == 'seeking_opportunities')
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.75rem;">
                                            <i class="fas fa-search me-1"></i>باحث عن عمل
                                        </span>
                                    @elseif($graduate->employment_status == 'continuing_education')
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.75rem;">
                                            <i class="fas fa-graduation-cap me-1"></i>مستكمل للدراسة
                                        </span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.75rem;">
                                            <i class="fas fa-times-circle me-1"></i>غير موظف
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.75rem;">
                                        {{ $graduate->nominations_count }}
                                    </span>
                                    @if($graduate->accepted_nominations_count > 0)
                                        <div class="mt-1">
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-2 py-1 fw-semibold" style="font-size: 0.7rem;">
                                                {{ $graduate->accepted_nominations_count }} مقبول
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 text-end">
                                    <div class="d-flex align-items-center justify-content-end gap-1">
                                        <a href="{{ route('admin.career-guidance.graduates.show', $graduate->id) }}"
                                           class="btn btn-sm btn-outline-primary action-circle-btn"
                                           data-bs-toggle="tooltip" title="عرض التفاصيل">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.career-guidance.graduates.edit', $graduate->id) }}"
                                           class="btn btn-sm btn-outline-secondary action-circle-btn"
                                           data-bs-toggle="tooltip" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#"
                                           class="btn btn-sm btn-success action-circle-btn"
                                           data-bs-toggle="tooltip" title="ترشيح لفرصة">
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
                    <div class="mb-3">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="fas fa-user-graduate fa-3x text-muted opacity-50"></i>
                        </div>
                    </div>
                    <h5 class="text-muted mb-3">لا توجد بيانات خريجين</h5>
                    <a href="{{ route('admin.career-guidance.graduates.create') }}" class="btn btn-primary-modern">
                        <i class="fas fa-plus me-2"></i>إضافة أول خريج
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal لاستيراد الخريجين -->
<div class="modal fade" id="importGraduatesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-file-import me-2"></i>
                    استيراد بيانات الخريجين من Excel
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.career-guidance.graduates.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                    @csrf
                    
                    <!-- تحميل الملف -->
                    <div class="mb-4">
                        <label for="excel_file" class="form-label fw-bold">رفع ملف Excel</label>
                        <input type="file" class="form-control @error('excel_file') is-invalid @enderror" 
                               id="excel_file" name="excel_file" accept=".xlsx,.xls,.csv" required>
                        @error('excel_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">الملفات المسموحة: .xlsx, .xls, .csv (الحجم الأقصى: 5MB)</div>
                    </div>

                  <!-- تعليمات التنسيق -->
<div class="alert alert-info">
    <h6 class="alert-heading">
        <i class="fas fa-info-circle me-2"></i>
        تعليمات تنسيق الملف
    </h6>
    <hr>
    <div class="row">
        <div class="col-md-6">
            <strong>الأعمدة المطلوبة:</strong>
            <ul class="mb-2 small">
                <li><code>name</code> - الاسم الكامل <span class="text-danger">*</span></li>
                <li><code>major</code> - التخصص <span class="text-danger">*</span></li>
                <li><code>graduation_year</code> - سنة التخرج <span class="text-danger">*</span></li>
                <li><code>employment_status</code> - حالة التوظيف <span class="text-danger">*</span></li>
            </ul>
        </div>
        <div class="col-md-6">
            <strong>الأعمدة الاختيارية:</strong>
            <ul class="mb-0 small">
                <li><code>email</code> - البريد الإلكتروني</li>
                <li><code>phone</code> - رقم الهاتف</li>
                <li><code>gpa</code> - المعدل التراكمي</li>
                <li><code>degree</code> - الدرجة العلمية</li>
                <li><code>skills</code> - المهارات (مفصولة بفاصلة)</li>
                <li><code>languages</code> - اللغات (مفصولة بفاصلة)</li>
                <li><code>work_experience</code> - الخبرات العملية</li>
                <li><code>address</code> - العنوان</li>
                <li><code>linkedin_url</code> - رابط LinkedIn</li>
            </ul>
        </div>
    </div>
    <div class="mt-2 p-2 bg-light rounded">
        <small class="text-muted">
            <i class="fas fa-lightbulb me-1"></i>
            <strong>ملاحظة:</strong> الحقول المطلوبة marked with <span class="text-danger">*</span> are mandatory
        </small>
    </div>
</div>

                    <!-- قيم حالة التوظيف -->
                    <div class="alert alert-warning">
                        <h6 class="alert-heading">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                    قيم حالة التوظيف المسموحة
                        </h6>
                        <div class="row small">
                            <div class="col-3">
                                <span class="badge bg-success">employed</span> - موظف
                            </div>
                            <div class="col-3">
                                <span class="badge bg-danger">unemployed</span> - غير موظف
                            </div>
                            <div class="col-3">
                                <span class="badge bg-warning">seeking_opportunities</span> - باحث عن عمل
                            </div>
                            <div class="col-3">
                                <span class="badge bg-info">continuing_education</span> - مستكمل للدراسة
                            </div>
                        </div>
                    </div>

                    <!-- معاينة الملف -->
<div class="mb-3">
    <label class="form-label fw-bold">معاينة البيانات:</label>
    <div class="table-responsive">
        <table class="table table-sm table-bordered">
            <thead class="table-light">
                <tr>
                    <th>الاسم</th>
                    <th>البريد</th>
                    <th>التخصص</th>
                    <th>سنة التخرج</th>
                    <th>المعدل</th>
                    <th>الحالة</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>أحمد محمد</td>
                    <td>ahmed@example.com</td>
                    <td>هندسة حاسوب</td>
                    <td>2023</td>
                    <td>3.75</td>
                    <td><span class="badge bg-warning">باحث عن عمل</span></td>
                </tr>
                <tr>
                    <td>فاطمة علي</td>
                    <td><em class="text-muted">(اختياري)</em></td>
                    <td>علوم حاسوب</td>
                    <td>2022</td>
                    <td><em class="text-muted">(اختياري)</em></td>
                    <td><span class="badge bg-success">موظف</span></td>
                </tr>
                <tr>
                    <td>محمد حسن</td>
                    <td>mohamed@example.com</td>
                    <td>هندسة كهربائية</td>
                    <td>2021</td>
                    <td><em class="text-muted">(اختياري)</em></td>
                    <td><span class="badge bg-danger">غير موظف</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

                    <!-- أزرار الإجراء -->
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('admin.career-guidance.graduates.template') }}" class="btn btn-outline-primary">
                            <i class="fas fa-download me-2"></i>
                            تحميل النموذج
                        </a>
                        <div>
                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i>
                                إلغاء
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-upload me-2"></i>
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

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
          return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
@endsection
