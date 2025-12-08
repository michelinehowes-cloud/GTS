@extends('layouts.app')

@section('title', 'إدارة فرص العمل والتدريب')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumbs -->
    @include('components.breadcrumbs', [
        'items' => [
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'إدارة فرص العمل والتدريب', 'active' => true],
        ]
    ])

    <!-- رأس الصفحة -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center bg-white p-4 rounded-3 shadow-sm border-start border-4 border-primary">
                <div>
                    <h1 class="h3 mb-2 text-primary font-weight-bold">
                        <i class="fas fa-briefcase me-2"></i>فرص العمل والتدريب
                    </h1>
                    <p class="text-muted mb-0">إدارة وعرض جميع فرص العمل والتدريب المتاحة</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('job-opportunities.create') }}" class="btn btn-primary-modern">
                        <i class="fas fa-plus-circle me-2"></i>إضافة فرصة جديدة
                    </a>
                    <button class="btn btn-outline-success-modern" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="fas fa-file-import me-2"></i>استيراد
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- بطاقات الإحصائيات -->
    <div class="row mb-4">
        @include('components.stat-card', [
            'col' => 'col-xl-2 col-md-4 col-sm-6 mb-3',
            'title' => 'إجمالي الفرص',
            'value' => $opportunities->count(),
            'icon' => 'fas fa-briefcase',
            'color' => 'primary'
        ])
        
        @include('components.stat-card', [
            'col' => 'col-xl-2 col-md-4 col-sm-6 mb-3',
            'title' => 'مفتوحة',
            'value' => $opportunities->where('status', 'open')->count(),
            'icon' => 'fas fa-door-open',
            'color' => 'success'
        ])
        
        @include('components.stat-card', [
            'col' => 'col-xl-2 col-md-4 col-sm-6 mb-3',
            'title' => 'وظائف',
            'value' => $opportunities->where('type', 'job')->count(),
            'icon' => 'fas fa-user-tie',
            'color' => 'info'
        ])
        
        @include('components.stat-card', [
            'col' => 'col-xl-2 col-md-4 col-sm-6 mb-3',
            'title' => 'تدريبات',
            'value' => $opportunities->where('type', 'training')->count(),
            'icon' => 'fas fa-graduation-cap',
            'color' => 'warning'
        ])
        
        @include('components.stat-card', [
            'col' => 'col-xl-2 col-md-4 col-sm-6 mb-3',
            'title' => 'تدريب عملي',
            'value' => $opportunities->where('type', 'internship')->count(),
            'icon' => 'fas fa-laptop-code',
            'color' => 'secondary'
        ])
        
        @include('components.stat-card', [
            'col' => 'col-xl-2 col-md-4 col-sm-6 mb-3',
            'title' => 'الترشيحات',
            'value' => $opportunities->sum('nominations_count'),
            'icon' => 'fas fa-users',
            'color' => 'dark'
        ])
    </div>

    <!-- فلترة البيانات -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card-modern">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 text-primary font-weight-bold">
                        <i class="fas fa-filter me-2"></i>فلاتر البحث
                    </h6>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label for="type" class="form-label-modern small fw-bold">نوع الفرصة</label>
                            <select name="type" id="type" class="form-select-modern form-select-sm">
                                <option value="">جميع الأنواع</option>
                                <option value="job" {{ request('type') == 'job' ? 'selected' : '' }}>وظيفة</option>
                                <option value="training" {{ request('type') == 'training' ? 'selected' : '' }}>تدريب</option>
                                <option value="internship" {{ request('type') == 'internship' ? 'selected' : '' }}>تدريب عملي</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label-modern small fw-bold">الحالة</label>
                            <select name="status" id="status" class="form-select-modern form-select-sm">
                                <option value="">جميع الحالات</option>
                                <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>جديدة</option>
                                <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>مفتوحة</option>
                                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>مغلقة</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتملة</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="company_id" class="form-label-modern small fw-bold">الشركة</label>
                            <select name="company_id" id="company_id" class="form-select-modern form-select-sm">
                                <option value="">جميع الشركات</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary-modern btn-sm me-2 w-50">
                                <i class="fas fa-search me-1"></i>بحث
                            </button>
                            <a href="{{ route('job-opportunities.index') }}" class="btn btn-secondary-modern btn-sm w-50">
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
            <div class="card-modern">
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
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light text-muted">
                                    <tr>
                                        <th class="py-3 px-4 border-0" width="50">#</th>
                                        <th class="py-3 border-0">الفرصة</th>
                                        <th class="py-3 border-0">الشركة</th>
                                        <th class="py-3 border-0" width="120">النوع</th>
                                        <th class="py-3 border-0" width="120">المكان</th>
                                        <th class="py-3 border-0" width="150">التواريخ</th>
                                        <th class="py-3 border-0 text-center" width="80">المقاعد</th>
                                        <th class="py-3 border-0 text-center" width="80">الترشيحات</th>
                                        <th class="py-3 border-0" width="100">الحالة</th>
                                        <th class="py-3 border-0 text-center" width="150">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($opportunities as $opportunity)
                                    <tr>
                                        <td class="px-4 text-muted fw-bold">{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="d-flex align-items-start">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1 fw-bold text-dark">{{ $opportunity->title }}</h6>
                                                    <p class="text-muted small mb-0 text-truncate" style="max-width: 250px;">
                                                        {{ Str::limit($opportunity->description, 60) }}
                                                    </p>
                                                    @if($opportunity->salary)
                                                        <small class="text-success fw-bold">
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
                                                    <span class="fw-bold text-dark">{{ $opportunity->company->name }}</span>
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
                                            <span class="badge bg-{{ $typeColors[$opportunity->type] }}-subtle text-{{ $typeColors[$opportunity->type] }} px-3 py-2 rounded-pill">
                                                {{ $typeLabels[$opportunity->type] }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-dark"><i class="fas fa-map-marker-alt text-muted me-1"></i>{{ $opportunity->location }}</span>
                                        </td>
                                        <td>
                                            <div class="small">
                                                <div class="mb-1 text-muted">
                                                    <i class="fas fa-play-circle me-1 text-success"></i>
                                                    {{ $opportunity->start_date->format('Y-m-d') }}
                                                </div>
                                                <div class="mb-1 text-muted">
                                                    <i class="fas fa-flag-checkered me-1 text-danger"></i>
                                                    {{ $opportunity->end_date->format('Y-m-d') }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark border rounded-pill px-3">{{ $opportunity->seats }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info-subtle text-info rounded-pill px-3">{{ $opportunity->nominations_count }}</span>
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
                                            <span class="badge bg-{{ $statusColors[$opportunity->status] }} px-3 py-2 rounded-pill">
                                                {{ $statusLabels[$opportunity->status] }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <a href="{{ route('job-opportunities.show', $opportunity->id) }}" 
                                                   class="btn btn-sm btn-icon btn-outline-info-modern" 
                                                   data-bs-toggle="tooltip" 
                                                   title="عرض التفاصيل">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                 <a href="{{ route('job-opportunities.nominations', $opportunity->id) }}"
                                                   class="btn btn-sm btn-icon btn-outline-success-modern"
                                                   data-bs-toggle="tooltip"
                                                   title="الترشيحات">
                                                    <i class="fas fa-users"></i>
                                                </a>
                                                <a href="{{ route('job-opportunities.edit', $opportunity->id) }}" 
                                                   class="btn btn-sm btn-icon btn-outline-warning-modern"
                                                   data-bs-toggle="tooltip" 
                                                   title="تعديل">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-icon btn-outline-secondary-modern" 
                                                        data-bs-toggle="dropdown" aria-expanded="false" title="المزيد">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                                    <li><a class="dropdown-item" href="{{ route('partnership.nominations') }}"><i class="fas fa-list me-2 text-info"></i>جميع الترشيحات</a></li>
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
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="empty-state">
                                <div class="mb-4">
                                    <span class="fa-stack fa-2x">
                                        <i class="fas fa-circle fa-stack-2x text-light"></i>
                                        <i class="fas fa-briefcase fa-stack-1x text-muted"></i>
                                    </span>
                                </div>
                                <h4 class="text-muted fw-bold">لا توجد فرص عمل أو تدريب</h4>
                                <p class="text-muted mb-4">يمكنك البدء بإضافة أول فرصة عمل أو تدريب</p>
                                <a href="{{ route('job-opportunities.create') }}" class="btn btn-primary-modern px-4">
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
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-file-import me-2"></i>
                    استيراد فرص عمل من ملف CSV
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('job-opportunities.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="company_id_import" class="form-label-modern">الشركة <span class="text-danger">*</span></label>
                        <select name="company_id" id="company_id_import" class="form-select-modern" required>
                            <option value="">اختر الشركة</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="excel_file" class="form-label-modern">ملف CSV <span class="text-danger">*</span></label>
                        <input type="file" name="excel_file" id="excel_file" class="form-control-modern" 
                               accept=".csv,.txt" required>
                        <div class="form-text text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            يجب أن يكون الملف بصيغة CSV ولا يتجاوز 5MB
                        </div>
                    </div>
                    <div class="alert alert-info border-0 bg-info-subtle text-info-emphasis">
                        <h6 class="fw-bold"><i class="fas fa-download me-2"></i>تحميل نموذج CSV</h6>
                        <p class="mb-2 small">يمكنك تحميل النموذج لمعرفة تنسيق البيانات المطلوب:</p>
                        <a href="#" class="btn btn-sm btn-outline-info-modern" onclick="downloadTemplate()">
                            <i class="fas fa-file-csv me-1"></i>تحميل النموذج
                        </a>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary-modern" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success-modern">
                        <i class="fas fa-check me-2"></i>استيراد البيانات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
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