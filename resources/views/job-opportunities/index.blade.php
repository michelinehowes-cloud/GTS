@extends('layouts.app')

@section('title', 'إدارة فرص العمل والتدريب')

@section('content')
<div class="container-fluid py-4">
    <!-- رأس الصفحة -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-1 text-primary">
                            <i class="fas fa-briefcase me-2"></i>
                            فرص العمل والتدريب
                        </h1>
                        <p class="text-muted mb-0">إدارة وعرض جميع فرص العمل والتدريب المتاحة</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('job-opportunities.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-2"></i>
                            إضافة فرصة جديدة
                        </a>
                        <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#importModal">
                            <i class="fas fa-file-import me-2"></i>
                            استيراد
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- بطاقات الإحصائيات -->
<div class="row mb-4">
    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
        <div class="card stat-card border-start border-start-4 border-start-primary">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-title text-muted small fw-bold">إجمالي الفرص</h6>
                        <h3 class="card-value text-dark mb-0">{{ $opportunities->count() }}</h3>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="icon-circle bg-primary text-white">
                            <i class="fas fa-briefcase"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
        <div class="card stat-card border-start border-start-4 border-start-success">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-title text-muted small fw-bold">مفتوحة</h6>
                        <h3 class="card-value text-dark mb-0">{{ $opportunities->where('status', 'open')->count() }}</h3>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="icon-circle bg-success text-white">
                            <i class="fas fa-door-open"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
        <div class="card stat-card border-start border-start-4 border-start-info">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-title text-muted small fw-bold">وظائف</h6>
                        <h3 class="card-value text-dark mb-0">{{ $opportunities->where('type', 'job')->count() }}</h3>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="icon-circle bg-info text-white">
                            <i class="fas fa-user-tie"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
        <div class="card stat-card border-start border-start-4 border-start-warning">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-title text-muted small fw-bold">تدريبات</h6>
                        <h3 class="card-value text-dark mb-0">{{ $opportunities->where('type', 'training')->count() }}</h3>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="icon-circle bg-warning text-white">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
        <div class="card stat-card border-start border-start-4 border-start-secondary">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-title text-muted small fw-bold">تدريب عملي</h6>
                        <h3 class="card-value text-dark mb-0">{{ $opportunities->where('type', 'internship')->count() }}</h3>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="icon-circle bg-secondary text-white">
                            <i class="fas fa-laptop-code"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
        <div class="card stat-card border-start border-start-4 border-start-dark">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-title text-muted small fw-bold">الترشيحات</h6>
                        <h3 class="card-value text-dark mb-0">{{ $opportunities->sum('nominations_count') }}</h3>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="icon-circle bg-dark text-white">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- فلترة البيانات -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-filter me-2 text-primary"></i>
                        فلاتر البحث
                    </h6>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label for="type" class="form-label small fw-bold">نوع الفرصة</label>
                            <select name="type" id="type" class="form-select form-select-sm">
                                <option value="">جميع الأنواع</option>
                                <option value="job" {{ request('type') == 'job' ? 'selected' : '' }}>وظيفة</option>
                                <option value="training" {{ request('type') == 'training' ? 'selected' : '' }}>تدريب</option>
                                <option value="internship" {{ request('type') == 'internship' ? 'selected' : '' }}>تدريب عملي</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label small fw-bold">الحالة</label>
                            <select name="status" id="status" class="form-select form-select-sm">
                                <option value="">جميع الحالات</option>
                                <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>جديدة</option>
                                <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>مفتوحة</option>
                                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>مغلقة</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتملة</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="company_id" class="form-label small fw-bold">الشركة</label>
                            <select name="company_id" id="company_id" class="form-select form-select-sm">
                                <option value="">جميع الشركات</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary btn-sm me-2">
                                <i class="fas fa-search me-1"></i>بحث
                            </button>
                            <a href="{{ route('job-opportunities.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-redo me-1"></i>إعادة تعيين
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- جدول البيانات -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($opportunities->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">#</th>
                                        <th>الفرصة</th>
                                        <th>الشركة</th>
                                        <th width="120">النوع</th>
                                        <th width="120">المكان</th>
                                        <th width="150">التواريخ</th>
                                        <th width="80">المقاعد</th>
                                        <th width="80">الترشيحات</th>
                                        <th width="100">الحالة</th>
                                        <th width="120" class="text-center">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($opportunities as $opportunity)
                                    <tr>
                                        <td class="text-muted">{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="d-flex align-items-start">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1 text-dark">{{ $opportunity->title }}</h6>
                                                    <p class="text-muted small mb-0 line-clamp-2">
                                                        {{ Str::limit($opportunity->description, 60) }}
                                                    </p>
                                                    @if($opportunity->salary)
                                                        <small class="text-success">
                                                            <i class="fas fa-money-bill-wave me-1"></i>
                                                            {{ number_format($opportunity->salary) }} د.ل
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1">
                                                    <span class="fw-medium text-dark">{{ $opportunity->company->name }}</span>
                                                    <br>
                                                    <small class="text-muted">{{ $opportunity->company->industry }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $typeColors = [
                                                    'job' => 'success',
                                                    'training' => 'info', 
                                                    'internship' => 'warning'
                                                ];
                                                $typeLabels = [
                                                    'job' => 'وظيفة',
                                                    'training' => 'تدريب',
                                                    'internship' => 'تدريب عملي'
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $typeColors[$opportunity->type] }} rounded-pill">
                                                {{ $typeLabels[$opportunity->type] }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-dark">{{ $opportunity->location }}</span>
                                        </td>
                                        <td>
                                            <div class="small">
                                                <div class="text-muted">
                                                    <i class="fas fa-play-circle me-1 text-success"></i>
                                                    {{ $opportunity->start_date->format('Y-m-d') }}
                                                </div>
                                                <div class="text-muted">
                                                    <i class="fas fa-flag-checkered me-1 text-danger"></i>
                                                    {{ $opportunity->end_date->format('Y-m-d') }}
                                                </div>
                                                <div class="text-danger">
                                                    <i class="fas fa-clock me-1"></i>
                                                    {{ $opportunity->application_deadline->format('Y-m-d') }}
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary rounded-pill">{{ $opportunity->seats }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info rounded-pill">{{ $opportunity->nominations_count }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'new' => 'secondary',
                                                    'open' => 'success',
                                                    'closed' => 'danger',
                                                    'completed' => 'info'
                                                ];
                                                $statusLabels = [
                                                    'new' => 'جديدة',
                                                    'open' => 'مفتوحة',
                                                    'closed' => 'مغلقة',
                                                    'completed' => 'مكتملة'
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $statusColors[$opportunity->status] }}">
                                                {{ $statusLabels[$opportunity->status] }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-1">
                                                <a href="{{ route('job-opportunities.show', $opportunity->id) }}" 
                                                   class="btn btn-sm btn-outline-info" 
                                                   data-bs-toggle="tooltip" 
                                                   title="عرض التفاصيل">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('job-opportunities.edit', $opportunity->id) }}" 
                                                   class="btn btn-sm btn-outline-warning"
                                                   data-bs-toggle="tooltip" 
                                                   title="تعديل">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('job-opportunities.nominations', $opportunity->id) }}" 
                                                   class="btn btn-sm btn-outline-success"
                                                   data-bs-toggle="tooltip" 
                                                   title="الترشيحات">
                                                    <i class="fas fa-users"></i>
                                                </a>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                            type="button" 
                                                            data-bs-toggle="dropdown"
                                                            data-bs-toggle="tooltip" 
                                                            title="المزيد">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li>
                                                            <a class="dropdown-item" href="#" 
                                                               onclick="duplicateOpportunity({{ $opportunity->id }})">
                                                                <i class="fas fa-copy me-2"></i>نسخ الفرصة
                                                            </a>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <form action="{{ route('job-opportunities.destroy', $opportunity->id) }}" 
                                                                  method="POST" class="d-inline" 
                                                                  id="deleteForm{{ $opportunity->id }}">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="button" class="dropdown-item text-danger" 
                                                                        onclick="confirmDelete({{ $opportunity->id }})">
                                                                    <i class="fas fa-trash me-2"></i>حذف
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-briefcase fa-4x text-muted mb-3"></i>
                                <h4 class="text-muted">لا توجد فرص عمل أو تدريب</h4>
                                <p class="text-muted mb-4">يمكنك البدء بإضافة أول فرصة عمل أو تدريب</p>
                                <a href="{{ route('job-opportunities.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>إضافة أول فرصة
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal لاستيراد من Excel -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-file-import me-2 text-success"></i>
                    استيراد فرص عمل من ملف CSV
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('job-opportunities.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="company_id_import" class="form-label">الشركة <span class="text-danger">*</span></label>
                        <select name="company_id" id="company_id_import" class="form-select" required>
                            <option value="">اختر الشركة</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="excel_file" class="form-label">ملف CSV <span class="text-danger">*</span></label>
                        <input type="file" name="excel_file" id="excel_file" class="form-control" 
                               accept=".csv,.txt" required>
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            يجب أن يكون الملف بصيغة CSV ولا يتجاوز 5MB
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <h6><i class="fas fa-download me-2"></i>تحميل نموذج CSV</h6>
                        <p class="mb-2">يمكنك تحميل النموذج لمعرفة تنسيق البيانات المطلوب:</p>
                        <a href="#" class="btn btn-sm btn-outline-primary" onclick="downloadTemplate()">
                            <i class="fas fa-file-csv me-1"></i>تحميل النموذج
                        </a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success">استيراد البيانات</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.stat-card {
    border: none;
    border-radius: 12px;
    transition: all 0.3s ease;
    height: 100%;
    background: #fff;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.12);
}

