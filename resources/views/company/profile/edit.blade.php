@extends('layouts.app')

@section('page-title', 'ملف الشركة التعريفي')

@section('content')
<div class="container-fluid py-4">
    <div class="row g-4">
        
        <!-- القسم الجانبي: حالة الشراكة والشعار -->
        <div class="col-lg-4">
            <div class="registration-card overflow-hidden mb-4">
                <div class="card-header text-center">
                    <div class="position-relative d-inline-block mb-3 mt-2" style="z-index: 2;">
                        @if($company->logo_path)
                            <img src="{{ Storage::url($company->logo_path) }}" alt="{{ $company->name }}" class="rounded-circle border border-4 border-white shadow" style="width: 120px; height: 120px; object-fit: cover; background: white;">
                        @else
                            <div class="rounded-circle border border-4 border-white shadow d-flex align-items-center justify-content-center bg-white text-primary" style="width: 120px; height: 120px; font-size: 3rem; font-weight: bold; margin: 0 auto;">
                                {{ mb_substr($company->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <h5 class="mb-0 fw-bold" style="position: relative; z-index: 2;">{{ $company->name }}</h5>
                    <p class="mb-0 text-white-50 small mt-1" style="position: relative; z-index: 2;"><i class="fas fa-building me-1"></i> {{ $company->industry ?? 'قطاع غير محدد' }}</p>
                </div>
                
                <div class="card-body bg-light">
                    <h6 class="fw-bold mb-3 text-secondary border-bottom pb-2">تفاصيل الشراكة مع الجامعة</h6>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted"><i class="fas fa-handshake me-2"></i> نوع الشراكة:</span>
                        <span class="badge bg-primary rounded-pill px-3 py-2">
                            @php
                                $types = [
                                    'employment' => 'توظيف',
                                    'training' => 'تدريب',
                                    'logistic_support' => 'دعم لوجستي',
                                    'academic' => 'أكاديمي',
                                    'training_employment' => 'تدريب وتوظيف'
                                ];
                            @endphp
                            {{ $types[$company->partnership_type] ?? 'غير محدد' }}
                        </span>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted"><i class="fas fa-shield-alt me-2"></i> حالة الشراكة:</span>
                        @if($company->partnership_status == 'active')
                            <span class="badge bg-success rounded-pill px-3 py-2"><i class="fas fa-check-circle me-1"></i> نشطة</span>
                        @elseif($company->partnership_status == 'expired')
                            <span class="badge bg-danger rounded-pill px-3 py-2"><i class="fas fa-times-circle me-1"></i> منتهية</span>
                        @else
                            <span class="badge bg-warning text-dark rounded-pill px-3 py-2"><i class="fas fa-clock me-1"></i> قيد المراجعة</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- القسم الرئيسي: تعديل البيانات -->
        <div class="col-lg-8">
            <x-bento-form title="تحديث بيانات الشركة" subtitle="قم بتحديث معلومات شركتك لتظهر بشكل احترافي للخريجين." icon="fa-edit">
                <form action="{{ route('company.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">اسم الشركة <span class="required">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $company->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="industry" class="form-label">مجال العمل <span class="required">*</span></label>
                            <input type="text" class="form-control @error('industry') is-invalid @enderror" id="industry" name="industry" value="{{ old('industry', $company->industry) }}" required>
                            @error('industry') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">البريد الإلكتروني للشركة</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $company->user->email ?? '') }}">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">رقم الهاتف <span class="required">*</span></label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $company->phone) }}" required>
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">عنوان المقر الرئيسي <span class="required">*</span></label>
                        <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', $company->address) }}" required>
                        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="website" class="form-label">الموقع الإلكتروني</label>
                        <input type="url" class="form-control @error('website') is-invalid @enderror" id="website" name="website" value="{{ old('website', $company->website) }}" placeholder="https://example.com">
                        @error('website') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">نبذة عن الشركة</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description', $company->description) }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="logo" class="form-label">تحديث الشعار (اختياري)</label>
                        <input type="file" class="form-control @error('logo') is-invalid @enderror" id="logo" name="logo" accept="image/png, image/jpeg, image/jpg">
                        <div class="form-text">يفضل أن يكون الشعار بخلفية شفافة (PNG). الحد الأقصى للحجم 2MB.</div>
                        @error('logo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="text-center mt-5">
                        <button type="submit" class="btn-register">
                            <i class="fas fa-save me-2"></i> حفظ التعديلات
                        </button>
                    </div>
                </form>
            </x-bento-form>
        </div>
    </div>
</div>
@endsection
