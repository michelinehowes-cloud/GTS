@extends('layouts.app')

@section('page-title', 'ملف الشركة التعريفي')

@section('content')
<div class="container-fluid px-2 px-md-3 py-3">
    <!-- الشريط الترحيبي المعتمد بنمط المنصة الموحد -->
    <x-page-hero
        title="الملف التعريفي للشركة"
        subtitle="إدارة بيانات الشركة ومجال العمل، والشعار، وأمان الحساب وكلمة المرور"
        icon="fas fa-building"
        :breadcrumbs="[
            ['label' => 'لوحة تحكم الشركة', 'url' => route('company.dashboard')],
            ['label' => 'الملف التعريفي للشركة']
        ]"
    >
        <a href="{{ route('company.dashboard') }}" class="btn btn-light bg-white text-primary fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem; transition: transform 0.2s ease;">
            <i class="fas fa-home fs-6"></i>
            <span>لوحة التحكم</span>
        </a>
        <a href="{{ route('job-opportunities.index') }}" class="btn btn-warning text-dark fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem; transition: transform 0.2s ease;">
            <i class="fas fa-briefcase fs-6"></i>
            <span>إدارة الشواغر</span>
        </a>
    </x-page-hero>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center gap-2">
            <i class="fas fa-check-circle fs-5"></i>
            <div class="fw-bold">{{ session('success') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
            <div class="fw-bold mb-1"><i class="fas fa-exclamation-triangle me-1"></i> يرجى تصحيح الأخطاء التالية:</div>
            <ul class="mb-0 small">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4 mb-4">
        <!-- القسم الجانبي: حالة الشراكة والشعار وأمان الحساب -->
        <div class="col-lg-4">
            <!-- بطاقة الشعار وحالة الشراكة -->
            <div class="registration-card overflow-hidden mb-4 border-0 shadow-sm rounded-4">
                <div class="card-header text-center py-4" style="background: linear-gradient(135deg, #0d3882 0%, #1e3a8a 100%);">
                    <div class="position-relative d-inline-block mb-3 mt-2" style="z-index: 2;">
                        @if($company->logo_path)
                            <img src="{{ Storage::url($company->logo_path) }}" alt="{{ $company->name }}" class="rounded-circle border border-4 border-white shadow" style="width: 120px; height: 120px; object-fit: cover; background: white;">
                        @else
                            <div class="rounded-circle border border-4 border-white shadow d-flex align-items-center justify-content-center bg-white text-primary" style="width: 120px; height: 120px; font-size: 3rem; font-weight: bold; margin: 0 auto;">
                                {{ mb_substr($company->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <h5 class="mb-0 fw-bold text-white" style="position: relative; z-index: 2;">{{ $company->name }}</h5>
                    <p class="mb-0 text-white-50 small mt-1" style="position: relative; z-index: 2;"><i class="fas fa-building me-1"></i> {{ $company->industry ?? 'قطاع غير محدد' }}</p>
                </div>
                
                <div class="card-body bg-light p-4">
                    <h6 class="fw-bold mb-3 text-secondary border-bottom pb-2">تفاصيل الشراكة مع الجامعة</h6>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted"><i class="fas fa-handshake me-2 text-primary"></i> نوع الشراكة:</span>
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
                            {{ $types[$company->partnership_type] ?? 'شراكة معتمدة' }}
                        </span>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted"><i class="fas fa-shield-alt me-2 text-success"></i> حالة الاعتماد:</span>
                        @if($company->partnership_status == 'active')
                            <span class="badge bg-success rounded-pill px-3 py-2"><i class="fas fa-check-circle me-1"></i> نشطة ومعتمدة</span>
                        @elseif($company->partnership_status == 'expired')
                            <span class="badge bg-danger rounded-pill px-3 py-2"><i class="fas fa-times-circle me-1"></i> منتهية</span>
                        @else
                            <span class="badge bg-warning text-dark rounded-pill px-3 py-2"><i class="fas fa-clock me-1"></i> قيد المراجعة</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- بطاقة أمان الحساب وتغيير كلمة المرور -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <span class="badge rounded-circle p-2" style="background:#fef3c7;color:#d97706;"><i class="fas fa-lock"></i></span>
                        أمان الحساب وتغيير كلمة المرور
                    </h6>
                </div>
                <div class="card-body p-4 bg-white">
                    <form action="{{ route('company.password.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="current_password" class="form-label small fw-bold text-secondary">كلمة المرور الحالية <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-key text-muted"></i></span>
                                <input type="password" class="form-control border-start-0 ps-0 @error('current_password') is-invalid @enderror" 
                                    id="current_password" name="current_password" placeholder="أدخل كلمة المرور الحالية..." required>
                            </div>
                            @error('current_password')
                                <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="new_password" class="form-label small fw-bold text-secondary">كلمة المرور الجديدة <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock text-muted"></i></span>
                                <input type="password" class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror" 
                                    id="new_password" name="password" placeholder="8 خانات على الأقل..." required minlength="8">
                            </div>
                            @error('password')
                                <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="new_password_confirmation" class="form-label small fw-bold text-secondary">تأكيد كلمة المرور الجديدة <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-check-double text-muted"></i></span>
                                <input type="password" class="form-control border-start-0 ps-0" 
                                    id="new_password_confirmation" name="password_confirmation" placeholder="أعد إدخال كلمة المرور..." required minlength="8">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-warning text-dark w-100 rounded-pill py-2.5 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <i class="fas fa-shield-alt"></i>
                            <span>تحديث كلمة المرور</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- القسم الرئيسي: تعديل البيانات العامة للشركة -->
        <div class="col-lg-8">
            <x-bento-form title="تحديث بيانات الشركة" subtitle="قم بتحديث معلومات شركتك لتظهر بشكل احترافي للخريجين ومسؤولي التوظيف." icon="fa-building">
                <form action="{{ route('company.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label-modern">اسم الشركة <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-building text-muted"></i></span>
                                <input type="text" class="form-control-modern border-start-0 ps-0 @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $company->name) }}" required>
                            </div>
                            @error('name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="industry" class="form-label-modern">مجال العمل / الصناعة <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-industry text-muted"></i></span>
                                <input type="text" class="form-control-modern border-start-0 ps-0 @error('industry') is-invalid @enderror" id="industry" name="industry" value="{{ old('industry', $company->industry) }}" required>
                            </div>
                            @error('industry') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label-modern">البريد الإلكتروني للشركة</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                                <input type="email" class="form-control-modern border-start-0 ps-0 @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $company->user->email ?? $company->email) }}">
                            </div>
                            @error('email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label-modern">رقم الهاتف <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-phone text-muted"></i></span>
                                <input type="text" class="form-control-modern border-start-0 ps-0 @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $company->phone) }}" required>
                            </div>
                            @error('phone') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label-modern">عنوان المقر الرئيسي <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-map-marker-alt text-muted"></i></span>
                            <input type="text" class="form-control-modern border-start-0 ps-0 @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', $company->address) }}" required>
                        </div>
                        @error('address') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="website" class="form-label-modern">الموقع الإلكتروني</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-globe text-muted"></i></span>
                            <input type="url" class="form-control-modern border-start-0 ps-0 @error('website') is-invalid @enderror" id="website" name="website" value="{{ old('website', $company->website) }}" placeholder="https://example.com">
                        </div>
                        @error('website') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label-modern">نبذة تعريفية عن الشركة</label>
                        <textarea class="form-control-modern @error('description') is-invalid @enderror" id="description" name="description" rows="4" placeholder="اكتب نبذة مختصرة عن نشاط شركتكم وتاريخها...">{{ old('description', $company->description) }}</textarea>
                        @error('description') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="logo" class="form-label-modern">تحديث الشعار (اختياري)</label>
                        <input type="file" class="form-control-modern @error('logo') is-invalid @enderror" id="logo" name="logo" accept="image/png, image/jpeg, image/jpg">
                        <div class="form-text text-muted small mt-1">يفضل أن يكون الشعار بخلفية شفافة (PNG) أو بيضاء وبدقة عالية. الحد الأقصى للحجم 2MB.</div>
                        @error('logo') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="text-center mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-primary px-5 py-2.5 rounded-pill shadow-sm fw-bold">
                            <i class="fas fa-save me-2"></i> حفظ تعديلات الملف التعريفي
                        </button>
                    </div>
                </form>
            </x-bento-form>
        </div>
    </div>
</div>
@endsection
