@extends('layouts.app')

@section('title', 'إضافة فرصة عمل جديدة')

@section('content')

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

                    <x-bento-form title="إضافة فرصة عمل جديدة" subtitle="أدخل تفاصيل الفرصة الوظيفية ومتطلباتها" icon="fa-briefcase" :backRoute="route('job-opportunities.index')">
<form id="jobOpportunityForm" action="{{ route('job-opportunities.store') }}" method="POST">
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
                                @if(auth()->user()->isCompany())
                                    @if(count($companies) > 0)
                                        <input type="hidden" name="company_id" value="{{ $companies->first()->id }}">
                                        <input type="text" class="form-control bg-light" value="{{ $companies->first()->name }}" readonly>
                                    @else
                                        <div class="alert alert-warning py-2 mb-0 small">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            حساب شركتكم غير مرتبط بسجل شركة معتمد حتى الآن، يرجى التواصل مع إدارة المنظومة.
                                        </div>
                                    @endif
                                @else
                                    <select name="company_id" id="company_id" class="form-select @error('company_id') is-invalid @enderror" required>
                                        <option value="">اختر الشركة</option>
                                        @foreach($companies as $company)
                                            <option value="{{ $company->id }}" {{ old('company_id', request('company_id')) == $company->id ? 'selected' : '' }}>
                                                {{ $company->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('company_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>
                        </div>

                        {{-- تخصيص الوظيفة لمعرض التوظيف --}}
                        @if(isset($participatingFairs) && $participatingFairs->count() > 0)
                            <div class="card border border-warning-subtle rounded-4 mb-4 shadow-xs" style="background: linear-gradient(135deg, #fffdf5 0%, #fefce8 100%);">
                                <div class="card-body p-3.5">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                        <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                                            <input class="form-check-input" type="checkbox" role="switch" id="is_for_job_fair" name="is_for_job_fair" value="1" {{ old('is_for_job_fair') ? 'checked' : '' }} style="cursor: pointer; width: 2.5rem; height: 1.3rem;">
                                            <label class="form-check-label fw-bold text-dark mb-0 fs-6" for="is_for_job_fair" style="cursor: pointer;">
                                                <i class="fas fa-calendar-alt text-warning me-1"></i>
                                                تخصيص هذه الوظيفة لمعرض التوظيف
                                            </label>
                                        </div>
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1" style="font-size: 0.74rem;">
                                            <i class="fas fa-shield-alt me-1"></i>تخضع لاعتماد إدارة المعرض
                                        </span>
                                    </div>
                                    <p class="text-muted small mb-0 ms-md-4 ps-md-2">
                                        عند تفعيل هذا الخيار، سيتم ربط الوظيفة بجناح شركتكم في المعرض ومؤشرات التوظيف الحية. لن تصبح مرئية للخريجين أو في ماسح الأجنحة إلا بعد اعتمادها من الجهة المخولة.
                                    </p>

                                    <div id="job_fair_selection_wrapper" class="mt-3 ps-md-4 {{ old('is_for_job_fair') ? '' : 'd-none' }}">
                                        <div class="row align-items-center g-3">
                                            <div class="col-md-7">
                                                <label for="job_fair_id" class="form-label small fw-bold text-dark mb-1">اختر معرض التوظيف المستهدف <span class="text-danger">*</span></label>
                                                <select name="job_fair_id" id="job_fair_id" class="form-select @error('job_fair_id') is-invalid @enderror">
                                                    @if($participatingFairs->count() > 1)
                                                        <option value="">-- اختر المعرض --</option>
                                                    @endif
                                                    @foreach($participatingFairs as $fair)
                                                        <option value="{{ $fair->id }}" {{ old('job_fair_id', $participatingFairs->count() == 1 ? $fair->id : '') == $fair->id ? 'selected' : '' }}>
                                                            {{ $fair->title }} ({{ $fair->event_date ? $fair->event_date->format('Y-m-d') : 'مستمر' }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('job_fair_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-5">
                                                <div class="alert alert-info py-2 px-3 small rounded-3 mb-0 border-0 bg-info-subtle text-info-emphasis">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    <strong>إشعار:</strong> تظهر الوظيفة في لوحة الإحصائيات كـ (بانتظار الاعتماد) حتى يتم قبولها ونشرها.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

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
                                <label for="contract_type" class="form-label">نوع العقد</label>
                                <select name="contract_type" id="contract_type" class="form-select @error('contract_type') is-invalid @enderror">
                                    <option value="">-- اختياري للتدريب أو اختر نوع العقد --</option>
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
                                       id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="end_date" class="form-label">تاريخ الانتهاء <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                       id="end_date" name="end_date" value="{{ old('end_date') }}" required>
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
                            <button type="submit" id="btnSubmitJob" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>حفظ الفرصة
                            </button>
                        </div>
                    </form>
</x-bento-form>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // التحقق من أن تاريخ الانتهاء بعد تاريخ البدء
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    
    if (startDate && endDate) {
        startDate.addEventListener('change', function() {
            if (this.value) {
                endDate.min = this.value;
                if (endDate.value && endDate.value < this.value) {
                    endDate.value = this.value;
                }
            }
        });
    }

    // إدارة ظهور حقل معرض التوظيف
    const isForJobFair = document.getElementById('is_for_job_fair');
    const jobFairWrapper = document.getElementById('job_fair_selection_wrapper');
    const jobFairSelect = document.getElementById('job_fair_id');
    if (isForJobFair && jobFairWrapper) {
        isForJobFair.addEventListener('change', function() {
            if (this.checked) {
                jobFairWrapper.classList.remove('d-none');
                if (jobFairSelect && jobFairSelect.options.length === 1) {
                    jobFairSelect.selectedIndex = 0;
                }
            } else {
                jobFairWrapper.classList.add('d-none');
            }
        });
    }

    // مؤشر التحميل عند الإرسال وتفادي تعطيل الزر في حال وجود خطأ تحقق
    const jobForm = document.getElementById('jobOpportunityForm');
    const submitBtn = document.getElementById('btnSubmitJob');
    if (jobForm && submitBtn) {
        jobForm.addEventListener('submit', function(e) {
            if (jobForm.checkValidity && !jobForm.checkValidity()) {
                return;
            }
            if (submitBtn.disabled) {
                e.preventDefault();
                return;
            }
            setTimeout(() => {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> جاري الحفظ والمعالجة...';
            }, 50);
        });

        window.addEventListener('pageshow', function() {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>حفظ الفرصة';
        });
    }
});
</script>
@endsection
