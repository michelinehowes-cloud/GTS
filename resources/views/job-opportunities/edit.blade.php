@extends('layouts.app')

@section('title', 'تعديل فرصة العمل - ' . $opportunity->title)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>تعديل فرصة العمل: {{ $opportunity->title }}</h3>
                    <div class="d-flex gap-2">
                        <a href="{{ route('job-opportunities.show', $opportunity->id) }}" class="btn btn-info">
                            <i class="fas fa-eye me-2"></i>عرض التفاصيل
                        </a>
                        <a href="{{ route('job-opportunities.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-right me-2"></i>رجوع للقائمة
                        </a>
                    </div>
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

                    <form action="{{ route('job-opportunities.update', $opportunity->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="title" class="form-label">عنوان الفرصة *</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title', $opportunity->title) }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="company_id" class="form-label">الشركة *</label>
                                <select name="company_id" id="company_id" class="form-select @error('company_id') is-invalid @enderror" required>
                                    <option value="">اختر الشركة</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}" {{ old('company_id', $opportunity->company_id) == $company->id ? 'selected' : '' }}>
                                            {{ $company->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('company_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">الوصف *</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4" required>{{ old('description', $opportunity->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="type" class="form-label">نوع الفرصة *</label>
                                <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                                    <option value="job" {{ old('type', $opportunity->type) == 'job' ? 'selected' : '' }}>وظيفة</option>
                                    <option value="training" {{ old('type', $opportunity->type) == 'training' ? 'selected' : '' }}>تدريب</option>
                                    <option value="internship" {{ old('type', $opportunity->type) == 'internship' ? 'selected' : '' }}>تدريب عملي</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="contract_type" class="form-label">نوع العقد *</label>
                                <select name="contract_type" id="contract_type" class="form-select @error('contract_type') is-invalid @enderror" required>
                                    <option value="full_time" {{ old('contract_type', $opportunity->contract_type) == 'full_time' ? 'selected' : '' }}>دوام كامل</option>
                                    <option value="part_time" {{ old('contract_type', $opportunity->contract_type) == 'part_time' ? 'selected' : '' }}>دوام جزئي</option>
                                    <option value="contract" {{ old('contract_type', $opportunity->contract_type) == 'contract' ? 'selected' : '' }}>عقد</option>
                                    <option value="freelance" {{ old('contract_type', $opportunity->contract_type) == 'freelance' ? 'selected' : '' }}>عمل حر</option>
                                </select>
                                @error('contract_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="status" class="form-label">الحالة *</label>
                                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="new" {{ old('status', $opportunity->status) == 'new' ? 'selected' : '' }}>جديدة</option>
                                    <option value="open" {{ old('status', $opportunity->status) == 'open' ? 'selected' : '' }}>مفتوحة</option>
                                    <option value="closed" {{ old('status', $opportunity->status) == 'closed' ? 'selected' : '' }}>مغلقة</option>
                                    <option value="completed" {{ old('status', $opportunity->status) == 'completed' ? 'selected' : '' }}>مكتملة</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="location" class="form-label">المكان *</label>
                                <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                       id="location" name="location" value="{{ old('location', $opportunity->location) }}" required>
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="seats" class="form-label">عدد المقاعد *</label>
                                <input type="number" class="form-control @error('seats') is-invalid @enderror" 
                                       id="seats" name="seats" value="{{ old('seats', $opportunity->seats) }}" min="1" required>
                                @error('seats')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="start_date" class="form-label">تاريخ البدء *</label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                       id="start_date" name="start_date" value="{{ old('start_date', $opportunity->start_date->format('Y-m-d')) }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="end_date" class="form-label">تاريخ الانتهاء *</label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                       id="end_date" name="end_date" value="{{ old('end_date', $opportunity->end_date->format('Y-m-d')) }}" required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="application_deadline" class="form-label">آخر موعد للتقديم *</label>
                                <input type="date" class="form-control @error('application_deadline') is-invalid @enderror" 
                                       id="application_deadline" name="application_deadline" value="{{ old('application_deadline', $opportunity->application_deadline->format('Y-m-d')) }}" required>
                                @error('application_deadline')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="salary" class="form-label">الراتب</label>
                                <input type="number" class="form-control @error('salary') is-invalid @enderror" 
                                       id="salary" name="salary" value="{{ old('salary', $opportunity->salary) }}" min="0" step="0.01">
                                @error('salary')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="required_experience" class="form-label">الخبرة المطلوبة</label>
                                <input type="text" class="form-control @error('required_experience') is-invalid @enderror" 
                                       id="required_experience" name="required_experience" value="{{ old('required_experience', $opportunity->required_experience) }}">
                                @error('required_experience')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="benefits" class="form-label">المزايا</label>
                            <textarea class="form-control @error('benefits') is-invalid @enderror" 
                                      id="benefits" name="benefits" rows="3">{{ old('benefits', $opportunity->benefits) }}</textarea>
                            @error('benefits')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="requirements" class="form-label">المتطلبات العامة</label>
                            <textarea class="form-control @error('requirements') is-invalid @enderror" 
                                      id="requirements" name="requirements" rows="3">{{ old('requirements', $opportunity->requirements) }}</textarea>
                            @error('requirements')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('job-opportunities.show', $opportunity->id) }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>إلغاء
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
</div>
@endsection