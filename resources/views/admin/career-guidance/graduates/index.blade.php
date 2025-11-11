@extends('layouts.app')

@section('title', 'إدارة الخريجين - مسؤول الإرشاد المهني')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header mb-4">
                <h1 class="page-title">
                    <i class="fas fa-users me-2"></i>
                    إدارة بيانات الخريجين
                </h1>
                <div class="page-subtitle">عرض وإدارة قائمة الخريجين المسجلين في النظام</div>
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
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.career-guidance.graduates') }}" method="GET">
                <div class="row">
                    <div class="col-md-3">
                        <label for="major" class="form-label">التخصص</label>
                        <select name="major" id="major" class="form-select">
                            <option value="">جميع التخصصات</option>
                            @foreach($majors as $major)
                                <option value="{{ $major }}" {{ request('major') == $major ? 'selected' : '' }}>
                                    {{ $major }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="graduation_year" class="form-label">سنة التخرج</label>
                        <select name="graduation_year" id="graduation_year" class="form-select">
                            <option value="">جميع السنوات</option>
                            @foreach($graduationYears as $year)
                                <option value="{{ $year }}" {{ request('graduation_year') == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="employment_status" class="form-label">حالة التوظيف</label>
                        <select name="employment_status" id="employment_status" class="form-select">
                            <option value="">جميع الحالات</option>
                            <option value="employed" {{ request('employment_status') == 'employed' ? 'selected' : '' }}>موظف</option>
                            <option value="unemployed" {{ request('employment_status') == 'unemployed' ? 'selected' : '' }}>غير موظف</option>
                            <option value="seeking_opportunities" {{ request('employment_status') == 'seeking_opportunities' ? 'selected' : '' }}>باحث عن عمل</option>
                            <option value="continuing_education" {{ request('employment_status') == 'continuing_education' ? 'selected' : '' }}>مستكمل للدراسة</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>بحث
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- إحصائيات سريعة -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h4>{{ $graduates->count() }}</h4>
                    <p class="mb-0">إجمالي الخريجين</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h4>{{ $graduates->where('employment_status', 'employed')->count() }}</h4>
                    <p class="mb-0">خريجين موظفين</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h4>{{ $graduates->where('employment_status', 'seeking_opportunities')->count() }}</h4>
                    <p class="mb-0">باحثين عن عمل</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h4>{{ $graduates->sum('nominations_count') }}</h4>
                    <p class="mb-0">إجمالي الترشيحات</p>
                </div>
            </div>
        </div>
    </div>

    <!-- جدول الخريجين -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">قائمة الخريجين</h5>
            <div>
                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#importGraduatesModal">
                    <i class="fas fa-file-import me-2"></i>استيراد من Excel
                </button>
                <a href="{{ route('admin.career-guidance.graduates.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-2"></i>إضافة خريج
                </a>
            </div>
        </div>
        <div class="card-body">
            @if($graduates->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>الاسم</th>
                                <th>التخصص</th>
                                <th>سنة التخرج</th>
                                <th>المعدل</th>
                                <th>حالة التوظيف</th>
                                <th>الترشيحات</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($graduates as $graduate)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $graduate->name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $graduate->email }}</small>
                                </td>
                                <td>{{ $graduate->major }}</td>
                                <td>{{ $graduate->graduation_year }}</td>
                                <td>
                                    @if($graduate->gpa)
                                        <span class="badge bg-info">{{ $graduate->gpa }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $graduate->employment_status == 'employed' ? 'success' : ($graduate->employment_status == 'seeking_opportunities' ? 'warning' : 'secondary') }}">
                                        {{ $graduate->employment_status == 'employed' ? 'موظف' : ($graduate->employment_status == 'seeking_opportunities' ? 'باحث عن عمل' : 'غير موظف') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $graduate->nominations_count }}</span>
                                    @if($graduate->accepted_nominations_count > 0)
                                        <span class="badge bg-success">{{ $graduate->accepted_nominations_count }} مقبول</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.career-guidance.graduates.show', $graduate->id) }}" 
                                           class="btn btn-info btn-sm" title="عرض التفاصيل">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.career-guidance.graduates.edit', $graduate->id) }}" class="btn btn-warning btn-sm" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#" class="btn btn-success btn-sm" title="ترشيح لفرصة">
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
                    <i class="fas fa-users fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">لا توجد بيانات خريجين</h4>
                    <p class="text-muted">يمكنك البدء بإضافة خريجين جدد أو استيرادهم من ملف Excel</p>
                    <a href="{{ route('admin.career-guidance.graduates.create') }}" class="btn btn-primary">
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
