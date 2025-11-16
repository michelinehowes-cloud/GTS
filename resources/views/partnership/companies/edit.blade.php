@extends('layouts.app')

@section('title', 'تعديل شركة - ' . $company->name)

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>تعديل شركة: {{ $company->name }}</h3>
                    <a href="{{ route('partnership.companies') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>رجوع للقائمة
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                    <div class="alert alert-danger">
                        <h6>يوجد أخطاء في البيانات:</h6>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('partnership.companies.update', $company->id) }}" method="POST" id="companyEditForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- المعلومات الأساسية -->
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-info-circle me-2"></i>المعلومات الأساسية
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">اسم الشركة <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name', $company->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                               id="email" name="email" value="{{ old('email', $company->email) }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                               id="phone" name="phone" value="{{ old('phone', $company->phone) }}" required>
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="industry" class="form-label">المجال الصناعي <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('industry') is-invalid @enderror" 
                                               id="industry" name="industry" value="{{ old('industry', $company->industry) }}" required>
                                        @error('industry')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="address" class="form-label">العنوان <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                           id="address" name="address" value="{{ old('address', $company->address) }}" required>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="website" class="form-label">الموقع الإلكتروني</label>
                                        <input type="url" class="form-control @error('website') is-invalid @enderror" 
                                               id="website" name="website" value="{{ old('website', $company->website) }}"
                                               placeholder="https://example.com">
                                        @error('website')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="description" class="form-label">وصف الشركة</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                                  id="description" name="description" rows="3"
                                                  placeholder="وصف مختصر عن نشاط الشركة...">{{ old('description', $company->description) }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- معلومات الشراكة -->
                        <div class="card mb-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-handshake me-2"></i>معلومات الشراكة
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="partnership_type" class="form-label">نوع الشراكة <span class="text-danger">*</span></label>
                                        <select name="partnership_type" id="partnership_type" class="form-select @error('partnership_type') is-invalid @enderror" required>
                                            <option value="">اختر نوع الشراكة</option>
                                            <option value="employment" {{ old('partnership_type', $company->partnership_type) == 'employment' ? 'selected' : '' }}>توظيف</option>
                                            <option value="training" {{ old('partnership_type', $company->partnership_type) == 'training' ? 'selected' : '' }}>تدريب</option>
                                            <option value="logistic_support" {{ old('partnership_type', $company->partnership_type) == 'logistic_support' ? 'selected' : '' }}>دعم لوجستي</option>
                                            <option value="academic" {{ old('partnership_type', $company->partnership_type) == 'academic' ? 'selected' : '' }}>أكاديمي</option>
                                            <option value="training_employment" {{ old('partnership_type', $company->partnership_type) == 'training_employment' ? 'selected' : '' }}>تدريب + توظيف</option>
                                        </select>
                                        @error('partnership_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="partnership_status" class="form-label">حالة الشراكة <span class="text-danger">*</span></label>
                                        <select name="partnership_status" id="partnership_status" class="form-select @error('partnership_status') is-invalid @enderror" required>
                                            <option value="under_review" {{ old('partnership_status', $company->partnership_status) == 'under_review' ? 'selected' : '' }}>قيد المراجعة</option>
                                            <option value="active" {{ old('partnership_status', $company->partnership_status) == 'active' ? 'selected' : '' }}>نشطة</option>
                                            <option value="expired" {{ old('partnership_status', $company->partnership_status) == 'expired' ? 'selected' : '' }}>منتهية</option>
                                        </select>
                                        @error('partnership_status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="partnership_start_date" class="form-label">تاريخ بداية الشراكة</label>
                                        <input type="date" name="partnership_start_date" id="partnership_start_date" 
                                               class="form-control" value="{{ old('partnership_start_date', $company->partnership_start_date ? $company->partnership_start_date->format('Y-m-d') : '') }}">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="partnership_end_date" class="form-label">تاريخ نهاية الشراكة</label>
                                        <input type="date" name="partnership_end_date" id="partnership_end_date" 
                                               class="form-control" value="{{ old('partnership_end_date', $company->partnership_end_date ? $company->partnership_end_date->format('Y-m-d') : '') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- معلومات الاتصال -->
                        <div class="card mb-4">
                            <div class="card-header bg-info text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-user-tie me-2"></i>معلومات شخص الاتصال
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="contact_person" class="form-label">اسم شخص الاتصال</label>
                                        <input type="text" class="form-control" 
                                               id="contact_person" name="contact_person" value="{{ old('contact_person', $company->contact_person) }}" 
                                               placeholder="اسم شخص الاتصال في الشركة">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="contact_position" class="form-label">المنصب</label>
                                        <input type="text" class="form-control" 
                                               id="contact_position" name="contact_position" value="{{ old('contact_position', $company->contact_position) }}" 
                                               placeholder="منصب شخص الاتصال">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="contact_phone" class="form-label">هاتف الاتصال</label>
                                        <input type="text" class="form-control" 
                                               id="contact_phone" name="contact_phone" value="{{ old('contact_phone', $company->contact_phone) }}" 
                                               placeholder="هاتف شخص الاتصال">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="contact_email" class="form-label">بريد الاتصال</label>
                                        <input type="email" class="form-control" 
                                               id="contact_email" name="contact_email" value="{{ old('contact_email', $company->contact_email) }}" 
                                               placeholder="بريد شخص الاتصال">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- أزرار الحفظ -->
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('partnership.companies') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>إلغاء
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-save me-2"></i>حفظ جميع التعديلات
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-radius: 10px;
}
.card-header {
    border-radius: 10px 10px 0 0 !important;
    font-weight: 600;
}
.form-label {
    font-weight: 600;
    margin-bottom: 0.5rem;
}
</style>
@endsection

@section('scripts')
<script>
// حل مبسط وفعال لمشكلة double submission
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('companyEditForm');
    const submitBtn = document.getElementById('submitBtn');
    let isSubmitting = false;

    form.addEventListener('submit', function(e) {
        console.log('🔄 حدث submit تم تشغيله');
        
        if (isSubmitting) {
            console.log('⛔ منع double submission');
            e.preventDefault();
            e.stopImmediatePropagation();
            return false;
        }
        
        console.log('✅ السماح بالإرسال الأول');
        isSubmitting = true;
        
        // تعطيل الزر وإظهار حالة التحميل
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جاري الحفظ...';
        
        // منع double click سريع
        setTimeout(() => {
            submitBtn.disabled = true;
        }, 100);
        
        return true;
    });

    // منع double click مباشر على الزر
    submitBtn.addEventListener('click', function(e) {
        if (isSubmitting) {
            e.preventDefault();
            e.stopPropagation();
            console.log('⛔ منع double click على الزر');
        }
    });

    // تنظيف حالة الإرسال عند مغادرة الصفحة
    window.addEventListener('beforeunload', function() {
        if (isSubmitting) {
            // إعادة تعيين حالة الإرسال إذا غادر المستخدم الصفحة
            sessionStorage.removeItem('formSubmitting');
        }
    });
});

// حل إضافي: منع إعادة إرسال النموذج عند تحديث الصفحة
if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
}
</script>
@endsection
