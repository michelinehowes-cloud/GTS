@extends('layouts.app')

@section('title', 'إدارة الخريجين - مسؤول الإرشاد المهني')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4 p-4 rounded-4 shadow-sm border-start border-4 border-primary bg-white">
                <div>
                    <h1 class="h3 mb-2 text-primary font-weight-bold">
                        <i class="fas fa-users-graduate me-2"></i>إدارة بيانات الخريجين
                    </h1>
                    <p class="text-muted mb-0">عرض وإدارة قائمة الخريجين المسجلين في النظام</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.career-guidance.graduates.create') }}" class="btn btn-primary-modern">
                        <i class="fas fa-user-plus me-2"></i>إضافة خريج جديد
                    </a>
                    <button class="btn btn-outline-success-modern" data-bs-toggle="modal" data-bs-target="#importGraduatesModal">
                        <i class="fas fa-file-import me-2"></i>استيراد Excel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- رسائل التنبيه -->
    @if(session('import_errors'))
        <div class="alert alert-danger">
            <h6><i class="fas fa-exclamation-triangle me-2"></i>أخطاء في الاستيراد:</h6>
            <ul class="mb-0">
                @foreach(session('import_errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
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
    <div class="row">
        <div class="col-12">
            <div class="card-modern">
                <div class="card-body p-0">
                    @if($graduates->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light text-muted">
                                    <tr>
                                        <th class="py-3 px-4 border-0" width="50">#</th>
                                        <th class="py-3 border-0">الخريج</th>
                                        <th class="py-3 border-0">التخصص</th>
                                        <th class="py-3 border-0 text-center">سنة التخرج</th>
                                        <th class="py-3 border-0 text-center">المعدل</th>
                                        <th class="py-3 border-0 text-center">حالة التوظيف</th>
                                        <th class="py-3 border-0 text-center">الترشيحات</th>
                                        <th class="py-3 border-0 text-center" width="150">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($graduates as $graduate)
                                    <tr>
                                        <td class="px-4 text-muted fw-bold">{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-weight: bold;">
                                                    {{ mb_substr($graduate->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-bold text-dark">{{ $graduate->name }}</h6>
                                                    <small class="text-muted">{{ $graduate->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-light text-dark border">{{ $graduate->major }}</span></td>
                                        <td class="text-center fw-bold text-secondary">{{ $graduate->graduation_year }}</td>
                                        <td class="text-center">
                                            @if($graduate->gpa)
                                                <span class="badge" style="background: rgba(14, 165, 233, 0.15); color: #0ea5e9;">{{ $graduate->gpa }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($graduate->employment_status == 'employed')
                                                <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">موظف</span>
                                            @elseif($graduate->employment_status == 'seeking_opportunities')
                                                <span class="badge" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b;">باحث عن عمل</span>
                                            @elseif($graduate->employment_status == 'continuing_education')
                                                <span class="badge" style="background: rgba(59, 130, 246, 0.15); color: #3b82f6;">مستكمل للدراسة</span>
                                            @else
                                                <span class="badge" style="background: rgba(107, 114, 128, 0.15); color: #6b7280;">غير موظف</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex flex-column align-items-center gap-1">
                                                <span class="badge bg-primary rounded-pill">{{ $graduate->nominations_count }}</span>
                                                @if($graduate->accepted_nominations_count > 0)
                                                    <span class="badge bg-success rounded-pill" style="font-size: 0.7rem;">{{ $graduate->accepted_nominations_count }} مقبول</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="{{ route('admin.career-guidance.graduates.show', $graduate->id) }}" class="btn btn-sm btn-primary-modern rounded-circle" style="width: 32px; height: 32px; padding: 0; line-height: 32px;" title="عرض التفاصيل">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.career-guidance.graduates.edit', $graduate->id) }}" class="btn btn-sm btn-secondary-modern rounded-circle" style="width: 32px; height: 32px; padding: 0; line-height: 32px;" title="تعديل">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="#" class="btn btn-sm btn-success text-white rounded-circle" style="width: 32px; height: 32px; padding: 0; line-height: 32px; border-radius: 50% !important;" title="ترشيح لفرصة">
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
                                <i class="fas fa-users-slash text-muted" style="font-size: 4rem; opacity: 0.5;"></i>
                            </div>
                            <h5 class="text-muted fw-bold">لا توجد بيانات خريجين</h5>
                            <p class="text-muted mb-4">يمكنك البدء بإضافة خريجين جدد أو استيرادهم من ملف Excel</p>
                            <a href="{{ route('admin.career-guidance.graduates.create') }}" class="btn btn-primary-modern">
                                <i class="fas fa-plus me-2"></i>إضافة أول خريج
                            </a>
                        </div>
                    @endif
                </div>
            </div>
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
