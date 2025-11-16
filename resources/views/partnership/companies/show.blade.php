@extends('layouts.app')

@section('title', 'تفاصيل الشركة - ' . $company->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>تفاصيل الشركة: {{ $company->name }}</h3>
                    <div class="d-flex gap-2">
                        <a href="{{ route('partnership.companies') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-right me-2"></i>رجوع للقائمة
                        </a>
                        <a href="{{ route('job-opportunities.create', ['company_id' => $company->id]) }}" 
                           class="btn btn-success">
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

                    <div class="row">
                        <!-- معلومات الأساسية -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-info-circle me-2"></i>معلومات الأساسية
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>اسم الشركة:</strong></div>
                                        <div class="col-sm-8">{{ $company->name }}</div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>البريد الإلكتروني:</strong></div>
                                        <div class="col-sm-8">{{ $company->email }}</div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>رقم الهاتف:</strong></div>
                                        <div class="col-sm-8">{{ $company->phone }}</div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>المجال الصناعي:</strong></div>
                                        <div class="col-sm-8">{{ $company->industry }}</div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>العنوان:</strong></div>
                                        <div class="col-sm-8">{{ $company->address }}</div>
                                    </div>
                                    @if($company->website)
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>الموقع الإلكتروني:</strong></div>
                                        <div class="col-sm-8">
                                            <a href="{{ $company->website }}" target="_blank" class="text-primary">
                                                {{ $company->website }}
                                            </a>
                                        </div>
                                    </div>
                                    @endif
                                    @if($company->description)
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>الوصف:</strong></div>
                                        <div class="col-sm-8">{{ $company->description }}</div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- معلومات الشراكة -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-success text-white">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-handshake me-2"></i>معلومات الشراكة
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('partnership.companies.update-partnership', $company->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="partnership_type" class="form-label">نوع الشراكة</label>
                                                <select name="partnership_type" id="partnership_type" class="form-select">
                                                    <option value="">اختر نوع الشراكة</option>
                                                    <option value="employment" {{ $company->partnership_type == 'employment' ? 'selected' : '' }}>توظيف</option>
                                                    <option value="training" {{ $company->partnership_type == 'training' ? 'selected' : '' }}>تدريب</option>
                                                    <option value="logistic_support" {{ $company->partnership_type == 'logistic_support' ? 'selected' : '' }}>دعم لوجستي</option>
                                                    <option value="academic" {{ $company->partnership_type == 'academic' ? 'selected' : '' }}>أكاديمي</option>
                                                    <option value="training_employment" {{ $company->partnership_type == 'training_employment' ? 'selected' : '' }}>تدريب + توظيف</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="partnership_status" class="form-label">حالة الشراكة</label>
                                                <select name="partnership_status" id="partnership_status" class="form-select">
                                                    <option value="under_review" {{ $company->partnership_status == 'under_review' ? 'selected' : '' }}>قيد المراجعة</option>
                                                    <option value="active" {{ $company->partnership_status == 'active' ? 'selected' : '' }}>نشطة</option>
                                                    <option value="expired" {{ $company->partnership_status == 'expired' ? 'selected' : '' }}>منتهية</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="partnership_start_date" class="form-label">تاريخ البدء</label>
                                                <input type="date" name="partnership_start_date" id="partnership_start_date" 
                                                       class="form-control" value="{{ $company->partnership_start_date }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="partnership_end_date" class="form-label">تاريخ الانتهاء</label>
                                                <input type="date" name="partnership_end_date" id="partnership_end_date" 
                                                       class="form-control" value="{{ $company->partnership_end_date }}">
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="contact_person" class="form-label">شخص الاتصال</label>
                                            <input type="text" name="contact_person" id="contact_person" 
                                                   class="form-control" value="{{ $company->contact_person }}" 
                                                   placeholder="اسم شخص الاتصال">
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="contact_position" class="form-label">المنصب</label>
                                                <input type="text" name="contact_position" id="contact_position" 
                                                       class="form-control" value="{{ $company->contact_position }}" 
                                                       placeholder="منصب شخص الاتصال">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="contact_phone" class="form-label">هاتف الاتصال</label>
                                                <input type="text" name="contact_phone" id="contact_phone" 
                                                       class="form-control" value="{{ $company->contact_phone }}" 
                                                       placeholder="هاتف شخص الاتصال">
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="contact_email" class="form-label">بريد الاتصال</label>
                                            <input type="email" name="contact_email" id="contact_email" 
                                                   class="form-control" value="{{ $company->contact_email }}" 
                                                   placeholder="بريد شخص الاتصال">
                                        </div>

                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-save me-2"></i>حفظ معلومات الشراكة
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- فرص العمل -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-briefcase me-2"></i>فرص العمل والتدريب
                                        <span class="badge bg-light text-dark ms-2">{{ $company->jobOpportunities->count() }}</span>
                                    </h5>
                                    <a href="{{ route('job-opportunities.create', ['company_id' => $company->id]) }}" 
                                       class="btn btn-light btn-sm">
                                        <i class="fas fa-plus me-1"></i>إضافة فرصة
                                    </a>
                                </div>
                                <div class="card-body">
                                    @if($company->jobOpportunities->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>العنوان</th>
                                                        <th>النوع</th>
                                                        <th>الحالة</th>
                                                        <th>المقاعد</th>
                                                        <th>آخر موعد</th>
                                                        <th>الإجراءات</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($company->jobOpportunities as $opportunity)
                                                    <tr>
                                                        <td>{{ $opportunity->title }}</td>
                                                        <td>
                                                            <span class="badge bg-{{ $opportunity->type == 'job' ? 'success' : ($opportunity->type == 'training' ? 'info' : 'warning') }}">
                                                                {{ $opportunity->type == 'job' ? 'وظيفة' : ($opportunity->type == 'training' ? 'تدريب' : 'تدريب عملي') }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-{{ $opportunity->status == 'open' ? 'success' : ($opportunity->status == 'closed' ? 'danger' : 'secondary') }}">
                                                                {{ $opportunity->status == 'open' ? 'مفتوحة' : ($opportunity->status == 'closed' ? 'مغلقة' : 'جديدة') }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $opportunity->seats }}</td>
                                                        <td>{{ $opportunity->application_deadline->format('Y-m-d') }}</td>
                                                        <td>
                                                            <a href="{{ route('job-opportunities.show', $opportunity->id) }}" 
                                                               class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-briefcase fa-2x text-muted mb-3"></i>
                                            <p class="text-muted">لا توجد فرص عمل أو تدريب لهذه الشركة</p>
                                            <a href="{{ route('job-opportunities.create', ['company_id' => $company->id]) }}" 
                                               class="btn btn-primary">
                                                <i class="fas fa-plus me-2"></i>إضافة أول فرصة
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