.card-value {
    font-size: 1.8rem;
    font-weight: 700;
    color: #2d3748 !important;
}

.card-title {
    font-size: 0.85rem;
    font-weight: 600;
    color: #718096 !important;
    margin-bottom: 0.5rem;
}

.icon-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.border-start-primary { border-left-color: #3b82f6 !important; }
.border-start-success { border-left-color: #10b981 !important; }
.border-start-info { border-left-color: #06b6d4 !important; }
.border-start-warning { border-left-color: #f59e0b !important; }
.border-start-secondary { border-left-color: #6b7280 !important; }
.border-start-dark { border-left-color: #1f2937 !important; }

.bg-primary { background-color: #3b82f6 !important; }
.bg-success { background-color: #10b981 !important; }
.bg-info { background-color: #06b6d4 !important; }
.bg-warning { background-color: #f59e0b !important; }
.bg-secondary { background-color: #6b7280 !important; }
.bg-dark { background-color: #1f2937 !important; }

/* رأس الصفحة */
.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border-radius: 15px;
    margin-bottom: 2rem;
}

.page-header h1 {
    color: white;
    margin-bottom: 0.5rem;
}

.page-header .text-muted {
    color: rgba(255, 255, 255, 0.8) !important;
}

/* تحسينات إضافية للجدول */
.table > :not(caption) > * > * {
    padding: 1rem 0.75rem;
    vertical-align: middle;
    border-bottom: 1px solid #e2e8f0;
}

.table thead th {
    background-color: #f8fafc;
    color: #4a5568;
    font-weight: 600;
    border-bottom: 2px solid #e2e8f0;
}

.table tbody tr:hover {
    background-color: #f7fafc;
}

/* الأزرار */
.btn {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

/* البادجات */
.badge {
    font-size: 0.75em;
    padding: 0.5em 0.75em;
    font-weight: 500;
}

/* حالة عدم وجود بيانات */
.empty-state {
    padding: 3rem 1rem;
    text-align: center;
}

.empty-state i {
    opacity: 0.5;
}
</style>
@endsection

@section('scripts')
<script>
function confirmDelete(opportunityId) {
    Swal.fire({
        title: 'هل أنت متأكد؟',
        text: "لا يمكن التراجع عن هذا الإجراء!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'نعم، احذف!',
        cancelButtonText: 'إلغاء'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('deleteForm' + opportunityId).submit();
        }
    });
}

function duplicateOpportunity(opportunityId) {
    if (confirm('هل تريد نسخ هذه الفرصة؟')) {
        window.location.href = "{{ url('job-opportunities/duplicate') }}/" + opportunityId;
    }
}

function downloadTemplate() {
    const csvContent = "العنوان,الوصف,النوع,نوع العقد,الموقع,المقاعد,تاريخ البدء,تاريخ الانتهاء,آخر موعد,التخصصات المطلوبة,المهارات المطلوبة,الخبرة المطلوبة,الراتب,المزايا,المتطلبات\n" +
                      "مبرمج ويب,تطوير مواقع إلكترونية,وظيفة,دوام كامل,طرابلس,3,2024-02-01,2024-12-01,2024-01-20,هندسة برمجيات,تطوير ويب,PHP,Laravel,JavaScript,مبتدئ,1500,تأمين صحي,تدريب,شهادة في علوم الحاسب\n" +
                      "مدرب تقني,تدريب على التقنيات الحديثة,تدريب,دوام جزئي,بنغازي,10,2024-03-01,2024-03-15,2024-02-25,تدريب,تطوير,اتصال,عرض,2 سنوات,800,شهادة مشاركة,خبرة في المجال";
    
    const blob = new Blob(["\uFEFF" + csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'نموذج_فرص_العمل.csv';
    link.click();
}

// تفعيل tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
});
</script>
@endsection