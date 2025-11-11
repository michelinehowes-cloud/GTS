@extends('layouts.app')

@section('title', 'إضافة شركة جديدة - مسؤول الشراكات')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>إضافة شركة جديدة</h3>
                    <a href="{{ route('partnership.companies') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>رجوع للقائمة
                    </a>
                </div>
                <div class="card-body">
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

                    <form action="{{ route('partnership.companies.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">اسم الشركة <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" 
                                       placeholder="أدخل اسم الشركة" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email') }}" 
                                       placeholder="example@company.com" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone') }}" 
                                       placeholder="0912345678" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="industry" class="form-label">المجال الصناعي <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('industry') is-invalid @enderror" 
                                       id="industry" name="industry" value="{{ old('industry') }}" 
                                       placeholder="مثل: تكنولوجيا، تسويق، تعليم..." required>
                                @error('industry')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">العنوان <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                   id="address" name="address" value="{{ old('address') }}" 
                                   placeholder="العنوان الكامل للشركة" required>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="website" class="form-label">الموقع الإلكتروني</label>
                                <input type="url" class="form-control @error('website') is-invalid @enderror" 
                                       id="website" name="website" value="{{ old('website') }}" 
                                       placeholder="https://example.com">
                                @error('website')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="partnership_type" class="form-label">نوع الشراكة <span class="text-danger">*</span></label>
                                <select name="partnership_type" id="partnership_type" class="form-select @error('partnership_type') is-invalid @enderror" required>
                                    <option value="">اختر نوع الشراكة</option>
                                    <option value="employment" {{ old('partnership_type') == 'employment' ? 'selected' : '' }}>توظيف</option>
                                    <option value="training" {{ old('partnership_type') == 'training' ? 'selected' : '' }}>تدريب</option>
                                    <option value="logistic_support" {{ old('partnership_type') == 'logistic_support' ? 'selected' : '' }}>دعم لوجستي</option>
                                    <option value="academic" {{ old('partnership_type') == 'academic' ? 'selected' : '' }}>أكاديمي</option>
                                </select>
                                @error('partnership_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">وصف الشركة</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4" 
                                      placeholder="وصف مختصر عن نشاط الشركة وخبراتها...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- معلومات الاتصال -->
                        <div class="card mb-4">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-user-tie me-2"></i>معلومات شخص الاتصال</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="contact_person" class="form-label">اسم شخص الاتصال</label>
                                        <input type="text" class="form-control" 
                                               id="contact_person" name="contact_person" value="{{ old('contact_person') }}" 
                                               placeholder="اسم شخص الاتصال في الشركة">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="contact_position" class="form-label">المنصب</label>
                                        <input type="text" class="form-control" 
                                               id="contact_position" name="contact_position" value="{{ old('contact_position') }}" 
                                               placeholder="منصب شخص الاتصال">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="contact_phone" class="form-label">هاتف الاتصال</label>
                                        <input type="text" class="form-control" 
                                               id="contact_phone" name="contact_phone" value="{{ old('contact_phone') }}" 
                                               placeholder="هاتف شخص الاتصال">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="contact_email" class="form-label">بريد الاتصال</label>
                                        <input type="email" class="form-control" 
                                               id="contact_email" name="contact_email" value="{{ old('contact_email') }}" 
                                               placeholder="بريد شخص الاتصال">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('partnership.companies') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>إلغاء
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>حفظ الشركة
                            </button>
                        </div>
                    </form>
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