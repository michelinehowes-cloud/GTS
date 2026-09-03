@extends('layouts.app')

@section('title', 'تعديل بيانات الشركة')

@section('page-title', 'تعديل بيانات الشركة')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumbs -->
    @include('components.breadcrumbs', [
        'items' => [
            ['label' => 'الرئيسية', 'url' => route('admin.dashboard')],
            ['label' => 'الشركات', 'url' => route('admin.companies')],
            ['label' => $company->name, 'active' => true],
        ]
    ])

    <x-bento-form title="تعديل بيانات الشركة: {{ $company->name }}" subtitle="تحديث بيانات المؤسسة أو الشركة الشريكة ومسؤول الاتصال" icon="fa-building" :backRoute="route('admin.companies')">
                    @if($errors->any())
                    <div class="alert alert-danger border-0 bg-light text-danger text-danger mb-4">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('admin.companies.update', $company->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3">
                            <!-- اسم الشركة -->
                            <div class="col-md-6">
                                <label for="name" class="form-label-modern">اسم الشركة <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-building text-muted"></i></span>
                                    <input type="text" class="form-control-modern border-start-0 ps-0 @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $company->name) }}" required>
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
                                @if($company->logo_path)
                                <div class="mt-2">
                                    <img src="{{ Storage::url($company->logo_path) }}" alt="الشعار الحالي" style="height: 50px; object-fit: contain; border-radius: 4px; border: 1px solid #ddd; padding: 2px;">
                                </div>
                                @endif
                            </div>
                            
                            <!-- البريد الإلكتروني -->
                            <div class="col-md-6">
                                <label for="email" class="form-label-modern">البريد الإلكتروني <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                                    <input type="email" class="form-control-modern border-start-0 ps-0 @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $company->email) }}" required>
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
                                           id="phone" name="phone" value="{{ old('phone', $company->phone) }}" required>
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
                                           id="industry" name="industry" value="{{ old('industry', $company->industry) }}" required>
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
                                           id="address" name="address" value="{{ old('address', $company->address) }}" required>
                                </div>
                                @error('address')
                                    <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- الوصف -->
                            <div class="col-12">
                                <label for="description" class="form-label-modern">وصف الشركة</label>
                                <textarea class="form-control-modern @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="4">{{ old('description', $company->description) }}</textarea>
                                @error('description')
                                    <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr class="mt-4">
                            <h6 class="fw-bold text-primary mb-3">تفاصيل الشراكة</h6>
                            
                            <!-- نوع الشراكة -->
                            <div class="col-md-6">
                                <label for="partnership_type" class="form-label-modern">نوع الشراكة <span class="text-danger">*</span></label>
                                <select class="form-select-modern w-100 @error('partnership_type') is-invalid @enderror" 
                                        id="partnership_type" name="partnership_type" required>
                                    <option value="">اختر نوع الشراكة...</option>
                                    <option value="employment" {{ old('partnership_type', $company->partnership_type) == 'employment' ? 'selected' : '' }}>توظيف</option>
                                    <option value="training" {{ old('partnership_type', $company->partnership_type) == 'training' ? 'selected' : '' }}>تدريب</option>
                                    <option value="logistic_support" {{ old('partnership_type', $company->partnership_type) == 'logistic_support' ? 'selected' : '' }}>دعم لوجستي</option>
                                    <option value="academic" {{ old('partnership_type', $company->partnership_type) == 'academic' ? 'selected' : '' }}>أكاديمي</option>
                                    <option value="training_employment" {{ old('partnership_type', $company->partnership_type) == 'training_employment' ? 'selected' : '' }}>تدريب وتوظيف</option>
                                </select>
                                @error('partnership_type')
                                    <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- حالة الشراكة والاعتماد -->
                            <div class="col-md-6">
                                <label for="partnership_status" class="form-label-modern">حالة الشركة والاعتماد <span class="text-danger">*</span></label>
                                <select class="form-select-modern w-100 @error('partnership_status') is-invalid @enderror" 
                                        id="partnership_status" name="partnership_status" required>
                                    <option value="active" {{ old('partnership_status', ($company->is_approved && $company->partnership_status === 'active') ? 'active' : $company->partnership_status) === 'active' ? 'selected' : '' }}>🟢 نشطة ومعتمدة (مفعلة)</option>
                                    <option value="under_review" {{ old('partnership_status', $company->partnership_status) === 'under_review' || !$company->is_approved ? 'selected' : '' }}>⏳ قيد المراجعة (معلقة)</option>
                                    <option value="expired" {{ old('partnership_status', $company->partnership_status) === 'expired' ? 'selected' : '' }}>⚪ منتهية الشراكة</option>
                                </select>
                                <div class="form-text text-muted small mt-1">تحديد "نشطة ومعتمدة" يمنح الشركة صلاحية تسجيل الدخول وإضافة وظائف فوراً.</div>
                                @error('partnership_status')
                                    <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <a href="{{ route('admin.companies') }}" class="btn btn-secondary px-4 py-2 rounded-pill">
                                <i class="fas fa-times me-2"></i>إلغاء
                            </a>
                            <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill shadow-sm">
                                <i class="fas fa-save me-2"></i>حفظ التعديلات
                            </button>
                        </div>
                    </form>
    </x-bento-form>
</div>
@endsection