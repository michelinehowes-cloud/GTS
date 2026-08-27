@extends('layouts.app')

@section('title', 'إضافة شركة جديدة')

@section('page-title', 'إضافة شركة جديدة')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumbs -->
    @include('components.breadcrumbs', [
        'items' => [
            ['label' => 'الرئيسية', 'url' => route('admin.dashboard')],
            ['label' => 'الشركات', 'url' => route('admin.companies')],
            ['label' => 'إضافة شركة', 'active' => true],
        ]
    ])

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card-modern">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 text-primary fw-bold">
                        <i class="fas fa-building me-2"></i> بيانات الشركة الجديدة
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if($errors->any())
                    <div class="alert alert-danger border-0 bg-danger-subtle text-danger-emphasis mb-4">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('admin.companies.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row g-3">
                            <!-- اسم الشركة -->
                            <div class="col-md-6">
                                <label for="name" class="form-label-modern">اسم الشركة <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-building text-muted"></i></span>
                                    <input type="text" class="form-control-modern border-start-0 ps-0 @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required placeholder="اسم الشركة الرسمي">
                                </div>
                                @error('name')
                                    <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- شعار الشركة -->
                            <div class="col-md-6">
                                <label for="logo" class="form-label-modern">شعار الشركة (اختياري)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-image text-muted"></i></span>
                                    <input type="file" class="form-control-modern border-start-0 ps-0 @error('logo') is-invalid @enderror" 
                                           id="logo" name="logo" accept="image/*">
                                </div>
                                <div class="form-text mt-1">يُفضل أن تكون الصورة بخلفية بيضاء أو شفافة (PNG/JPG).</div>
                                @error('logo')
                                    <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- البريد الإلكتروني -->
                            <div class="col-md-6">
                                <label for="email" class="form-label-modern">البريد الإلكتروني <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                                    <input type="email" class="form-control-modern border-start-0 ps-0 @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email') }}" required placeholder="البريد الرسمي للتواصل">
                                </div>
                                @error('email')
                                    <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- رقم الهاتف -->
                            <div class="col-md-6">
                                <label for="phone" class="form-label-modern">رقم الهاتف <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-phone text-muted"></i></span>
                                    <input type="text" class="form-control-modern border-start-0 ps-0 @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone') }}" required placeholder="رقم هاتف الشركة">
                                </div>
                                @error('phone')
                                    <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- المجال الصناعي -->
                            <div class="col-md-6">
                                <label for="industry" class="form-label-modern">المجال الصناعي <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-industry text-muted"></i></span>
                                    <input type="text" class="form-control-modern border-start-0 ps-0 @error('industry') is-invalid @enderror" 
                                           id="industry" name="industry" value="{{ old('industry') }}" required 
                                           placeholder="مثل: تكنولوجيا، تسويق، تعليم...">
                                </div>
                                @error('industry')
                                    <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- العنوان -->
                            <div class="col-12">
                                <label for="address" class="form-label-modern">العنوان <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-map-marker-alt text-muted"></i></span>
                                    <input type="text" class="form-control-modern border-start-0 ps-0 @error('address') is-invalid @enderror" 
                                           id="address" name="address" value="{{ old('address') }}" required placeholder="عنوان المقر الرئيسي">
                                </div>
                                @error('address')
                                    <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- كلمة المرور -->
                            <div class="col-md-6">
                                <label for="password" class="form-label-modern">كلمة المرور <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock text-muted"></i></span>
                                    <input type="password" class="form-control-modern border-start-0 ps-0 @error('password') is-invalid @enderror" 
                                           id="password" name="password" required minlength="8">
                                </div>
                                <small class="text-muted"><i class="fas fa-info-circle me-1"></i> 8 أحرف على الأقل</small>
                                @error('password')
                                    <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- تأكيد كلمة المرور -->
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label-modern">تأكيد كلمة المرور <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-check-double text-muted"></i></span>
                                    <input type="password" class="form-control-modern border-start-0 ps-0" 
                                           id="password_confirmation" name="password_confirmation" required>
                                </div>
                            </div>

                            <!-- الوصف -->
                            <div class="col-12">
                                <label for="description" class="form-label-modern">وصف الشركة</label>
                                <textarea class="form-control-modern @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="4" 
                                          placeholder="نبذة مختصرة عن نشاط الشركة وخدماتها...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr class="mt-4">
                            <h6 class="fw-bold text-primary mb-3">تفاصيل الشراكة</h6>
                            
                            <!-- نوع الشراكة -->
                            <div class="col-md-6">
                                <label for="partnership_type" class="form-label-modern">نوع الشراكة <span class="text-danger">*</span></label>
                                <select class="form-select @error('partnership_type') is-invalid @enderror" 
                                        id="partnership_type" name="partnership_type" required>
                                    <option value="">اختر نوع الشراكة...</option>
                                    <option value="employment" {{ old('partnership_type') == 'employment' ? 'selected' : '' }}>توظيف</option>
                                    <option value="training" {{ old('partnership_type') == 'training' ? 'selected' : '' }}>تدريب</option>
                                    <option value="logistic_support" {{ old('partnership_type') == 'logistic_support' ? 'selected' : '' }}>دعم لوجستي</option>
                                    <option value="academic" {{ old('partnership_type') == 'academic' ? 'selected' : '' }}>أكاديمي</option>
                                    <option value="training_employment" {{ old('partnership_type') == 'training_employment' ? 'selected' : '' }}>تدريب وتوظيف</option>
                                </select>
                                @error('partnership_type')
                                    <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- حالة الشراكة -->
                            <div class="col-md-6">
                                <label for="partnership_status" class="form-label-modern">حالة الشراكة <span class="text-danger">*</span></label>
                                <select class="form-select @error('partnership_status') is-invalid @enderror" 
                                        id="partnership_status" name="partnership_status" required>
                                    <option value="">اختر حالة الشراكة...</option>
                                    <option value="active" {{ old('partnership_status') == 'active' ? 'selected' : '' }}>نشطة</option>
                                    <option value="expired" {{ old('partnership_status') == 'expired' ? 'selected' : '' }}>منتهية</option>
                                    <option value="under_review" {{ old('partnership_status') == 'under_review' ? 'selected' : '' }}>قيد المراجعة</option>
                                </select>
                                @error('partnership_status')
                                    <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <a href="{{ route('admin.companies') }}" class="btn btn-secondary-modern">
                                <i class="fas fa-arrow-right me-2"></i>رجوع للقائمة
                            </a>
                            <button type="submit" class="btn btn-primary-modern px-4">
                                <i class="fas fa-save me-2"></i>حفظ الشركة
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection