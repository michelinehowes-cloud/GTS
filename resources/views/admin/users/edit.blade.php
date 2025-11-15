{{-- ملف: resources/views/admin/users/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'تعديل مستخدم - ' . $user->name)

@section('page-title', 'تعديل مستخدم - ' . $user->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user-edit me-2"></i>تعديل بيانات المستخدم
                </h5>
            </div>
            <div class="card-body">
                @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">الاسم الكامل *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">البريد الإلكتروني *</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">كلمة المرور الجديدة</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" 
                                   placeholder="اتركه فارغاً إذا لم ترد التغيير">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">يجب أن تكون كلمة المرور 8 أحرف على الأقل</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">تأكيد كلمة المرور</label>
                            <input type="password" class="form-control" 
                                   id="password_confirmation" name="password_confirmation" 
                                   placeholder="أعد إدخال كلمة المرور الجديدة">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="role" class="form-label">الدور *</label>
                           <select name="role" id="role" class="form-select" required>
                            <option value="">اختر الدور</option>
                            <option value="admin" {{ old('role', $user->role ?? '') == 'admin' ? 'selected' : '' }}>مدير النظام</option>
                            <option value="training_coordinator" {{ old('role', $user->role ?? '') == 'training_coordinator' ? 'selected' : '' }}>منسق التدريب</option>
                            <option value="graduate" {{ old('role', $user->role ?? '') == 'graduate' ? 'selected' : '' }}>خريج</option>
                            <option value="partnership_officer" {{ old('role', $user->role ?? '') == 'partnership_officer' ? 'selected' : '' }}>مسؤول الشراكات والتوظيف</option>
                            <option value="career_guidance_officer" {{ old('role', $user->role ?? '') == 'career_guidance_officer' ? 'selected' : '' }}>مسؤول الإرشاد المهني</option>
                            <option value="company" {{ old('role', $user->role ?? '') == 'company' ? 'selected' : '' }}>شركة</option>
                            <option value="evaluation_followup" {{ old('role', $user->role ?? '') == 'evaluation_followup' ? 'selected' : '' }}>تقييم ومتابعة</option>
                            <option value="media_officer" {{ old('role', $user->role ?? '') == 'media_officer' ? 'selected' : '' }}>مسؤول الميديا</option>
                        </select>   
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">رقم الهاتف</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('admin.users') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-right me-2"></i>رجوع للقائمة
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>حفظ التعديلات
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
