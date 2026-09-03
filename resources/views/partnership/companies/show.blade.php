@extends('layouts.app')

@section('title', 'تفاصيل الشركة: ' . $company->name . ' - مسؤول الشراكات')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>تفاصيل الشركة: {{ $company->name }}</h3>
                    <div class="d-flex gap-2">
                        <a href="{{ route('partnership.companies') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-right me-2"></i>رجوع للقائمة
                        </a>
                        <a href="{{ route('partnership.companies.edit', $company->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>تعديل الشركة
                        </a>
                        <a href="{{ route('job-opportunities.create', ['company_id' => $company->id]) }}" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i>إضافة فرصة عمل
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

                    <!-- البيانات الأساسية -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">اسم الشركة</label>
                            <div class="fs-6 fw-bold text-dark p-2 bg-light rounded border">{{ $company->name }}</div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">البريد الإلكتروني</label>
                            <div class="fs-6 fw-bold text-dark p-2 bg-light rounded border">
                                <a href="mailto:{{ $company->email }}" class="text-decoration-none">
                                    <i class="fas fa-envelope me-1 text-primary"></i>{{ $company->email }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">رقم الهاتف</label>
                            <div class="fs-6 fw-bold text-dark p-2 bg-light rounded border">
                                <a href="tel:{{ $company->phone }}" class="text-decoration-none text-dark">
                                    <i class="fas fa-phone me-1 text-success"></i>{{ $company->phone }}
                                </a>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">المجال الصناعي</label>
                            <div class="fs-6 fw-bold text-dark p-2 bg-light rounded border">{{ $company->industry }}</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small">العنوان</label>
                        <div class="fs-6 fw-bold text-dark p-2 bg-light rounded border">
                            <i class="fas fa-map-marker-alt me-1 text-danger"></i>{{ $company->address }}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">الموقع الإلكتروني</label>
                            <div class="fs-6 fw-bold text-dark p-2 bg-light rounded border">
                                @if($company->website)
                                    <a href="{{ $company->website }}" target="_blank" class="text-primary text-decoration-none">
                                        <i class="fas fa-globe me-1"></i>{{ $company->website }}
                                    </a>
                                @else
                                    <span class="text-muted fw-normal">غير متوفر</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">نوع الشراكة</label>
                            <div class="fs-6 fw-bold p-2 bg-light rounded border">
                                @switch($company->partnership_type)
                                    @case('employment') <span class="badge bg-primary">توظيف</span> @break
                                    @case('training') <span class="badge bg-info text-dark">تدريب</span> @break
                                    @case('logistic_support') <span class="badge bg-warning text-dark">دعم لوجستي</span> @break
                                    @case('academic') <span class="badge bg-secondary">أكاديمي</span> @break
                                    @case('training_employment') <span class="badge bg-success">تدريب + توظيف</span> @break
                                    @default <span class="badge bg-light text-dark border">{{ $company->partnership_type ?? 'غير محدد' }}</span>
                                @endswitch
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">حالة الشراكة</label>
                            <div class="fs-6 fw-bold p-2 bg-light rounded border">
                                @if($company->partnership_status == 'active')
                                    <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>نشطة</span>
                                @elseif($company->partnership_status == 'expired')
                                    <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>منتهية</span>
                                @else
                                    <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>قيد المراجعة</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label text-muted small">تاريخ البدء</label>
                            <div class="fs-6 fw-bold text-dark p-2 bg-light rounded border">
                                {{ $company->partnership_start_date ? \Carbon\Carbon::parse($company->partnership_start_date)->format('Y-m-d') : 'غير محدد' }}
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label text-muted small">تاريخ الانتهاء</label>
                            <div class="fs-6 fw-bold text-dark p-2 bg-light rounded border">
                                {{ $company->partnership_end_date ? \Carbon\Carbon::parse($company->partnership_end_date)->format('Y-m-d') : 'غير محدد' }}
                            </div>
                        </div>
                    </div>

                    @if($company->description)
                    <div class="mb-3">
                        <label class="form-label text-muted small">وصف الشركة</label>
                        <div class="p-3 bg-light rounded border text-secondary" style="line-height: 1.7;">
                            {{ $company->description }}
                        </div>
                    </div>
                    @endif

                    <!-- معلومات شخص الاتصال -->
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-user-tie me-2"></i>معلومات شخص الاتصال</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small">اسم شخص الاتصال</label>
                                    <div class="fw-bold text-dark">{{ $company->contact_person ?? 'غير محدد' }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small">المنصب</label>
                                    <div class="fw-bold text-dark">{{ $company->contact_position ?? 'غير محدد' }}</div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small">هاتف الاتصال</label>
                                    <div class="fw-bold text-dark">
                                        @if($company->contact_phone)
                                            <a href="tel:{{ $company->contact_phone }}" class="text-decoration-none text-dark">
                                                <i class="fas fa-phone me-1 text-success"></i>{{ $company->contact_phone }}
                                            </a>
                                        @else
                                            غير محدد
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small">بريد الاتصال</label>
                                    <div class="fw-bold text-dark">
                                        @if($company->contact_email)
                                            <a href="mailto:{{ $company->contact_email }}" class="text-decoration-none">
                                                <i class="fas fa-envelope me-1 text-primary"></i>{{ $company->contact_email }}
                                            </a>
                                        @else
                                            غير محدد
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- فرص العمل والتدريب التابعة للشركة -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center bg-light">
                            <h6 class="mb-0 fw-bold text-primary">
                                <i class="fas fa-briefcase me-2"></i>فرص العمل والتدريب المطروحة
                                <span class="badge bg-primary ms-1">{{ $company->jobOpportunities->count() }}</span>
                            </h6>
                            <a href="{{ route('job-opportunities.create', ['company_id' => $company->id]) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-plus me-1"></i>إضافة فرصة
                            </a>
                        </div>
                        <div class="card-body p-0">
                            @if($company->jobOpportunities->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="ps-3 py-2 text-secondary small fw-bold">المسمى الوظيفي</th>
                                                <th class="py-2 text-secondary small fw-bold">النوع</th>
                                                <th class="py-2 text-secondary small fw-bold">الحالة</th>
                                                <th class="py-2 text-secondary small fw-bold text-center">المقاعد</th>
                                                <th class="py-2 text-secondary small fw-bold">الموعد النهائي</th>
                                                <th class="pe-3 py-2 text-secondary small fw-bold text-center">الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($company->jobOpportunities as $opportunity)
                                            <tr>
                                                <td class="ps-3 fw-bold text-dark">{{ $opportunity->title }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $opportunity->type == 'job' ? 'success' : ($opportunity->type == 'training' ? 'info text-dark' : 'warning text-dark') }}">
                                                        {{ $opportunity->type == 'job' ? 'وظيفة' : ($opportunity->type == 'training' ? 'تدريب' : 'تدريب عملي') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $opportunity->status == 'open' ? 'success' : ($opportunity->status == 'closed' ? 'danger' : 'secondary') }}">
                                                        {{ $opportunity->status == 'open' ? 'مفتوحة' : ($opportunity->status == 'closed' ? 'مغلقة' : 'جديدة') }}
                                                    </span>
                                                </td>
                                                <td class="text-center fw-bold">{{ $opportunity->seats }}</td>
                                                <td>{{ $opportunity->application_deadline ? $opportunity->application_deadline->format('Y-m-d') : 'غير محدد' }}</td>
                                                <td class="pe-3 text-center">
                                                    <a href="{{ route('job-opportunities.show', $opportunity->id) }}" class="btn btn-sm btn-outline-info rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="عرض التفاصيل">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4 text-muted">
                                    <i class="fas fa-briefcase fa-2x mb-2 opacity-50"></i>
                                    <p class="mb-0 small">لا توجد فرص عمل مسجلة لهذه الشركة حالياً</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- أزرار الإجراءات السفلية -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <a href="{{ route('partnership.companies') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-right me-2"></i>رجوع للقائمة
                        </a>
                        <a href="{{ route('partnership.companies.edit', $company->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>تعديل بيانات الشركة
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.form-label {
    font-weight: 600;
    margin-bottom: 0.5rem;
}
.card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-radius: 10px;
}
.card-header {
    border-radius: 10px 10px 0 0 !important;
}
.btn {
    border-radius: 6px;
    font-weight: 500;
}
</style>
@endsection
