@extends('layouts.app')

@section('title', 'تعديل بيانات الخريج - ' . $graduate->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>تعديل بيانات الخريج: {{ $graduate->name }}</h3>
                    <a href="{{ route('admin.career-guidance.graduates.show', $graduate->id) }}" class="btn btn-info">
                        <i class="fas fa-eye me-2"></i>عرض التفاصيل
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.career-guidance.graduates.update', $graduate->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">الاسم الكامل *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $graduate->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">البريد الإلكتروني *</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $graduate->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">رقم الهاتف</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone', $graduate->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="national_id" class="form-label">رقم القيد الجامعي</label>
                                <input type="text" class="form-control @error('national_id') is-invalid @enderror" 
                                       id="national_id" name="national_id" value="{{ old('national_id', $graduate->national_id) }}"
                                       placeholder="رقم القيد بالجامعة (اختياري)">
                                @error('national_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="major" class="form-label">التخصص *</label>
                                <input type="text" class="form-control @error('major') is-invalid @enderror" 
                                       id="major" name="major" value="{{ old('major', $graduate->major) }}" required>
                                @error('major')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="graduation_year" class="form-label">سنة التخرج *</label>
                                <select class="form-select @error('graduation_year') is-invalid @enderror" 
                                        id="graduation_year" name="graduation_year" required>
                                    <option value="">اختر سنة التخرج</option>
                                    @for($year = date('Y'); $year >= 2000; $year--)
                                        <option value="{{ $year }}" {{ old('graduation_year', $graduate->graduation_year) == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endfor
                                </select>
                                @error('graduation_year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="gpa" class="form-label">المعدل التراكمي (%)</label>
                                <input type="number" step="0.01" min="0" max="100" 
                                       class="form-control @error('gpa') is-invalid @enderror" 
                                       id="gpa" name="gpa" value="{{ old('gpa', $graduate->gpa) }}"
                                       placeholder="مثال: 85.50">
                                @error('gpa')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">النسبة المئوية من 0 إلى 100% (اختياري)</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="employment_status" class="form-label">حالة التوظيف *</label>
                                <select class="form-select @error('employment_status') is-invalid @enderror" 
                                        id="employment_status" name="employment_status" required>
                                    <option value="">اختر الحالة</option>
                                    <option value="employed" {{ old('employment_status', $graduate->employment_status) == 'employed' ? 'selected' : '' }}>موظف</option>
                                    <option value="unemployed" {{ old('employment_status', $graduate->employment_status) == 'unemployed' ? 'selected' : '' }}>غير موظف</option>
                                    <option value="seeking_opportunities" {{ old('employment_status', $graduate->employment_status) == 'seeking_opportunities' ? 'selected' : '' }}>باحث عن عمل</option>
                                    <option value="continuing_education" {{ old('employment_status', $graduate->employment_status) == 'continuing_education' ? 'selected' : '' }}>مستكمل للدراسة</option>
                                </select>
                                @error('employment_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label for="skills" class="form-label">المهارات (افصل بينها بفواصل)</label>
                                <input type="text" class="form-control @error('skills') is-invalid @enderror" 
                                       id="skills" name="skills" 
                                       value="{{ old('skills', $graduate->skills ? implode(', ', $graduate->skills) : '') }}"
                                       placeholder="مثال: PHP, Laravel, JavaScript">
                                @error('skills')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">أدخل المهارات مفصولة بفواصل</small>
                            </div>

                            <div class="col-12 mb-3">
                                <label for="work_experience" class="form-label">الخبرات العملية</label>
                                <textarea class="form-control @error('work_experience') is-invalid @enderror" 
                                          id="work_experience" name="work_experience" rows="3">{{ old('work_experience', $graduate->work_experience) }}</textarea>
                                @error('work_experience')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label for="notes" class="form-label">ملاحظات إضافية</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" name="notes" rows="3">{{ old('notes', $graduate->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('admin.career-guidance.graduates.show', $graduate->id) }}" class="btn btn-secondary">
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

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // يمكنك إضافة أي سكريبتات إضافية هنا إذا لزم الأمر
});
</script>
@endsection
@endsection
