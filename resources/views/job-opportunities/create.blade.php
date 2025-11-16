@extends('layouts.app')

@section('title', 'إضافة فرصة عمل جديدة')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>إضافة فرصة عمل جديدة</h3>
                    <a href="{{ route('job-opportunities.index') }}" class="btn btn-secondary">
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

                    <form action="{{ route('job-opportunities.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="title" class="form-label">عنوان الفرصة <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title') }}" 
                                       placeholder="مثال: مبرمج ويب" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="company_id" class="form-label">الشركة <span class="text-danger">*</span></label>
                                <select name="company_id" id="company_id" class="form-select @error('company_id') is-invalid @enderror" required>
                                    <option value="">اختر الشركة</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
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
                            <label for="description" class="form-label">وصف الفرصة <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4" 
                                      placeholder="وصف مفصل للفرصة والمهام المطلوبة..." required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="type" class="form-label">نوع الفرصة <span class="text-danger">*</span></label>
                                <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                                    <option value="">اختر النوع</option>
                                    <option value="job" {{ old('type') == 'job' ? 'selected' : '' }}>وظيفة</option>
                                    <option value="training" {{ old('type') == 'training' ? 'selected' : '' }}>تدريب</option>
                                    <option value="internship" {{ old('type') == 'internship' ? 'selected' : '' }}>تدريب عملي</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="contract_type" class="form-label">نوع العقد <span class="text-danger">*</span></label>
                                <select name="contract_type" id="contract_type" class="form-select @error('contract_type') is-invalid @enderror" required>
                                    <option value="">اختر نوع العقد</option>
                                    <option value="full_time" {{ old('contract_type') == 'full_time' ? 'selected' : '' }}>دوام كامل</option>
                                    <option value="part_time" {{ old('contract_type') == 'part_time' ? 'selected' : '' }}>دوام جزئي</option>
                                    <option value="contract" {{ old('contract_type') == 'contract' ? 'selected' : '' }}>عقد</option>
                                    <option value="freelance" {{ old('contract_type') == 'freelance' ? 'selected' : '' }}>عمل حر</option>
                                </select>
                                @error('contract_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="location" class="form-label">المكان <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                       id="location" name="location" value="{{ old('location') }}" 
                                       placeholder="مثال: طرابلس، بنغازي" required>
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="seats" class="form-label">عدد المقاعد <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('seats') is-invalid @enderror" 
                                       id="seats" name="seats" value="{{ old('seats', 1) }}" 
                                       min="1" required>
                                @error('seats')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="salary" class="form-label">الراتب (دينار ليبي)</label>
                                <input type="number" class="form-control @error('salary') is-invalid @enderror" 
                                       id="salary" name="salary" value="{{ old('salary') }}" 
                                       min="0" step="0.01" placeholder="اختياري">
                                @error('salary')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="required_experience" class="form-label">الخبرة المطلوبة</label>
                                <input type="text" class="form-control @error('required_experience') is-invalid @enderror" 
                                       id="required_experience" name="required_experience" value="{{ old('required_experience') }}" 
                                       placeholder="مثال: مبتدئ، 2 سنوات">
                                @error('required_experience')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="start_date" class="form-label">تاريخ البدء <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                       id="start_date" name="start_date" value="{{ old('start_date') }}" 
                                       min="{{ date('Y-m-d') }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="end_date" class="form-label">تاريخ الانتهاء <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                       id="end_date" name="end_date" value="{{ old('end_date') }}" 
                                       min="{{ date('Y-m-d') }}" required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="application_deadline" class="form-label">آخر موعد للتقديم <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('application_deadline') is-invalid @enderror" 
                                       id="application_deadline" name="application_deadline" value="{{ old('application_deadline') }}" 
                                       min="{{ date('Y-m-d') }}" required>
                                @error('application_deadline')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="required_specializations" class="form-label">التخصصات المطلوبة</label>
                                <select name="required_specializations[]" id="required_specializations" 
                                        class="form-select @error('required_specializations') is-invalid @enderror" multiple>
                                    @foreach($specializations as $specialization)
                                        <option value="{{ $specialization }}" {{ in_array($specialization, old('required_specializations', [])) ? 'selected' : '' }}>
                                            {{ $specialization }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">اضغط Ctrl لاختيار أكثر من تخصص</div>
                                @error('required_specializations')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="required_skills" class="form-label">المهارات المطلوبة</label>
                                <select name="required_skills[]" id="required_skills" 
                                        class="form-select @error('required_skills') is-invalid @enderror" multiple>
                                    @foreach($skills as $skill)
                                        <option value="{{ $skill }}" {{ in_array($skill, old('required_skills', [])) ? 'selected' : '' }}>
                                            {{ $skill }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">اضغط Ctrl لاختيار أكثر من مهارة</div>
                                @error('required_skills')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="benefits" class="form-label">المزايا</label>
                            <textarea class="form-control @error('benefits') is-invalid @enderror" 
                                      id="benefits" name="benefits" rows="2" 
                                      placeholder="المزايا المقدمة مثل: تأمين صحي، تدريب، مواصلات...">{{ old('benefits') }}</textarea>
                            @error('benefits')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="requirements" class="form-label">المتطلبات العامة</label>
                            <textarea class="form-control @error('requirements') is-invalid @enderror" 
                                      id="requirements" name="requirements" rows="2" 
                                      placeholder="المتطلبات العامة مثل: شهادة جامعية، خبرة سابقة...">{{ old('requirements') }}</textarea>
                            @error('requirements')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('job-opportunities.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>إلغاء
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>حفظ الفرصة
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // التحقق من أن تاريخ الانتهاء بعد تاريخ البدء
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    
    startDate.addEventListener('change', function() {
        endDate.min = this.value;
    });

    // التحقق من أن آخر موعد قبل تاريخ البدء
    const deadline = document.getElementById('application_deadline');
    
    startDate.addEventListener('change', function() {
        deadline.max = this.value;
    });
});
</script>
@endsection
@endsection
