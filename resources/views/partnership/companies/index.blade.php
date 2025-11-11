@extends('layouts.app')

@section('title', 'إدارة الشركات الشريكة - مسؤول الشراكات')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>الشركات الشريكة</h3>
                    <div class="d-flex gap-2 align-items-center">
                        <span class="badge bg-primary fs-6">
                            إجمالي الشركات: {{ $companies->count() }}
                        </span>
                        <span class="badge bg-success fs-6">
                            شراكات نشطة: {{ $companies->where('partnership_status', 'active')->count() }}
                        </span>
                        <a href="{{ route('partnership.companies.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>إضافة شركة جديدة
                        </a>
                        <a href="{{ route('partnership.reports') }}" class="btn btn-info">
                            <i class="fas fa-chart-bar me-2"></i>التقارير
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- فلترة الشركات -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">فلاتر البحث</h6>
                                    <form method="GET" class="row g-3">
                                        <div class="col-md-3">
                                            <label for="partnership_status" class="form-label">حالة الشراكة</label>
                                            <select name="partnership_status" id="partnership_status" class="form-select">
                                                <option value="">جميع الحالات</option>
                                                <option value="active" {{ request('partnership_status') == 'active' ? 'selected' : '' }}>نشطة</option>
                                                <option value="expired" {{ request('partnership_status') == 'expired' ? 'selected' : '' }}>منتهية</option>
                                                <option value="under_review" {{ request('partnership_status') == 'under_review' ? 'selected' : '' }}>قيد المراجعة</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="partnership_type" class="form-label">نوع الشراكة</label>
                                            <select name="partnership_type" id="partnership_type" class="form-select">
                                                <option value="">جميع الأنواع</option>
                                                <option value="employment" {{ request('partnership_type') == 'employment' ? 'selected' : '' }}>توظيف</option>
                                                <option value="training" {{ request('partnership_type') == 'training' ? 'selected' : '' }}>تدريب</option>
                                                <option value="logistic_support" {{ request('partnership_type') == 'logistic_support' ? 'selected' : '' }}>دعم لوجستي</option>
                                                <option value="academic" {{ request('partnership_type') == 'academic' ? 'selected' : '' }}>شراكة أكاديمية</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="search" class="form-label">بحث</label>
                                            <input type="text" name="search" id="search" class="form-control" 
                                                   placeholder="ابحث باسم الشركة..." value="{{ request('search') }}">
                                        </div>
                                        <div class="col-md-3 d-flex align-items-end">
                                            <button type="submit" class="btn btn-primary me-2">
                                                <i class="fas fa-search me-1"></i>بحث
                                            </button>
                                            <a href="{{ route('partnership.companies') }}" class="btn btn-secondary">
                                                <i class="fas fa-redo me-1"></i>إعادة تعيين
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($companies->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>الشركة</th>
                                        <th>معلومات الاتصال</th>
                                        <th>نوع الشراكة</th>
                                        <th>حالة الشراكة</th>
                                        <th>فرص العمل</th>
                                        <th>الوثائق</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($companies as $company)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <strong class="d-block">{{ $company->name }}</strong>
                                            <small class="text-muted">{{ $company->industry }}</small>
                                            @if($company->website)
                                                <br>
                                                <small>
                                                    <a href="{{ $company->website }}" target="_blank" class="text-decoration-none">
                                                        <i class="fas fa-globe me-1"></i>الموقع الإلكتروني
                                                    </a>
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($company->contact_person)
                                                <strong>{{ $company->contact_person }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $company->contact_position }}</small>
                                                <br>
                                                <small>
                                                    <i class="fas fa-phone me-1"></i>{{ $company->contact_phone }}
                                                </small>
                                                <br>
                                                <small>
                                                    <i class="fas fa-envelope me-1"></i>{{ $company->contact_email }}
                                                </small>
                                            @else
                                                <span class="text-muted">غير محدد</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($company->partnership_type)
                                                @php
                                                    $partnershipTypes = [
                                                        'employment' => ['label' => 'توظيف', 'color' => 'success'],
                                                        'training' => ['label' => 'تدريب', 'color' => 'info'],
                                                        'logistic_support' => ['label' => 'دعم لوجستي', 'color' => 'warning'],
                                                        'academic' => ['label' => 'أكاديمية', 'color' => 'primary']
                                                    ];
                                                    $type = $partnershipTypes[$company->partnership_type] ?? ['label' => $company->partnership_type, 'color' => 'secondary'];
                                                @endphp
                                                <span class="badge bg-{{ $type['color'] }}">{{ $type['label'] }}</span>
                                            @else
                                                <span class="badge bg-secondary">غير محدد</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($company->partnership_status)
                                                @php
                                                    $statusColors = [
                                                        'active' => 'success',
                                                        'expired' => 'danger',
                                                        'under_review' => 'warning'
                                                    ];
                                                @endphp
                                                <span class="badge bg-{{ $statusColors[$company->partnership_status] ?? 'secondary' }}">
                                                    {{ $company->partnership_status == 'active' ? 'نشطة' : 
                                                       ($company->partnership_status == 'expired' ? 'منتهية' : 'قيد المراجعة') }}
                                                </span>
                                                @if($company->partnership_start_date)
                                                    <br>
                                                    <small class="text-muted">
                                                        من: {{ $company->partnership_start_date->format('Y-m-d') }}
                                                    </small>
                                                @endif
                                                @if($company->partnership_end_date)
                                                    <br>
                                                    <small class="text-muted">
                                                        إلى: {{ $company->partnership_end_date->format('Y-m-d') }}
                                                    </small>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary">غير محدد</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $company->job_opportunities_count ?? 0 }}</span>
                                            فرصة
                                        </td>
                                        <td>
                                            <span class="badge bg-warning">{{ $company->partnership_documents_count ?? 0 }}</span>
                                            وثيقة
                                        <td>
    <div class="btn-group" role="group">
        {{-- زر العرض --}}
        <a href="{{ route('partnership.companies.show', $company->id) }}" 
           class="btn btn-info btn-sm" title="عرض التفاصيل">
            <i class="fas fa-eye"></i>
        </a>
        
        {{-- زر التعديل الشامل --}}
        <a href="{{ route('partnership.companies.edit', $company->id) }}" 
           class="btn btn-warning btn-sm" 
           title="تعديل جميع بيانات الشركة">
            <i class="fas fa-edit"></i>
        </a>
        
        {{-- زر فرص العمل --}}
        <a href="{{ route('job-opportunities.index', ['company_id' => $company->id]) }}" 
           class="btn btn-success btn-sm" title="فرص العمل">
            <i class="fas fa-briefcase"></i>
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
                            <i class="fas fa-building fa-4x text-muted mb-3"></i>
                            <h4 class="text-muted">لا توجد شركات شريكة</h4>
                            <p class="text-muted">سيتم عرض الشركات الشريكة هنا عند إضافتها</p>
                            <a href="{{ route('partnership.companies.create') }}" class="btn btn-primary mt-3">
                                <i class="fas fa-plus me-2"></i>إضافة أول شركة
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // تفعيل tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
@endsection
@section('scripts')
<script>
// منع الـ double events بشكل كامل
function handleEditClick(event, companyId) {
    console.log('🎯 بدء تعديل الشركة:', companyId);
    
    // منع جميع الأحداث الأخرى
    event.preventDefault();
    event.stopPropagation();
    event.stopImmediatePropagation();
    
    // تأكيد أن هذا هو الحدث الوحيد
    if (window.editInProgress) {
        console.log('⛔ تعديل قيد التقدم بالفعل');
        return false;
    }
    
    window.editInProgress = true;
    
    // الانتقال بعد تأخير بسيط
    setTimeout(() => {
        console.log('✅ الانتقال إلى صفحة التعديل');
        window.location.href = event.target.closest('a').href;
    }, 150);
    
    return false;
}

// تعطيل أي event listeners أخرى
document.addEventListener('DOMContentLoaded', function() {
    // إزالة جميع event listeners الحالية
    const editButtons = document.querySelectorAll('a[href*="edit"]');
    editButtons.forEach(button => {
        const newButton = button.cloneNode(true);
        button.parentNode.replaceChild(newButton, button);
    });
    
    // إضافة event listener واحدة فقط
    const newEditButtons = document.querySelectorAll('a[href*="edit"]');
    newEditButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const companyId = this.href.split('/').pop();
            handleEditClick(e, companyId);
        }, { once: true });
    });
});
</script>
@endsection